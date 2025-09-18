<?php
namespace app\controllers;
use app\models\ProfilsModel;
use app\models\TypeContratModel;
use app\models\AnnoncesModel;

use Flight;

class AnnoncesController {
    public function form() {
        $ProfilsModel = new ProfilsModel(Flight::db());
        $Profils = $ProfilsModel->getAll();
        
        $TypeContratModel = new TypeContratModel(Flight::db());
        $TypeContrat = $TypeContratModel->getAll();
        Flight::render('formAnnonces' , ['Profils' => $Profils ,  'TypeContrat' => $TypeContrat ]);
    }
    public static function create() {
    try {
        $titre = $_POST['titre'] ?? "Nouvelle annonce";
        $contenuAnnonce = $_POST['annonceText'] ?? "";
        $nombrePoste = $_POST['nombrePoste'] ?? 1;
        $idProfil = $_POST['id_profil'] ?? null;   // si tu envoies maintenant profilData, tu peux décoder JSON
        $date_expiration = date('Y-m-d', strtotime("+30 days"));

        // Si tu envoies le profil complet via profilData
        if (!empty($_POST['profilData'])) {
            $profil = json_decode($_POST['profilData'], true);
            $idProfil = $profil['id_profil'] ?? $idProfil;
        }

        $model = new AnnoncesModel();
        $filename = $model->create(
            $titre,
            $nombrePoste,
            $idProfil,
            $contenuAnnonce,
            $date_expiration
        );

        Flight::json(['success' => true, 'file' => $filename]);
        } catch (\Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public static function read()
    {
        $model = new AnnoncesModel();
        $Annonces = $model->getAll();
        Flight::render('listeAnnonces' , ['Annonces' => $Annonces]);            
    }

}

?>