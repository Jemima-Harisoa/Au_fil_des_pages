<?php
use app\controllers\WelcomeController;
use app\controllers\PlanningEntretienController;
use app\controllers\ApiPlanningEntretienController;

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

$planning_entretien_controller = new PlanningEntretienController();
$router->get('/planning-entretien',[$planning_entretien_controller,'showPageEntretien']);

$api_planning_entretien_controller = new ApiPlanningEntretienController();
$router->get('/api/planifier-entretien',[$api_planning_entretien_controller,'planifierEntretien']);

