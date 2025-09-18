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
        $date_expiration = /*date('Y-m-d', strtotime("+30 days"))*/ null;

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
        Flight::render('listAnnonces' , ['Annonces' => $Annonces]);            
    }

    public static function update()
{
    try {
        // Lecture du JSON reçu
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id']) || empty($input['key'])) {
            throw new \Exception("Paramètres manquants");
        }

        $idAnnonce = (int) $input['id'];
        $key = $input['key'];
        $value = $input['value'];

        // Vérifier qu'on ne met à jour que la date_expiration
        if ($key !== 'date_expiration') {
            throw new \Exception("Seule la date d'expiration peut être modifiée");
        }

        // Si valeur vide → NULL
        if (empty($value)) {
            $value = null;
        } else {
            // Vérifier format de date
            $d = \DateTime::createFromFormat('Y-m-d', $value);
            if (!$d || $d->format('Y-m-d') !== $value) {
                throw new \Exception("Format de date invalide (attendu : YYYY-MM-DD)");
            }

            // Récupérer la date_publication de l'annonce
            $model = new \app\models\AnnoncesModel();
            $annonce = $model->get($idAnnonce);

            if (!$annonce) {
                throw new \Exception("Annonce introuvable");
            }

            $datePublication = new \DateTime($annonce['date_publication']);
            $dateExpiration = new \DateTime($value);

            // Vérifier que expiration >= publication
            if ($dateExpiration < $datePublication) {
                throw new \Exception("La date d'expiration ne peut pas être avant la date de publication");
            }
        }

        // Mise à jour
        $stmt = Flight::db()->prepare("
            UPDATE annonces SET date_expiration = :date_expiration WHERE id_annonce = :id
        ");
        $ok = $stmt->execute([
            'date_expiration' => $value,
            'id' => $idAnnonce
        ]);

        if (!$ok) {
            throw new \Exception("La mise à jour a échoué");
        }

        Flight::json(['success' => true, 'message' => 'Date mise à jour']);
    } catch (\Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()]);
    }
}



}

?>