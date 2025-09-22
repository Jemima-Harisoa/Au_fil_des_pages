<?php
namespace app\controllers\migration;

use Flight;
use app\models\migration\PersonneModel;
use app\models\migration\CandidatModel;
use app\models\migration\ScoringModel;
use app\models\migration\TypeContratModel;
use app\models\migration\ContratModel;
use app\models\migration\HistoriqueValidationModel;
use app\models\EtatModel;


class MigrationController {
    // Redirection vers la liste des contrats classer par etat Valide / non valide / En attente de validation 
    public function validation(){
        Flight::render();
    }

    // Redirection vers la page d'enregistrement des information du candidat dans json *brouillon
    public function registerContrat() {
        $id_candidat = Flight::request()->query['id_candidat'] ?? null;
        if (!$id_candidat) {
            Flight::halt(400, "ID du candidat manquant.");
            return;
        }

        // Récupération des données du formulaire (POST)
        $data = Flight::request()->data;

        // Modèles
        $personneModel = Flight::Personne();
        $candidatModel = Flight::Candidat();
        $contratModel = Flight::Contrat();
        $historiqueModel = Flight::HistoriqueValidation(); 
        $etatModel = Flight::Etat();

        // Chercher le candidat et son état
        $etat = $etatModel->search('nom', 'En attente de validation');
        $personne = $personneModel->getBy('id_personne', $id_candidat);
        $candidat  = $candidatModel->getBy('id_candidat', $id_candidat);

        if (!$personne || !$candidat) {
            Flight::halt(404, "Candidat non trouvé.");
            return;
        }

        // Charger le contrat modèle
        $modelePath = realpath(__DIR__ . "/../../../public/json/contrats/contrat_travail/contrat_exemple.json");
        if (!$modelePath || !file_exists($modelePath)) {
            Flight::halt(500, "Fichier modèle introuvable.");
            return;
        }
        $modele = json_decode(file_get_contents($modelePath), true);

        if (!$modele) {
            Flight::halt(500, "Erreur lors du chargement du modèle de contrat.");
            return;
        }

        // Remplacer la partie employe avec les données du formulaire
        $modele["employe"] = [
            "resilliation" => $data['typeContrat'] ?? 'cdd',
            "modalite" => [
                "debut_contrat" => $data['dateDebut'] ?? '',
                "duree" => $data['dureeCDD'] ?? null,
                "essai" => $data['essai'] ?? null
            ],
            "lieu" => $data['lieuEmploi'] ?? '',
            "poste" => [
                "qualite" => $data['poste'] ?? '',
                "class" => $data['classification'] ?? ''
            ],
            "remuneration" => [
                "salaire" => $data['salaire'] ?? '',
                "avantages" => isset($data['avantages']) 
                    ? (is_array($data['avantages']) 
                        ? array_map('trim', $data['avantages']) 
                        : array_map('trim', explode(',', $data['avantages'])))
                    : []
            ],
            "noms_prenoms" => "{$personne['nom']} {$personne['prenom']}",
            "ne_le" => $data['dateNaissance'] ?? '',
            "ne_a" => $data['lieuNaissance'] ?? '',
            "fils_ou_fille_de" => $data['parents'] ?? '',
            "nationalite" => $data['nationalite'] ?? '',
            "domicile" => $data['domicile'] ?? '',
            "lieu_edition" => $data['lieuEdition'] ?? 'Antananarivo',
            "date_edition" => date('d/m/Y'),
            "signature" => $data['signature'] ?? "{$personne['nom']} {$personne['prenom']}"
        ];

        // Créer le répertoire du candidat si inexistant
        $basePath = realpath(__DIR__ . "/../../../public/json/contrats/contrat_travail");
        $dir = $basePath . "/candidat_" . $id_candidat;
        
        if (file_exists($dir)) {
            // Le dossier existe déjà
            // -> tu peux soit empêcher la création
            // -> soit ajouter une logique différente
            Flight::json([
                "success" => false,
                "message" => "Le dossier du candidat existe déjà."
            ]);
            return; // on arrête ici si on veut empêcher la suite
        } else {
            // Sinon, on crée le dossier
            mkdir($dir, 0777, true);
        }

        // Nom du nouveau fichier JSON
        $file = $dir . "/contrat_" . date('Ymd_His') . ".json";

        // Enregistrement du contrat final
        if (file_put_contents($file, json_encode($modele, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            $publicDir = DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR;
            $pos = strpos($file, $publicDir);
            $url_contrat = ($pos !== false) ? substr($file, $pos) : $file;

            // Sauvegarde en base
            $contratData = [
                "id_candidat" => $id_candidat,
                "id_type_contrat" => $data["id_type_contrat"] ?? null,
                "url_contrat" => $url_contrat
            ];
            $contratModel->save($contratData);

            $historiqueData = [
                'id_employe' => $contratData['id_candidat'],
                'id_candidat' => $contratData['id_candidat'],
                'date_heure_validation' => date('Y-m-d H:i:s'),
                'id_etat' => $data['id_etat'] 
                    ?? ($etat && isset($etat['id_etat']) ? $etat['id_etat'] : null)
            ];
            $historiqueModel->save($historiqueData);

            Flight::json([
                "success" => true,
                "message" => "Contrat enregistré avec succès à partir du modèle.",
                "file" => $file,
                "etat" => "Brouillon"
            ]);
        } else {
            Flight::json([
                "success" => false,
                "message" => "Erreur lors de l'enregistrement du contrat."
            ]);
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
        // Préparer les données pour le formulaire
        $data = [
            'candidat' => $candidat,
            'personne' => $personne,
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

        Flight::render('migration/list', ['rows' => $rows]);
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