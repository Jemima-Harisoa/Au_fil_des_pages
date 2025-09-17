<?php
namespace app\models;

use PDO;
use Flight;


class Query{
    /***************requete generalisée*****************/
public static function query($query, ...$tab) {
    try {
        $statement = Flight::db()->prepare($query);

        // Si des paramètres sont passés, les lier aux placeholders
        if (!empty($tab)) { 
            if(is_array($tab[0])) {
                $tab = $tab[0]; // Si la variable donnée est un tableau
            }
            $i = 1;
            foreach ($tab as $index => $param) {
                $statement->bindValue($i++, $param); // Boucle pour lier les paramètres
            }
        }

        $statement->execute(); // Exécution de la requête

        // Gérer les requêtes INSERT
        if (stripos($query, 'insert') === 0) {
            if(!empty(Flight::db()->lastInsertId())) {
                return Flight::db()->lastInsertId();  // Retourne l'ID du dernier insert
            }  
            return false;
        }

        // Gérer les requêtes UPDATE et DELETE
        if (stripos($query, 'update') === 0 || stripos($query, 'delete') === 0) {
            if($statement->rowCount() != 0) return true;  // Retourne true si des lignes ont été affectées
            return false;            
        }

        // Gérer la création de vues (CREATE VIEW)
        if (stripos($query, 'create or') === 0) {
            if ($statement->rowCount() > 0) {
                return true;  // Retourne true si la vue a été créée avec succès
            }
            return false;  // Retourne false en cas d'échec
        }

        // Gérer les autres requêtes (SELECT)
        return $statement->fetchAll(PDO::FETCH_ASSOC); // Retourne les résultats de la requête SELECT

    } 
    catch (PDOException $sql) {
        error_log($sql->getMessage());  // Log des erreurs de la requête
        return false;  // Retourne false en cas d'erreur
    }
    finally {
        $statement = null;  // Libération des ressources
    }
}

}