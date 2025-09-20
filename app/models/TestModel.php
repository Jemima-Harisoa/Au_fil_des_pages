<?php

namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
class TestModel{
    private $db;
    private $id_test;
    private $id_candidat;
    private $score_test;
    private $date_test;

    public function __construct($db,$id_candidat=0, $score_test=0., $date_test=new \DateTime()){
        $this->db = $db;
        $this->id_candidat = $id_candidat;
        $this->score_test = $score_test;
        $this->date_test = $date_test;
    }
    public function save() {
        $stmt = $this->db->prepare("INSERT INTO tests (id_candidat, score_test, date_test) 
                              VALUES (?, ?, ?)");
        $stmt->execute([$this->id_candidat, $this->score_test, $this->date_test]);
        $this->id_test = $db->lastInsertId();
    }

    // Récupérer tous les tests
    public  function all() {
        $db = Flight::db();
        $stmt = $db->query("SELECT * FROM tests");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un test par ID
    public static function find($id) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM tests WHERE id_test = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // fonction qui sert a recuperer les candidats qui ont reussi le test
    public  function getCandidatsAvecSuccesTest(int $nombreCandidats)
    {
        try {
            // Vérification du paramètre
            if ($nombreCandidats <= 0 || $nombreCandidats == null) {
                throw new \InvalidArgumentException("Le nombre de candidats doit être supérieur à 0.");
            }
            $db = $this->db;
            // Préparation de la requête
            $sql = "
            SELECT *
            FROM (
                SELECT 
                    ca.*,
                    te.score_test,
                    te.date_test,
                    ROW_NUMBER() OVER (PARTITION BY ca.id_profil ORDER BY te.score_test DESC) AS rang
                FROM candidats ca
                JOIN tests te ON ca.id_candidat = te.id_candidat
            ) t
            WHERE rang <= :nombre
            ORDER BY id_profil, rang;
            ";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':nombre', $nombreCandidats, \PDO::PARAM_INT);
            $stmt->execute();

            $resultats = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return Flight::json([
                'success' => true,
                'data' => $resultats
            ]);

        } catch (\Exception $e) {
            // Gestion des erreurs
            
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


}