<?php
use app\controllers\WelcomeController;
use app\controllers\ConnexionController;
use app\controllers\AnnoncesController;
use app\controllers\TestController;
use app\controllers\migration\MigrationController;
use app\controllers\cvController;
use app\controllers\PlanningEntretienController;
use app\controllers\ApiPlanningEntretienController;
use app\controllers\MessagerieController;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// Initialize controllers
$ConnexionController = new ConnexionController();
$WelcomeController = new WelcomeController();
$AnnoncesController = new AnnoncesController();
$cvController = new cvController();
$TestController = new TestController();
$MessagerieController = new MessagerieController();
$planning_entretien_controller = new PlanningEntretienController();
$api_planning_entretien_controller = new ApiPlanningEntretienController();
$Migration_Controller = new MigrationController();

// ===== CONNEXION ROUTES =====
$router->get('/', [ $ConnexionController, 'AppelLoginU' ]);
$router->post('/inscriptionU', [ $ConnexionController, 'InscrireU' ]);

$router->post('/loginU', [ $ConnexionController, 'VerificationConnectionU' ]); // 11.16

$router->get('/deconnexionU', [ $ConnexionController, 'deconnexionU' ]);

$router->get('/admin', [ $ConnexionController, 'AppelLoginA' ]);
$router->post('/inscriptionA', [ $ConnexionController, 'InscrireA' ]);
$router->post('/loginA', [ $ConnexionController, 'VerificationConnectionA' ]);

// ===== WELCOME/ACCUEIL ROUTES =====
$router->get('/accueilG', [ $WelcomeController, 'AppelAccueilG' ]);
$router->get('/accueilA', [ $WelcomeController, 'AppelAccueilA' ]);
$router->get('/accueilU', [ $WelcomeController, 'AppelAccueilU' ]);

// ===== ANNONCES ROUTES =====
$router->group('/annonces', function($router) use ($AnnoncesController) {
    $router->get('/form', [ $AnnoncesController, 'form' ]);
    $router->post('/create', [$AnnoncesController, 'create']);
    $router->get('/read', [ $AnnoncesController, 'read' ]);
    $router->get('/readU', [ $AnnoncesController, 'readU' ]);
    $router->get('/read/{id}', [ $AnnoncesController, 'read' ]);
    $router->post('/update', [ $AnnoncesController, 'update' ]);
});

// ===== CV ROUTES =====
$router->get('/@idUser/Annonce', [ $cvController, 'redirectCV' ]);
$router->get('/@idUser/Annonce/@idAnnonce/@idProfil/fillCV', [ $cvController, 'fillCV']);
$router->post('/@idUser/Annonce/@idAnnonce/@idProfil/fillCV/postulationCV',[ $cvController, 'getDataCV']);
$router->get('/retourConfirmation',[ $cvController, 'retourAccueilU']);
$router->get('/retourFill',[ $cvController, 'retourAccueilU']);
$router->get('/listeCV',[ $cvController, 'listeCV']);

// ===== TEST/QCM ROUTES =====
$router->get('/testAccueil', [ $TestController, 'QCM' ]); 
$router->post('/traitement-qcm', [ $TestController, 'traitementQCM' ]); 
$router->get('/allTests', [ $TestController, 'getList' ]); 
$router->get('/triMetier', [ $TestController, 'getListByJob' ]); 
$router->get('/triageTests', [ $TestController, 'getListSorted' ]); 
$router->get('/listTest', [ $TestController, 'getAllQstWtRep' ]); 
$router->post('/deleteAjax',[$TestController,'traitementDelete']);
$router->get('/triMetierQst',[$TestController,'getAllTriQstWtRep']);
$router->post('/updateQstRep',[$TestController,'traitementModifQstRep']);
$router->post('/addRep',[$TestController,'ajoutQst']);
$router->post('/createTest', [$TestController,'cheminTestWtMetier']);
$router->get('/createTest',[$TestController,'cheminTestWtMetier']);
$router->post('/creationQst',[$TestController,'traitementCreation']);

// ===== MESSAGERIE ROUTES =====
$router->get('/messagerieU/@id_candidat/@id_annonce', [ $MessagerieController, 'showMessagerieU' ]);
$router->post('/messagerieU/send', [ $MessagerieController, 'sendMessageU' ]);
$router->post('/messagerieA/send', [ $MessagerieController, 'sendMessageA' ]);
$router->get('/messagerieA/@id_candidat/@id_annonce', [ $MessagerieController, 'showMessagerieA' ]);
$router->get('/api/refresh-notifications', [ $MessagerieController, 'refreshNotifications' ]);

// Additional Messagerie routes using Flight directly
Flight::route('GET /messagerie/getCount', [MessagerieController::class, 'getNotificationCount']);
Flight::route('GET /messagerie/refresh', [MessagerieController::class, 'refreshNotifications']);
Flight::route('POST /messagerie/markAsRead', [MessagerieController::class, 'markAsReadAndGetCount']);
Flight::route('GET /messagerie/markAsRead/@id_candidat/@id_annonce', [MessagerieController::class, 'markConversationAsRead']);
Flight::route('GET /messagerie/sse', [MessagerieController::class, 'sseNotifications']);
Flight::route('GET /messagerie/refreshSession', [MessagerieController::class, 'refreshConversation']);

// ===== PLANNING/ENTRETIEN ROUTES =====
$router->get('/planning-entretien',[$planning_entretien_controller,'showPageEntretien']);
$router->post('/planning_entretien/filtre', [$api_planning_entretien_controller, 'filtrerEntretien']);
$router->get('/api/planifier-entretien',[$api_planning_entretien_controller,'planifierEntretien']);

// ===== MIGRATION ROUTES =====
$router->group("/migration", function($router) use ($Migration_Controller){
    $router->get("/", function(){
        Flight::redirect("/migration/candidats");
    });
    $router->get("/candidats", [$Migration_Controller, 'getCandidatRetenu']);
    $router->get("/contrat/create", [$Migration_Controller, 'createContrat']);
    $router->post("/contrat/register", [$Migration_Controller, 'registerContrat']);
    $router->get("/contrats", [$Migration_Controller, 'getContrat']);
    $router->get("/contrat/edit", [$Migration_Controller, 'editContrat']);   
    $router->post("/contrat/envoyer-validation", [$Migration_Controller, 'envoyerValidation']);
});
// ===== AGENDA ROUTE =====
$router->get("/agenda",[$WelcomeController,'home']);

?>