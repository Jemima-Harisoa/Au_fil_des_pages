<?php

namespace app\controllers;

use app\models;
use Flight;

class CompetenceController {

    /**
     * Affiche la liste complète des compétences avec statistiques
     */
    public function getListeCompetences() {
        // Vérifier les droits d'administration ou si c'est un employé consultant
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::redirect('/employe');
            return;
        }


        $competenceModel = Flight::Competence();
        
        // Récupérer les paramètres de filtrage
        $params = [
            'page' => $_GET['page'] ?? 1,
            'perPage' => $_GET['perPage'] ?? 10,
            'search' => $_GET['search'] ?? '',
            'domaine' => $_GET['domaine'] ?? '',
            'niveau_min' => $_GET['niveau_min'] ?? 0,
            'sortField' => $_GET['sortField'] ?? 'nb_employes',
            'sortOrder' => $_GET['sortOrder'] ?? 'DESC'
        ];

        // Si c'est un employé (pas admin), on peut appliquer des restrictions supplémentaires
        if ($this->estEmploye() && !$this->estAdministrateur()) {
            $idEmploye = $this->getIdEmployeConnecte();
            // Optionnel: filtrer pour ne montrer que les compétences de l'employé
            // $params['id_employe'] = $idEmploye;
        }

        $tableau = $competenceModel->getTableauListeCompetences($params);
        $stats_section = $competenceModel->getSectionStatsCompetences();
        
        Flight::render('competences_admin', [
            'tableau' => $tableau,
            'stats_section' => $stats_section,
            'estEmploye' => $this->estEmploye() && !$this->estAdministrateur(),
            'params' => $params
        ]);
    }
    /**
     * Affiche les détails d'une compétence spécifique
     */
    public function getDetailsCompetence($idCompetence) {
    // Vérifier les droits
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $competenceModel = Flight::Competence();
        $detailsHTML = $competenceModel->getTableauDetailCompetence($idCompetence);
        
        if (!$detailsHTML) {
            Flight::json([
                'success' => false,
                'error' => 'Compétence non trouvée'
            ], 404);
            return;
        }

        // Vérifier que l'utilisateur a le droit de voir ces détails
        if (!$this->peutVoirCompetence($idCompetence)) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé à cette compétence'
            ], 403);
            return;
        }

        echo $detailsHTML;
    }
    /**
     * Exporte les compétences au format CSV ou PDF
     */
    public function exportCompetences() {
        // Vérifier les droits d'administration
        if (!$this->estAdministrateur()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $format = $_GET['format'] ?? 'csv';
        
        if (!in_array($format, ['csv', 'pdf'])) {
            Flight::json([
                'success' => false,
                'error' => 'Format non supporté'
            ], 400);
            return;
        }

        $competenceModel = Flight::Competence();
        
        try {
            $data = $competenceModel->exportCompetences($format);
            
            if ($format === 'csv') {
                $this->genererCSV($data);
            } elseif ($format === 'pdf') {
                $this->genererPDF($data);
            }

        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur lors de l\'export: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recherche de compétences (pour autocomplétion)
     */
    public function searchCompetences() {
        // Vérifier les droits
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $keyword = $_GET['q'] ?? '';
        
        if (empty($keyword)) {
            Flight::json([]);
            return;
        }

        $competenceModel = Flight::Competence();
        
        try {
            $results = $competenceModel->searchCompetences($keyword);
            Flight::json($results);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur lors de la recherche: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filtre les compétences selon plusieurs critères
     */
    public function filterCompetences() {
        // Vérifier les droits
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $filters = [
            'departement' => $_GET['departement'] ?? '',
            'niveau_min' => $_GET['niveau_min'] ?? 0,
            'domaine' => $_GET['domaine'] ?? ''
        ];

        $competenceModel = Flight::Competence();
        
        try {
            $results = $competenceModel->filterCompetences($filters);
            Flight::json($results);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur lors du filtrage: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche les statistiques globales des compétences
     */
    public function getStatsGlobales() {
        // Vérifier les droits
        if (!$this->estAdministrateur() && !$this->estEmploye()) {
            Flight::json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
            return;
        }

        $competenceModel = Flight::Competence();
        
        try {
            $stats = $competenceModel->getStatsGlobales();
            $sectionStats = $competenceModel->getSectionStatsCompetences();
            
            Flight::json([
                'success' => true,
                'stats' => $stats,
                'html' => $sectionStats
            ]);
            
        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifie si l'utilisateur peut voir les détails d'une compétence
     */
    private function peutVoirCompetence($idCompetence) {
        // Les admins peuvent tout voir
        if ($this->estAdministrateur()) {
            return true;
        }
        
        // Les employés peuvent voir toutes les compétences (ou adapter selon besoins)
        if ($this->estEmploye()) {
            return true;
        }
        
        return false;
    }

    /**
     * Génère un fichier CSV des compétences
     */
    private function genererCSV($data) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="competences_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // En-tête CSV
        fputcsv($output, [
            'ID', 'Nom', 'Domaine', 'Type', 'Nb Employés', 
            'Niveau Moyen', 'Nb Validés', 'Dernière MAJ'
        ]);
        
        // Données
        foreach ($data['data'] as $competence) {
            fputcsv($output, [
                $competence['id_competence'],
                $competence['nom'],
                $competence['domaine'],
                $competence['type_competence'] ?? '',
                $competence['nb_employes'],
                $competence['niveau_moyen'],
                $competence['nb_employes_valides'],
                $competence['derniere_maj_formatted']
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Génère un fichier PDF des compétences (à implémenter selon votre bibliothèque PDF)
     */
    private function genererPDF($data) {
        // Implémentation basique - à adapter avec votre bibliothèque PDF (TCPDF, Dompdf, etc.)
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="competences_' . date('Y-m-d') . '.pdf"');
        
        // Exemple basique - à remplacer par votre génération PDF
        $html = '<h1>Export des Compétences</h1>';
        $html .= '<p>Date d\'export: ' . $data['date_export'] . '</p>';
        $html .= '<p>Total compétences: ' . $data['total_competences'] . '</p>';
        
        // Conversion HTML en PDF (à implémenter)
        echo $html;
        exit;
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
     * Récupère l'ID de l'employé connecté
     */
    private function getIdEmployeConnecte() {
        if (isset($_SESSION['infoAdmin']['id_employe'])) {
            return $_SESSION['infoAdmin']['id_employe'];
        } elseif (isset($_SESSION['employe']['id_employe'])) {
            return $_SESSION['employe']['id_employe'];
        } elseif (isset($_SESSION['utilisateur']['id_utilisateur'])) {
            return $_SESSION['utilisateur']['id_utilisateur'];
        }
        return null;
    }
}