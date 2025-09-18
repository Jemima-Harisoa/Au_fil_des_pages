<?php
namespace app\controllers;
use app\models\PlanningEntretienModel;
use Flight;
class PlanningEntretienController {
    public function showAllPlanningEntretien() {
        Flight::render('planning_entretien');
    }
}
?>