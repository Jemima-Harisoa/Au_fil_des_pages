<?php
namespace app\models;
use Flight;
use PDO;

class DisponibiliteEntretienModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Récupérer toutes les disponibilités
    public function all(): array {
        $stmt = $this->db->query("SELECT * FROM disponibilite_entretien");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une disponibilité par ID
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM disponibilite_entretien WHERE id_dispo = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Créer une nouvelle disponibilité
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO disponibilite_entretien (id_admin, heure_debut, heure_fin, jour, est_valide)
            VALUES (:id_admin, :heure_debut, :heure_fin, :jour, :est_valide)
        ");
        $stmt->execute([
            ':id_admin' => $data['id_admin'],
            ':heure_debut'    => $data['heure_debut'],
            ':heure_fin'      => $data['heure_fin'],
            ':jour'           => $data['jour'],
            ':est_valide'     => true, 
        ]);
        return (int)$this->db->lastInsertId();
    }

    // Mettre à jour une disponibilité
    public function update(array $data): bool {
        $dispo_entretien = self::find($data["id_dispo"]);
        $historique = Flight::historiqueDisponibiliteEntretienModel();
        $donnees= [
            "id_admin"=>$data["id_admin"],
            "id_dispo" =>$data["id_dispo"],
            "heure_debut" =>$data["heure_debut"],
            "heure_fin" =>$data["heure_fin"],
            "jour" =>$data["jour"],
            "est_valide"=>$data["est_valide"]
        ];
        $historique->create($data);
        $stmt = $this->db->prepare("
            UPDATE disponibilite_entretien 
            SET id_admin = :id_admin,
                heure_debut = :heure_debut,
                heure_fin = :heure_fin,
                jour = :jour,
                est_valide = :est_valide
            WHERE id_dispo = :id
        ");
        return $stmt->execute([
            ':id_admin' => $donnees['id_admin'],
            ':heure_debut'    => $donnees['heure_debut'],
            ':heure_fin'      => $donnees['heure_fin'],
            ':jour'           => $donnees['jour'],
            ':est_valide'     => $donnees['est_valide'],
            ':id'             => $donnees["id_dispo"]
        ]);
    }

    // Supprimer une disponibilité
    public function delete(int $id): bool {
        $result = false;
        $dispo_entretien = self::find($id);
        $dispo_entretien = [
            "id_dispo" =>$dispo_entretien["id_dispo"],
            "id_admin" =>$dispo_entretien["id_admin"],
            "heure_debut" =>$dispo_entretien["heure_debut"],
            "heure_fin" =>$dispo_entretien["heure_fin"],
            "jour" =>$dispo_entretien["jour"],
            "est_valide"=>false
        ];
        $data= [
            "id_dispo" =>$dispo_entretien["id_dispo"],
            "id_admin" =>$dispo_entretien["id_admin"],
            "heure_debut" =>$dispo_entretien["heure_debut"],
            "heure_fin" =>$dispo_entretien["heure_fin"],
            "jour" =>$dispo_entretien["jour"],
            "est_valide"=>$dispo_entretien["est_valide"]
        ];
        self::update($dispo_entretien);
        $result = true;
        return $result;
    }
    public function getTempsDisponiblesEntretien($idAdmin){
        $query = "SELECT * FROM disponibilite_entretien where id_admin = :id_admin ORDER by jour,heure_debut asc ";
        $db = $this->db;
        try{
            if($idAdmin== 0 || $idAdmin == null){
                throw new \Exception("l'id du Responsable doit etre positive et non nul");
            }
            $stmt= $db->prepare($query);
            $stmt->execute(["id_admin"=> $idAdmin]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getDisponibiliteEntretienByIdAdmin($idAdmin, $draw, $start, $length) {
        try {
            $connexion = $this->db;
            // 1️⃣ Compter le nombre total de lignes
            $countSql = "
                SELECT COUNT(*) as total
                FROM disponibilite_entretien de
                WHERE de.id_admin = :id_admin
            ";
            $stmtCount = $connexion->prepare($countSql);
            $stmtCount->bindParam(':id_admin', $idAdmin, PDO::PARAM_INT);
            $stmtCount->execute();
            $totalData = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

            $sql = "
                SELECT 
                    de.id_dispo,
                    de.id_admin,
                    de.heure_debut,
                    de.heure_fin,
                    de.jour,
                    de.est_valide,
                    re.ordre_passage
                FROM disponibilite_entretien de
                join responsable_entretien re on re.id_admin = de.id_admin
                WHERE re.id_admin = :id_admin
                ORDER BY de.jour, de.heure_debut
                OFFSET :start LIMIT :length
            ";
            $stmt = $connexion->prepare($sql);
            $stmt->bindParam(':id_admin', $idAdmin, \PDO::PARAM_INT);
            $stmt->bindParam(':start', $start, \PDO::PARAM_INT);
            $stmt->bindParam(':length', $length, \PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            

            // 3️⃣ Structure de réponse compatible DataTables
            return [
                "draw" => intval($draw),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalData),
                "data" => $rows
            ];

        } catch (PDOException $e) {
            return [
                "error" => "Erreur : " . $e->getMessage()
            ];
        }
    }


    // fonction qui sert a trouver la date de disponibilite d'entretien ouvrable
    public static function getDateNonFerieProche($dateHeure,$listeDisponibiliteEntretien){
        $indiceActuel = 0;
        $diffJour = 0;
        $chiffreJour = DateModel::getJourChiffreDate($dateHeure);
        for($i = 0 ; $i < count($listeDisponibiliteEntretien);$i++){
            if($listeDisponibiliteEntretien[$i]["jour"] == $chiffreJour){
                $indiceActuel = $i;
                break;
            }
        }
        
        while(JourFerieModel::estJourFerie($dateHeure)){
            if($indiceActuel==count($listeDisponibiliteEntretien) -1){
                $diffJour = ($listeDisponibiliteEntretien[0]["jour"]+7)-$listeDisponibiliteEntretien[$indiceActuel];
                $indiceActuel = 0;
            }
            else{
                $diffJour = $listeDisponibiliteEntretien[$indiceActuel+1]["jour"]-$listeDisponibiliteEntretien[$indiceActuel]["jour"];
            }
            $dateHeure = DateModel::ajouterJours($dateHeure,$diffJour);
            $indiceActuel++;
        }
        return $dateHeure;
    }
    public function jourOuvrableEntretien($candidat,$listeDisponibiliteEntretien){
        $compterSuperieur = 0;
        $diffJour = 0;
        $date = new \DateTime($candidat["date_test"]);
        $jourChiffreDate = DateModel::getJourChiffreDate($date);
        $jourDispo=0;
        $resultat= $date;
        $dateTestCandidat = $resultat->format("Y-m-d");
        $dateHeureActuelle = new \DateTime(); 
        $dateActuelle = $dateHeureActuelle->format("Y-m-d");
        for($i= 0;$i < count($listeDisponibiliteEntretien) ;$i++){
            $jourDispo = $listeDisponibiliteEntretien[$i]["jour"];
            if($jourChiffreDate<$jourDispo){
                $diffJour = $jourDispo - $jourChiffreDate;
                DateModel::changerHeure($resultat,\DateTime::createFromFormat('H:i:s', $listeDisponibiliteEntretien[$i]["heure_debut"]));
                break;
            }
            else if($jourChiffreDate == $jourDispo){
                if($dateTestCandidat != $dateActuelle){
                    DateModel::changerHeure($resultat,\DateTime::createFromFormat('H:i:s', $listeDisponibiliteEntretien[$i]["heure_debut"]));
                }
                else{
                    switch($i){
                        case count($listeDisponibiliteEntretien) -1:
                            $diffJour = ($listeDisponibiliteEntretien[0]["jour"]+7)-$listeDisponibiliteEntretien[$i]["jour"];
                            break;
                        default:
                            error_log("TENA NANKATO KOSA");
                            $j = $i;
                            while($jourChiffreDate == $listeDisponibiliteEntretien[$j]["jour"]){
                                $diffJour = $listeDisponibiliteEntretien[$j+1]["jour"]-$listeDisponibiliteEntretien[$j]["jour"];
                                $nouvelleDate = DateModel::ajouterJours($resultat,$diffJour);
                                DateModel::changerHeure($nouvelleDate,\DateTime::createFromFormat('H:i:s', $listeDisponibiliteEntretien[$j+1]["heure_debut"]));
                                $resultat = $nouvelleDate;
                                $j++;
                            }
                    }
                                     
                    $resultat = DateModel::ajouterJours($resultat,$diffJour);
                    DateModel::changerHeure($resultat,\DateTime::createFromFormat('H:i:s', $listeDisponibiliteEntretien[$i]["heure_debut"]));
                }
                break;
            }
            else{
                $compterSuperieur++;
            }
        }
        
        
        $resultat = DateModel::ajouterJours($resultat,$diffJour);
        if ($compterSuperieur == count($listeDisponibiliteEntretien) && !empty($listeDisponibiliteEntretien)) {
            $diffJour = ($listeDisponibiliteEntretien[0]["jour"]+7)-$jourChiffreDate;
            $resultat = DateModel::ajouterJours($date,$diffJour);
            DateModel::changerHeure($resultat,\DateTime::createFromFormat('H:i:s', $listeDisponibiliteEntretien[0]["heure_debut"]));
        }
        
        $resultat = self::getDateNonFerieProche($resultat,$listeDisponibiliteEntretien);
        return $resultat;
    }
    public static function checkDateDisponible($dateHeure,$listeDisponibiliteEntretien){
        $diffJour = 0;
        for($i=0; $i<count($listeDisponibiliteEntretien);$i++){
            if($listeDisponibiliteEntretien[$i]["jour"] == DateModel::getJourChiffreDate($dateHeure)){
                if(DateModel::recupererHeure($dateHeure) <= $listeDisponibiliteEntretien[$i]["heure_fin"]){
                    return DisponibiliteEntretienModel::getDateNonFerieProche($dateHeure,$listeDisponibiliteEntretien);
                }
                else{
                    switch($i){
                        case count($listeDisponibiliteEntretien)-1:
                            $diffJour = ($listeDisponibiliteEntretien[0]["jour"]+7)-$listeDisponibiliteEntretien[$i]["jour"];
                            break;
                        default:
                            $diffJour = $listeDisponibiliteEntretien[$i+1]["jour"]-$listeDisponibiliteEntretien[$i]["jour"];
                            break;
                    }
                    $dateHeure = DateModel::ajouterJours($dateHeure,$diffJour);
                    DateModel::changerHeure($dateHeure,new \DateTime($listeDisponibiliteEntretien[$i]["heure_debut"]));
                }
            }
        }
        return DisponibiliteEntretienModel::getDateNonFerieProche($dateHeure,$listeDisponibiliteEntretien);
    }
    public function getDisponibiliteSuivantByDate($dateHeure,$listeDisponibiliteEntretien){
        $resultat = $dateHeure;
        $date = $dateHeure->format("Y-m-d");
        $dateHeureActuelle = new \DateTime();
        $dateActuelle =  $dateHeureActuelle->format("Y-m-d");
        $jourChiffreDate = DateModel::getJourChiffreDate($dateHeure);
        if($dateActuelle == $date){
            for($i=0 ; $i<count($listeDisponibiliteEntretien);$i++){
                if($listeDisponibiliteEntretien[$i]["jour"] == $jourChiffreDate){
                    $diffJour = $listeDisponibiliteEntretien[$i+1]["jour"]  - $listeDisponibiliteEntretien[$i]["jour"]; 
                    $resultat = DateModel::ajouterJours($dateHeure,$diffJour);
                }
            }
        }
        return $result;
    }
}
