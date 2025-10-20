<?php
namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
class HistoriquePlanningEntretienModel
{
    private $pdo;

    private $id_historique_planning;
    private $id_entretien;
    private $etat;
    private $date_modification;
    private $raison_modification;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
        // ----------------------
    // Getters
    // ----------------------
    public function getIdHistoriquePlanning()
    {
        return $this->id_historique_planning;
    }

    public function getIdEntretien()
    {
        return $this->id_entretien;
    }

    public function getEtat()
    {
        return $this->etat;
    }

    public function getDateModification()
    {
        return $this->date_modification;
    }

    public function getRaisonModification()
    {
        return $this->raison_modification;
    }

    // ----------------------
    // Setters
    // ----------------------
    public function setIdHistoriquePlanning($id)
    {
        $this->id_historique_planning = $id;
    }

    public function setIdEntretien($id_entretien)
    {
        $this->id_entretien = $id_entretien;
    }

    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    public function setDateModification($date_modification)
    {
        $this->date_modification = $date_modification;
    }


    public function setRaisonModification($raison_modification)
    {
        $this->raison_modification = $raison_modification;
    }
    /**
     * Sauvegarde un nouvel enregistrement dans la table
     */
    public function save()
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO historique_planning_entretien (id_entretien, etat,raison_modification)
            VALUES (:id_entretien, :etat,:raison_modification)
            RETURNING id_historique_planning, date_modification
        ");
        $stmt->execute([
            ':id_entretien' => $this->id_entretien,
            ':etat' => $this->etat,
            ':raison_modification' => $this->raison_modification
        ]);

        $result = $stmt->fetch();
        $this->id_historique_planning = $result['id_historique_planning'];
        $this->date_modification = $result['date_modification'];
        return $this;
    }

    /**
     * Récupère un enregistrement par son ID
     */
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM historique_planning_entretien
            WHERE id_historique_planning = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les enregistrements
     */
    public function getAll()
    {
        $stmt = $this->pdo->query("
            SELECT * FROM historique_planning_entretien
            ORDER BY date_modification DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
