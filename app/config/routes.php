<?php
use app\controllers\WelcomeController;
use app\controllers\ConnexionController;
use app\controllers\AnnoncesController;


use app\controllers\TestController;

use app\controllers\migration\MigrationController;

use flight\Engine;
use flight\net\Router;
//use Flight;

/** 
 * @var Router $router 
 * @var Engine $app
 */

$ConnexionController = new ConnexionController();
$router->get('/', [ $ConnexionController, 'AppelLoginU' ]);
$router->post('/inscriptionU', [ $ConnexionController, 'InscrireU' ]);
$router->post('/loginU', [ $ConnexionController, 'VerificationConnectionU' ]);
$router->get('/deconnexionU', [ $ConnexionController, 'deconnexionU' ]);

$router->get('/admin', [ $ConnexionController, 'AppelLoginA' ]);
$router->post('/inscriptionA', [ $ConnexionController, 'InscrireA' ]);
$router->post('/loginA', [ $ConnexionController, 'VerificationConnectionA' ]);
$router->get('/deconnexionA', [ $ConnexionController, 'deconnexionA' ]);


$WelcomeController = new WelcomeController();
$router->get('/accueilG', [ $WelcomeController, 'AppelAccueilG' ]);
$router->get('/accueilA', [ $WelcomeController, 'AppelAccueilA' ]);
$router->get('/accueilU', [ $WelcomeController, 'AppelAccueilU' ]);

$AnnoncesController = new AnnoncesController();
    
// Groupe annonces
$router->group('/annonces', function($router) use ($AnnoncesController) {
    $router->get('/form', [ $AnnoncesController, 'form' ]);
    $router->post('/create', [$AnnoncesController, 'create']);
    $router->get('/read', [ $AnnoncesController, 'read' ]);
    $router->get('/readU', [ $AnnoncesController, 'readU' ]);
    $router->get('/read/{id}', [ $AnnoncesController, 'read' ]);
    $router->post('/update', [ $AnnoncesController, 'update' ]);
});

$Welcome_Controller = new WelcomeController();
$Test_Controller = new TestController();

$router->get('/', [ $Welcome_Controller, 'home' ]);
$router->get('/testAccueil', [ $Test_Controller, 'QCM' ]); 
$router->post('/traitement-qcm', [ $Test_Controller, 'traitementQCM' ]); 
$router->get('/allTests', [ $Test_Controller, 'getList' ]); 
$router->get('/triMetier', [ $Test_Controller, 'getListByJob' ]); 
$router->get('/triageTests', [ $Test_Controller, 'getListSorted' ]); 

/***************Route Module RH / Features migration***************/

// Contrat
$Migration_Controller = new MigrationController(); 
//$router->get('/migration/Redaction',  [ $Contrat_Controller, 'RedactionContrat' ]);
$router->group( "/migration" , function($router) use ($Migration_Controller){
		// route de configuration 
		$router->get("/test", [$Migration_Controller, "test"]);
		// route vers la liste des candidat apres le scoring  
		$router->get("/candidats", [$Migration_Controller , 'getCandidatRetenu']);
		// route vers le formulaire de soumission de contrat de travail 
		$router->get("/contrat/create", [$Migration_Controller , 'createContrat']);
		// enregister le brouillon du contrat avant validation
		$router->post("/contrat/register", [$Migration_Controller, 'registerContrat']);
		// route vers la liste des contrat 
		$router->get("/contrats", [$Migration_Controller, 'getContrat']);

		// route vers la page d'editon des contrat pour retouche ou bien validation
		$router->get("/contrat/edit", [$Migration_Controller, 'editContrat']);   
	}
);