<?php
namespace app\controllers;
use app\models\PlanningEntretienModel;
use Flight;
class PlanningEntretienController {
    public function showPageEntretien() {
        $candidats = Flight::testModel()->getCandidatsAvecSuccesTest(3);
        error_log("taille de candidats: ".count($candidats));
;        error_log($_SESSION['admin']["id_admin"]);
        $candidats = Flight::responsableEntretienModel()->getListeCandidatsAEntretenir($candidats,$_SESSION['admin']["id_admin"]);
        error_log("ahoana eto: ".count($candidats));
        $planningEntretien = Flight::planningEntretienModel();
        $allVerified = $planningEntretien-> checkCandidatsInEntretien($candidats);
        Flight::render('planning_entretien',["verified" => $allVerified]);
    }   

}
?>