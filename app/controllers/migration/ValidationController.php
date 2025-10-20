<?php
namespace app\controllers\migration;

use Flight;
use app\models\migration\ValidationContratModel;
use app\models\MessagerieModel;

class ValidationController {
    
    /**
     * 🔹 Récupère les informations de validation pour un contrat
     */
    public function getInfosValidation($id_contrat) {
        $validationModel = new ValidationContratModel(Flight::db());
        
        return [
            'statut_actuel' => $validationModel->getStatutActuel($id_contrat),
            'historique_validation' => $validationModel->getHistoriqueValidation($id_contrat),
            'peut_valider' => $validationModel->verifierPermission(
                $id_contrat, 
                $this->getRoleUtilisateur()
            )
        ];
    }

    /**
     * 🔹 Initialise le statut "draft" pour un nouveau contrat
     */
    public function initialiserStatutDraft($id_contrat, $id_employe, $note = 'Création du contrat (brouillon)') {
        $validationModel = new ValidationContratModel(Flight::db());
        
        try {
            return $validationModel->envoyerPourValidation($id_contrat, $id_employe, $note);
        } catch (\Exception $e) {
            error_log("Erreur initialisation statut draft: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 🔹 Envoie une notification à la prochaine personne concernée
     */
    public function notifierProchaineEtape($id_contrat, $prochain_statut) {
        $messagerieModel = new MessagerieModel();
        $contratModel = Flight::Contrat();
        $contrat = $contratModel->getBy('id_contrat', $id_contrat);
        
        if ($contrat) {
            $lien_edition = Flight::get('flight.base_url') . "/migration/contrat/edit?id=" . $id_contrat;
            
            $messages_par_statut = [
                'en_attente_etape1' => "📋 Le contrat nécessite votre validation RH. \n🔗 Lien: {$lien_edition}",
                'en_attente_etape2' => "📋 Le contrat nécessite la validation du service concerné. \n🔗 Lien: {$lien_edition}", 
                'en_attente_etape3' => "📋 Veuillez consulter et valider votre contrat. \n🔗 Lien: {$lien_edition}",
                'validé' => "✅ Contrat validé avec succès! \n🔗 Lien: {$lien_edition}"
            ];
            
            $message = $messages_par_statut[$prochain_statut] ?? 
                      "📋 Contrat en attente de validation (statut: {$prochain_statut}). \n🔗 Lien: {$lien_edition}";
            
            $messagerieModel->repondreA(
                $contrat['id_candidat'],
                $contrat['id_annonce'] ?? 1,
                $message
            );
        }
    }

    /**
     * 🔹 Détermine le rôle de l'utilisateur connecté
     */
    public function getRoleUtilisateur() {
        if (!isset($_SESSION['admin'])) {
            return 'visiteur';
        }
        
        return 'rh'; // Par défaut, tous les admins sont considérés comme RH
    }

    /**
     * 🔹 Vérifie que l'utilisateur connecté a le droit d'accéder à ce contrat
     */
    public function verifierAccesContrat($id_contrat) {
        $contratModel = Flight::Contrat();
        $contrat = $contratModel->getBy('id_contrat', $id_contrat);
        
        if (!$contrat) {
            return false;
        }
        
        // 🔹 Si admin, accès autorisé (à affiner selon les départements)
        if (isset($_SESSION['admin'])) {
            return true;
        }
        
        // 🔹 Si utilisateur, vérifier que c'est son contrat
        if (isset($_SESSION['utilisateur'])) {
            $candidatModel = Flight::Candidat();
            $candidat = $candidatModel->getBy('id_candidat', $contrat['id_candidat']);
            
            return $candidat && $candidat['id_utilisateur'] == $_SESSION['utilisateur']['id_utilisateur'];
        }
        
        return false;
    }
}