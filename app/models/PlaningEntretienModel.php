<?php
namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
class PlanningEntretienModel{
    private $id_entretien;
    private $id_candidat;
    private $date_heure_entretien;
    private $score_entretien;
    private $etat;
    private $id_appreciation;

    // Constructeur
    public function __construct($id_candidat, $date_heure_entretien, $score_entretien = null, $etat = 0, $id_appreciation = null) {
        $this->id_candidat = $id_candidat;
        $this->date_heure_entretien = $date_heure_entretien;
        $this->score_entretien = $score_entretien;
        $this->etat = $etat;
        $this->id_appreciation = $id_appreciation;
    }

    // Insert
    public function save() {
        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO planning_entretien (id_candidat, date_heure_entretien, score_entretien, etat, id_appreciation) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $this->id_candidat,
            $this->date_heure_entretien,
            $this->score_entretien,
            $this->etat,
            $this->id_appreciation
        ]);
        $this->id_entretien = $db->lastInsertId();
    }

    // Récupérer tous les entretiens
    public static function all() {
        $db = Flight::db();
        $stmt = $db->query("SELECT * FROM planning_entretien");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un entretien par ID
    public static function find($id) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM planning_entretien WHERE id_entretien = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
