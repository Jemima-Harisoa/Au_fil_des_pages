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
        $_SESSION['messagerie'] = $model->getTitresConversationsU($_SESSION['utilisateur']['id_utilisateur']);

        // Calcul du badge avec la nouvelle méthode
        $nbNonLus = $model->countNouveauxMessagesU($_SESSION['utilisateur']['id_utilisateur']);

        // Rendu avec badge correct
        Flight::render('messagerieU', [
            'messages'     => $messages,
            'id_candidat'  => $id_candidat,
            'id_annonce'   => $id_annonce,
            'titre'        => $titre,
            'nbNonLus'     => $nbNonLus
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
                'newCount' => $newCount
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

/**
 * Version améliorée de sanitizeMessage pour supporter plus de balises HTML
 */
public static function sanitizeMessage($message) {
    // D'abord, échapper tout le HTML
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    
    // Patterns pour différents types de balises autorisées
    $patterns = [
        // Liens avec href
        '/&lt;a\s+href=([\'&quot;])([^\'&quot;]+)\1[^&gt;]*&gt;([^&lt;]*)&lt;\/a&gt;/i',
        // Liens sans texte (self-closing ou vides)
        '/&lt;a\s+href=([\'&quot;])([^\'&quot;]+)\1[^&gt;]*&gt;&lt;\/a&gt;/i',
        // Support pour d'autres balises si nécessaire (gras, italique, etc.)
        '/&lt;(strong|b)&gt;([^&lt;]+)&lt;\/\1&gt;/i',
        '/&lt;(em|i)&gt;([^&lt;]+)&lt;\/\1&gt;/i',
    ];
    
    $replacements = [
        // Remplacer les liens avec texte
        function($matches) {
            $quote = $matches[1];
            $url = $matches[2];
            $text = $matches[3];
            
            if (self::isValidUrl($url)) {
                // Si pas de texte, utiliser l'URL comme texte
                $linkText = !empty($text) ? htmlspecialchars($text, ENT_QUOTES) : htmlspecialchars($url, ENT_QUOTES);
                return '<a href="' . htmlspecialchars($url, ENT_QUOTES) . '" target="_blank" rel="noopener noreferrer">' . $linkText . '</a>';
            } else {
                return htmlspecialchars($text ?: $url, ENT_QUOTES);
            }
        },
        // Remplacer les liens sans texte
        function($matches) {
            $quote = $matches[1];
            $url = $matches[2];
            
            if (self::isValidUrl($url)) {
                return '<a href="' . htmlspecialchars($url, ENT_QUOTES) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($url, ENT_QUOTES) . '</a>';
            } else {
                return htmlspecialchars($url, ENT_QUOTES);
            }
        },
        // Gras
        '<$1>$2</$1>',
        // Italique  
        '<$1>$2</$1>',
    ];
    
    // Appliquer les patterns avec callbacks pour les liens
    $message = preg_replace_callback($patterns[0], $replacements[0], $message);
    $message = preg_replace_callback($patterns[1], $replacements[1], $message);
    
    // Appliquer les autres patterns simples
    for ($i = 2; $i < count($patterns); $i++) {
        $message = preg_replace($patterns[$i], $replacements[$i], $message);
    }
    
    // Convertir les retours à la ligne
    return nl2br($message);
}

/**
 * Version améliorée de isValidUrl pour supporter les chemins relatifs
 */
private static function isValidUrl($url) {
    // Nettoyer l'URL des entités HTML si présentes
    $url = html_entity_decode($url, ENT_QUOTES, 'UTF-8');
    
    // Autoriser les URLs relatives (commençant par /)
    if (strpos($url, '/') === 0) {
        return true;
    }
    
    // Autoriser les chemins relatifs (sans / au début)
    if (!preg_match('/^[a-z]+:\/\//i', $url)) {
        // Vérifier que c'est un chemin valide (pas de caractères dangereux)
        if (preg_match('/^[a-zA-Z0-9\/_.-]+$/', $url)) {
            return true;
        }
    }
    
    // Autoriser les URLs HTTP/HTTPS
    if (filter_var($url, FILTER_VALIDATE_URL) && 
        (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0)) {
        return true;
    }
    
    return false;
}
}