<?php
namespace app\controllers\migration;

use Flight;
use app\models\migration\PersonneModel;
use app\models\migration\CandidatModel;
use app\models\migration\ScoringModel;
use app\models\migration\TypeContratModel;
use app\models\migration\ContratModel;
use app\models\migration\HistoriqueValidation;


class MigrationController {
    // Redirection vers la page d'enregistrement des information du candidat dans json *brouillon
    public function registerContrat() {
        // Récupérer l'id du candidat depuis le formulaire ou query string
        $id_candidat = Flight::request()->query['id_candidat'] ?? null;

        if (!$id_candidat) {
            Flight::halt(400, "ID du candidat manquant.");
            return;
        }

        // Récupérer les données POST
        $data = Flight::request()->data;

        // Récupérer la personne et le candidat
        $personneModel = Flight::Personne();
        $candidatModel = Flight::Candidat();

        // Récupérer le model de contrat
        $contratModel = Flight::Contrat();

        // Récupérer le model historique de validation
        $historiqueModel = Flight::HistoriqueValidation(); 
        
        $personne = $personneModel->getBy('id_personne', $id_candidat);
        $candidat  = $candidatModel->getBy('id_candidat', $id_candidat);

        if (!$personne || !$candidat) {
            Flight::halt(404, "Candidat non trouvé.");
            return;
        }

        // Chemin relatif vers public/json/contrats/contrat_travail/candidat_X
        $basePath = realpath(__DIR__ . "/../../../public/json/contrats/contrat_travail");
        if (!$basePath) {
            Flight::halt(500, "Répertoire des contrats introuvable.");
            return;
        }

        $dir = $basePath . "/candidat_" . $id_candidat;
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        // Structuration du JSON
        $contrat = [
            "_comment" => "fichier avec les clauses du contrat general pour un employee donne",
            "type" => $data['typeContrat'] ?? 'CDD',
            "modalite" => [
                "debut_contrat" => $data['dateDebut'] ?? '',
                "duree" => $data['dureeCDD'] ?? null, // si CDI → null
                "essai" => $data['essai'] ?? null
            ],
            "lieu" => $data['lieuEmploi'] ?? '',
            "poste" => [
                "qualité" => $data['poste'] ?? '',
                "class" => $data['classification'] ?? ''
            ],
            "remuneration" => [
                "salaire" => $data['salaire'] ?? '',
                "avantages" => isset($data['avantages']) ? explode(',', $data['avantages']) : []
            ],
            "resiliation" => [
                "type" => $data['typeContrat'] ?? 'CDD',
                "indemnite" => $data['indemnite'] ?? ''
            ],
            "engagement" => "Les parties s'engagent à respecter les dispositions du Code du Travail et de ses textes d'application pour tout ce qui n'est pas prévu dans ce contrat",
            "lieu_edition" => $data['lieuEdition'] ?? 'Antananarivo',
            "date_edition" => date('d/m/Y'),
            "signature" => $data['signature'] ?? "{$personne['nom']} {$personne['prenom']}, {$data['poste']}",
        ];

        // Nom du fichier JSON
        $file = $dir . "/contrat_" . date('Ymd_His') . ".json";

        // Enregistrer le JSON// Enregistrer le JSON
        if (file_put_contents($file, json_encode($contrat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            
            $contratData = [
                "id_candidat" => $id_candidat ,
                "id_type_contrat" => $data["id_type_contrat"] , // Cdd temp
                "url_contrat" =>  str_replace(getcwd() . '/public', '/public', $file) // lien relatif depuis /public
            ];
            $contratModel->save($contratData);

            //  Création de l'historique de validation
            $historiqueData = [
                'id_employe' => $contrat['id_employe'] ?? null,   // adapte selon ton modèle
                'id_candidat' => $contratData['id_candidat'] ?? null, // adapte selon ton modèle
                'date_heure_validation' => date('Y-m-d H:i:s'),
                'id_etat' => 5, // Etat "Brouillon"
            ];
            
            $historiqueModel->save($historiqueData);

            Flight::json([
                "success" => true,
                "message" => "Contrat enregistré avec succès.",
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

        // Préparer les données pour le formulaire
        $data = [
            'candidat' => $candidat,
            'personne' => $personne,
            'type_contrats' => $typeContrats
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
                $contratUrl = $contrat['url_contrat'];
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
                'adresse'   => $pers['contact'] ?? '',
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