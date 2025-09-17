<?php

namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
class TestModel{
    private $id_test;
    private $id_candidat;
    private $score_test;
    private $date_test;

    public function __construct($id_candidat, $score_test, $date_test) {
        $this->id_candidat = $id_candidat;
        $this->score_test = $score_test;
        $this->date_test = $date_test;
    }
    public function save() {
        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO tests (id_candidat, score_test, date_test) 
                              VALUES (?, ?, ?)");
        $stmt->execute([$this->id_candidat, $this->score_test, $this->date_test]);
        $this->id_test = $db->lastInsertId();
    }

    // Récupérer tous les tests
    public static function all() {
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

}