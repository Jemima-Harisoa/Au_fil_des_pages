<?php
namespace app\models;

use PDO;

class DisponibiliteEntretienModel {
    private PDO $db;

    public function __construct(PDO $db) {
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
            INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour, est_valide)
            VALUES (:id_responsable, :heure_debut, :heure_fin, :jour, :est_valide)
        ");
        $stmt->execute([
            ':id_responsable' => $data['id_responsable'],
            ':heure_debut'    => $data['heure_debut'],
            ':heure_fin'      => $data['heure_fin'],
            ':jour'           => $data['jour'],
            ':est_valide'     => $data['est_valide'] ?? true, // valeur par défaut
        ]);
        return (int)$this->db->lastInsertId();
    }

    // Mettre à jour une disponibilité
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE disponibilite_entretien 
            SET id_responsable = :id_responsable,
                heure_debut = :heure_debut,
                heure_fin = :heure_fin,
                jour = :jour,
                est_valide = :est_valide
            WHERE id_dispo = :id
        ");
        return $stmt->execute([
            ':id_responsable' => $data['id_responsable'],
            ':heure_debut'    => $data['heure_debut'],
            ':heure_fin'      => $data['heure_fin'],
            ':jour'           => $data['jour'],
            ':est_valide'     => $data['est_valide'],
            ':id'             => $id
        ]);
    }

    // Supprimer une disponibilité
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM disponibilite_entretien WHERE id_dispo = ?");
        return $stmt->execute([$id]);
    }
    public function getTempsDisponiblesEntretien($idResponsable){
        $query = "SELECT * FROM disponibilite_entretien where id_responsable = :id_responsable ORDER by jour asc";
        $db = $this->db;
        try{
            if($idResponsable== 0 || $idResponsable == null){
                throw new \Exception("l'id du Responsable doit etre positive et non nul");
            }
            $stmt= $db->prepare($query);
            $stmt->execute(["id_responsable"=> $idResponsable]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    // fonction qui sert a trouver la date de disponibilite d'entretien ouvrable
    public static function getDateNonFerieProche($dateHeure,$listeDisponibiliteEntretien){
        $indiceActuel = 0;
        $diffJour = 0;
        $chiffreJour = DateModel::getJourChiffreDate($dateHeure);
        for($i = 0 ; $i < count($listeDisponibiliteEntretien);$i++){
            if($listeDisponibiliteEntretien[$i]["jour"] ==$chiffreJour){
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
                $diffJour = $listeDisponibiliteEntretien[$indiceActuel+1]-$listeDisponibiliteEntretien[$indiceActuel];
            }
            $dateHeure = DateModel::ajouterJours($dateHeure,$diffJour);
            $indiceActuel++;
        }
        return $dateHeure;
    }
    public static function checkDateDisponible($dateHeure,$listeDisponibiliteEntretien){
        $diffJour = 0;
        for($i=0; $i<count($listeDisponibiliteEntretien);$i++){
            if($listeDisponibiliteEntretien[$i]["jour"] == DateModel::getJourChiffreDate($dateHeure)){
                if(DateModel::recupererHeure($dateHeure) <= $listeDisponibiliteEntretien[$i]["heure_fin"]){
                    return DisponibiliteEntretienModel::getDateNonFerieProche($dateHeure,$listeDisponibiliteEntretien);
                }
                else{
                    switch($i ){
                        case count($listeDisponibiliteEntretien)-1:
                            $diffJour = ($listeDisponibiliteEntretien[0]+7)-$listeDisponibiliteEntretien[$i]["jour"];
                            break;
                        default:
                            error_log("diffJour: ".$listeDisponibiliteEntretien[$i]["jour"]);
                            error_log("diffJour:".$diffJour);
                            $diffJour = $listeDisponibiliteEntretien[$i+1]["jour"]-$listeDisponibiliteEntretien[$i]["jour"];
                            break;
                    }
                    $dateHeure = DateModel::ajouterJours($dateHeure,$diffJour);
                    DateModel::changerHeure($dateHeure,new \DateTime($listeDisponibiliteEntretien[$i]["heure_debut"]));
                }
            }
        }
        return $dateHeure;
    }
}