<?php

namespace app\controllers;

use app\models\fonctionTest;
use Flight;

class TestController {

	public function __construct() {

	}

	public function QCM() {
        $fonction = new fonctionTest(Flight::db());
        $idCandidat = 6;
        $idAnnonce=1;
        $profilData = $fonction->getIdProfil($idCandidat,$idAnnonce);
        if (empty($profilData)) {
            Flight::halt(404, "Profil non trouvé pour ce candidat");
            return;
        }
        $idProfil = $profilData[0]['id_profil'];
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
        Flight::render('formulaireTest', ['qcm' => $qcm]);
    }
    public function traitementQCM() {
         $fonction = new fonctionTest(Flight::db());
        $idCandidat = 6;
        $idAnnonce=1;
        $profilData = $fonction->getIdProfil($idCandidat,$idAnnonce);
        $idProfil = $profilData[0]['id_profil'];
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
        $score = $fonction->comparaisonReponse($reponses,6, 1);
        Flight::render('formulaireTest', [
            'score'     => $score,
            'reponses'  => $reponses,
            'qcm'=>     $qcm
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
}

