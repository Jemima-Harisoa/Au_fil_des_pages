<?php

namespace app\models;

use Flight;
use PDO;
use PDOException;

class EmployeModel
{
    private $id_employe;
    private $id_personne;
    private $id_contrat;
    private $id_departement;
    private $poste;
    private $date_embauche;
    private $nombre_conge;  // int
    private $salaire_base;  // float



    private $db;

    public function __construct()
    {
        // Récupération de la connexion PDO depuis Flight
        $this->db = Flight::db();
    }

    // --- Getters ---
    public function getNombreConge(): ?int
    {
        return $this->nombre_conge;
    }
    public function setNombreConge(int $nb): void
    {
        $this->nombre_conge = $nb;
    }

    public function getSalaireBase(): ?float
    {
        return $this->salaire_base;
    }
    public function setSalaireBase(float $salaire): void
    {
        $this->salaire_base = $salaire;
    }
    public function getIdEmploye(): ?int
    {
        return $this->id_employe;
    }

    public function getIdPersonne(): ?int
    {
        return $this->id_personne;
    }

    public function getIdContrat(): ?int
    {
        return $this->id_contrat;
    }

    public function getIdDepartement(): ?int
    {
        return $this->id_departement;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function getDateEmbauche(): ?string
    {
        return $this->date_embauche;
    }

    // --- Setters ---
    public function setIdEmploye(int $id): void
    {
        $this->id_employe = $id;
    }

    public function setIdPersonne(int $id): void
    {
        $this->id_personne = $id;
    }

    public function setIdContrat(int $id): void
    {
        $this->id_contrat = $id;
    }

    public function setIdDepartement(int $id): void
    {
        $this->id_departement = $id;
    }

    public function setPoste(string $poste): void
    {
        $this->poste = $poste;
    }

    public function setDateEmbauche(string $date): void
    {
        $this->date_embauche = $date;
    }

// 
public 
public static function contratEnAlerte(int $id_employe): bool{
    $db = Flight::db();
    $sql = "
        SELECT EXISTS (
            SELECT 1 
            FROM contrats c
            JOIN candidats ca ON c.id_candidat = ca.id_candidat
            JOIN employes e   ON ca.id_personne = e.id_personne
            JOIN type_contrats tc ON c.id_type_contrat = tc.id_type_contrat
            WHERE e.id_employe = :id_employe
              AND c.date_fin IS NOT NULL
              AND tc.nom != 'CDI'
              AND c.date_fin <= CURRENT_DATE + INTERVAL '30 days'
        )
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute([':id_employe' => $id_employe]);
    return $stmt->fetchColumn(); // Directement true ou false
}
// 


    public static function getEmployerHistoriqueMouvement($idEmploye)
    {
        $db = Flight::db();
        $sql = "
    SELECT
        hm.date_evenement                                          AS \"dateEvenement\",
        COALESCE(ev.nom_evenement, 'Événement inconnu')            AS \"typeEvenement\",
        hm.support                                                 AS \"support\",
        
        COALESCE(
            p.titre,
            CASE 
                WHEN ev.nom_evenement = 'Embauche' THEN 'Embauche initiale'
                WHEN ev.nom_evenement = 'Promotion' THEN 'Nouveau poste'
                ELSE 'Poste non précisé'
            END
        )                                                          AS \"posteCible\",
        
        COALESCE(d.nom, 'Non précisé')                             AS \"departementCible\",
        
        CASE 
            WHEN ev.nom_evenement = 'Embauche'      
                THEN 'Embauche en tant que ' || COALESCE(p.titre, 'collaborateur')
            WHEN ev.nom_evenement = 'Promotion'     
                THEN 'Promotion → ' || COALESCE(p.titre, 'nouveau poste')
            WHEN ev.nom_evenement = 'Mutation'      
                THEN 'Mobilité → ' || COALESCE(d.nom, 'nouveau département') 
                     || CASE WHEN p.titre IS NOT NULL THEN ' ('||p.titre||')' ELSE '' END
            WHEN ev.nom_evenement = 'Démission'     THEN 'Démission'
            WHEN ev.nom_evenement = 'Licenciement'  THEN 'Licenciement'
            WHEN ev.nom_evenement = 'Fin de CDD'    THEN 'Fin de contrat CDD'
            ELSE COALESCE(ev.nom_evenement, 'Événement')
        END                                                        AS \"libelleComplet\"

    FROM historique_mobilite hm
    LEFT JOIN evenements ev        ON hm.id_evenement = ev.id_evenement
    LEFT JOIN profils p            ON hm.id_profil = p.id_profil
    LEFT JOIN departements d       ON hm.id_departement = d.id_departement
    JOIN employes e                ON hm.id_employe = e.id_employe
    WHERE hm.id_employe = :idEmploye
    ORDER BY hm.date_evenement DESC
";
        $stmt = $db->prepare($sql);
        $stmt->execute(['idEmploye' => $idEmploye]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listeEmployerV2()
    {
        $db = Flight::db();
        $sql = "
        SELECT
            e.id_employe,
            p.nom AS Nom,
            p.prenom AS Prenoms,
            d.nom AS Departement,
            e.poste AS Poste,
            tc.nom AS Type_Contrat,
            e.date_embauche AS Date_embauche
        FROM
            employes e
            LEFT JOIN personnes p ON e.id_personne = p.id_personne
            LEFT JOIN contrats c ON e.id_contrat = c.id_contrat
            LEFT JOIN type_contrats tc ON c.id_type_contrat = tc.id_type_contrat
            LEFT JOIN departements d ON e.id_departement = d.id_departement
        ORDER BY
            e.date_embauche DESC
        ";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function verifierEmploye($prenom, $mdp)
    {
        $sql = "
        SELECT e.id_employe, c.mdp
        FROM employes e
        JOIN connexEmployes c ON e.id_employe = c.idemploye
        JOIN personnes p ON e.id_personne = p.id_personne
        WHERE p.prenom = :prenom
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['prenom' => $prenom]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            // Comparaison du mot de passe (à remplacer par password_verify si hash)
            if ($result['mdp'] === $mdp) {
                return (int)$result['id_employe'];
            }
        }

        return false;
    }

    // --- Méthodes BDD ---
    public static function list(): array
    {
        $db = Flight::db();
        $sql = "SELECT * FROM employes";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Méthode pour récupérer les employés avec les informations liées
    public function listWithDetails(): array
    {
        $sql = "SELECT e.id_employe,
                   e.id_personne,
                   e.id_contrat,
                   e.id_departement,
                   e.poste,
                   e.date_embauche,
                   e.nombre_conge,
                   e.salaire_base,
                   d.nom AS nom_departement,
                   p.nom AS nom_personne,
                   p.prenom,
                   p.date_naissance,
                   p.contact,
                   p.lien_image
            FROM employes e
            JOIN departements d ON e.id_departement = d.id_departement
            JOIN personnes p ON e.id_personne = p.id_personne
            JOIN connexEmployes c ON e.id_employe = c.idemploye";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function save($data)
    {
        $sql = "INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche) 
                VALUES (:id_personne, :id_contrat, :id_departement, :poste, :date_embauche)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    // Récupérer l'id_employe via le poste (utile pour login ou pointage)
    public function getIdEmployeByPoste($poste): ?int
    {
        $sql = "SELECT id_employe FROM employes WHERE poste = :poste LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['poste' => $poste]);
        return $stmt->fetchColumn() ?: null;
    }
    public function getInfosEmploye($idEmploye)
    {
        $sql = "SELECT e.*, 
                   d.nom AS nom_departement,
                   p.nom AS nom_personne,
                   p.prenom,
                   p.date_naissance,
                   p.contact,
                   p.lien_image,
                   c.mdp AS mdp_login
            FROM employes e
            JOIN departements d ON e.id_departement = d.id_departement
            JOIN personnes p ON e.id_personne = p.id_personne
            JOIN connexEmployes c ON e.id_employe = c.idemploye
            WHERE e.id_employe = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $idEmploye]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function getEmployesWithDetails(int $idEmploye): array
    {
        $db = Flight::db();

        $sql = "
        SELECT 
            e.id_employe,
            e.id_personne,
            p.nom AS nom_personne,
            p.prenom,
            p.lien_image,
            p.contact,
            p.date_naissance,

            e.id_contrat,
            c.id_candidat,
            c.id_type_contrat,
            tc.nom AS nom_type_contrat,

            e.id_departement,
            d.nom AS nom_departement,

            e.date_embauche,
            e.poste
        FROM 
            employes e
            LEFT JOIN personnes p ON e.id_personne = p.id_personne
            LEFT JOIN contrats c ON e.id_contrat = c.id_contrat
            LEFT JOIN type_contrats tc ON c.id_type_contrat = tc.id_type_contrat
            LEFT JOIN departements d ON e.id_departement = d.id_departement
        WHERE 
            e.id_employe = :idEmploye
        LIMIT 1
    ";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([':idEmploye' => $idEmploye]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Retourne un tableau vide si rien trouvé
            return $result ?: [];
        } catch (PDOException $e) {
            error_log("Erreur dans getEmployesWithDetails($idEmploye) : " . $e->getMessage());
            return [];
        }
    }

    // Mettre à jour un employé
    public function updateById($id, $data)
    {
        $sql = "UPDATE employes SET 
                id_personne = :id_personne, 
                id_contrat = :id_contrat, 
                id_departement = :id_departement, 
                poste = :poste, 
                date_embauche = :date_embauche 
                WHERE id_employe = :id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Supprimer un employé
    public function deleteById($id)
    {
        $sql = "DELETE FROM employes WHERE id_employe = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Chercher par champ
    public function getBy($field, $value)
    {
        $sql = "SELECT * FROM employes WHERE {$field} = :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM employes WHERE id_employe = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Trouver un employé avec tous les détails
    public function findByIdWithDetails(int $id): ?array
    {
        $sql = "SELECT e.*, p.nom, p.prenom, p.email, p.telephone, d.nom as departement_nom 
                FROM employes e 
                LEFT JOIN personnes p ON e.id_personne = p.id_personne 
                LEFT JOIN departements d ON e.id_departement = d.id_departement 
                WHERE e.id_employe = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function insert(): bool
    {
        $sql = "INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche) 
                VALUES (:id_personne, :id_contrat, :id_departement, :poste, :date_embauche)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id_personne' => $this->id_personne,
            'id_contrat' => $this->id_contrat,
            'id_departement' => $this->id_departement,
            'poste' => $this->poste,
            'date_embauche' => $this->date_embauche
        ]);
    }

    public function update(): bool
    {
        if (!$this->id_employe) {
            throw new \Exception("Impossible de mettre à jour sans id_employe.");
        }
        $sql = "UPDATE employes SET 
                id_personne = :id_personne, 
                id_contrat = :id_contrat, 
                id_departement = :id_departement, 
                poste = :poste, 
                date_embauche = :date_embauche 
                WHERE id_employe = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id_personne' => $this->id_personne,
            'id_contrat' => $this->id_contrat,
            'id_departement' => $this->id_departement,
            'poste' => $this->poste,
            'date_embauche' => $this->date_embauche,
            'id' => $this->id_employe
        ]);
    }

    public function delete(): bool
    {
        if (!$this->id_employe) {
            throw new \Exception("Impossible de supprimer sans id_employe.");
        }
        $sql = "DELETE FROM employes WHERE id_employe = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $this->id_employe]);
    }

    // Rechercher une valeur précise dans la table
    public function search($field, $value)
    {
        $sql = "SELECT * FROM employes WHERE {$field} ILIKE :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Rechercher avec détails
    public function searchWithDetails($field, $value)
    {
        $sql = "SELECT e.*, p.nom, p.prenom, p.email, d.nom as departement_nom 
                FROM employes e 
                LEFT JOIN personnes p ON e.id_personne = p.id_personne 
                LEFT JOIN departements d ON e.id_departement = d.id_departement 
                WHERE e.{$field} ILIKE :value OR p.nom ILIKE :value OR p.prenom ILIKE :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => '%' . $value . '%']);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
