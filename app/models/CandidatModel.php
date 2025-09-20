<?php

namespace app\models\migration;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;


class CandidatModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    // Enregistrer un candidat
    public function save($data) {
        $sql = "INSERT INTO candidats (id_personne , id_annonce , cv_url, poste) 
                VALUES (:id_personne, :id_annonce, :cv_url, :poste)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // Mettre à jour
    public function update($id, $data) {
        $sql = "UPDATE candidats 
                SET id_personne=:id_personne, id_annonce=:id_annonce, cv_url=:cv_url, poste=:poste
                WHERE id_candidat=:id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Supprimer
    public function delete($id) {
        $sql = "DELETE FROM candidats WHERE id_candidat=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Chercher par champ
    public function getBy($field, $value) {
        $sql = "SELECT * FROM candidats WHERE {$field} = :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Lister tous
    public function list() {
        $sql = "SELECT * FROM candidats";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
