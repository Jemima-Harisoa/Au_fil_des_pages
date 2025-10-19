<?php
use app\controllers\WelcomeController;
use app\controllers\PlanningEntretienController;
use app\controllers\ApiPlanningEntretienController;
use app\controllers\ConnexionController;

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

$welcomeController = new WelcomeController();
$ConnexionController = new ConnexionController();
$router->get('/', [ $ConnexionController, 'AppelLoginU' ]);
$router->post('/inscriptionU', [ $ConnexionController, 'InscrireU' ]);
$router->post('/loginU', [ $ConnexionController, 'VerificationConnectionU' ]);
$router->get('/deconnexion', [ $ConnexionController, 'deconnexion' ]);
$router->get('/admin', [ $ConnexionController, 'AppelLoginA' ]);
$router->post('/inscriptionA', [ $ConnexionController, 'InscrireA' ]);
$router->post('/loginA', [ $ConnexionController, 'VerificationConnectionA' ]);
$planning_entretien_controller = new PlanningEntretienController();
$router->get('/planning-entretien',[$planning_entretien_controller,'showPageEntretien']);

$api_planning_entretien_controller = new ApiPlanningEntretienController();
$router->post('/planning_entretien/filtre', [$api_planning_entretien_controller, 'filtrerEntretien']);
$router->get('/api/planifier-entretien',[$api_planning_entretien_controller,'planifierEntretien']);

