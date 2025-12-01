<?php

namespace app\controllers;

use app\models\DepartementModel;
use Flight;

use app\models\EmployeModel;
use app\models\PointageModel;
use PDO;
class PointageController {

	public function __construct() {

	}

    
    public static function getAllEmployes()
    {
        $empModel = new EmployeModel(Flight::db());
        $deptModel = new DepartementModel(Flight::db());
        $allDepts = $deptModel->list();
        $allPresence = $empModel->listWithDetails();
       // return $allPresence;
        Flight::render('pointage',["allPresence" => $allPresence, "allDepts" => $allDepts]);
    }
public static function releverPresenceIndividuelle($idEmploye)
{
    $debut = Flight::request()->query['debut'] ?? null;
    $fin   = Flight::request()->query['fin'] ?? null;

    $pointage = new PointageModel(Flight::db());
    $result = $pointage->creerReleverPresenceIndividuelle($idEmploye, $debut, $fin);

    Flight::json(['success' => $result]);
}


public static function releverPresenceGroupe($dept)
{
    $debut = Flight::request()->query['debut'] ?? null;
    $fin   = Flight::request()->query['fin'] ?? null;

    $pointage = new PointageModel(Flight::db());
    $result = $pointage->creerReleverPresenceGroupe($dept, $debut, $fin);

    Flight::json(['success' => $result]);
}

public static function releverPresenceE(?int $idEmploye = null): void
{   
    $empModel = new EmployeModel(Flight::db());

    // Si idEmploye non fourni, prendre l'employé de session
    if (!$idEmploye) {
        if (isset($_SESSION['employe']['id_employe'])) {
            $idEmploye = (int) $_SESSION['employe']['id_employe'];
        } else {
            // Valeur par défaut ou erreur
            Flight::halt(400, "L'identifiant de l'employé est manquant !");
            return;
        }
    }

    $presenceE = $empModel->getInfosEmploye($idEmploye);

    Flight::render('releve_presence_e', ["allPresence" => $presenceE]);
}
public function exporterCSV($idEmploye)
{
    $pointageModel = new PointageModel(Flight::db());

    // Recupere le releve complet
    $pointages = $pointageModel->creerReleverPresenceIndividuelle($idEmploye);

    // Nom du fichier
    $filename = "releve_presence_{$pointages['nom']}_{$pointages['prenom']}_{$idEmploye}.csv";

    // Entetes HTTP pour CSV
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    
    // BOM UTF-8 pour Excel
    fputs($output, "\xEF\xBB\xBF");

    // En-tete informatif
    fputcsv($output, ["Pointage detaille de {$pointages['prenom']} {$pointages['nom']}"], ';');
    fputcsv($output, ["Total heures : {$pointages['total_heures']}"], ';');
    fputcsv($output, ["Total retard : {$pointages['retard']}"], ';');
    fputcsv($output, ["Total pause : {$pointages['pause']}"], ';');
    fputcsv($output, ["Total heures sup : {$pointages['heures_supp']}"], ';');
    fputcsv($output, [], ';'); // Ligne vide

    // Entetes du tableau
    $header = [
        'Date',
        'Periode',
        'Arrivee',
        'Depart',
        'Total periode',
        'Retard',
        'Pause',
        'Heures sup',
        'Etat'
    ];
    fputcsv($output, $header, ';');

    // Contenu par date et periode
    foreach ($pointages['dates'] as $date => $data) {
        // Forcer le format texte pour Excel en ajoutant un apostrophe
        $dateFormatted = "'" . $date;

        // Ligne pour la periode MATIN
        $matin = $data['matin'];
        $rowMatin = [
            $dateFormatted,
            'Matin',
            isset($matin['sessions'][0]['arrivee']) ? "'" . $matin['sessions'][0]['arrivee'] : '-',
            '-',
            "'" . ($matin['total_jour'] ?? '00:00:00'),
            "'" . ($matin['retard'] ?? '00:00:00'),
            "'" . ($matin['pause'] ?? '00:00:00'),
            "'" . ($matin['heures_sup'] ?? '00:00:00'),
            $data['etat']
        ];
        
        // Recuperer le depart du matin (derniere session)
        if (!empty($matin['sessions'])) {
            $lastSession = end($matin['sessions']);
            $rowMatin[3] = $lastSession['depart'] ? "'" . $lastSession['depart'] : '-';
        }

        fputcsv($output, $rowMatin, ';');

        // Ligne pour la periode APRES-MIDI
        $apresMidi = $data['apres_midi'];
        $rowApresMidi = [
            $dateFormatted,
            'Apres-midi',
            isset($apresMidi['sessions'][0]['arrivee']) ? "'" . $apresMidi['sessions'][0]['arrivee'] : '-',
            '-',
            "'" . ($apresMidi['total_jour'] ?? '00:00:00'),
            "'" . ($apresMidi['retard'] ?? '00:00:00'),
            "'" . ($apresMidi['pause'] ?? '00:00:00'),
            "'" . ($apresMidi['heures_sup'] ?? '00:00:00'),
            $data['etat']
        ];
        
        // Recuperer le depart de l'apres-midi (derniere session)
        if (!empty($apresMidi['sessions'])) {
            $lastSession = end($apresMidi['sessions']);
            $rowApresMidi[3] = $lastSession['depart'] ? "'" . $lastSession['depart'] : '-';
        }

        fputcsv($output, $rowApresMidi, ';');
    }

    fclose($output);
    exit;
}

    
}