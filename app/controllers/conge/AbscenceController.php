<?php

namespace app\controllers\conge;
use app\models;
use Flight;


class AbscenceController {

    /**
     * Affiche la liste complète des absences (tous les employés) - ADMIN SEULEMENT
     */
    public function getListeAbsence($estAutorise = null) {
        // Vérifier les droits d'administration ou si c'est un employé consultant ses propres absences
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::redirect('/employe');
            return;
        }

        if ($estAutorise !== null) {
            $estAutorise = filter_var($estAutorise, FILTER_VALIDATE_BOOLEAN);
        }

        $abscenceModel = Flight::Abscence();
        
        // Si c'est un employé (pas admin), on ne montre que ses propres absences
        if ($this->estEmploye() && !$this->estAdministrateur()) {
            $idEmploye = $this->getIdEmployeConnecte();
            $tableau = $abscenceModel->getTableauDetailAbsence($idEmploye, $estAutorise);
        } else {
            // Admin voit toutes les absences
            $tableau = $abscenceModel->getTableauListeAbsence($estAutorise);
        }
        
        Flight::render('conge/absences_admin', [
            'tableau' => $tableau,
            'estEmploye' => $this->estEmploye() && !$this->estAdministrateur()
        ]);
    }

    /**
     * Affiche le justificatif d'une absence
     */
    public function getJustificatif($idAbsence) {
        $abscenceModel = Flight::Abscence();
        $justificatif = $abscenceModel->getJustificatifPath($idAbsence);
        
        if (!$justificatif) {
            Flight::halt(404, 'Justificatif non trouvé');
            return;
        }

        // Vérifier que l'utilisateur a le droit de voir ce justificatif
        if (!$this->peutVoirJustificatif($idAbsence)) {
            Flight::halt(403, 'Accès non autorisé à ce justificatif');
            return;
        }

        $cheminFichier = __DIR__ . '/../../../public/uploads/justificatifs_absence/' . $justificatif;
        
        if (!file_exists($cheminFichier)) {
            Flight::halt(404, 'Fichier justificatif introuvable');
            return;
        }

        // Déterminer le type MIME
        $typeMime = mime_content_type($cheminFichier);
        header('Content-Type: ' . $typeMime);
        
        // Pour les images et PDF, afficher dans le navigateur
        if (in_array($typeMime, ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'])) {
            header('Content-Disposition: inline; filename="' . $justificatif . '"');
        } else {
            // Pour les autres types, forcer le téléchargement
            header('Content-Disposition: attachment; filename="' . $justificatif . '"');
        }
        
        readfile($cheminFichier);
        exit;
    }

    /**
     * Vérifie si l'utilisateur peut voir le justificatif
     */
    private function peutVoirJustificatif($idAbsence) {
        // Les admins peuvent tout voir
        if ($this->estAdministrateur()) {
            return true;
        }
        
        // Les employés ne peuvent voir que leurs propres justificatifs
        if ($this->estEmploye()) {
            $idEmploye = $this->getIdEmployeConnecte();
            $abscenceModel = Flight::Abscence();
            $absence = $abscenceModel->getAbsenceById($idAbsence, $idEmploye);
            return !empty($absence);
        }
        
        return false;
    }

    /**
     * Envoie une notification à un employé pour justifier son absence - ADMIN SEULEMENT
     */
    public function notifierEmploye() {
        // Vérifier les droits d'administration
        if (!$this->estAdministrateur()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $data = [
            'id_employe' => $_POST['id_employe'] ?? null,
            'id_abscence' => $_POST['id_abscence'] ?? null,
            'message' => $_POST['message'] ?? 'Veuillez justifier votre absence dans les plus brefs délais.'
        ];

        // Validation
        if (empty($data['id_employe']) || empty($data['id_abscence'])) {
            Flight::json([
                'success' => false,
                'error' => 'Données manquantes'
            ], 400);
            return;
        }

        try {
            // Récupérer les informations de l'employé
            $employeModel = Flight::Employe();
            $employe = $employeModel->findByIdWithDetails($data['id_employe']);
            
            if (!$employe) {
                Flight::json([
                    'success' => false,
                    'error' => 'Employé non trouvé'
                ], 404);
                return;
            }

            // Ici, vous pouvez implémenter l'envoi d'email
            $this->envoyerNotification($employe['email'], 'Justification d\'absence requise', $data['message']);

            // Loguer l'action
            error_log("Notification envoyée à l'employé " . $employe['nom'] . " pour l'absence " . $data['id_abscence']);

            Flight::json([
                'success' => true,
                'message' => 'Notification envoyée avec succès à ' . $employe['prenom'] . ' ' . $employe['nom']
            ]);

        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur lors de l\'envoi de la notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    private function estAdministrateur() {
        return isset($_SESSION['infoAdmin']['id_employe']);
    }

    /**
     * Vérifie si l'utilisateur est un employé connecté
     */
    private function estEmploye() {
        return isset($_SESSION['employe']['id_employe']);
    }

    /**
     * Envoie une notification (à adapter selon votre système)
     */
    private function envoyerNotification($email, $sujet, $message) {
        // Implémentez ici l'envoi d'email ou de notification
        // Exemple basique avec mail() - à adapter
        /*
        $headers = "From: noreply@votreentreprise.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        return mail($email, $sujet, $message, $headers);
        */
        
        // Pour l'instant, on log juste l'action
        error_log("Notification à envoyer: $email - $sujet - $message");
        return true;
    }

    /**
     * Affiche la liste des absences non autorisées pour justifier
     */
    public function getListeAbsencesAJustifier() {
        // Récupérer l'ID de l'employé connecté
        $idEmploye = $this->getIdEmployeConnecte();
        
        if (!$idEmploye) {
            Flight::redirect('/employe');
            return;
        }

        $abscenceModel = Flight::Abscence();
        $employeModel = Flight::Employe();
        
        // Récupérer les absences non autorisées
        $absencesNonAutorisees = $abscenceModel->getDetailAbsence($idEmploye, false);
        
        // Récupérer les informations de l'employé
        $employe = $employeModel->findByIdWithDetails($idEmploye);
        
        Flight::render('conge/liste_absence', [
            'absences' => $absencesNonAutorisees,
            'employe' => $employe
        ]);
    }

    /**
     * Affiche le formulaire de justification pour une absence spécifique
     */
    public function getJustifierAbsence($idAbsence) {
        // Récupérer l'ID de l'employé connecté
        $idEmploye = $this->getIdEmployeConnecte();
        
        if (!$idEmploye) {
            Flight::json([
                'success' => false,
                'error' => 'Non authentifié'
            ], 401);
            return;
        }

        $abscenceModel = Flight::Abscence();
        
        // Récupérer les détails de l'absence
        $absence = $abscenceModel->getAbsenceById($idAbsence, $idEmploye);
        
        if (!$absence) {
            Flight::json([
                'success' => false,
                'error' => 'Absence non trouvée'
            ], 404);
            return;
        }

        // Afficher le formulaire de justification
        Flight::render('conge/justification_absence', [
            'absence' => $absence
        ]);
    }

    /**
     * Traite la soumission du formulaire de justification
     */
    public function submitJustification() {
        // Récupérer l'ID de l'employé connecté
        $idEmploye = $this->getIdEmployeConnecte();
        
        if (!$idEmploye) {
            Flight::json([
                'success' => false,
                'error' => 'Non authentifié'
            ], 401);
            return;
        }

        $abscenceModel = Flight::Abscence();
        
        // Récupérer les données du formulaire
        $data = [
            'id_abscence' => $_POST['id_abscence'] ?? null,
            'justificatif_texte' => $_POST['justificatif_texte'] ?? null,
            'justificatif_fichier' => $_FILES['justificatif_fichier'] ?? null,
            'type_justificatif' => $_POST['type_justificatif'] ?? 'auto'
        ];

        // Validation des données
        $validation = $this->validateJustification($data);
        if (!$validation['success']) {
            Flight::json([
                'success' => false,
                'error' => $validation['error']
            ], 400);
            return;
        }

        // Vérifier que l'absence appartient bien à l'employé
        $absence = $abscenceModel->getAbsenceById($data['id_abscence'], $idEmploye);
        if (!$absence) {
            Flight::json([
                'success' => false,
                'error' => 'Absence non trouvée ou accès non autorisé'
            ], 404);
            return;
        }

        try {
            // Traitement de la justification
            $result = $abscenceModel->justifierAbsence($data['id_abscence'], $data);
            
            if ($result['success']) {
                // Journaliser l'action
                error_log("Absence {$data['id_abscence']} justifiée par l'employé $idEmploye - Type: " . ($result['type'] ?? 'inconnu'));
            }
            
            Flight::json($result);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur lors du traitement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valide les données de justification
     */
    private function validateJustification($data) {
        if (empty($data['id_abscence'])) {
            return ['success' => false, 'error' => 'ID d\'absence manquant'];
        }

        // Vérifier qu'au moins un justificatif est fourni
        $hasTexte = !empty(trim($data['justificatif_texte'] ?? ''));
        $hasFichier = !empty($data['justificatif_fichier']['name']) && $data['justificatif_fichier']['error'] === UPLOAD_ERR_OK;
        
        if (!$hasTexte && !$hasFichier) {
            return ['success' => false, 'error' => 'Veuillez fournir un justificatif (texte ou fichier)'];
        }

        // Validation du fichier si fourni
        if ($hasFichier) {
            $file = $data['justificatif_fichier'];
            
            // Vérifier la taille (5MB max)
            if ($file['size'] > 5 * 1024 * 1024) {
                return ['success' => false, 'error' => 'Le fichier est trop volumineux (max 5MB)'];
            }

            // Vérifier le type
            $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'txt'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($extension, $allowedTypes)) {
                return ['success' => false, 'error' => 'Type de fichier non autorisé. Types acceptés: ' . implode(', ', $allowedTypes)];
            }
        }

        return ['success' => true];
    }

    /**
     * Upload le fichier justificatif
     */
    private function uploadJustificatif($file) {
        $dossierUpload = __DIR__ . '/../../../public/uploads/justificatifs_absence/';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($dossierUpload)) {
            mkdir($dossierUpload, 0755, true);
        }

        // Validation du type de fichier
        $typesAutorises = ['pdf', 'jpg', 'jpeg', 'png'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $typesAutorises)) {
            throw new \Exception('Type de fichier non autorisé');
        }

        // Validation de la taille (2MB max)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new \Exception('Le fichier est trop volumineux (max 2MB)');
        }

        // Générer un nom unique
        $nomFichier = uniqid() . '_' . time() . '.' . $extension;
        $cheminComplet = $dossierUpload . $nomFichier;

        if (!move_uploaded_file($file['tmp_name'], $cheminComplet)) {
            throw new \Exception('Erreur lors de l\'upload du fichier');
        }

        return $nomFichier;
    }

    /**
     * Récupère l'ID de l'employé connecté
     */
    private function getIdEmployeConnecte() {
        if (isset($_SESSION['infoAdmin']['id_employe'])) {
            return $_SESSION['infoAdmin']['id_employe'];
        } elseif (isset($_SESSION['employe']['id_employe'])) {
            return $_SESSION['employe']['id_employe'];
        } elseif (isset($_SESSION['utilisateur']['id_utilisateur'])) {
            // À adapter selon votre structure si les utilisateurs sont liés aux employés
            return $_SESSION['utilisateur']['id_utilisateur'];
        }
        return null;
    }
}