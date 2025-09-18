<?php
use app\controllers\WelcomeController;
use app\controllers\ConnexionController;
use app\controllers\AnnoncesController;

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
    $router->post('/create', [$AnnoncesController, 'createAnnonce']);
    $router->get('/read', [ $AnnoncesController, 'readAnnonces' ]);
    $router->get('/read/{id}', [ $AnnoncesController, 'readAnnonce' ]);
});

