<?php

namespace app\models\migration;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class ScoringModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    // Lire par id_candidat
    public function getBy($field, $value) {
        $sql = "SELECT * FROM view_scoring WHERE {$field} = :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Lister tous
    public function list() {
        $sql = "SELECT * FROM view_scoring";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
