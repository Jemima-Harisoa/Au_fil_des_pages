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

    // --------------------------------------------------------
    // Relevé individuel avec retard, pause et heures sup
    // --------------------------------------------------------
public function creerReleverPresenceIndividuelle($idEmploye, $debutPeriode = null, $finPeriode = null)
{
    // 1️⃣ Déterminer la période à afficher
    if (!$debutPeriode) $debutPeriode = date('Y-m-01'); // 1er du mois courant par défaut
    if (!$finPeriode) $finPeriode = date('Y-m-t');      // dernier jour du mois

    // 2️⃣ Récupérer tous les pointages de l'employé dans cette période
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

    // 3️⃣ Récupérer nom/prénom
    $sqlPers = "SELECT p.nom, p.prenom FROM employes e
                LEFT JOIN personnes p ON e.id_personne = p.id_personne
                WHERE e.id_employe = :id";
    $stmtPers = $this->db->prepare($sqlPers);
    $stmtPers->execute(['id' => $idEmploye]);
    $pers = $stmtPers->fetch(PDO::FETCH_ASSOC);
    $nom = $pers['nom'] ?? '';
    $prenom = $pers['prenom'] ?? '';

    // 4️⃣ Générer la liste de tous les jours de la période
    $dates = [];
    $current = strtotime($debutPeriode);
    $end = strtotime($finPeriode);
    while ($current <= $end) {
        $dates[date('Y-m-d', $current)] = [];
        $current = strtotime('+1 day', $current);
    }

    // 5️⃣ Organiser les sessions par jour
    foreach ($rows as $r) {
        $date = date('Y-m-d', strtotime($r['connexion']));
        $dates[$date][] = [
            'arrivee' => date('H:i:s', strtotime($r['connexion'])),
            'depart' => $r['deconnexion'] ? date('H:i:s', strtotime($r['deconnexion'])) : null
        ];
    }

    // 6️⃣ Calcul total par jour et cumul global
    $totalWorked = $totalRetard = $totalPause = $totalSup = 0;
    $result = [];

    foreach ($dates as $date => $sessions) {
        if (empty($sessions)) {
            // absent
            $result[$date] = [
                'etat' => 'Absent',
                'sessions' => [],
                'total_jour' => '00:00:00',
                'retard' => '00:00:00',
                'pause' => '00:00:00',
                'heures_sup' => '00:00:00'
            ];
            continue;
        }

        $prevDepart = null;
        $totalSecDay = $retardDay = $pauseDay = $supDay = 0;

        foreach ($sessions as $i => $s) {
            $arrivee = strtotime($s['arrivee']);
            $depart = $s['depart'] ? strtotime($s['depart']) : null;

            // durée travail
            if ($depart) $totalSecDay += $depart - $arrivee;

            // pause entre sessions
            if ($prevDepart && $arrivee > $prevDepart) $pauseDay += $arrivee - $prevDepart;
            $prevDepart = $depart;

            // horaires employé
            $jourSemaine = date('N', $arrivee);
            $sqlHoraire = "SELECT debut_travail, fin_travail, seuil_retard
                           FROM horaires_employe
                           WHERE id_employe = ? AND jour_semaine = ?";
            $stmtHoraire = $this->db->prepare($sqlHoraire);
            $stmtHoraire->execute([$idEmploye, $jourSemaine]);
            $horaires = $stmtHoraire->fetchAll(PDO::FETCH_ASSOC);

            $rSec = $sSec = 0;
            foreach ($horaires as $h) {
                $debut = strtotime($date.' '.$h['debut_travail']);
                $fin = strtotime($date.' '.$h['fin_travail']);
                $seuil = strtotime($h['seuil_retard']) - strtotime("00:00:00");

                $rSec += max(0, $arrivee - $debut - $seuil);
                $sSec += $depart ? max(0, $depart - $fin) : 0;
            }

            $retardDay += $rSec;
            $supDay += $sSec;

            $sessions[$i]['retardSec'] = $rSec;
            $sessions[$i]['supSec'] = $sSec;
            $sessions[$i]['pauseSec'] = $prevDepart ? $arrivee - ($prevDepart - $depart + $arrivee) : 0;
        }

        $totalWorked += $totalSecDay;
        $totalRetard += $retardDay;
        $totalPause += $pauseDay;
        $totalSup += $supDay;

        $result[$date] = [
            'etat' => 'Présent',
            'sessions' => $sessions,
            'total_jour' => gmdate('H:i:s', $totalSecDay),
            'retard' => gmdate('H:i:s', $retardDay),
            'pause' => gmdate('H:i:s', $pauseDay),
            'heures_sup' => gmdate('H:i:s', $supDay)
        ];
    }

    return [
        'id_employe' => $idEmploye,
        'nom' => $nom,
        'prenom' => $prenom,
        'dates' => $result,
        'total_heures' => gmdate('H:i:s', $totalWorked),
        'retard' => gmdate('H:i:s', $totalRetard),
        'pause' => gmdate('H:i:s', $totalPause),
        'heures_supp' => gmdate('H:i:s', $totalSup),
        'etat' => $totalWorked > 0 ? 'Présent' : 'Absent'
    ];
}


    // --------------------------------------------------------
    // Relevé groupe
    // --------------------------------------------------------
    public function creerReleverPresenceGroupe($idDepartement = null)
    {
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

        foreach ($ids as $id) {
            $result[$id] = $this->creerReleverPresenceIndividuelle($id);
        }

        return $result;
    }
}
?>
