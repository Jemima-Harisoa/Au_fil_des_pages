<?php

namespace app\models;

use Flight;

class EtatModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db(); // Connexion PDO
    }

    // Enregistrer un état
    public function save($data) {
        $sql = "INSERT INTO etat (nom) VALUES (:nom)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // Mettre à jour un état
    public function update($id, $data) {
        $sql = "UPDATE etat SET nom=:nom WHERE id_etat=:id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Supprimer un état
    public function delete($id) {
        $sql = "DELETE FROM etat WHERE id_etat=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Chercher par champ
    public function getBy($field, $value) {
        $sql = "SELECT * FROM etat WHERE {$field} = :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Lister tous les états
    public function list() {
        $sql = "SELECT * FROM etat";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
