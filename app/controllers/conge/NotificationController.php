<?php

namespace app\controllers\conge;

use Flight;
use app\models\MessagerieModel;

class NotificationController
{

    /**
     * Notifie les employés concernant leurs absences non justifiées
     * Route: GET /notifier
     */
    public function notifierAbsences($idAbsence) {
        error_log("notifierAbsences called idAbsence=" . json_encode($idAbsence) . " sessionInfoAdmin=" . json_encode($_SESSION['infoAdmin'] ?? null));
        try {
            // Vérifier les droits d'administration
            if (!$this->estAdministrateur()) {
                error_log("Accès non autorisé: session infoAdmin manquante");
                Flight::json([
                    'success' => false,
                    'error' => 'Accès non autorisé. Droits administrateur requis.'
                ], 403);
                return;
            }
            
            if (!$idAbsence) {
                error_log("ID d'absence manquant");
                Flight::json([
                    'success' => false,
                    'error' => 'ID d\'absence manquant. Utilisez: /notifier?id_abscence=ID'
                ], 400);
                return;
            }

            // Récupérer les informations de l'absence et de l'employé
            $absenceInfo = $this->getAbsenceInfo($idAbsence);
            
            if (!$absenceInfo) {
                error_log("Absence non trouvée pour idAbsence: " . $idAbsence);
                Flight::json([
                    'success' => false,
                    'error' => 'Absence non trouvée'
                ], 404);
                return;
            }

            error_log("Absence trouvée: " . json_encode($absenceInfo));

            // Envoyer la notification via la messagerie interne
            $resultat = $this->envoyerNotificationMessagerie($absenceInfo);
            
            if ($resultat['success']) {
                // Journaliser l'action
                error_log("Notification envoyée à l'employé " . $absenceInfo['employe'] . 
                         " pour l'absence ID: " . $idAbsence);
                
                Flight::json([
                    'success' => true,
                    'message' => 'Notification envoyée avec succès à ' . $absenceInfo['employe'],
                    'employe' => $absenceInfo['employe'],
                    'absence_id' => $idAbsence,
                    'type' => 'messagerie_interne'
                ]);
            } else {
                error_log("Erreur lors de l'envoi de la notification: " . $resultat['error']);
                Flight::json([
                    'success' => false,
                    'error' => $resultat['error']
                ], 500);
            }

        } catch (\Exception $e) {
            error_log("Erreur lors de l'envoi de notification: " . $e->getMessage());
            
            Flight::json([
                'success' => false,
                'error' => 'Erreur interne: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les informations de l'absence et de l'employé en utilisant AbscenceModel
     */
    private function getAbsenceInfo($idAbsence) {
        // Récupérer toutes les absences non autorisées
        $abscenceModel = Flight::Abscence();
        $absences = $abscenceModel->getListeAbsence(false);
        
        error_log("Absences non autorisées: " . json_encode(array_column($absences, 'id_abscence')));
        error_log("Recherche d'absence ID: " . $idAbsence);
        
        // Trouver l'absence spécifique par ID
        foreach ($absences as $absence) {
            if ($absence['id_abscence'] == $idAbsence) {
                error_log("Absence trouvée: " . json_encode($absence));
                return $absence;
            }
        }
        
        error_log("Absence non trouvée pour l'ID: " . $idAbsence);
        return null;
    }

    /**
     * Envoie la notification via la messagerie interne entre employés
     */
    private function envoyerNotificationMessagerie($absenceInfo) {
        try {
            error_log("envoyerNotificationMessagerie called with absenceInfo: " . json_encode($absenceInfo));
            // ID de l'admin qui envoie la notification
            $idAdmin = $_SESSION['infoAdmin']['id_employe'] ?? 1;
            $idEmploye = $absenceInfo['id_employe'];
            
            // Préparer le message de notification
            $message = $this->preparerMessageNotification($absenceInfo);
            
            // Utiliser la messagerie entre employés pour envoyer la notification
            $messagerieModel = new MessagerieModel();
            $resultat = $messagerieModel->repondreE($idAdmin, $idEmploye, $message);
            
            if ($resultat) {
                error_log("Notification envoyée avec succès via messagerie");
                return ['success' => true];
            } else {
                error_log("Erreur lors de l'envoi via la messagerie");
                return [
                    'success' => false,
                    'error' => 'Erreur lors de l\'envoi via la messagerie'
                ];
            }
            
        } catch (\Exception $e) {
            error_log("Exception dans envoyerNotificationMessagerie: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Erreur messagerie: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Prépare le message de notification formaté
     */
    private function preparerMessageNotification($absenceInfo) {
        $debut = $absenceInfo['debut_formatted'];
        $fin = $absenceInfo['fin_formatted'];
        $joursPris = $absenceInfo['jours_pris'];
        
        $lienJustification = '/absence/justifier';
        
        // Message en une seule ligne avec bouton HTML
        $message = "🔔 **Notification d'absence non justifiée** - ";
        $message .= "Bonjour {$absenceInfo['prenom']}, ";
        $message .= "Votre absence du **{$debut}** au **{$fin}** ({$joursPris} jour(s)) n'a pas encore été justifiée. ";
        
        if (!empty($absenceInfo['description'])) {
            $message .= "**Motif déclaré :** {$absenceInfo['description']} - ";
        }
        
        $message .= "**Veuillez justifier cette absence en cliquant sur le bouton ci-dessous :** ";
        
        // Ajout du bouton HTML stylisé
        $message .= '<a href="' . htmlspecialchars($lienJustification, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"  class="btn btn-primary btn-sm message-lien" style="padding: 8px 16px; margin: 5px 0; display: inline-block; text-decoration: none; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; border-radius: 8px; font-weight: 600; border: none;">📍 Justifier mon absence</a> ';
        
        $message .= "⚠️ **Important :** En l'absence de justification, cette absence pourra entraîner des mesures disciplinaires. ";
        $message .= "Cordialement, L'équipe des Ressources Humaines";
        
        return $message;
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    private function estAdministrateur() {
        error_log("Vérification admin - Session: " . json_encode($_SESSION));
        return isset($_SESSION['infoAdmin']['id_employe']);
    }

    /**
     * Méthode pour notifier plusieurs employés en lot (optionnel) - ADMIN SEULEMENT
     */
    public function notifierAbsencesLot() {
        try {
            // Vérifier les droits d'administration
            if (!$this->estAdministrateur()) {
                Flight::json([
                    'success' => false,
                    'error' => 'Accès non autorisé'
                ], 403);
                return;
            }

            // Récupérer toutes les absences non justifiées en utilisant AbscenceModel
            $absencesNonJustifiees = $this->getAbsencesNonJustifiees();
            
            if (empty($absencesNonJustifiees)) {
                Flight::json([
                    'success' => true,
                    'message' => 'Aucune absence non justifiée trouvée',
                    'notifications_envoyees' => 0
                ]);
                return;
            }

            $resultats = [];
            $employesNotifies = [];

            foreach ($absencesNonJustifiees as $absence) {
                // Éviter les doublons (un employé peut avoir plusieurs absences)
                if (in_array($absence['id_employe'], $employesNotifies)) {
                    continue;
                }

                $resultat = $this->envoyerNotificationMessagerie($absence);
                $resultats[] = [
                    'employe' => $absence['employe'],
                    'success' => $resultat['success'],
                    'error' => $resultat['error'] ?? null
                ];

                if ($resultat['success']) {
                    $employesNotifies[] = $absence['id_employe'];
                }
            }

            Flight::json([
                'success' => true,
                'message' => 'Notifications envoyées à ' . count($employesNotifies) . ' employé(s)',
                'notifications_envoyees' => count($employesNotifies),
                'details' => $resultats
            ]);

        } catch (\Exception $e) {
            Flight::json([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère toutes les absences non justifiées en utilisant AbscenceModel
     */
    private function getAbsencesNonJustifiees() {
        // Récupérer toutes les absences non autorisées
        $abscenceModel = Flight::Abscence();
        $absencesNonAutorisees = $abscenceModel->getListeAbsence(false);
        
        // Filtrer celles qui n'ont pas de justificatif
        $absencesNonJustifiees = array_filter($absencesNonAutorisees, function($absence) {
            return empty($absence['justificatif']);
        });
        
        return array_values($absencesNonJustifiees);
    }
}