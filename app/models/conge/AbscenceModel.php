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
     * Récupère la liste complète des absences avec informations employés
     * @param bool|null $estAutorise true=autorisées, false=non autorisées, null=toutes
     * @return array Tableau avec les détails des absences et infos employés
     */
    public function getListeAbsence($estAutorise = null) {
        $sql = "
                SELECT 
                a.id_abscence,
                a.id_employe,
                a.debut,
                a.fin,
                a.est_autorise,
                a.justificatif,
                p.nom,
                p.prenom,
                e.poste,
                d.nom as departement,
                (EXTRACT(EPOCH FROM (a.fin - a.debut))/86400 + 1) as jours_pris
            FROM abscence a
            LEFT JOIN employes e ON a.id_employe = e.id_employe
            LEFT JOIN personnes p ON e.id_personne = p.id_personne
            LEFT JOIN departements d ON e.id_departement = d.id_departement
        ";
        
        // Ajouter le filtre sur est_autorise si spécifié
        if ($estAutorise !== null) {
            $sql .= " WHERE a.est_autorise = :est_autorise";
        }
        
        $sql .= " ORDER BY a.debut DESC";
        
        $stmt = $this->db->prepare($sql);
        
        if ($estAutorise !== null) {
            $stmt->bindValue(':est_autorise', $estAutorise, \PDO::PARAM_BOOL);
        }
        
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Formater les dates avec gestion des valeurs NULL
        foreach ($result as &$absence) {
            // Formater la date de début (gérer les NULL)
            if (!empty($absence['debut'])) {
                $absence['debut_formatted'] = date('d/m/Y', strtotime($absence['debut']));
            } else {
                $absence['debut_formatted'] = 'Non définie';
            }
            
            // Formater la date de fin (gérer les NULL)
            if (!empty($absence['fin'])) {
                $absence['fin_formatted'] = date('d/m/Y', strtotime($absence['fin']));
            } else {
                $absence['fin_formatted'] = 'Non définie';
            }
            
            $absence['jours_pris'] = (int)$absence['jours_pris'];
            $absence['periode'] = $absence['debut_formatted'] . ' - ' . $absence['fin_formatted'];
            $absence['employe'] = $absence['prenom'] . ' ' . $absence['nom'];
            $absence['poste_complet'] = $absence['poste'] . ' - ' . $absence['departement'];
            
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
     * Génère le HTML pour le tableau de la liste des absences (tous les employés)
     * @param bool|null $estAutorise Type d'absence (true=autorisé, false=non autorisé, null=tous)
     * @return string HTML formaté du tableau
     */
    public function getTableauListeAbsence($estAutorise = null) {
        $absences = $this->getListeAbsence($estAutorise);
        
        $titre = "Liste des Absences : ";
        if ($estAutorise === true) {
            $titre .= "Autorisées";
        } elseif ($estAutorise === false) {
            $titre .= "Non Autorisées";
        } else {
            $titre .= "Toutes";
        }

        // Construction du HTML complet
        $html = '
            <!-- Content Row - Liste Absences -->
            <div class="row" id="listeAbscence">

                <!-- Liste des Absences -->
                <div class="col-xl-12 col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">' . $titre . '</h6>
                            <div class="btn-group">
                                <a href="/absence/liste" class="btn btn-sm ' . ($estAutorise === null ? 'btn-primary' : 'btn-outline-primary') . '">
                                    <i class="fas fa-list"></i> Toutes
                                </a>
                                <a href="/absence/liste/1" class="btn btn-sm ' . ($estAutorise === true ? 'btn-success' : 'btn-outline-success') . '">
                                    <i class="fas fa-check-circle"></i> Autorisées
                                </a>
                                <a href="/absence/liste/0" class="btn btn-sm ' . ($estAutorise === false ? 'btn-danger' : 'btn-outline-danger') . '">
                                    <i class="fas fa-times-circle"></i> Non Autorisées
                                </a>
                                <!-- Bouton pour réinitialiser les filtres -->
                                <button class="btn btn-sm btn-outline-secondary" id="resetFilters">
                                    <i class="fas fa-sync-alt"></i> Réinitialiser
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Statistiques rapides -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Total Absences</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">' . count($absences) . '</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-left-success shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                        Autorisées</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">' . count(array_filter($absences, function($a) { return $a['est_autorise']; })) . '</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                        À justifier</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">' . count(array_filter($absences, function($a) { return !$a['est_autorise']; })) . '</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-left-info shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                        Jours cumulés</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">' . array_sum(array_column($absences, 'jours_pris')) . '</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtres par colonne -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="employeFilter" class="small font-weight-bold">Employé</label>
                                    <input type="text" class="form-control form-control-sm" id="employeFilter" placeholder="Filtrer par employé">
                                </div>
                                <div class="col-md-2">
                                    <label for="posteFilter" class="small font-weight-bold">Poste</label>
                                    <input type="text" class="form-control form-control-sm" id="posteFilter" placeholder="Filtrer par poste">
                                </div>
                                <div class="col-md-2">
                                    <label for="periodeFilter" class="small font-weight-bold">Période</label>
                                    <input type="text" class="form-control form-control-sm" id="periodeFilter" placeholder="Filtrer par période">
                                </div>
                                <div class="col-md-1">
                                    <label for="joursPrisFilter" class="small font-weight-bold">Jours pris</label>
                                    <select class="form-control form-control-sm" id="joursPrisFilter">
                                        <option value="">Tous</option>
                                        <option value="1">1 jour</option>
                                        <option value="2-4">2-4 jours</option>
                                        <option value="5+">5+ jours</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="descriptionFilter" class="small font-weight-bold">Description</label>
                                    <input type="text" class="form-control form-control-sm" id="descriptionFilter" placeholder="Filtrer description">
                                </div>
                                <div class="col-md-1">
                                    <label for="justificatifFilter" class="small font-weight-bold">Justification</label>
                                    <select class="form-control form-control-sm" id="justificatifFilter">
                                        <option value="">Tous</option>
                                        <option value="Aucune">Aucune</option>
                                        <option value="Médical">Médical</option>
                                        <option value="Familial">Familial</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="penaliteFilter" class="small font-weight-bold">Pénalité</label>
                                    <select class="form-control form-control-sm" id="penaliteFilter">
                                        <option value="">Toutes</option>
                                        <option value="Aucune">Aucune</option>
                                        <option value="Avertissement écrit">Avertissement écrit</option>
                                        <option value="Retenue sur salaire">Retenue sur salaire</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered" id="listeAbsencesTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Employé</th>
                                            <th>Poste</th>
                                            <th>Période</th>
                                            <th>Jours pris</th>
                                            <th>Description</th>
                                            <th>Justification</th>
                                            <th>Pénalité</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Employé</th>
                                            <th>Poste</th>
                                            <th>Période</th>
                                            <th>Jours pris</th>
                                            <th>Description</th>
                                            <th>Justification</th>
                                            <th>Pénalité</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>';

            if (empty($absences)) {
                $html .= '
                                        <tr>
                                            <td colspan="9" class="text-center">Aucune absence trouvée</td>
                                        </tr>';
            } else {
                foreach ($absences as $absence) {
                    // Déterminer le badge de statut
                    $statutBadge = $absence['est_autorise'] 
                        ? '<span class="badge badge-success"><i class="fas fa-check"></i> Autorisée</span>'
                        : '<span class="badge badge-danger"><i class="fas fa-times"></i> Non Autorisée</span>';
                    
                    $html .= '
                                        <tr>
                                            <td>' . htmlspecialchars($absence['employe']) . '</td>
                                            <td>' . htmlspecialchars($absence['poste_complet']) . '</td>
                                            <td>' . htmlspecialchars($absence['periode']) . '</td>
                                            <td>' . htmlspecialchars($absence['jours_pris']) . '</td>
                                            <td>' . htmlspecialchars($absence['description'] ?? 'Non spécifié') . '</td>
                                            <td>' . htmlspecialchars($absence['justificatif'] ?? 'Aucune') . '</td>
                                            <td>' . htmlspecialchars($absence['penalite']) . '</td>
                                            <td class="text-center">' . $statutBadge . '</td>
                                            <td class="text-center">';
                        
                    // Actions selon si l'absence est autorisée ou non
                    if ($absence['est_autorise']) {
                        // Si autorisée, on affiche un lien pour voir le justificatif
                        if (!empty($absence['justificatif'])) {
                            $html .= '<button onclick="afficherJustificatif(' . $absence['id_abscence'] . ')" class="btn btn-sm btn-primary" title="Voir le justificatif">
                                        <i class="fas fa-eye"></i> Voir
                                    </button>';
                        } else {            
                            $html .= '<span class="text-muted small">Aucun justificatif</span>';
                        }
                    } else {
                        // Si non autorisée, on affiche un bouton pour notifier l'employé
                        $html .= '<button onclick="notifierAbsence(' . $absence['id_abscence'] . ')" 
                                        class="btn btn-sm btn-warning" 
                                        title="Notifier l\'employé pour justifier son absence">
                                    <i class="fas fa-bell"></i> Notifier
                                </button>';
                    }
                    
                    $html .= '
                                            </td>
                                        </tr>';
                }
            }

            $html .= '
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>';
        
        return $html;
    }

    /**
     * Récupère le chemin du justificatif d'une absence
     * @param int $idAbsence ID de l'absence
     * @return string|null Chemin du justificatif ou null
     */
    public function getJustificatifPath($idAbsence) {
        $sql = "SELECT justificatif FROM abscence WHERE id_abscence = :id_abscence";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_abscence' => $idAbsence]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result['justificatif'] ?? null;
    }
    /**
     * Récupère une absence spécifique par son ID pour un employé
     */
    public function getAbsenceById($idAbsence, $idEmploye) {
        $sql = "
            SELECT 
                a.id_abscence,
                a.debut,
                a.fin,
                a.est_autorise,
                a.justificatif,
                cd.description,
                (EXTRACT(EPOCH FROM (a.fin - a.debut))/86400 + 1) as jours_pris,
                acs.penalite_appliquee
            FROM abscence_conge_suivi acs
            LEFT JOIN abscence a ON acs.id_abscence = a.id_abscence
            LEFT JOIN conge_demande cd ON acs.id_demande = cd.id_demande
            WHERE a.id_abscence = :id_abscence 
            AND acs.id_employe = :id_employe
            AND a.est_autorise = false
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_abscence' => $idAbsence,
            'id_employe' => $idEmploye
        ]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($result) {
            $result['debut_formatted'] = date('d/m/Y', strtotime($result['debut']));
            $result['fin_formatted'] = date('d/m/Y', strtotime($result['fin']));
            $result['jours_pris'] = (int)$result['jours_pris'];
            $result['periode'] = $result['debut_formatted'] . ' - ' . $result['fin_formatted'];
        }
        
        return $result;
    }

    /**
     * Met à jour une absence avec la justification (fichier, texte ou les deux)
     */
    public function justifierAbsence($idAbsence, $data) {
        try {
            $uploadDir = __DIR__ . '/../../../public/uploads/justificatifs_absence/';
            
            // Créer le dossier s'il n'existe pas
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $justificatifValue = '';

            // Cas 1: Justificatif texte uniquement
            if (!empty($data['justificatif_texte']) && empty($data['justificatif_fichier'])) {
                $justificatifValue = $data['justificatif_texte'];
            }
            // Cas 2: Fichier unique
            elseif (!empty($data['justificatif_fichier']) && $data['justificatif_fichier']['error'] === UPLOAD_ERR_OK) {
                $fileInfo = pathinfo($data['justificatif_fichier']['name']);
                $extension = strtolower($fileInfo['extension'] ?? '');
                
                // Valider le type de fichier
                $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'txt'];
                if (!in_array($extension, $allowedTypes)) {
                    return [
                        'success' => false,
                        'error' => 'Type de fichier non autorisé'
                    ];
                }

                // Générer un nom de fichier unique
                $fileName = uniqid() . '_' . time() . '.' . $extension;
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($data['justificatif_fichier']['tmp_name'], $filePath)) {
                    $justificatifValue = $fileName;
                }
            }
            // Cas 3: Texte ET fichier - stocker dans un dossier
            elseif (!empty($data['justificatif_texte']) && !empty($data['justificatif_fichier']) && $data['justificatif_fichier']['error'] === UPLOAD_ERR_OK) {
                // Créer un dossier pour cette absence
                $dossierAbsence = 'absence_' . $idAbsence . '_' . time();
                $dossierPath = $uploadDir . $dossierAbsence;
                
                if (!is_dir($dossierPath)) {
                    mkdir($dossierPath, 0755, true);
                }

                // Sauvegarder le fichier
                $fileInfo = pathinfo($data['justificatif_fichier']['name']);
                $extension = strtolower($fileInfo['extension'] ?? '');
                $fileName = 'justificatif.' . $extension;
                $filePath = $dossierPath . '/' . $fileName;

                if (move_uploaded_file($data['justificatif_fichier']['tmp_name'], $filePath)) {
                    // Sauvegarder le texte dans un fichier séparé
                    $textePath = $dossierPath . '/description.txt';
                    file_put_contents($textePath, $data['justificatif_texte']);
                    
                    $justificatifValue = $dossierAbsence;
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'Aucun justificatif fourni'
                ];
            }

            // Mettre à jour la base de données
            $sql = "
                UPDATE abscence 
                SET justificatif = :justificatif,
                    est_autorise = true
                WHERE id_abscence = :id_abscence
            ";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                'justificatif' => $justificatifValue,
                'id_abscence' => $idAbsence
            ]);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Absence justifiée avec succès',
                    'type' => $this->determinerTypeJustificatif($justificatifValue),
                    'id_abscence' => $idAbsence
                ];
            }

            return [
                'success' => false,
                'error' => 'Erreur lors de la mise à jour de la base de données'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }
    /**
     * Détermine le type de justificatif stocké
     */
    private function determinerTypeJustificatif($justificatif) {
        $uploadDir = __DIR__ . '/../../../public/uploads/justificatifs_absence/';
        $fullPath = $uploadDir . $justificatif;
        
        if (is_dir($fullPath)) {
            return 'dossier';
        } elseif (file_exists($fullPath)) {
            return 'fichier';
        } else {
            return 'texte';
        }
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
        
        // Utiliser bindValue pour spécifier le type
        $stmt->bindValue(':id_employe', $idEmploye, \PDO::PARAM_INT);
        
        if ($estAutorise !== null) {
            // Convertir explicitement en boolean pour PostgreSQL
            $stmt->bindValue(':est_autorise', $estAutorise, \PDO::PARAM_BOOL);
        }
        
        $stmt->execute();
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

        // Construction du HTML complet
        $html = '
            <!-- Content Row - Détail Absences -->
            <div class="row" id="detailAbscence">

                <!-- Détail des Absences -->
                <div class="col-xl-12 col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">' . $titre . '</h6>
                            <!-- Bouton pour réinitialiser les filtres -->
                            <button class="btn btn-sm btn-outline-secondary" id="resetFilters">
                                <i class="fas fa-sync-alt"></i> Réinitialiser
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Filtres par colonne -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="periodeFilter" class="small font-weight-bold">Période</label>
                                    <input type="text" class="form-control form-control-sm" id="periodeFilter" placeholder="Filtrer par période">
                                </div>
                                <div class="col-md-2">
                                    <label for="joursPrisFilter" class="small font-weight-bold">Jours pris</label>
                                    <select class="form-control form-control-sm" id="joursPrisFilter">
                                        <option value="">Tous</option>
                                        <option value="1">1 jour</option>
                                        <option value="2-4">2-4 jours</option>
                                        <option value="5+">5+ jours</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="descriptionFilter" class="small font-weight-bold">Description</label>
                                    <input type="text" class="form-control form-control-sm" id="descriptionFilter" placeholder="Filtrer description">
                                </div>
                                <div class="col-md-2">
                                    <label for="justificatifFilter" class="small font-weight-bold">Justification</label>
                                    <select class="form-control form-control-sm" id="justificatifFilter">
                                        <option value="">Tous</option>
                                        <option value="Aucune">Aucune</option>
                                        <option value="Médical">Médical</option>
                                        <option value="Familial">Familial</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="penaliteFilter" class="small font-weight-bold">Pénalité</label>
                                    <select class="form-control form-control-sm" id="penaliteFilter">
                                        <option value="">Toutes</option>
                                        <option value="Aucune">Aucune</option>
                                        <option value="Avertissement écrit">Avertissement écrit</option>
                                        <option value="Retenue sur salaire">Retenue sur salaire</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered" id="detailAbsencesTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Période</th>
                                            <th>Jours pris</th>
                                            <th>Description</th>
                                            <th>Justification</th>
                                            <th>Pénalité</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Période</th>
                                            <th>Jours pris</th>
                                            <th>Description</th>
                                            <th>Justification</th>
                                            <th>Pénalité</th>
                                        </tr>
                                    </tfoot>
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
    
    public function getNombreAbsenceTous() {
        $sql = "
                SELECT
            COUNT(*) FILTER (WHERE est_autorise = true)  AS absences_autorisees,
            COUNT(*) FILTER (WHERE est_autorise = false) AS absences_non_autorisees
            FROM abscence;
        ";
            
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
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
                            <a href="/conge/fiche/' . $idEmploye . '/' . ($type['est_autorise'] ? '1' : '0') . '#detailAbscence" class="btn btn-sm btn-primary">
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