<?php
namespace app\controllers;
use app\models\PlanningEntretienModel;
use Flight;
class PlanningEntretienController {
    public function showPageEntretien() {
        if(!isset($_SESSION["admin"]["id_admin"])){
            Flight::render('connexionA',null);
            return;
        }
        $candidats = Flight::testModel()->getCandidatsAvecSuccesTest(3);
        $candidats = Flight::responsableEntretienModel()->getListeCandidatsAEntretenir($candidats,$_SESSION['admin']["id_admin"]);
        $planningEntretien = Flight::planningEntretienModel();
        $allVerified = $planningEntretien-> checkCandidatsInEntretien($candidats);
        Flight::render('planning_entretien',["verified" => $allVerified]);
    }   

}
?>