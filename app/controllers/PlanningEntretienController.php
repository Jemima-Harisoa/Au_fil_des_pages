<?php
namespace app\controllers;
use app\models\PlanningEntretienModel;
use Flight;
class PlanningEntretienController {
    public function showPageEntretien() {
        $candidats = Flight::testModel()->getCandidatsAvecSuccesTest(4);
        $_SESSION["IdAdmin"] = 3;
        $candidats = Flight::responsableEntretienModel()->getListeEntretiensInListeCandidats($candidats,3);
        $planningEntretien = Flight::planningEntretienModel();
        $allVerified = $planningEntretien-> checkCandidatsInEntretien($candidats);
        $entretiens = $planningEntretien->all();
        Flight::render('planning_entretien',["entretiens" => $entretiens]);
    }

}
?>