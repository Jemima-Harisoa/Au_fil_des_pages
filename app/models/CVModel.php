<?php

namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class CVModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public static function insertCV($dataCV){
        $db = Flight::db();
        
        $sql = "INSERT INTO cv_temp (nom, prenom, date_naissance, contact, lien_image) VALUES (:nom, :prenoms, :date_naissance, :contact, :photo_path)";
    
        $stmt = $db->prepare($sql);
    
        $stmt->execute([
            ':nom'           => $dataCV[0],
            ':prenoms'       => $dataCV[1],
            ':date_naissance'=> $dataCV[2],
            ':contact'       => $dataCV[3],
            ':photo_path'    => $dataCV[4]
        ]);
    }

    public static function insertCandidat(){
        $db = Flight::db();

        $getMaxID = "SELECT MAX(id_personne) FROM personnes";
        

        $sql = "INSERT INTO candidats (id_personne, id_annonce, poste) VALUES ()";
    }
}