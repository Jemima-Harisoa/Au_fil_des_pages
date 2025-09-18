<?php

namespace app\models;

use PDO;
use PDOException;
use Flight;

class AnnoncesModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    // Récupérer toutes les annonces
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM annonces ORDER BY date_publication DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une annonce par ID
    public function get($idAnnonce) {
        $stmt = $this->db->prepare("SELECT * FROM annonces WHERE id_annonce = :id_annonce");
        $stmt->execute(['id_annonce' => $idAnnonce]);
        $annonce = $stmt->fetch(PDO::FETCH_ASSOC);
        return $annonce ?: null;
    }

    // Ajouter une nouvelle annonce
    public function add($titre, $lien, $date_publication, $date_expiration, $nombre_poste, $id_profil) {
        $stmt = $this->db->prepare("
            INSERT INTO annonces (titre, lien, date_publication, date_expiration, nombre_poste, id_profil)
            VALUES (:titre, :lien, :date_publication, :date_expiration, :nombre_poste, :id_profil)
        ");
        return $stmt->execute([
            'titre' => $titre,
            'lien' => $lien,
            'date_publication' => $date_publication,
            'date_expiration' => $date_expiration,
            'nombre_poste' => $nombre_poste,
            'id_profil' => $id_profil
        ]);
    }

    // Mettre à jour une annonce
    public function update($idAnnonce, $titre, $lien, $date_publication, $date_expiration, $nombre_poste, $id_profil) {
        $stmt = $this->db->prepare("
            UPDATE annonces 
            SET titre = :titre, lien = :lien, date_publication = :date_publication, 
                date_expiration = :date_expiration, nombre_poste = :nombre_poste, id_profil = :id_profil
            WHERE id_annonce = :id_annonce
        ");
        return $stmt->execute([
            'id_annonce' => $idAnnonce,
            'titre' => $titre,
            'lien' => $lien,
            'date_publication' => $date_publication,
            'date_expiration' => $date_expiration,
            'nombre_poste' => $nombre_poste,
            'id_profil' => $id_profil
        ]);
    }

    // Supprimer une annonce
    public function delete($idAnnonce) {
        $stmt = $this->db->prepare("DELETE FROM annonces WHERE id_annonce = :id_annonce");
        return $stmt->execute(['id_annonce' => $idAnnonce]);
    }

    public function create($titre, $nombrePoste, $idProfil, $contenuAnnonce, $date_expiration) {
        $date_publication = date('Y-m-d H:i:s');
        $filename = $this->createFichier($contenuAnnonce, $nombrePoste, $idProfil, $titre, $date_publication);
        $this->add($titre, $filename, $date_publication, $date_expiration, $nombrePoste, $idProfil);
        return $filename;
    }

    public function createFichier($contenuAnnonce, $nombrePoste, $idProfil, $titre = "Nouvelle annonce", $date_publication = null) {
        // Structure de l’annonce
        $data = [
            'titre' => $titre,
            'id_profil' => $idProfil,
            'nombre_poste' => $nombrePoste,
            'contenu' => $contenuAnnonce,
            'date_publication' => $date_publication ?? date('Y-m-d H:i:s')
        ];
    
        // Nom du fichier basé sur la date (pour cohérence avec la DB)
        $dir = __DIR__ . "\..\..\public\json\publier\p";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    
        // On base le nom du fichier sur la date de publication pour cohérence
        $filename = $dir . "annonce_" . str_replace([' ', ':'], ['_', '-'], $data['date_publication']) . ".json";
    
        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
        return $filename;
    }

}
