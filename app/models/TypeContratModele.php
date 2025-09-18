<?php

namespace app\models;

use PDO;
use PDOException;
use Flight;

class TypeContratModele {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    // CREATE
    public function create($nom)
    {
        $sql = "INSERT INTO type_contrats (nom) VALUES (:nom)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['nom' => $nom]);
    }

    // READ (all)
    public function getAll()
    {
        $sql = "SELECT * FROM type_contrats";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ (one)
    public function getById($id)
    {
        $sql = "SELECT * FROM type_contrats WHERE id_contrat = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function update($id, $nom)
    {
        $sql = "UPDATE type_contrats SET nom = :nom WHERE id_contrat = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['nom' => $nom, 'id' => $id]);
    }

    // DELETE
    public function delete($id)
    {
        $sql = "DELETE FROM type_contrats WHERE id_contrat = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}