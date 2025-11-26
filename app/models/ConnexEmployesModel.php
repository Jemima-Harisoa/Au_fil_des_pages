<?php
namespace app\models;
use PDO;

class ConnexEmployesModel {
    private $db;

    public function __construct($db) {
        $this->db = $db; // $db = instance PDO
    }

    /**
     * Vérifie si le login et le mot de passe sont corrects
     * @param string $nom - Nom ou identifiant de l'employé
     * @param string $mdp - Mot de passe
     * @return int|false - Retourne idEmploye si correct, sinon false
     */
    public function verifierUtilisateur($nom, $mdp) {
        // Requête pour récupérer l'employé correspondant
        $sql = "
            SELECT c.idEmploye, c.mdp
            FROM connexEmployes c
            JOIN employes e ON c.idEmploye = e.id_employe
            WHERE e.poste = :nom
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['nom' => $nom]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Pour la sécurité, tu peux remplacer === par password_verify() si mot de passe haché
            if ($result['mdp'] === $mdp) {
                return $result['idEmploye'];
            }
        }
        return false;
    }

    /**
     * Récupère le mot de passe haché (si tu veux hacher les mdp)
     */
    public function getMotDePasse($idEmploye) {
        $sql = "SELECT mdp FROM connexEmployes WHERE idEmploye = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $idEmploye]);
        return $stmt->fetchColumn();
    }

    /**
     * Modifier le mot de passe d'un employé
     */
    public function changerMotDePasse($idEmploye, $nouveauMdp) {
        $sql = "UPDATE connexEmployes SET mdp = :mdp WHERE idEmploye = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['mdp' => $nouveauMdp, 'id' => $idEmploye]);
    }
}
