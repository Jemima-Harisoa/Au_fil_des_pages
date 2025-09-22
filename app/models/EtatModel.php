<?php

namespace app\models;

use Flight;

class EtatModel {
    private $id_etat;
    private $nom;

    /** @var \PDO */
    private $db;

    public function __construct() {
        // Récupération de la connexion PDO depuis Flight
        $this->db = Flight::db();
    }

    // --- Getters ---
    public function getIdEtat(): ?int {
        return $this->id_etat;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    // --- Setters ---
    public function setIdEtat(int $id): void {
        $this->id_etat = $id;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    // --- Méthodes BDD ---
    public function list(): array {
        $sql = "SELECT * FROM etat";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function save($data) {
        $sql = "INSERT INTO etat (nom) VALUES (:nom)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // Mettre à jour un état
    public function updateById($id, $data) {
        $sql = "UPDATE etat SET nom=:nom WHERE id_etat=:id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Supprimer un état
    public function deleteById($id) {
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

    public function findById(int $id): ?array {
        $sql = "SELECT * FROM etat WHERE id_etat = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function insert(): bool {
        $sql = "INSERT INTO etat (nom) VALUES (:nom)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['nom' => $this->nom]);
    }

    public function update(): bool {
        if (!$this->id_etat) {
            throw new \Exception("Impossible de mettre à jour sans id_etat.");
        }
        $sql = "UPDATE etat SET nom = :nom WHERE id_etat = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nom' => $this->nom,
            'id'  => $this->id_etat
        ]);
    }

    public function delete(): bool {
        if (!$this->id_etat) {
            throw new \Exception("Impossible de supprimer sans id_etat.");
        }
        $sql = "DELETE FROM etat WHERE id_etat = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $this->id_etat]);
    }

    // Rechercher une valeur précise dans la table
    public function search($field, $value) {
        $sql = "SELECT * FROM etat WHERE {$field} ILIKE :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
