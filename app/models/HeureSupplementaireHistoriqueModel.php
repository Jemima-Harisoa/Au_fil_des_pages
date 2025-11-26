<?php
namespace app\models;

use \PDOException;
use \Exception;

class HeuresSupplementaireHistoriqueModel extends Query {

    /**
     * Enregistre un historique d'heure supplémentaire
     * @param array $parametres
     * @return int id inséré
     * @throws Exception
     */
    public static function save($parametres) {
        try {
            $sql = "INSERT INTO heures_supplementaire_historique
                (id_heure_supp, nombre_heure_effectue, mois, annee, numero_semaine)
                VALUES (?, ?, ?, ?, ?)
                RETURNING id";

            $id = self::query($sql, [
                $parametres['id_heure_supp'],
                $parametres['nombre_heure_effectue'],
                $parametres['mois'],
                $parametres['annee'],
                $parametres['numero_semaine']
            ]);

            if (!$id) {
                throw new Exception("Impossible d'enregistrer l'historique des heures supplémentaires.");
            }

            return $id;

        } catch (PDOException $e) {
            throw new Exception("Erreur PDO : " . $e->getMessage());
        }
    }
}
