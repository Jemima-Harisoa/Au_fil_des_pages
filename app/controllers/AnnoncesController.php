<?php
namespace app\controllers;
use app\models\ProfilsModel;
use app\models\TypeContratModele;
use app\models\AnnoncesModele;

use Flight;

class AnnoncesController {
    public function form() {
        $ProfilsModel = new ProfilsModel(Flight::db());
        $Profils = $ProfilsModel->getAll();
        
        $TypeContratModele = new TypeContratModele(Flight::db());
        $TypeContrat = $TypeContratModele->getAll();
        Flight::render('formAnnonces' , ['Profils' => $Profils ,  'TypeContrat' => $TypeContrat ]);
    }
    public static function create() {
        try {
            $titre = $_POST['titre'] ?? "Nouvelle annonce";
            $contenuAnnonce = $_POST['annonceText'] ?? "";
            $nombrePoste = $_POST['nombrePoste'] ?? 1;
            $idProfil = $_POST['id_profil'] ?? null;
            $lien = ""; // si tu veux stocker un lien
            $date_expiration = date('Y-m-d', strtotime("+30 days"));

            $model = new AnnoncesModel();
            $filename = $model->create(
                $titre,
                $lien,
                $date_expiration,
                $nombrePoste,
                $idProfil,
                $contenuAnnonce
            );

            Flight::json(['success' => true, 'file' => $filename]);
        } catch (\Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

?>