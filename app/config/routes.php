<?php
use app\controllers\WelcomeController;
use app\controllers\cvController;
use app\controllers\ConnexionController;
use app\controllers\AnnoncesController;

use app\controllers\TestController;
use app\controllers\MessagerieController;
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

// $router->get('/', [ $cvController, 'home' ]);

$router->get('/@idUser/Annonce', [ $cvController, 'redirectCV' ]);

$router->get('/@idUser/Annonce/@idAnnonce/@idProfil/fillCV', [ $cvController, 'fillCV']);

$router->post('/@idUser/Annonce/@idAnnonce/@idProfil/fillCV/postulationCV',[ $cvController, 'getDataCV']);

$router->get('/retourConfirmation',[ $cvController, 'retourAccueilU']);
$router->get('/retourFill',[ $cvController, 'retourAccueilU']);

$router->get('/listeCV',[ $cvController, 'listeCV']);

// $router->get('/CV', [ $cvController, 'redirectCV']);

// $router->get('/CV/fillCV/@idUser/@idAnnonce', [ $cvController, 'fillCV']);

// $router->post('/CV/fillCV/postulationCV', [ $cvController, 'getDataCV']);
$ConnexionController = new ConnexionController();
$router->get('/', [ $ConnexionController, 'AppelLoginU' ]);
$router->post('/inscriptionU', [ $ConnexionController, 'InscrireU' ]);

$router->post('/loginU', [ $ConnexionController, 'VerificationConnectionU' ]); // 11.16

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
    $router->post('/create', [$AnnoncesController, 'create']);
    $router->get('/read', [ $AnnoncesController, 'read' ]);
    $router->get('/readU', [ $AnnoncesController, 'readU' ]);
    $router->get('/read/{id}', [ $AnnoncesController, 'read' ]);
    $router->post('/update', [ $AnnoncesController, 'update' ]);
});

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

$MessagerieController = new MessagerieController();
$router->get('/messagerieU/@id_candidat/@id_annonce', [ $MessagerieController, 'showMessagerieU' ]);
$router->post('/messagerieU/send', [ $MessagerieController, 'sendMessageU' ]);
$router->post('/messagerieA/send', [ $MessagerieController, 'sendMessageA' ]);
$router->get('/messagerieA/@id_candidat/@id_annonce', [ $MessagerieController, 'showMessagerieA' ]);

$router->get('/api/refresh-notifications', [ $MessagerieController, 'refreshNotifications' ]);

// Routes existantes (à garder)
Flight::route('GET /messagerieU/@id_candidat/@id_annonce', [MessagerieController::class, 'showMessagerieU']);
Flight::route('POST /messagerieU/send', [MessagerieController::class, 'sendMessageU']);
Flight::route('GET /messagerieA/@id_candidat/@id_annonce', [MessagerieController::class, 'showMessagerieA']);
Flight::route('POST /messagerieA/send', [MessagerieController::class, 'sendMessageA']);

// Nouvelles routes pour l'actualisation temps réel
Flight::route('GET /messagerie/getCount', [MessagerieController::class, 'getNotificationCount']);
Flight::route('GET /messagerie/refresh', [MessagerieController::class, 'refreshNotifications']);
Flight::route('POST /messagerie/markAsRead', [MessagerieController::class, 'markAsReadAndGetCount']);
Flight::route('GET /messagerie/markAsRead/@id_candidat/@id_annonce', [MessagerieController::class, 'markConversationAsRead']);
?>
