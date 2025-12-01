<?php 
namespace app\models;

use PDO;

class PointageModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // --------------------------------------------------------
    // Dernier pointage
    // --------------------------------------------------------
    public function getDernierPointage($idEmploye) {
        $sql = "SELECT * FROM pointage WHERE id_employe = ? ORDER BY id_pointage DESC LIMIT 1";
        $query = $this->db->prepare($sql);
        $query->execute([$idEmploye]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // --------------------------------------------------------
    // Ouverture pointage
    // --------------------------------------------------------
    public function ajouterPointage($idEmploye) {
        $sql = "INSERT INTO pointage (id_employe, connexion, deconnexion) 
                VALUES (?, NOW(), NULL)";
        $query = $this->db->prepare($sql);
        $query->execute([$idEmploye]);
    }

    // --------------------------------------------------------
    // Fermeture pointage
    // --------------------------------------------------------
    public function cloturerPointage($idPointage) {
        $sql = "UPDATE pointage SET deconnexion = NOW()
                WHERE id_pointage = ?";
        $query = $this->db->prepare($sql);
        $query->execute([$idPointage]);
    }

    // --------------------------------------------------------
    // Liste brute pour tableau admin
    // --------------------------------------------------------
    public function getAllPresence(): array {
        $sql = "SELECT 
                    e.id_employe,
                    e.poste,
                    p.nom,
                    p.prenom,
                    pt.connexion,
                    pt.deconnexion,
                    pt.duree_session
                FROM employes e
                LEFT JOIN pointage pt ON e.id_employe = pt.id_employe
                LEFT JOIN personnes p ON e.id_personne = p.id_personne
                ORDER BY e.id_departement, e.poste, pt.connexion";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
public function creerReleverPresenceIndividuelle($idEmploye, $debutPeriode = null, $finPeriode = null)
{
    if (!$debutPeriode) $debutPeriode = date('Y-m-01');
    if (!$finPeriode) $finPeriode = date('Y-m-t');
    $aujourdhui = date('Y-m-d');

    // --- 1️⃣ Récupération des sessions
    $sql = "SELECT connexion, deconnexion
            FROM pointage
            WHERE id_employe = :id
              AND connexion::date BETWEEN :debut AND :fin
            ORDER BY connexion ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        'id' => $idEmploye,
        'debut' => $debutPeriode,
        'fin' => $finPeriode
    ]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- 2️⃣ Récupération nom/prénom
    $sqlPers = "SELECT p.nom, p.prenom
                FROM employes e
                LEFT JOIN personnes p ON e.id_personne = p.id_personne
                WHERE e.id_employe = :id";
    $stmtPers = $this->db->prepare($sqlPers);
    $stmtPers->execute(['id' => $idEmploye]);
    $pers = $stmtPers->fetch(PDO::FETCH_ASSOC);
    $nom = $pers['nom'] ?? '';
    $prenom = $pers['prenom'] ?? '';

    // --- 3️⃣ Générer toutes les dates de la période
    $dates = [];
    $current = strtotime($debutPeriode);
    $end = strtotime($finPeriode);
    while ($current <= $end) {
        $dates[date('Y-m-d', $current)] = [
            'matin' => [],
            'apres_midi' => []
        ];
        $current = strtotime('+1 day', $current);
    }

    // --- 4️⃣ Dispatcher les sessions matin / après-midi
    foreach ($rows as $r) {
        $date = date('Y-m-d', strtotime($r['connexion']));
        $heure = intval(date('H', strtotime($r['connexion'])));
        $session = [
            'arrivee' => date('H:i:s', strtotime($r['connexion'])),
            'depart'  => $r['deconnexion'] ? date('H:i:s', strtotime($r['deconnexion'])) : null
        ];
        if ($heure < 12) {
            $dates[$date]['matin'][] = $session;
        } else {
            $dates[$date]['apres_midi'][] = $session;
        }
    }

    // --- 5️⃣ Calculs par jour
    $totalWorked = $totalRetard = $totalPause = $totalSup = 0;
    $result = [];

    foreach ($dates as $date => $sessionsJour) {
        $etatJour = strtotime($date) > strtotime($aujourdhui) ? 'À venir' : 'Absent';
        $totalSecDay = $retardDay = $pauseDay = $supDay = 0;
        $sessionsDetail = [];

        // Récupérer les horaires depuis la table pour ce jour
        $jourSemaine = date('N', strtotime($date)); // 1 = Lundi ... 7 = Dimanche
        $sqlHoraire = "SELECT debut_travail, fin_travail, seuil_retard
                       FROM horaires_employe
                       WHERE id_employe = :id AND jour_semaine = :jour
                       ORDER BY debut_travail ASC";
        $stmtHoraire = $this->db->prepare($sqlHoraire);
        $stmtHoraire->execute(['id' => $idEmploye, 'jour' => $jourSemaine]);
        $horaires = $stmtHoraire->fetchAll(PDO::FETCH_ASSOC);

        // Organiser les horaires par période
        $horairesMatin = [];
        $horairesApresMidi = [];
        foreach ($horaires as $h) {
            $heureDebut = intval(date('H', strtotime($h['debut_travail'])));
            if ($heureDebut < 12) {
                $horairesMatin[] = $h;
            } else {
                $horairesApresMidi[] = $h;
            }
        }

        $prevDepartJour = null;

        foreach (['matin','apres_midi'] as $periode) {
            $sess = $sessionsJour[$periode] ?? [];
            $horairesPeriode = ($periode === 'matin') ? $horairesMatin : $horairesApresMidi;
            $prevDepart = $prevDepartJour;
            $totalSecPeriode = $retardPeriode = $pausePeriode = $supPeriode = 0;

            foreach ($sess as $i => $s) {
                $arrivee = strtotime($date.' '.$s['arrivee']);
                $depart  = $s['depart'] ? strtotime($date.' '.$s['depart']) : null;

                if ($depart) $totalSecPeriode += ($depart - $arrivee);

                // Pause entre sessions
                if ($prevDepart && $arrivee > $prevDepart) {
                    $pausePeriode += $arrivee - $prevDepart;
                }

                $rSec = $sSec = 0;
                
                // Calcul du retard et heures supplémentaires
                foreach ($horairesPeriode as $h) {
                    $hDebut = strtotime($date.' '.$h['debut_travail']);
                    $hFin   = strtotime($date.' '.$h['fin_travail']);
                    $seuilRetard = strtotime($h['seuil_retard']) - strtotime('00:00:00');

                    // === RETARD ===
                    // Retard = arrivée après (début + seuil)
                    if ($arrivee > ($hDebut + $seuilRetard)) {
                        $rSec = $arrivee - ($hDebut + $seuilRetard);
                    }

                    // === HEURES SUPPLÉMENTAIRES ===
                    // 1. Arrivée plus tôt que l'horaire normal
                    if ($arrivee < $hDebut) {
                        $sSec += $hDebut - $arrivee;
                    }
                    
                    // 2. Départ plus tard que l'horaire normal
                    if ($depart && $depart > $hFin) {
                        $sSec += $depart - $hFin;
                    }
                    
                    break; // On prend le premier horaire qui correspond
                }

                $retardPeriode += $rSec;
                $supPeriode    += $sSec;

                $sess[$i]['retardSec'] = $rSec;
                $sess[$i]['supSec']    = $sSec;
                $sess[$i]['pauseSec']  = ($prevDepart && $arrivee > $prevDepart) ? ($arrivee - $prevDepart) : 0;

                $prevDepart = $depart;
            }

            // Calcul de la pause entre matin et après-midi
            if ($periode === 'matin' && !empty($sess)) {
                $lastSession = end($sess);
                $prevDepartJour = $lastSession['depart'] ? strtotime($date.' '.$lastSession['depart']) : null;
            }

            if (!empty($sess)) $etatJour = 'Présent';

            $sessionsDetail[$periode] = [
                'sessions'   => $sess,
                'total_jour' => gmdate('H:i:s', $totalSecPeriode),
                'retard'     => gmdate('H:i:s', $retardPeriode),
                'pause'      => gmdate('H:i:s', $pausePeriode),
                'heures_sup' => gmdate('H:i:s', $supPeriode)
            ];

            $totalSecDay += $totalSecPeriode;
            $retardDay   += $retardPeriode;
            $pauseDay    += $pausePeriode;
            $supDay      += $supPeriode;
        }

        // Pause déjeuner entre fin du matin et début de l'après-midi
        $pauseDejeuner = 0;
        if (!empty($sessionsJour['matin']) && !empty($sessionsJour['apres_midi'])) {
            $lastMatin = end($sessionsJour['matin']);
            $firstApresMidi = $sessionsJour['apres_midi'][0];
            
            $finMatin = $lastMatin['depart'] ? strtotime($date.' '.$lastMatin['depart']) : null;
            $debutApresMidi = strtotime($date.' '.$firstApresMidi['arrivee']);
            
            if ($finMatin && $debutApresMidi > $finMatin) {
                $pauseDejeuner = $debutApresMidi - $finMatin;
                $pauseDay += $pauseDejeuner;
                $totalPause += $pauseDejeuner;
            }
        }

        // Heures supplémentaires pour travail en dehors des horaires prévus
        // Si l'employé travaille un jour sans horaire défini, tout est considéré comme heures sup
        if (empty($horaires)) {
            $supDay += $totalSecDay;
        }

        $result[$date] = [
            'etat' => $etatJour,
            'matin' => $sessionsDetail['matin'],
            'apres_midi' => $sessionsDetail['apres_midi'],
            'pause_dejeuner' => gmdate('H:i:s', $pauseDejeuner)
        ];

        $totalWorked += $totalSecDay;
        $totalRetard += $retardDay;
        $totalPause += $pauseDay;
        $totalSup += $supDay;
    }

    return [
        'id_employe'   => $idEmploye,
        'nom'          => $nom,
        'prenom'       => $prenom,
        'dates'        => $result,
        'total_heures' => gmdate('H:i:s', $totalWorked),
        'retard'       => gmdate('H:i:s', $totalRetard),
        'pause'        => gmdate('H:i:s', $totalPause),
        'heures_supp'  => gmdate('H:i:s', $totalSup),
        'etat'         => $totalWorked > 0 ? 'Présent' : 'Absent'
    ];
}

   public function creerReleverPresenceGroupe($idDepartement = null, $debutPeriode = null, $finPeriode = null)
{
    // 1️⃣ Récupérer tous les IDs des employés
    if ($idDepartement && $idDepartement !== "all") {
        $sql = "SELECT id_employe FROM employes WHERE id_departement = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idDepartement]);
    } else {
        $sql = "SELECT id_employe FROM employes";
        $stmt = $this->db->query($sql);
    }

    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $result = [];

    // 2️⃣ Pour chaque employé, récupérer le relevé individuel avec période
    foreach ($ids as $id) {
        $result[$id] = $this->creerReleverPresenceIndividuelle($id, $debutPeriode, $finPeriode);
    }

    return $result;
}
}