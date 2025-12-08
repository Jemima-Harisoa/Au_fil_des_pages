<?php

namespace app\controllers\conge;

use app\models;
use Flight;

class CongeController {

    /**
     * Calcule le taux de validation global pour les statistiques
     */
    private function calculerTauxValidation($demandesEnAttente) {
        if (empty($demandesEnAttente)) {
            return 100;
        }
        
        $totalValidationsRequises = 0;
        $totalValidationsObtenues = 0;
        
        foreach ($demandesEnAttente as $demande) {
            $totalValidationsRequises += $demande['niveau_validation'];
            $totalValidationsObtenues += $demande['validations_obtenues'];
        }
        
        if ($totalValidationsRequises > 0) {
            return round(($totalValidationsObtenues / $totalValidationsRequises) * 100);
        }
        
        return 0;
    }

    /**
     * Affiche l'interface de validation des congés - ADMIN SEULEMENT
     */
    public function getInterfaceValidation() {
        // Vérifier les droits d'administration
        if (!$this->estAdministrateur()) {
            Flight::redirect('/connexion-employe');
            return;
        }

        $congeModel = Flight::Conge();
        
        // Récupérer l'ID de l'employé connecté
        $idEmployeConnecte = $this->getIdEmployeConnecte();
        
        // Récupérer les demandes en attente de validation
        $demandesEnAttente = $congeModel->getDemandesEnAttente();
        
        // Calculer le taux de validation global UNE SEULE FOIS
        $tauxValidationGlobal = $this->calculerTauxValidation($demandesEnAttente);
        
        // Calculer les jours ouvrables et le pourcentage pour chaque demande
        foreach ($demandesEnAttente as &$demande) {
            $demande['jours_ouvrables'] = $congeModel->calculerJoursOuvrables($demande['id_demande']);
            
            // Calculer le pourcentage de validation pour cette demande
            $demande['pourcentage_validation'] = $demande['niveau_validation'] > 0 
                ? round(($demande['validations_obtenues'] / $demande['niveau_validation']) * 100, 2)
                : 0;
            
            // Vérifier si l'employé connecté a déjà validé cette demande
            $demande['deja_valide'] = $congeModel->aDejaValide($demande['id_demande'], $idEmployeConnecte);
        }
        
        Flight::render('conge/liste_validation', [
            'demandes_en_attente' => $demandesEnAttente,
            'taux_validation_global' => $tauxValidationGlobal
        ]);
    }

    /**
     * Valide une demande de congé - ADMIN SEULEMENT
     */
    public function postValidation($idDemande) {
        try {
            // Vérifier les droits d'administration
            if (!$this->estAdministrateur()) {
                Flight::json([
                    'success' => false,
                    'error' => 'Accès non autorisé. Droits administrateur requis.'
                ], 403);
                return;
            }

            $congeModel = Flight::Conge();
            
            // Récupérer l'ID de l'employé validateur depuis la session
            $idValidateur = $this->getIdEmployeConnecte();
            
            $demande = $congeModel->getDemandeConge($idDemande);
            
            if($demande['id_employe'] == $idValidateur){
                Flight::json([
                    'success' => false,
                    'error' => 'Vous ne pouvez pas valider votre propre demande'
                ]);
                return;
            }

            if (!$idValidateur) {
                throw new \Exception("Employé non connecté");
            }
            
            // Vérifier si l'employé a déjà validé cette demande
            if ($congeModel->aDejaValide($idDemande, $idValidateur)) {
                Flight::json([
                    'success' => false,
                    'error' => 'Vous avez déjà validé cette demande'
                ], 403);
                return;
            }
            
            // Ajouter la validation
            $validationAjoutee = $congeModel->ajouterValidation($idDemande, $idValidateur);
            
            if (!$validationAjoutee) {
                throw new \Exception("Erreur lors de l'ajout de la validation");
            }
            
            // Vérifier si la demande est maintenant complètement validée
            if ($congeModel->estDemandeValidee($idDemande)) {
                // Traiter la validation complète
                $traitementReussi = $congeModel->traiterValidationComplete($idDemande);
                
                if ($traitementReussi) {
                    Flight::json([
                        'success' => true,
                        'message' => 'Demande validée et traitement appliqué avec succès',
                        'validation_complete' => true
                    ]);
                } else {
                    Flight::json([
                        'success' => false,
                        'error' => 'Erreur lors du traitement de la validation complète'
                    ], 500);
                }
            } else {
                Flight::json([
                    'success' => true,
                    'message' => 'Validation ajoutée avec succès',
                    'validation_complete' => false
                ]);
            }
            
        } catch (\Exception $e) {
            error_log("Erreur validation congé: " . $e->getMessage());
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcule une estimation de déduction salariale
     */
    public function getEstimationDeduction() {
        try {
            $data = Flight::request()->query->getData();
            
            $salaireBase = $data['salaire_base'] ?? 0;
            $nombreJours = $data['nombre_jours'] ?? 0;
            $idTypeConge = $data['id_type_conge'] ?? null;
            
            $congeModel = Flight::Conge();
            
            // Vérifier si le type de congé est deductible sur salaire
            $deductibleSalaire = true; // Par défaut
            if ($idTypeConge) {
                $deductibleSalaire = $congeModel->estDeductibleSalaire($idTypeConge);
            }
            
            $deduction = 0;
            if ($deductibleSalaire && $salaireBase > 0 && $nombreJours > 0) {
                $deduction = $congeModel->calculerDeductionSalaire($salaireBase, $nombreJours);
            }
            
            Flight::json([
                'success' => true,
                'deductible_salaire' => $deductibleSalaire,
                'deduction_estimee' => $deduction,
                'nombre_jours' => $nombreJours
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère l'ID de l'employé connecté
     */
    private function getIdEmployeConnecte() {
        if (isset($_SESSION['infoAdmin']['id_employe'])) {
            return $_SESSION['infoAdmin']['id_employe'];
        } elseif (isset($_SESSION['employe']['id_employe'])) {
            return $_SESSION['employe']['id_employe'];
        }
        return null;
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
     * Affiche le formulaire de demande de congé
     */
    public function getDemandeConge() {
        // Vérifier que l'utilisateur est connecté (admin ou employé)
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::redirect('/connexion-employe');
            return;
        }

        $congeModel = Flight::Conge();
        $types_conge = $congeModel->getTypesConge();
        
        $employeModel = Flight::Employe();
        
        // Déterminer l'ID de l'employé selon le type de session
        $idEmploye = $this->getIdEmployeConnecte();
        
        if (!$idEmploye) {
            Flight::redirect('/connexion-employe');
            return;
        }
        
        // Récupérer les informations détaillées de l'employé
        $employe = $employeModel->findByIdWithDetails($idEmploye);
        
        // Récupérer les statistiques de congés
        $statistiquesConges = $congeModel->getDonneesCongesParType($idEmploye);
        
        // Calculer le nombre total de congés restants basé sur conge_type
        $nombre_conge_restant = 0;
        foreach ($statistiquesConges as $stat) {
            $nombre_conge_restant += max(0, $stat['jours_totaux'] - $stat['jours_pris']);
        }
        
        // Récupérer le nombre de demandes cette année
        $demandes_annee = $congeModel->getNombreDemandesAnnee($idEmploye);
        
        // Calculer le taux d'approbation
        $taux_approbation = $congeModel->getTauxApprobation($idEmploye);
        
        Flight::render('conge/demande_conge', [
            'types_conge' => $types_conge,
            'employe' => $employe,
            'nombre_conge' => $nombre_conge_restant,
            'demandes_annee' => $demandes_annee,
            'taux_approbation' => $taux_approbation,
            'statistiques' => $statistiquesConges,
            'estEmploye' => $this->estEmploye() && !$this->estAdministrateur()
        ]);
    }

    /**
     * Traite la soumission du formulaire de demande de congé
     */
    public function submitDemande() {
        // Vérifier que l'utilisateur est connecté (admin ou employé)
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::json([
                'success' => false,
                'error' => 'Non authentifié'
            ], 401);
            return;
        }

        $congeModel = Flight::Conge();
        $employeModel = Flight::Employe();
        
        // Récupérer l'ID de l'employé connecté
        $idEmployeConnecte = $this->getIdEmployeConnecte();
        
        // Récupérer les données du formulaire
        $data = [
            'id_employe' => $idEmployeConnecte, // Utiliser l'ID de l'employé connecté
            'id_type_conge' => $_POST['id_type_conge'] ?? null,
            'date_debut' => $_POST['date_debut'] ?? null,
            'date_fin' => $_POST['date_fin'] ?? null,
            'description' => $_POST['description'] ?? null
        ];

        // Validation des données
        $validation = $this->validateDemande($data);
        if (!$validation['success']) {
            error_log("Validation échouée: " . $validation['error']);
            Flight::json([
                'success' => false,
                'error' => $validation['error']
            ], 400);
            return;
        }

        // Traitement du justificatif si présent
        $nomFichier = null;
        if (!empty($_FILES['justificatif']) && $_FILES['justificatif']['error'] === UPLOAD_ERR_OK) {
            $nomFichier = $this->uploadJustificatif($_FILES['justificatif']);
        }

        // Enregistrement de la demande
        $result = $congeModel->creerDemandeConge($data, $nomFichier);
        
        // Rendu vers la page demande_conge avec le résultat
        Flight::render('conge/demande_conge', [
            'resultat_demande' => $result,
            'employe' => $employeModel->findByIdWithDetails($idEmployeConnecte),
            'types_conge' => $congeModel->getTypesConge(),
            'nombre_conge' => $congeModel->getSoldeConge($idEmployeConnecte),
            'demandes_annee' => $congeModel->getNombreDemandesAnnee($idEmployeConnecte),
            'taux_approbation' => $congeModel->getTauxApprobation($idEmployeConnecte),
            'estEmploye' => $this->estEmploye() && !$this->estAdministrateur()
        ]);
    }

    /**
     * Valide les données de la demande
     */
    private function validateDemande($data) {
        // Vérification des champs obligatoires
        $required = ['id_employe', 'id_type_conge', 'date_debut', 'date_fin', 'description'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'error' => "Le champ $field est obligatoire"];
            }
        }

        // Validation des dates
        $dateDebut = new \DateTime($data['date_debut']);
        $dateFin = new \DateTime($data['date_fin']);
        $aujourdhui = new \DateTime();
        $deuxSemaines = (new \DateTime())->modify('+14 days');

        if ($dateDebut < $deuxSemaines) {
            return ['success' => false, 'error' => 'La date de début doit être au moins 2 semaines après aujourd\'hui'];
        }

        if ($dateFin < $dateDebut) {
            return ['success' => false, 'error' => 'La date de fin doit être après la date de début'];
        }

        return ['success' => true];
    }

    /**
     * Upload le fichier justificatif
     */
    private function uploadJustificatif($file) {
        $dossierUpload = __DIR__ . '/../../../public/uploads/justificatifs/';
        
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
     * Récupère les détails complets des congés pour un employé
     */
    public function getDetailConges($idEmploye, $idType = null) {
        // Vérifier les droits d'accès
        if (!$this->peutVoirFiche($idEmploye)) {
            Flight::redirect('/connexion-employe');
            return;
        }

        $ficheDetail = $this->parametreInfoEmploye($idEmploye);
        $congeModel = Flight::Conge();
        
        // Récupération des détails de congés
        if ($idType !== null) {
            $ficheDetail['detailConge'] = $congeModel->getTableauDetailCongeParType($idEmploye, $idType);
        } else {
            $ficheDetail['detailConge'] = $congeModel->getTableauDetailConge($idEmploye);
        }
        
        // Passage des données à la vue
        Flight::render('conge/fiche_employe', $ficheDetail);
    }

    /**
     * Récupère les détails complets des absences pour un employé
     */
    public function getDetailAbsences($idEmploye, $estAutorise = null) {
        // Vérifier les droits d'accès
        if (!$this->peutVoirFiche($idEmploye)) {
            Flight::redirect('/connexion-employe');
            return;
        }

        $ficheDetail = $this->parametreInfoEmploye($idEmploye);
        $abscenceModel = Flight::Abscence();
        // Récupération des détails d'absences
        $ficheDetail['detailAbsence']  = $abscenceModel->getTableauDetailAbsence($idEmploye, $estAutorise);
        
        // Passage des données à la vue
        Flight::render('conge/fiche_employe', $ficheDetail);
    }

    /**
     * Affiche la fiche complète d'un employé avec ses congés et absences
     */
    public function getFicheEmploye($idEmploye) {
        // Vérifier les droits d'accès
        if (!$this->peutVoirFiche($idEmploye)) {
            Flight::redirect('/employe');
            return;
        }

        // Passage des données à la vue
        Flight::render('conge/fiche_employe', $this->parametreInfoEmploye($idEmploye));
    }

    /**
     * Vérifie si l'utilisateur peut voir la fiche de l'employé
     */
    private function peutVoirFiche($idEmploye) {
        // Les admins peuvent tout voir
        if ($this->estAdministrateur()) {
            return true;
        }
        
        // Les employés ne peuvent voir que leur propre fiche
        if ($this->estEmploye()) {
            $idEmployeConnecte = $this->getIdEmployeConnecte();
            return $idEmployeConnecte == $idEmploye;
        }
        
        return false;
    }

    /**
     * Paramètre les info de la personne et les options (abscence, conge, compétences, etc)
     */
    private function parametreInfoEmploye($idEmploye) {
        $employeModel = Flight::Employe();
        $abscenceModel = Flight::Abscence();
        $congeModel = Flight::Conge();
        $competenceModel = Flight::Competence();
        
        // Récupération des données
        $fiche = $employeModel->getFicheEmploye($idEmploye);
        $absences = $abscenceModel->getSectionAbsences($idEmploye);
        $listeconge = $congeModel->getNombreConge($idEmploye);
        $employe = $employeModel->findByIdWithDetails($idEmploye);
        
        // Nouvelles données de compétences
        $competences = $competenceModel->getCompetencesByEmployee($idEmploye);
        $statsCompetences = $this->getStatsCompetencesEmploye($competences);
        $suggestionsFormations = $competenceModel->suggestFormationsForEmployee($idEmploye);

        return [
            'fiche' => $fiche,
            'absences' => $absences,
            'listeconge' => $listeconge, 
            'nombre_conge' => $employe['nombre_conge'],
            'competences' => $competences,
            'stats_competences' => $statsCompetences,
            'suggestions_formations' => $suggestionsFormations,
            'estEmploye' => $this->estEmploye() && !$this->estAdministrateur()
        ];
    }

    /**
     * Calcule les statistiques des compétences d'un employé
     * @param array $competences Liste des compétences
     * @return array Statistiques
     */
    private function getStatsCompetencesEmploye($competences)
    {
        $total = count($competences);
        $validees = 0;
        $niveauMoyen = 0;
        $parNiveau = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        
        foreach ($competences as $competence) {
            if ($competence['valide']) {
                $validees++;
            }
            $niveauMoyen += $competence['niveau'];
            $parNiveau[$competence['niveau']]++;
        }
        
        $niveauMoyen = $total > 0 ? $niveauMoyen / $total : 0;
        
        return [
            'total' => $total,
            'validees' => $validees,
            'niveau_moyen' => round($niveauMoyen, 2),
            'pourcentage_validees' => $total > 0 ? round(($validees / $total) * 100, 1) : 0,
            'par_niveau' => $parNiveau
        ];
    }
    /**
     * Affiche la liste des employés avec leurs informations de congés et absences - ADMIN SEULEMENT
     */
    public function getListeEmployes() {
        // Vérifier les droits d'administration
        if (!$this->estAdministrateur()) {
            Flight::redirect('/employe');
            return;
        }

        // Récupération des modèles
        $employeModel = Flight::Employe();
        $congeModel = Flight::Conge();
        $abscenceModel = Flight::Abscence();
        
        // Récupération de tous les employés avec leurs détails
        $employes = $employeModel->listWithDetails();
        
        // Préparation des données pour chaque employé
        $listeEmployes = [];
        
        foreach ($employes as $employe) {
            // Récupération des congés restants
            $congeData = $employeModel->findById($employe['id_employe']);
            $congesRestants = $congeData['nombre_conge'] ?? 0;
            
            // Récupération des absences (à adapter selon votre modèle d'absences)
            $absencesData = $abscenceModel->getAbsencesCumulees($employe['id_employe']);
            
            // Récupération du CV depuis le modèle candidat
            $candidatModel = Flight::candidatModel();
            $candidat = $candidatModel->getBy('id_personne', $employe['id_personne']);
            $cvUrl = $candidat['cv_url'] ?? 'Non disponible';
            
            $listeEmployes[] = [
                'id_employe' => $employe['id_employe'],
                'nom' => $employe['nom_personne'],
                'prenom' => $employe['prenom'],
                'poste' => $employe['poste'],
                'cv_url' => $cvUrl,
                'conges_restants' => $congesRestants,
                'absences_cumule' => $absencesData,
            ];
        }
        // Passage des données à la vue
        Flight::render('conge/liste_employes', [
            'employes' => $listeEmployes
        ]);
    }
    
    /**
     * Récupère seulement les statistiques de congés (pour API)
     */
    public function getStatistiques($idEmploye) {
        try {
            // Vérifier les droits d'accès
            if (!$this->peutVoirFiche($idEmploye)) {
                Flight::json([
                    'success' => false,
                    'error' => 'Accès non autorisé'
                ], 403);
                return;
            }

            $congeModel = Flight::Conge();
            $statistiques = $congeModel->getDonneesConges($idEmploye);
            
            Flight::json([
                'success' => true,
                'data' => $statistiques
            ]);
            
        } 
        catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}