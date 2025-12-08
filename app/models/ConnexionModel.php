<?php
namespace app\models;

use PDO;
use PDOException;
use DateTime;

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
    $stmt = $this->db->prepare("
        SELECT 
            a.id_admin AS id_admin,
            a.id_employe AS id_employe,
            a.nom AS nom,
            a.mdp AS mdp,
            a.date_affiliation AS date_affiliation,
            a.date_fin_affiliation AS date_fin_affiliation,
            ma.id_manager AS id_manager
        FROM admins a
        LEFT JOIN manager_admins ma
            ON ma.id_admin = a.id_admin
        WHERE a.nom = :nom
          AND a.mdp = :mdp
    ");

    $stmt->execute([
        'nom' => $nom,
        'mdp' => $motDePasse
    ]);

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin === false) {
        return null;
    }

    return $admin;
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

    if ($Admin === null) {
        return false;
    }

    // Date actuelle
    $now = new DateTime();

    // Si pas de date_fin_affiliation => toujours valide
    if ($Admin['date_fin_affiliation'] === null) {
        return true;
    }

    // Comparaison avec date_fin_affiliation
    $dateFin = new DateTime($Admin['date_fin_affiliation']);

    return $dateFin > $now;
}


    public function getDepartementAdmin($idAdmin)
    {
        $idDepartement = null;

        $stmt = $this->db->prepare(" SELECT departements.id_departement , departements.nom FROM admins JOIN employes ON admins.id_employe=employes.id_employe JOIN departements ON employes.id_departement = departements.id_departement  WHERE admins.id_admin= :id_admin ;");
        $stmt->execute(['id_admin' => $idAdmin]);
        $Departement = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($Departement === false) {
            return null;
        }
        return $Departement; 
    }

    
    public function getIdEmployeAdmin($idAdmin)
    {
        $idDepartement = null;

        $stmt = $this->db->prepare(" select id_employe from admins  WHERE admins.id_admin= :id_admin ;");
        $stmt->execute(['id_admin' => $idAdmin]);
        $idEmploye = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($idEmploye === false) {
            return null;
        }
        return $idEmploye; 
    }

    public function deconnexion()
{
    // On démarre la session si elle n'est pas déjà active
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // On vide toutes les variables de session
    $_SESSION = [];

    // On détruit la session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();

    return true;
}


}
?>
