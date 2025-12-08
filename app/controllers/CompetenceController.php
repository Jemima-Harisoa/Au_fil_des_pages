<?php

namespace app\controllers;

use Flight;
use app\models\CompetenceModel;

class CompetenceController
{
    public function __construct()
    {
        // plus d'instance persistante : on utilisera Flight::Competence() à chaque appel
    }
    /**
     * Log une action dans le système
     */
    private function logAction($type, $id, $details = [])
    {
        try {
            $sql = "
                INSERT INTO competence_audit_log 
                (operation_type, id_employe_operateur, details, date_operation)
                VALUES (:type, :user_id, :details, NOW())
            ";
            
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute([
                'type' => $type,
                'user_id' => Flight::get('session')['id'] ?? null,
                'details' => json_encode($details)
            ]);
        } catch (\Exception $e) {
            // Ne pas bloquer l'application si le log échoue
            error_log('Erreur de logging: ' . $e->getMessage());
        }
    }

    /**
     * POST /api/competences/recommandations/bulk-apply
     * Appliquer plusieurs recommandations en batch
     */
    public function appliquerRecommandationsBulk()
    {
        try {
            $data = Flight::request()->data;
            $ids = $data->ids ?? [];
            
            if (empty($ids)) {
                Flight::json([
                    'success' => false,
                    'message' => 'Aucune recommandation sélectionnée'
                ], 400);
                return;
            }
            
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "
                UPDATE competence_recommandations 
                SET statut = 'en_cours',
                    date_application = NOW()
                WHERE id_recommandation IN ($placeholders)
            ";
            
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute($ids);
            
            $this->logAction('bulk_apply_recommandations', null, [
                'ids' => $ids,
                'count' => count($ids),
                'utilisateur' => Flight::get('session')['id'] ?? null
            ]);
            
            Flight::json([
                'success' => true,
                'applied' => $stmt->rowCount(),
                'message' => 'Recommandations appliquées avec succès'
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * GET /competences/dashboard/admin
     * Affiche le dashboard administrateur
     */

    public function getDashboardAdmin()
    {
        try {
            // Vérifier les droits
            if (!$this->estAdministrateur() && !$this->estEmploye()) {
                Flight::redirect('/employe');
                return;
            }

            // Initialiser le modèle
            $competenceModel = Flight::Competence();
            
            // 1. Récupérer les données du dashboard global (utilise la vue matérialisée)
            $dashboardData = $competenceModel->getDashboardGlobal();
            
            // 2. Récupérer les statistiques réalistes des compétences
            $statsRealistes = $competenceModel->getRealisticCompetenceStats();
            
            // 3. Fusionner les données (priorité aux données réalistes)
            $dashboardData = array_merge($dashboardData, [
                'stats_realistes' => $statsRealistes['stats_globales'] ?? [],
                'competences_realistes' => $statsRealistes['competences'] ?? []
            ]);
            
            // 4. Récupérer les gaps critiques (sans filtre = tous)
            $gapsCritiques = $competenceModel->getGapsCritiques();
            
            // 5. Récupérer les alertes actives (sans filtre = toutes)
            $alertesActives = $competenceModel->getAlertesActives();
            
            // 6. Récupérer la cartographie optimisée pour les graphiques
            $cartographieOptimisee = $competenceModel->getCartographieOptimisee();
            
            // 7. Calculer les données pour les graphiques
            $chartData = $this->preparerDonneesGraphiques($cartographieOptimisee, $statsRealistes);
            
            // 8. Fusionner avec les données du dashboard
            $dashboardData = array_merge($dashboardData, $chartData);
            
            // 9. Calculer les tendances (top progression/régression)
            $tendances = $this->calculerTendances($cartographieOptimisee);
            $dashboardData['tendances'] = $tendances;

            // Afficher la vue avec les données réelles
            Flight::render('dashboard_admin', [
                'dashboardData' => $dashboardData,
                'gapsCritiques' => $gapsCritiques,
                'alertesActives' => $alertesActives,
                'tendancesData' => $tendances,
                'erreurs' => [],
                'estEmploye' => $this->estEmploye() && !$this->estAdministrateur(),
                'isAdmin' => $this->estAdministrateur(),
                'isEmploye' => $this->estEmploye()
            ]);
            
        } catch (\Exception $e) {
            error_log("Erreur critique getDashboardAdmin: " . $e->getMessage());
            
            // En cas d'erreur, utiliser des données par défaut
            Flight::render('dashboard_admin', [
                'dashboardData' => $this->genererDonneesTest(),
                'gapsCritiques' => ['gaps' => [], 'statistiques' => []],
                'alertesActives' => ['alertes' => [], 'statistiques' => []],
                'tendancesData' => ['progression' => [], 'regression' => []],
                'erreurs' => ['Erreur lors du chargement du dashboard: ' . $e->getMessage()],
                'estEmploye' => false,
                'isAdmin' => $this->estAdministrateur(),
                'isEmploye' => $this->estEmploye()
            ]);
        }
    }
    
    /**
     * Prépare les données pour les graphiques du dashboard
     */
    private function preparerDonneesGraphiques($cartographieOptimisee, $statsRealistes)
    {
        $chartData = [];
        
        // 1. Distribution des évaluations
        if (!empty($cartographieOptimisee)) {
            $distributionEvaluation = [];
            foreach ($cartographieOptimisee as $competence) {
                $evaluation = $competence['evaluation_globale'] ?? 'Non évalué';
                if (!isset($distributionEvaluation[$evaluation])) {
                    $distributionEvaluation[$evaluation] = 0;
                }
                $distributionEvaluation[$evaluation]++;
            }
            
            $chartData['repartition_evaluation'] = [];
            foreach ($distributionEvaluation as $evaluation => $count) {
                $chartData['repartition_evaluation'][] = [
                    'evaluation_globale' => $evaluation,
                    'nb_competences' => $count
                ];
            }
        }
        
        // 2. Top 10 compétences par maturité
        if (!empty($statsRealistes['competences'])) {
            usort($statsRealistes['competences'], function($a, $b) {
                return ($b['indice_maturite'] ?? 0) <=> ($a['indice_maturite'] ?? 0);
            });
            
            $chartData['top_10_competences'] = array_slice($statsRealistes['competences'], 0, 10);
        }
        
        // 3. Distribution par domaine
        if (!empty($statsRealistes['competences'])) {
            $distributionDomaines = [];
            foreach ($statsRealistes['competences'] as $competence) {
                $domaine = $competence['domaine'] ?? 'Non classé';
                if (!isset($distributionDomaines[$domaine])) {
                    $distributionDomaines[$domaine] = 0;
                }
                $distributionDomaines[$domaine]++;
            }
            
            $chartData['distribution_domaines'] = [];
            foreach ($distributionDomaines as $domaine => $count) {
                $chartData['distribution_domaines'][] = [
                    'domaine' => $domaine,
                    'nb_competences' => $count
                ];
            }
        }
        
        // 4. Répartition de maturité
        if (!empty($statsRealistes['competences'])) {
            $repartitionMaturite = [
                'Très faible (<20)' => 0,
                'Faible (20-40)' => 0,
                'Moyenne (40-60)' => 0,
                'Bonne (60-80)' => 0,
                'Excellente (80-100)' => 0
            ];
            
            foreach ($statsRealistes['competences'] as $competence) {
                $maturite = $competence['indice_maturite'] ?? 0;
                if ($maturite < 20) $repartitionMaturite['Très faible (<20)']++;
                elseif ($maturite < 40) $repartitionMaturite['Faible (20-40)']++;
                elseif ($maturite < 60) $repartitionMaturite['Moyenne (40-60)']++;
                elseif ($maturite < 80) $repartitionMaturite['Bonne (60-80)']++;
                else $repartitionMaturite['Excellente (80-100)']++;
            }
            
            $chartData['repartition_maturite'] = [];
            foreach ($repartitionMaturite as $niveau => $count) {
                $chartData['repartition_maturite'][] = [
                    'niveau_maturite' => $niveau,
                    'nb_competences' => $count
                ];
            }
        }
        
        // 5. Compétences critiques (score < 40)
        if (!empty($statsRealistes['competences'])) {
            $chartData['competences_critiques'] = array_filter(
                $statsRealistes['competences'],
                fn($c) => ($c['indice_maturite'] ?? 100) < 40
            );
            $chartData['competences_critiques'] = array_slice(
                $chartData['competences_critiques'], 0, 10
            );
        }
        
        return $chartData;
    }

    /**
     * Calcule les tendances (top progression et régression)
     */
    private function calculerTendances($cartographieOptimisee)
    {
        $tendances = [
            'progression' => [],
            'regression' => []
        ];
        
        if (empty($cartographieOptimisee)) {
            return $tendances;
        }
        
        // Trie par taux de couverture (ascendant pour progression, descendant pour régression)
        usort($cartographieOptimisee, function($a, $b) {
            return ($a['taux_couverture'] ?? 0) <=> ($b['taux_couverture'] ?? 0);
        });
        
        // Top 5 progression (meilleure couverture)
        $tendances['progression'] = array_slice($cartographieOptimisee, -5, 5);
        $tendances['progression'] = array_reverse($tendances['progression']);
        
        // Top 5 régression (pire couverture)
        $tendances['regression'] = array_slice($cartographieOptimisee, 0, 5);
        
        return $tendances;
    }

    /**
     * GET /api/competences/departements
     * Liste des départements pour les filtres
     */
    public function getDepartements()
    {
        try {
            $sql = "
                SELECT id_departement, nom 
                FROM departements 
                WHERE actif = true 
                ORDER BY nom
            ";
            
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute();
            $departements = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            Flight::json([
                'success' => true,
                'departements' => $departements
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/competences
     * Liste des compétences pour les filtres
     */
    public function getCompetences()
    {
        try {
            $sql = "
                SELECT id_competence, nom, domaine
                FROM competences 
                ORDER BY nom
                LIMIT 50
            ";
            
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute();
            $competences = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            Flight::json([
                'success' => true,
                'data' => $competences
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * GET /api/competences/dashboard/charts
     * Données pour les graphiques du dashboard
     */
    public function getDashboardCharts()
    {
        try {
            // Données pour le graphique de distribution des niveaux
            $sqlNiveaux = "
                SELECT 
                    ROUND(niveau_moyen_simple) as niveau,
                    COUNT(*) as count
                FROM mv_competence_cartographie_optimisee
                GROUP BY ROUND(niveau_moyen_simple)
                ORDER BY niveau
            ";
            
            $stmtNiveaux = Flight::db()->prepare($sqlNiveaux);
            $stmtNiveaux->execute();
            $distributionNiveaux = $stmtNiveaux->fetchAll(\PDO::FETCH_ASSOC);
            
            // Données pour les compétences critiques
            $sqlCritiques = "
                SELECT 
                    nom,
                    score_maturite
                FROM mv_competence_cartographie_optimisee
                WHERE evaluation_globale = 'À développer'
                OR taux_couverture < 20
                ORDER BY score_maturite ASC
                LIMIT 10
            ";
            
            $stmtCritiques = Flight::db()->prepare($sqlCritiques);
            $stmtCritiques->execute();
            $competencesCritiques = $stmtCritiques->fetchAll(\PDO::FETCH_ASSOC);
            
            // Distribution par domaine
            $sqlDomaines = "
                SELECT 
                    domaine,
                    COUNT(*) as count
                FROM mv_competence_cartographie_optimisee
                WHERE domaine IS NOT NULL
                GROUP BY domaine
                ORDER BY count DESC
                LIMIT 10
            ";
            
            $stmtDomaines = Flight::db()->prepare($sqlDomaines);
            $stmtDomaines->execute();
            $distributionDomaines = $stmtDomaines->fetchAll(\PDO::FETCH_ASSOC);
            
            // Évaluation globale
            $sqlEvaluation = "
                SELECT 
                    evaluation_globale,
                    COUNT(*) as count
                FROM mv_competence_cartographie_optimisee
                GROUP BY evaluation_globale
            ";
            
            $stmtEvaluation = Flight::db()->prepare($sqlEvaluation);
            $stmtEvaluation->execute();
            $distributionEvaluation = $stmtEvaluation->fetchAll(\PDO::FETCH_ASSOC);
            
            Flight::json([
                'success' => true,
                'data' => [
                    'distribution_niveaux' => $distributionNiveaux,
                    'competences_critiques' => $competencesCritiques,
                    'distribution_domaines' => $distributionDomaines,
                    'distribution_evaluation' => $distributionEvaluation
                ]
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/competences/recommandations/en-attente
     * Liste des recommandations en attente
     */
    public function getRecommandationsEnAttente()
    {
        try {
            $sql = "
                SELECT 
                    r.*,
                    c.nom as competence_nom,
                    CONCAT(p.nom, ' ', p.prenom) as employe_nom,
                    e.poste,
                    d.nom as departement_nom
                FROM competence_recommandations r
                JOIN competences c ON r.id_competence = c.id_competence
                JOIN employes e ON r.id_employe = e.id_employe
                JOIN personnes p ON e.id_personne = p.id_personne
                LEFT JOIN departements d ON e.id_departement = d.id_departement
                WHERE r.statut = 'en_attente'
                ORDER BY r.priorite DESC, r.date_creation DESC
            ";
            
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute();
            $recommandations = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            Flight::json([
                'success' => true,
                'data' => $recommandations,
                'count' => count($recommandations)
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/competences/recommandations/:id/appliquer
     * Appliquer une recommandation
     */
    public function appliquerRecommandation($id)
    {
        try {
            $data = Flight::request()->data;
            
            $sql = "
                UPDATE competence_recommandations 
                SET statut = 'en_cours',
                    date_application = NOW()
                WHERE id_recommandation = :id
            ";
            
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            // Log de l'action
            // $this->logAction('appliquer_recommandation', $id, [
            //     'utilisateur' => Flight::get('session')['id'] ?? null,
            //     'commentaire' => $data->commentaire ?? null
            // ]);
            
            Flight::json([
                'success' => true,
                'message' => 'Recommandation appliquée avec succès'
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/competences/alertes/:id/resoudre
     * Marquer une alerte comme résolue
     */
    public function resoudreAlerte($id)
    {
        try {
            $sql = "
                UPDATE competence_alertes 
                SET est_resolue = TRUE,
                    date_resolution = NOW()
                WHERE id_alerte = :id
            ";
            
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            Flight::json([
                'success' => true,
                'message' => 'Alerte résolue avec succès'
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * GET /api/competences/:id/indicateurs
     * Indicateurs détaillés d'une compétence
     */
    public function getIndicateursCompetence($id_competence)
    {
        try {
            $indicateurs = Flight::Competence()->getIndicateursCompetence($id_competence);
            
            if (empty($indicateurs)) {
                Flight::json([
                    'success' => false,
                    'message' => 'Compétence non trouvée'
                ], 404);
                return;
            }
            
            Flight::json([
                'success' => true,
                'data' => $indicateurs
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => Flight::get('flight.log_errors') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * GET /api/competences/:id/trend
     * Tendance d'une compétence sur période
     */
    public function getTrendCompetence($id_competence, $periode = '3 months')
    {
        try {
            // Valider la période
            $periodesValides = ['1 month', '3 months', '6 months', '1 year'];
            if (!in_array($periode, $periodesValides)) {
                $periode = '3 months';
            }
            
            $tendance = Flight::Competence()->getTrendCompetence($id_competence, $periode);
            
            Flight::json([
                'success' => true,
                'data' => $tendance,
                'periode' => $periode
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/gaps/critiques
     * Liste des gaps critiques
     */
    public function getGapsCritiques($id_departement = null)
    {
        try {
            $gaps = Flight::Competence()->getGapsCritiques($id_departement);
            
            Flight::json([
                'success' => true,
                'data' => $gaps
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/alertes
     * Liste des alertes actives avec filtres
     */
    public function getAlertesActives()
    {
        try {
            // Récupérer les filtres depuis la requête
            $filtres = [
                'type_alerte' => Flight::request()->query->type_alerte,
                'severite' => Flight::request()->query->severite,
                'departement' => Flight::request()->query->departement,
                'competence' => Flight::request()->query->competence,
                'resolue' => Flight::request()->query->resolue === 'true'
            ];
            
            // Filtrer les valeurs null
            $filtres = array_filter($filtres, function($value) {
                return $value !== null && $value !== '';
            });
            
            $alertes = Flight::Competence()->getAlertesActives($filtres);
            
            Flight::json([
                'success' => true,
                'data' => $alertes,
                'filtres' => $filtres
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/recommandations/employe/:id
     * Recommandations personnalisées pour un employé
     */
    public function getRecommandationsEmploye($id_employe)
    {
        try {
            // Vérifier que l'employé existe
            $sqlCheck = "SELECT COUNT(*) FROM employes WHERE id_employe = :id";
            $stmt = Flight::db()->prepare($sqlCheck);
            $stmt->execute(['id' => $id_employe]);
            
            if ($stmt->fetchColumn() == 0) {
                Flight::json([
                    'success' => false,
                    'message' => 'Employé non trouvé'
                ], 404);
                return;
            }
            
            $recommandations = Flight::Competence()->getRecommandationsEmploye($id_employe);
            
            Flight::json([
                'success' => true,
                'data' => $recommandations
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/competences/job/batch
     * Déclencher job batch manuel
     */
    public function executeJobBatch()
    {
        try {
            // Vérifier les permissions (ex: admin seulement)
            // $this->checkAdminPermission();
            
            $resultat = Flight::Competence()->executeJobBatch();
            
            Flight::json($resultat);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/competences/job/incremental
     * Webhook pour traitement incrémental
     */
    public function executeJobIncremental()
    {
        try {
            $data = Flight::request()->data;
            
            // Validation des paramètres
            if (empty($data->id_employe) || empty($data->id_competence)) {
                Flight::json([
                    'success' => false,
                    'message' => 'Paramètres id_employe et id_competence requis'
                ], 400);
                return;
            }
            
            $resultat = Flight::Competence()->executeJobIncremental(
                $data->id_employe,
                $data->id_competence
            );
            
            Flight::json($resultat);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/competences/refresh-vues
     * Rafraîchir les vues matérialisées
     */
    public function refreshVuesMaterialisees()
    {
        try {
            $resultat = Flight::Competence()->refreshVuesMaterialisees();
            
            Flight::json($resultat);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/competences/purge-donnees
     * Purger les anciennes données
     */
    public function purgerAnciennesDonnees()
    {
        try {
            $data = Flight::request()->data;
            
            $config = [
                'jours_snapshot' => $data->jours_snapshot ?? 365,
                'jours_logs' => $data->jours_logs ?? 90,
                'jours_alertes' => $data->jours_alertes ?? 30
            ];
            
            $resultat = Flight::Competence()->purgerAnciennesDonnees($config);
            
            Flight::json($resultat);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/competences/doublons
     * Détecter les doublons
     */
    public function detecterDoublons($seuil = 0.9)
    {
        try {
            // Valider le seuil
            $seuil = floatval($seuil);
            if ($seuil < 0.5 || $seuil > 1.0) {
                $seuil = 0.9;
            }
            
            $doublons = Flight::Competence()->detecterDoublons($seuil);
            
            Flight::json([
                'success' => true,
                'data' => $doublons,
                'seuil' => $seuil
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/competences/dashboard
     * Dashboard global
     */
    public function getDashboardGlobal()
    {
        try {
            $dashboard = Flight::Competence()->getDashboardGlobal();
            
            Flight::json([
                'success' => true,
                'data' => $dashboard
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/competences/cartographie-optimisee
     * Cartographie optimisée
     */
    public function getCartographieOptimisee()
    {
        try {
            // Récupérer les filtres depuis la requête
            $filtres = [
                'domaine' => Flight::request()->query->domaine,
                'type_competence' => Flight::request()->query->type_competence,
                'evaluation' => Flight::request()->query->evaluation
            ];
            
            // Filtrer les valeurs null
            $filtres = array_filter($filtres, function($value) {
                return $value !== null && $value !== '';
            });
            
            $cartographie = Flight::Competence()->getCartographieOptimisee($filtres);
            
            Flight::json([
                'success' => true,
                'data' => $cartographie,
                'filtres' => $filtres,
                'count' => count($cartographie)
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifie les permissions admin
     */
    private function checkAdminPermission()
    {
        // Récupérer la session
        $session = Flight::get('session');
        
        if (!$session || $session['role'] !== 'admin') {
            Flight::json([
                'success' => false,
                'message' => 'Permission refusée'
            ], 403);
            exit;
        }
    }

    /**
     * POST /api/competences/import
     * Importer des données de compétences
     */
    public function importCompetences()
    {
        try {
            $this->checkAdminPermission();
            
            $data = Flight::request()->data;
            
            if (empty($data->type_import) || empty($data->donnees)) {
                Flight::json([
                    'success' => false,
                    'message' => 'Paramètres type_import et donnees requis'
                ], 400);
                return;
            }
            
            $typeImport = $data->type_import; // 'auto', 'manager', 'test', 'rh'
            $donnees = is_array($data->donnees) ? $data->donnees : json_decode($data->donnees, true);
            
            // Log de début d'import
            $this->logImport('import_competences', count($donnees), 'En cours', [
                'type_import' => $typeImport,
                'utilisateur' => Flight::get('session')['id'] ?? null
            ]);
            
            // Traitement de l'import
            $resultats = [];
            $erreurs = [];
            
            foreach ($donnees as $index => $ligne) {
                try {
                    // Valider la ligne
                    if (empty($ligne['id_employe']) || empty($ligne['competence']) || !isset($ligne['niveau'])) {
                        throw new \Exception("Ligne $index: champs requis manquants");
                    }
                    
                    // Chercher l'ID de la compétence
                    $id_competence = $this->getOrCreateCompetence($ligne['competence'], $ligne['domaine'] ?? null);
                    
                    // Chercher l'ID de la source
                    $id_source = $this->getSourceId($typeImport);
                    
                    // Insérer ou mettre à jour
                    $sql = "
                        INSERT INTO employe_competences 
                        (id_employe, id_competence, niveau, id_source, valide, date_mesure)
                        VALUES (:id_employe, :id_competence, :niveau, :id_source, :valide, NOW())
                        ON CONFLICT (id_employe, id_competence) 
                        DO UPDATE SET 
                            niveau = EXCLUDED.niveau,
                            id_source = EXCLUDED.id_source,
                            valide = EXCLUDED.valide,
                            date_mesure = EXCLUDED.date_mesure
                    ";
                    
                    $stmt = Flight::db()->prepare($sql);
                    $stmt->execute([
                        'id_employe' => $ligne['id_employe'],
                        'id_competence' => $id_competence,
                        'niveau' => min(5, max(1, $ligne['niveau'])), // Entre 1 et 5
                        'id_source' => $id_source,
                        'valide' => $typeImport !== 'auto' // Auto-évaluation non validée par défaut
                    ]);
                    
                    $resultats[] = [
                        'ligne' => $index,
                        'statut' => 'succes',
                        'id_competence' => $id_competence
                    ];
                    
                    // Déclencher traitement incrémental
                    Flight::Competence()->executeJobIncremental($ligne['id_employe'], $id_competence);
                    
                } catch (\Exception $e) {
                    $erreurs[] = [
                        'ligne' => $index,
                        'statut' => 'erreur',
                        'message' => $e->getMessage()
                    ];
                }
            }
            
            // Log de fin d'import
            $this->logImport('import_competences', count($donnees), 'Terminé', [
                'succes' => count($resultats),
                'erreurs' => count($erreurs)
            ]);
            
            Flight::json([
                'success' => true,
                'resultats' => $resultats,
                'erreurs' => $erreurs,
                'resume' => [
                    'total' => count($donnees),
                    'succes' => count($resultats),
                    'erreurs' => count($erreurs)
                ]
            ]);
            
        } catch (\Exception $e) {
            // Log d'erreur
            $this->logImport('import_competences', 0, 'Erreur', [
                'error' => $e->getMessage()
            ]);
            
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère ou crée une compétence
     */
    private function getOrCreateCompetence($nom, $domaine = null)
    {
        $sql = "SELECT id_competence FROM competences WHERE nom = :nom";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['nom' => $nom]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result['id_competence'];
        }
        
        // Créer la compétence
        $sql = "
            INSERT INTO competences (nom, domaine, created_at, updated_at)
            VALUES (:nom, :domaine, NOW(), NOW())
            RETURNING id_competence
        ";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([
            'nom' => $nom,
            'domaine' => $domaine
        ]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC)['id_competence'];
    }

    /**
     * Récupère l'ID d'une source d'évaluation
     */
    private function getSourceId($type)
    {
        $sql = "SELECT id_source FROM source_evaluation WHERE libelle = :libelle";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['libelle' => $type . '-evaluation']);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result['id_source'];
        }
        
        // Créer la source si elle n'existe pas
        $sql = "
            INSERT INTO source_evaluation (libelle, description)
            VALUES (:libelle, :description)
            RETURNING id_source
        ";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([
            'libelle' => $type . '-evaluation',
            'description' => 'Import ' . $type
        ]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC)['id_source'];
    }

    /**
     * Log une opération d'import
     */
    private function logImport($operation, $nbLignes, $statut, $details = [])
    {
        $sql = "
            INSERT INTO competence_audit_log 
            (operation_type, nb_lignes_affectees, statut, details, date_operation)
            VALUES (:operation, :nb_lignes, :statut, :details, NOW())
        ";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([
            'operation' => $operation,
            'nb_lignes' => $nbLignes,
            'statut' => $statut,
            'details' => json_encode($details)
        ]);
    }

    /**
     * GET /api/competences/stats-import
     * Statistiques des imports
     */
    public function getStatsImport()
    {
        try {
            $sql = "
                SELECT 
                    operation_type,
                    statut,
                    COUNT(*) as nb_operations,
                    SUM(nb_lignes_affectees) as total_lignes,
                    MIN(date_operation) as date_debut,
                    MAX(date_operation) as date_fin
                FROM competence_audit_log
                WHERE operation_type LIKE '%import%'
                GROUP BY operation_type, statut
                ORDER BY date_fin DESC
            ";
            
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            Flight::json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche la liste complète des compétences avec statistiques
     */
    public function getListeCompetences() {
        // Vérifier les droits d'administration ou si c'est un employé consultant
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::redirect('/employe');
            return;
        }


        $competenceModel = Flight::Competence();
        
        // Récupérer les paramètres de filtrage
        $params = [
            'page' => $_GET['page'] ?? 1,
            'perPage' => $_GET['perPage'] ?? 10,
            'search' => $_GET['search'] ?? '',
            'domaine' => $_GET['domaine'] ?? '',
            'niveau_min' => $_GET['niveau_min'] ?? 0,
            'sortField' => $_GET['sortField'] ?? 'nb_employes',
            'sortOrder' => $_GET['sortOrder'] ?? 'DESC'
        ];

        // Si c'est un employé (pas admin), on peut appliquer des restrictions supplémentaires
        if ($this->estEmploye() && !$this->estAdministrateur()) {
            $idEmploye = $this->getIdEmployeConnecte();
            // Optionnel: filtrer pour ne montrer que les compétences de l'employé
            // $params['id_employe'] = $idEmploye;
        }

        $tableau = $competenceModel->getTableauListeCompetences($params);
        $stats_section = $competenceModel->getSectionStatsCompetences();
        
        Flight::render('competences_admin', [
            'tableau' => $tableau,
            'stats_section' => $stats_section,
            'estEmploye' => $this->estEmploye() && !$this->estAdministrateur(),
            'params' => $params
        ]);
    }
    /**
     * Affiche les détails d'une compétence spécifique
     */
    public function getDetailsCompetence($idCompetence) {
    // Vérifier les droits
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $competenceModel = Flight::Competence();
        $detailsHTML = $competenceModel->getTableauDetailCompetence($idCompetence);
        
        if (!$detailsHTML) {
            Flight::json([
                'success' => false,
                'error' => 'Compétence non trouvée'
            ], 404);
            return;
        }

        // Vérifier que l'utilisateur a le droit de voir ces détails
        if (!$this->peutVoirCompetence($idCompetence)) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé à cette compétence'
            ], 403);
            return;
        }

        echo $detailsHTML;
    }
    /**
     * Exporte les compétences au format CSV ou PDF
     */
    public function exportCompetences() {
        // Vérifier les droits d'administration
        if (!$this->estAdministrateur()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $format = $_GET['format'] ?? 'csv';
        
        if (!in_array($format, ['csv', 'pdf'])) {
            Flight::json([
                'success' => false,
                'error' => 'Format non supporté'
            ], 400);
            return;
        }

        $competenceModel = Flight::Competence();
        
        try {
            $data = $competenceModel->exportCompetences($format);
            
            if ($format === 'csv') {
                $this->genererCSV($data);
            } elseif ($format === 'pdf') {
                $this->genererPDF($data);
            }

        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur lors de l\'export: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recherche de compétences (pour autocomplétion)
     */
    public function searchCompetences() {
        // Vérifier les droits
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $keyword = $_GET['q'] ?? '';
        
        if (empty($keyword)) {
            Flight::json([]);
            return;
        }

        $competenceModel = Flight::Competence();
        
        try {
            $results = $competenceModel->searchCompetences($keyword);
            Flight::json($results);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur lors de la recherche: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filtre les compétences selon plusieurs critères
     */
    public function filterCompetences() {
        // Vérifier les droits
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $filters = [
            'departement' => $_GET['departement'] ?? '',
            'niveau_min' => $_GET['niveau_min'] ?? 0,
            'domaine' => $_GET['domaine'] ?? ''
        ];

        $competenceModel = Flight::Competence();
        
        try {
            $results = $competenceModel->filterCompetences($filters);
            Flight::json($results);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur lors du filtrage: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche les statistiques globales des compétences
     */
    public function getStatsGlobales() {
        // Vérifier les droits
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $competenceModel = Flight::Competence();
        
        try {
            $stats = $competenceModel->getStatsGlobales();
            $sectionStats = $competenceModel->getSectionStatsCompetences();
            
            Flight::json([
                'success' => true,
                'stats' => $stats,
                'html' => $sectionStats
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifie si l'utilisateur peut voir les détails d'une compétence
     */
    private function peutVoirCompetence($idCompetence) {
        // Les admins peuvent tout voir
        if ($this->estAdministrateur()) {
            return true;
        }
        
        // Les employés peuvent voir toutes les compétences (ou adapter selon besoins)
        if ($this->estEmploye()) {
            return true;
        }
        
        return false;
    }

    /**
     * Génère un fichier CSV des compétences
     */
    private function genererCSV($data) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="competences_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // En-tête CSV
        fputcsv($output, [
            'ID', 'Nom', 'Domaine', 'Type', 'Nb Employés', 
            'Niveau Moyen', 'Nb Validés', 'Dernière MAJ'
        ]);
        
        // Données
        foreach ($data['data'] as $competence) {
            fputcsv($output, [
                $competence['id_competence'],
                $competence['nom'],
                $competence['domaine'],
                $competence['type_competence'] ?? '',
                $competence['nb_employes'],
                $competence['niveau_moyen'],
                $competence['nb_employes_valides'],
                $competence['derniere_maj_formatted']
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Génère un fichier PDF des compétences (à implémenter selon votre bibliothèque PDF)
     */
    private function genererPDF($data) {
        // Implémentation basique - à adapter avec votre bibliothèque PDF (TCPDF, Dompdf, etc.)
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="competences_' . date('Y-m-d') . '.pdf"');
        
        // Exemple basique - à remplacer par votre génération PDF
        $html = '<h1>Export des Compétences</h1>';
        $html .= '<p>Date d\'export: ' . $data['date_export'] . '</p>';
        $html .= '<p>Total compétences: ' . $data['total_competences'] . '</p>';
        
        // Conversion HTML en PDF (à implémenter)
        echo $html;
        exit;
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    private function estAdministrateur() {
        return isset($_SESSION['infoAdmin']['id_employe']);
    }

    /**
     * Vérifie si l'utilisateur est un employé connecté
     */
    private function estEmploye() {
        return isset($_SESSION['employe']['id_employe']);
    }

    /**
     * Récupère l'ID de l'employé connecté
     */
    private function getIdEmployeConnecte() {
        if (isset($_SESSION['infoAdmin']['id_employe'])) {
            return $_SESSION['infoAdmin']['id_employe'];
        } elseif (isset($_SESSION['employe']['id_employe'])) {
            return $_SESSION['employe']['id_employe'];
        } elseif (isset($_SESSION['utilisateur']['id_utilisateur'])) {
            return $_SESSION['utilisateur']['id_utilisateur'];
        }
        return null;
    }
    /**
 * Génère des données de test pour le dashboard
 */
private function genererDonneesTest()
{
    return [
        'statistiques_globales' => [
            'total_competences' => 156,
            'niveau_moyen_global' => 3.2,
            'couverture_moyenne' => 45.5
        ],
        'repartition_evaluation' => [
            ['evaluation_globale' => 'Excellent', 'nb_competences' => 25],
            ['evaluation_globale' => 'Bon', 'nb_competences' => 67],
            ['evaluation_globale' => 'Satisfaisant', 'nb_competences' => 45],
            ['evaluation_globale' => 'À développer', 'nb_competences' => 19]
        ],
        'top_10_competences' => [
            ['nom' => 'Gestion de projet', 'score_maturite' => 89],
            ['nom' => 'JavaScript', 'score_maturite' => 85],
            ['nom' => 'SQL', 'score_maturite' => 82],
            ['nom' => 'Python', 'score_maturite' => 78],
            ['nom' => 'React', 'score_maturite' => 76],
            ['nom' => 'Communication', 'score_maturite' => 75],
            ['nom' => 'Analyse de données', 'score_maturite' => 72],
            ['nom' => 'Docker', 'score_maturite' => 70],
            ['nom' => 'AWS', 'score_maturite' => 68],
            ['nom' => 'UX Design', 'score_maturite' => 65]
        ],
        'distribution_domaines' => [
            ['domaine' => 'Développement', 'nb_competences' => 67],
            ['domaine' => 'Gestion de projet', 'nb_competences' => 34],
            ['domaine' => 'Infrastructure', 'nb_competences' => 28],
            ['domaine' => 'Design', 'nb_competences' => 18],
            ['domaine' => 'Marketing', 'nb_competences' => 9]
        ],
        'repartition_maturite' => [
            ['niveau_maturite' => 'Débutant', 'nb_competences' => 23],
            ['niveau_maturite' => 'Intermédiaire', 'nb_competences' => 67],
            ['niveau_maturite' => 'Avancé', 'nb_competences' => 45],
            ['niveau_maturite' => 'Expert', 'nb_competences' => 21]
        ]
    ];
}
}