<?php
namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class ResponsableEntretienModel
{
    private PdoWrapper $db;

    public function __construct($db)
    {
        // Récupération de l'instance de connexion configurée dans Flight
        $this->db = $db;
    }

    /**
     * Récupérer tous les responsables d'entretien
     */
    public function all(): array
    {
        $sql = "SELECT * FROM responsable_entretien ORDER BY id_responsable";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Trouver un responsable par son ID
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM responsable_entretien WHERE id_responsable = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    public function findByIdAdmin(int $idAdmin): ?array
    {
        $sql = "SELECT * FROM responsable_entretien WHERE id_admin = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idAdmin]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    /**
     * Ajouter un nouveau responsable
     */
    public function create(int $id_profil, int $id_employe, int $ordre_passage): bool
    {
        $sql = "INSERT INTO responsable_entretien (id_profil, id_employe, ordre_passage) 
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_profil, $id_employe, $ordre_passage]);
    }

    /**
     * Mettre à jour un responsable
     */
    public function update(int $id_responsable, int $id_profil, int $id_employe, int $ordre_passage): bool
    {
        $sql = "UPDATE responsable_entretien 
                SET id_profil = ?, id_employe = ?, ordre_passage = ? 
                WHERE id_responsable = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_profil, $id_employe, $ordre_passage, $id_responsable]);
    }

    /**
     * Supprimer un responsable
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM responsable_entretien WHERE id_responsable = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getResponsablesEntretienCandidat(int $idProfil): ?array
    {
        $db = $this->db;
        $sql = "SELECT * FROM responsable_entretien where id_profil = ?";
        try {
            //code...
            if($idProfil==0 || $idProfil == null){
                throw new \Exception("l'id du Candidat est invalide");
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$idProfil]);

        } catch (\Exception $e) {
            throw $e->get;
        }

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getPropresResponsablesEntretiens($listeResponsable,$idAdmin){
      $results = [];
        foreach($listeResponsable as $responsable){
                if($responsable["id_admin"] === $idAdmin){
                    $results[]= $responsable;  
                    return $results;            
                }
        }
    }
    public function getDepartement(array $responsable): ?array{
        $query = "SELECT re.*,de.*
        FROM (SELECT * 
        FROM responsable_entretien  
        WHERE  id_responsable = ?
        )re
        JOIN (
            SELECT em.*,ad.id_admin
            from admins ad
            join employes em 
            on em.id_employe = ad.id_employe
        )em
        on em.id_admin = re.id_admin
        JOIN departements de 
        ON de.id_departement = em.id_departement";
        try{
            if($responsable == null){
                throw new \Exception("aucun responsable n'a ete trouve");
            }
        $db = $this->db;
        $stmt = $db->prepare($query);
        $stmt->execute([$responsable["id_responsable"]]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
    public function getResponsableEntretiensByIdAdmin($idAdmin){
        $db = $this->db;
        $query = "SELECT * FROM responsable_entretien  where id_admin = ?";
       
        try{
            if(empty($idAdmin)){
                throw new \Exception("aucune liste de profils d'entretiens trouves pour cet administrateur");   
            }
            $stmt = $db->prepare($query);
            $stmt->execute([$idAdmin]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
    public function getListeCandidatsAEntretenir($listeCandidats,$idAdmin):?array{
        $results = array();
        $profilsEntretiens = self::getResponsableEntretiensByIdAdmin($idAdmin);
        $i = 0;
        foreach($listeCandidats as $candidat){
            foreach($profilsEntretiens as $profil){
                if($candidat["id_profil"] === $profil["id_profil"]){
                    $results[$i] = $candidat;
                    $i++;
                }
            }
        }
        return $results;
    }
    

}
