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
                $etatModel = new EtatModel();
                $stmt = $db->prepare("INSERT INTO planning_entretien (id_candidat, id_responsable,date_heure_entretien, score_entretien, etat, id_appreciation) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $this->id_candidat,
                    $this->id_responsable,
                    $this->date_heure_entretien->format('Y-m-d H:i:s'),
                    $this->score_entretien,
                    $etatModel->findById(1)["id_etat"],
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

    public function entretienExiste($id_candidat, $date_heure_entretien) {
        $sql = "SELECT COUNT(*) as total 
                FROM planning_entretien 
                WHERE id_candidat = :id_candidat 
                AND date_heure_entretien = :date_heure_entretien";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_candidat' => $id_candidat,
            ':date_heure_entretien' => $date_heure_entretien
        ]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    }
    // Récupérer tous les entretiens
    public static function all() {
        $db = Flight::db();
        $stmt = $db->query("SELECT * FROM planning_entretien");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    // Récupérer un entretien par ID
    public static function find($id) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM planning_entretien WHERE id_entretien = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
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
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ?: null; 

        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la récupération du planning : " . $e->getMessage());
        }
    }
    
    public function planifierEntretien($listeCandidats,$idAdmin){
        $candidats = null;
        $responsables = null;
        $dateHeureEntretien = null;
        try {
                $candidats = Flight::responsableEntretienModel()->getListeCandidatsAEntretenir($listeCandidats,$idAdmin);
                foreach($listeCandidats as $candidat){
                    $responsables = Flight::responsableEntretienModel()->getResponsablesEntretienCandidat($candidat["id_profil"]);
                    $responsables = Flight::responsableEntretienModel()->getPropresResponsablesEntretiens($responsables,$idAdmin);
                    foreach($responsables as $responsable){       
                        $disponibilitesEntretien = Flight::disponibiliteEntretienModel()->getTempsDisponiblesEntretien($responsable["id_responsable"]);
                        $configEntretien = Flight::configEntretienModel()->getConfigurationEntretienResponsable($responsable);
                        $lastPlanning = Flight::planningEntretienModel()->getLastPlanning($responsable["id_responsable"]);
                        $planningEntretien = Flight::planningEntretienModel();
                        $planningEntretien->setIdCandidat($candidat["id_candidat"]);
                        $planningEntretien->setIdResponsable($responsable["id_responsable"]);
                        if($lastPlanning && is_array($lastPlanning)){
                            $dateModel= new DateModel($lastPlanning["date_heure_entretien"]);
                            $dateHeureEntretien= $dateModel->addInterval($configEntretien["duree_entretien"]);
                            $planningEntretien->setDateHeureEntretien (DisponibiliteEntretienModel::checkDateDisponible($dateHeureEntretien,$disponibilitesEntretien));
                        }
                        else{
                            $dateHeureEntretien = Flight::disponibiliteEntretienModel()->jourOuvrableEntretien($candidat,$disponibilitesEntretien);
                            $planningEntretien->setDateHeureEntretien($dateHeureEntretien);
                        }
                        $planningEntretien->save();
                    }
                }
           
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getEntretiensParEtat($etat) {
        $db = Flight::db();

        $sql = "
        SELECT pe.*,
       te.*,
       vrp.nom as nom_responsable,
       vrp.prenom as prenom_responsable,
       e.nom as etat
    FROM planning_entretien pe
    JOIN (
        SELECT vcp.id_candidat,
                vcp.nom_candidat,
                vcp.prenom_candidat,
                vcp.date_naissance,
                vcp.titre as profil,
                te.score_test,  
                te.date_test
            FROM tests  te
        join v_candidats_personnes vcp 
        ON te.id_candidat = vcp.id_candidat
    )
    as te
    ON pe.id_candidat = te.id_candidat
    JOIN v_responsable_personnes vrp
    on vrp.id_responsable = pe.id_responsable
    JOIN etat e
    ON e.id_etat = pe.etat
    WHERE pe.etat = ? 
        ";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([$etat]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            Flight::halt(500, "Erreur DB: " . $e->getMessage());
        }
    }
    public function checkCandidatsInEntretien($candidats){
        
        $candidatsEntretien = self::all();
        $compteur = 0;
        foreach($candidats as $candidat){
            if(CandidatModel::estDansLaListe($candidat,$candidatsEntretien)){
                $compteur++;
            }
        }
        if($compteur == count($candidats)){
            return true;
        }
        return false;
    }
    public function filtreEntretien($data) {
    $pdo = $this->db;

    // Récupérer tous les responsables liés à l'admin
    $responsables = Flight::responsableEntretienModel()->findByIdAdmin($data["id_admin"]);
    if (!$responsables || count($responsables) === 0) {
        return [
            "draw" => 1,
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        ];
    }

    // Construire la liste des IDs responsables
    $ids = array_column($responsables, "id_responsable");
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // Requête principale
    $sql = "
        SELECT 
            pe.*, 
            te.nom_candidat, te.prenom_candidat, te.profil, te.date_naissance, te.score_test, te.date_test,
            vrp.nom AS nom_responsable, vrp.prenom AS prenom_responsable,
            e.nom AS nom_etat
        FROM planning_entretien pe
        JOIN (
            SELECT 
                vcp.id_candidat,
                vcp.nom_candidat,
                vcp.prenom_candidat,
                vcp.titre AS profil,
                vcp.date_naissance,
                te.score_test,  
                te.date_test
            FROM tests te
            JOIN v_candidats_personnes vcp ON te.id_candidat = vcp.id_candidat
        ) te ON pe.id_candidat = te.id_candidat
        JOIN v_responsable_personnes vrp ON vrp.id_responsable = pe.id_responsable
        JOIN etat e ON e.id_etat = pe.etat
        WHERE vrp.id_responsable IN ($placeholders)
    ";

    $params = $ids;

    // Ajout des filtres dynamiques
    if (!empty($data["candidat"])) {
        $sql .= " AND (te.nom_candidat ILIKE ? OR te.prenom_candidat ILIKE ?)";
        $params[] = "%{$data['candidat']}%";
        $params[] = "%{$data['candidat']}%";
    }
    if (!empty($data["age_min"])) {
        $sql .= " AND EXTRACT(YEAR FROM AGE(te.date_naissance)) >= ?";
        $params[] = $data["age_min"];
    }
    if (!empty($data["age_max"])) {
        $sql .= " AND EXTRACT(YEAR FROM AGE(te.date_naissance)) <= ?";
        $params[] = $data["age_max"];
    }
    if (!empty($data["responsable"])) {
        $sql .= " AND (vrp.nom ILIKE ? OR vrp.prenom ILIKE ?)";
        $params[] = "%{$data['responsable']}%";
        $params[] = "%{$data['responsable']}%";
    }
    if (!empty($data["profil_candidat"])) {
        $sql .= " AND te.profil ILIKE ?";
        $params[] = "%{$data['profil_candidat']}%";
    }
    if (!empty($data["score_min"])) {
        $sql .= " AND te.score_test >= ?";
        $params[] = $data["score_min"];
    }
    if (!empty($data["score_max"])) {
        $sql .= " AND te.score_test <= ?";
        $params[] = $data["score_max"];
    }
    if (!empty($data["date_test"])) {
        $sql .= " AND te.date_test = ?";
        $params[] = $data["date_test"];
    }
    if (!empty($data["date_heure_entretien"])) {
        $sql .= " AND pe.date_heure_entretien::date = ?";
        $params[] = $data["date_heure_entretien"];
    }

    // Compter le total filtré
    $count_sql = "SELECT COUNT(*) FROM ($sql) AS sub";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $recordsFiltered = $stmt->fetchColumn();

    // Pagination DataTables
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;

    $sql .= " ORDER BY pe.date_heure_entretien DESC OFFSET ? LIMIT ?";
    $params[] = $start;
    $params[] = $length;

    // Exécution finale
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $dataRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Total général (sans filtre)
    $total_sql = "SELECT COUNT(*) FROM planning_entretien";
    $totalRecords = $pdo->query($total_sql)->fetchColumn();

    return [
        "draw" => $draw,
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($recordsFiltered),
        "data" => $dataRows
        ];
    }

}