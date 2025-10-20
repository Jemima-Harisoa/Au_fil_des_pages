<?php
namespace app\controllers\migration;

use Flight;
use app\models\migration\PersonneModel;
use app\models\migration\CandidatModel;
use app\models\migration\ScoringModel;
use app\models\migration\TypeContratModel;
use app\models\migration\ContratModel;
use app\models\migration\HistoriqueValidationModel;
use app\models\migration\HistoriqueContratModel;
use app\models\migration\ValidationContratModel;
use app\models\ProfilsModel;
use app\models\EtatModel;
use app\models\ConnexionModel;
use app\models\MessagerieModel;

class MigrationController {
    
    /**
     * Vérifie qu'une session admin est présente et active.
     */
    private function requireAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['admin']) || !is_array($_SESSION['admin'])) {
            $msg = urlencode("Accès réservé aux administrateurs. Veuillez vous connecter.");
            Flight::redirect("/admin?msg={$msg}&msg_type=warning");
            return false;
        }

        $connModel = new ConnexionModel(Flight::db());
        $adminSession = $_SESSION['admin'];

        $nom = $adminSession['nom'] ?? null;
        $mdp = $adminSession['mdp'] ?? null;

        if (!$nom || !$mdp || !$connModel->verifierAdmin($nom, $mdp)) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();

            $msg = urlencode("Session administrateur invalide ou expirée. Veuillez vous reconnecter.");
            Flight::redirect("/admin?msg={$msg}&msg_type=warning");
            return false;
        }

        return true;
    }

    /**
     * 🔹 ENVOYER POUR VALIDATION - CONSERVÉE DANS MIGRATIONCONTROLLER
     */
    public function envoyerValidation($id_contrat_param = null) {
        if (!$this->requireAdmin()) return;

        // ✅ Si la fonction est appelée depuis registerContrat
        $id_contrat = $id_contrat_param ?? (Flight::request()->query['id'] ?? null);
        $note = Flight::request()->data->note ?? '';

        if (!$id_contrat) {
            Flight::halt(400, "ID du contrat manquant");
            return;
        }

        // Utilisation de ValidationController pour les vérifications
        $validationController = new ValidationController();
        
        if (!$validationController->verifierAccesContrat($id_contrat)) {
            $msg = urlencode("Accès non autorisé à ce contrat.");
            Flight::redirect("/migration/contrats?msg={$msg}&msg_type=error");
            return;
        }

        $validationModel = new ValidationContratModel(Flight::db());
        $role_utilisateur = $validationController->getRoleUtilisateur();

        if (!$validationModel->verifierPermission($id_contrat, $role_utilisateur)) {
            $msg = urlencode("Vous n'avez pas la permission de valider cette étape.");
            Flight::redirect("/migration/contrat/edit?id={$id_contrat}&msg={$msg}&msg_type=error");
            return;
        }

        try {
            $prochain_statut = $validationModel->envoyerPourValidation(
                $id_contrat,
                $_SESSION['admin']['id_employe'] ?? null,
                $note
            );

            // ✅ Notification au prochain validateur
            $validationController->notifierProchaineEtape($id_contrat, $prochain_statut);

            // ✅ Message au candidat comme avant
            $messagerieModel = new MessagerieModel();
            $contratModel = Flight::Contrat();
            $candidatModel = Flight::Candidat();

            $contrat = $contratModel->getBy('id_contrat', $id_contrat);
            $candidat = $candidatModel->getBy('id_candidat', $contrat['id_candidat']);

            $lien_contrat = "/migration/contrat/edit?id={$id_contrat}";
            $formated_link = $messagerieModel->styliserLiens($lien_contrat, "Voir le contrat");
            $messagerieModel->repondreA($candidat['id_candidat'], $candidat['id_annonce'], 
                "📄 Votre contrat a été validé et envoyé pour la prochaine étape. {$formated_link}");

            // ✅ Redirection si appel via route HTTP
            if (!$id_contrat_param) {
                $msg = urlencode("Contrat envoyé pour validation (nouveau statut : {$prochain_statut}).");
                Flight::redirect("/migration/contrat/edit?id={$id_contrat}&msg={$msg}&msg_type=success");
            }

        } catch (\Exception $e) {
            $msg = urlencode("Erreur lors de l'envoi : " . $e->getMessage());
            Flight::redirect("/migration/contrat/edit?id={$id_contrat}&msg={$msg}&msg_type=error");
        }
    }

    // Redirection vers la pages d'edition du contrat
    public function editContrat() {
        // if (!$this->requireAdmin()) return; pas besoin car l'utilisateur peut valider

        $id_contrat = Flight::request()->query['id'] ?? null;

        if (!$id_contrat) {
            Flight::halt(400, "ID du contrat manquant !");
            return;
        }

        // Utilisation de ValidationController pour vérifier l'accès
        $validationController = new ValidationController();
        if (!$validationController->verifierAccesContrat($id_contrat)) {
            $msg = urlencode("Accès non autorisé à ce contrat.");
            Flight::redirect("/migration/contrats?msg={$msg}&msg_type=error");
            return;
        }

        // Modèles
        $contratModel   = Flight::Contrat();
        $candidatModel  = Flight::Candidat();
        $personneModel  = Flight::Personne();
        $typeContratModel = Flight::TypeContrat();

        // Récupérer le contrat
        $contrat = $contratModel->getBy('id_contrat', $id_contrat);
        if (!$contrat) {
            Flight::halt(404, "Contrat non trouvé !");
            return;
        }

        // Récupérer infos candidat / personne
        $candidat = $candidatModel->getBy('id_candidat', $contrat['id_candidat'] ?? null);
        $personne = $personneModel->getBy('id_personne', $candidat['id_personne'] ?? null);

        // Liste types de contrat
        $typeContrats = $typeContratModel->list();

        // Charger le contrat modèle JSON
        $modele = [];
        if (!empty($contrat['url_contrat'])) {
            $modelePath = realpath(__DIR__ . "/../../../public" . $contrat['url_contrat']);
            if ($modelePath && file_exists($modelePath)) {
                $modele = json_decode(file_get_contents($modelePath), true) ?: [];
            }
        }

        // Extraire la partie employe depuis le modele
        $employeFromModele = $modele['employe'] ?? [];

        // Construire le tableau $data
        $data = [
            'contrat' => $contrat,
            'candidat' => $candidat,
            'personne' => $personne,
            'type_contrats' => $typeContrats,
            'modele' => $modele,
            'employe' => $employeFromModele,
            'profil' => null
        ];
        
        // Utilisation de ValidationController pour les informations de validation
        $infosValidation = $validationController->getInfosValidation($id_contrat);
        if ($infosValidation) {
            $data['statut_actuel'] = $infosValidation['statut_actuel'];
            $data['historique_validation'] = $infosValidation['historique_validation'];
            $data['peut_valider'] = $infosValidation['peut_valider'];
        }



        Flight::render('validation/form', ['data' => $data]);
    }

    // Redirection vers la liste des contrats classer par etat dynamique
    public function getContrat() {
        if (!$this->requireAdmin()) return;

        // Modèles
        $historiqueContratModel = Flight::HistoriqueContrat();
        $candidatModel = Flight::Candidat();
        $personneModel = Flight::Personne();

        // Récupérer tous les contrats (vue historique_contrat)
        $contrats = $historiqueContratModel->getAll();

        // Classer les contrats par état dynamique
        $contratsParEtat = [];

        foreach ($contrats as $contrat) {
            // Récupérer les infos candidat
            $candidat = $candidatModel->getBy('id_candidat', $contrat['id_candidat']);
            $cv_url = $candidat['cv_url'] ?? '';

            // Récupérer contact depuis la personne
            $personne = $personneModel->getBy('id_personne', $candidat['id_personne'] ?? null);
            $contact = $personne['contact'] ?? '';

            // Ajouter les infos au contrat
            $contrat['cv'] = $cv_url;
            $contrat['contact'] = $contact;

            $etat = $contrat['etat'] ?? 'En attente de validation';
            if (!isset($contratsParEtat[$etat])) {
                $contratsParEtat[$etat] = [];
            }
            $contratsParEtat[$etat][] = $contrat;
        }

        Flight::render('validation/listContrat', [
            'contratsParEtat' => $contratsParEtat
        ]);
    }

    // Enregistrement / action sur contrat (création JSON puis sauvegarde / validation / refus / attente)
    public function registerContrat() {
        if (!$this->requireAdmin()) return;

        $id_candidat = Flight::request()->query['id_candidat'] ?? null;
        if (!$id_candidat) {
            Flight::halt(400, "ID du candidat manquant.");
            return;
        }

        // Récupération des données du formulaire (POST)
        $req  = Flight::request();
        $data = is_object($req->data) ? $req->data->getData() : (array)$req->data;

        // Modèles
        $personneModel    = Flight::Personne();
        $candidatModel    = Flight::Candidat();
        $contratModel     = Flight::Contrat();
        $historiqueModel  = Flight::HistoriqueValidation();
        $etatModel        = Flight::Etat();
        $typeContrats     = Flight::TypeContrat();

        // Vérification du candidat
        $candidat = $candidatModel->getBy('id_candidat', $id_candidat);
        $personne = $personneModel->getBy('id_personne', $candidat['id_candidat']);

        if (!$personne || !$candidat) {
            Flight::halt(404, "Candidat non trouvé.");
            return;
        }

        // --- 1) vérifier en base s'il existe déjà un contrat pour ce candidat ---
        $existingContrat = $contratModel->getBy('id_candidat', $id_candidat);

        // Préparer chemins
        $basePath   = realpath(__DIR__ . "/../../../public/json/contrats/contrat_travail");
        if ($basePath === false) {
            $msg = urlencode("Répertoire de stockage introuvable.");
            Flight::redirect("/migration/contrat/create?id={$id_candidat}&msg={$msg}&msg_type=error");
            return;
        }
        $dir = $basePath . "/candidat_" . $id_candidat;

        $createdNewFile = false;
        $url_contrat = null;

        // --- 2) Si pas de contrat en base => on crée un fichier JSON horodaté et on insert en base ---
        if (!$existingContrat) {
            // Charger modèle
            $modelePath = realpath(__DIR__ . "/../../../public/json/contrats/contrat_travail/contrat_exemple.json");
            if (!$modelePath || !file_exists($modelePath)) {
                $msg = urlencode("Fichier modèle introuvable.");
                Flight::redirect("/migration/contrat/create?id={$id_candidat}&msg={$msg}&msg_type=error");
                return;
            }
            $modele = json_decode(file_get_contents($modelePath), true);
            if (!$modele) {
                $msg = urlencode("Erreur lors du chargement du modèle de contrat.");
                Flight::redirect("/migration/contrat/create?id={$id_candidat}&msg={$msg}&msg_type=error");
                return;
            }

            // Remplissage du modèle avec données candidat
            $type = $typeContrats->getBy("nom", "CDD");
            $modele["employe"] = [
                "resilliation" => $data['typeContrat'] ?? $type["id_type_contrat"] ?? null,
                "modalite" => [
                    "debut_contrat" => $data['dateDebut'] ?? '',
                    "duree"         => $data['dureeCDD'] ?? null,
                    "essai"         => $data['essai'] ?? null
                ],
                "lieu" => $data['lieuEmploi'] ?? '',
                "poste" => [
                    "qualite" => $data['poste'] ?? '',
                    "class"   => $data['classification'] ?? ''
                ],
                "remuneration" => [
                    "salaire"   => $data['salaire'] ?? '',
                    "avantages" => isset($data['avantages'])
                        ? (is_array($data['avantages'])
                            ? array_map('trim', $data['avantages'])
                            : array_map('trim', explode(',', $data['avantages'])))
                        : []
                ],
                "noms_prenoms"     => "{$personne['nom']} {$personne['prenom']}",
                "ne_le"            => $data['dateNaissance'] ?? '',
                "ne_a"             => $data['lieuNaissance'] ?? '',
                "fils_ou_fille_de" => $data['parents'] ?? '',
                "nationalite"      => $data['nationalite'] ?? '',
                "domicile"         => $data['domicile'] ?? '',
                "lieu_edition"     => $data['lieuEdition'] ?? 'Antananarivo',
                "date_edition"     => date('d/m/Y'),
                "signature"        => $data['signature'] ?? "{$personne['nom']} {$personne['prenom']}"
            ];

            // Créer dossier si absent (ne bloque pas si présent)
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                    $msg = urlencode("Impossible de créer le dossier du candidat.");
                    Flight::redirect("/migration/contrat/create?id={$id_candidat}&msg={$msg}&msg_type=error");
                    return;
                }
            }

            // Nom du fichier JSON horodaté
            $file = $dir . "/contrat_" . date('Ymd_His') . ".json";

            if (!file_put_contents($file, json_encode($modele, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
                $msg = urlencode("Erreur lors de l'enregistrement du fichier JSON.");
                Flight::redirect("/migration/contrat/create?id={$id_candidat}&msg={$msg}&msg_type=error");
                return;
            }

            // Construire url_contrat relative (chemin utilisé en base)
            $publicDir = DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR;
            $pos = strpos($file, $publicDir);
            $url_contrat = ($pos !== false) ? substr($file, $pos) : $file;

            // Insérer contrat en base
            $contratData = [
                "id_candidat"     => $id_candidat,
                "id_type_contrat" => $data["typeContrat"] ?? null,
                "url_contrat"     => $url_contrat
            ];
            $contratModel->save($contratData);

            // récupérer l'enregistrement inséré
            $existingContrat = $contratModel->getBy('url_contrat', $url_contrat);
            $createdNewFile = true;
        } else {
            // Si contrat existant en base, réutiliser son url
            $url_contrat = $existingContrat['url_contrat'] ?? null;
        }

        // --- 3) à partir d'ici, on a $existingContrat et $id_contrat (ou null si pb) ---
        $id_contrat = $existingContrat['id_contrat'] ?? null;
        
        // INITIALISER le statut "draft" si nouveau contrat
        if ($createdNewFile && $id_contrat) {
            // Utilisation de ValidationController pour initialiser le statut
            $validationController = new ValidationController();
            $validationController->initialiserStatutDraft(
                $id_contrat,
                $_SESSION['admin']['id_employe'] ?? null,
                'Création du contrat (brouillon)'
            );
        }

        // Déterminer l'état en fonction du bouton cliqué (action)
        $action = $data['action'] ?? null;
        switch ($action) {
            case 'valider':
                $etat = $etatModel->getBy("nom", "Validé");
                if (!$etat) {
                    $etatModel->save(['nom' => 'Validé']);
                    $etat = $etatModel->getBy("nom", "Validé"); 
                }
                $actionLabel = "Validation";

                // Appel de la méthode d’envoi pour validation
                $migrationController = new MigrationController();
                $migrationController->envoyerValidation($id_contrat);
                break;
            case 'refuser':
                $etat = $etatModel->getBy("nom", "Non validé");
                if (!$etat) { $etatModel->save(['nom'=>'Non validé']); $etat = $etatModel->getBy("nom","Non validé"); }
                $actionLabel = "Refus";
                break;
            case 'attente':
            default:
                $etat = $etatModel->getBy("nom", "En attente de validation");
                if (!$etat) { $etatModel->save(['nom'=>'En attente de validation']); $etat = $etatModel->getBy("nom","En attente de validation"); }
                $actionLabel = "Mise en attente";
                break;
        }

        // Enregistrer l'historique (si état disponible)
        if (!empty($etat) && isset($etat['id_etat'])) {
            $historiqueData = [
                'id_employe'            => $_SESSION['admin']['id_employe'] ?? $id_candidat, // préférence : id de l'admin connecté
                'id_candidat'           => $id_candidat,
                'date_heure_validation' => date('Y-m-d H:i:s'),
                'id_etat'               => $etat['id_etat']
            ];
            $historiqueModel->save($historiqueData);
        }

        // Construire message pour redirection
        $msgParts = [];
        $msgParts[] = $createdNewFile ? "Contrat créé" : "Contrat réutilisé";
        $msgParts[] = strtolower($actionLabel) . " effectuée";
        $msg = urlencode(implode(" et ", $msgParts) . " avec succès.");

        // Redirection vers l'édition du contrat (si on a un id_contrat)
        if ($id_contrat) {
            Flight::redirect("/migration/contrat/edit?id={$id_contrat}&msg={$msg}&msg_type=success");
        } else {
            Flight::redirect("/migration/contrat/create?id={$id_candidat}&msg={$msg}&msg_type=info");
        }
    }

    // Redirection vers la page de generation de contrat 
    public function createContrat() {
        if (!$this->requireAdmin()) return;

        $id_candidat = Flight::request()->query['id'] ?? null;

        // Modèles
        $personneModel     = Flight::Personne();
        $candidatModel     = Flight::Candidat();
        $contratModel      = Flight::Contrat();
        $typeContratModel  = Flight::TypeContrat();
        $etatModel         = Flight::Etat();
        $employeModel      = Flight::Employe();      // ✅ nouveau
        $departementModel  = Flight::Departement();  // ✅ nouveau

        // Récupérer la liste des employés et départements
        $listeEmployes     = $employeModel->listWithDetails();
        $listeDepartements = $departementModel->list();

        if (!$id_candidat) {
            Flight::render('migration/form', [
                'data' => [
                    'liste_employes' => $listeEmployes,
                    'liste_departements' => $listeDepartements
                ]
            ]);
            return;
        }

        // Récupérer le candidat
        $candidat = $candidatModel->getBy('id_candidat', $id_candidat);

        // Si candidat non trouvé → formulaire vide
        if (!$candidat) {
            Flight::render('migration/form', [
                'data' => [
                    'liste_employes' => $listeEmployes,
                    'liste_departements' => $listeDepartements
                ]
            ]);
            return;
        }

        // Récupérer la personne
        $personne = $personneModel->getBy('id_personne', $candidat['id_personne']);

        // Récupérer la liste des types et états
        $typeContrats = $typeContratModel->list();
        $etats = $etatModel->list();

        $data = [
            'candidat' => $candidat,
            'personne' => $personne,
            'type_contrats' => $typeContrats,
            'etats' => $etats,
            'liste_employes' => $listeEmployes,        // ✅ ajouté ici
            'liste_departements' => $listeDepartements // ✅ ajouté ici
        ];

        Flight::render('migration/form', ['data' => $data]);
    }

    // Redirection vers la page de résultat des tests et entretiens
    public function getCandidatRetenu() {
        if (!$this->requireAdmin()) return;

        $personneModel = Flight::Personne();
        $candidatModel = Flight::Candidat();
        $scoringModel  = Flight::Scoring();
        $contratModel  = Flight::Contrat();

        // Récupération des données
        $candidats = $candidatModel->list();
        $personnes = $personneModel->list();
        $scoring   = $scoringModel->list();
        $contrats  = $contratModel->list();

        // On mappe les données ensemble
        $rows = [];
        foreach ($candidats as $cand) {
            $pers = array_filter($personnes, fn($p) => $p['id_personne'] == $cand['id_personne']);
            $pers = reset($pers);

            $score = array_filter($scoring, fn($s) => $s['id_candidat'] == $cand['id_candidat']);
            $score = reset($score);

            // Vérification si un contrat existe déjà pour ce candidat
            $contrat = array_filter($contrats, fn($c) => $c['id_candidat'] == $cand['id_candidat']);
            $contrat = reset($contrat);

            if ($contrat) {
                // Contrat déjà généré → lien de consultation
                $contratUrl = ".." . $contrat['url_contrat'];
                $contratLabel = "Voir Contrat";
                $contratBtnClass = "btn-primary";
            } else {
                // Pas encore de contrat → lien de génération
                $contratUrl = '/migration/contrat/create?id=' . $cand['id_candidat'];
                $contratLabel = "Générer Contrat";
                $contratBtnClass = "btn-success";
            }

            $rows[] = [
                'id_personne'  => $pers['id_personne'],
                'nom'       => $pers['nom'] ?? '',
                'poste'     => $cand['poste'] ?? '',
                'contact'   => $pers['contact'] ?? '',
                'score_test'      => $score['score_test'] ?? null,
                'score_entretien' => $score['score_entretien'] ?? null,
                'cv'        => $cand['cv_url'] ?? '',
                'contrat_url'  => $contratUrl,
                'contrat_label'=> $contratLabel,
                'contrat_class'=> $contratBtnClass
            ];
        }

        Flight::render('migration/listCandidat', ['rows' => $rows]);
    }

    // Test des modèles (optionnel)
    public function test(){
        if (!$this->requireAdmin()) return;

        // ... logique test (inchangée)
        Flight::render('migration/test', ['data' => []]);
    }
}