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
        $repQstVrai = Query::query("SELECT * FROM Reponses_Question WHERE id_question = ? AND est_Correct=1", [$idQst]);
        return $repQstVrai;
    }
    public function getRepQst($idQst) {
        $repQst = Query::query("SELECT * FROM Reponses_Question WHERE id_question = ?", [$idQst]);
        return $repQst;
    }
    public function getIdProfil($idCandidat) {
        $repQst = Query::query("SELECT * FROM Profils WHERE id_candidat = ?", [$idCandidat]);
        return $repQst;
    }
    public function createFichierTemp($data) {
        $filePath = __DIR__ . '/reponses_candidat.json';
        $jsonData = json_encode($data,   JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($filePath, $jsonData);
        return $filePath;
    }

   public function comparaisonReponse($data, $idProfil) {
    $score = 0; 
    $questions = $this->getQst($idProfil);

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
        if ($repCorrectes[0]['reponse'] == $repCandidat[0]) {
            $score += $q['note']; 
        }
    }
    $dateTest = date('Y-m-d H:i:s'); 
    $sql = "INSERT INTO tests (id_candidat, score_test, date_test) VALUES (?, ?, ?)";
    Query::query($sql, [$idProfil, $score, $dateTest]);
}
    public function listTest(){
         $test = Query::query(" select *from tests join candidats on tests.id_candidat=candidats.id_candidat join personnes on personnes.id_personne=candidats.id_personne");
        return $test;
    }
     public function trierTests(){
         $test = Query::query(" select *from tests join candidats on tests.id_candidat=candidats.id_candidat join personnes on personnes.id_personne=candidats.id_personne");
        return $test;
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
