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

use app\models\ProfilsModel;
use app\models\EtatModel;


class MigrationController {
    // Redirection vers la pages d'edition du contrat
    public function editContrat() {
        $id_contrat = Flight::request()->query['id'] ?? null;

        if (!$id_contrat) {
            Flight::halt(400, "ID du contrat manquant !");
            return;
        }

        // Modèles
        $contratModel   = Flight::Contrat();      // modèle contrats
        $candidatModel  = Flight::Candidat();     // modèle candidats
        $personneModel  = Flight::Personne();     // modèle personnes
        $typeContratModel = Flight::TypeContrat();// pour la liste des types
        $contrat = $contratModel->getBy('id_contrat', $id_contrat);
        
        // Récupérer infos candidat / personne (optionnel)
        $candidat = $candidatModel->getBy('id_candidat', $contrat['id_candidat'] ?? null);
        $personne = $personneModel->getBy('id_personne', $candidat['id_personne'] ?? null);

        // Liste types de contrat (pour select)
        $typeContrats = $typeContratModel->list();

        // Récupérer le contrat
        if (!$contrat) {
            Flight::halt(404, "Contrat non trouvé !");
            return;
        }
        // Charger le contrat modèle
        $modelePath = realpath(__DIR__ . "/../../../public".$contrat['url_contrat']);
        if (!$modelePath || !file_exists($modelePath)) {
            Flight::halt(500, "Fichier modèle introuvable.");
            return;
        }
        $modele = json_decode(file_get_contents($modelePath), true);
        if (!$modele) {
            Flight::halt(500, "Erreur lors du chargement du modèle de contrat.");
            return;
        }

        // Extraire la partie employe depuis le modele (s'il existe)
        $employeFromModele = $modele['employe'] ?? [];

        // Construire le tableau $data que la vue attend
        $data = [
            'contrat' => $contrat,
            'candidat' => $candidat,
            'personne' => $personne,
            'type_contrats' => $typeContrats,
            'modele' => $modele,
            'employe' => $employeFromModele,
            'profil' => $profil ?? null
        ];

        // Rendre la vue en passant $data
        Flight::render('validation/form', ['data' => $data]);

    }

    // Redirection vers la liste des contrats classer par etat Valide / non valide / En attente de validation 
    public function getContrat() {
        // Modèles
        $historiqueContratModel = Flight::HistoriqueContrat();  
        $candidatModel = Flight::Candidat();
        $personneModel = Flight::Personne();

        // Récupérer tous les contrats
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

        // Passer les données à la vue
        Flight::render('validation/listContrat', [
            'contratsParEtat' => $contratsParEtat
        ]);
    }

    // Redirection vers la page d'enregistrement des information du candidat dans json *brouillon

    public function registerContrat() {
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
        $personne = $personneModel->getBy('id_personne', $id_candidat);
        $candidat = $candidatModel->getBy('id_candidat', $id_candidat);

        if (!$personne || !$candidat) {
            Flight::halt(404, "Candidat non trouvé.");
            return;
        }

        // --- 1) vérifier d'abord en base s'il existe déjà un contrat pour ce candidat ---
        $existingContrat = $contratModel->getBy('id_candidat', $id_candidat);

        // Préparer chemins
        $basePublic = realpath(__DIR__ . "/../../../public");
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

            // Créer dossier si absent (mais ne bloque pas si présent)
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

        // Déterminer l'état en fonction du bouton cliqué (action)
        $action = $data['action'] ?? null;
        switch ($action) {
            case 'valider':
                $etat = $etatModel->getBy("nom", "Validé");
                if (!$etat) { $etatModel->save(['nom'=>'Validé']); $etat = $etatModel->getBy("nom","Validé"); }
                $actionLabel = "Validation";
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

        // Si signatures complètes, tu peux prioriser marquer comme validé — mais ici on suit l'action bouton
        // Enregistrer l'historique (si état disponible)
        if (!empty($etat) && isset($etat['id_etat'])) {
            $historiqueData = [
                'id_employe'            => $id_candidat, // remplace par id utilisateur connecté si dispo
                'id_candidat'           => $id_candidat,
                'date_heure_validation' => date('Y-m-d H:i:s'),
                'id_etat'               => $etat['id_etat']
            ];
            $historiqueModel->save($historiqueData);
        }

        // Construire message pour redirection
        $msgParts = [];
        if ($createdNewFile) {
            $msgParts[] = "Contrat créé";
        } else {
            $msgParts[] = "Contrat réutilisé";
        }
        $msgParts[] = strtolower($actionLabel) . " effectuée";
        $msg = urlencode(implode(" et ", $msgParts) . " avec succès.");

        // Redirection vers l'édition du contrat (si on a un id_contrat)
        if ($id_contrat) {
            Flight::redirect("/migration/contrat/edit?id={$id_contrat}&msg={$msg}&msg_type=success");
        } else {
            // fallback : rediriger vers la création ou liste
            Flight::redirect("/migration/contrat/create?id={$id_candidat}&msg={$msg}&msg_type=info");
        }
    }

    // Redirection vers la page de generation de contrat 
    public function createContrat() {
        $id_candidat = Flight::request()->query['id'] ?? null;

        // Si pas d'id → formulaire vierge
        if (!$id_candidat) {
            Flight::render('migration/form');
            return;
        }

        // Modèles
        $personneModel   = Flight::Personne();
        $candidatModel   = Flight::Candidat();
        $contratModel    = Flight::Contrat();
        $typeContratModel = Flight::TypeContrat();
        $etatModel        = Flight::Etat();
        // Récupérer le candidat
        $candidat = $candidatModel->getBy('id_candidat', $id_candidat);

        // Modèles
        $profilsModel = Flight::Profils(); // ton modèle ProfilsModel

        // Récupérer le profil du candidat si existant
        $profil = null;
        if (!empty($candidat['id_profil'])) {
            $profil = $profilsModel->getById($candidat['id_profil']);
        }


        // Si candidat non trouvé → formulaire vierge
        if (!$candidat) {
            Flight::render('migration/form', ['data' => []]);
            return;
        }

        // Récupérer la personne associée
        $personne = $personneModel->getBy('id_personne', $candidat['id_personne']);

        // Récupérer la liste des types de contrats
        $typeContrats = $typeContratModel->list();
        // Récupérer la liste des états
        $etats = $etatModel->list();
       // Ajouter au tableau $data pour le formulaire
        $data = [
            'candidat' => $candidat,
            'personne' => $personne,
            'profil' => $profil,      // <--- nouveau
            'type_contrats' => $typeContrats,
            'etats' => $etats
        ];


        Flight::render('migration/form', ['data' => $data]);
    }

    // Redirection vers la page de résultat des tests et entretiens
    public function getCandidatRetenu() {
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

    // Test des modèles
    public function test(){
        $data = [];

        // --- Personnes ---
        $personneModel = Flight::Personne();
        $personneModel->save([
            'nom' => 'Randria',
            'prenom' => 'Mickael',
            'date_naissance' => '1995-07-12',
            'contact' => '0341234567',
            'lien_image' => 'photo.jpg',
            'mdp' => 'secret'
        ]);
        $data['personnes'] = $personneModel->list();

        // --- Candidats ---
        $candidatModel = Flight::Candidat();
        $candidatModel->save([
            'id_personne' => $data['personnes'][0]['id_personne'],
            'id_annonce' => null,
            'cv_url' => 'cv.pdf',
            'poste' => 'Développeur'
        ]);
        $data['candidats'] = $candidatModel->list();

        // --- TypeContrats ---
        $typeContratModel = Flight::TypeContrat();
        $typeContratModel->save(['nom' => 'CDI']);
        $data['type_contrats'] = $typeContratModel->list();

        // --- Contrats ---
        $contratModel = Flight::Contrat();
        $contratModel->save([
            'id_candidat' => $data['candidats'][0]['id_candidat'],
            'id_type_contrat' => $data['type_contrats'][0]['id_type_contrat'],
            'url_contrat' => 'contrat.pdf'
        ]);
        $data['contrats'] = $contratModel->list();

        // --- Scoring (lecture seule) ---
        $scoringModel = Flight::Scoring();
        $data['scoring'] = $scoringModel->list();

        // --- Render view ---
        Flight::render('migration/test', ['data' => $data]);
    }
}

?>