<?php

namespace app\models\migration;

use Flight;
use PDO;

class HistoriqueContratModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db(); // Connexion PDO
    }

    /**
     * Récupérer tous les historiques de contrats
     */
    public function getAll() {
        $sql = "SELECT * FROM historique_contrat ORDER BY etat, date_heure_validation DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un historique de contrat par id_contrat
     */
    public function getById($id_contrat) {
        $sql = "SELECT * FROM historique_contrat WHERE id_contrat = :id_contrat";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_contrat' => $id_contrat]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lister les historiques d’un candidat
     */
    public function listByCandidat($id_candidat) {
        $sql = "SELECT * FROM historique_contrat WHERE id_candidat = :id_candidat ORDER BY date_heure_validation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_candidat' => $id_candidat]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lister les historiques validés par un employé (redacteur)
     */
    public function listByRedacteur($id_employe) {
        $sql = "SELECT * FROM historique_contrat WHERE redacteur = (
                    SELECT nom || ' ' || prenom FROM employes e
                    JOIN personnes p ON e.id_personne = p.id_personne
                    WHERE e.id_employe = :id_employe
                ) ORDER BY date_heure_validation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $id_employe]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
