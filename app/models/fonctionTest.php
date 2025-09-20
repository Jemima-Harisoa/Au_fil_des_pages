<?php
namespace app\models;

use app\models\Query;

class fonctionTest {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


    public function getQst($idProfil) {

        $qst = Query::query("SELECT * FROM Questions WHERE id_profil = ?", [$idProfil]);
        return $qst;
    }
     public function getRepCorrectes($idQst) {
        $repQstVrai = Query::query("SELECT * FROM Reponses_Question WHERE id_question = ? AND est_Correct=True", [$idQst]);
        return $repQstVrai;
    }
    public function getRepQst($idQst) {
        $repQst = Query::query("SELECT * FROM Reponses_Question WHERE id_question = ? ORDER BY id_reponse ASC", [$idQst]);
        return $repQst;
    }
    public function getIdProfil($idCandidat,$idAnnonce) {
        $repQst = Query::query("SELECT * FROM Candidats WHERE id_candidat = ? AND id_Annonce=?", [$idCandidat,$idAnnonce]);
        return $repQst; 
    }
    public function createFichierTemp($data) {
        $filePath = __DIR__ . '/reponses_candidat.json';
        $jsonData = json_encode($data,   JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($filePath, $jsonData);
        return $filePath;
    }

  public function comparaisonReponse($data, $idProfil, $idAnnonce) {
    $score = 0; 
    $questions = $this->getQst($idAnnonce);

    foreach ($questions as $q) {
        $idQst = $q['id_question'];
        $repCorrectes = $this->getRepCorrectes($idQst); 
        $repCandidat = $data[$idQst] ?? null; 
        $repCandidat = is_array($repCandidat) ? $repCandidat[0] : $repCandidat; 
        if (!empty($repCorrectes) && $repCandidat !== null) {
            if ($repCorrectes[0]['reponse'] == $repCandidat) {
                $score += $q['note'];
            }
        }
    }

    $dateTest = date('Y-m-d H:i:s'); 
    $sql = "INSERT INTO tests (id_candidat, id_annonce, score_test, date_test)
            VALUES (?, ?, ?, ?) RETURNING id_test";
    $lastId = Query::query($sql, [$idProfil, $questions[0]['id_profil'] ?? null, $score, $dateTest]);

    return $score; 
}

    public function listTest(){
         $test = Query::query("select *from tests join candidats on tests.id_candidat=candidats.id_candidat join personnes on personnes.id_personne=candidats.id_personne");
        return $test;
    }
     public function trierMetier($job){
        $j=Query::query("SELECT *
FROM tests
JOIN annonces   ON tests.id_annonce   = annonces.id_annonce
JOIN candidats  ON tests.id_candidat  = candidats.id_candidat
JOIN personnes  ON candidats.id_personne = personnes.id_personne
WHERE annonces.titre = ?",[$job]);
        return $j;
    }
    
    public function getAllJobs(){
         $test = Query::query("Select *from Profils");
        return $test;
    }
    public function trierTests($job,$colonne, $ordre) {
         $j=Query::query("SELECT *
FROM tests
JOIN annonces   ON tests.id_annonce   = annonces.id_annonce
JOIN candidats  ON tests.id_candidat  = candidats.id_candidat
JOIN personnes  ON candidats.id_personne = personnes.id_personne
WHERE annonces.titre = ? ORDER BY $colonne $ordre",[$job]);
        return $j;
    }
    
    public function listeTests(){
         $j=Query::query("select *from Questions ORDER BY id_question ASC");
        return $j;
    }
    public function deleteQst($idQst){
         $j=Query::query("delete from questions where id_question=?",[$idQst]);
        return $j;
    }
     public function deleteRepByQst($idQst,$idRep){
         $j=Query::query("delete from reponses_question where id_question=? and id_reponse=?",[$idQst,$idRep]);
        return $j;
    }
     public function deleteRep($idQst){
         $j=Query::query("delete from reponses_question where id_question=?",[$idQst]);
        return $j;
    }
    
public function deleteQstRep($tabQstRep){
    $del = true;

    foreach($tabQstRep as $item){

        if(empty($item['idRep'])){
            $rep=$this->deleteRep($item['idQst']);
            $res = $this->deleteQst($item['idQst']);
            $del = true;
        } 
        else if(empty($item['idQst'])){
            $res = $this->deleteRep($item['idQst']); 
            $del = true;
        } 
        else {
            $res = $this->deleteRepByQst($item['idQst'], $item['idRep']);
            $del = true;
        }
    }

    return $del;
}
    public function getIdJob($job){
    $j = Query::query("SELECT * FROM profils WHERE titre=?", [$job]);
    return !empty($j) ? $j[0]['id_profil'] : null; 
}

public function triQuestion($job) {
    $id = $this->getIdJob($job);
    if ($id === null) {
        return [];
    }
    $q = Query::query("SELECT * FROM questions WHERE id_profil=?", [$id]);
    return $q;
}

public function modifQst($newQst,$idQst) {
       $j=Query::query("UPDATE questions
SET question = ? WHERE id_question=?;
",[$newQst,$idQst]);
        return $j;
}
public function modifRep($newQst, $repType, $idQst, $idRep) {
    if (!is_null($repType)) {
        $repType = $repType ? 'TRUE' : 'FALSE';
    }

    $sql = "UPDATE reponses_question
            SET reponse = ?, est_correct = $repType
            WHERE id_question = ? AND id_reponse = ?;";

    $j = Query::query($sql, [$newQst, $idQst, $idRep]);

    return $j;
}


public function modifQstRep($tabQstRep){
        $modif = true;
    foreach($tabQstRep as $item){
        
        if(empty($item['idRep'])){
            $rep=$this->modifQst($item['nouvelleValeur'],$item['idQst']);
            $modif = true;
        } 
        else {
            $res = $this->modifRep($item['nouvelleValeur'],$item['repType'],$item['idQst'], $item['idRep']);
            $modif = true;
        }
}
    return $modif;

    
}

public function ajoutRep($tabQstRep){
    $modif = true;

    foreach($tabQstRep as $item){
        $idQst = $item['idQst'] ?? null;
        $nouvelleValeur = $item['nouvelleValeur'] ?? '';
        $repType = !empty($item['repType']) ? 1 : 0;

        $j = Query::query(
            "INSERT INTO reponses_question(id_question, reponse, est_correct) VALUES (?, ?, ?) RETURNING id_reponse",
            [$idQst, $nouvelleValeur, $repType]
        );

        if($j === false){
            $modif = false;
        }
    }

    return $modif;
}

public function creerTest($qst, $job, $point, $reps, $checks){
    $idJob = $this->getIdJob($job);
    $idQuestion = Query::query(
        "INSERT INTO questions (question, id_profil, note) VALUES (?, ?, ?) RETURNING id_question",
        [$qst, $idJob, $point]
    );

    if (!$idQuestion) {
        return false; 
    }

    foreach ($reps as $index => $answer) {
        $estCorrect = isset($checks[$index]) && $checks[$index] ? 1 : 0; 
        Query::query(
            "INSERT INTO reponses_question (id_question, reponse, est_correct) VALUES (?, ?, ?) RETURNING id_reponse",
            [$idQuestion, $answer, $estCorrect]
        );
    }

    return true; 
}


}