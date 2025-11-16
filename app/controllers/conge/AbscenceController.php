<?php

namespace app\controllers\conge;

use app\models;
use Flight;

class AbscenceController {

    /**
     * Affiche la liste des absences non autorisées pour justifier
     */
    public function getListeAbsencesAJustifier() {
        // Récupérer l'ID de l'employé connecté
        $idEmploye = $this->getIdEmployeConnecte();
        
        if (!$idEmploye) {
            Flight::redirect('/admin');
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
        $abscenceModel = Flight::Abscence();
        
        // Récupérer les données du formulaire
        $data = [
            'id_abscence' => $_POST['id_abscence'] ?? null,
            'justificatif' => $_FILES['justificatif'] ?? null,
            'commentaire' => $_POST['commentaire'] ?? null
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

        // Traitement du justificatif
        $nomFichier = null;
        if (!empty($data['justificatif']) && $data['justificatif']['error'] === UPLOAD_ERR_OK) {
            $nomFichier = $this->uploadJustificatif($data['justificatif']);
        }

        // Mise à jour de l'absence avec la justification
        $result = $abscenceModel->justifierAbsence($data['id_abscence'], $nomFichier, $data['commentaire']);
        
        Flight::json($result);
    }

    /**
     * Valide les données de justification
     */
    private function validateJustification($data) {
        if (empty($data['id_abscence'])) {
            return ['success' => false, 'error' => 'ID d\'absence manquant'];
        }

        if (empty($data['justificatif']) || $data['justificatif']['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Justificatif obligatoire'];
        }

        return ['success' => true];
    }

    /**
     * Upload le fichier justificatif (identique à celui des congés)
     */
    private function uploadJustificatif($file) {
        $dossierUpload = __DIR__ . '/../../uploads/justificatifs_absence/';
        
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
        } elseif (isset($_SESSION['utilisateur']['id_utilisateur'])) {
            // À adapter selon votre structure
            return $_SESSION['utilisateur']['id_utilisateur'];
        }
        return null;
    }
}