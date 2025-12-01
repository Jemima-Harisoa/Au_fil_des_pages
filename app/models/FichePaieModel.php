<?php
namespace app\models;
use \PDOException;
use \Exception;
use Flight;
class FichePaieModel extends Query {
    private $db;
    public function __construct($db){
        $this->db = $db;
    }
    public static function getMontantRetenu(String $type,$salaireBase){
        $relevantRetenu = ParametreModel::getByName($type);
        
        return $relevantRetenu[0]["pourcentage"]*$salaireBase;
    }
    public static function getTaux(string $type, $montant){
        if($type=== "journalier"){
            $tauxHoraire = self::getTaux("horaire",$montant);
            return $tauxHoraire * 8;
        }
        else if($type ==="horaire"){
            return $montant / 173.33;
        }
        return 0;
    }
    public static function formaterMilier($montant){
        
        return  number_format($montant,2,".",' '). " Ar";
    }

    public static function convertirJourEnHeure($nombre_jour){
        return $nombre_jour * 8;
    }

    public static function deformater(string $montant) {
        // 1. Retirer "Ar"
        $montant = str_replace("Ar", "", $montant);

        // 2. Retirer les espaces
        $montant = str_replace(" ", "", $montant);

        // 3. Remplacer la virgule par un point décimal
        $montant = str_replace(",", ".", $montant);

        // 4. Retourner un nombre
        return doubleval($montant);
    }

    public static function getAnciennete($id_employe):string{
        try {
            if(empty($id_employe) || $id_employe == 0)
                throw new \Exception("l'id de l'employe ne peut etre null ou 0");
            $employe = Flight::Employe()->findById($id_employe);
            if(empty($employe)){
                throw new \Exception("Employe non trouvé");
            }
            $sql = "select * from v_anciennete_employe where id_employe= ?";
            $anciennete_employe = self::query($sql,[$employe["id_employe"]]);
            return "".$anciennete_employe[0]["anciennete_annees"]. " ans ".
            $anciennete_employe[0]["anciennete_mois"]." mois ".$anciennete_employe[0]["anciennete_jours"]." jours";
            } catch (\Exception $e) {
            throw new Exception("Erreur lors du calcul de l'ancienneté : " . $e->getMessage());
        }
    }
    
    public static  function getDetailsIRSA($montantImposable){
        $listeIrsa = IrsaModel::getAll();
        $results = array();
        $i = 0;
        $cpt = 0; 
        $results= array();
        foreach( $listeIrsa as $irsa)
        {
            $min =doubleval($irsa["min"]);
            $max =doubleval($irsa["max"]);
            $minformater = number_format($min,0,"",' ');
            $maxformater = number_format($max,0,"",' '). " Ar";
            $pourcentage =doubleval($irsa["pourcentage"]);
            $cpt++;
            switch($max){
                case $max != null || $max == 0: 
                    if($max< $montantImposable){
                        $diff = $max - ($min-1);
                        $results[$i] = [
                            "designation"=>"tranche de ".$minformater." à ".$maxformater,
                            "pourcentage"=>($pourcentage *100). " %",
                            "montant"=>self::formaterMilier($diff* $pourcentage)
                            ];
                        $i++;
                    }
                    else{
                        if( $min<= $montantImposable && $montantImposable <= $max){
                            $diff = $montantImposable - ($min-1);
                            $results[$i] = [
                            "designation"=>"tranche de ".$minformater." à ".$maxformater,
                            "pourcentage"=>($pourcentage*100). " %",
                            "montant"=>self::formaterMilier($diff* $pourcentage)
                            ];
                            break;
                        }
                    }
                    break;
                default:
                    if($cpt== count($listeIrsa)- 1 ){
                        if($min<$montantImposable){     
                            $diff = $montantImposable - $min;
                            $results[$i] = [
                                "designation"=>"tranche de ".$min."Ar et plus ",
                                "pourcentage"=>($pourcentage*100). " %",
                                "montant"=>self::formaterMilier($diff* $pourcentage)
                                ];
                        }
                    }
                    break;
            }   
        }
        return $results;
    }


    public static function getSommeIrsa($montantImposable){
        $listeIrsa = self::getDetailsIRSA($montantImposable);
        $somme = 0.0;
        foreach($listeIrsa as $irsa){
            $somme += self::deformater($irsa["montant"]);
        }
        return $somme;
    }


    public static function sommeRetenus($retenus){
        $result = 0.0;
        foreach($retenus as $retenu){
            $result += self::deformater($retenu);
        }
        return $result;
    }
     public static function calculerHeureSupplementaire($id_employe,$date){
        $heures_supplementaires = Flight::HeureSupplementaire()->getByIdEmployeAndDate($id_employe,$date);
        $hsConfRecent =  HeureSupplementaireConfigModel::getConfRecent();
        $somme = 0.0;
        $employe = Flight::Employe()->findById($id_employe);
        $nombre_premieres_heures = $hsConfRecent["nombre_premieres_heures"];
        $salaireBase = $employe["salaire_base"];
        $taux_horaire = self::getTaux("horaire",$salaireBase);
        foreach($heures_supplementaires as $hs){
            $nombre_heure_effectue = doubleval($hs["nombre_heure_effectue"]);
            if($nombre_heure_effectue <= $nombre_premieres_heures){
                $somme += $taux_horaire  * $nombre_heure_effectue * 1.3 ;
            }
            else{
                $heures_restantes = $nombre_heure_effectue - $nombre_premieres_heures;
                $somme += $taux_horaire*$nombre_premieres_heures*1.3 + $taux_horaire * $heures_restantes*1.5;
            }
        }
    }


    public static function getTotalPrime($employe,$date){
        
        $primes = Flight::Prime()->getByIdEmployeAndDate($employe["id_employe"],$date);
        $somme = 0.0;
        
        foreach($primes as $prime){
            $somme += doubleval($prime["pourcentage"])* doubleval($employe["salaire_base"]);
        }
        return $somme;
    }

    
    public static function getDetailsPrimeByIdEmployeAndDate($employe,$date){
        $detailsPrimes = PrimeModel::getDetailsByIdEmployeAndDate(intval($employe["id_employe"]),$date);
        $results = array();
        $i = 0;
        $nom_prime = "";
        foreach($detailsPrimes as $detail){
            $nom_prime = $detail["type_prime_libelle"];
            $results[$i] = array();
            $results[$i]=[
                "designation" => $nom_prime,
                "taux"=> $detail["pourcentage"],
                "montant"=>self::formaterMilier($employe["salaire_base"] *  $detail["pourcentage"])
            ];
            $i++; 
        }
        return $results;
    }
    public static function calculerSalaireBrute($salaireBase,$totalGains){
        return $salaireBase + $totalGains;
    }
    public static function getMontantImposable($salaireBase,$totalGains, $totalRet){
        $salaireBrute = self::calculerSalaireBrute($salaireBase,$totalGains);
        return $salaireBrute- $totalRet;
        
    }
}
?>
