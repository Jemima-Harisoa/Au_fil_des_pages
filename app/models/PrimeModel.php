<?php
namespace app\models;
class PrimeModel extends Query {
    private $db;
        public function __construct($db)
    {
        $this->db = $db;
    }
    public static function getAll() {
        try {
            $sql = "SELECT * FROM prime";
            $result = self::query($sql);
            return $result;

        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la récupération des primes : " . $e->getMessage());
        }
    }
    public static function getByIdEmployeAndNom($id_employe, $nom_prime) {
        try {
            if (empty($id_employe) || $id_employe == 0) {
                throw new \Exception("L'id de l'employé ne peut être null ou 0");
            }
            else if(empty($nom_prime)) {
                throw new \Exception("Le nom de la prime ne peut être null ou vide");
            }
            $sql = "SELECT * FROM prime WHERE id_employe = ? AND nom_prime = ?";
            $result = self::query($sql, [$id_employe, $nom_prime]);
            return $result;

        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la récupération des primes : " . $e->getMessage());
        }
    }
    public  static function getByIdEmployeAndDate($id_employe, \DateTime $date) {
        try {
            if (empty($id_employe) || $id_employe == 0) {
                throw new \Exception("L'id de l'employé ne peut être null ou 0");
            }
            $sql = "SELECT * FROM prime WHERE id_employe = ? AND extract(month from date_creation)=? and extract(year from date_creation)=?";
            $fDate = strtotime($date->format("Y-m-d"));
            $result = self::query($sql, [$id_employe, intval(date('m',$fDate)),intval(date('Y',$fDate))]);
            return $result;

        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la récupération des primes : " . $e->getMessage());
        }
    }

        /**
     * Récupère les primes d'un employé avec le libellé du type de prime
     *
     * @param int $id_employe
     * @return array|false
     *         - array : liste des primes (peut être vide)
     *         - false : jamais retourné ici car les exceptions sont propagées
     * @throws Exception
     */
    public static function getDetailsByIdEmployeAndDate($id_employe, \DateTime $date)
    {
        try {
            // Validation du paramètre
            if (
                $id_employe === null ||
                $id_employe === '' ||
                !is_numeric($id_employe) ||
                intval($id_employe) <= 0
            ) {
                throw new \Exception(
                    "L'identifiant \$id_employe est invalide (null, 0, ou non positif)."
                );
            }
            else if($date == null){ 
                throw new \Exception(
                    "La date pour le prime est indefini"
                );
            }

            $id = intval($id_employe);

            $sql = "
                SELECT 
                    p.id,
                    p.id_type_prime,
                    p.pourcentage,
                    p.id_employe,
                    p.date_creation,
                    tp.libelle AS type_prime_libelle
                FROM prime p
                INNER JOIN type_prime tp ON p.id_type_prime = tp.id
                WHERE p.id_employe = ?
                AND EXTRACT (month from p.date_creation) = ?
                AND EXTRACT (year from p.date_creation) = ?
            ";

            $result = parent::query($sql, [$id, date('m', strtotime($date->format('Y-m-d'))),date('Y', strtotime($date->format('Y-m-d')))]);
            
            if ($result === false) {
                throw new \Exception(
                    "Erreur lors de l'exécution de la requête SELECT prime JOIN type_prime pour id_employe = {$id}."
                );
            }

            return $result;

        }catch (\Exception $e) {

            throw new \Exception("PrimeModel::getByIdEmploye() - Erreur SQL : " . $e->getMessage());
        }
    }
}