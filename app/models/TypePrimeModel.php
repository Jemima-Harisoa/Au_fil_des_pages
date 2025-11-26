<?php
namespace app\models;

use PDOException;

class TypePrimeModel extends Query
{
    /**
     * Récupère toutes les primes
     * @return array|false Liste des types de prime ou false en cas d'erreur
     */
    public static function getAll()
    {
        try {
            $sql = "SELECT * FROM type_prime ORDER BY id ASC";
            $result = parent::query($sql);

            if ($result === false) {
                throw new PDOException("Erreur lors de l'exécution de la requête SELECT type_prime.");
            }

            return $result;

        } catch (PDOException $e) {

            throw new \Exception("TypePrimeModel::getAll() - " . $e->getMessage());
            // Retourne false en cas d'échec
            return false;
        }
    }

     /**
     * Récupère un type de prime par son id
     *
     * @param int $id_type_prime
     * @return array|null|false
     *         - array : ligne trouvée (associative)
     *         - null  : aucune ligne trouvée pour cet id
     *         - false : erreur (validation ou SQL)
     */
    public static function getById($id_type_prime)
    {
        try {
            // Validation du paramètre
            if ($id_type_prime === null || $id_type_prime === '' || !is_numeric($id_type_prime) || intval($id_type_prime) <= 0) {
                throw new InvalidArgumentException("L'identifiant \$id_type_prime est invalide (null, 0, ou non positif).");
            }

            $id = intval($id_type_prime);

            $sql = "SELECT * FROM type_prime WHERE id = ? LIMIT 1";
            $result = parent::query($sql, $id);

            if ($result === false) {
                throw new \Exception("Erreur lors de l'exécution de la requête SELECT type_prime WHERE id = {$id}.");
            }

            // Si aucune ligne trouvée, retourner null
            if (empty($result)) {
                return null;
            }

            // Renvoie la première (et unique) ligne
            return $result[0];

        }  catch (\Exception $e) {
            // Erreur SQL
            throw new Exception("TypePrimeModel::getById() - PDOException - " . $e->getMessage());
        }
    }
}
