<?php
namespace app\models;

use Flight;
use Flight;
use \PDOException;
use \Exception;


// class pour la vue v_heure_supp

// class pour la vue v_heure_supp
class HeureSupplementaireModel extends Query {

    /**
     * Enregistre une nouvelle ligne
     * @param array $parametres
     * @return int id inséré
     * @throws Exception
     */
    public function construct($db){
        $this->db = $db;
    }

    /**
     * Enregistre une nouvelle ligne
     * @param array $parametres
     * @return int id inséré
     * @throws Exception
     */
    public function construct($db){
        $this->db = $db;
    }

        /**
        /**
     * Enregistre une nouvelle ligne
     * @param array $parametres
     * @return int id inséré
     * @throws Exception
     */
    public static function save($parametres) {
        try {
            self::handleException($parametres);
            $sql = "INSERT INTO heure_supplementaire 
                (id_employe, heure_effectue, mois, annee, numero_semaine)
                (id_employe, heure_effectue, mois, annee, numero_semaine)
                VALUES (?, ?, ?, ?, ?)
                RETURNING id";
            
            
            $id = self::query($sql, [
                $parametres['id_employe'],
                $parametres['nombre_heure_effectue'],
                $parametres['mois'],
                $parametres['annee'],
                $parametres['numero_semaine']
            ]);

            if (!$id) {
                throw new Exception("Impossible d'enregistrer l'heure supplémentaire.");
            }

            return $id;
        } catch (PDOException $e) {
            throw new Exception("Erreur PDO : " . $e->getMessage());
        }
    }
    /**
     * Met à jour une ligne existante
     * @param array $parametres doit contenir 'id'
     * @return bool
     * @throws Exception
     */
    public static function update($parametres) {
        if (!isset($parametres['id'])) {
            throw new Exception("L'identifiant est requis pour la mise à jour.");
        }
        self::handleException($parametres);
        try {

            $getLast = getById($parametres['id_employe']);
            if($getLast["nombre_heure_effectue"] > 20){
                throw new Exception("le nombre d'heure supplémentaire ne doit pas dépasser 20 heures par semaine");
            } 

            $sql = "UPDATE heure_supplementaire 
                    SET id_employe = ?, nombre_heure_effectue = ?, mois = ?, annee = ?, numero_semaine = ?
                    WHERE id = ?";

            $ok = self::query($sql, [
                $parametres['id_employe'],
                $parametres['heure_effectue'],
                $parametres['mois'],
                $parametres['annee'],
                $parametres['numero_semaine'],
                $parametres['id']
            ]);

            if (!$ok) {
                throw new Exception("Aucune ligne mise à jour. Vérifiez l'identifiant.");
            }

            return true;

        } catch (PDOException $e) {
            throw new Exception("Erreur PDO : " . $e->getMessage());
        }
    }

        /**
     * Récupère les heures supplémentaires d'un employé pour un mois et une année donnés
     * @param int $id_employe
     * @param string $date format YYYY-MM-DD
     * @return array
     * @throws Exception si id_employe ou date sont invalides ou aucun résultat
     */
    public static function getByIdEmployeAndDate($id_employe, $date) {
        // Vérification des paramètres
        if (empty($id_employe) || $id_employe == 0) {
            throw new Exception("L'identifiant de l'employé est invalide.");
        }

        if (empty($date)) {
            throw new Exception("La date fournie est invalide.");
        }

        // Extraire le mois et l'année de la date
        $timestamp = strtotime($date->format("Y-m-d H:i:s"));
        if ($timestamp === false) {
            throw new Exception("La date fournie n'est pas valide.");
        }

        $mois = date('m', $timestamp);
        $annee = date('Y', $timestamp);
        try {
            $sql = "SELECT * FROM heure_supplementaire 
                    WHERE id_employe = ? 
                      AND EXTRACT(MONTH FROM date_heure_debut) = ? 
                      AND EXTRACT(YEAR FROM date_heure_debut) = ?";

            $result = self::query($sql, [$id_employe, $mois, $annee]);

            if (!$result || count($result) === 0) {
                throw new Exception("Aucune heure supplémentaire trouvée pour cet employé à la date spécifiée.");
            }

            return $result;

        } catch (PDOException $e) {
            throw new Exception("Erreur PDO : " . $e->getMessage());
        }
    }
    public static function handleException(array $parametres):void {
        if(empty($parametres['id_employe'])){
                throw new Exception("l'id employe est obligatoire");
            }
            else if(empty($parametres['heure_effectue'])){
                throw new Exception("l'heure effectué est obligatoire");
            }
            else if(empty($parametres['mois'])){
                throw new Exception("Le mois est obligatoire");
            }
            else if(empty($parametres['annee'])){
                throw new Exception("L'année est obligatoire");
            }
            else if(empty(empty($parametres['numero_semaine']))){
                throw new Exception("Le numéro de semaine est obligatoire");
            }
    }
    public function getTypeMajoration($heureSupplementaire):int {
        $idEmploye = $heureSupplementaire["id_employe"];
        $date_heure_debut = new \DateTime($heureSupplementaire["date_heure_debut"]);
        $dateStr =  $date_heure_debut->format("Y-m-d H:i:s");
        $moisDateStr = date("m",strtotime($dateStr));
        $anneeDateStr = date("Y",strtotime($dateStr));
        $listeHsSemaine = $this->getAllByIdEmployeAndSemaineDate($idEmploye,$dateStr);
        $dateTempo = new DateModel($listeHsSemaine[0]["heure_effectue"]);
        $sommeHeures = count($listeHsSemaine) == 1 ? new DateModel($dateTempo->format("H:i:s")): new DateModel("00:00:00");
        
        $moisDate = 0;
        $anneeDate = 0;
        
        foreach($listeHsSemaine as $hs){
            $moisDate = date("m",strtotime($hs["date_heure_debut"]));
            $anneeDate = date("Y",strtotime($hs["date_heure_debut"]));
            if($hs["date_heure_debut"] == $dateStr){
                if($sommeHeures->getDateTime() >= new \DateTime("00:00:00") && $sommeHeures->getDateTime() <= new \DateTime("08:00:00")){
                    return 1;
                }
                else if($sommeHeures->getDateTime() > new \DateTime("08:00:00") && $sommeHeures->getDateTime() <= new \DateTime("20:00:00")){
                    return 2;
                }
                else if($sommeHeures->getDateTime() > new \DateTime("20:00:00")){
                    return 0;
                }
            }

            if($moisDate == $anneeDateStr && $anneeDate == $anneeDateStr){
                $sommeHeures = DateModel::addInterval($hs["heureEffectue"]);
            }
        }
        if($sommeHeures->getDateTime() == new \DateTime("00:00:00")){
            return 0;
        }
    }
    public function hasHeureDeNuit($hs){
        if(empty($hs)){
            throw new \Exception("heure supplementaire non fournie pour la verification de nuit");
        }
        $dhDebut = new \DateTime($hs["date_heure_debut"]);
        $dhDebutClone = clone $dhDebut;
        $dhFin = new \DateTime($hs["date_heure_fin"]);;
        $dhTempo = DateModel::ajouterJours($dhDebutClone,1);
        $dhInf = new \DateTime($dhDebut->format("Y-m-d")." 22:00:00");
        $dhSup = new \DateTime($dhTempo->format("Y-m-d")."05:00:00");
        echo "debut: ".$dhDebut->format("Y-m-d H:i:s")."\n";
        echo "fin: ".$dhFin->format("Y-m-d H:i:s")."\n";
        echo "inf: ".$dhInf->format("Y-m-d H:i:s")."\n";
        echo "sup: ".$dhSup->format("Y-m-d H:i:s")."\n";
        echo ($dhDebut < $dhInf && $dhFin < $dhInf) ? "tsy misy":" misy";
        if($dhDebut < $dhInf && $dhFin < $dhInf){
            error_log("tsy misy");
            return false;
        }
        if($dhDebut >= $dhInf && $dhFin >= $dhInf){
            return false;
        }
        return true;
    }
    public function getAllByIdEmployeAndSemaineDate(int $idEmploye,string $date){
        $query = "select * 
                from 
                    heure_supplementaire where numero_semaine=:numero_semaine and id_employe=:id_employe";
        $results = array();
        $i = 0;
        try{
            if(empty($idEmploye)||empty($date)){
                throw new \Exception("idEmploye ou date non fournie pour la liste des heures sup");
            }
            $results = parent::query($query ,[date('W',strtotime($date)),$idEmploye]);
            
        }
        catch(\Exception $e){
            throw new \Exception("erreur".$e->getMessage());
        }
        return $results;
    }
    public function calculerHeureDeNuitsAvecouSansExces($hs){
        $resultats = array();
        $dhDebut = new \DateTime($hs["date_heure_debut"]);
        $dhDebutClone = clone $dhDebut;
        $dhFin = new \DateTime($hs["date_heure_fin"]);
        $dhTempo = DateModel::ajouterJours($dhDebutClone,1);
        $dhInf = new \DateTime($dhDebut->format("Y-m-d")." 22:00:00");
        $dhSup = new \DateTime($dhTempo->format("Y-m-d")."05:00:00");
        $dmInf = new DateModel($dhInf->format("Y-m-d H:i:s"));
        $dmSup = new DateModel($dhSup->format("Y-m-d H:i:s"));
        $dmDhDebut = new DateModel($dhDebut->format("Y-m-d H:i:s"));
        $dmDhFin = new DateModel($dhFin->format("Y-m-d H:i:s"));

        $diferenceIntervalleExces = null;
        $diferenceIntervalleDeNuit = null;
        $resultats = [
            "exces"=>new \DateTime($dhDebut->format("Y-m-d")."00:00:00"),
            "heure_de_nuit"=>new \DateTime($dhDebut->format("Y-m-d")."00:00:00"),
            "nombre_heure_de_nuit"=> 0,
            "nombre_heure_exces"=>0
        ];
        if($this->hasHeureDeNuit($hs)){
            if($dhDebut < $dhInf){
                
                $differenceIntervalleExces = $dmInf->differenceIntervalle($dmDhDebut)->format("%H:%i:%s");
                 $resultats["exces"] = (new DateModel($resultats["exces"]->format("Y-m-d H:i:s")))->addInterval($differenceIntervalleExces);
                $resultats["nombre_heure_exces"]++;
                if($dhFin > $dhInf){
                    if($dhFin  > $dhInf)
                    {
                        if($dhFin > $dhSup)
                        {
                            $differenceIntervalleNuit = $dmSup->differenceIntervalle($dmInf)->format("%H:%i:%s");
                            $differenceIntervalleExces = $dmDhFin->differenceIntervalle($dmSup)->format("%H:%i:%s");
                            $resultats["heure_de_nuit"] = (new DateModel($resultats["heure_de_nuit"]->format("Y-m-d H:i:s")))->addInterval($differenceIntervalleNuit);
                            $resultats["exces"] = (new DateModel($resultats["exces"]->format("Y-m-d H:i:s")))->addInterval($differenceIntervalleExces);
                            $resultats["nombre_heure_de_nuit"]++;
                            $resultats["nombre_heure_exces"]++;
                        }
                        else
                        {
                            $differenceIntervalleNuit = $dmDhFin->differenceIntervalle($dmInf)->format("%H:%i:%s");
                            $resultats["heure_de_nuit"] = (new DateModel($resultats["heure_de_nuit"]->format("Y-m-d H:i:s")))->addInterval($differenceIntervalleNuit);
                            $resultats["nombre_heure_de_nuit"]++;
                        }
                    }
                }
            }
            else{
                if($dhFin <= $dhSup){
                    $differenceIntervalleNuit = $dmDhDebut->differenceIntervalle($dmDhFin)->format("%H:%i:%s");
                    $resultats["heure_de_nuit"] = (new DateModel($resultats["heure_de_nuit"]->format("Y-m-d H:i:s")))->addInterval($differenceIntervalleNuit);
                    $resultats["nombre_heure_de_nuit"] ++;
                }
                else{
                    $differenceIntervalleNuit = $dmDhDebut->differenceIntervalle($dmSup)->format("%H:%i:%s");
                    $differenceIntervalleExces =  $dmSup->differenceIntervalle($dmDhFin)->format("%H:%i:%s");
                    $resultats["heure_de_nuit"] = (new DateModel($resultats["heure_de_nuit"]->format("Y-m-d H:i:s")))->addInterval($differenceIntervalleNuit);
                    $resultats["exces"] = $resultats["exces"]->addInterval($differenceIntervalleExces);
                    $resultats["nombre_heure_de_nuit"]++;
                    $resultats["nombre_heure_exces"]++;
                }
            }
        }        
        return $resultats;
    }
    public function estWeekEnd($hs){
        if(empty($hs)){
            throw new \Exception("heure supplementaire non fournie pour la verification de week-end");
        }
        $dhDebut = new \DateTime($hs["date_heure_debut"]);
        $jour = DateModel::getJourChiffreDate($dhDebut);
        if($jour ==7){
            return true;
        }
        return false;
    }
    
    public function getTypeMajoration($heureSupplementaire):int {
        $idEmploye = $heureSupplementaire["id_employe"];
        $date_heure_debut = new \DateTime($heureSupplementaire["date_heure_debut"]);
        $dateStr =  $date_heure_debut->format("Y-m-d H:i:s");
        $moisDateStr = date("m",strtotime($dateStr));
        $anneeDateStr = date("Y",strtotime($dateStr));
        $listeHsSemaine = $this->getAllByIdEmployeAndSemaineDate($idEmploye,$dateStr);
        $dateTempo = new DateModel($listeHsSemaine[0]["heure_effectue"]);
        $sommeHeures = count($listeHsSemaine) == 1 ? new DateModel($dateTempo->format("H:i:s")): new DateModel("00:00:00");
        
        $moisDate = 0;
        $anneeDate = 0;
        
        foreach($listeHsSemaine as $hs){
            $moisDate = date("m",strtotime($hs["date_heure_debut"]));
            $anneeDate = date("Y",strtotime($hs["date_heure_debut"]));
            if($hs["date_heure_debut"] == $dateStr){
                if($sommeHeures->getDateTime() >= new \DateTime("00:00:00") && $sommeHeures->getDateTime() <= new \DateTime("08:00:00")){
                    return 1;
                }
                else if($sommeHeures->getDateTime() > new \DateTime("08:00:00") && $sommeHeures->getDateTime() <= new \DateTime("20:00:00")){
                    return 2;
                }
                else if($sommeHeures->getDateTime() > new \DateTime("20:00:00")){
                    return 0;
                }
            }

            if($moisDate == $anneeDateStr && $anneeDate == $anneeDateStr){
                $sommeHeures = DateModel::addInterval($hs["heureEffectue"]);
            }
        }
        if($sommeHeures->getDateTime() == new \DateTime("00:00:00")){
            return 0;
        }
    }
    public function hasHeureDeNuit($hs){
        if(empty($hs)){
            throw new \Exception("heure supplementaire non fournie pour la verification de nuit");
        }
        $dhDebut = new \DateTime($hs["date_heure_debut"]);
        $dhDebutClone = clone $dhDebut;
        $dhFin = new \DateTime($hs["date_heure_fin"]);;
        $dhTempo = DateModel::ajouterJours($dhDebutClone,1);
        $dhInf = new \DateTime($dhDebut->format("Y-m-d")." 22:00:00");
        $dhSup = new \DateTime($dhTempo->format("Y-m-d")."05:00:00");
        if($dhDebut< $dhInf){
            error_log("tokony hankto io".($dhDebut< $dhInf)?"True":"False");
            if($dhFin> $dhSup || $dhFin<= $dhSup){
                error_log("energy");
                return true;
            }
            
        }
        else {
            return true;
        }
        return false;
    }
    public function getAllByIdEmployeAndSemaineDate(int $idEmploye,string $date){
        $query = "select * 
                from 
                    heure_supplementaire where numero_semaine=:numero_semaine and id_employe=:id_employe";
        $results = array();
        $i = 0;
        try{
            if(empty($idEmploye)||empty($date)){
                throw new \Exception("idEmploye ou date non fournie pour la liste des heures sup");
            }
            $results = parent::query($query ,[date('W',strtotime($date)),$idEmploye]);
            
        }
        catch(\Exception $e){
            throw new \Exception("erreur".$e->getMessage());
        }
        return $results;
    }
    public function calculerHeureDeNuitsAvecouSansExces($hs){
        $resultats = array();
        $dhDebut = new \DateTime($hs["date_heure_debut"]);
        $dhDebutClone = clone $dhDebut;
        $dhFin = new \DateTime($hs["date_heure_fin"]);
        $dhTempo = DateModel::ajouterJours($dhDebutClone,1);
        $dhInf = new \DateTime($dhDebut->format("Y-m-d")." 22:00:00");
        $dhSup = new \DateTime($dhTempo->format("Y-m-d")."05:00:00");
        $dmInf = new DateModel($dhInf->format("Y-m-d H:i:s"));
        $dmSup = new DateModel($dhSup->format("Y-m-d H:i:s"));
        $dmDhDebut = new DateModel($dhDebut->format("Y-m-d H:i:s"));
        $dmDhFin = new DateModel($dhFin->format("Y-m-d H:i:s"));

        $diferenceIntervalleExces = null;
        $diferenceIntervalleDeNuit = null;
        $resultats = [
            "exces"=>new \DateTime($dhDebut->format("Y-m-d")."00:00:00"),
            "heure_de_nuit"=>new \DateTime($dhDebut->format("Y-m-d")."00:00:00"),
            "nombre_heure_de_nuit"=> 0,
            "nombre_heure_exces"=>0
        ];
        if($this->hasHeureDeNuit($hs)){
            if($dhDebut < $dhInf){
                $differenceIntervalleExces = $dmInf->differenceIntervalle($dmDhDebut)->format("%H:%i:%s");
                 $resultats["exces"] = (new DateModel($resultats["exces"]->format("Y-m-d H:i:s")))->addInterval($differenceIntervalleExces);
                $resultats["nombre_heure_exces"]++;
                if($dhFin > $dhInf){
                    if($dhFin  > $dhInf)
                    {
                        if($dhFin > $dhSup)
                        {
                            $differenceIntervalleNuit = $dmSup->differenceIntervalle($dmInf)->format("%H:%i:%s");
                            $differenceIntervalleExces = $dmDhFin->differenceIntervalle($dmSup)->format("%H:%i:%s");
                            $resultats["heure_de_nuit"] = new DateModel($resultats["heure_de_nuit"]->format("Y-m-d H:i:s"))->addInterval($differenceIntervalleNuit);
                            $resultats["exces"] = (new DateModel($resultats["exces"]->format("Y-m-d H:i:s")))->addInterval($differenceIntervalleExces);
                            $resultats["nombre_heure_de_nuit"]++;
                            $resultats["nombre_heure_exces"]++;
                        }
                        else
                        {
                            $differenceIntervalleNuit = $dmDhFin->differenceIntervalle($dmInf)->format("%H:%i:%s");
                            $resultats["heure_de_nuit"] = (new DateModel($resultats["heure_de_nuit"]->format("Y-m-d H:i:s")))->addInterval($differenceIntervalleNuit);
                            $resultats["nombre_heure_de_nuit"]++;
                        }
                    }
                }
            }
            else{
                if($dhFin <= $dhSup){
                    $differenceIntervalleNuit = $dmDhDebut->differenceIntervalle($dmDhFin)->format("%H:%i:%s");
                    $resultats["heure_de_nuit"] = (new DateModel($resultats["heure_de_nuit"]->format("Y-m-d H:i:s")))->addInterval($differenceIntervalleNuit);
                    echo"masoso";
                    $resultats["nombre_heure_de_nuit"] ++;
                }
                else{
                    $differenceIntervalleNuit = $dmDhDebut->differenceIntervalle($dmSup)->format("%H:%i:%s");
                    $differenceIntervalleExces =  $dmSup->differenceIntervalle($dmDhFin)->format("%H:%i:%s");
                    $resultats["heure_de_nuit"] = (new DateModel($resultats["heure_de_nuit"]->format("Y-m-d H:i:s")))->addInterval($differenceIntervalleNuit);
                    $resultats["exces"] = $resultats["exces"]->addInterval($differenceIntervalleExces);
                    $resultats["nombre_heure_de_nuit"]++;
                    $resultats["nombre_heure_exces"]++;
                }
            }
        }        
        return $resultats;
    }
    public function estWeekEnd($hs){
        if(empty($hs)){
            throw new \Exception("heure supplementaire non fournie pour la verification de week-end");
        }
        $dhDebut = new \DateTime($hs["date_heure_debut"]);
        $jour = DateModel::getJourChiffreDate($dhDebut);
        if($jour ==7){
            return true;
        }
        return false;
    }
    
}
