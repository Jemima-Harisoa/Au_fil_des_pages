<?php
namespace app\controllers;
use app\models\PlanningEntretienModel;
use Flight;
class PlanningEntretienController {
    public function showPageEntretien() {
        $candidats = Flight::testModel()->getCandidatsAvecSuccesTest(4);
        error_log("count oooo :  ".count($candidats));
        $_SESSION["IdAdmin"] = 1;
        $candidats = Flight::responsableEntretienModel()->getListeEntretiensInListeCandidats($candidats,1);
        $planningEntretien = Flight::planningEntretienModel();
        $allVerified = $planningEntretien-> checkCandidatsInEntretien($candidats);
        $entretiens = $planningEntretien->all();
        Flight::render('planning_entretien',["entretiens" => $entretiens]);
    }   

}
?>