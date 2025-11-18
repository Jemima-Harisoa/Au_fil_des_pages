<?php
namespace app\models;

use app\models\Query;
use Exception;
use PDOException;

class IrsaModel extends Query
{
    private $db;
        public function __construct($db)
    {
        $this->db = $db;
    }
    /**
     * Récupère toutes les tranches IRSA.
     *
     * @return array
     * @throws Exception si aucun résultat
     */
    public static function getAll()
    {
        try {
            $sql = "SELECT * FROM irsa ORDER BY min ASC";
            $result = self::query($sql);

            if ($result === false) {
                throw new Exception("Erreur lors de l'exécution de la requête.");
            }

            if (empty($result)) {
                throw new Exception("Aucune tranche IRSA trouvée dans la base.");
            }

            return $result;

        } catch (PDOException $e) {
            throw new Exception("Erreur base de données dans getAll IRSA : " . $e->getMessage());
        }
    }
}
