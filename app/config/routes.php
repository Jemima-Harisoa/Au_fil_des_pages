<?php
use app\controllers\WelcomeController;
use app\controllers\migration\ContratController;

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
$Contrat_Controller = new ContratController(); 
$router->get('/migration/Redaction',  [ $Contrat_Controller, 'RedactionContrat' ]);