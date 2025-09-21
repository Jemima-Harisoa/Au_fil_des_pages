<?php
namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
class PlanningEntretienModel{
    private $id_entretien;
    private $id_candidat;
    private $id_responsable;
    private $date_heure_entretien;
    private $score_entretien;
    private $etat;
    private $id_appreciation;
    private $db;

    // Constructeur
    public function __construct($db,$id_candidat=0,$id_responsable=0, $date_heure_entretien=null, $score_entretien = null, $etat = 0, $id_appreciation = null) {
        $this->id_candidat = $id_candidat;
        $this->id_responsable = $id_responsable;
        $this->date_heure_entretien = $date_heure_entretien;
        $this->score_entretien = $score_entretien;
        $this->etat = $etat;
        $this->id_appreciation = $id_appreciation;
        $this->db = $db;
    }

        public function getIdEntretien() {
        return $this->id_entretien;
    }

    public function getIdCandidat() {
        return $this->id_candidat;
    }

    public function getDateHeureEntretien() {
        return $this->date_heure_entretien;
    }

    public function getScoreEntretien() {
        return $this->score_entretien;
    }

    public function getEtat() {
        return $this->etat;
    }

    public function getIdAppreciation() {
        return $this->id_appreciation;
    }

    public function getDb() {
        return $this->db;
    }

    // Setters
    public function setIdEntretien($id_entretien) {
        $this->id_entretien = $id_entretien;
    }

    public function setIdCandidat($id_candidat) {
        $this->id_candidat = $id_candidat;
    }

    public function setIdResponsable($id_responsable) {
        $this->id_responsable = $id_responsable;
    }
    public function setDateHeureEntretien($date_heure_entretien) {
        $this->date_heure_entretien = $date_heure_entretien;
    }
    // Insert
    public function save() {
        $db = Flight::db();
        try {
            //code...
            $db->beginTransaction();
            if($this->date_heure_entretien!= null && $this->id_candidat!= null){
                $stmt = $db->prepare("INSERT INTO planning_entretien (id_candidat, date_heure_entretien, score_entretien, etat, id_appreciation) 
                                      VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $this->id_candidat,
                    $this->date_heure_entretien->format('Y-m-d H:i:s'),
                    $this->score_entretien,
                    $this->etat,
                    $this->id_appreciation
                ]);
                $db->commit();
            }
            else{
                throw new \Exception("la date heure ou l'id du candidat ne doit jamais etre null");
            }
                $this->id_entretien = $db->lastInsertId();
            } catch (\Exception $e) {
                $db->rollback();
                throw new \Exception($e->getMessage());
            }
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
    public function getLastPlanning($idResponsable) {
        try {
            if (empty($idResponsable)) {
                throw new \Exception("L'identifiant du responsable est nul ou non défini.");
            }

            $sql = "
                SELECT *
                FROM v_planning_entretien_recent_responsable
                WHERE id_responsable = :id_responsable
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id_responsable' => $idResponsable]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la récupération du planning : " . $e->getMessage());
        }
    }

    public function planifierEntretien($listeCandidats,$idResponsable){
        $candidats;
        $idProfil = 0;
        $idCandidat = 0;
        $idResponsable = 0;
        $dateHeureEntretien = null;
        try {
            if(empty($idResponsable)){
            if(count($listeCandidats) != 0){
                foreach($listeCandidats as $candidat){
                    $idProfil = Flight::profilsModel()->getById($candidat["id_profil"]);
                    $responsables = Flight::responsableEntretienModel()->getResponsablesEntretienCandidat($candidat["id_profil"]);
                    var_dump($responsables);
                    // return;
                    $i = 0;
                    foreach($responsables as $responsable){
                        $disponibilitesEntretien = Flight::disponibiliteEntretienModel()->getTempsDisponiblesEntretien($responsable["id_responsable"]);
                        $configEntretien = Flight::configEntretienModel()->getConfigurationEntretienResponsable($responsable);
                        $lastPlanning = Flight::planningEntretienModel()->getLastPlanning($responsable["id_responsable"]);
                        $planningEntretien = Flight::planningEntretienModel();
                        $planningEntretien->setIdCandidat($candidat["id_candidat"]);
                        $planningEntretien->setIdResponsable($responsable["id_responsable"]);
                        if($lastPlanning != null){
                            $dateHeureEntretien = $last_planning["date_test"]+$configEntretien["duree_planning"];
                            $planningEntretien->setDateHeureEntretien (DisponibiliteEntretienModel::checkDateDisponible($dateHeureEntretien,$disponibilitesEntretien));
                        }
                        else{
                            $dateHeureEntretien = Flight::disponibiliteEntretienModel()->jourOuvrableEntretien($candidat,$disponibilitesEntretien);
                            $planningEntretien->setDateHeureEntretien($dateHeureEntretien);
                        }
                        $planningEntretien->save();
                    }
                }
            }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        
    }
}
