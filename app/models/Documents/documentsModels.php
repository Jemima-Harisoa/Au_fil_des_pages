<?php

namespace app\models\Documents;

use Flight;
use PDO;
use PDOException;
use Exception;

class documentsModels
{
    public function __construct() {}

    public static function creerDossierCompletEmploye($id_employe): array
    {
        // Chemin absolu depuis la racine du serveur
        $basePath = $_SERVER['DOCUMENT_ROOT'] . '/Documents';
        
        // OU en utilisant une constante définie dans votre bootstrap
        // $basePath = PUBLIC_PATH . '/Documents';
        

        // === 2. Création du répertoire de base s'il n'existe pas ===
        if (!is_dir($basePath)) {
            if (!mkdir($basePath, 0755, true) && !is_dir($basePath)) {
                return ['success' => false, 'message' => 'Impossible de créer le répertoire de base', 'path' => null];
            }
        }

        if (!is_writable($basePath)) {
            return ['success' => false, 'message' => 'Le répertoire de base n\'est pas accessible en écriture', 'path' => null];
        }

        // === 3. Récupération des infos de l'employé ===
        try {
            $db = Flight::db(); // Adapte selon ton framework (ou $pdo)

            $sql = "
            SELECT p.nom, p.prenom, e.id_employe
            FROM employes e
            JOIN personnes p ON e.id_personne = p.id_personne
            WHERE e.id_employe = :id
            LIMIT 1
        ";

            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $id_employe]);
            $emp = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$emp) {
                return ['success' => false, 'message' => "Employé ID $id_employe non trouvé", 'path' => null];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage(), 'path' => null];
        }

        // === 4. Nom du dossier : NOM_Prenom_ID ===
        $nomDossier = trim($emp['nom']) . '_' . trim($emp['prenom']) . '_' . $emp['id_employe'];
        $nomDossier = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $nomDossier); // Sécurité
        $cheminDossier = rtrim($basePath, '/') . '/' . $nomDossier;

        // === 5. Création du dossier principal ===
        if (is_dir($cheminDossier)) {
            return [
                'success' => true,
                'message' => 'Le dossier existe déjà',
                'path'    => $cheminDossier,
                'existe'  => true
            ];
        }

        if (!mkdir($cheminDossier, 0755, true)) {
            return ['success' => false, 'message' => 'Échec création du dossier principal', 'path' => $cheminDossier];
        }

        // === 6. Sous-dossiers RH standards ===
        $sousDossiers = [
            '01 - Documents administratifs',
            '02 - Contrats et avenants',
            '03 - Bulletins de paie',
            '04 - Congés et absences',
            '05 - Formations',
            '06 - Évaluations',
            '07 - Divers'
        ];

        foreach ($sousDossiers as $sub) {
            $subPath = $cheminDossier . '/' . $sub;
            if (!is_dir($subPath)) {
                mkdir($subPath, 0755, true);
            }
        }

        // === 7. Fichier index.html vide pour bloquer l'exploration web (sécurité) ===
        file_put_contents($cheminDossier . '/index.html', '<!DOCTYPE html><title></title>');

        return [
            'success' => true,
            'message' => 'Dossier + sous-dossiers créés avec succès !',
            'path'    => $cheminDossier,
            'existe'  => false
        ];
    }
}
