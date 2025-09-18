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

$router->get('/', [ $cvController, 'home' ]);

$router->get('/@idUser/Annonce', [ $cvController, 'redirectCV' ]);
$router->get('/@idUser/Annonce/@idAnnonce/fillCV', [ $cvController, 'fillCV']);

$router->post('/@idUser/Annonce/@idAnnonce/fillCV/postulationCV',[ $cvController, 'printDataCV']);
// $router->get('/CV', [ $cvController, 'redirectCV']);

// $router->get('/CV/fillCV/@idUser/@idAnnonce', [ $cvController, 'fillCV']);

// $router->post('/CV/fillCV/postulationCV', [ $cvController, 'getDataCV']);
