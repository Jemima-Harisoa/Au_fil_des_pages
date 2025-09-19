<?php
namespace app\models;

use PDO;
use Flight;
use PDOException;

class Query {
    /*************** Requête généralisée *****************/
    public static function query($query, ...$tab) {
        try {
            $statement = Flight::db()->prepare($query);

            // Lier les paramètres s'il y en a
            if (!empty($tab)) { 
                if (is_array($tab[0])) {
                    $tab = $tab[0]; // Si un tableau est passé
                }
                $i = 1;
                foreach ($tab as $param) {
                    $statement->bindValue($i++, $param);
                }
            }

            $statement->execute();

            // Gestion INSERT avec RETURNING id
            if (stripos($query, 'insert') === 0) {
                // PostgreSQL : on peut utiliser RETURNING id pour récupérer l'id
                $insertId = $statement->fetchColumn();
                if ($insertId !== false) {
                    return $insertId;
                }
                return false;
            }

            // Gestion UPDATE et DELETE
            if (stripos($query, 'update') === 0 || stripos($query, 'delete') === 0) {
                return $statement->rowCount() > 0;
            }

            // Gestion CREATE VIEW ou autres commandes DDL
            if (stripos($query, 'create') === 0 || stripos($query, 'drop') === 0) {
                return true; // Retourne true si la requête a été exécutée sans erreur
            }

            // Gestion SELECT
            return $statement->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        } finally {
            $statement = null;
        }
    }
}
