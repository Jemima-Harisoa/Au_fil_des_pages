<?php

namespace app\models;

use Flight;

class DepartementModel {
    private $id_departement;
    private $nom;

    /** @var \PDO */
    private $db;

    public function __construct() {
        // Récupération de la connexion PDO depuis Flight
        $this->db = Flight::db();
    }

    // --- Getters ---
    public function getIdDepartement(): ?int {
        return $this->id_departement;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    // --- Setters ---
    public function setIdDepartement(int $id): void {
        $this->id_departement = $id;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    // --- Méthodes BDD ---
    // [CHATBOT] Liste tous les départements - Questions: "Quels départements?", "Liste des services?"
    public function list(): array {
        $sql = "SELECT * FROM departements";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function save($data) {
        $sql = "INSERT INTO departements (nom) VALUES (:nom)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // Mettre à jour un département
    public function updateById($id, $data) {
        $sql = "UPDATE departements SET nom=:nom WHERE id_departement=:id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Supprimer un département
    public function deleteById($id) {
        $sql = "DELETE FROM departements WHERE id_departement=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Chercher par champ
    public function getBy($field, $value) {
        $sql = "SELECT * FROM departements WHERE {$field} = :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $sql = "SELECT * FROM departements WHERE id_departement = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function insert(): bool {
        $sql = "INSERT INTO departements (nom) VALUES (:nom)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['nom' => $this->nom]);
    }

    public function update(): bool {
        if (!$this->id_departement) {
            throw new \Exception("Impossible de mettre à jour sans id_departement.");
        }
        $sql = "UPDATE departements SET nom = :nom WHERE id_departement = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nom' => $this->nom,
            'id'  => $this->id_departement
        ]);
    }

    public function delete(): bool {
        if (!$this->id_departement) {
            throw new \Exception("Impossible de supprimer sans id_departement.");
        }
        $sql = "DELETE FROM departements WHERE id_departement = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $this->id_departement]);
    }

    // Rechercher une valeur précise dans la table
    public function search($field, $value) {
        $sql = "SELECT * FROM departements WHERE {$field} ILIKE :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}