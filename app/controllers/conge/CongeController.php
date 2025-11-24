<?php

namespace app\controllers\conge;

use app\models;
use Flight;

class CongeController {

    /**
     * Calcule le taux de validation global pour les statistiques
     * @param array $demandesEnAttente Liste des demandes en attente
     * @return float Taux de validation en pourcentage
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
     * Affiche l'interface de validation des congés
     */
    public function getInterfaceValidation() {
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
            
            // ✅ NOUVEAU : Vérifier si l'employé connecté a déjà validé cette demande
            $demande['deja_valide'] = $congeModel->aDejaValide($demande['id_demande'], $idEmployeConnecte);
        }
        
        Flight::render('conge/liste_validation', [
            'demandes_en_attente' => $demandesEnAttente,
            'taux_validation_global' => $tauxValidationGlobal
        ]);
    }

    /**
     * Valide une demande de congé
     * @param int $idDemande ID de la demande de congé
     */
    public function postValidation($idDemande) {
        try {
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
            
            // ✅ NOUVEAU : Vérifier si l'employé a déjà validé cette demande
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
     * @return int|null ID de l'employé ou null si non connecté
     */
    private function getIdEmployeConnecte() {
        // ✅ ADAPTATION aux tables admins et employes du schéma
        if (isset($_SESSION['admin']['id_employe'])) {
            return $_SESSION['admin']['id_employe'];
        } elseif (isset($_SESSION['admin']['id_admin'])) {
            // Si seul l'ID admin est disponible, récupérer l'ID employé associé
            $adminModel = Flight::adminModel(); // À créer selon votre structure
            $admin = $adminModel->getAdminById($_SESSION['admin']['id_admin']);
            return $admin['id_employe'] ?? null;
        }
        
        return null;
    }

    /**
     * Affiche le formulaire de demande de congé
     */
    public function getDemandeConge() {
        // Récupérer les types de congé disponibles
        $congeModel = Flight::Conge();
        $types_conge = $congeModel->getTypesConge();
        
        // Récupérer les informations de l'employé connecté
        $employeModel = Flight::Employe();
        
        // Déterminer l'ID de l'employé selon le type de session
        if (isset($_SESSION['infoAdmin']['id_employe'])) {
            $idEmploye = $_SESSION['infoAdmin']['id_employe'];
        } elseif (isset($_SESSION['utilisateur']['id_utilisateur'])) {
            // Si vous avez un lien entre utilisateur et employé
            $idEmploye = $_SESSION['utilisateur']['id_utilisateur']; // À adapter selon votre structure
        } else {
            // Redirection si non connecté
            Flight::redirect('/admin');
            return;
        }
        
        // Récupérer les informations détaillées de l'employé
        $employe = $employeModel->findByIdWithDetails($idEmploye);
        
        // Récupérer les statistiques de congés
        $statistiquesConges = $congeModel->getDonneesCongesParType($idEmploye);
        
        // CORRECTION : Calculer le nombre total de congés restants basé sur conge_type
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
            'nombre_conge' => $nombre_conge_restant, // CORRECTION : Utiliser le calcul basé sur conge_type
            'demandes_annee' => $demandes_annee,
            'taux_approbation' => $taux_approbation,
            'statistiques' => $statistiquesConges
        ]);
    }
    /**
     * Traite la soumission du formulaire de demande de congé
     */
    public function submitDemande() {
        $congeModel = Flight::Conge();
        $employeModel = Flight::Employe();
        // Récupérer les données du formulaire
        $data = [
            'id_employe' => $_POST['id_employe'] ?? null,
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
            'employe' => $employeModel->findByIdWithDetails($data['id_employe']), // Récupère les infos employé
            'types_conge' => $congeModel->getTypesConge(),
            'nombre_conge' => $congeModel->getSoldeConge($data['id_employe']),
            'demandes_annee' => $congeModel->getNombreDemandesAnnee($data['id_employe']),
            'taux_approbation' => $congeModel->getTauxApprobation($data['id_employe'])
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
        $dossierUpload = __DIR__ . '/../../uploads/justificatifs/';
        
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
     * @param int $idEmploye ID de l'employé
     * @param int|null $idType Type de congé (optionnel)
     */
    public function getDetailConges($idEmploye, $idType = null) {
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
     * @param int $idEmploye ID de l'employé
     * @param bool|null $estAutorise Type d'absence (true=autorisé, false=non autorisé, null=tous)
     */
    public function getDetailAbsences($idEmploye, $estAutorise = null) {
        $ficheDetail = $this->parametreInfoEmploye($idEmploye);
        $abscenceModel = Flight::Abscence();
        // Récupération des détails d'absences
        $ficheDetail['detailAbsence']  = $abscenceModel->getTableauDetailAbsence($idEmploye, $estAutorise);
        
        // Passage des données à la vue
        Flight::render('conge/fiche_employe', $ficheDetail);
    }

    /**
     * Affiche la fiche complète d'un employé avec ses congés et absences
     * @param int $idEmploye ID de l'employé
     */
    public function getFicheEmploye($idEmploye) {
        // Passage des données à la vue
        Flight::render('conge/fiche_employe', $this->parametreInfoEmploye($idEmploye));
    }

    /**
     * Parametre les info de la personne et les options (abscence, conge, etc)
     * @param int $idEmploye ID de l'employé
     */
    
    private function parametreInfoEmploye($idEmploye) {
            $employeModel = Flight::Employe();
            $abscenceModel = Flight::Abscence();
            $congeModel = Flight::Conge();
            
            // Récupération des données
            $fiche = $employeModel->getFicheEmploye($idEmploye);
            $absences = $abscenceModel->getSectionAbsences($idEmploye);
            $listeconge = $congeModel->getNombreConge($idEmploye);
            $employe = $employeModel->findByIdWithDetails($idEmploye);
        
        return [
           'fiche' => $fiche,
            'absences' => $absences,
            'listeconge' => $listeconge, 
            'nombre_conge' => $employe['nombre_conge']
        ];
    }
    /**
     * Affiche la liste des employés avec leurs informations de congés et absences
     */
    public function getListeEmployes() {
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
                'nom' => $employe['nom'],
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
     * @param int $idEmploye ID de l'employé
     */
    public function getStatistiques($idEmploye) {
        try {
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