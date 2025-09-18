<?php
use app\controllers\WelcomeController;
use app\controllers\PlanningEntretienController;

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
$router->get('/planning_entretien',[$planning_entretien_controller,'showAllPlanningEntretien']);