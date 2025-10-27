<?php

namespace app\models;

use Flight;
use PDO;
use PDOException;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CVModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public static function getAllCVCandidatDetail(){
        $db = Flight::db();
        $stmt = $db->query("SELECT
          cv.id_cv_candidats,
          cv.competences         AS cv_competences,
          cv.skills              AS cv_skills,
          cv.loisirs             AS cv_loisirs,
          cv.filiere             AS cv_filiere,
          cv.experience_pro      AS cv_experience_pro,
          cv.certifications      AS cv_certifications,
          cv.langues             AS cv_langues,
          cv.date_deposition     AS cv_date_deposition,
          cand.id_candidat,
          cand.poste             AS candidat_poste,
          cand.cv_url            AS candidat_cv_url,
          per.id_personne,
          per.nom                AS personne_nom,
          per.prenom             AS personne_prenom,
          per.date_naissance     AS personne_date_naissance,
          per.contact            AS personne_contact,
          per.lien_image         AS personne_lien_image,
          prof.id_profil,
          prof.titre             AS profil_titre,
          prof.competences       AS profil_competences,
          prof.skills            AS profil_skills,
          prof.loisirs           AS profil_loisirs,
          prof.id_diplome        AS profil_id_diplome,
          ann.id_annonce,
          ann.titre              AS annonce_titre,
          ann.lien               AS annonce_lien,
          ann.date_publication,
          ann.date_expiration,
          usr.id_utilisateur,
          usr.nom                AS utilisateur_nom,
          usr.date_inscription   AS utilisateur_date_inscription,
          dip.id_diplome,
          dip.nom                AS diplome_nom,
          dip.niveau             AS diplome_niveau
        FROM cv_candidats cv
        LEFT JOIN candidats     cand ON cv.id_candidat = cand.id_candidat
        LEFT JOIN personnes    per  ON cand.id_personne = per.id_personne
        LEFT JOIN profils      prof ON cand.id_profil = prof.id_profil
        LEFT JOIN diplomes     dip  ON cv.id_diplome = dip.id_diplome
        LEFT JOIN annonces     ann  ON cand.id_annonce = ann.id_annonce
        LEFT JOIN utilisateurs usr  ON cand.id_utilisateur = usr.id_utilisateur
        ORDER BY cv.date_deposition DESC;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Export Excel avec colonnes réordonnées et headers stylés.
     */
    public static function exportCVCandidatsExcel(){
        // protections mémoire / timeout
        if (!ini_get('memory_limit') || ini_get('memory_limit') < '256M') {
            ini_set('memory_limit', '512M');
        }
        set_time_limit(300);

        if (!class_exists(Spreadsheet::class)) {
            throw new Exception("PhpSpreadsheet introuvable. Assurez-vous d'avoir exécuté 'composer require phpoffice/phpspreadsheet' et que vendor/autoload.php est inclus dans public/index.php.");
        }

        try {
            $rows = self::getAllCVCandidatDetail();

            // nouvelle ordre de colonnes (groupe lisible)
            $desiredOrder = [
                // Personne (infos personnelles)
                'id_personne','personne_nom','personne_prenom','personne_date_naissance','personne_contact','personne_lien_image',
                // Candidat / dépôt
                'id_candidat','candidat_poste','candidat_cv_url','cv_date_deposition','id_cv_candidats',
                // Contenu CV
                'cv_competences','cv_skills','cv_loisirs','cv_filiere','cv_experience_pro','cv_certifications','cv_langues',
                // Profil lié
                'id_profil','profil_titre','profil_competences','profil_skills','profil_loisirs','profil_id_diplome',
                // Diplôme du CV
                'id_diplome','diplome_nom','diplome_niveau',
                // Annonce liée
                'id_annonce','annonce_titre','annonce_lien','date_publication','date_expiration',
                // Utilisateur
                'id_utilisateur','utilisateur_nom','utilisateur_date_inscription'
            ];

            // Si $rows vide, on prendra les en-têtes désirés, sinon on build à partir des clés réelles en respectant l'ordre souhaité
            if (empty($rows)) {
                $headers = $desiredOrder;
            } else {
                $actualKeys = array_keys($rows[0]);
                // Construire headers : d'abord les colonnes dans desiredOrder présentes dans actualKeys, puis les restants dans actualKeys
                $headers = [];
                foreach ($desiredOrder as $k) {
                    if (in_array($k, $actualKeys, true)) {
                        $headers[] = $k;
                    }
                }
                // ajouter les clés restantes qui ne figuraient pas dans desiredOrder (sécurité)
                foreach ($actualKeys as $k) {
                    if (!in_array($k, $headers, true)) {
                        $headers[] = $k;
                    }
                }
            }

            // mapping pour labels plus lisibles (français)
            $labels = [
                'id_personne' => 'ID Personne',
                'personne_nom' => 'Nom',
                'personne_prenom' => 'Prénom',
                'personne_date_naissance' => 'Date de naissance',
                'personne_contact' => 'Contact',
                'personne_lien_image' => 'Photo URL',
                'id_candidat' => 'ID Candidat',
                'candidat_poste' => 'Poste candidat',
                'candidat_cv_url' => 'CV URL (candidat)',
                'cv_date_deposition' => 'Date dépôt CV',
                'id_cv_candidats' => 'ID CV',
                'cv_competences' => 'Compétences (CV)',
                'cv_skills' => 'Skills (CV)',
                'cv_loisirs' => 'Loisirs',
                'cv_filiere' => 'Filière (CV)',
                'cv_experience_pro' => 'Expérience pro (CV)',
                'cv_certifications' => 'Certifications (CV)',
                'cv_langues' => 'Langues (CV)',
                'id_profil' => 'ID Profil',
                'profil_titre' => 'Titre Profil',
                'profil_competences' => 'Compétences (Profil)',
                'profil_skills' => 'Skills (Profil)',
                'profil_loisirs' => 'Loisirs (Profil)',
                'profil_id_diplome' => 'ID Diplôme (Profil)',
                'id_diplome' => 'ID Diplôme (CV)',
                'diplome_nom' => 'Diplôme',
                'diplome_niveau' => 'Niveau diplôme',
                'id_annonce' => 'ID Annonce',
                'annonce_titre' => 'Titre annonce',
                'annonce_lien' => 'Lien annonce',
                'date_publication' => 'Date publication annonce',
                'date_expiration' => 'Date expiration annonce',
                'id_utilisateur' => 'ID Utilisateur',
                'utilisateur_nom' => 'Utilisateur',
                'utilisateur_date_inscription' => 'Date inscription utilisateur'
            ];

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('CV Candidats');

            // écrire les en-têtes (MAJUSCULES, gras, highlight)
            $colIndex = 1;
            foreach ($headers as $h) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                // label francisé si présent, sinon clé formatée
                $rawLabel = $labels[$h] ?? ucwords(str_replace('_', ' ', $h));
                $label = mb_strtoupper($rawLabel, 'UTF-8'); // tout en majuscules
                $sheet->setCellValue($colLetter . '1', $label);
                $colIndex++;
            }

            // Style des headers : on applique au range A1:LAST1
            $highestColumnIndex = count($headers);
            $lastColLetter = Coordinate::stringFromColumnIndex($highestColumnIndex);
            $headerRange = 'A1:' . $lastColLetter . '1';

            // Fond et police
            $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FFFFFFFF'); // texte blanc
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1F4E78'); // couleur bleu foncé (ARGB)
            // Bordures fines
            $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->getColor()->setARGB('FFCCCCCC');
            // Alignement centré et wrap pour en-têtes longues
            $sheet->getStyle($headerRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            // hauteur de la ligne d'en-tête
            $sheet->getRowDimension(1)->setRowHeight(28);

            // écrire les données (à partir de la ligne 2)
            $rowIndex = 2;
            foreach ($rows as $r) {
                $colIndex = 1;
                foreach ($headers as $h) {
                    $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                    $cell = $colLetter . $rowIndex;
                    $val = $r[$h] ?? null;

                    if ($val !== null && preg_match('/^\d{4}-\d{2}-\d{2}(?:[ T]\d{2}:\d{2}:\d{2})?$/', (string)$val)) {
                        $sheet->setCellValueExplicit($cell, (string)$val, DataType::TYPE_STRING);
                    } else {
                        $sheet->setCellValue($cell, $val);
                    }
                    $colIndex++;
                }
                $rowIndex++;
            }

            // ajuster largeur des colonnes et activer le retour à la ligne pour colonnes larges
            for ($i = 1; $i <= $highestColumnIndex; $i++) {
                $columnLetter = Coordinate::stringFromColumnIndex($i);
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                // par sécurité : wrap pour colonnes texte longues
                $sheet->getStyle($columnLetter)->getAlignment()->setWrapText(true);
            }

            // figer la première ligne pour faciliter la lecture
            $sheet->freezePane('A2');

            // Préparer le téléchargement
            $filename = 'cv_candidats_' . date('Ymd_His') . '.xlsx';
            while (ob_get_level() > 0) { ob_end_clean(); }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: max-age=0');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Pragma: public');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Throwable $e) {
            throw new Exception('Erreur lors de l\'export Excel : ' . $e->getMessage());
        }
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
