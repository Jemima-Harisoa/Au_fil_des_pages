<?php

namespace app\models;
 
use PDO;
use PDOException;
use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
Flight::map('db', function() {
    return new PDO(
        'pgsql:host=localhost;port=5432;dbname=aufildespages', // DSN PostgreSQL
        'postgres',        // ton user PostgreSQL
        'postgres' // ton mot de passe PostgreSQL
    );
});

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
        $sql = "SELECT candidats.id_candidat, annonces.id_annonce, annonces.lien, personnes.nom, personnes.prenom , personnes.lien_image
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
                'nouveaux_messages' => $nouveauxMessages,
                'lien_image' => $row['lien_image'],

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

        // Fonction pour détecter et styliser les liens dans les messages
    public function styliserLiens($message, $urlAffichage ) {
        $pattern = '/((https?:\/\/[^\s]+)|(\/[a-zA-Z0-9\/._-][^\s]*))/i';
        
        $messageAvecLiens = preg_replace_callback($pattern, function($matches) use ($urlAffichage) {
            $raw = $matches[0];
            // Escape displayed text and href values
            $display = htmlspecialchars($raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            
            // Relative path -> build full URL safely
            if (strpos($raw, '/') === 0 && !preg_match('/^\/\/[^\/]/', $raw)) {
                $baseUrl = Flight::request()->base ?? '';
                $href = htmlspecialchars($baseUrl . $raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            } else {
                $href = htmlspecialchars($raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            }
            
            $label = ($urlAffichage ?? 'Voir le lien');
            return '<a href="' . $href . '" target="_blank" class="btn btn-primary btn-sm message-lien" style="padding: 4px 12px; margin: 2px; display: inline-block; text-decoration: none;">' . htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
        }, $message);
        
        return $messageAvecLiens;
    }

    // Envoi/lecture entre employés : fichier unique par paire (ordre croissant des ids)
    public function repondreE($id_employe_envoyeur, $id_employe_envoye, $message) {
        $dir = __DIR__ . '/../../public/conversations_employe';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Fichier commun pour la paire d'employés (ordre stable)
        $a = (int)$id_employe_envoyeur;
        $b = (int)$id_employe_envoye;
        if ($a === $b) $b = $a; // autoriser conversation avec soi-même si besoin
        $min = min($a, $b);
        $max = max($a, $b);

        $file = $dir . '/conversation_emp_' . $min . '_' . $max . '.txt';
        $date = date('Y-m-d H:i:s');

        // Auteur marqué avec l'id pour pouvoir distinguer
        $authorTag = 'Employe' . $id_employe_envoyeur;

        if (empty(trim($message))) {
            // message vide = indicateur de lecture
            $log = "[$date] $authorTag: [LU]\n";
        } else {
            // vrai message
            $log = "[$date] $authorTag: $message\n";
        }

        file_put_contents($file, $log, FILE_APPEND | LOCK_EX);
        return true;
    }

    /**
     * Retourne toutes les conversations (par partenaire) pour un employé.
     * Format renvoyé : [
     *   ['partenaire_id' => X, 'file' => '...', 'messages' => [ ['date','auteur','message'], ... ] ],
     *   ...
     * ]
     */
    public function getMessagesEmploye($id_employe) {
        $dir = __DIR__ . '/../../public/conversations_employe';
        $conversations = [];

        if (!is_dir($dir)) return $conversations;

        $files = glob($dir . '/conversation_emp_*.txt');
        foreach ($files as $file) {
            // extraire les deux ids depuis le nom : conversation_emp_{a}_{b}.txt
            if (preg_match('/conversation_emp_(\d+)_(\d+)\.txt$/', $file, $m)) {
                $a = (int)$m[1];
                $b = (int)$m[2];

                if ($a !== (int)$id_employe && $b !== (int)$id_employe) {
                    continue; // fichier ne concerne pas cet employé
                }

                $partenaire = ($a === (int)$id_employe) ? $b : $a;
                $lines = array_filter(file($file, FILE_IGNORE_NEW_LINES));
                $messages = [];

                foreach ($lines as $line) {
                    if (preg_match('/^\[(.*?)\]\s+([^:]+):\s*(.*)$/', $line, $parts)) {
                        $dateMsg = $parts[1];
                        $auteurMsg = trim($parts[2]); // ex: Employe12
                        $contenu = trim($parts[3]);

                        // N'afficher que les vrais messages (pas [LU]) ; laisser HTML tel quel
                        if ($contenu !== '[LU]' && $contenu !== '') {
                            $messages[] = [
                                'date' => $dateMsg,
                                'auteur' => $auteurMsg,
                                'message' => $contenu
                            ];
                        }
                    }
                }

                $conversations[] = [
                    'partenaire_id' => $partenaire,
                    'file' => $file,
                    'messages' => $messages
                ];
            }
        }

        // trier par date dernière modification du fichier (décroissant)
        usort($conversations, function($x, $y) {
            $tx = file_exists($x['file']) ? filemtime($x['file']) : 0;
            $ty = file_exists($y['file']) ? filemtime($y['file']) : 0;
            return $ty - $tx;
        });

        return $conversations;
    }

    /**
     * Retourne tous les messages d'une conversation entre deux employés (exclut les marqueurs [LU]).
     *
     * @param int $id_employe       employé connecté (pour repérer le partenaire)
     * @param int $partenaire_id    autre employé de la conversation
     * @return array                [{date, auteur, message}, ...]
     */
    public function getConversationEmploye(int $id_employe, int $partenaire_id): array {
        $a = (int)$id_employe;
        $b = (int)$partenaire_id;
        $min = min($a, $b);
        $max = max($a, $b);

        $file = __DIR__ . '/../../public/conversations_employe/conversation_emp_' . $min . '_' . $max . '.txt';
        $messages = [];

        if (!file_exists($file)) {
            return $messages;
        }

        $lines = array_filter(file($file, FILE_IGNORE_NEW_LINES));
        foreach ($lines as $line) {
            if (preg_match('/^\[(.*?)\]\s+([^:]+):\s*(.*)$/', $line, $m)) {
                $date = $m[1];
                $auteur = trim($m[2]); // ex: Employe12
                $contenu = trim($m[3]);

                // Ignorer les marqueurs de lecture
                if ($contenu === '[LU]' || $contenu === '') continue;

                $messages[] = [
                    'date' => $date,
                    'auteur' => $auteur,
                    'message' => $contenu
                ];
            }
        }

        return $messages;
    }

    /**
     * Retourne les conversations employé avec métadonnées (nom du partenaire, dernier message, etc.).
     * 
     * @param int $id_employe
     * @return array [{partenaire_id, nom_partenaire, prenom_partenaire, derniere_modif, dernier_auteur, nouveaux_messages}, ...]
     */
    public function getTitresConversationsE(int $id_employe): array {
        $dir = __DIR__ . '/../../public/conversations_employe';
        $conversations = [];

        if (!is_dir($dir)) return $conversations;

        $files = glob($dir . '/conversation_emp_*.txt');
        $employeModel = new EmployeModel(); // pour récupérer les infos du partenaire

        foreach ($files as $file) {
            if (preg_match('/conversation_emp_(\d+)_(\d+)\.txt$/', $file, $m)) {
                $a = (int)$m[1];
                $b = (int)$m[2];

                if ($a !== (int)$id_employe && $b !== (int)$id_employe) {
                    continue; // pas concerné
                }

                $partenaire_id = ($a === (int)$id_employe) ? $b : $a;

                // Récupérer infos du partenaire
                $infosPartenaire = $employeModel->getInfosEmploye($partenaire_id);
                $nomPartenaire = $infosPartenaire['nom'] ?? 'Inconnu';
                $prenomPartenaire = $infosPartenaire['prenom'] ?? '';

                // Dernière modification du fichier
                $derniere_modif = file_exists($file) ? date('Y-m-d H:i:s', filemtime($file)) : null;

                // Dernier auteur (vrai message, pas [LU])
                $dernierAuteur = 'Inconnu';
                if (file_exists($file)) {
                    $lines = array_reverse(array_filter(file($file, FILE_IGNORE_NEW_LINES)));
                    foreach ($lines as $line) {
                        if (preg_match('/^\[(.*?)\]\s+([^:]+):\s*(.*)$/', $line, $matches)) {
                            $auteurMsg = trim($matches[2]);
                            $contenu = trim($matches[3]);
                            if ($contenu !== '[LU]' && $contenu !== '') {
                                $dernierAuteur = $auteurMsg;
                                break;
                            }
                        }
                    }
                }

                // Vérifier si non lu : dernier message vient du partenaire et pas encore de [LU] de ma part après
                $nouveauxMessages = $this->aNouveauxMessagesE($id_employe, $partenaire_id);

                $conversations[] = [
                    'partenaire_id' => $partenaire_id,
                    'nom_partenaire' => $nomPartenaire,
                    'prenom_partenaire' => $prenomPartenaire,
                    'derniere_modification' => $derniere_modif,
                    'dernier_auteur' => $dernierAuteur,
                    'nouveaux_messages' => $nouveauxMessages
                ];
            }
        }

        // Trier par date de dernière modification (décroissant)
        usort($conversations, function($x, $y) {
            return strtotime($y['derniere_modification'] ?? '1970-01-01') - strtotime($x['derniere_modification'] ?? '1970-01-01');
        });

        return $conversations;
    }

    /**
     * Indique si la conversation avec un partenaire a des messages non lus pour id_employe.
     */
    private function aNouveauxMessagesE(int $id_employe, int $partenaire_id): bool {
        $a = (int)$id_employe;
        $b = (int)$partenaire_id;
        $min = min($a, $b);
        $max = max($a, $b);

        $file = __DIR__ . '/../../public/conversations_employe/conversation_emp_' . $min . '_' . $max . '.txt';

        if (!file_exists($file)) return false;

        $lines = array_filter(file($file, FILE_IGNORE_NEW_LINES));
        $dernierMessagePartenaire = null;
        $derniereLectureMoi = null;

        $auteurPartenaire = 'Employe' . $partenaire_id;
        $auteurMoi = 'Employe' . $id_employe;

        foreach ($lines as $line) {
            if (preg_match('/^\[(.*?)\]\s+([^:]+):\s*(.*)$/', $line, $m)) {
                $dateMsg = $m[1];
                $auteurMsg = trim($m[2]);
                $contenu = trim($m[3]);

                if ($auteurMsg === $auteurPartenaire && $contenu !== '[LU]' && $contenu !== '') {
                    $dernierMessagePartenaire = $dateMsg;
                }

                if ($auteurMsg === $auteurMoi && $contenu === '[LU]') {
                    $derniereLectureMoi = $dateMsg;
                }
            }
        }

        if (!$dernierMessagePartenaire) return false;
        if (!$derniereLectureMoi) return true;

        return strtotime($dernierMessagePartenaire) > strtotime($derniereLectureMoi);
    }

    /**
     * Compte les conversations employé non lues.
     */
    public function countNouveauxMessagesE(int $id_employe): int {
        $convs = $this->getTitresConversationsE($id_employe);
        $count = 0;
        foreach ($convs as $c) {
            if ($c['nouveaux_messages']) $count++;
        }
        return $count;
    }
}