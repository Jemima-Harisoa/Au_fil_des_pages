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
    public function creer(){
        $input = file_get_contents("php://input");
        $data = json_decode($input,true);
        error_log(count($data));
        $data["id_responsable"] = Flight::responsableEntretienModel()->findByIdAdmin($_SESSION["admin"]["id_admin"])[0]["id_responsable"];
        error_log($data["id_responsable"]);
        $model = Flight::disponibiliteEntretienModel();
        $model->create($data);
    }
    public function modifier(){
        $input = file_get_contents("php://input");
        $data = json_decode($input,true);
        $disponibiliteEntretien = Flight::disponibiliteEntretienModel();
        $disponibiliteEntretien->update($data);
    }
    public function supprimer(){
        $id_dispo = $_POST["data"]["id_dispo"];
        $disponibiliteEntretien = Flight::disponibiliteEntretienModel();
        $disponibiliteEntretien->delete($id_dispo);
    }
}
?>