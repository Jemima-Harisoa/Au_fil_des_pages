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

    public function getAllNonExpire() {
    $stmt = $this->db->prepare("SELECT * FROM annonces WHERE date_expiration IS NULL OR date_expiration >= CURRENT_DATE ORDER BY date_publication DESC");
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
        'nom_entreprise' => 'AufildesPages',
        'date_publication' => $date_publication ?? date('Y-m-d H:i:s')
    ];

    // Chemin côté serveur pour écrire le fichier
    $dirServer = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "json" . DIRECTORY_SEPARATOR . "publier" . DIRECTORY_SEPARATOR;

    // Créer le dossier si nécessaire
    if (!is_dir($dirServer)) {
        mkdir($dirServer, 0777, true);
    }

    // Nom du fichier basé sur la date de publication
    $safeDate = str_replace([' ', ':'], ['_', '-'], $data['date_publication']);
    $filenameServer = $dirServer . "annonce_" . $safeDate . ".json";

    // Écrire le fichier JSON
    file_put_contents($filenameServer, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Retourner le chemin relatif utilisable dans le navigateur
    $filenamePublic = "/json/publier/annonce_" . $safeDate . ".json";

    return $filenamePublic;
}


}
