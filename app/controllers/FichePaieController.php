<?php 
namespace app\controllers;
use app\models\EmployeModel;
use app\models\FichePaieModel;
use app\models\ParametreModel;

use Flight;

class FichePaieController{
    public function renderFichePaie(){
        $employe = Flight::Employe()->findByIdWithDetails(4);
        $listeHs = Flight::HeureSupplementaire()->getAllByIdEmployeAndSemaineDate(4,"2025-11-08 14:45:00");
        $detailsPrimes = FichePaieModel::getDetailsPrimeByIdEmployeAndDate($employe,new \DateTime("2025-11-30 09:00:00"));
        $totalPrime = FichePaieModel::getTotalPrime($employe,new \DateTime());
        $salaireBase =  doubleval($employe["salaire_base"]);
        $salaireBrute = FichePaieModel::calculerSalaireBrute($salaireBase,$totalPrime);
        $montantImposable = FichePaieModel::getMontantImposable($salaireBase,$totalPrime,FichePaieModel::sommeRetenus([FichePaieModel::getMontantRetenu("CNAPS",$salaireBrute),FichePaieModel::getMontantRetenu("OSTIE",$salaireBrute)]));
        $employe["salaire_base"] = FichePaieModel::formaterMilier($employe["salaire_base"]) ;
        $retenus = array();
        $retenus = [
            FichePaieModel::getSommeIrsa($montantImposable),
            FichePaieModel::getMontantRetenu("CNAPS",$salaireBase),
            FichePaieModel::getMontantRetenu("OSTIE",$salaireBase)
        ];

        $hs = [
            "id"=>83, 
            "id_employe"=>4,
            "date_heure_debut"=>"2025-11-08 20:00:00",
            "date_heure_fin"=>"2025-11-09 05:30:00",
            "mois"=> 11,
            "annee"=>2025,
            "numero_semaine"=> 45
        ];

        $HeureSup = Flight::HeureSupplementaire();
        $heureDeNuitAvecOuSansExces = $HeureSup ->calculerHeureDeNuitsAvecouSansExces($hs);
        $data = [
            "employe"=> $employe,
            "anciennete"=> FichePaieModel::getAnciennete(4),
            "taux_journalier"=>FichePaieModel::formaterMilier(FichePaieModel::getTaux("journalier",$salaireBase)),
            "taux_horaire"=> FichePaieModel::formaterMilier(FichePaieModel::getTaux("horaire",$salaireBase)),
            "details_irsa"=> FichePaieModel::getDetailsIRSA($montantImposable),
            "somme_retenus"=>FichePaieModel::formaterMilier(FichePaieModel::sommeRetenus($retenus)),
            "somme_irsa"=>FichePaieModel::formaterMilier(FichePaieModel::getSommeIrsa($montantImposable)),
            "cnaps"=>[ 
                "montant"=>FichePaieModel::formaterMilier(FichePaieModel::getMontantRetenu("CNAPS",$salaireBrute)),
                "taux"=> ParametreModel::getByName("CNAPS")[0]["pourcentage"] *100 . " %"
            ],
            "ostie"=>[
                "montant"=>FichePaieModel::formaterMilier(FichePaieModel::getMontantRetenu("OSTIE",$salaireBrute)),
                "taux"=> ParametreModel::getByName("OSTIE")[0]["pourcentage"] *100 . " %"
            ],
            // "somme_montant_heure_supplementaire"=> FichePaieModel::calculerHeureSupplementaire($employe["id_employe"],new \DateTime()), 
            "details_prime"=>$detailsPrimes,
            "total_prime"=>FichePaieModel::formaterMilier($totalPrime),
            "salaire_brute"=>FichePaieModel::formaterMilier($salaireBrute),
            "montant_imposable"=>FichePaieModel::formaterMilier($montantImposable),
            "net_a_payer"=> FichePaieModel::formaterMilier($salaireBrute - FichePaieModel::sommeRetenus($retenus)),
            "type_majoration"=>Flight::HeureSupplementaire()->getTypeMajoration($listeHs[0]),   
            "heure_de_nuit_avec_ou_sans_exces"=> $heureDeNuitAvecOuSansExces
        ];
        return Flight::render("paie/fiche_paie",["data"=>$data]);
    }
    public function renderListeFichePaie(){
        return Flight::render("paie/liste_fiche_paie");
    }
}
