<?php

namespace app\controllers\conge;

use app\models;
use Flight;

class CongeController {
    
    /**
     * Affiche la fiche complète d'un employé avec ses congés et absences
     * @param int $idEmploye ID de l'employé
     */
    public function getFicheEmploye($idEmploye) {
        // Récupération des modèles
            $employeModel = Flight::Employe();
            $abscenceModel = Flight::Abscence();
            $congeModel = Flight::Conge();
            
            // Récupération des données
            $fiche = $employeModel->getFicheEmploye($idEmploye);
            $absences = $abscenceModel->getSectionAbsences($idEmploye);
            $listeconge = $congeModel->getNombreConge($idEmploye);
            $employe = $employeModel->findByIdWithDetails($idEmploye);
 
            // Passage des données à la vue
            Flight::render('conge/fiche_employe', [
                'fiche' => $fiche,
                'absences' => $absences,
                'listeconge' => $listeconge, 
                'nombre_conge' => $employe['nombre_conge']
            ]);
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