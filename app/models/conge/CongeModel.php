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


    /**
     * Récupère le décompte des congés pris par un employé groupés par type
     * @param int $idEmploye ID de l'employé
     * @return string HTML formaté de la section congés
     */
    public function getNombreConge($idEmploye) {
        // Requête pour récupérer les congés par type
        $sql = "
            SELECT 
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
        $conges = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Générer les options du filtre type de congé
        $optionsType = '';
        foreach ($conges as $conge) {
            $optionsType .= '<option value="' . htmlspecialchars($conge['type_conge']) . '">' . htmlspecialchars($conge['type_conge']) . '</option>';
        }
        
        // Générer les lignes du tableau
        $lignesTableau = '';
        foreach ($conges as $conge) {
            $joursRestants = max(0, $conge['jours_totaux'] - $conge['jours_pris']);
            
            $lignesTableau .= '
                                                    <tr>
                                                        <td>' . htmlspecialchars($conge['type_conge']) . '</td>
                                                        <td>' . htmlspecialchars($conge['description']) . '</td>
                                                        <td>' . $conge['jours_pris'] . '</td>
                                                        <td>' . $joursRestants . '</td>
                                                    </tr>';
        }
        
        // Construction du HTML complet
        $html = '
                        <!-- Content Row - Détail Congés -->
                        <div class="row">

                            <!-- Détail des Congés par Type -->
                            <div class="col-xl-12 col-lg-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-primary">Détail des Congés par Type</h6>
                                        <!-- Bouton pour réinitialiser les filtres -->
                                        <button class="btn btn-sm btn-outline-secondary" id="resetFilters">
                                            <i class="fas fa-sync-alt"></i> Réinitialiser
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <!-- Filtres par colonne -->
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="typeFilter" class="small font-weight-bold">Type de congé</label>
                                                <select class="form-control form-control-sm" id="typeFilter">
                                                    <option value="">Tous les types</option>
                                                    ' . $optionsType . '
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="joursPrisFilter" class="small font-weight-bold">Jours pris</label>
                                                <select class="form-control form-control-sm" id="joursPrisFilter">
                                                    <option value="">Tous</option>
                                                    <option value="0">0 jour</option>
                                                    <option value="1-4">1-4 jours</option>
                                                    <option value="5+">5+ jours</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="joursRestantsFilter" class="small font-weight-bold">Jours restants</label>
                                                <select class="form-control form-control-sm" id="joursRestantsFilter">
                                                    <option value="">Tous</option>
                                                    <option value="0-5">0-5 jours</option>
                                                    <option value="6-15">6-15 jours</option>
                                                    <option value="16+">16+ jours</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="detailCongesTable" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th>Type de Congé</th>
                                                        <th>Description</th>
                                                        <th>Jours Pris</th>
                                                        <th>Jours Restants</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>Type de Congé</th>
                                                        <th>Description</th>
                                                        <th>Jours Pris</th>
                                                        <th>Jours Restants</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    ' . $lignesTableau . '
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
}