<?php
namespace app\controllers;

use app\models\MessagerieModel;
use app\models\AnnoncesModel;
use Flight;

class MessagerieController {
    
    // Méthode pour mettre à jour la session des notifications employé
    public function updateEmployeNotificationsSession() {
        if (!isset($_SESSION['employe']) && !isset($_SESSION['admin'])) {
            return;
        }
        
        $id_employe = $_SESSION['employe']['id_employe'] ?? $_SESSION['admin']['id_employe'] ?? null;
        
        if (!$id_employe) {
            return;
        }
        
        $messagerieModel = new MessagerieModel();
        $notifications = $messagerieModel->getNotificationsEmploye($id_employe);
        $notificationCount = $messagerieModel->countNouvellesNotifications($id_employe);
        
        // Mettre à jour la session
        $_SESSION['notifications_employe'] = $notifications;
        $_SESSION['notifications_employe_count'] = $notificationCount;
        
        return [
            'notifications' => $notifications,
            'count' => $notificationCount
        ];
    }

    // Méthode pour rafraîchir les notifications employé via AJAX
    public function refreshEmployeNotifications() {
        $result = $this->updateEmployeNotificationsSession();
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'notifications' => $result['notifications'],
                'count' => $result['count']
            ]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    // Dans MessagerieController ou un nouveau NotificationController
    public function getNotificationsEmploye() {
        if (!isset($_SESSION['employe']) && !isset($_SESSION['admin'])) {
            Flight::redirect('/connexion');
            return;
        }
        
        $id_employe = $_SESSION['employe']['id_employe'] ?? $_SESSION['admin']['id_employe'] ?? null;
        
        if (!$id_employe) {
            Flight::halt(403, "Accès non autorisé");
            return;
        }
        
        $messagerieModel = new MessagerieModel();
        $notifications = $messagerieModel->getNotificationsEmploye($id_employe);
        
        Flight::render('notifications/employe', [
            'notifications' => $notifications,
            'id_employe' => $id_employe
        ]);
    }

    // NOUVELLE MÉTHODE : Envoyer une notification à un employé
    public function sendNotificationEmploye() {
        if (!isset($_SESSION['admin'])) {
            echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $id_employe = $data['id_employe'] ?? null;
        $message = $data['message'] ?? '';
        
        if ($id_employe && $message) {
            $model = new MessagerieModel();
            $success = $model->notifierEmploye($id_employe, $message);
            
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Notification envoyée' : 'Erreur lors de l\'envoi'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        }
        exit;
    }

    // NOUVELLE MÉTHODE : Récupérer les notifications en temps réel pour employé
    public function sseNotificationsEmploye() {
        if (!isset($_SESSION['employe']) && !isset($_SESSION['admin'])) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            echo "data: " . json_encode(['error' => 'Non connecté']) . "\n\n";
            flush();
            return;
        }
        
        $id_employe = $_SESSION['employe']['id_employe'] ?? $_SESSION['admin']['id_employe'] ?? null;
        
        if (!$id_employe) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            echo "data: " . json_encode(['error' => 'Accès non autorisé']) . "\n\n";
            flush();
            return;
        }

        // Configurer les headers pour SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        
        // Empêcher la mise en buffer
        if (ob_get_level()) ob_end_clean();
        
        $model = new MessagerieModel();
        $lastCheck = 0;
        
        // Boucle infinie pour envoyer les mises à jour
        while (true) {
            try {
                $notifications = $model->getNotificationsEmploye($id_employe);
                $currentCheck = count($notifications);
                
                // Envoyer seulement si de nouvelles notifications
                if ($currentCheck !== $lastCheck) {
                    $data = [
                        'count' => $currentCheck,
                        'notifications' => $notifications,
                        'timestamp' => time(),
                        'type' => 'employe'
                    ];
                    
                    echo "data: " . json_encode($data) . "\n\n";
                    flush();
                    $lastCheck = $currentCheck;
                }
                
            } catch (Exception $e) {
                echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
                flush();
                break;
            }
            
            // Attendre 5 secondes avant la prochaine vérification
            sleep(5);
            
            // Vérifier si la connexion est encore active
            if (connection_aborted()) {
                break;
            }
        }
    }

    // NOUVELLE MÉTHODE : Rafraîchir les notifications employé via AJAX
    public function refreshNotificationsEmploye() {
        if (!isset($_SESSION['employe']) && !isset($_SESSION['admin'])) {
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            exit;
        }
        
        $id_employe = $_SESSION['employe']['id_employe'] ?? $_SESSION['admin']['id_employe'] ?? null;
        
        if (!$id_employe) {
            echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
            exit;
        }
        
        $model = new MessagerieModel();
        
        try {
            $notifications = $model->getNotificationsEmploye($id_employe);
            
            echo json_encode([
                'success' => true, 
                'type' => 'employe',
                'count' => count($notifications),
                'notifications' => $notifications,
                'timestamp' => time()
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }

    // NOUVELLE MÉTHODE : Marquer les notifications employé comme lues
    public function markNotificationsAsRead() {
        if (!isset($_SESSION['employe']) && !isset($_SESSION['admin'])) {
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            exit;
        }
        
        $id_employe = $_SESSION['employe']['id_employe'] ?? $_SESSION['admin']['id_employe'] ?? null;
        
        if (!$id_employe) {
            echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
            exit;
        }
        
        $model = new MessagerieModel();
        
        try {
            // Créer un fichier de marquage de lecture
            $dir = __DIR__ . '/../../public/conversations/notifications';
            $readFile = $dir . '/employe_' . $id_employe . '_read.txt';
            file_put_contents($readFile, time());
            
            // Mettre à jour la session
            $this->updateEmployeNotificationsSession();
            
            echo json_encode([
                'success' => true,
                'message' => 'Notifications marquées comme lues',
                'timestamp' => time()
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function showMessagerieU($id_candidat, $id_annonce) {
        $model = new MessagerieModel();

        // Marquer comme lu AVANT de récupérer les messages
        $model->repondreU($id_candidat, $id_annonce, '');
        
        // Récupérer les messages
        $messages = $model->getMessagerie($id_candidat, $id_annonce);

        $AnnoncesModel = new AnnoncesModel();
        $titre = $AnnoncesModel->get($id_annonce)['titre'];

        // MAJ session APRÈS avoir marqué comme lu
        $messagerie = $model->getTitresConversationsU($_SESSION['utilisateur']['id_utilisateur']);

        // Calcul du badge avec la nouvelle méthode
        $nbNonLus = $model->countNouveauxMessagesU($_SESSION['utilisateur']['id_utilisateur']);

        // Rendu avec badge correct
        Flight::render('messagerieU', [
            'messages'     => $messages,
            'id_candidat'  => $id_candidat,
            'id_annonce'   => $id_annonce,
            'titre'        => $titre,
            'nbNonLus'     => $nbNonLus ,
            'messagerie'     => $messagerie 
        ]);
    }

    public function sendMessageU() {
        $data = json_decode(file_get_contents('php://input'), true);
        $id_candidat = $data['id_candidat'] ?? null;
        $id_annonce = $data['id_annonce'] ?? null;
        $message = $data['message'] ?? '';
    
        if ($id_candidat && $id_annonce && $message) {
            $model = new MessagerieModel();
            $model->repondreU($id_candidat, $id_annonce, $message);
            
            // Mettre à jour la session
            $_SESSION['messagerie'] = $model->getTitresConversationsU($_SESSION['utilisateur']['id_utilisateur']);
            
            // Retourner aussi le nouveau count pour l'actualisation du badge
            $newCount = $model->countNouveauxMessagesU($_SESSION['utilisateur']['id_utilisateur']);
            
            echo json_encode([
                'success' => true,
                'newCount' => $newCount,
            ]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    public function sendMessageA() {
        $data = json_decode(file_get_contents('php://input'), true);
        $id_candidat = $data['id_candidat'] ?? null;
        $id_annonce = $data['id_annonce'] ?? null;
        $message = $data['message'] ?? '';
    
        if ($id_candidat && $id_annonce && $message) {
            $model = new MessagerieModel();
            $model->repondreA($id_candidat, $id_annonce, $message);
            
            // Mettre à jour la session
            $_SESSION['messagerie'] = $model->getTitresConversationsA();
            
            // Retourner aussi le nouveau count
            $newCount = $model->countNouveauxMessagesA();
            
            echo json_encode([
                'success' => true,
                'newCount' => $newCount
            ]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    public function showMessagerieA($id_candidat, $id_annonce) {
        $model = new MessagerieModel();
        
        // Marquer comme lu AVANT de récupérer
        $model->repondreA($id_candidat, $id_annonce, '');
        $messages = $model->getMessagerie($id_candidat, $id_annonce);
        
        $AnnoncesModel = new AnnoncesModel();
        $titre = $AnnoncesModel->get($id_annonce)['titre'];
        
        // MAJ session APRÈS avoir marqué comme lu
        $_SESSION['messagerie'] = $model->getTitresConversationsA();

        Flight::render('messagerieA', [
            'messages'     => $messages,
            'id_candidat'  => $id_candidat,
            'id_annonce'   => $id_annonce,
            'titre' => $titre
        ]);
    }

    // NOUVELLE MÉTHODE : Endpoint SSE (Server-Sent Events) pour les mises à jour en temps réel
    public function sseNotifications() {
        // Configurer les headers pour SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        
        // Empêcher la mise en buffer
        if (ob_get_level()) ob_end_clean();
        
        $model = new MessagerieModel();
        $lastCount = -1;
        
        // Boucle infinie pour envoyer les mises à jour
        while (true) {
            try {
                if (isset($_SESSION['utilisateur'])) {
                    $count = $model->countNouveauxMessagesU($_SESSION['utilisateur']['id_utilisateur']);
                    $type = 'utilisateur';
                } elseif (isset($_SESSION['admin'])) {
                    $count = $model->countNouveauxMessagesA();
                    $type = 'admin';
                } else {
                    echo "data: " . json_encode(['error' => 'Non connecté']) . "\n\n";
                    flush();
                    break;
                }
                
                // Envoyer seulement si le count a changé
                if ($count !== $lastCount) {
                    $data = [
                        'count' => $count,
                        'type' => $type,
                        'timestamp' => time()
                    ];
                    
                    echo "data: " . json_encode($data) . "\n\n";
                    flush();
                    $lastCount = $count;
                }
                
            } catch (Exception $e) {
                echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
                flush();
                break;
            }
            
            // Attendre 5 secondes avant la prochaine vérification
            sleep(5);
            
            // Vérifier si la connexion est encore active
            if (connection_aborted()) {
                break;
            }
        }
    }

    // Méthode pour rafraîchir les notifications en temps réel (version améliorée)
    public function refreshNotifications() {
        $model = new MessagerieModel();
        
        try {
            if (isset($_SESSION['utilisateur'])) {
                // Forcer la régénération de la liste des conversations
                $_SESSION['messagerie'] = $model->getTitresConversationsU($_SESSION['utilisateur']['id_utilisateur']);
                $count = $model->countNouveauxMessagesU($_SESSION['utilisateur']['id_utilisateur']);
                
                echo json_encode([
                    'success' => true, 
                    'type' => 'utilisateur',
                    'count' => $count,
                    'conversations' => $_SESSION['messagerie'],
                    'timestamp' => time()
                ]);
            } elseif (isset($_SESSION['admin'])) {
                $_SESSION['messagerie'] = $model->getTitresConversationsA();
                $count = $model->countNouveauxMessagesA();
                
                echo json_encode([
                    'success' => true, 
                    'type' => 'admin',
                    'count' => $count,
                    'conversations' => $_SESSION['messagerie'],
                    'timestamp' => time()
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Non connecté']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }

    // Méthode pour obtenir le count actuel sans rafraîchir
    public function getNotificationCount() {
        $model = new MessagerieModel();
        
        try {
            if (isset($_SESSION['utilisateur'])) {
                $count = $model->countNouveauxMessagesU($_SESSION['utilisateur']['id_utilisateur']);
                echo json_encode([
                    'success' => true, 
                    'count' => $count, 
                    'type' => 'utilisateur',
                    'timestamp' => time()
                ]);
            } elseif (isset($_SESSION['admin'])) {
                $count = $model->countNouveauxMessagesA();
                echo json_encode([
                    'success' => true, 
                    'count' => $count, 
                    'type' => 'admin',
                    'timestamp' => time()
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Non connecté']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }

    // Méthode pour marquer comme lu via AJAX et retourner le nouveau count
    public function markAsReadAndGetCount() {
        $data = json_decode(file_get_contents('php://input'), true);
        $id_candidat = $data['id_candidat'] ?? null;
        $id_annonce  = $data['id_annonce'] ?? null;

        if ($id_candidat && $id_annonce) {
            $model = new MessagerieModel();

            // Marquer comme lu selon le type de session
            if (isset($_SESSION['utilisateur'])) {
                $model->repondreU($id_candidat, $id_annonce, '');
                $_SESSION['messagerie'] = $model->getTitresConversationsU($_SESSION['utilisateur']['id_utilisateur']);
                $newCount = $model->countNouveauxMessagesU($_SESSION['utilisateur']['id_utilisateur']);
            } elseif (isset($_SESSION['admin'])) {
                $model->repondreA($id_candidat, $id_annonce, '');
                $_SESSION['messagerie'] = $model->getTitresConversationsA();
                $newCount = $model->countNouveauxMessagesA();
            } else {
                echo json_encode(['success' => false, 'message' => 'Non connecté']);
                exit;
            }

            echo json_encode([
                'success'  => true,
                'newCount' => $newCount
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        }

        exit;
    }

    // Nouvelle méthode pour marquer comme lu simplement (sans paramètres POST)
    public function markConversationAsRead($id_candidat, $id_annonce) {
        $model = new MessagerieModel();
        
        if (isset($_SESSION['utilisateur'])) {
            $model->repondreU($id_candidat, $id_annonce, '');
            $_SESSION['messagerie'] = $model->getTitresConversationsU($_SESSION['utilisateur']['id_utilisateur']);
            $newCount = $model->countNouveauxMessagesU($_SESSION['utilisateur']['id_utilisateur']);
        } elseif (isset($_SESSION['admin'])) {
            $model->repondreA($id_candidat, $id_annonce, '');
            $_SESSION['messagerie'] = $model->getTitresConversationsA();
            $newCount = $model->countNouveauxMessagesA();
        } else {
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            exit;
        }

        echo json_encode([
            'success'  => true,
            'newCount' => $newCount
        ]);
        exit;
    }

    // Dans MessagerieModel.php - Modifier la méthode getMessagerie()

public function getMessagerie($id_candidat, $id_annonce) {
    $file = __DIR__ . '/../../public/conversations/conversation_' . $id_candidat . '_' . $id_annonce . '.txt';

    $conversation = [];

    if (file_exists($file)) {
        $lines = array_filter(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        foreach ($lines as $line) {
            if (preg_match('/^\[(.*?)\]\s+([^:]+):\s*(.*)$/', $line, $matches)) {
                $dateMsg = $matches[1];
                $auteurMsg = trim($matches[2]);
                $contenu = trim($matches[3]);
                
                // N'afficher que les vrais messages, pas les indicateurs de lecture
                if ($contenu !== '[LU]' && !empty($contenu)) {
                    $conversation[] = [
                        'date'    => $dateMsg,
                        'auteur'  => $auteurMsg,
                        'message' => self::sanitizeMessage($contenu) // <-- UTILISER LA MÉTHODE DE SANITISATION
                    ];
                }
            }
        }
    }

    return $conversation;
}
public function refreshConversation() {
    $model = new MessagerieModel();

    try {
        if (isset($_SESSION['utilisateur'])) {
            $_SESSION['messagerie'] = $model->getTitresConversationsU($_SESSION['utilisateur']['id_utilisateur']);
            echo json_encode(['success' => true, 'messagerie' => $_SESSION['messagerie']]);
        } elseif (isset($_SESSION['admin'])) {
            $_SESSION['messagerie'] = $model->getTitresConversationsA();
            echo json_encode(['success' => true, 'messagerie' => $_SESSION['messagerie']]);
        } else {
            echo json_encode(['success' => false]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

}