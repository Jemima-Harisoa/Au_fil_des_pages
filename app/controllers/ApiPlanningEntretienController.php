<?php
namespace app\controllers;
use Flight;
class ApiPlanningEntretienController {
    public function planifierEntretien() {
        try {
            // Récupère les 4 meilleurs candidats
            $candidats = Flight::testModel()->getCandidatsAvecSuccesTest(4);

            // Id responsable (tu peux le récupérer depuis une requête POST, une session, etc.)
            $idResponsable = $_SESSION["IdAdmin"]; // ou Flight::request()->data->id_responsable;
            // Appel du modèle
            $resultat = Flight::planningEntretienModel()->planifierEntretien($candidats, $idResponsable);

            // Retour JSON (le modèle retourne déjà du JSON si j’ai bien vu, mais tu peux sécuriser ici)
            Flight::json([
                "message" => "Planification terminée",
                "resultat" => Flight::planningEntretienModel()->getEntretiensParEtat(1),
                "status" => 200
            ]);
        } catch (\Exception $e) {
            Flight::json([
                "message" => $e->getMessage(),
                "status" => 500
            ]);
        }
    }

    public function filtrerEntretien(){
        $data = Flight::request()->data->getData();
        $data["id_admin"] = $_SESSION["IdAdmin"];
        return Flight::json(Flight::planningEntretienModel()->filtreEntretien($data));
    }
    public function updateEntretien(){
        $input = file_get_contents("php://input");
        $data = json_decode($input,true);
        $response = Flight::json(Flight::planningEntretienModel()->modifierEntretien($data));
    }
}
?>