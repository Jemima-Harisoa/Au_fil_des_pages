<?php
namespace app\models;

use \PDOException;
use \Exception;

class ParametreModel extends Query {

    /**
     * Récupère tous les paramètres
     * @return array
     * @throws Exception si aucun résultat n'est trouvé
     */
    public static function getAll() {
        try {
            $sql = "SELECT * FROM parametre";
            $result = self::query($sql);

            if (!$result || count($result) === 0) {
                throw new Exception("Aucun paramètre trouvé dans la base de données.");
            }

            return $result;

        } catch (PDOException $e) {
            throw new Exception("Erreur PDO : " . $e->getMessage());
        }
    }

    public static function getByName(string $name){
        try {
            $sql = "SELECT * FROM parametre WHERE libelle=?";
            $result = self::query($sql, [$name]);

            if (!$result || count($result) === 0) {
                throw new Exception("Aucun paramètre trouvé pour ce nom");
            }
            return $result;

        } catch (PDOException $e) {
            throw new Exception("Erreur PDO : " . $e->getMessage());
        }
    }
}
