<?php

namespace app\controllers;

session_start();

use Flight;
use app\models\ConnexionModel;
use app\models\AdminModel;
use app\models\PointageModel;
use app\models\MessagerieModel;
use app\models\ConnexEmployesModel;

use app\models\EmployeModel;
class ConnexionController {

	public function __construct() {

	}

    
    // UTILISATEURS SIMPLES
    public static function AppelLoginU()
    {
		Flight::render('connexionU',null);
    }
     public static function AppelLoginE()
    {
		Flight::render('connexionE',null);
    }

	public function VerificationConnectionU()
    {
        $p = new ConnexionModel(Flight::db());

        $messagerieModel = new messagerieModel(Flight::db());
        
            $Nom = $_POST['Nom'];
            $mdp = $_POST['mdp'];
        $v = $p->verifierUtilisateur($Nom, $mdp);
        if($v == true)
        {
            $_SESSION['utilisateur']  = $p->getUtilisateur($Nom, $mdp);
            $_SESSION['messagerie'] = $messagerieModel->getTitresConversationsU($_SESSION['utilisateur']['id_utilisateur']);  
            $_SESSION['nbNonLus'] = $messagerieModel->countNouveauxMessagesU($_SESSION['utilisateur']['id_utilisateur']);
            
            Flight::render('accueilU',null);

        }
        else {
            $mess = "Verifier votre mot de passe ou votre nom d'utilisateur";
            Flight::render('connexionU', ['mess' => $mess]);
        }
    }
    public function VerificationConnectionE()
    {
        $employeModel = new EmployeModel(Flight::db());
        $messagerieModel = new messagerieModel(Flight::db());
        $pointageModel = new PointageModel(Flight::db());

        $Nom = $_POST['Nom'];
        $mdp = $_POST['mdp'];
       
        $idEmploye = $employeModel->verifierEmploye($Nom, $mdp);

        if ($idEmploye) {

            // ---- 🔹 Récupérer les infos de l'employé ----
            $_SESSION['employe'] = $employeModel->getInfosEmploye($idEmploye);

            // ---- 🔹 Processus de pointage ----
            if (!isset($_SESSION['pointage_en_cours'])) {
                $dernierPointage = $pointageModel->getDernierPointage($idEmploye);

                if (!$dernierPointage || $dernierPointage['deconnexion'] != null) {
                    // Aucun pointage ouvert → créer une nouvelle ligne
                    $pointageModel->ajouterPointage($idEmploye);
                }

                $_SESSION['pointage_en_cours'] = true; // marque que le pointage est ouvert
            }

            // ---- 🔹 Messagerie ----
            $model = new \app\models\MessagerieModel();
            $_SESSION['messagerie'] = $model->getTitresConversationsE($_SESSION['employe']['id_employe']);
            $_SESSION['nbNonLus'] = $model->countNouveauxMessagesE($_SESSION['employe']['id_employe']);
            
            // ---- 🔹 Affichage accueil ----
            Flight::render('accueilE', null);

        } else {
            $mess = "Vérifiez votre nom d'utilisateur ou votre mot de passe";
            Flight::render('connexionE', ['mess' => $mess]);
        }
    }


    public function deconnexionE() {
        $employeModel = new EmployeModel(Flight::db());                      
        $pointageModel = new PointageModel(Flight::db());

        if (!isset($_SESSION['employe'])) {
            Flight::render('connexionE', ['error' => 'Vous n\'êtes pas connecté']);
            return;
        }

        $idEmploye = $_SESSION['employe']['id_employe'];

        // ---- 🔹 Clôturer le dernier pointage ----
        $dernierPointage = $pointageModel->getDernierPointage($idEmploye);
        if ($dernierPointage && $dernierPointage['deconnexion'] == null) {
            $pointageModel->cloturerPointage($dernierPointage['id_pointage']);
        }

        // ---- 🔹 Détruire la session ----
        session_destroy();

        Flight::render('connexionE', null);
    }



    public function InscrireU()
    {
        $p = new ConnexionModel(Flight::db());
        
            $Nom = $_POST['Nom'];
            $mdp = $_POST['mdp'];
        $v = $p->inscrireUtilisateur($Nom, $mdp);
        
        if($v == true)
        {
            $_SESSION['utilisateur']  = $p->getUtilisateur($Nom, $mdp);     
            Flight::render('accueilU',null);
        
        }
        else {
            $mess = "Erreur lors de l'inscription , Verifiez votre connexion internet et reessayez";
            Flight::render('connexionU', ['mess' => $mess]);
        }
    }

    // ADMINS
    public static function AppelLoginA()
    {
		Flight::render('connexionA',null);
    }

	public function VerificationConnectionA()
    {
        //tezitra
        $idGestion = 1;
        $p = new ConnexionModel(Flight::db());
        $AdminModel = new AdminModel(Flight::db());

        $messagerieModel = new MessagerieModel(Flight::db());


        
            $Nom = $_POST['Nom'];
            $mdp = $_POST['mdp'];


        $v = $p->verifierAdmin($Nom, $mdp);
        if($v == true)
        {
            $_SESSION['admin']  = $p->getAdmin($Nom, $mdp);
            $_SESSION['departement']  = $p-> getDepartementAdmin($_SESSION['admin']['id_admin']);     
            $_SESSION['infoAdmin'] = $AdminModel -> getDetailsPersoAdmin($_SESSION['admin']['id_admin']);
            $_SESSION['messagerie'] = $messagerieModel->getTitresConversationsA();  
            $_SESSION['nbNonLus'] = $messagerieModel->countNouveauxMessagesA();

            // if($_SESSION['departement']['id_departement'] ==  $idGestion  )
            // {
    
            // }
            // else{
            //     Flight::render('accueilA',null);
            // }

            $idEmploye =$p->getIdEmployeAdmin($_SESSION['admin']['id_admin']);  
            
        if ($idEmploye) {

            // ---- 🔹 Récupérer les infos de l'employé ----
            $_SESSION['employe'] = $employeModel->getInfosEmploye($idEmploye);

            // ---- 🔹 Processus de pointage ----
            if (!isset($_SESSION['pointage_en_cours'])) {
                $dernierPointage = $pointageModel->getDernierPointage($idEmploye);

                if (!$dernierPointage || $dernierPointage['deconnexion'] != null) {
                    // Aucun pointage ouvert → créer une nouvelle ligne
                    $pointageModel->ajouterPointage($idEmploye);
                }

                $_SESSION['pointage_en_cours'] = true; // marque que le pointage est ouvert
            }

            // ---- 🔹 Messagerie ----
            $model = new \app\models\MessagerieModel();
            $_SESSION['messagerie'] = $model->getTitresConversationsE($_SESSION['employe']['id_employe']);
            $_SESSION['nbNonLus'] = $model->countNouveauxMessagesE($_SESSION['employe']['id_employe']);
            
            // ---- 🔹 Affichage accueil ----
            Flight::render('accueilG', null);

        }}
    }
    
    public function deconnexion() {
        $model = new ConnexionModel(Flight::db());

       
        if(isset($_SESSION['admin']))
        {
            $model->deconnexion();
            Flight::redirect('/admin');
        }else{
                    $model->deconnexion();
        Flight::redirect('/');
        }

    }
    

    
}