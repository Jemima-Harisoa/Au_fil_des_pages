<?php

namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
use PDO;
use PDOException;
use Exception;

class CVModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public static function insertCandidat($idAnnonce) {
        $db = Flight::db();
        
        try {
            // 1️⃣ Récupérer le dernier id_personne
            $stmtMax = $db->query("SELECT MAX(id_personne) AS max_id FROM personnes");
            $maxIdRow = $stmtMax->fetch(PDO::FETCH_ASSOC);
            $idPersonne = $maxIdRow['max_id'] ?? null;
    
            if (!$idPersonne) {
                throw new Exception("Aucune personne trouvée dans la table personnes");
            }
    
            // 2️⃣ Récupérer le titre du poste
            $stmtPoste = $db->prepare("SELECT titre FROM annonces WHERE id_annonce = :idAnnonce");
            $stmtPoste->execute([':idAnnonce' => $idAnnonce]);
            $posteRow = $stmtPoste->fetch(PDO::FETCH_ASSOC);
            $poste = $posteRow['titre'] ?? null;
    
            if (!$poste) {
                throw new Exception("Annonce non trouvée pour l'id_annonce: $idAnnonce");
            }
    
            // 3️⃣ Insérer dans la table candidats
            $sql = "INSERT INTO candidats (id_personne, id_annonce, poste) VALUES (:idPersonne, :idAnnonce, :poste)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':idPersonne' => $idPersonne,
                ':idAnnonce' => $idAnnonce,
                ':poste' => $poste
            ]);
    
            return true;
        } catch (Exception $e) {
            // Gérer l'erreur (peut être adapté selon vos besoins)
            throw new Exception("Erreur lors de l'insertion du candidat: " . $e->getMessage());
        }
    }

    public static function insertCV($dataCV, $idAnnonce){
        $db = Flight::db();
        
        $sql = "INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) VALUES (:nom, :prenoms, :date_naissance, :contact, :photo_path)";
    
        $stmt = $db->prepare($sql);
    
        $stmt->execute([
            ':nom'           => $dataCV[0],
            ':prenoms'       => $dataCV[1],
            ':date_naissance'=> $dataCV[2],
            ':contact'       => $dataCV[3],
            ':photo_path'    => $dataCV[4]
        ]);

        self::insertCandidat($idAnnonce);
    }
}