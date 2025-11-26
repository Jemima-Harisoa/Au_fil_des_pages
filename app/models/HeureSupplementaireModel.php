<?php
namespace app\models;

use \PDOException;
use \Exception;

class HeureSupplementaireModel extends Query {

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
                (id_employe, nombre_heure_effectue, mois, annee, numero_semaine)
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
    public static function getById($id){
        $sql = "SELECT * FROM heure_supplementaire WHERE id = ?";
        $result = self::query($sql, [$id]);
        if (!$result || count($result) === 0) {
            throw new Exception("Aucune heure supplémentaire trouvée pour l'ID donné.");
        }
        return $result[0];
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
                $parametres['nombre_heure_effectue'],
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
                      AND EXTRACT(MONTH FROM date_enregistrement) = ? 
                      AND EXTRACT(YEAR FROM date_enregistrement) = ?";

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
            else if(empty($parametres['nombre_heure_effectue'])){
                throw new Exception("le nombre d'heure effectué est obligatoire");
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

}
