<?php
namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
class ConfigEntretienModel {
    private $id_config_entretien;
    private $id_departement;
    private $duree_entretien;

    // Constructeur
    public function __construct($id_departement, $duree_entretien) {
        $this->id_departement = $id_departement;
        $this->duree_entretien = $duree_entretien;
    }

    // Méthode pour insérer une nouvelle configuration
    public function save() {
        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO config_entretien (id_departement, duree_entretien) 
                              VALUES (?, ?)");
        $stmt->execute([
            $this->id_departement,
            $this->duree_entretien
        ]);
        $this->id_config_entretien = $db->lastInsertId();
    }

    // Récupérer toutes les configurations
    public static function all() {
        $db = Flight::db();
        $stmt = $db->query("SELECT * FROM config_entretien");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une configuration par ID
    public static function find($id) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM config_entretien WHERE id_config_entretien = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Optionnel : mettre à jour une configuration
    public function update() {
        $db = Flight::db();
        $stmt = $db->prepare("UPDATE config_entretien 
                              SET id_departement = ?, duree_entretien = ? 
                              WHERE id_config_entretien = ?");
        $stmt->execute([
            $this->id_departement,
            $this->duree_entretien,
            $this->id_config_entretien
        ]);
    }

    // Optionnel : supprimer une configuration
    public function delete() {
        $db = Flight::db();
        $stmt = $db->prepare("DELETE FROM config_entretien WHERE id_config_entretien = ?");
        $stmt->execute([$this->id_config_entretien]);
    }
}
