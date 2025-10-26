<?php
namespace app\controllers\migration;

use Flight;
use app\models\migration\ValidationContratModel;
use app\models\MessagerieModel;
use app\models\ConnexionModel;

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
     * Retourne le rôle de l'utilisateur courant basé sur les sessions et la BD.
     * Valeurs retournées possibles : 'visiteur', 'utilisateur', 'admin', 'gestion', 'rh',
     * 'compta', 'stock', 'vente', 'responsable' (prioritaire si l'admin/employe est responsable d'entretien).
     */
    public static function getRoleUtilisateur()
    {
        // session_start() est déjà appelé en haut du fichier, donc on suppose la session active.

        // 1) Si c'est un simple utilisateur connecté
        if (isset($_SESSION['utilisateur']) && !isset($_SESSION['admin']) && !isset($_SESSION['employe'])) {
            return 'utilisateur';
        }

        // 2) Si c'est un employé connecté (NOUVEAU)
        if (isset($_SESSION['employe'])) {
            $idEmploye = $_SESSION['employe']['id_employe'] ?? null;

            // Vérifier si l'employé est responsable d'entretien
            if ($idEmploye) {
                try {
                    $db = Flight::db();
                    $stmt = $db->prepare('SELECT 1 FROM responsable_entretien WHERE id_employe = :id_employe LIMIT 1');
                    $stmt->execute(['id_employe' => $idEmploye]);
                    if ($stmt->fetchColumn()) {
                        return 'responsable';
                    }
                } catch (\Exception $e) {
                    // silent fallback si la requête échoue
                }
            }

            // Déterminer le département de l'employé
            $idDepartement = $_SESSION['employe']['id_departement'] ?? null;
            
            // mapping id_departement -> rôle
            switch ($idDepartement) {
                case 1: // Direction
                    return 'gestion';
                case 2: // Comptabilité
                    return 'compta';
                case 3: // Stock
                    return 'stock';
                case 4: // RH
                    return 'rh';
                case 5: // Vente
                    return 'vente';
                default:
                    return 'employe'; // rôle générique pour employé
            }
        }

        // 3) Si c'est un admin connecté
        if (isset($_SESSION['admin'])) {
            // Priorité : si l'admin est aussi responsable d'entretien => 'responsable'
            $idEmploye = $_SESSION['admin']['id_employe'] ?? null;

            if ($idEmploye) {
                try {
                    $db = Flight::db();
                    $stmt = $db->prepare('SELECT 1 FROM responsable_entretien WHERE id_employe = :id_employe LIMIT 1');
                    $stmt->execute(['id_employe' => $idEmploye]);
                    if ($stmt->fetchColumn()) {
                        return 'responsable';
                    }
                } catch (\Exception $e) {
                    // silent fallback
                }
            }

            // Ensuite, on détermine le département (id_departement) soit depuis la session
            // soit en interrogeant ConnexionModel
            $idDepartement = null;
            if (isset($_SESSION['departement']['id_departement'])) {
                $idDepartement = (int) $_SESSION['departement']['id_departement'];
            } else {
                // fallback : essayer de récupérer depuis le modèle
                try {
                    $db = Flight::db();
                    $connexionModel = new ConnexionModel($db);
                    $depart = $connexionModel->getDepartementAdmin($_SESSION['admin']['id_admin'] ?? null);
                    if (!empty($depart) && isset($depart['id_departement'])) {
                        $idDepartement = (int) $depart['id_departement'];
                        $_SESSION['departement'] = $depart;
                    }
                } catch (\Exception $e) {
                    // fallback silencieux
                }
            }

            // mapping simple id_departement -> rôle
            switch ($idDepartement) {
                case 1: // Direction
                    return 'gestion';
                case 2: // Comptabilité
                    return 'compta';
                case 3: // Stock
                    return 'stock';
                case 4: // RH
                    return 'rh';
                case 5: // Vente
                    return 'vente';
                default:
                    return 'admin'; // admin générique
            }
        }

        // 4) Par défaut : visiteur non authentifié
        return 'visiteur';
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
        
        // 🔹 Si admin ou employé, accès autorisé
        if (isset($_SESSION['admin']) || isset($_SESSION['employe'])) {
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