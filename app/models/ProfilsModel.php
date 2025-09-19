<?php

namespace app\models;

use PDO;
use PDOException;
use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class ProfilsModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }
    
    // CREATE
    public function create($data)
    {
        $sql = "INSERT INTO profils (titre, id_departement, competences, skills, loisirs, niveau_etudes, id_diplome, experience_pro, certifications, langues, id_type_contrat, est_minimum)
                VALUES (:titre, :id_departement, :competences, :skills, :loisirs, :niveau_etudes, :id_diplome, :experience_pro, :certifications, :langues, :id_type_contrat, :est_minimum)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getAll()
{
    $sql = "SELECT 
    p.id_profil, 
    p.titre, 
    p.competences, 
    p.skills, 
    p.loisirs,
    d.nom AS diplome, 
    d.niveau AS niveau_diplome,
    f.nom AS filiere,
    p.experience_pro, 
    p.certifications, 
    p.langues,
    tc.nom AS type_contrat,
    p.est_minimum
FROM profils p
LEFT JOIN diplomes d ON p.id_diplome = d.id_diplome
LEFT JOIN filieres f ON p.id_filiere = f.id_filiere
LEFT JOIN type_contrats tc ON p.id_type_contrat = tc.id_type_contrat;
";
    
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getById($id)
{
    $sql = "SELECT p.id_profil, p.titre, p.competences, p.skills, p.loisirs,
                   d.nom AS diplome, d.niveau AS niveau_diplome,
                   p.filiere, p.experience_pro, p.certifications, p.langues,
                   tc.nom AS type_contrat,
                   p.est_minimum
            FROM profils p
            LEFT JOIN diplomes d ON p.id_diplome = d.id_diplome
            LEFT JOIN type_contrats tc ON p.id_type_contrat = tc.id_contrat
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
                    niveau_etudes = :niveau_etudes,
                    id_diplome = :id_diplome,
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