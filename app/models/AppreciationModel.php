<?php

namespace app\models;

use PDO;
use PDOException;
use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class AppreciationModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }
    public function getAll(){
        $stmt = $pdo->query("SELECT * FROM appreciation ORDER BY code ASC");
        $appreciations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $appreciations;
    }
    function getAppreciationByNote($note){
    $appreciations = self::getAll();
    $count = count($appreciations);
    for ($i = 0; $i < $count; $i++) {
        $current = $appreciations[$i];
        $next = $appreciations[$i + 1] ?? null; 
        if ($next) {
            
            if ($note >= $current['code'] && $note < $next['code']) {
                return $current['type_appreciation'];
            }
        } else {
            
            if ($note >= $current['code']) {
                return $current['type_appreciation'];
            }
        }
    }
        return "Aucune appréciation trouvée";
    }

}