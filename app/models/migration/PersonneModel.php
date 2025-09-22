<?php

namespace app\models\migration;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;


class PersonneModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db(); // Connexion PDO
    }

    // Enregistrer une personne
    public function save($data) {
        $sql = "INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image, mdp) 
                VALUES (:nom, :prenom, :date_naissance, :contact, :lien_image, :mdp)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // Mettre à jour
    public function update($id, $data) {
        $sql = "UPDATE personnes 
                SET nom=:nom, prenom=:prenom, date_naissance=:date_naissance, contact=:contact, lien_image=:lien_image, mdp=:mdp
                WHERE id_personne=:id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Supprimer
    public function delete($id) {
        $sql = "DELETE FROM personnes WHERE id_personne=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Chercher par champ
    public function getBy($field, $value) {
        $sql = "SELECT * FROM personnes WHERE {$field} = :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Lister tous
    public function list() {
        $sql = "SELECT * FROM personnes";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
