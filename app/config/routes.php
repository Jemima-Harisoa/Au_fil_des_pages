<?php
use app\controllers\WelcomeController;
use app\controllers\cvController;

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
$cvController = new cvController();

$router->get('/', [ $Welcome_Controller, 'home' ]);
$router->get('/CV', [ $cvController, 'redirectCV']);
$router->get('/CV/fillCV', [ $cvController, 'fillCV']);
$router->post('/CV/fillCV/postulationCV', [ $cvController, 'getDataCV']);
