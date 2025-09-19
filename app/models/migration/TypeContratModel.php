<?php

namespace app\models\migration;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class TypeContratModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    // Enregistrer un type de contrat
    public function save($data) {
        $sql = "INSERT INTO type_contrats (nom) VALUES (:nom)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // Mettre à jour un type de contrat
    public function update($id, $data) {
        $sql = "UPDATE type_contrats SET nom=:nom WHERE id_type_contrat=:id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Supprimer
    public function delete($id) {
        $sql = "DELETE FROM type_contrats WHERE id_type_contrat=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Chercher par champ
    public function getBy($field, $value) {
        $sql = "SELECT * FROM type_contrats WHERE {$field} = :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Lister tous
    public function list() {
        $sql = "SELECT * FROM type_contrats";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
