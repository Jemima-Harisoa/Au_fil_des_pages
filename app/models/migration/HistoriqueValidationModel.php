<?php

namespace app\models\migration;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class HistoriqueValidationModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db(); // Connexion PDO
    }

    // Enregistrer un historique de validation
    public function save($data) {
        $sql = "INSERT INTO historique_validation (id_employe, id_candidat, date_heure_validation, id_etat) 
                VALUES (:id_employe, :id_candidat, :date_heure_validation, :id_etat)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // Mettre à jour un historique
    public function update($id, $data) {
        $sql = "UPDATE historique_validation 
                SET id_employe=:id_employe, id_candidat=:id_candidat, date_heure_validation=:date_heure_validation, id_etat=:id_etat
                WHERE id_historique_validation=:id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Supprimer un historique
    public function delete($id) {
        $sql = "DELETE FROM historique_validation WHERE id_historique_validation=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Chercher un historique par champ
    public function getBy($field, $value) {
        $sql = "SELECT * FROM historique_validation WHERE {$field} = :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Lister tous les historiques
    public function list() {
        $sql = "SELECT * FROM historique_validation";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lister les historiques d’un candidat
    public function listByCandidat($id_candidat) {
        $sql = "SELECT * FROM historique_validation WHERE id_candidat = :id_candidat ORDER BY date_heure_validation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_candidat' => $id_candidat]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lister les historiques d’un employé (validateur)
    public function listByEmploye($id_employe) {
        $sql = "SELECT * FROM historique_validation WHERE id_employe = :id_employe ORDER BY date_heure_validation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $id_employe]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
