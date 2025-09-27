<?php
namespace app\controllers;
use app\models\PlanningEntretienModel;
use Flight;
class PlanningEntretienController {
    public function showPageEntretien() {
        $candidats = Flight::testModel()->getCandidatsAvecSuccesTest(4);
        $planningEntretien = Flight::planningEntretienModel();
        $allVerified = $planningEntretien-> checkCandidatsInEntretien($candidats);
        Flight::render('planning_entretien',["allVerified"=>$allVerified]);
    }
}
?>