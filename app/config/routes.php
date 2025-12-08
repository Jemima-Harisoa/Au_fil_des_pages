<?php
use app\controllers\DocumentsController;
use app\controllers\WelcomeController;
use app\controllers\ConnexionController;
use app\controllers\AnnoncesController;
use app\controllers\EmployeController;
use app\controllers\conge\JustificatifController;


use app\controllers\TestController;
use app\controllers\migration\MigrationController;
use app\controllers\cvController;

use app\controllers\EvaluationController;
use app\controllers\MessagerieController;
use app\controllers\PlanningEntretienController;
use app\controllers\ApiPlanningEntretienController;
use app\controllers\MobiliteHistoriqueController;
use app\controllers\PointageController;
use app\models\EmployeModel;
use app\controllers\conge\CongeController;
use app\controllers\conge\AbscenceController;
use app\controllers\conge\NotificationController;
use app\controllers\conge\CalendrierController;

use app\controllers\CompetenceController;
use app\controllers\EmployeeCompetenceController;

use app\models\EvaluationModel;
use app\controllers\FichePaieController;
use app\models\AdminModel;

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
$MobiliteHistoriqueController = new MobiliteHistoriqueController();
$DocumentsController = new DocumentsController();

$router -> get('/documents', [$DocumentsController, 'redirectDocuments']);
$router -> get('/MobiliteHistorique', [$MobiliteHistoriqueController, 'redirectEmploye']);
$evaluationController = new EvaluationController();

$router-> get('/evaluations', [$evaluationController, 'liste']);
$router-> get('/evaluation/@id', [$evaluationController, 'detail']);
$router->get('/evaluation/@id/saisie', [$evaluationController, 'saisie']);
$router->get('/evaluation/@id/score', [$evaluationController, 'score']);
$router->post('/evaluation/@id/save', [$evaluationController, 'saveNotes']);
$router->get('/evaluation/@id/score', [$evaluationController, 'score']);
$router-> get('/employeList', [ $employeController, 'redirectEmploye' ]);
$router-> get('/employeDetails/@id', [ $employeController, 'redirectEmployeDetails' ]);
$router->get('/employee/@id/historique', [$evaluationController, 'historique']);
// Dashboard manager
$router->get('/dashboard/@idManager/manager', function($idManager) {
    try {
        // Vérifier si l'admin est connecté
        if (!isset($_SESSION['admin']['id_admin'])) {
            Flight::redirect('/login');
            return;
        }

        $adminModel = new AdminModel();
        $evaModel = new EvaluationModel(Flight::db());

        // Récupérer les détails de l'admin
        $adminDetails = $adminModel->getDetailsPersoAdmin($_SESSION['admin']['id_admin']);
        
        // DEBUG - Afficher l'id_manager
        error_log("DEBUG - ID Manager from URL: " . $idManager);
        error_log("DEBUG - Admin Details: " . print_r($adminDetails, true));
        
        // Vérifier si l'admin a un manager_id
        if (!$adminDetails || !isset($adminDetails['id_manager'])) {
            error_log("ERREUR - Manager ID non trouvé dans adminDetails");
            throw new Exception("Manager ID non trouvé pour cet administrateur");
        }

        error_log("DEBUG - ID Manager from DB: " . $adminDetails['id_manager']);

        // Vérifier que l'ID manager dans l'URL correspond à celui de la session
        if ($idManager != $adminDetails['id_manager']) {
            error_log("REDIRECTION - URL: $idManager vs Session: " . $adminDetails['id_manager']);
            Flight::redirect('/dashboard/' . $adminDetails['id_manager'] . '/manager');
            return;
        }

        // Récupérer les données pour le dashboard
        $summary = $evaModel->getTeamEvaluationsSummary($idManager);
        $trend = $evaModel->getScoreTrends($idManager);
        $urgent = $evaModel->getTeamUrgentEvaluations($idManager);

        Flight::render('dashboard_manager.php', [
            'summary' => $summary,
            'trend'   => $trend,
            'urgent'  => $urgent,
            'managerId' => $idManager,
            'adminDetails' => $adminDetails // ← Ajouté pour le debug
        ]);

    } catch (Exception $e) {
        error_log("Erreur dashboard manager: " . $e->getMessage());
        Flight::json([
            'success' => false,
            'message' => 'Erreur lors du chargement du dashboard'
        ], 500);
    }
});
$router->get('/performance_dashboard', function() {
    $employeId = $_GET['employe'] ?? null;
    $empModel=new EmployeModel();
    $nom = $empModel->getEmployesWithDetails($employeId);
    
    $annee = $_GET['annee'] ?? date('Y');
    $periodeA = $_GET['periodeA'] ?? null;
    $periodeB = $_GET['periodeB'] ?? null;

    $perfModel = new EvaluationModel(Flight::db());

    // Performance globale
    $performanceData = $perfModel->generatePerformance($annee, $employeId);

    // Comparaison périodes si spécifiées
    $comparison = [];
    if($employeId && $periodeA && $periodeB){
        $comparison = $perfModel->comparePeriods($employeId, $periodeA, $periodeB);
    }

    Flight::render('performance_generation.php', [
        'performanceData' => $performanceData,
        'comparison' => $comparison,
        'employeNom' => $nom['nom_personne'] ?? 'Tous',
        'annee' => $annee
    ]);
});
$router->get('/perform', function(){
    Flight::render('intro_performance_generation.php', []);
});

$router->get('/traiterEval', function(){
   $db = Flight::db();

    $employes = $db->query("SELECT e.id_employe, p.nom, p.prenom FROM employes e 
                             JOIN personnes p ON e.id_personne = p.id_personne
                             ORDER BY p.nom")->fetchAll(PDO::FETCH_ASSOC);

    $periodes = $db->query("SELECT * FROM employe_evaluation_periodes ORDER BY id_periode")->fetchAll(PDO::FETCH_ASSOC);

    Flight::render('formEval.php', [
        'employes' => $employes,
        'periodes' => $periodes
    ]);
});


$router->post('/evaluation/planifier', function() {
    $db = Flight::db();

    $employe_id = Flight::request()->data->employe_id;
    $periode_id = Flight::request()->data->periode_id;
    $date_eval = Flight::request()->data->date_evaluation;
    $manager_id = Flight::request()->data->manager_id ?? 1;

    $stmt = $db->prepare("
        INSERT INTO employe_evaluations 
        (employe_id, periode_id, date_generation, date_evaluation, statut, score_total, manager_id, created_at, updated_at)
        VALUES (?, ?, CURRENT_DATE, ?, 'PREVUE', 0, ?, NOW(), NOW())
    ");
    $stmt->execute([$employe_id, $periode_id, $date_eval, $manager_id]);

    Flight::redirect('/evaluations'); // Retour au formulaire ou page récap
});
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
$router->get('/accueilE', [ $WelcomeController, 'AppelAccueilE' ]);

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
$router->get('/presence/export/pdf/@idEmploye/@debutPeriode/@finPeriode', [$pointageController, 'exporterPDF']);
$router->get('/presence/export/csv/@idEmploye', function($idEmploye) use ($pointageController) {
    $debut = $_GET['debut'] ?? null;
    $fin   = $_GET['fin'] ?? null;
    $pointageController->exporterCSV($idEmploye, $debut, $fin);
});




// $router->get('/CV', [ $cvController, 'redirectCV']);

// $router->get('/CV/fillCV/@idUser/@idAnnonce', [ $cvController, 'fillCV']);

// $router->post('/CV/fillCV/postulationCV', [ $cvController, 'getDataCV']);
$ConnexionController = new ConnexionController();
$router->get('/', [ $ConnexionController, 'AppelLoginU' ]);
$router->post('/inscriptionU', [ $ConnexionController, 'InscrireU' ]);

$router->post('/loginU', [ $ConnexionController, 'VerificationConnectionU' ]); // 11.16

$router->get('/deconnexionU', [ $ConnexionController, 'deconnexionU' ]);

$router->get('/admin', [ $ConnexionController, 'AppalLoginA' ]);
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

// --- Routes supplémentaires pour messagerie employé (fusion des deux fichiers) ---
// AJAX / API pour conversations entre employés
Flight::route('GET /messagerie/conversationsE/@id_employe', [\app\controllers\MessagerieController::class, 'getConversationsE']);
Flight::route('GET /messagerie/searchEmployes/@id_employe', [\app\controllers\MessagerieController::class, 'searchEmployes']);
Flight::route('GET /messagerie/convEmploye/@id_employe/@partenaire_id', [\app\controllers\MessagerieController::class, 'getConversationEmploye']);

// Interface et envoi pour messagerie entre employés
Flight::route('GET /messagerieE/@id_employe/@partenaire_id', [\app\controllers\MessagerieController::class, 'showMessagerieE']);
Flight::route('POST /messagerieE/send', [\app\controllers\MessagerieController::class, 'sendMessageE']);

// routes existantes pour candidats/admin (conservées)
Flight::route('GET /messagerieU/@id_candidat/@id_annonce', [MessagerieController::class, 'showMessagerieU']);
Flight::route('POST /messagerieU/send', [MessagerieController::class, 'sendMessageU']);
Flight::route('GET /messagerieA/@id_candidat/@id_annonce', [MessagerieController::class, 'showMessagerieA']);
Flight::route('POST /messagerieA/send', [MessagerieController::class, 'sendMessageA']);

// Nouvelles routes pour l'actualisation temps réel (conservées)
Flight::route('GET /messagerie/getCount', [MessagerieController::class, 'getNotificationCount']);
Flight::route('GET /messagerie/refresh', [MessagerieController::class, 'refreshNotifications']);
Flight::route('POST /messagerie/markAsRead', [MessagerieController::class, 'markAsReadAndGetCount']);
Flight::route('GET /messagerie/markAsRead/@id_candidat/@id_annonce', [MessagerieController::class, 'markConversationAsRead']);
// Route SSE (déjà présente, double déclaration tolérée mais gardez une seule si possible)
Flight::route('GET /messagerie/sse', [MessagerieController::class, 'sseNotifications']);
Flight::route('GET /messagerie/refreshSession', [MessagerieController::class, 'refreshConversation']);


Flight::route('GET /messagerieE/@id_employe/@partenaire_id', [MessagerieController::class, 'showMessagerieE']);
Flight::route('POST /messagerieE/send', [MessagerieController::class, 'sendMessageE']);

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
$Calendrier_Controller = new CalendrierController();

// Routes de gestion des congés et absences
$router->group('/conge', function($router) use ($Conge_Controller, $Calendrier_Controller) {
    
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

    // Routes pour le calendrier des congés
    $router->get('/calendrier', [$Calendrier_Controller, 'index']);
    $router->get('/calendrier/data', [$Calendrier_Controller, 'getData']);
});

// Routes de gestion des justifications d'absences
$Abscence_Controller = new AbscenceController();
$Justificatif_Controller = new JustificatifController();
$Notification_Controller = new NotificationController();

$router->group('/absence', function($router) use ($Abscence_Controller,$Justificatif_Controller, $Notification_Controller ) {
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

    $router->get('/notifier/@idAbsence', [$Notification_Controller, 'notifierAbsences'] );
    $router->get('/notifier/tous', [$Notification_Controller, 'notifierAbsencesLot'] );
});



$Competence_Controller = new CompetenceController();

// Routes de gestion des compétences
$router->group('/competences', function($router) use ($Competence_Controller) {
    
    // Route principale - liste des compétences avec pagination, filtres et tri
    $router->get('/liste', [$Competence_Controller, 'getListeCompetences']);
    
    // Routes pour les détails d'une compétence spécifique
    $router->get('/details/@idCompetence', [$Competence_Controller, 'getDetailsCompetence']);
    
    // Routes pour l'export des compétences
    $router->get('/export', [$Competence_Controller, 'exportCompetences']);
    
    // Routes pour la recherche et le filtrage
    $router->get('/recherche', [$Competence_Controller, 'searchCompetences']);
    $router->get('/filtres', [$Competence_Controller, 'filterCompetences']);
    
    // Route pour les statistiques globales
    $router->get('/statistiques', [$Competence_Controller, 'getStatsGlobales']);
    
    // Route par défaut - redirige vers la liste
    $router->get('/', [$Competence_Controller, 'getListeCompetences']);
});

// Alternative: routes API pour les appels AJAX
$router->group('/api/competences', function($router) use ($Competence_Controller) {
    
    // API pour la liste des compétences (retour JSON)
    $router->get('/liste', [$Competence_Controller, 'getListeCompetences']);
    
    // API pour les détails d'une compétence
    $router->get('/details/@idCompetence', [$Competence_Controller, 'getDetailsCompetence']);
    
    // API pour l'export
    $router->get('/export', [$Competence_Controller, 'exportCompetences']);
    
    // API pour la recherche (autocomplétion)
    $router->get('/recherche', [$Competence_Controller, 'searchCompetences']);
    
    // API pour le filtrage avancé
    $router->get('/filtres', [$Competence_Controller, 'filterCompetences']);
    
    // API pour les statistiques
    $router->get('/statistiques', [$Competence_Controller, 'getStatsGlobales']);
});


$EmployeeCompetence_Controller = new EmployeeCompetenceController();

// Routes pour les compétences des employés
$router->group('/employees', function($router) use ($EmployeeCompetence_Controller) {
    
    // Vues HTML
    $router->get('/@id/competences/form', [$EmployeeCompetence_Controller, 'showCompetenceForm']);
    $router->get('/@id/competences/list', [$EmployeeCompetence_Controller, 'showCompetenceList']);

    // Auto-évaluation des compétences
    $router->post('/@id/competences', [$EmployeeCompetence_Controller, 'createFromEmployee']);
    
    // Liste des compétences d'un employé
    $router->get('/@id/competences', [$EmployeeCompetence_Controller, 'listFromEmployee']);
    
    // Détail d'une compétence spécifique
    $router->get('/@id/competences/@id_competence', [$EmployeeCompetence_Controller, 'getCompetenceDetail']);
    
    // Suppression d'une compétence
    $router->delete('/@id/competences/@id_competence', [$EmployeeCompetence_Controller, 'deleteCompetence']);
});
// Route Chatbot -> vue messagerieBot (crée la vue ci‑dessous)
Flight::route('GET /messagerieBot', function(){
    // Vérifier la session admin/employé (ajout de plus de vérifications)
    $hasAdminSession = isset($_SESSION['infoAdmin']) && !empty($_SESSION['infoAdmin']);
    $hasEmployeSession = isset($_SESSION['employe']) && !empty($_SESSION['employe']);
    
    if (!$hasAdminSession && !$hasEmployeSession) {
        Flight::redirect('/admin'); // ou '/employe' selon votre logique
        return;
    }
    
    Flight::render('messagerieBot', ['messages' => []]);
});

// POST route for chatbot: delegate to controller
Flight::route('POST /messagerieBot/send', [\app\controllers\ChatBotController::class, 'send']);
?>
