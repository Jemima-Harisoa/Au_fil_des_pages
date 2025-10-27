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
     * 🔹 Envoie une notification à la prochaine personne concernée selon la liste de priorité
     */
    public function notifierProchaineEtape($id_contrat, $prochain_statut) {
        $messagerieModel = new MessagerieModel();
        $validationModel = new ValidationContratModel(Flight::db());
        $contratModel = Flight::Contrat();
        $candidatModel = Flight::Candidat();
        $personneModel = Flight::Personne();
        $employeModel = Flight::Employe();
        
        $contrat = $contratModel->getBy('id_contrat', $id_contrat);
        if (!$contrat) {
            error_log("❌ Contrat introuvable pour notification: $id_contrat");
            return;
        }

        $candidat = $candidatModel->getBy('id_candidat', $contrat['id_candidat']);
        if (!$candidat) {
            error_log("❌ Candidat introuvable pour contrat: $id_contrat");
            return;
        }

        $lien_contrat = Flight::get('flight.base_url') . "/migration/contrat/edit?id=" . $id_contrat;
        
        // Récupérer les validateurs pour l'étape suivante
        $validateurs = $validationModel->getValidateursPourEtape($prochain_statut);
        error_log("🔔 Notification - Contrat: $id_contrat, Statut: $prochain_statut, Validateurs: " . implode(', ', $validateurs));
        
        $messages = [
            'en_attente_etape1' => [
                'role' => 'rh',
                'message_candidat' => "📋 Votre contrat a été créé et est en cours de validation par les ressources humaines.",
                'message_validateur' => "📋 Nouveau contrat nécessitant votre validation RH."
            ],
            'en_attente_etape2' => [
                'role' => 'service', 
                'message_candidat' => "✅ Votre contrat a été validé par les RH et est en cours de validation par le service concerné.",
                'message_validateur' => "📋 Contrat validé par les RH, nécessite maintenant votre validation."
            ],
            'en_attente_etape3' => [
                'role' => 'candidat',
                'message_candidat' => "✅ Votre contrat a été validé par le service! Veuillez le consulter et le valider.",
                'message_validateur' => "📋 Contrat en attente de validation finale par le candidat."
            ],
            'validé' => [
                'role' => 'candidat',
                'message_candidat' => "🎉 Félicitations! Votre contrat a été validé avec succès!",
                'message_validateur' => "✅ Contrat validé avec succès!"
            ]
        ];
        
        $config = $messages[$prochain_statut] ?? [
            'role' => 'rh',
            'message_candidat' => "📋 Votre contrat est en cours de traitement (statut: {$prochain_statut}).",
            'message_validateur' => "📋 Contrat en attente de validation (statut: {$prochain_statut})."
        ];
        
        // 🔹 1. Notification au candidat
        if ($candidat) {
            try {
                $formated_link = $messagerieModel->styliserLiens($lien_contrat, "Voir le contrat");
                $message_complet = $config['message_candidat'] . " " . $formated_link;
                
                $result = $messagerieModel->repondreA(
                    $candidat['id_candidat'],
                    $candidat['id_annonce'] ?? 1,
                    $message_complet
                );
                
                if ($result) {
                    error_log("✅ Notification envoyée au candidat: {$candidat['id_candidat']}");
                } else {
                    error_log("❌ Échec envoi notification candidat: {$candidat['id_candidat']}");
                }
            } catch (\Exception $e) {
                error_log("❌ Erreur notification candidat: " . $e->getMessage());
            }
        }
        
        // 🔹 2. Notification aux validateurs internes (RH, service) - AVEC NOUVELLE MÉTHODE
        if (in_array($config['role'], ['rh', 'service'])) {
            $employes = $validationModel->getEmployesParRole($config['role'], $id_contrat);
            error_log("🔔 Employés à notifier ({$config['role']}): " . count($employes));
            
            foreach ($employes as $employe) {
                try {
                    $id_employe = $employe['id_employe'];
                    $employe_info = $employeModel->getBy('id_employe', $id_employe);
                    
                    if (!$employe_info) continue;
                    
                    // 🔹 NOUVELLE METHODE : Notification interne dédiée avec lien
                    $message_employe = $config['message_validateur'] . " [Contrat #$id_contrat]";
                    $result = $messagerieModel->notifierEmploye($id_employe, $message_employe);
                    
                    if ($result) {
                        error_log("✅ Notification interne pour employé {$employe_info['poste']}");
                    } else {
                        error_log("❌ Échec notification interne employé {$employe_info['poste']}");
                    }
                    
                } catch (\Exception $e) {
                    error_log("❌ Erreur notification employé {$employe['id_employe']}: " . $e->getMessage());
                }
            }
        }
        error_log("🔔 Processus de notification terminé pour contrat: $id_contrat");
    }

    /**
     * 🔹 Mettre à jour les sessions de notification pour tous les employés concernés
     */
    public function updateNotificationsSessions($id_contrat) {
        $validationModel = new ValidationContratModel(Flight::db());
        $messagerieModel = new MessagerieModel();
        
        // Récupérer tous les employés concernés par ce contrat
        $employes_concernes = $validationModel->getEmployesConcernesParContrat($id_contrat);
        
        foreach ($employes_concernes as $employe) {
            $id_employe = $employe['id_employe'];
            
            // Mettre à jour la session si l'employé est actuellement connecté
            if (isset($_SESSION['employe']['id_employe']) && $_SESSION['employe']['id_employe'] == $id_employe) {
                $notifications = $messagerieModel->getNotificationsEmploye($id_employe);
                $notificationCount = $messagerieModel->countNouvellesNotifications($id_employe);
                
                $_SESSION['notifications_employe'] = $notifications;
                $_SESSION['notifications_employe_count'] = $notificationCount;
            }
        }
    }

    /**
     * Crée une notification pour un employé dans la base de données
     */
    private function creerNotificationEmploye($id_employe, $message, $id_contrat) {
        try {
            $db = Flight::db();
            
            // Récupérer l'id_personne de l'employé
            $sql = "SELECT id_personne FROM employes WHERE id_employe = :id_employe";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id_employe' => $id_employe]);
            $employe = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($employe && isset($employe['id_personne'])) {
                $sql = "INSERT INTO notifications (id_personne, message, date_notification) 
                        VALUES (:id_personne, :message, NOW())";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'id_personne' => $employe['id_personne'],
                    'message' => $message . " (Contrat #$id_contrat)"
                ]);
                
                error_log("✅ Notification BD créée pour employé: $id_employe");
                return true;
            }
        } catch (\Exception $e) {
            error_log("❌ Erreur création notification BD: " . $e->getMessage());
        }
        return false;
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