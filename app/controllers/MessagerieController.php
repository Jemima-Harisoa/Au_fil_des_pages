<?php
namespace app\controllers;

use app\models\MessagerieModel;
use app\models\AnnoncesModel;
use Flight;

class MessagerieController {
    
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
                
            } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }

    // Méthode pour obtenir le count actuel sans rafraîchir
    public function getNotificationCount() {
        $model = new MessagerieModel();
        
        try {
           // if (isset($_SESSION['utilisateur'])) {
              //  $count = $model->countNouveauxMessagesU($_SESSION['utilisateur']['id_utilisateur']);
            if (isset($_SESSION['employe'])) {
                $count = $model->countNouveauxMessagesU($_SESSION['employe']['id_employe']);
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
        } catch (\Exception $e) {
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

    public function show_messagerie_E($id_employe) {
        $model = new MessagerieModel();

        // Marque de session (optionnel) : liste des conversations
        $_SESSION['messagerie_employe'] = $model->getMessagesEmploye($id_employe);

        // Render : créer la vue 'messagerieE' si elle n'existe pas encore
        Flight::render('messagerieE', [
            'id_employe' => $id_employe,
            'conversations' => $_SESSION['messagerie_employe']
        ]);
    }

    /**
     * Endpoint AJAX : renvoie la conversation entre l'employé connecté et un partenaire.
     * Usage : GET /messagerie/convEmploye/{id_employe}/{partenaire_id}
     */
    public function getConversationEmploye($id_employe, $partenaire_id) {
        // Sécurité : l'utilisateur doit être l'employé demandé ou un admin
        $sessionId = $_SESSION['employe']['id_employe'] ?? null;
        $isAdmin = isset($_SESSION['admin']);

        if (!$isAdmin && ((int)$sessionId !== (int)$id_employe)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            exit;
        }

        $model = new MessagerieModel();

        // Optionnel : marquer comme lu la conversation pour l'employé connecté
        // $model->repondreE($id_employe, $partenaire_id, '');

        $messages = $model->getConversationEmploye((int)$id_employe, (int)$partenaire_id);

        echo json_encode([
            'success' => true,
            'messages' => $messages,
            'count' => count($messages)
        ]);
        exit;
    }

    /**
     * Endpoint pour récupérer les conversations employé (AJAX).
     */
    public function getConversationsE($id_employe) {
        $sessionId = $_SESSION['employe']['id_employe'] ?? null;
        $isAdmin = isset($_SESSION['admin']);

        if (!$isAdmin && ((int)$sessionId !== (int)$id_employe)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            exit;
        }

        $model = new MessagerieModel();
        $conversations = $model->getTitresConversationsE((int)$id_employe);
        $nbNonLus = $model->countNouveauxMessagesE((int)$id_employe);

        echo json_encode([
            'success' => true,
            'conversations' => $conversations,
            'nbNonLus' => $nbNonLus
        ]);
        exit;
    }

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

// ...existing code...
    /**
     * Endpoint pour rechercher des employés (AJAX).
     */
    public function searchEmployes($id_employe) {
        $sessionId = $_SESSION['employe']['id_employe'] ?? null;
        $isAdmin = isset($_SESSION['admin']);

        if (!$isAdmin && ((int)$sessionId !== (int)$id_employe)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            exit;
        }

        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 1) {
            echo json_encode(['success' => true, 'employes' => []]);
            exit;
        }

        $employeModel = new \app\models\EmployeModel();
        
        // Récupérer tous les employés sauf l'employé connecté
        $tousEmployes = $employeModel->getEmployeAutre((int)$id_employe);
        
        // Filtrer selon la recherche
        $query = strtolower($query);
        $resultats = array_filter($tousEmployes, function($emp) use ($query) {
            $nom = strtolower($emp['nom_personne'] ?? '');
            $prenom = strtolower($emp['prenom'] ?? '');
            $poste = strtolower($emp['poste'] ?? '');
            $dept = strtolower($emp['nom_departement'] ?? '');
            
            return strpos($nom, $query) !== false 
                || strpos($prenom, $query) !== false
                || strpos($poste, $query) !== false
                || strpos($dept, $query) !== false;
        });

        echo json_encode([
            'success' => true,
            'employes' => array_values($resultats)
        ]);
        exit;
    }
// ...existing code...
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
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

    /**
     * Afficher la conversation entre deux employés.
     */
    public function showMessagerieE($id_employe, $partenaire_id) {
        $sessionId = $_SESSION['employe']['id_employe'] ?? null;
        $isAdmin = isset($_SESSION['admin']);

        if (!$isAdmin && ((int)$sessionId !== (int)$id_employe)) {
            Flight::redirect('/employe');
            return;
        }

        $model = new MessagerieModel();
        $employeModel = new \app\models\EmployeModel();
        
        // Marquer comme lu
        $model->repondreE((int)$id_employe, (int)$partenaire_id, '');
        
        // Récupérer les messages
        $messages = $model->getConversationEmploye((int)$id_employe, (int)$partenaire_id);
        
        // Récupérer les infos du partenaire
        $infosPartenaire = $employeModel->getInfosEmploye((int)$partenaire_id);
        
        // Mettre à jour le compteur dans la session
        $_SESSION['nbNonLus'] = $model->countNouveauxMessagesE((int)$id_employe);
        
        Flight::render('messagerieE', [
            'messages' => $messages,
            'partenaire_id' => $partenaire_id,
            'partenaire_nom' => $infosPartenaire['nom'] ?? 'Inconnu',
            'partenaire_prenom' => $infosPartenaire['prenom'] ?? ''
        ]);
    }
    
    /**
     * Envoyer un message entre employés (POST).
     */
    public function sendMessageE() {
        $id_employe = $_POST['id_employe'] ?? null;
        $partenaire_id = $_POST['partenaire_id'] ?? null;
        $message = $_POST['message'] ?? '';

        $sessionId = $_SESSION['employe']['id_employe'] ?? null;
        
        if (!$sessionId || (int)$sessionId !== (int)$id_employe) {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        if (empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Message vide']);
            exit;
        }

        $model = new MessagerieModel();
        $result = $model->repondreE((int)$id_employe, (int)$partenaire_id, $message);

        echo json_encode(['success' => $result]);
        exit;
    }

}