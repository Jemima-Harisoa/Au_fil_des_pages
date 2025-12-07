<?php

namespace app\controllers;

//use app\models\DepartementModel;
use Flight;

use app\models\EvaluationModel;
//use app\models\PointageModel;
use PDO;

class EvaluationController {
    public function __construct() {

	}

    
    // LISTE + FILTRES
    public static function liste() {
    $evaModel = new EvaluationModel(Flight::db());

    $data = $evaModel->getAllEvaluations();
    Flight::render('liste.php', ['evaluations' => $data]);
}


   public function detail($id) {
    if (!is_numeric($id) || $id <= 0) {
        Flight::halt(400, "ID d'évaluation invalide");
        return;
    }

    $evaModel = new EvaluationModel(Flight::db());
    $evaluation = $evaModel->getEvaluationById($id);
    
    if (!$evaluation) {
        Flight::halt(404, "Évaluation non trouvée");
        return;
    }

    // Récupérer les détails des critères si l'évaluation est terminée
    $details = [];
    if ($evaluation['statut'] == 'TERMINEE') {
        $details = $evaModel->getEvaluationDetails($id);
    }

    Flight::render('detail.php', [
        'evaluation' => $evaluation,
        'details' => $details
    ]);
}
    // FORM SAISIE NOTES
public static function saisie($id) {
    $evaModel = new EvaluationModel(Flight::db());
    $evaluation = $evaModel->getEvaluationById($id);
    $criteres = $evaModel->getCriteriaForEvaluation($id);

    Flight::render('saisie.php', [
        'evaluationId' => $id,
        'evaluation' => $evaluation, // ajout pour infos générales
        'criteres' => $criteres
    ]);
}
public static function saveNotes($id) {
    $evaModel = new EvaluationModel(Flight::db());
    
    // Utiliser $_POST directement pour multipart/form-data
    $notes = $_POST['note'] ?? [];
    
    // Debug
    error_log("=== DEBUT SAUVEGARDE NOTES ===");
    error_log("ID Evaluation: " . $id);
    error_log("Notes reçues: " . print_r($notes, true));

    // Validation des notes
    foreach ($notes as $critereId => $note) {
        // Permettre les notes vides mais rejeter les notes invalides
        if ($note !== '' && (!is_numeric($note) || $note < 0 || $note > 10)) {
            error_log("Note invalide - Critère: $critereId, Note: $note");
            Flight::json([
                'success' => false,
                'message' => 'Les notes doivent être comprises entre 0 et 10'
            ]);
            return;
        }
    }

    // Sauvegarder les notes
    $success = $evaModel->saveNotesForEvaluation($id, $notes);

    if ($success) {
        error_log("=== SAUVEGARDE REUSSIE ===");
        Flight::json([
            'success' => true,
            'message' => 'Notes enregistrées avec succès',
            'redirect' => "/evaluation/$id/saisie" // URL plus explicite
        ]);
    } else {
        error_log("=== ERREUR SAUVEGARDE ===");
        Flight::json([
            'success' => false,
            'message' => 'Erreur lors de la sauvegarde des notes'
        ]);
    }
}
    // SCORE GLOBAL
    public static function score($id) {
        $evaModel = new EvaluationModel(Flight::db());
       
        $details = $evaModel->getEvaluationDetails($id);
        $calculatedScore = $evaModel->calculateScore($id);
        if($calculatedScore !== null){ {
             $updateStatut = $evaModel->updateEvaluationStatus($id, 'TERMINEE');
        }
       
        Flight::render('score.php', [
            'score' => $calculatedScore,
            'details' => $details
        ]);
    }
    }
    // HISTORIQUE EMPLOYÉ
    public static function historique($id) {
        $evaModel = new EvaluationModel(Flight::db());
        $data = $evaModel->getEmployeeEvaluations($id);
        Flight::render('historique.php', ['evaluations' => $data]);
    }
    // Dashboard Manager
public static function dashboardManager($managerId) {
    $evaModel = new EvaluationModel(Flight::db());
    $summary = $evaModel->getTeamEvaluationsSummary($managerId);
    $trend = $evaModel->getScoreTrends($managerId, 6); // derniers 6 mois
    $urgent = $evaModel->getTeamUrgentEvaluations($managerId);

    Flight::render('evaluations/dashboard_manager.php', [
        'summary' => $summary,
        'trend' => $trend,
        'urgent' => $urgent
    ]);
}

// Génération des performances
public static function performanceGeneration() {
    $annee = $_GET['annee'] ?? date('Y');
    $employeId = $_GET['employe'] ?? null;
    $managerId = $_GET['manager'] ?? null;

    $evaModel = new EvaluationModel(Flight::db());
    $performanceData = $evaModel->generatePerformance($annee, $employeId);

    Flight::render('evaluations/performance_generation.php', [
        'performanceData' => $performanceData
    ]);
}

}
