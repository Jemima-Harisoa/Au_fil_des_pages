<?php

namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class AdminModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public function getDepartementByIdAdmin($id_admin) {
        $sql = "
            SELECT ad.*, de.nom_departement
            FROM admin ad
            JOIN employes em ON ad.id_employe = em.id_employe
            JOIN departements de ON em.id_departement = de.id_departement
            WHERE ad.id_admin = :id_admin
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_admin', $id_admin, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC); // un seul admin
    }

}