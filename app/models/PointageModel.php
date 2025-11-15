<?php 
namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class PointageModel {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function getDernierPointage($idEmploye) {
        $sql = "SELECT * FROM pointage WHERE id_employe = ? ORDER BY id_pointage DESC LIMIT 1";
        $query = $this->db->prepare($sql);
        $query->execute([$idEmploye]);
        return $query->fetch();
    }

    public function ajouterPointage($idEmploye) {
        $sql = "INSERT INTO pointage (id_employe, connexion, deconnexion) VALUES (?, NOW(), NULL)";
        $query = $this->db->prepare($sql);
        $query->execute([$idEmploye]);
    }

    public function cloturerPointage($idPointage) {
    $sql = "UPDATE pointage 
            SET deconnexion = NOW()
            WHERE id_pointage = ?";
    $query = $this->db->prepare($sql);
    $query->execute([$idPointage]);
}

}
?>