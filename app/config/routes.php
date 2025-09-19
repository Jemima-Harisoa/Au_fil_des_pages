<?php
use app\controllers\WelcomeController;
use app\controllers\migration\MigrationController;

use flight\Engine;
use flight\net\Router;
//use Flight;

/** 
 * @var Router $router 
 * @var Engine $app
 */
/*$router->get('/', function() use ($app) {
	$Welcome_Controller = new WelcomeController($app);
	$app->render('welcome', [ 'message' => 'It works!!' ]);
});*/

$Welcome_Controller = new WelcomeController();
$router->get('/', [ $Welcome_Controller, 'home' ]);

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
}
);