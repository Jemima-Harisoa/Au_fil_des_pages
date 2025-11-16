<?php

namespace app\models\conge;

use Flight;

class CongeModel {
    /** @var \PDO */
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getSoldeConge($id_employe) {
        $sql = "SELECT nombre_conge FROM employes WHERE id_employe = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_employe]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result['nombre_conge'] : 0;
    }
    /**
     * Récupère le nombre de demandes de congé pour l'année en cours
     */
    public function getNombreDemandesAnnee($idEmploye) {
        $sql = "
            SELECT COUNT(*) as nombre_demandes 
            FROM conge_demande 
            WHERE id_employe = :id_employe 
            AND EXTRACT(YEAR FROM date_demande) = EXTRACT(YEAR FROM CURRENT_DATE)
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result['nombre_demandes'] ?? 0;
    }

    /**
     * Calcule le taux d'approbation des demandes de congé basé sur l'historique de validation
     */
    public function getTauxApprobation($idEmploye) {
        $sql = "
            SELECT 
                cd.id_demande,
                cd.niveau_validation,
                COUNT(chv.id_historique_validation) as validations_obtenues
            FROM conge_demande cd
            LEFT JOIN conge_historique_validation chv ON cd.id_demande = chv.id_demande
            WHERE cd.id_employe = :id_employe
            GROUP BY cd.id_demande, cd.niveau_validation
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        $demandes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $total_demandes = count($demandes);
        $demandes_approuvees = 0;
        
        foreach ($demandes as $demande) {
            // Une demande est approuvée si le nombre de validations obtenues >= niveau de validation requis
            if ($demande['validations_obtenues'] >= $demande['niveau_validation']) {
                $demandes_approuvees++;
            }
        }
        
        if ($total_demandes > 0) {
            return round(($demandes_approuvees / $total_demandes) * 100);
        }
        
        return 0;
    }
    /**
     * Vérifie si un employé existe et est actif
     */
    public function verifierEmploye($idEmploye) {
        $sql = "
            SELECT e.id_employe, p.nom, p.prenom, e.poste
            FROM employes e
            JOIN personnes p ON e.id_personne = p.id_personne
            WHERE e.id_employe = :id_employe
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    /**
     * Récupère tous les types de congé disponibles
     */
    public function getTypesConge() {
        $sql = "SELECT id_type, nom, description FROM conge_type ORDER BY nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * Crée une nouvelle demande de congé
     */
    public function creerDemandeConge($data, $justificatif = null) {
        try {
            $sqlDemande = "
                INSERT INTO conge_demande 
                (description, id_employe, date_demande, date_debut, date_fin, id_type_conge) 
                VALUES (:description, :id_employe, NOW(), :date_debut, :date_fin, :id_type_conge)
            ";
            
            $stmtDemande = $this->db->prepare($sqlDemande);
            $result = $stmtDemande->execute([
                'description' => $data['description'],
                'id_employe' => $data['id_employe'],
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'id_type_conge' => $data['id_type_conge']
            ]);

            if ($result) {
                $id_demande = $this->db->lastInsertId();
                
                return [
                    'success' => true,
                    'id_demande' => $id_demande,
                    'message' => 'Demande de congé créée avec succès',
                    'data' => $data
                ];
            } else {
                $errorInfo = $stmtDemande->errorInfo();
                
                return [
                    'success' => false,
                    'error' => 'Échec de l\'insertion dans la base de données',
                    'pdo_error' => $errorInfo,
                    'data' => $data
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la création de la demande: ' . $e->getMessage(),
                'data' => $data
            ];
        }
    }
    /**
     * Génère le HTML pour le tableau des détails des congés pour un type spécifique
     * @param int $idEmploye ID de l'employé
     * @param int $idType ID du type de congé
     * @return string HTML formaté du tableau
     */
    public function getTableauDetailCongeParType($idEmploye, $idType) {
        $conges = $this->getDetailCongeParType($idEmploye, $idType);
        
        // Récupérer le nom du type de congé pour le titre
        $sqlType = "SELECT nom FROM conge_type WHERE id_type = :id_type";
        $stmtType = $this->db->prepare($sqlType);
        $stmtType->execute(['id_type' => $idType]);
        $typeConge = $stmtType->fetch(\PDO::FETCH_ASSOC);
        $nomType = $typeConge['nom'] ?? 'Inconnu';
        
        $titre = "Détail des Congés - " . $nomType;

        // Construction du HTML complet
        $html = '
        <!-- Content Row - Détail Congés -->
        <div class="row" id="detailConge">

            <!-- Détail des Congés -->
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">' . $titre . '</h6>
                        <!-- Bouton pour réinitialiser les filtres -->
                        <button class="btn btn-sm btn-outline-secondary" id="resetFiltersConge">
                            <i class="fas fa-sync-alt"></i> Réinitialiser
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Filtres par colonne -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="periodeFilter" class="small font-weight-bold">Période</label>
                                <input type="text" class="form-control form-control-sm" id="periodeFilter" placeholder="Filtrer par période">
                            </div>
                            <div class="col-md-2">
                                <label for="joursDemandesFilter" class="small font-weight-bold">Jours demandés</label>
                                <select class="form-control form-control-sm" id="joursDemandesFilter">
                                    <option value="">Tous</option>
                                    <option value="1">1 jour</option>
                                    <option value="2-4">2-4 jours</option>
                                    <option value="5+">5+ jours</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="joursApprouvesFilter" class="small font-weight-bold">Jours approuvés</label>
                                <select class="form-control form-control-sm" id="joursApprouvesFilter">
                                    <option value="">Tous</option>
                                    <option value="0">0 jour</option>
                                    <option value="1-4">1-4 jours</option>
                                    <option value="5+">5+ jours</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="statutFilter" class="small font-weight-bold">Statut</label>
                                <select class="form-control form-control-sm" id="statutFilter">
                                    <option value="">Tous les statuts</option>
                                    <option value="En attente">En attente</option>
                                    <option value="Approuvé">Approuvé</option>
                                    <option value="Refusé">Refusé</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="detailCongesTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Date Demande</th>
                                        <th>Période</th>
                                        <th>Description</th>
                                        <th>Jours Demandés</th>
                                        <th>Jours Approuvés</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Date Demande</th>
                                        <th>Période</th>
                                        <th>Description</th>
                                        <th>Jours Demandés</th>
                                        <th>Jours Approuvés</th>
                                        <th>Statut</th>
                                    </tr>
                                </tfoot>
                                <tbody>';

        if (empty($conges)) {
            $html .= '
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune demande de congé trouvée pour ce type</td>
                                    </tr>';
        } else {
            foreach ($conges as $conge) {
                // Déterminer la classe CSS pour le statut
                $classeStatut = '';
                if ($conge['statut_validation'] === 'Approuvé') {
                    $classeStatut = 'badge-success';
                } elseif ($conge['statut_validation'] === 'Refusé') {
                    $classeStatut = 'badge-danger';
                } else {
                    $classeStatut = 'badge-warning';
                }
                
                $html .= '
                                    <tr>
                                        <td>' . htmlspecialchars($conge['date_demande_formatted']) . '</td>
                                        <td>' . htmlspecialchars($conge['periode']) . '</td>
                                        <td>' . htmlspecialchars($conge['description'] ?? 'Aucune') . '</td>
                                        <td>' . htmlspecialchars($conge['jours_demandes']) . '</td>
                                        <td>' . htmlspecialchars($conge['jours_approuves']) . '</td>
                                        <td><span class="badge ' . $classeStatut . '">' . htmlspecialchars($conge['statut_validation']) . '</span></td>
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
     * Récupère le détail des congés pour un employé et un type spécifique
     * @param int $idEmploye ID de l'employé
     * @param int $idType ID du type de congé
     * @return array Détail des congés
     */
    public function getDetailCongeParType($idEmploye, $idType) {
        $sql = "
            SELECT 
                cd.id_demande,
                cd.description,
                cd.date_demande,
                cd.date_debut,
                cd.date_fin,
                cd.niveau_validation,
                ct.nom as type_conge,
                (EXTRACT(EPOCH FROM (cd.date_fin - cd.date_debut))/86400 + 1) as jours_demandes,
                acs.nombre_conge as jours_approuves,
                CASE 
                    WHEN cd.niveau_validation = 0 THEN 'En attente'
                    WHEN cd.niveau_validation = 1 THEN 'Approuvé'
                    WHEN cd.niveau_validation = 2 THEN 'Refusé'
                    ELSE 'Statut inconnu'
                END as statut_validation
            FROM conge_demande cd
            LEFT JOIN conge_type ct ON cd.id_type_conge = ct.id_type
            LEFT JOIN abscence_conge_suivi acs ON cd.id_demande = acs.id_demande
            WHERE cd.id_employe = :id_employe AND cd.id_type_conge = :id_type
            ORDER BY cd.date_demande DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_employe' => $idEmploye,
            'id_type' => $idType
        ]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Formater les dates
        foreach ($result as &$conge) {
            $conge['date_demande_formatted'] = date('d/m/Y H:i', strtotime($conge['date_demande']));
            $conge['date_debut_formatted'] = date('d/m/Y', strtotime($conge['date_debut']));
            $conge['date_fin_formatted'] = date('d/m/Y', strtotime($conge['date_fin']));
            $conge['periode'] = $conge['date_debut_formatted'] . ' - ' . $conge['date_fin_formatted'];
            $conge['jours_demandes'] = (int)$conge['jours_demandes'];
            $conge['jours_approuves'] = $conge['jours_approuves'] ?? 0;
        }
        
        return $result;
    }
    
    /**
     * Récupère le détail complet des congés pour un employé
     * @param int $idEmploye ID de l'employé
     * @return array Tableau avec les détails des congés
     */
    public function getDetailConge($idEmploye) {
        $sql = "
            SELECT 
                cd.id_demande,
                cd.description,
                cd.date_demande,
                cd.date_debut,
                cd.date_fin,
                cd.niveau_validation,
                ct.nom as type_conge,
                ct.description as type_description,
                (EXTRACT(EPOCH FROM (cd.date_fin - cd.date_debut))/86400 + 1) as jours_demandes,
                acs.nombre_conge as jours_approuves,
                CASE 
                    WHEN cd.niveau_validation = 0 THEN 'En attente'
                    WHEN cd.niveau_validation = 1 THEN 'Approuvé'
                    WHEN cd.niveau_validation = 2 THEN 'Refusé'
                    ELSE 'Statut inconnu'
                END as statut_validation
            FROM conge_demande cd
            LEFT JOIN conge_type ct ON cd.id_type_conge = ct.id_type
            LEFT JOIN abscence_conge_suivi acs ON cd.id_demande = acs.id_demande
            WHERE cd.id_employe = :id_employe
            ORDER BY cd.date_demande DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Formater les dates
        foreach ($result as &$conge) {
            $conge['date_demande_formatted'] = date('d/m/Y H:i', strtotime($conge['date_demande']));
            $conge['date_debut_formatted'] = date('d/m/Y', strtotime($conge['date_debut']));
            $conge['date_fin_formatted'] = date('d/m/Y', strtotime($conge['date_fin']));
            $conge['periode'] = $conge['date_debut_formatted'] . ' - ' . $conge['date_fin_formatted'];
            $conge['jours_demandes'] = (int)$conge['jours_demandes'];
            $conge['jours_approuves'] = $conge['jours_approuves'] ?? 0;
        }
        
        return $result;
    }

    /**
     * Génère le HTML pour le tableau des détails des congés
     * @param int $idEmploye ID de l'employé
     * @return string HTML formaté du tableau
     */
    public function getTableauDetailConge($idEmploye) {
        $conges = $this->getDetailConge($idEmploye);
        
        // Construction du HTML complet
        $html = '
        <!-- Content Row - Détail Congés -->
        <div class="row" id="detail">

            <!-- Détail des Congés -->
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Détail des Demandes de Congé</h6>
                        <!-- Bouton pour réinitialiser les filtres -->
                        <button class="btn btn-sm btn-outline-secondary" id="resetFiltersConge">
                            <i class="fas fa-sync-alt"></i> Réinitialiser
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Filtres par colonne -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="periodeFilter" class="small font-weight-bold">Période</label>
                                <input type="text" class="form-control form-control-sm" id="periodeFilter" placeholder="Filtrer par période">
                            </div>
                            <div class="col-md-2">
                                <label for="joursDemandesFilter" class="small font-weight-bold">Jours demandés</label>
                                <select class="form-control form-control-sm" id="joursDemandesFilter">
                                    <option value="">Tous</option>
                                    <option value="1">1 jour</option>
                                    <option value="2-4">2-4 jours</option>
                                    <option value="5+">5+ jours</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="joursApprouvesFilter" class="small font-weight-bold">Jours approuvés</label>
                                <select class="form-control form-control-sm" id="joursApprouvesFilter">
                                    <option value="">Tous</option>
                                    <option value="0">0 jour</option>
                                    <option value="1-4">1-4 jours</option>
                                    <option value="5+">5+ jours</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="typeCongeFilter" class="small font-weight-bold">Type de congé</label>
                                <select class="form-control form-control-sm" id="typeCongeFilter">
                                    <option value="">Tous les types</option>';
        
        // Générer les options de type de congé
        $typesConge = array_unique(array_column($conges, 'type_conge'));
        foreach ($typesConge as $type) {
            if (!empty($type)) {
                $html .= '<option value="' . htmlspecialchars($type) . '">' . htmlspecialchars($type) . '</option>';
            }
        }
        
        $html .= '
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="statutFilter" class="small font-weight-bold">Statut</label>
                                <select class="form-control form-control-sm" id="statutFilter">
                                    <option value="">Tous les statuts</option>
                                    <option value="En attente">En attente</option>
                                    <option value="Approuvé">Approuvé</option>
                                    <option value="Refusé">Refusé</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="detailCongesTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Date Demande</th>
                                        <th>Période</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Jours Demandés</th>
                                        <th>Jours Approuvés</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Date Demande</th>
                                        <th>Période</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Jours Demandés</th>
                                        <th>Jours Approuvés</th>
                                        <th>Statut</th>
                                    </tr>
                                </tfoot>
                                <tbody>';

        if (empty($conges)) {
            $html .= '
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune demande de congé trouvée</td>
                                    </tr>';
        } else {
            foreach ($conges as $conge) {
                // Déterminer la classe CSS pour le statut
                $classeStatut = '';
                if ($conge['statut_validation'] === 'Approuvé') {
                    $classeStatut = 'badge-success';
                } elseif ($conge['statut_validation'] === 'Refusé') {
                    $classeStatut = 'badge-danger';
                } else {
                    $classeStatut = 'badge-warning';
                }
                
                $html .= '
                                    <tr>
                                        <td>' . htmlspecialchars($conge['date_demande_formatted']) . '</td>
                                        <td>' . htmlspecialchars($conge['periode']) . '</td>
                                        <td>' . htmlspecialchars($conge['type_conge'] ?? 'Non spécifié') . '</td>
                                        <td>' . htmlspecialchars($conge['description'] ?? 'Aucune') . '</td>
                                        <td>' . htmlspecialchars($conge['jours_demandes']) . '</td>
                                        <td>' . htmlspecialchars($conge['jours_approuves']) . '</td>
                                        <td><span class="badge ' . $classeStatut . '">' . htmlspecialchars($conge['statut_validation']) . '</span></td>
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
     * Récupère les données brutes des congés par type pour un employé
     * @param int $idEmploye ID de l'employé
     * @return array Données des congés
     */

    public function getDonneesConges($idEmploye) {
        $sql = "
            SELECT 
                ct.nom as type_conge,
                ct.description,
                COALESCE(SUM(acs.nombre_conge), 0) as jours_pris,
                COALESCE(MAX(e.nombre_conge), 0) as jours_totaux,
                COALESCE(MAX(e.nombre_conge), 0) - COALESCE(SUM(acs.nombre_conge), 0) as jours_restants
            FROM conge_type ct
            LEFT JOIN abscence_conge_suivi acs ON ct.id_type = acs.id_type AND acs.id_employe = :id_employe
            LEFT JOIN employes e ON acs.id_employe = e.id_employe
            GROUP BY ct.id_type, ct.nom, ct.description
            ORDER BY ct.nom
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    /**
     * Récupère les données des congés par type pour un employé
     * @param int $idEmploye ID de l'employé
     * @return array Données des congés par type
     */
    public function getDonneesCongesParType($idEmploye) {
        // Requête pour récupérer les congés par type
        $sql = "
            SELECT 
                ct.id_type,
                ct.nom as type_conge,
                ct.description,
                COALESCE(SUM(acs.nombre_conge), 0) as jours_pris,
                COALESCE(MAX(e.nombre_conge), 0) as jours_totaux
            FROM conge_type ct
            LEFT JOIN abscence_conge_suivi acs ON ct.id_type = acs.id_type AND acs.id_employe = :id_employe
            LEFT JOIN employes e ON acs.id_employe = e.id_employe
            GROUP BY ct.id_type, ct.nom, ct.description
            ORDER BY ct.nom
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Génère le HTML pour la section des congés par type
     * @param int $idEmploye ID de l'employé
     * @return string HTML formaté de la section congés
     */
    public function getNombreConge($idEmploye) {
        $conges = $this->getDonneesCongesParType($idEmploye);
        
        // Préparer les données pour les cartes
        $typesConges = [];
        $couleurs = ['primary', 'success', 'info', 'warning', 'secondary'];
        $icones = ['fa-calendar-check', 'fa-umbrella-beach', 'fa-heart', 'fa-stethoscope', 'fa-graduation-cap'];
        
        foreach ($conges as $index => $conge) {
            $joursRestants = max(0, $conge['jours_totaux'] - $conge['jours_pris']);
            $pourcentageUtilise = $conge['jours_totaux'] > 0 ? ($conge['jours_pris'] / $conge['jours_totaux']) * 100 : 0;
            
            // Déterminer la couleur en fonction du pourcentage utilisé
            if ($pourcentageUtilise >= 80) {
                $couleur = 'danger';
            } elseif ($pourcentageUtilise >= 60) {
                $couleur = 'warning';
            } else {
                $couleur = $couleurs[$index % count($couleurs)];
            }
            
            $typesConges[] = [
                'id_type' => $conge['id_type'],
                'titre' => $conge['type_conge'],
                'jours_pris' => $conge['jours_pris'],
                'jours_restants' => $joursRestants,
                'jours_totaux' => $conge['jours_totaux'],
                'pourcentage_utilise' => round($pourcentageUtilise),
                'description' => $conge['description'],
                'couleur' => $couleur,
                'icone' => $icones[$index % count($icones)] ?? 'fa-calendar-alt'
            ];
        }
        
        // Construction du HTML complet
        $html = '
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques des Congés par Type</h6>
                </div>
                <div class="card-body">
                    <div class="row">';
        
        foreach ($typesConges as $type) {
            $html .= '
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-' . $type['couleur'] . ' shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-' . $type['couleur'] . ' text-uppercase mb-1">
                                    ' . $type['titre'] . '</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">' . $type['jours_pris'] . '/' . $type['jours_totaux'] . ' jours</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    ' . $type['jours_restants'] . ' jours restants
                                </div>
                                <div class="mt-2">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-' . $type['couleur'] . '" role="progressbar" 
                                            style="width: ' . $type['pourcentage_utilise'] . '%" 
                                            aria-valuenow="' . $type['pourcentage_utilise'] . '" 
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted">' . $type['pourcentage_utilise'] . '% utilisés</small>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas ' . $type['icone'] . ' fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">' . htmlspecialchars($type['description']) . '</small>
                        </div>
                        <div class="mt-2">
                            <a href="/conge/fiche/' . $idEmploye . '/type/' . $type['id_type'] . '#detailConge" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye fa-sm"></i> Détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>';
        }
        
        // Si aucun congé trouvé
        if (empty($typesConges)) {
            $html .= '
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucun type de congé configuré
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