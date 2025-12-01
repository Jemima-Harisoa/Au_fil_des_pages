<?php

namespace app\controllers;

use Flight;
use app\models\EmployeModel;

class EmployeeCompetenceController {

    /**
     * Soumet une auto-évaluation de compétence par un employé
     * POST /employees/:id/competences
     */
    public function createFromEmployee($id_employe) {
        // Vérifier que l'employé existe et est actif
        if (!$this->employeExisteEtActif($id_employe)) {
            Flight::json([
                'success' => false,
                'error' => 'Employé non trouvé ou non actif'
            ], 404);
            return;
        }

        // L'employé peut toujours modifier ses propres compétences (auto-évaluation)
        if (!$this->estLePropreEmploye($id_employe)) {
            Flight::json([
                'success' => false,
                'error' => 'Vous ne pouvez modifier que vos propres compétences'
            ], 403);
            return;
        }

        // Récupérer les données de la requête
        $data = Flight::request()->data;
        
        // Validation des données requises
        if (empty($data['id_competence']) || empty($data['niveau'])) {
            Flight::json([
                'success' => false,
                'error' => 'Données manquantes: id_competence et niveau sont requis'
            ], 400);
            return;
        }

        $employeModel = Flight::Employe();
        
        try {
            $success = $employeModel->submitSelfCompetence(
                $id_employe,
                $data['id_competence'],
                $data['niveau'],
                $data['details'] ?? []
            );

            if ($success) {
                Flight::json([
                    'success' => true,
                    'message' => 'Compétence auto-évaluée avec succès',
                    'data' => [
                        'id_employe' => $id_employe,
                        'id_competence' => $data['id_competence'],
                        'niveau' => $data['niveau'],
                        'valide' => false // Toujours false pour auto-évaluation
                    ]
                ], 201);
            } else {
                Flight::json([
                    'success' => false,
                    'error' => 'Erreur lors de la soumission de la compétence'
                ], 500);
            }

        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste les auto-évaluations d'un employé
     * GET /employees/:id/competences
     */
    public function listFromEmployee($id_employe) {
        // Vérifier que l'employé existe
        if (!$this->employeExiste($id_employe)) {
            Flight::json([
                'success' => false,
                'error' => 'Employé non trouvé'
            ], 404);
            return;
        }

        // L'employé peut voir ses propres compétences, l'admin peut voir toutes
        if (!$this->peutVoirCompetences($id_employe)) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $employeModel = Flight::Employe();
        
        try {
            $competences = $employeModel->listSelfSubmissions($id_employe);
            
            Flight::json([
                'success' => true,
                'data' => $competences,
                'count' => count($competences)
            ]);

        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des compétences: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les détails d'une compétence spécifique d'un employé
     * GET /employees/:id/competences/:id_competence
     */
    public function getCompetenceDetail($id_employe, $id_competence) {
        // Vérifier que l'employé existe
        if (!$this->employeExiste($id_employe)) {
            Flight::json([
                'success' => false,
                'error' => 'Employé non trouvé'
            ], 404);
            return;
        }

        if (!$this->peutVoirCompetences($id_employe)) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $employeModel = Flight::Employe();
        $competences = $employeModel->listSelfSubmissions($id_employe);
        
        // Trouver la compétence spécifique
        $competence = null;
        foreach ($competences as $comp) {
            if ($comp['id_competence'] == $id_competence) {
                $competence = $comp;
                break;
            }
        }

        Flight::json([
            'success' => true,
            'data' => $competence
        ]);
    }

    /**
     * Supprime une auto-évaluation de compétence
     * DELETE /employees/:id/competences/:id_competence
     */
    public function deleteCompetence($id_employe, $id_competence) {
        // L'employé peut toujours supprimer ses propres compétences
        if (!$this->estLePropreEmploye($id_employe)) {
            Flight::json([
                'success' => false,
                'error' => 'Vous ne pouvez supprimer que vos propres compétences'
            ], 403);
            return;
        }

        $employeModel = Flight::Employe();
        
        try {
            $success = $employeModel->deleteSelfCompetence($id_employe, $id_competence);

            if ($success) {
                Flight::json([
                    'success' => true,
                    'message' => 'Compétence supprimée avec succès'
                ]);
            } else {
                Flight::json([
                    'success' => false,
                    'error' => 'Erreur lors de la suppression de la compétence'
                ], 500);
            }

        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche le formulaire d'auto-évaluation des compétences
     * GET /employees/:id/competences/form
     */
    public function showCompetenceForm($id_employe) {
        // Vérifier que l'employé existe et est actif
        if (!$this->employeExisteEtActif($id_employe)) {
            echo '<div class="alert alert-danger">Employé non trouvé ou non actif</div>';
            return;
        }

        // Tout employé peut accéder à son propre formulaire, même sans compétences
        if (!$this->estLePropreEmploye($id_employe)) {
            echo '<div class="alert alert-danger">Vous ne pouvez accéder qu\'à votre propre formulaire</div>';
            return;
        }

        $employeModel = Flight::Employe();
        $competences = $employeModel->getAvailableCompetences();
        $niveaux = $employeModel->getNiveauLibelles();

        // Afficher la vue
        $this->renderCompetenceForm($id_employe, $competences, $niveaux);
    }

    /**
     * Affiche la liste des compétences de l'employé
     * GET /employees/:id/competences/list
     */
    public function showCompetenceList($id_employe) {
        // Vérifier que l'employé existe
        if (!$this->employeExiste($id_employe)) {
            echo '<div class="alert alert-danger">Employé non trouvé</div>';
            return;
        }

        // L'employé peut voir ses propres compétences
        if (!$this->peutVoirCompetences($id_employe)) {
            echo '<div class="alert alert-danger">Accès non autorisé</div>';
            return;
        }

        $employeModel = Flight::Employe();
        $competences = $employeModel->listSelfSubmissions($id_employe);

        // Afficher la vue
        $this->renderCompetenceList($id_employe, $competences);
    }

    /**
     * Vérifie si l'utilisateur peut modifier les compétences de cet employé
     * MODIFICATION : Tout employé peut modifier ses propres compétences
     */
    private function peutModifierCompetences($id_employe) {
        // Admin peut tout modifier
        if ($this->estAdministrateur()) {
            return true;
        }
        
        // Tout employé peut modifier ses propres compétences
        return $this->estLePropreEmploye($id_employe);
    }

    /**
     * Vérifie si l'utilisateur peut voir les compétences de cet employé
     */
    private function peutVoirCompetences($id_employe) {
        // Admin peut tout voir
        if ($this->estAdministrateur()) {
            return true;
        }
        
        // Employé ne peut voir que ses propres compétences
        return $this->estLePropreEmploye($id_employe);
    }

    /**
     * Vérifie si l'utilisateur est l'employé concerné
     */
    private function estLePropreEmploye($id_employe) {
        $id_employe_connecte = $this->getIdEmployeConnecte();
        return $id_employe_connecte && $id_employe_connecte == $id_employe;
    }

    /**
     * Vérifie si un employé existe
     */
    private function employeExiste($id_employe) {
        $employeModel = Flight::Employe();
        $employe = $employeModel->getEmployesWithDetails($id_employe);
        return $employe !== null;
    }

    /**
     * Vérifie si un employé existe et est actif
     */
    private function employeExisteEtActif($id_employe) {
        $employeModel = Flight::Employe();
        $employe = $employeModel->getEmployesWithDetails($id_employe);
        return $employe !== null && (!isset($employe['actif']) || $employe['actif'] == 1);
    }

    /**
     * Rendu du formulaire d'auto-évaluation
     */
    private function renderCompetenceForm($id_employe, $competences, $niveaux) {
        Flight::render('autoEval', [
            'id_employe' => $id_employe,
            'competences' => $competences,
            'niveaux' => $niveaux
        ]);
    }

    /**
     * Rendu de la liste des compétences
     */
    private function renderCompetenceList($id_employe, $competences) {
        Flight::render('listeAutoEval', [
            'id_employe' => $id_employe,
            'competences' => $competences,
            'getCouleurNiveau' => [$this, 'getCouleurNiveau']
        ]);
    }

    /**
     * Convertit le nom de couleur en code hexadécimal
     */
    private function getCouleurNiveau($couleur) {
        $couleurs = [
            'rouge' => '#e74a3b',
            'orange' => '#fd7e14',
            'jaune' => '#f6c23e',
            'vert' => '#1cc88a',
            'bleu' => '#4e73df'
        ];
        return $couleurs[strtolower($couleur)] ?? '#6c757d';
    }

    // Méthodes d'authentification (reprises de CompetenceController)
    private function estAdministrateur() {
        return isset($_SESSION['infoAdmin']['id_employe']);
    }

    private function getIdEmployeConnecte() {
        if (isset($_SESSION['infoAdmin']['id_employe'])) {
            return $_SESSION['infoAdmin']['id_employe'];
        } elseif (isset($_SESSION['employe']['id_employe'])) {
            return $_SESSION['employe']['id_employe'];
        }
        return null;
    }


    /**
     * Valide une compétence (API)
     * POST /api/validations/:entryId/validate
     */
    public function validateCompetence($entryId) {
        // Vérifier que l'utilisateur est administrateur
        if (!$this->estAdministrateur()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }
        
        $managerId = $this->getIdEmployeConnecte();
        $data = Flight::request()->data;
        
        // Validation des données
        if (empty($data['action'])) {
            Flight::json([
                'success' => false,
                'error' => 'Action manquante'
            ], 400);
            return;
        }
        
        $action = $data['action']; // 'valide', 'rejete', 'ajuste'
        
        // Pour 'valide' et 'ajuste', vérifier le niveau
        if ($action !== 'rejete') {
            if (empty($data['niveauFinal'])) {
                Flight::json([
                    'success' => false,
                    'error' => 'niveauFinal manquant pour cette action'
                ], 400);
                return;
            }
            
            $niveauFinal = (int)$data['niveauFinal'];
            if ($niveauFinal < 1 || $niveauFinal > 5) {
                Flight::json([
                    'success' => false,
                    'error' => 'Le niveau doit être compris entre 1 et 5'
                ], 400);
                return;
            }
        } else {
            $niveauFinal = 0; // Non utilisé pour rejete
        }
        
        $employeModel = Flight::Employe();
        
        try {
            $success = $employeModel->validateCompetence($entryId, $niveauFinal, $action, $managerId);
            
            if ($success) {
                Flight::json([
                    'success' => true,
                    'message' => "Action $action effectuée avec succès",
                    'data' => [
                        'entryId' => $entryId,
                        'action' => $action,
                        'validateur' => $managerId
                    ]
                ]);
            } else {
                Flight::json([
                    'success' => false,
                    'error' => 'Aucune modification effectuée'
                ], 404);
            }
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ajoute une compétence observée par le manager (API)
     * POST /api/employees/:id/competences/manager
     */
    public function addManagerCompetence($id_employe) {
        // Vérifier que l'utilisateur est administrateur
        if (!$this->estAdministrateur()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }
        
        // Vérifier que l'employé existe
        if (!$this->employeExiste($id_employe)) {
            Flight::json([
                'success' => false,
                'error' => 'Employé non trouvé'
            ], 404);
            return;
        }
        
        $managerId = $this->getIdEmployeConnecte();
        $data = Flight::request()->data;
        
        // Validation des données
        if (empty($data['id_competence']) || empty($data['niveau'])) {
            Flight::json([
                'success' => false,
                'error' => 'Données manquantes: id_competence et niveau sont requis'
            ], 400);
            return;
        }
        
        $id_competence = (int)$data['id_competence'];
        $niveau = (int)$data['niveau'];
        
        if ($niveau < 1 || $niveau > 5) {
            Flight::json([
                'success' => false,
                'error' => 'Le niveau doit être compris entre 1 et 5'
            ], 400);
            return;
        }
        
        $employeModel = Flight::Employe();
        
        try {
            $success = $employeModel->addManagerObservedCompetence(
                $id_employe,
                $id_competence,
                $niveau,
                $managerId
            );
            
            if ($success) {
                Flight::json([
                    'success' => true,
                    'message' => 'Compétence ajoutée avec succès',
                    'data' => [
                        'id_employe' => $id_employe,
                        'id_competence' => $id_competence,
                        'niveau' => $niveau,
                        'valide' => true,
                        'validateur' => $managerId
                    ]
                ], 201);
            } else {
                Flight::json([
                    'success' => false,
                    'error' => 'Erreur lors de l\'ajout de la compétence'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valide plusieurs compétences en une fois (API)
     * POST /api/validations/bulk-validate
     */
    public function bulkValidate() {
        // Vérifier que l'utilisateur est administrateur
        if (!$this->estAdministrateur()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }
        
        $managerId = $this->getIdEmployeConnecte();
        $data = Flight::request()->data;
        
        if (empty($data['entries']) || !is_array($data['entries'])) {
            Flight::json([
                'success' => false,
                'error' => 'Données manquantes: entries (tableau) est requis'
            ], 400);
            return;
        }
        
        $employeModel = Flight::Employe();
        
        try {
            $results = $employeModel->bulkValidateCompetences($managerId, $data['entries']);
            
            Flight::json([
                'success' => true,
                'message' => 'Validation en masse effectuée',
                'data' => $results,
                'count' => count($results)
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche le dashboard de validation managériale
     * GET /validations/dashboard
     */
    public function showValidationDashboard() {
        // Vérifier que l'utilisateur est administrateur/manager
        if (!$this->estAdministrateur()) {
            echo '<div class="alert alert-danger">Accès non autorisé</div>';
            return;
        }
        
        $managerId = $this->getIdEmployeConnecte();
        $employeModel = Flight::Employe();
        
        try {
            $pendingValidations = $employeModel->getPendingValidations();
            $competences = $employeModel->getAvailableCompetences();
            $niveaux = $employeModel->getNiveauLibelles();
            
            // Afficher la vue
            Flight::render('validationDashboard', [
                'pendingValidations' => $pendingValidations,
                'competences' => $competences,
                'niveaux' => $niveaux,
                'managerId' => $managerId,
                'getCouleurNiveau' => [$this, 'getCouleurNiveau']
            ]);
            
        } catch (\Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /**
     * Liste les auto-évaluations en attente de validation (API)
     * GET /api/validations/pending
     */
    public function listPendingValidations() {
        // Vérifier que l'utilisateur est administrateur
        if (!$this->estAdministrateur()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }
        
        $employeModel = Flight::Employe();
        
        try {
            $pending = $employeModel->getPendingValidations();
            
            Flight::json([
                'success' => true,
                'data' => $pending,
                'count' => count($pending)
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }
}