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
        $data["id_admin"] = $_SESSION["admin"]["id_admin"];
        $model = Flight::disponibiliteEntretienModel();
        $result = $model->create($data);
        if($result != 0){
            return Flight::json(["message"=>"success"]);
        }
        else{
            return Flight::json(["message"=>"error"]);   
        }
    }
    public function modifier(){
        $input = file_get_contents("php://input");
        $data = json_decode($input,true);
        $data["id_admin"] = $_SESSION["admin"]["id_admin"];
        $disponibiliteEntretien = Flight::disponibiliteEntretienModel();
        $result = $disponibiliteEntretien->update($data);
        if($result){
            return Flight::json(["message"=>"success"]);
        }
        else{
            return Flight::json(["message"=>"error"]);   
        }
    }
    public function supprimer(){
        $input = file_get_contents("php://input");
        error_log("Contenu brut reçu : ".$input);
        $data = json_decode($input,true);
        error_log("Décodé : ".print_r($data, true));
        $disponibiliteEntretien = Flight::disponibiliteEntretienModel();
        $result  = $disponibiliteEntretien->delete($data);
                if($result){
            return Flight::json(["message"=>"success"]);
        }
        else{
            return Flight::json(["message"=>"error"]);   
        }
    }
}
?>