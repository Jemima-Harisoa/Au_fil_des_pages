<?php
use app\controllers\WelcomeController;
use app\controllers\ConnexionController;
use app\controllers\AnnoncesController;
use app\controllers\EmployeController;
use app\controllers\conge\JustificatifController;


use app\controllers\TestController;

use app\controllers\migration\MigrationController;

use app\controllers\cvController;

use app\controllers\MessagerieController;
use app\controllers\PlanningEntretienController;
use app\controllers\ApiPlanningEntretienController;
use app\controllers\PointageController;

use app\controllers\conge\CongeController;
use app\controllers\conge\AbscenceController;

use app\controllers\FichePaieController;
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
$employeController = new EmployeController();

$router-> get('/employeList', [ $employeController, 'redirectEmploye' ]);
$router-> get('/employeDetails/@id', [ $employeController, 'redirectEmployeDetails' ]);

$pointageController = new PointageController();
$router->get('/pointage', [ $pointageController, 'getAllEmployes' ]);
// fichier routes.php ou bootstrap

// Relève individuelle AVEC période
Flight::route('GET /presence/individuelle/@idEmploye', [$pointageController, 'releverPresenceIndividuelle']);

// Relève groupe AVEC période
Flight::route('GET /presence/groupe/@dept', [$pointageController, 'releverPresenceGroupe']);

$router->get('/employe', [ $ConnexionController, 'AppelLoginE' ]);

$ConnexionController = new ConnexionController();
$router->get('/', [ $ConnexionController, 'AppelLoginU' ]);
$router->post('/inscriptionU', [ $ConnexionController, 'InscrireU' ]);
$router->post('/loginU', [ $ConnexionController, 'VerificationConnectionU' ]);
$router->post('/loginE', [ $ConnexionController, 'VerificationConnectionE' ]);
$router->get('/deconnexion', [ $ConnexionController, 'deconnexion' ]);

$router->get('/admin', [ $ConnexionController, 'AppelLoginA' ]);
$router->post('/inscriptionA', [ $ConnexionController, 'InscrireA' ]);
$router->post('/loginA', [ $ConnexionController, 'VerificationConnectionA' ]);


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

$cvController = new cvController();

// $router->get('/', [ $cvController, 'home' ]);

$router->get('/@idUser/Annonce', [ $cvController, 'redirectCV' ]);

$router->get('/@idUser/Annonce/@idAnnonce/@idProfil/fillCV', [ $cvController, 'fillCV']);

$router->post('/@idUser/Annonce/@idAnnonce/@idProfil/fillCV/postulationCV',[ $cvController, 'getDataCV']);

$router->get('/retourConfirmation',[ $cvController, 'retourAccueilU']);
$router->get('/retourFill',[ $cvController, 'retourAccueilU']);

$router->get('/listeCV',[ $cvController, 'listeCV']);

$router->get('/exportCV',[ $cvController, 'exportExcel']);
$router->post('/exportRelevePost',[ $pointageController, 'exportExcelPost']);
$router->get('/relevePresenceE/@idEmploye', [$pointageController, 'releverPresenceE']);
$router->get('/presence/export/pdf/@idEmploye', [$pointageController, 'exporterPDF']);
$router->get('/presence/export/csv/@idEmploye', [$pointageController, 'exporterCSV']);



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
$router->post('/deconnexionE', [ $ConnexionController, 'deconnexionE' ]);


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
// Route pour SSE
Flight::route('GET /messagerie/sse', [MessagerieController::class, 'sseNotifications']);
Flight::route('GET /messagerie/refreshSession', [MessagerieController::class, 'refreshConversation']);

$planning_entretien_controller = new PlanningEntretienController();
$router->get('/planning-entretien',[$planning_entretien_controller,'showPageEntretien']);

$api_planning_entretien_controller = new ApiPlanningEntretienController();
$router->get('/api/planifier-entretien',[$api_planning_entretien_controller,'planifierEntretien']);

$Migration_Controller = new MigrationController(); 
//$router->get('/migration/Redaction',  [ $Contrat_Controller, 'RedactionContrat' ]);
$router->group( "/migration" , function($router) use ($Migration_Controller){
		// route de configuration 
		$router->get("/", function(){
			Flight::redirect("/migration/candidats");
		});
		// route vers la liste des candidat apres le scoring  
		$router->get("/candidats", [$Migration_Controller , 'getCandidatRetenu']);
		// route vers le formulaire de soumission de contrat de travail 
		$router->get("/contrat/create", [$Migration_Controller , 'createContrat']);
		// enregister le brouillon du contrat avant validation
		$router->post("/contrat/register", [$Migration_Controller, 'registerContrat']);
		// route vers la liste des contrat 
		$router->get("/contrats", [$Migration_Controller, 'getContrat']);

		// route vers la page d'editon des contrat pour retouche ou bien validation
		$router->get("/contrat/edit", [$Migration_Controller, 'editContrat']);   
	}
);

// Route pour SSE
Flight::route('GET /messagerie/sse', [MessagerieController::class, 'sseNotifications']);


$fiche_paie_controller = new FichePaieController();

$router->group( "/fiche_paie" , function($router) use ($fiche_paie_controller){
		// route vers la page de formulaire
		$router->get("/affichage", [$fiche_paie_controller,'renderFichePaie']);
		// route vers la liste des fiche de paie d'un candidat
		$router->get("/liste", [$fiche_paie_controller , 'renderListeFichePaie']); 
	}
);
$Conge_Controller = new CongeController();

// Routes de gestion des congés et absences
$router->group('/conge', function($router) use ($Conge_Controller) {
    
    // Route principale - fiche employé complète
    $router->get('/fiche/@idEmploye', [$Conge_Controller, 'getFicheEmploye']);
    
    // Route API pour voir les détails des absences (tous types)
    $router->get('/fiche/@idEmploye(/@estAutorise)', [$Conge_Controller, 'getDetailAbsences']);
    $router->get('/fiche/@idEmploye/type/@idType', [$Conge_Controller, 'getDetailConges']);
    $router->get('/demande', [$Conge_Controller, 'getDemandeConge']);
    $router->post('/demande', [$Conge_Controller, 'submitDemande']);

    // Routes pour la validation des congés
    $router->get('/validation', [$Conge_Controller, 'getInterfaceValidation']);
    $router->post('/validation/@id_demande', [$Conge_Controller, 'postValidation']);
    $router->get('/estimation-deduction', [$Conge_Controller, 'getEstimationDeduction']);
    
    // Route par défaut
    $router->get('/', [$Conge_Controller, 'getListeEmployes']);
});

// Routes de gestion des justifications d'absences
$Abscence_Controller = new AbscenceController();
$Justificatif_Controller = new JustificatifController();

$router->group('/absence', function($router) use ($Abscence_Controller,$Justificatif_Controller ) {
    // Afficher les absences à justifier
    $router->get('/justifier', [$Abscence_Controller, 'getListeAbsencesAJustifier']);
    
    // Afficher le formulaire de justification pour une absence spécifique
    $router->get('/justifier/@idAbsence', [$Abscence_Controller, 'getJustifierAbsence']);
    
    // Traiter la soumission du formulaire de justification
    $router->post('/justifier/submit', [$Abscence_Controller, 'submitJustification']);

    // Liste des abscences
    $router->get('/liste(/@estAutorise)', [$Abscence_Controller, 'getListeAbsence']);

    $router->get('/', function(){
        Flight::redirect('/absence/justifications');
    });

    // Routes pour les justificatifs
    $router->get('/justificatif/view/@id', [$Justificatif_Controller, 'viewJustificatif']);
    $router->get('/justificatif/download/@id', [$Justificatif_Controller, 'downloadJustificatif']);
});