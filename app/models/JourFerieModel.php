<?php
namespace app\models;

use Flight;
use PDO;

class JourFerieModel {
    private $id_jour_ferie;
    private $date;

    // Constructeur
    public function __construct($date) {
        $this->date = $date;
    }

    // Insérer un jour férié
    public function save() {
        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO jour_ferie (\"date\") VALUES (?)");
        $stmt->execute([$this->date]);
        $this->id_jour_ferie = $db->lastInsertId();
    }

    // Récupérer tous les jours fériés
    public static function all() {
        $db = Flight::db();
        $stmt = $db->query("SELECT * FROM jour_ferie ORDER BY date");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un jour férié par ID
    public static function find($id) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM jour_ferie WHERE id_jour_ferie = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Supprimer un jour férié
    public static function delete($id) {
        $db = Flight::db();
        $stmt = $db->prepare("DELETE FROM jour_ferie WHERE id_jour_ferie = ?");
        return $stmt->execute([$id]);
    }
    public static function estJourFerie(DateTime $dateHeureReference): bool
    {
        // Récupérer tous les jours fériés
        $joursFeries = self::all();

        // Formater la date donnée pour ne garder que Y-m-d
        $dateRef = $dateHeureReference->format('Y-m-d');

        // Parcourir les jours fériés
        foreach ($joursFeries as $jour) {
            // Comparer la date du jour férié avec la date donnée
            if ($jour['date'] === $dateRef) {
                return true; // c'est un jour férié
            }
        }

        return false; // pas trouvé
    }

}
