<?php
namespace app\models;

use PDO;
use PDOException;

class ConnexionModel {
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // UTILISATEURS SIMPLES
    public function getUtilisateur($nom, $motDePasse)
    {
        $utilisateur = null;
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE nom = :nom AND mdp = :mdp");
        $stmt->execute(['nom' => $nom, 'mdp' => $motDePasse]);
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($utilisateur === false) {
            return null;
        }

        return $utilisateur; 
    }

    public function inscrireUtilisateur($nom, $motDePasse)
    {
        // $hash = password_hash($motDePasse, PASSWORD_BCRYPT);
        $hash = $motDePasse;
        $stmt = $this->db->prepare("INSERT INTO Utilisateurs (nom, mdp) VALUES (:nom, :mdp)");
        return $stmt->execute([
            'nom' => $nom,
            'mdp' => $hash
        ]);
    }

    public function verifierUtilisateur($nom, $motDePasse)
    {
        $utilisateur = $this->getUtilisateur($nom, $motDePasse);
        return $utilisateur != null;
    }

    // ADMINS
    public function getAdmin($nom, $motDePasse)
    {
        $utilisateur = null;
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE nom = :nom AND mdp = :mdp");
        $stmt->execute(['nom' => $nom, 'mdp' => $motDePasse]);
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($utilisateur === false) {
            return null;
        }

        return $utilisateur; 
    }

    public function inscrireAdmin($nom, $motDePasse)
    {
        // $hash = password_hash($motDePasse, PASSWORD_BCRYPT);
        $hash = $motDePasse;
        $stmt = $this->db->prepare("INSERT INTO Admins (nom, mdp) VALUES (:nom, :mdp)");
        return $stmt->execute([
            'nom' => $nom,
            'mdp' => $hash
        ]);
    }
    
    public function verifierAdmin($nom, $motDePasse)
    {
        $Admin = $this->getAdmin($nom, $motDePasse);
        return $Admin != null;
    }

    public function getDepartementAdmin($idAdmin)
    {
        $idDepartement = null;

        $stmt = $this->db->prepare(" SELECT id_departement FROM admins JOIN employes ON admins.id_employe=employes.id_employe WHERE admins.id_admin= :id_admin;");
        $stmt->execute(['id_admin' => $idAdmin]);
        $idDepartement = $stmt->fetch(PDO::FETCH_ASSOC);
        $idDepartement =  $idDepartement['id_departement'];
        if ($idDepartement === false) {
            return null;
        }
        return $idDepartement; 
    }

}
?>
