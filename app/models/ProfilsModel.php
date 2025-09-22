<?php

namespace app\models;

use PDO;
use Flight;

class ProfilsModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }
    
    // CREATE
    public function create($data)
    {
        $sql = "INSERT INTO profils (titre, id_departement, competences, skills, loisirs, id_diplome, id_filiere, experience_pro, certifications, langues, id_type_contrat, est_minimum)
                VALUES (:titre, :id_departement, :competences, :skills, :loisirs, :id_diplome, :id_filiere, :experience_pro, :certifications, :langues, :id_type_contrat, :est_minimum)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // READ ALL
    public function getAll()
    {
        $sql = "SELECT p.id_profil, p.titre, p.competences, p.skills, p.loisirs,
                       d.nom AS diplome, d.niveau AS niveau_diplome,
                       f.nom AS filiere,
                       p.experience_pro, p.certifications, p.langues,
                       tc.nom AS type_contrat,
                       p.est_minimum
                FROM profils p
                LEFT JOIN diplomes d ON p.id_diplome = d.id_diplome
                LEFT JOIN filieres f ON p.id_filiere = f.id_filiere
                LEFT JOIN type_contrats tc ON p.id_type_contrat = tc.id_type_contrat";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ BY ID
    public function getById($id)
    {
        $sql = "SELECT p.id_profil, p.titre, p.competences, p.skills, p.loisirs,
                       d.nom AS diplome, d.niveau AS niveau_diplome,
                       f.nom AS filiere,
                       p.experience_pro, p.certifications, p.langues,
                       tc.nom AS type_contrat,
                       p.est_minimum
                FROM profils p
                LEFT JOIN diplomes d ON p.id_diplome = d.id_diplome
                LEFT JOIN filieres f ON p.id_filiere = f.id_filiere
                LEFT JOIN type_contrats tc ON p.id_type_contrat = tc.id_type_contrat
                WHERE p.id_profil = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function update($id, $data)
    {
        $sql = "UPDATE profils SET 
                    titre = :titre,
                    id_departement = :id_departement,
                    competences = :competences,
                    skills = :skills,
                    loisirs = :loisirs,
                    id_diplome = :id_diplome,
                    id_filiere = :id_filiere,
                    experience_pro = :experience_pro,
                    certifications = :certifications,
                    langues = :langues,
                    id_type_contrat = :id_type_contrat,
                    est_minimum = :est_minimum
                WHERE id_profil = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // DELETE
    public function delete($id)
    {
        $sql = "DELETE FROM profils WHERE id_profil = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
