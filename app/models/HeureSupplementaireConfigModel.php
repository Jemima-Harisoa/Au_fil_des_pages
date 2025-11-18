<?php
namespace app\models;

use \PDOException;
use \Exception;

class HeureSupplementaireConfigModel extends Query {

    /**
     * Récupère la configuration la plus récente
     * @return array
     * @throws Exception si aucune config n'est trouvée
     */
    public static function getConfRecent() {
        $sql = "SELECT * FROM heure_supplementaire_config ORDER BY date_creation DESC LIMIT 1";
        $result = self::query($sql);
        if (!$result || count($result) === 0) {
            throw new Exception("Aucune configuration d'heure supplémentaire trouvée.");
        }

        return $result[0];
    }
}
