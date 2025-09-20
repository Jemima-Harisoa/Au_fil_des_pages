<?php
use app\controllers\WelcomeController;
use app\controllers\TestController;
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
$Test_Controller = new TestController();

$router->get('/', [ $Welcome_Controller, 'home' ]);
$router->get('/testAccueil', [ $Test_Controller, 'QCM' ]); 
$router->post('/traitement-qcm', [ $Test_Controller, 'traitementQCM' ]); 
$router->get('/allTests', [ $Test_Controller, 'getList' ]); 
$router->get('/triMetier', [ $Test_Controller, 'getListByJob' ]); 
$router->get('/triageTests', [ $Test_Controller, 'getListSorted' ]); 
$router->get('/listTest', [ $Test_Controller, 'getAllQstWtRep' ]); 
$router->post('/deleteAjax',[$Test_Controller,'traitementDelete']);
$router->get('/triMetierQst',[$Test_Controller,'getAllTriQstWtRep']);
$router->post('/updateQstRep',[$Test_Controller,'traitementModifQstRep']);
$router->post('/addRep',[$Test_Controller,'ajoutQst']);
$router->post('/createTest', [$Test_Controller,'cheminTestWtMetier']);
$router->get('/createTest',[$Test_Controller,'cheminTestWtMetier']);
$router->post('/creationQst',[$Test_Controller,'traitementCreation']);