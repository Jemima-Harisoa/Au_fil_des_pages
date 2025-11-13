<?php
namespace app\models;

use PDO;

class HistoriqueDisponibiliteEntretienModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Récupérer toutes les disponibilités
    public function all(): array {
        $stmt = $this->db->query("SELECT * FROM histrorique_disponibilite_entretien");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une disponibilité par ID
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM histrorique_disponibilite_entretien WHERE id_historique = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Créer une nouvelle disponibilité
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO historique_disponibilite_entretien (id_dispo, heure_debut, heure_fin, jour, est_valide)
            VALUES (:id_dispo, :heure_debut, :heure_fin, :jour, :est_valide)
        ");
        $stmt->execute([
            ':id_dispo' => $data['id_dispo'],
            ':heure_debut'    => $data['heure_debut'],
            ':heure_fin'      => $data['heure_fin'],
            ':jour'           => $data['jour'],
            ':est_valide'     => $data['est_valide']
        ]);
        return (int)$this->db->lastInsertId();
    }

    
}