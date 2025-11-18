<?php
namespace app\models;

use \PDOException;
use \Exception;
class SmigModel extends Query {
    public function __construct($db)
    {
        $this->db = $db;
    }
    // Méthode pour récupérer le SMIG le plus récent
    public static function getLast() {
        try {
            $sql = "SELECT * FROM smig ORDER BY date_application DESC LIMIT 1";
            $result = self::query($sql);
            if ($row) {
                return $result[0];
            } else {
                throw new Exception("Aucun SMIG trouvé");
            }
        } catch (PDOException $e) {
            // Gestion d'erreur
            throw new Exception("Erreur lors de la récupération du SMIG : " . $e->getMessage());
        }
    }
}