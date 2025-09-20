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
    public function getDepartement(ResponsableEntretienModel $responsable): ?array{
    $query = "";
    }
}
