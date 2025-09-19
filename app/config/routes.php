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
	$router->get("/candidats", [$Migration_Controller , 'getCandidatRetenu']);

}
);