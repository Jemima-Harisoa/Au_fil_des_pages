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

    // Méthode pour rafraîchir les notifications en temps réel
    public function refreshNotifications() {
        $model = new MessagerieModel();
        
        try {
            if (isset($_SESSION['utilisateur'])) {
                $_SESSION['messagerie'] = $model->getTitresConversationsU($_SESSION['utilisateur']['id_utilisateur']);
                $count = $model->countNouveauxMessagesU($_SESSION['utilisateur']['id_utilisateur']);
                
                echo json_encode([
                    'success' => true, 
                    'type' => 'utilisateur',
                    'count' => $count,
                    'conversations' => $_SESSION['messagerie']
                ]);
            } elseif (isset($_SESSION['admin'])) {
                $_SESSION['messagerie'] = $model->getTitresConversationsA();
                $count = $model->countNouveauxMessagesA();
                
                echo json_encode([
                    'success' => true, 
                    'type' => 'admin',
                    'count' => $count,
                    'conversations' => $_SESSION['messagerie']
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
                    'type' => 'utilisateur'
                ]);
            } elseif (isset($_SESSION['admin'])) {
                $count = $model->countNouveauxMessagesA();
                echo json_encode([
                    'success' => true, 
                    'count' => $count, 
                    'type' => 'admin'
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
}