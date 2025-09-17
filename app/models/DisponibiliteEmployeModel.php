<?php
namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
class DisponibiliteEmployeModel {
    private $id_dispo;
    private $id_employe;
    private $heure_debut;
    private $heure_fin;
    private $jour;

    // Constructeur
    public function __construct($id_employe, $heure_debut, $heure_fin, $jour) {
        $this->id_employe = $id_employe;
        $this->heure_debut = $heure_debut;
        $this->heure_fin = $heure_fin;
        $this->jour = $jour;
    }

    // Méthode pour insérer une nouvelle disponibilité
    public function save() {
        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO disponibilite_employe (id_employe, heure_debut, heure_fin, jour) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $this->id_employe,
            $this->heure_debut,
            $this->heure_fin,
            $this->jour
        ]);
        $this->id_dispo = $db->lastInsertId();
    }

    // Récupérer toutes les disponibilités
    public static function all() {
        $db = Flight::db();
        $stmt = $db->query("SELECT * FROM disponibilite_employe");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une disponibilité par ID
    public static function find($id) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM disponibilite_employe WHERE id_dispo = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
