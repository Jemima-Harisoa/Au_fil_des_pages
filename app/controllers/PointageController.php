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
    $pointage = new PointageModel(Flight::db());
    $result = $pointage->creerReleverPresenceIndividuelle($idEmploye);
    Flight::json(['success' => $result]);
}

public static function releverPresenceGroupe($dept)
{
    $pointage = new PointageModel(Flight::db());
    $result = $pointage->creerReleverPresenceGroupe($dept);
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

    // Récupère le relevé complet
    $pointages = $pointageModel->creerReleverPresenceIndividuelle($idEmploye);

    // Détermination du nombre maximum de sessions pour toutes les dates
    $maxSessions = 0;
    foreach ($pointages['dates'] as $date => $sessions) {
        $maxSessions = max($maxSessions, count($sessions));
    }

    // Nom du fichier
    $filename = "releve_presence_{$idEmploye}.csv";

    // En-têtes HTTP pour CSV
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // Entêtes colonnes
    $header = ['Date'];
    for ($i = 1; $i <= $maxSessions; $i++) {
        $header[] = "Arrivee $i";
        $header[] = "Depart $i";
    }
    $header[] = 'Total journee';
    fputcsv($output, $header, ';');

    // Contenu par date
    foreach ($pointages['dates'] as $date => $sessions) {
        // Forcer Excel à interpréter la date comme texte
        $row = ["'" . $date];

        $totalSecondsDay = 0;

        // Ajout des sessions
        foreach ($sessions as $s) {
            $arrivee = $s['arrivee'] ?? '-';
            $depart  = $s['depart'] ?? '-';
            $row[] = $arrivee;
            $row[] = $depart;

            if ($depart !== '-') {
                $totalSecondsDay += strtotime($depart) - strtotime($arrivee);
            }
        }

        // Colonnes manquantes si moins de sessions
        $missing = $maxSessions - count($sessions);
        for ($i = 0; $i < $missing; $i++) {
            $row[] = '-';
            $row[] = '-';
        }

        // Total journée en texte pour Excel
        $row[] = "'" . gmdate("H:i:s", $totalSecondsDay);

        fputcsv($output, $row, ';');
    }

    fclose($output);
    exit;
}

    
}