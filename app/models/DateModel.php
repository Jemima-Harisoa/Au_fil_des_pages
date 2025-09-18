<?php
namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
class DateModel{ 
 private DateTime $dateTime;
 private const JourEnLettres = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimache'];
    // Constructeur : accepte soit un timestamp, soit une chaîne de date
    public function __construct(int|string|null $timeOrDateTime = null)
    {
        if ($timeOrDateTime === null) {
            $this->dateTime = new DateTime(); // date actuelle
        } elseif (is_int($timeOrDateTime)) {
            $this->dateTime = (new DateTime())->setTimestamp($timeOrDateTime);
        } elseif (is_string($timeOrDateTime)) {
            $this->dateTime = new DateTime($timeOrDateTime);
        } else {
            throw new InvalidArgumentException("Type non valide pour DateModel");
        }
    }

    // Retourner le timestamp
    public function getTimestamp(): int
    {
        return $this->dateTime->getTimestamp();
    }

    // Retourner la date formatée
    public function format(string $format = "Y-m-d H:i:s"): string
    {
        return $this->dateTime->format($format);
    }

    // Modifier la date via un timestamp
    public function setTimestamp(int $timestamp): void
    {
        $this->dateTime->setTimestamp($timestamp);
    }

    // Modifier la date via une chaîne
    public function setDate(string $dateString): void
    {
        $this->dateTime = new DateTime($dateString);
    }

    // Accéder directement à l'objet DateTime
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }
    public function addInterval(string $interval){
        try{
            if($interval!= null){
                $this->$date=$this->$date->add(new DateInterval($interval));
            }
            else{
                throw new Exception("l'intervalle n'existe pas ou est nul");
            }
        }
        catch(Exception $e){
            echo "".$e->getMessage(); 
        }
    }

    //     "d" → jour du mois avec 2 chiffres (ex: 01 à 31)

    // "j" → jour du mois sans zéro initial (ex: 1 à 31)

    // "N" → jour de la semaine ISO (1 = lundi, 7 = dimanche)

    // "w" → jour de la semaine (0 = dimanche, 6 = samedi)
    public static function getJourLettreDate(DateTime $datetime):string{
        $indiceJour = (int) $datetime->format("N");
        $jour = self::JourEnLettres[$indice];
        return $jour;
    }
    public static function getJourChiffreDate(DateTime $datetime):int{
        $indiceJour = (int) $datetime->format("N");
        return $indiceJour;
    }
    function ajouterJours(DateTime $date, int $nbJours): DateTime {
        $date->modify("+{$nbJours} days");
        return $date;
    }
}  