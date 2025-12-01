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
        SELECT 
            employes.id_employe,
            personnes.id_personne,
            personnes.nom,
            personnes.prenom,
            personnes.date_naissance,
            personnes.contact,
            personnes.lien_image,
            manager_admins.id_manager
        FROM admins
        JOIN employes 
            ON admins.id_employe = employes.id_employe
        JOIN personnes 
            ON personnes.id_personne = employes.id_personne
        LEFT JOIN manager_admins
            ON manager_admins.id_admin = admins.id_admin
        WHERE admins.id_admin = :id_admin
    ");

    $stmt->execute(['id_admin' => $idAdmin]);
    $info = $stmt->fetch(PDO::FETCH_ASSOC);

    return $info ?: null;
}

}