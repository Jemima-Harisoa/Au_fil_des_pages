<?php

namespace app\controllers\conge;

use Flight;
use app\models\MessagerieModel;

class NotificationController
{
    private $messagerieModel;



    /**
     * Notifie les employés concernant leurs absences non justifiées
     * Route: GET /notifier
     */
    public function notifierAbsences($idAbsence) {
        error_log("notifierAbsences called idAbsence=" . json_encode($idAbsence) . " sessionInfoAdmin=" . json_encode($_SESSION['infoAdmin'] ?? null));
        try {
            // Vérifier les droits d'administration
            if (!$this->estAdministrateur()) {
                Flight::json([
                    'success' => false,
                    'error' => 'Accès non autorisé. Droits administrateur requis.'
                ], 403);
                return;
            }
            
            if (!$idAbsence) {
                Flight::json([
                    'success' => false,
                    'error' => 'ID d\'absence manquant. Utilisez: /notifier?id_abscence=ID'
                ], 400);
                return;
            }

            // Récupérer les informations de l'absence et de l'employé
            $absenceInfo = $this->getAbsenceInfo($idAbsence);
            
            if (!$absenceInfo) {
                Flight::json([
                    'success' => false,
                    'error' => 'Absence non trouvée'
                ], 404);
                return;
            }

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
        
        // Trouver l'absence spécifique par ID
        foreach ($absences as $absence) {
            if ($absence['id_abscence'] == $idAbsence) {
                return $absence;
            }
        }
        
        return null;
    }

    /**
     * Envoie la notification via la messagerie interne entre employés
     */
    private function envoyerNotificationMessagerie($absenceInfo) {
        try {
            // ID de l'admin qui envoie la notification
            $idAdmin = $_SESSION['infoAdmin']['id_employe'] ?? 1;
            $idEmploye = $absenceInfo['id_employe'];
            
            // Préparer le message de notification
            $message = $this->preparerMessageNotification($absenceInfo);
            
            // Utiliser la messagerie entre employés pour envoyer la notification
            $messagerieModel = new MessagerieModel();
            $resultat = $messagerieModel->repondreE($idAdmin, $idEmploye, $message);
            
            if ($resultat) {
                return ['success' => true];
            } else {
                return [
                    'success' => false,
                    'error' => 'Erreur lors de l\'envoi via la messagerie'
                ];
            }
            
        } catch (\Exception $e) {
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
        
        $lienJustification = Flight::request()->base . '/absence/justifier';
        
        $message = "🔔 **Notification d'absence non justifiée**\n\n";
        $message .= "Bonjour {$absenceInfo['prenom']},\n\n";
        $message .= "Votre absence du **{$debut}** au **{$fin}** ({$joursPris} jour(s)) ";
        $message .= "n'a pas encore été justifiée.\n\n";
        
        if (!empty($absenceInfo['description'])) {
            $message .= "**Motif déclaré :** {$absenceInfo['description']}\n\n";
        }
        
        $message .= "**Veuillez justifier cette absence en cliquant sur le lien suivant :**\n";
        $message .= "📍 " . $lienJustification . "\n\n";
        $message .= "⚠️ **Important :** En l'absence de justification, cette absence pourra entraîner des mesures disciplinaires.\n\n";
        $message .= "Cordialement,\nL'équipe des Ressources Humaines";
        
        return $message;
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    private function estAdministrateur() {
        return isset($_SESSION['infoAdmin']['id_employe']);
    }

    /**
     * Méthode pour notifier plusieurs employés en lot (optionnel)
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