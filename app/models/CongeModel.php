<?php

namespace app\models\conge;

use Flight;

class CongeModel extends Query {
    /** @var \PDO */
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function absences($id_employe,$est_autorise){
        try{
            $autorise = "not est_autorise";
            if(empty($id_employe) || $id_employe == 0)
                throw new \Exception("l'id de l'employe ne peut etre null ou 0");
            if($est_autorise){
                $autorise = "est_autorise";
            }
            $sql = "SELECT * FROM abscence where id_employe=? ";
            $result = self::query($sql,[$id_employe, $est_autorise]);
        }
        catch(\Exception $e){
            throw new \Exception("erreur lors conge valide : ".$e->getMessage());
        }
    }
    /**
     * Récupère le décompte des congés pris par un employé groupés par type
     * @param int $iloye ID de l'employé
     * @return string HTML formaté de la section congés
     */
    public function getNombreConge($idEmploye) {
        // Requête pour récupérer les congés par type
        $sql = "
            SELECT 
                ct.nom as type_conge,
                ct.description,
                acs.annee,
                COALESCE(SUM(acs.nombre_conge), 0) as jours_pris,
                COALESCE(MAX(e.nombre_conge), 0) as jours_totaux
            FROM conge_type ct
            LEFT JOIN abscence_conge_suivi acs ON ct.id_type = acs.id_type AND acs.id_employe = 2
            LEFT JOIN employes e ON acs.id_employe = e.id_employe
            GROUP BY ct.id_type, ct.nom, ct.description,acs.annee
               ORDER BY ct.nom
        ";        
        $result = self::query($sql,[$idEmploye]);
        return $result;
    }
    /**
     * Récupère les données brutes des congés par type pour un employé
     * @param int $idEmploye ID de l'employé
     * @return array Données des congés
     */
    public function getDonneesConges($idEmploye) {
        $sql = "
            SELECT 
                ct.nom as type_conge,
                ct.description,
                COALESCE(SUM(acs.nombre_conge), 0) as jours_pris,
                COALESCE(MAX(e.nombre_conge), 0) as jours_totaux,
                COALESCE(MAX(e.nombre_conge), 0) - COALESCE(SUM(acs.nombre_conge), 0) as jours_restants
            FROM conge_type ct
            LEFT JOIN abscence_conge_suivi acs ON ct.id_type = acs.id_type AND acs.id_employe = :id_employe
            LEFT JOIN employes e ON acs.id_employe = e.id_employe
            GROUP BY ct.id_type, ct.nom, ct.description
            ORDER BY ct.nom
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}