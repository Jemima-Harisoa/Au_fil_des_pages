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
        // Requête pour compter les absences autorisées et non autorisées
        $sql = "
            SELECT 
                COUNT(CASE WHEN a.est_autorise = true THEN 1 END) as absences_autorisees,
                COUNT(CASE WHEN a.est_autorise = false THEN 1 END) as absences_non_autorisees
            FROM abscence_conge_suivi acs
            LEFT JOIN abscence a ON acs.id_demande = a.id_abscence
            WHERE acs.id_employe = :id_employe
        ";
            
        // pour la gestion des date anne actuelle : AND EXTRACT(YEAR FROM a.debut) = EXTRACT(YEAR FROM CURRENT_DATE) 
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Retourner les statistiques avec des valeurs par défaut si null
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
        // Récupérer les statistiques d'absences
        $absences = $this->getNombreAbsence($idEmploye);
        
        // Définir les types d'absences avec leurs configurations
        $typesAbsences = [
            [
                'titre' => 'Autorisées',
                'valeur' => $absences['autorisees'],
                'couleur' => 'success',
                'icone' => 'fa-check-circle',
                'description' => 'Jours d\'absence validés'
            ],
            [
                'titre' => 'Non Autorisées',
                'valeur' => $absences['non_autorisees'],
                'couleur' => 'danger',
                'icone' => 'fa-times-circle',
                'description' => 'Jours non justifiés'
            ],
        ];
        
        // Construction du HTML
        $html = '                    <!-- Absences Section -->
                        <div class="col-xl-12 col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Statistiques des Absences</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">';
        
        // Boucle sur les types d'absences
        foreach ($typesAbsences as $type) {
            $html .= '
                                        <!-- Absences ' . $type['titre'] . ' -->
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