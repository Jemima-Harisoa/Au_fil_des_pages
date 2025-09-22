<?php

namespace app\models;

use Flight;
use PDO;
use PDOException;
use Exception;

class CVModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    // Récupérer le dernier id_candidat
    public static function getLastCandidatId() {
        $db = Flight::db();
        $stmt = $db->query("SELECT MAX(id_candidat) AS last_id FROM candidats");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['last_id'] ?? null;
    }
    
    // Récupérer le dernier id_cv_candidats
    public static function getLastCvCandidatId() {
        $db = Flight::db();
        $stmt = $db->query("SELECT MAX(id_cv_candidats) AS last_id FROM cv_candidats");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['last_id'] ?? null;
    }

    // Insérer un candidat lié à une annonce et un profil
    public static function insertCandidat($idAnnonce, $idProfil) {
        $db = Flight::db();
        try {
            $stmtMax = $db->query("SELECT MAX(id_personne) AS max_id FROM personnes");
            $maxIdRow = $stmtMax->fetch(PDO::FETCH_ASSOC);
            $idPersonne = $maxIdRow['max_id'] ?? null;
            if (!$idPersonne) {
                throw new Exception("Aucune personne trouvée dans la table personnes");
            }

            $stmtPoste = $db->prepare("SELECT titre FROM annonces WHERE id_annonce = :idAnnonce");
            $stmtPoste->execute([':idAnnonce' => $idAnnonce]);
            $posteRow = $stmtPoste->fetch(PDO::FETCH_ASSOC);
            $poste = $posteRow['titre'] ?? null;
            if (!$poste) {
                throw new Exception("Annonce non trouvée pour l'id_annonce: $idAnnonce");
            }

            $sql = "INSERT INTO candidats (id_personne, id_annonce, id_profil, poste, id_utilisateur) 
                    VALUES (:idPersonne, :idAnnonce, :idProfil, :poste, :id_utilisateur)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':idPersonne' => $idPersonne,
                ':idAnnonce' => $idAnnonce,
                ':idProfil' => $idProfil,
                ':poste' => $poste,
                ':id_utilisateur' => $_SESSION['utilisateur']['id_utilisateur']
            ]);

            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'insertion du candidat: " . $e->getMessage());
        }
    }

    // Récupérer le dernier seuil
    public static function getLastTreshold() {
        $db = Flight::db();
        $stmt = $db->query("SELECT * FROM treshold ORDER BY id_treshold DESC LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['valeur'] ?? null;
    }

    // Récupérer le dernier candidat
    public static function getLastCandidat() {
        $db = Flight::db();
        $stmt = $db->query("SELECT * FROM candidats ORDER BY id_candidat DESC LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id_candidat'] ?? null;
    }

    // Insérer un CV complet
    public static function insertCV($dataCV, $idAnnonce, $idProfil, $idDiplome, $combineValuesMap) {
        $db = Flight::db();
        $sql = "INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) 
                VALUES (:nom, :prenoms, :date_naissance, :contact, :photo_path)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nom'           => $dataCV[0],
            ':prenoms'       => $dataCV[1],
            ':date_naissance'=> $dataCV[2],
            ':contact'       => $dataCV[3],
            ':photo_path'    => $dataCV[4]
        ]);

        self::insertCandidat($idAnnonce, $idProfil);
        self::insertCVCandidats($idDiplome, $combineValuesMap);

        $note_similarite = self::getProfilCvComparison(self::getLastTreshold());
        return $note_similarite;
    }

    // Insérer les données CV dans cv_candidats
    public static function insertCVCandidats($idDiplome, $combineValuesMap) {
        $db = Flight::db();
        $stmtMax = $db->query("SELECT MAX(id_candidat) AS max_id FROM candidats");
        $maxIdRow = $stmtMax->fetch(PDO::FETCH_ASSOC);
        $idCandidat = $maxIdRow['max_id'] ?? null;
        if (!$idCandidat) {
            throw new Exception("Aucune personne trouvée dans la table personnes");
        }

        $sql = 'INSERT INTO cv_candidats (
                    id_candidat, competences, skills, loisirs, id_diplome, filiere, 
                    experience_pro, certifications, langues
                ) VALUES (
                    :id_candidat, :competences, :skills, :loisirs, :id_diplome, :filiere,
                    :experience_pro, :certifications, :langues
                )';
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id_candidat'    => $idCandidat,
            ':competences'    => $combineValuesMap['competences'],
            ':skills'         => $combineValuesMap['skills'],
            ':loisirs'        => $combineValuesMap['loisirs'],
            ':id_diplome'     => $idDiplome,
            ':filiere'        => $combineValuesMap['filiere'],
            ':experience_pro' => $combineValuesMap['experience-pro'],
            ':certifications' => $combineValuesMap['certification'],
            ':langues'        => $combineValuesMap['langues']
        ]);
    }

    // Comparaison de texte avec API
    public static function comparaison($text1, $text2, $threshold) {
        $requete = "SELECT cle_api FROM api";
        $stmt = Flight::db()->prepare($requete);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $apiKey = $row['cle_api'];

        $apiUrl = "https://api-inference.huggingface.co/models/sentence-transformers/all-MiniLM-L6-v2";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $apiKey",
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'inputs' => [
                    'source_sentence' => $text1 ?? '',
                    'sentences' => [$text2 ?? '']
                ],
                'options' => ['use_cache' => false, 'wait_for_model' => true]
            ]),
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($httpCode !== 200) {
            throw new Exception("Échec de la requête API avec le code $httpCode: $response" . ($error ? " Erreur cURL: $error" : ""));
        }

        $result = json_decode($response, true);
        if (!is_array($result) || !isset($result[0])) {
            throw new Exception("Format de réponse API inattendu: $response");
        }

        return $result[0];
    }

    // Vérifie si la moyenne dépasse le seuil
    public static function isAverigeAboveThreshold($average) {
        $threshold = self::getLastTreshold();
        return $average >= $threshold;
    }

    // Vérifie si le dernier CV correspond au diplôme du profil
    public static function checkLatestCvDiplomeMatch() {
        $db = Flight::db();
        $stmt = $db->query("
            SELECT cv.id_cv_candidats, cv.id_candidat, cv.id_diplome AS diplome_cv, 
                   p.id_profil, p.id_diplome AS diplome_profil, p.est_minimum, 
                   dp.niveau AS niveau_profil, dc.niveau AS niveau_cv
            FROM cv_candidats cv
            JOIN candidats c ON cv.id_candidat = c.id_candidat
            JOIN profilsCV p ON c.id_profil = p.id_profil
            JOIN diplomes dp ON p.id_diplome = dp.id_diplome
            JOIN diplomes dc ON cv.id_diplome = dc.id_diplome
            WHERE cv.id_candidat = (SELECT MAX(id_candidat) FROM candidats)
        ");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;

        $niveauProfil = (int)$row['niveau_profil'];
        $niveauCv = (int)$row['niveau_cv'];
        $estMinimum = (bool)$row['est_minimum'];

        return $estMinimum ? ($niveauCv >= $niveauProfil) : ($niveauCv == $niveauProfil);
    }

    // Comparaison profil ↔ CV et insertion validation
    public static function getProfilCvComparison($threshold) {
        $sql = "SELECT p.competences AS competences_profil, p.skills AS skills_profil, 
                       p.loisirs AS loisirs_profil, p.filiere AS filiere_profil, 
                       p.experience_pro AS experience_pro_profil, p.certifications AS certifications_profil, 
                       p.langues AS langues_profil, c.competences AS competences_cv, 
                       c.skills AS skills_cv, c.loisirs AS loisirs_cv, c.filiere AS filiere_cv, 
                       c.experience_pro AS experience_pro_cv, c.certifications AS certifications_cv, 
                       c.langues AS langues_cv
                FROM candidats cand
                JOIN profilsCV p ON cand.id_profil = p.id_profil
                JOIN cv_candidats c ON cand.id_candidat = c.id_candidat
                WHERE cand.id_candidat = (SELECT MAX(id_candidat) FROM candidats)";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) return 0;

        $allInformation = [
            'competences' => ['cv' => [], 'profilsCV' => []],
            'skills' => ['cv' => [], 'profilsCV' => []],
            'loisirs' => ['cv' => [], 'profilsCV' => []],
            'filiere' => ['cv' => [], 'profilsCV' => []],
            'experience_pro' => ['cv' => [], 'profilsCV' => []],
            'certifications' => ['cv' => [], 'profilsCV' => []],
            'langues' => ['cv' => [], 'profilsCV' => []]
        ];

        foreach ($result as $row) {
            foreach ($allInformation as $key => $value) {
                $allInformation[$key]['cv'][] = $row[$key . '_cv'];
                $allInformation[$key]['profilsCV'][] = $row[$key . '_profil'];
            }
        }

        $totalScores = 0;
        $totalComparisons = 0;

        foreach ($allInformation as $key => $types) {
            $profilText = implode(' || ', $types['profilsCV']);
            $cvText = implode(' || ', $types['cv']);

            try {
                $score = self::comparaison($profilText, $cvText, $threshold);
                $totalScores += $score;
                $totalComparisons++;
            } catch (Exception $e) {
                continue;
            }
        }

        $average = ($totalComparisons > 0) ? ($totalScores / $totalComparisons) : 0;

        $sqlInsert = "INSERT INTO validation_cv (id_cv_candidat, id_status_validation_cv, similarite) 
                      VALUES (:id_cv_candidat, :statut, :similarite)";

        if (self::checkLatestCvDiplomeMatch()) {
            $stmt = Flight::db()->prepare($sqlInsert);
            $stmt->execute([
                ':id_cv_candidat' => (int)self::getLastCvCandidatId(),
                ':statut' => self::isAverigeAboveThreshold($average) ? 1 : 2,
                ':similarite' => $average
            ]);
        } else {
            $stmt = Flight::db()->prepare($sqlInsert);
            $stmt->execute([
                ':id_cv_candidat' => (int)self::getLastCvCandidatId(),
                ':statut' => 2,
                ':similarite' => $average
            ]);
        }
        // echo "Average". $average;

        return $average;
    }

    // Récupérer tous les CV
    public static function getAllCVs() {
        $db = Flight::db();
        $query = "
            SELECT 
                p.id_personne, p.nom, p.prenom, p.date_naissance, p.contact, p.lien_image,
                c.id_candidat, c.poste, a.id_annonce, a.titre AS annonce_titre,
                svc.statut AS validation_statut,
                vc.similarite AS similarite,
                pr.competences AS profil_competences, pr.skills AS profil_skills, 
                pr.loisirs AS profil_loisirs, pr.filiere AS profil_filiere, pr.experience_pro AS profil_experience_pro,
                pr.certifications AS profil_certifications, pr.langues AS profil_langues,
                cv.competences AS cv_competences, cv.skills AS cv_skills, cv.loisirs AS cv_loisirs, 
                cv.filiere AS cv_filiere, cv.experience_pro AS cv_experience_pro,
                cv.certifications AS cv_certifications, cv.langues AS cv_langues,
                cv.date_deposition AS date_deposition
            FROM personnes p
            JOIN candidats c ON p.id_personne = c.id_personne
            JOIN annonces a ON c.id_annonce = a.id_annonce
            JOIN profilsCV pr ON c.id_profil = pr.id_profil
            JOIN cv_candidats cv ON c.id_candidat = cv.id_candidat
            JOIN validation_cv vc ON cv.id_cv_candidats = vc.id_cv_candidat
            JOIN status_validation_cv svc ON vc.id_status_validation_cv = svc.id_status_validation_cv
            ORDER BY cv.date_deposition DESC;
        ";

        try {
            $stmt = $db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Query failed: ' . $e->getMessage());
        }
    }

    // Récupérer la dernière validation CV
    public function getLastValidationCV() {
        $db = Flight::db();
        $requete = "SELECT MAX(id_validation_cv) AS last_validation FROM validation_cv;";
        $stmt = $db->prepare($requete);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['last_validation'] ?? null;
    }
}
