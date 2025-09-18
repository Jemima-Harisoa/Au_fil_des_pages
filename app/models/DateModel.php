<?php
namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
class DateModel{ 
 private DateTime $dateTime;

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
}  