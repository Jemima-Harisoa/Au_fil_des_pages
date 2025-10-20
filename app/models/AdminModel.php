<?php

namespace app\models;

use PDO;
use PDOException;
use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class AdminModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }
    
    
    public function getDetailsPersoAdmin($idAdmin)
    {
        $stmt = $this->db->prepare(" 
        SELECT personnes.id_personne ,personnes.nom ,personnes.prenom ,personnes.date_naissance,personnes.contact ,personnes.lien_image      
            FROM admins 
            JOIN employes 
                ON admins.id_employe=employes.id_employe 
            JOIN personnes 
                ON personnes.id_personne = employes.id_personne  
            WHERE admins.id_admin= :id_admin ;");
        $stmt->execute(['id_admin' => $idAdmin]);
        $InfoAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($InfoAdmin === false) {
            return null;
        }
        return $InfoAdmin; 
    }

    public function getDepartementByIdAdmin($id_admin) {
        $sql = "
            SELECT ad.*, de.nom
            FROM admins ad
            JOIN employes em ON ad.id_employe = em.id_employe
            JOIN departements de ON em.id_departement = de.id_departement
            where ad.id_admin = :id_admin
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_admin', $id_admin, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC); // un seul admin
    }

}