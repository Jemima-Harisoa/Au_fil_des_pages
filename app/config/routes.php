<?php
use app\controllers\WelcomeController;
use app\controllers\PlanningEntretienController;
use app\controllers\ApiPlanningEntretienController;
use app\controllers\DisponibiliteEntretienController;
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

$router->post('/loginU', [ $ConnexionController, 'VerificationConnectionU' ]); // 11.16

$router->get('/deconnexionU', [ $ConnexionController, 'deconnexionU' ]);

$router->get('/admin', [ $ConnexionController, 'AppelLoginA' ]);
$router->post('/inscriptionA', [ $ConnexionController, 'InscrireA' ]);
$router->post('/loginA', [ $ConnexionController, 'VerificationConnectionA' ]);
$router->get('/deconnexionA', [ $ConnexionController, 'deconnexionA' ]);
$planning_entretien_controller = new PlanningEntretienController();
$router->get('/planning-entretien',[$planning_entretien_controller,'showPageEntretien']);
$router->get('/scoring-entretien',[$planning_entretien_controller,'renderPageScoreEntretien']);
$router->post('/scoring-entretien/liste',[$planning_entretien_controller,'listeScoringEntretien']);
$router->post('/scoring-entretien/@id',[$planning_entretien_controller,'listeScoringEntretien']);

$api_planning_entretien_controller = new ApiPlanningEntretienController();
$router->post('/planning_entretien/filtre', [$api_planning_entretien_controller, 'filtrerEntretien']);
$router->get('/api/planifier-entretien',[$api_planning_entretien_controller,'planifierEntretien']);
$router->post('/api/planning-entretien/modification',[$api_planning_entretien_controller,'updateEntretien']);

$disponibilite_entretien_controller = new DisponibiliteEntretienController();
$router->get('/disponibilite-entretien/liste',[$disponibilite_entretien_controller,'liste']);
$router->get('/disponibilite-entretien',[$disponibilite_entretien_controller,'renderPage']);
$router->post('/disponibilite-entretien/insertion',[$disponibilite_entretien_controller,'creer']);
$router->post('/disponibilite-entretien/modification',[$disponibilite_entretien_controller,'modifier']);
$router->post('/disponibilite-entretien/suppression',[$disponibilite_entretien_controller,'supprimer']);