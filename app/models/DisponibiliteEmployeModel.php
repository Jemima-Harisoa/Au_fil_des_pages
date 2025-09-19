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
    public function getIdDispo() {
        return $this->id_dispo;
    }

    public function setIdDispo($id_dispo) {
        $this->id_dispo = $id_dispo;
    }

    // Getter et Setter pour id_employe
    public function getIdEmploye() {
        return $this->id_employe;
    }

    public function setIdEmploye($id_employe) {
        $this->id_employe = $id_employe;
    }

    // Getter et Setter pour heure_debut
    public function getHeureDebut() {
        return $this->heure_debut;
    }

    public function setHeureDebut($heure_debut) {
        $this->heure_debut = $heure_debut;
    }

    // Getter et Setter pour heure_fin
    public function getHeureFin() {
        return $this->heure_fin;
    }

    public function setHeureFin($heure_fin) {
        $this->heure_fin = $heure_fin;
    }

    // Getter et Setter pour jour
    public function getJour() {
        return $this->jour;
    }

    public function setJour($jour) {
        $this->jour = $jour;
    }
    // Méthode pour insérer une nouvelle disponibilité
    public function save(){
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
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Récupérer une disponibilité par ID
    public static function find($id) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM disponibilite_employe WHERE id_dispo = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     /**
     * Récupère les disponibilités validées pour un département donné,
     * avec possibilité d'ordonner par heure_debut selon $orderGrandeur ('ASC' ou 'DESC').
     */
    public static function getByDepartement($id_departement, $orderGrandeur = 'ASC') {
        // On s'assure que l'ordre est valide pour éviter les injections
        $orderGrandeur = strtoupper($orderGrandeur) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT id_employe,
                       heure_debut,
                       heure_fin,
                       numero_jour,
                       est_valide,
                       id_departement,
                       poste
                  FROM v_disponibilite_employe_valide
                 WHERE id_departement = :id_departement
              ORDER BY heure_debut $orderGrandeur";

        try {
            // Récupération de la connexion PDO depuis Flight
            $pdo = Flight::db(); // suppose que tu as mis la connexion PDO dans Flight::set('pdo', $pdo)
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id_departement', $id_departement, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC); // ou FETCH_OBJ si tu veux des objets
        } catch (\PDOException $e) {
            // Gérer l'erreur si besoin
            error_log("Erreur PDO: " . $e->getMessage());
            return [];
        }
    }

    //liste des disponibilites des employes doivent etre croissants
    public static function getDateNonFerieProche(\DateTime $dateHeure,array $disponibilitesEmployes):\DateTime{
        $indiceActuel = 0;
        $jourChiffreDate = DateModel::getJourChiffreDate($dateHeure);
        $diffJour = 0;
        for($i = 0; $i<count($disponibilitesEmployes);$i++){
            if(DateModel::getJourLettreEnChiffre($disponibilitesEmployes[$i]->getJour())== $jourChiffreDate){
                $indiceActuel = $i;
                break;
            }
        }
        while(DateModel::estJourFerie($dateHeure)){
            if($indiceActuel == count($disponibilitesEmployes)-1){
                $diffJour = ($disponibilitesEmployes[0]+7)-DateModel::getJourLettreEnChiffre($disponibilitesEmployes[$indiceActuel]->getJour());
            }
            else{
                $diffJour = $disponibilitesEmployes[$indiceActuel+1]-$disponibilitesEmployes[$indiceActuel];  
            }
            $dateHeure = DateModel::ajouterJour($dateHeure,$diffJour);
            $indiceActuel++;
        }
        return $dateHeure;
    }
}

