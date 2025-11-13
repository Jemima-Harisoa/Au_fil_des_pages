<?php
namespace app\controllers;
use app\models\PlanningEntretienModel;
use Flight;
class PlanningEntretienController extends SessionController{
    public function showPageEntretien() {
        parent::checkSessionAdmin();
        $candidats = Flight::testModel()->getCandidatsAvecSuccesTest(3);
        $candidats = Flight::responsableEntretienModel()->getListeCandidatsAEntretenir($candidats,$_SESSION['admin']["id_admin"]);
        $planningEntretien = Flight::planningEntretienModel();
        $allVerified = $planningEntretien-> checkCandidatsInEntretien($candidats);
        Flight::render('planning_entretien',["verified" => $allVerified]);
    }   
    public function renderPageScoreEntretien(){
        parent::checkSessionAdmin();
        
        Flight::render('scoring_entretien');
    }
    public function listeScoringEntretien(){
        $candidats = Flight::planningEntretienModel()->getEntretiensByIdEtatAndIdAdmin(5,$_SESSION["admin"]["id_admin"]);
        return Flight::json($candidats);
    }
}
?>