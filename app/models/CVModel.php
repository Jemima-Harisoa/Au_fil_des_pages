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

    public static function getLastCandidatId() {
        $db = Flight::db();
        $stmt = $db->query("SELECT MAX(id_candidat) AS last_id FROM candidats");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['last_id'] ?? null;
    }
    
    public static function getLastCvCandidatId() {
        $db = Flight::db();
        $stmt = $db->query("SELECT MAX(id_cv_candidats) AS last_id FROM cv_candidats");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['last_id'] ?? null;
    }
    

    public static function insertCandidat($idAnnonce, $idProfil) {
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
            $sql = "INSERT INTO candidats (id_personne, id_annonce, id_profil, poste, id_utilisateur) VALUES (:idPersonne, :idAnnonce, :idProfil, :poste, :id_utilisateur)";
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
            // Gérer l'erreur (peut être adapté selon vos besoins)
            throw new Exception("Erreur lors de l'insertion du candidat: " . $e->getMessage());
        }
    }

    public static function insertCV($dataCV, $idAnnonce, $idProfil, $idDiplome, $combineValuesMap){
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

        self::insertCandidat($idAnnonce, $idProfil); //OK
        self::insertCVCandidats($idDiplome, $combineValuesMap); //OK
        self::getProfilCvComparison($threshold = 0.10);
        
    }

    public static function insertCVCandidats($idDiplome, $combineValuesMap) {
        $db = Flight::db();

        $stmtMax = $db->query("SELECT MAX(id_candidat) AS max_id FROM candidats");
        $maxIdRow = $stmtMax->fetch(PDO::FETCH_ASSOC);
        $idCandidat = $maxIdRow['max_id'] ?? null;

        if (!$idCandidat) {
            $idCandidat = null;
            throw new Exception("Aucune personne trouvée dans la table personnes");
        }
    
        $sql = 'INSERT INTO cv_candidats (
                    id_candidat, 
                    competences, 
                    skills, 
                    loisirs, 
                    id_diplome, 
                    filiere, 
                    experience_pro, 
                    certifications, 
                    langues
                ) 
                VALUES (
                    :id_candidat, 
                    :competences, 
                    :skills, 
                    :loisirs, 
                    :id_diplome, 
                    :filiere, 
                    :experience_pro, 
                    :certifications, 
                    :langues
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
    
    //     // Affichage détaillé
    //     echo "<h2>Détails des informations Profil ↔ CV</h2>";
    //     foreach ($allInformation as $key => $types) {
    //         echo "<h3>" . ucfirst($key) . "</h3>";
    //         echo "<strong>Profil :</strong> " . implode(', ', $types['profils']) . "<br>";
    //         echo "<strong>CV :</strong> " . implode(', ', $types['cv']) . "<hr>";
    //     }
    
    //     // return $allInformation;
    // }
    
    
    // function comparaisonCompetences($idProfil, $id_cv_candidats){
    //     $db = Flight::db();

    //     // Récupérer les compétences du profil
    //     $stmtProfil = $db->prepare("SELECT competences FROM profils WHERE id_profil = :idProfil");
    //     $stmtProfil->execute([':idProfil' => $idProfil]);
    //     $profilRow = $stmtProfil->fetch(PDO::FETCH_ASSOC);
    //     $competencesProfil = $profilRow['competences'] ?? null;

    //     if (!$competencesProfil) {
    //         throw new Exception("Profil non trouvé pour l'id_profil: $idProfil");
    //     }

    //     // Récupérer les compétences du CV
    //     $stmtCV = $db->prepare("SELECT competences FROM cv_candidats WHERE id_cv_candidats = :id_cv_candidats");
    //     $stmtCV->execute([':id_cv_candidats' => $id_cv_candidats]);
    //     $cvRow = $stmtCV->fetch(PDO::FETCH_ASSOC);
    //     $competencesCV = $cvRow['competences'] ?? null;

    //     if (!$competencesCV) {
    //         throw new Exception("CV non trouvé pour l'id_cv_candidats: $id_cv_candidats");
    //     }

    //     // Comparer les compétences en utilisant la fonction comparaison
    //     return $this->comparaison($competencesProfil, $competencesCV);
    // }

    public static function comparaison($text1, $text2, $threshold) {
        // Configuration de l'API

        $requete = "SELECT cle_api FROM api";
        $stmt = Flight::db()->prepare($requete);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $apiKey = $row['cle_api'];
        $apiUrl = "https://api-inference.huggingface.co/models/sentence-transformers/all-MiniLM-L6-v2";
    
        // Initialisation de cURL
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
    
        // Exécuter la requête
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
    
        // Vérifier les erreurs HTTP
        if ($httpCode !== 200) {
            throw new Exception("Échec de la requête API avec le code $httpCode: $response" . ($error ? " Erreur cURL: $error" : ""));
        }
    
        // Décoder la réponse
        $result = json_decode($response, true);
        if (!is_array($result) || !isset($result[0])) {
            throw new Exception("Format de réponse API inattendu: $response");
        }
    
        // Récupérer le score de similarité
        return $result[0];
    }
    
    public static function isAverigeAboveThreshold($average, $threshold) {
        return $average >= $threshold;
    }
    
    public static function checkLatestCvDiplomeMatch() {
        $db = Flight::db();
    
        // Récupérer le dernier candidat et son CV
        $stmt = $db->query("
            SELECT cv.id_cv_candidats, cv.id_candidat, cv.id_diplome AS diplome_cv, 
            p.id_profil, p.id_diplome AS diplome_profil, p.est_minimum, 
            dp.niveau AS niveau_profil, dc.niveau AS niveau_cv
                FROM cv_candidats cv
                JOIN candidats c ON cv.id_candidat = c.id_candidat
                JOIN profils p ON c.id_profil = p.id_profil
                JOIN diplomes dp ON p.id_diplome = dp.id_diplome
                JOIN diplomes dc ON cv.id_diplome = dc.id_diplome
                WHERE cv.id_candidat = (SELECT MAX(id_candidat) FROM candidats)

        ");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$row) {
            return false;
        }
    
        $niveauProfil = (int)$row['niveau_profil'];
        $niveauCv = (int)$row['niveau_cv'];
        $estMinimum = (bool)$row['est_minimum'];
    
        if ($estMinimum) {
            return $niveauCv >= $niveauProfil;
        } else {
            return $niveauCv == $niveauProfil;
        }
    }
    
    public static function getProfilCvComparison($threshold) {
        $sql = "SELECT p.competences AS competences_profil, p.skills AS skills_profil, 
        p.loisirs AS loisirs_profil, p.filiere AS filiere_profil, 
        p.experience_pro AS experience_pro_profil, p.certifications AS certifications_profil, 
        p.langues AS langues_profil, c.competences AS competences_cv, 
        c.skills AS skills_cv, c.loisirs AS loisirs_cv, c.filiere AS filiere_cv, 
        c.experience_pro AS experience_pro_cv, c.certifications AS certifications_cv, 
        c.langues AS langues_cv
 FROM candidats cand
 JOIN profils p ON cand.id_profil = p.id_profil
 JOIN cv_candidats c ON cand.id_candidat = c.id_candidat
 WHERE cand.id_candidat = (SELECT MAX(id_candidat) FROM candidats)";

    
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Vérifier si des résultats sont retournés
        if (empty($result)) {
            return 0;
        }
    
        $allInformation = [
            'competences' => ['cv' => [], 'profils' => []],
            'skills' => ['cv' => [], 'profils' => []],
            'loisirs' => ['cv' => [], 'profils' => []],
            'filiere' => ['cv' => [], 'profils' => []],
            'experience_pro' => ['cv' => [], 'profils' => []],
            'certifications' => ['cv' => [], 'profils' => []],
            'langues' => ['cv' => [], 'profils' => []]
        ];
    
        foreach ($result as $row) {
            foreach ($allInformation as $key => $value) {
                $allInformation[$key]['cv'][] = $row[$key . '_cv'];
                $allInformation[$key]['profils'][] = $row[$key . '_profil'];
            }
        }
    
        $totalScores = 0;
        $totalComparisons = 0;
    
        // Début du bloc de débogage et affichage
        // echo "Débogage getProfilCvComparison - Requête SQL: " . htmlspecialchars($sql) . "<br>";
        // echo "Débogage getProfilCvComparison - Données récupérées:<br>";
        // foreach ($result as $row) {
        //     foreach ($allInformation as $key => $value) {
        //         $profilValue = $row[$key . '_profil'] ?? '';
        //         $cvValue = $row[$key . '_cv'] ?? '';
        //         echo "Débogage getProfilCvComparison - $key (Profil): " . htmlspecialchars($profilValue) . ", (CV): " . htmlspecialchars($cvValue) . "<br>";
        //     }
        // }
    
        // echo "<h2>Détails des comparaisons Profil ↔ CV</h2>";
        // foreach ($allInformation as $key => $types) {
        //     $profilText = implode(' || ', $types['profils']);
        //     $cvText = implode(' || ', $types['cv']);
    
        //     echo "<h3>" . ucfirst($key) . "</h3>";
        //     echo "<strong>Profil :</strong> " . htmlspecialchars($profilText ?? '') . "<br>";
        //     echo "<strong>CV :</strong> " . htmlspecialchars($cvText ?? '') . "<br>";
    
        //     // Ignorer les comparaisons si les deux champs sont vides ou si le CV est trop court
        //     if (empty($profilText) && empty($cvText)) {
        //         echo "Débogage comparaison - Ignorée (champs vides).<hr>";
        //         continue;
        //     }
        //     if (strlen($cvText) < 2) {
        //         echo "Débogage comparaison - Ignorée (données CV insuffisantes).<hr>";
        //         continue;
        //     }
    
        //     try {
        //         $score = self::comparaison($profilText, $cvText, $threshold);
        //         echo "Débogage comparaison - Text1: " . htmlspecialchars($profilText ?? '') . "<br>";
        //         echo "Débogage comparaison - Text2: " . htmlspecialchars($cvText ?? '') . "<br>";
        //         echo "Débogage comparaison - Score utilisé pour la moyenne: " . number_format($score, 2) . "<hr>";
        //         $totalScores += $score;
        //         $totalComparisons++;
        //     } catch (Exception $e) {
        //         echo "Débogage comparaison - Erreur: " . htmlspecialchars($e->getMessage()) . "<hr>";
        //         continue;
        //     }
        // }
    
        // // Affichage final clair et précis
        // echo "<h2>Résultat final de la correspondance</h2>";
        // echo "➡ Pourcentage de correspondance : " . number_format($average * 100, 2) . "%<br>";
        // echo "➡ Score moyen (décimal) : " . number_format($average, 2) . "<br>";
    
        // // Vérification du diplôme et du seuil
        // echo "Débogage vérification finale - Validité du diplôme: " . (self::checkLatestCvDiplomeMatch() ? "✅ Diplôme conforme" : "❌ Diplôme non conforme") . "<br>";
        
        // Fin du bloc de débogage et affichage 

        // Calculer la moyenne des scores de similarité
        $average = ($totalComparisons > 0) ? ($totalScores / $totalComparisons) : 0;
        $sql = "INSERT INTO validation_cv (id_cv_candidat, id_status_validation_cv, similarite) VALUES (:id_cv_candidat, :statut, :similarite)";
        if (self::checkLatestCvDiplomeMatch()) {
            // echo "Débogage vérification finale - Comparaison moyenne/threshold: Moyenne = " . number_format($average, 2) . ", Seuil = " . number_format($threshold, 2) . "<br>";
        
            
            
            if (self::isAverigeAboveThreshold($average, $threshold)) {
                // echo "<h2>✅ Candidat accepté !</h2>";
                $stmt = Flight::db()->prepare($sql);
                $stmt->execute([
                    ':id_cv_candidat' => (int)(self::getLastCvCandidatId()),
                    ':statut' => 1, // Accepté
                    ':similarite' => $average
                ]);
            } else {
                // echo "<h2>❌ Candidat recalé (moyenne en dessous du seuil) !</h2>";
                $stmt = Flight::db()->prepare($sql);
                $stmt->execute([
                    ':id_cv_candidat' => (int)(self::getLastCvCandidatId()),
                    ':statut' => 2,
                    ':similarite' => $average
                ]);
            }
        
        } else {
            // echo "<h2>❌ Candidat recalé (diplôme non conforme) !</h2>";
                $stmt = Flight::db()->prepare($sql);
                $stmt->execute([
                    ':id_candidat' => (int)(self::getLastCandidatId()),
                    ':id_cv_candidat' => (int)(self::getLastCvCandidatId()),
                    ':statut' => 2 
                ]);
        }
    
        // Calculer la moyenne des scores de similarité
        $average = ($totalComparisons > 0) ? ($totalScores / $totalComparisons) : 0;
        // echo $average;
    
        // return $average;
    }
    public static function getAllCVs() {
        $db = Flight::db(); // Assure-toi que $db est défini
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
            JOIN profils pr ON c.id_profil = pr.id_profil
            JOIN cv_candidats cv ON c.id_candidat = cv.id_candidat
            JOIN validation_cv vc ON cv.id_cv_candidats = vc.id_cv_candidat
            JOIN status_validation_cv svc ON vc.id_status_validation_cv = svc.id_status_validation_cv
            ORDER BY cv.date_deposition DESC;
        ";
    
        try {
            $stmt = $db->prepare($query);
            $stmt->execute(); // ← Exécution de la requête
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // echo "Requete: ".$query;
            // echo "<br><br>";
            // echo "Data: ";
            // var_dump($data);
    
            return $data; // ← Retourner les données pour les utiliser ailleurs
    
        } catch (PDOException $e) {
            throw new Exception('Query failed: ' . $e->getMessage());
        }
    }
    
}