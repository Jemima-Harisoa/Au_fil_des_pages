<?php
namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class ConfigEntretienModel {
    private $db;

    // Constructeur
    public function __construct($db) {
        $this->db = $db;
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

    public function getConfigurationEntretienResponsable($responsable){
        $responsableEntretien = Flight::responsableEntretienModel();
        $departementResponsable = $responsableEntretien->getDepartement($responsable);
        $query = "SELECT re.*,de.*
                    FROM (SELECT * 
                FROM responsable_entretien  
                WHERE  id_responsable = ?
        )re
        JOIN employes em 
        ON em.id_employe = re.id_employe
        JOIN departements de 
        ON de.id_departement = em.id_departement";

        try {
            if($responsable == null){
                throw new Exception("le responsable n'a pas ete trouve");   
            }
            $db = Flight::db();
            $stmt = $db->prepare($query);
            $stmt->execute([$responsable["id_responsable"]]);
            return $stmt->fetch();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());

        }
    }
}

