<?php
namespace app\models;

use PDO;

class DisponibiliteEntretienModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Récupérer toutes les disponibilités
    public function all(): array {
        $stmt = $this->db->query("SELECT * FROM disponibilite_entretien");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une disponibilité par ID
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM disponibilite_entretien WHERE id_dispo = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Créer une nouvelle disponibilité
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour, est_valide)
            VALUES (:id_responsable, :heure_debut, :heure_fin, :jour, :est_valide)
        ");
        $stmt->execute([
            ':id_responsable' => $data['id_responsable'],
            ':heure_debut'    => $data['heure_debut'],
            ':heure_fin'      => $data['heure_fin'],
            ':jour'           => $data['jour'],
            ':est_valide'     => $data['est_valide'] ?? true, // valeur par défaut
        ]);
        return (int)$this->db->lastInsertId();
    }

    // Mettre à jour une disponibilité
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE disponibilite_entretien 
            SET id_responsable = :id_responsable,
                heure_debut = :heure_debut,
                heure_fin = :heure_fin,
                jour = :jour,
                est_valide = :est_valide
            WHERE id_dispo = :id
        ");
        return $stmt->execute([
            ':id_responsable' => $data['id_responsable'],
            ':heure_debut'    => $data['heure_debut'],
            ':heure_fin'      => $data['heure_fin'],
            ':jour'           => $data['jour'],
            ':est_valide'     => $data['est_valide'],
            ':id'             => $id
        ]);
    }

    // Supprimer une disponibilité
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM disponibilite_entretien WHERE id_dispo = ?");
        return $stmt->execute([$id]);
    }
    public function getTempsDisponiblesEntretien($idResponsable){
        $query = "SELECT * FROM disponibilite_entretien where id_responsable = :id_responsable ORDER by jour asc";
        $db = $this->db();
        try{
            if($idResponsable== 0 || $idResponsable == null){
                throw new \Exception("l'id du Responsable doit etre positive et non nul");
            }
            $stmt= $db->prepare($query);
            $stmt->execute(["id_responsable"=> $idResponsable]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}