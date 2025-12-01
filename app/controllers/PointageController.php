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

public function exporterCSV($idEmploye, $debutPeriode = null, $finPeriode = null)
{
    $pointageModel = new PointageModel(Flight::db());
    $pointages = $pointageModel->creerReleverPresenceIndividuelle($idEmploye, $debutPeriode, $finPeriode);
    $filename = "releve_presence_{$pointages['nom']}_{$pointages['prenom']}_{$idEmploye}.csv";

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // BOM UTF-8 pour Excel
    fputs($output, "\xEF\xBB\xBF");

    // Fonction utilitaire pour fputcsv avec séparateur ;
    $csvWrite = function($output, $fields) {
        fputcsv($output, $fields, ';');
    };

    // --- Entête résumé ---
    $csvWrite($output, ["Pointage détaillé de {$pointages['prenom']} {$pointages['nom']}"]);
    $csvWrite($output, ["Total heures :", $pointages['total_heures'] ?? '00:00:00']);
    $csvWrite($output, ["Total retard :", $pointages['retard'] ?? '00:00:00']);
    $csvWrite($output, ["Total pause :", $pointages['pause'] ?? '00:00:00']);
    $csvWrite($output, ["Total heures sup :", $pointages['heures_supp'] ?? '00:00:00']);
    $csvWrite($output, []); // ligne vide

    // --- Entête tableau ---
    $csvWrite($output, ["Date","Période","Arrivée","Départ","Total période","Retard","Pause","Heures sup","État"]);

    foreach ($pointages['dates'] as $date => $data) {
        $totalJour = ['total_periode'=>0,'retard'=>0,'pause'=>0,'heures_sup'=>0];

        foreach (['matin','apres_midi'] as $periodeKey) {
            $periodeLabel = ($periodeKey === 'matin') ? "Matin" : "Après-midi";
            $sessions = $data[$periodeKey]['sessions'] ?? [];
            $etat = $data[$periodeKey]['etat'] ?? 'Absent';
            $totalPeriode = $data[$periodeKey]['total_jour'] ?? '00:00:00';
            $retard = $data[$periodeKey]['retard'] ?? '00:00:00';
            $pause = $data[$periodeKey]['pause'] ?? '00:00:00';
            $heuresSup = $data[$periodeKey]['heures_sup'] ?? '00:00:00';

            // Totaux journaliers
            $totalJour['total_periode'] += strtotime($totalPeriode) - strtotime('00:00:00');
            $totalJour['retard'] += strtotime($retard) - strtotime('00:00:00');
            $totalJour['pause'] += strtotime($pause) - strtotime('00:00:00');
            $totalJour['heures_sup'] += strtotime($heuresSup) - strtotime('00:00:00');

            if (empty($sessions)) {
                $csvWrite($output, [$date,$periodeLabel,'-','-',$totalPeriode,$retard,$pause,$heuresSup,$etat]);
            } else {
                foreach ($sessions as $index => $s) {
                    $arrivee = $s['arrivee'] ?? '-';
                    $depart  = $s['depart'] ?? '-';
                    if ($index > 0) {
                        $arrivee = "Arrivée ".($index+1).": $arrivee";
                        $depart  = "Départ ".($index+1).": $depart";
                    }

                    $csvWrite($output, [
                        ($index===0) ? $date : '',
                        ($index===0) ? $periodeLabel : '',
                        $arrivee,
                        $depart,
                        ($index===0) ? $totalPeriode : '',
                        ($index===0) ? $retard : '',
                        ($index===0) ? $pause : '',
                        ($index===0) ? $heuresSup : '',
                        ($index===0) ? $etat : ''
                    ]);
                }
            }
        }

        // Totaux journaliers
        $csvWrite($output, [
            'TOTAL JOURNÉE','','','',
            gmdate('H:i:s',$totalJour['total_periode']),
            gmdate('H:i:s',$totalJour['retard']),
            gmdate('H:i:s',$totalJour['pause']),
            gmdate('H:i:s',$totalJour['heures_sup']),
            ''
        ]);

        $csvWrite($output, []); // ligne vide
    }

    fclose($output);
    exit;
}

    
}