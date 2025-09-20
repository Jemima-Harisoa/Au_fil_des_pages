<?php

namespace app\controllers;

session_start();

use Flight;
use app\models\ConnexionModel;
use app\models\AdminModel;
use app\models\MessagerieModel;
class ConnexionController {

	public function __construct() {

	}

    
    // UTILISATEURS SIMPLES
    public static function AppelLoginU()
    {
		Flight::render('connexionU',null);
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
            Flight::render('accueilU',null);
        }
        else {
            $mess = "Verifier votre mot de passe ou votre nom d'utilisateur";
            Flight::render('connexionU', ['mess' => $mess]);
        }
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
    
    public function deconnexionU()
    {
        $model = new ConnexionModel(Flight::db());
        $model->deconnexion();
        Flight::redirect('/');
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
            if($_SESSION['departement']['id_departement'] ==  $idGestion  )
            {
                Flight::render('accueilG',null);    
            }
            else{
                Flight::render('accueilA',null);
            }


        }
        else {
            $mess = "Verifier votre mot de passe ou votre nom d'utilisateur admin";
            Flight::render('connexionA', ['mess' => $mess]);
        }
    }
    public function deconnexionA()
    {
        $model = new ConnexionModel(Flight::db());
        $model->deconnexion();
        Flight::redirect('/admin');
    }

    
}