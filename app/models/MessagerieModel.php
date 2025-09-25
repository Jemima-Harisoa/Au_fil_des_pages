<?php

namespace app\models;
 
use PDO;
use PDOException;
use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class MessagerieModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }
    
    public function repondre($id_candidat, $id_annonce, $message, $destinateur) {
        $dir = __DIR__ . '/../../public/conversations';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = $dir . '/conversation_' . $id_candidat . '_' . $id_annonce . '.txt';
        $date = date('Y-m-d H:i:s');
        
        // Distinguer les messages vides (lecture) des vrais messages
        if (empty(trim($message))) {
            // Message vide = indicateur de lecture
            $log = "[$date] $destinateur: [LU]\n";
        } else {
            // Vrai message - AUCUN ÉCHAPPEMENT, HTML BRUT AUTORISÉ
            $log = "[$date] $destinateur: $message\n";
        }
        
        file_put_contents($file, $log, FILE_APPEND);
        return true;
    }

    public function repondreU($id_candidat, $id_annonce, $message) {
        return $this->repondre($id_candidat, $id_annonce, $message, 'Utilisateur');
    }

    public function repondreA($id_candidat, $id_annonce, $message) {
        return $this->repondre($id_candidat, $id_annonce, $message, 'Admin');
    }

    // Version corrigée pour vérifier les nouveaux messages
    public function aNouveauxMessages($id_candidat, $id_annonce, $auteur) {
        $dir = __DIR__ . '/../../public/conversations';
        $file = $dir . '/conversation_' . $id_candidat . '_' . $id_annonce . '.txt';
        
        if (!file_exists($file)) {
            return false;
        }
        
        $lines = array_filter(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        if (empty($lines)) {
            return false;
        }
        
        // L'autre auteur (celui qui envoie les messages qu'on veut vérifier)
        $autreAuteur = ($auteur === 'Utilisateur') ? 'Admin' : 'Utilisateur';
        
        // Trouver le dernier message de l'autre auteur et la dernière lecture de l'auteur courant
        $dernierMessageAutreAuteur = null;
        $derniereLectureAuteurCourant = null;
        
        // Parcourir TOUTES les lignes pour trouver les derniers événements
        foreach ($lines as $line) {
            if (preg_match('/^\[(.*?)\]\s+([^:]+):\s*(.*)$/', $line, $matches)) {
                $dateMsg = $matches[1];
                $auteurMsg = trim($matches[2]);
                $contenu = trim($matches[3]);
                
                // Dernier vrai message de l'autre auteur
                if ($auteurMsg === $autreAuteur && $contenu !== '[LU]' && !empty($contenu)) {
                    $dernierMessageAutreAuteur = $dateMsg;
                }
                
                // Dernière lecture de l'auteur courant
                if ($auteurMsg === $auteur && $contenu === '[LU]') {
                    $derniereLectureAuteurCourant = $dateMsg;
                }
            }
        }
        
        // S'il n'y a pas de message de l'autre auteur, pas de nouveau message
        if ($dernierMessageAutreAuteur === null) {
            return false;
        }
        
        // S'il n'y a jamais eu de lecture, il y a forcément un nouveau message
        if ($derniereLectureAuteurCourant === null) {
            return true;
        }
        
        // Comparer les timestamps : nouveau message si le dernier message est après la dernière lecture
        return strtotime($dernierMessageAutreAuteur) > strtotime($derniereLectureAuteurCourant);
    }

    public function getAllMessageAutomatique() {
        $sql = "SELECT * FROM message_automatique";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMessageAutomatique($id_reponseAutomatique) {
        $sql = "SELECT * FROM message_automatique WHERE id_message_automatique = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_reponseAutomatique]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function reponseAutomatique($id_candidat, $id_annonce, $id_reponseAutomatique) {
        $msg = $this->getMessageAutomatique($id_reponseAutomatique);
        if ($msg && isset($msg['message'])) {
            return $this->repondreA($id_candidat, $id_annonce, $msg['message']);
        }
        return false;
    }

    public function getTitresConversationsU($id_utilisateur) {
        $sql = "SELECT candidats.id_candidat, annonces.id_annonce, annonces.lien 
                FROM candidats  
                JOIN annonces ON candidats.id_annonce = annonces.id_annonce 
                WHERE id_utilisateur = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_utilisateur]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dir = __DIR__ . '/../../public/conversations';
        $titres = [];

        foreach ($rows as $row) {
            $jsonPath = $_SERVER['DOCUMENT_ROOT'] . $row['lien'];
            $details = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];

            $titreAnnonce = $details['titre'] ?? 'Titre inconnu';
            $nomEntreprise = $details['nom_entreprise'] ?? 'Entreprise inconnue';

            $file = $dir . '/conversation_' . $row['id_candidat'] . '_' . $row['id_annonce'] . '.txt';
            $lastModif = file_exists($file) ? date('Y-m-d H:i:s', filemtime($file)) : null;

            // Qui a répondu en dernier (en excluant les [LU])
            $dernierAuteur = 'Inconnu';
            if (file_exists($file)) {
                $lines = array_reverse(array_filter(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
                foreach ($lines as $line) {
                    if (preg_match('/^\[(.*?)\]\s+([^:]+):\s*(.*)$/', $line, $matches)) {
                        $auteurMsg = trim($matches[2]);
                        $contenu = trim($matches[3]);
                        
                        // Ignorer les indicateurs de lecture
                        if ($contenu !== '[LU]' && !empty($contenu)) {
                            $dernierAuteur = $auteurMsg;
                            break;
                        }
                    }
                }
            }

            // Vérifier s'il y a de nouveaux messages pour cet utilisateur
            $nouveauxMessages = $this->aNouveauxMessages($row['id_candidat'], $row['id_annonce'], 'Utilisateur');

            $titres[] = [
                'id_candidat' => $row['id_candidat'],
                'id_annonce' => $row['id_annonce'],
                'titre' => $titreAnnonce,
                'derniere_modification' => $lastModif,
                'nom_entreprise' => $nomEntreprise,
                'dernier_auteur' => $dernierAuteur,
                'nouveaux_messages' => $nouveauxMessages
            ];
        }

        usort($titres, function($a, $b) {
            return strtotime($b['derniere_modification'] ?? '1970-01-01') - strtotime($a['derniere_modification'] ?? '1970-01-01');
        });

        return $titres;
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
                            'message' => $contenu // HTML BRUT - AUCUN ÉCHAPPEMENT
                        ];
                    }
                }
            }
        }

        return $conversation;
    }

    public function getTitresConversationsA() {
        $sql = "SELECT candidats.id_candidat, annonces.id_annonce, annonces.lien, personnes.nom, personnes.prenom
                FROM candidats
                JOIN annonces ON candidats.id_annonce = annonces.id_annonce
                JOIN personnes ON candidats.id_personne = personnes.id_personne";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dir = __DIR__ . '/../../public/conversations';
        $titres = [];

        foreach ($rows as $row) {
            $jsonPath = $_SERVER['DOCUMENT_ROOT'] . $row['lien'];
            $details = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];

            $titreAnnonce = $details['titre'] ?? 'Titre inconnu';

            $file = $dir . '/conversation_' . $row['id_candidat'] . '_' . $row['id_annonce'] . '.txt';
            $lastModif = file_exists($file) ? date('Y-m-d H:i:s', filemtime($file)) : null;

            // Qui a répondu en dernier (en excluant les [LU])
            $dernierAuteur = 'Inconnu';
            if (file_exists($file)) {
                $lines = array_reverse(array_filter(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
                foreach ($lines as $line) {
                    if (preg_match('/^\[(.*?)\]\s+([^:]+):\s*(.*)$/', $line, $matches)) {
                        $auteurMsg = trim($matches[2]);
                        $contenu = trim($matches[3]);
                        
                        // Ignorer les indicateurs de lecture
                        if ($contenu !== '[LU]' && !empty($contenu)) {
                            $dernierAuteur = $auteurMsg;
                            break;
                        }
                    }
                }
            }

            // Vérifier s'il y a de nouveaux messages pour cet admin
            $nouveauxMessages = $this->aNouveauxMessages($row['id_candidat'], $row['id_annonce'], 'Admin');

            $titres[] = [
                'id_candidat' => $row['id_candidat'],
                'id_annonce' => $row['id_annonce'],
                'titre' => $titreAnnonce,
                'derniere_modification' => $lastModif,
                'nom' => $row['nom'],
                'prenom' => $row['prenom'],
                'dernier_auteur' => $dernierAuteur,
                'nouveaux_messages' => $nouveauxMessages
            ];
        }

        usort($titres, function($a, $b) {
            return strtotime($b['derniere_modification'] ?? '1970-01-01') - strtotime($a['derniere_modification'] ?? '1970-01-01');
        });

        return $titres;
    }

    // Méthode pour compter les conversations avec nouveaux messages
    public function countNouveauxMessagesU($id_utilisateur) {
        $conversations = $this->getTitresConversationsU($id_utilisateur);
        $count = 0;
        foreach ($conversations as $conv) {
            if ($conv['nouveaux_messages']) {
                $count++;
            }
        }
        return $count;
    }

    public function countNouveauxMessagesA() {
        $conversations = $this->getTitresConversationsA();
        $count = 0;
        foreach ($conversations as $conv) {
            if ($conv['nouveaux_messages']) {
                $count++;
            }
        }
        return $count;
    }

    public function showMessagerieA($id_candidat, $id_annonce) {
        $model = new MessagerieModel();
        // Envoyer le message vide pour marquer comme lu
        $model->repondreA($id_candidat, $id_annonce, '');
        $messages = $model->getMessagerie($id_candidat, $id_annonce);

        $AnnoncesModel = new AnnoncesModel();
        $titre = $AnnoncesModel->get($id_annonce)['titre'];
        $_SESSION['messagerie'] = $model->getTitresConversationsA();

        Flight::render('messagerieA', [
            'messages'     => $messages,
            'id_candidat'  => $id_candidat,
            'id_annonce'   => $id_annonce,
            'titre'        => $titre
        ]);
    }
}