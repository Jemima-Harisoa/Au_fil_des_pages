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
        $repQst = Query::query("SELECT * FROM Reponses_Question WHERE id_question = ?", [$idQst]);
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

   public function comparaisonReponse($data,$idProfil, $idAnnonce) {
    $score = 0; 
    $questions = $this->getQst($idAnnonce);

    foreach ($questions as $q) {
        $idQst = $q['id_question'];

        $repCorrectes = $this->getRepCorrectes($idQst); 
        $repCandidat = $data[$idQst] ?? [];
        sort($repCorrectes);
        sort($repCandidat);
        error_log("Question $idQst"); 
        error_log("Réponses correctes: " . json_encode($repCorrectes));
        error_log("Réponses candidat: " . json_encode($repCandidat));
        error_log("score: " . json_encode($q['note']));
        error_log("Réponse vrai: " . json_encode($repCorrectes[0]['reponse'] ));
        if (!empty($repCorrectes) && !empty($repCandidat)) {
    if ($repCorrectes[0]['reponse'] == $repCandidat[0]) {
        $score += $q['note'];
    }
}

    }
    $dateTest = date('Y-m-d H:i:s'); 
   $sql = "INSERT INTO tests (id_candidat, id_annonce, score_test, date_test)
        VALUES (?, ?, ?, ?) RETURNING id_test";

$lastId = Query::query($sql, [$idProfil, $questions[0]['id_profil'] ?? null, $score, $dateTest]);

if ($lastId === false) {
    error_log("Erreur insertion test");
} else {
    echo "ID inséré : $lastId";
}

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

    /*public function header($table) {
        try {
           
            $query = "SHOW COLUMNS FROM " . $table;
            $statement = Flight::db()->prepare($query);

            $statement->execute();
            $columns = $statement->fetchAll(PDO::FETCH_ASSOC);
            $headers = [];
            foreach ($columns as $column) {
                $headers[] = $column['Field'];
            }
    
            return $headers;
        } catch (PDOException $e) {
            error_log($e->getMessage()); 
            return []; 
        }
    }
    
    
    public function htmlPart( $tab,$table) {
        $head=array();
        $head=$this->header($table);
        $html = "
    <table border='1'>
        <tr>";
    
        foreach ($head as $headers) {
            $html .= "<th>" . htmlspecialchars($headers) . "</th>";
        }
        
        $html .= "</tr>
        <tr>";
        
        foreach ($tab as $row) {
            $html .= "<tr>";
            if (is_array($row)) {
                foreach ($row as $data) {
                    $html .= "<td>" . htmlspecialchars((string)$data) . "</td>";
                }
            } else {
                $html .= "<td>" . htmlspecialchars((string)$row) . "</td>";
            }
            $html .= "</tr>";
        }
        
        $html .= "</table>";
    
        
        return $html;
    }*/
    
}
