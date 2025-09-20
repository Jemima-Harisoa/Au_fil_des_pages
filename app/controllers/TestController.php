<?php

namespace app\controllers;

use app\models\fonctionTest;
use Flight;

class TestController {

	public function __construct() {

	}

	public function traitementQCM() {
    $fonction = new fonctionTest(Flight::db());
    $idCandidat = 6;
    $idAnnonce = 1;
    $profilData = $fonction->getIdProfil($idCandidat, $idAnnonce);
    $idProfil = $profilData[0]['id_profil'];

    // Récupération des questions et réponses
    $questions = $fonction->getQst($idProfil);
    $qcm = [];
    foreach ($questions as $q) {
        $idQst = $q['id_question']; 
        $reponses = $fonction->getRepQst($idQst); 

        $qcm[] = [
            'id_question' => $idQst,
            'question' => $q['question'],
            'note' => $q['note'],
            'reponses' => $reponses 
        ];
    }
    $data = Flight::request()->data->getData();
    $reponses = $data['reponses'] ?? [];

    $score = $fonction->comparaisonReponse($reponses, $idProfil, $idAnnonce);

    Flight::render('formulaireTest', [
        'score' => $score,
        'reponses' => $reponses,
        'qcm' => $qcm
    ]);
}

    public function getList(){
        $fonction = new fonctionTest(Flight::db());
        $list=$fonction->listTest();
        $jobs=$fonction->getAllJobs();
        Flight::render('listTestBack', ['list' => $list,'jobs'=> $jobs]);
    }
    public function getListByJob() {
    $job = Flight::request()->query['metier'];  

    $fonction = new fonctionTest(Flight::db());
    $list = $fonction->trierMetier($job);
    $jobs = $fonction->getAllJobs();

    Flight::render('listTestBack', [
        'list' => $list,
        'jobs' => $jobs
    ]);
}

    public function getListSorted() {
    $critere = Flight::request()->query['critere'] ?? 'score';
    $type    = Flight::request()->query['type'] ?? 'dec';
    $job=Flight::request()->query['metier'];
    $colonnesValides = ['score_test', 'nom', 'prenom'];
    $typesValides    = ['dec' => 'DESC', 'croi' => 'ASC'];

    $colonne = $critere === 'score' ? 'score_test' : $critere;

    if (!in_array($colonne, $colonnesValides)) {
        $colonne = 'score_test';
    }

    $ordre = $typesValides[$type] ?? 'DESC';
    $fonction = new fonctionTest(Flight::db());
    $list = $fonction->trierTests($job,$colonne, $ordre);
    $jobs = $fonction->getAllJobs();

    Flight::render('listTestBack', [
        'list' => $list,
        'jobs' => $jobs
    ]);
}

    public function getAllQstWtRep() {
        $fonction = new fonctionTest(Flight::db());
        $questions = $fonction->listeTests();
        $qcm = [];
        foreach ($questions as $q) {
            $idQst = $q['id_question']; 
            $reponses = $fonction->getRepQst($idQst); 
                $jobs = $fonction->getAllJobs();
            $qcm[] = [
                'id_question' => $idQst,
                'question' => $q['question'],
                'note' => $q['note'],
                'reponses' => $reponses 
            ];
        }
        Flight::render('listQstBack', ['qcm' => $qcm,'jobs' => $jobs]);
    }
    
  public function getAllTriQstWtRep() {
    $fonction = new fonctionTest(Flight::db());
    $job = Flight::request()->query['metier'];
    $questions = $fonction->triQuestion($job); // on passe juste le job !
    $jobs = $fonction->getAllJobs();

    $qcm = [];
    foreach ($questions as $q) {
        $idQst = $q['id_question']; 
        $reponses = $fonction->getRepQst($idQst); 

        $qcm[] = [
            'id_question' => $idQst,
            'question'   => $q['question'],
            'note'       => $q['note'],
            'reponses'   => $reponses 
        ];
    }
    Flight::render('listQstBack', ['qcm' => $qcm, 'jobs' => $jobs]);
}

    public function traitementDelete(){
    $data = json_decode(file_get_contents('php://input'), true);
    $fonction = new FonctionTest(Flight::db()); 
    $tabQstRep = [];
    if(!empty($data['tabQstRep'])){
        foreach($data['tabQstRep'] as $item){
            $tabQstRep[] = [
                'idQst' => $item['idQst'] ?? null,      
                'idRep' => $item['idReponse'] ?? null
            ];
        }
    }

    $success = $fonction->deleteQstRep($tabQstRep);

    Flight::json(['success' => $success]);
}
   public function traitementModifQstRep(){
    $playload = json_decode(file_get_contents('php://input'), true);

    $tabQstRep = [];

    if(!empty($playload['tabQstRep'])){
        foreach($playload['tabQstRep'] as $item){
            $repType = array_key_exists('estCorrect', $item) ? (bool)$item['estCorrect'] : null;

            $tabQstRep[] = [
                'idQst' => $item['idQst'] ?? null,
                'idRep' => $item['idRep'] ?? null,
                'nouvelleValeur' => $item['value'] ?? null,
                'repType'=>$repType
            ];
        }
    }

    $fonction = new FonctionTest(Flight::db()); 
    $success = $fonction->modifQstRep($tabQstRep);

      Flight::json([
        'success' => $success,
        'playload_recu' => $playload,
        'tabQstRep' => $tabQstRep
    ]);
}

public function ajoutQst(){
    $payload = json_decode(file_get_contents('php://input'), true);
    error_log("Payload reçu : " . print_r($payload, true));

    $tabQstRep = [];

    if(!empty($payload['tabQstRep'])){
        foreach($payload['tabQstRep'] as $item){
            $idQst = $item['idQst'] ?? null;
            $idRep = $item['idRep'] ?? null;
            $nouvelleValeur = $item['value'] ?? '';
            $repType = $item['estCorrect'] ; // true => 1, false => 0


            $tabQstRep[] = [
                'idQst' => $idQst,
                'idRep' => $idRep,
                'nouvelleValeur' => $nouvelleValeur,
                'repType' => $repType
            ];

           
            error_log("Item traité : " . print_r($tabQstRep[count($tabQstRep)-1], true));
        }
    }

    $fonction = new FonctionTest(Flight::db()); 
    $success = $fonction->ajoutRep($tabQstRep);

    
    error_log("Résultat ajoutRep : " . ($success ? 'OK' : 'Erreur'));

    Flight::json([
        'success' => $success,
        'payload_recu' => $payload,
        'tabQstRep' => $tabQstRep
    ]);
}

public function cheminTestWtMetier(){
    $fonction = new fonctionTest(Flight::db());
    $jobs = $fonction->getAllJobs();
     Flight::render('createTest',['jobs' => $jobs]);

}
public function traitementCreation(){
    $payload = json_decode(file_get_contents('php://input'), true);
    error_log("Payload reçu : " . print_r($payload, true));

    $qst = $payload['question'] ?? null;
    $job = $payload['job'] ?? null;
    $point = $payload['point'] ?? 0;
    $reps = $payload['reps'] ?? [];
    $checks = $payload['checks'] ?? [];

    $fonction = new FonctionTest(Flight::db());
    $success = $fonction->creerTest($qst, $job, $point, $reps, $checks);

    error_log("Résultat ajoutRep : " . ($success ? 'OK' : 'Erreur'));

    Flight::json([
        'success' => $success,
        'payload_recu' => $payload
    ]);
}



}
