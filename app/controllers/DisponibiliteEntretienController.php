<?php
namespace app\controllers;
use app\models\DateModel;
use Flight;
class DisponibiliteEntretienController {
    public function renderPage(){
        return Flight::render("disponibilite_entretien");
    }
    public function liste() {
        $draw = $_GET["draw"] ? $_GET["draw"]: 1;
        $start = $_GET["start"];
        $length = $_GET["length"] ? $_GET["length"]: 1;
        $dateModel = new DateModel();
        $liste = Flight:: disponibiliteEntretienModel()->getDisponibiliteEntretienByIdAdmin($_SESSION["admin"]["id_admin"],$draw,$start,$length);
        ;
        for($i = 0 ;$i<count($liste["data"]); $i++){
            $jour = $liste["data"][$i]["jour"];
            $liste["data"][$i]["jour"] = DateModel::JourEnLettres[intval($jour)-1];
        }
            return Flight::json($liste);
    }
}
?>