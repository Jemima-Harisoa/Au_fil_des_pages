<?php 

namespace app\models\conge;

class AbscenceModel
{
    /** @var \PDO */
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère le détail complet des absences (autorisées ou non) pour un employé
     * @param int $idEmploye ID de l'employé
     * @param bool|null $estAutorise true=autorisées, false=non autorisées, null=toutes
     * @return array Tableau avec les détails des absences
     */
    public function getDetailAbsence($idEmploye, $estAutorise = null) {
        $sql = "
            SELECT 
                a.id_abscence,
                a.debut,
                a.fin,
                a.est_autorise,
                a.justificatif,
                cd.description,
                (EXTRACT(EPOCH FROM (a.fin - a.debut))/86400 + 1) as jours_pris,
                acs.penalite_appliquee,
                atp.nom as type_penalite
            FROM abscence_conge_suivi acs
            LEFT JOIN abscence a ON acs.id_abscence = a.id_abscence
            LEFT JOIN conge_demande cd ON acs.id_demande = cd.id_demande
            LEFT JOIN abscence_type_penalite atp ON acs.id_type_penalite = atp.id_type_penalite
            WHERE acs.id_employe = :id_employe
        ";
        
        // Ajouter le filtre sur est_autorise si spécifié
        if ($estAutorise !== null) {
            $sql .= " AND a.est_autorise = :est_autorise";
        }
        
        $sql .= " ORDER BY a.debut DESC";
        
        $stmt = $this->db->prepare($sql);
        $params = ['id_employe' => $idEmploye];
        
        if ($estAutorise !== null) {
            $params['est_autorise'] = $estAutorise;
        }
        
        $stmt->execute($params);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Formater les dates et calculer les pénalités
        foreach ($result as &$absence) {
            $absence['debut_formatted'] = date('d/m/Y', strtotime($absence['debut']));
            $absence['fin_formatted'] = date('d/m/Y', strtotime($absence['fin']));
            $absence['jours_pris'] = (int)$absence['jours_pris'];
            $absence['periode'] = $absence['debut_formatted'] . ' - ' . $absence['fin_formatted'];
            
            // Calculer la pénalité si absence non autorisée
            if (!$absence['est_autorise']) {
                $absence['penalite'] = $this->calculerPenalite($absence['jours_pris']);
            } else {
                $absence['penalite'] = 'Aucune';
            }
        }
        
        return $result;
    }

    /**
     * Calcule la pénalité pour une absence non autorisée
     * @param int $joursPris Nombre de jours d'absence
     * @return string Description de la pénalité
     */
    private function calculerPenalite($joursPris) {
        // Règles métier à définir - exemple simple
        if ($joursPris <= 2) {
            return 'Avertissement écrit';
        } elseif ($joursPris <= 5) {
            return 'Retenue sur salaire (1 jour)';
        } else {
            return 'Retenue sur salaire (' . ceil($joursPris * 0.5) . ' jours)';
        }
    }

    /**
     * Génère le HTML pour le tableau des détails d'absence
     * @param int $idEmploye ID de l'employé
     * @param bool|null $estAutorise Type d'absence (true=autorisé, false=non autorisé, null=tous)
     * @return string HTML formaté du tableau
     */
    public function getTableauDetailAbsence($idEmploye, $estAutorise = null) {
        $absences = $this->getDetailAbsence($idEmploye, $estAutorise);
        
        $titre = "Détail des Absences ";
        if ($estAutorise === true) {
            $titre .= "Autorisées";
        } elseif ($estAutorise === false) {
            $titre .= "Non Autorisées";
        } else {
            $titre .= "Toutes";
        }

        $html = '
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">' . $titre . '</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTableAbsences" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Période</th>
                                <th>Jours pris</th>
                                <th>Description</th>
                                <th>Justification</th>
                                <th>Pénalité</th>
                            </tr>
                        </thead>
                        <tbody>';

        if (empty($absences)) {
            $html .= '
                            <tr>
                                <td colspan="5" class="text-center">Aucune absence trouvée</td>
                            </tr>';
        } else {
            foreach ($absences as $absence) {
                $html .= '
                            <tr>
                                <td>' . htmlspecialchars($absence['periode']) . '</td>
                                <td>' . htmlspecialchars($absence['jours_pris']) . '</td>
                                <td>' . htmlspecialchars($absence['description'] ?? 'Non spécifié') . '</td>
                                <td>' . htmlspecialchars($absence['justificatif'] ?? 'Aucune') . '</td>
                                <td>' . htmlspecialchars($absence['penalite']) . '</td>
                            </tr>';
            }
        }

        $html .= '
                        </tbody>
                    </table>
                </div>
            </div>
        </div>';

        return $html;
    }

    public function getAbsencesCumulees($idEmploye) {
        $absences = $this->getNombreAbsence($idEmploye);
        return $absences['autorisees'] + $absences['non_autorisees'];
    }

    /**
     * Récupère le nombre d'absences autorisées et non autorisées pour un employé
     * @param int $idEmploye ID de l'employé
     * @return array Tableau avec les statistiques d'absences
     */
    public function getNombreAbsence($idEmploye) {
        $sql = "
            SELECT 
                COUNT(CASE WHEN a.est_autorise = true THEN 1 END) as absences_autorisees,
                COUNT(CASE WHEN a.est_autorise = false THEN 1 END) as absences_non_autorisees
            FROM abscence_conge_suivi acs
            LEFT JOIN abscence a ON acs.id_abscence = a.id_abscence
            WHERE acs.id_employe = :id_employe
        ";
            
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return [
            'autorisees' => $result['absences_autorisees'] ?? 0,
            'non_autorisees' => $result['absences_non_autorisees'] ?? 0
        ];
    }

    /**
     * Génère le HTML pour la section des statistiques d'absences
     * @param int $idEmploye ID de l'employé
     * @return string HTML formaté de la section absences
     */
    public function getSectionAbsences($idEmploye) {
        $absences = $this->getNombreAbsence($idEmploye);
        
        $typesAbsences = [
            [
                'titre' => 'Autorisées',
                'valeur' => $absences['autorisees'],
                'couleur' => 'success',
                'icone' => 'fa-check-circle',
                'description' => 'Jours d\'absence validés',
                'est_autorise' => true
            ],
            [
                'titre' => 'Non Autorisées',
                'valeur' => $absences['non_autorisees'],
                'couleur' => 'danger',
                'icone' => 'fa-times-circle',
                'description' => 'Jours non justifiés',
                'est_autorise' => false
            ],
        ];
        
        $html = '
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques des Absences</h6>
                </div>
                <div class="card-body">
                    <div class="row">';
        
        foreach ($typesAbsences as $type) {
            $html .= '
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-' . $type['couleur'] . ' shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-' . $type['couleur'] . ' text-uppercase mb-1">
                                    ' . $type['titre'] . '</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">' . $type['valeur'] . '</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    ' . $type['description'] . '
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas ' . $type['icone'] . ' fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <a href="/conge/fiche/' . $idEmploye . '?type_absence=' . ($type['est_autorise'] ? '1' : '0') . '#detail-absences" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye fa-sm"></i> Détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>';
        }
        
        $html .= '
                    </div>
                </div>
            </div>
        </div>';
        
        return $html;
    }
}