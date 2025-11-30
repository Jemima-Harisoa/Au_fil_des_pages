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

    public static function getEmployerHistoriqueMouvement($idEmploye){
        $db = Flight::db();
        $sql = "
            SELECT 
                hm.date_evenement                                      AS Date,
                ev.nom_evenement                                       AS Event,
                CONCAT('Mobilité vers ', COALESCE(p.titre, ''), ' au département ', COALESCE(d.nom, '')) AS Details,
                hm.support                                             AS Support,
                COALESCE(p.titre, 'Non renseigné')                     AS Post
            FROM employes e
                JOIN personnes per      ON e.id_personne = per.id_personne
                JOIN candidats c        ON c.id_personne = per.id_personne
                JOIN historique_mobilite hm ON hm.id_candidat = c.id_candidat
                LEFT JOIN evenements ev ON hm.id_evenement = ev.id_evenement
                LEFT JOIN profils p     ON hm.id_profil = p.id_profil
                LEFT JOIN departements d ON hm.id_departement = d.id_departement
            WHERE e.id_employe = :idEmploye
            ORDER BY hm.date_evenement DESC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['idEmploye' => $idEmploye]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function listeEmployerV2(){
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

    public function verifierManager($prenom, $mdp)
{
    $sql = "
        SELECT m.id_manager, m.employe_id, m.mot_de_passe, p.nom, p.prenom
        FROM managers m
        JOIN employes e ON e.id_employe = m.employe_id
        JOIN personnes p ON p.id_personne = e.id_personne
        WHERE p.prenom = :prenom
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['prenom' => $prenom]);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    if ($result) {
        // Comparaison du mot de passe simple
        if ($result['mot_de_passe'] === $mdp) {
            return [
                'id_manager' => (int)$result['id_manager'],
                'employe_id' => (int)$result['employe_id'],
                'nom' => $result['nom'],
                'prenom' => $result['prenom']
            ];
        }
    }

    return false;
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
    public function list(): array
    {
        $sql = "SELECT * FROM employes";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Méthode pour récupérer les employés avec les informations liées
   public function listWithDetails(): array {
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
    public function findByIdWithDetails(int $id): ?array {
        $sql = "SELECT e.*, p.nom, p.prenom, p.contact, d.nom as departement_nom 
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
        /**
         * Retourne tous les employés sauf celui dont l'id est fourni.
         *
         * @param int $id_employe
         * @return array
         */
        public function getEmployeAutre(int $id_employe): array {
            $sql = "
                SELECT 
                    e.id_employe,
                    e.id_personne,
                    e.id_contrat,
                    e.id_departement,
                    e.poste,
                    e.date_embauche,
                    e.nombre_conge,
                    e.salaire_base,
                    p.nom AS nom_personne,
                    p.prenom,
                    p.date_naissance,
                    p.contact,
                    p.lien_image,
                    d.nom AS nom_departement
                FROM employes e
                LEFT JOIN personnes p ON e.id_personne = p.id_personne
                LEFT JOIN departements d ON e.id_departement = d.id_departement
                WHERE e.id_employe <> :id
                ORDER BY p.nom ASC, p.prenom ASC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id_employe]);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $rows ?: [];
        }


    // Reçoit la fiche complète d'un employé et renvoie le HTML de la carte
    public function getFicheEmploye($idEmploye) {
        // Récupération des modèles
        $employeModel = Flight::Employe();
        $personneModel = Flight::Personne();
        $departementModel = Flight::Departement();

        // Récupération des données de l'employé avec les détails
        $employe = $employeModel->findByIdWithDetails($idEmploye);
        
        if (!$employe) {
            return "<div class='alert alert-danger'>Employé non trouvé</div>";
        }

        // Récupération des informations personnelles supplémentaires
        $personne = $personneModel->getBy('id_personne', $employe['id_personne']);
        
        if (!$personne) {
            return "<div class='alert alert-danger'>Informations personnelles non trouvées</div>";
        }

        // Formatage des dates
        $dateEmbauche = date('d/m/Y', strtotime($employe['date_embauche']));
        $dateNaissance = isset($personne['date_naissance']) ? date('d/m/Y', strtotime($personne['date_naissance'])) : 'Non renseignée';
        
        // Extraction email et téléphone du contact
        $contact = $personne['contact'] ?? '';
        $email = filter_var($contact, FILTER_VALIDATE_EMAIL) ? $contact : 'Non renseigné';
        $telephone = preg_match('/[\+]?[0-9]{10,15}/', $contact, $matches) ? $matches[0] : 'Non renseigné';
        
        // Photo de profil
        $photo = $personne['lien_image'] ?? '/images/jean.jpg';
        
        // ID employé formaté
        $idFormate = "EMP-" . date('Y', strtotime($employe['date_embauche'])) . "-" . str_pad($employe['id_employe'], 3, '0', STR_PAD_LEFT);
        
        // Badge département (3 premières lettres)
        $badgeDepartement = substr(strtoupper($employe['departement_nom'] ?? 'DEP'), 0, 3);

        // Construction du HTML
        $html = <<<HTML
                            <!-- Carte d'identité Employé -->
                            <div class="col-xl-8 col-lg-7 mb-4">
                                <div class="card id-card">
                                    <div class="id-card-header">
                                        <div class="id-card-photo">
                                            <img src="{$photo}" alt="Photo employé">
                                        </div>
                                        <h4 class="text-white">{$employe['prenom']} {$employe['nom']}</h4>
                                        <p class="mb-0">{$employe['poste']}</p>
                                    </div>
                                    <div class="id-card-body">
                                        <div class="employee-info">
                                            <div class="info-item">
                                                <div class="info-icon">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                                <div class="info-content">
                                                    <div class="info-label">Département</div>
                                                    <div class="info-value">{$employe['departement_nom']}</div>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-icon">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </div>
                                                <div class="info-content">
                                                    <div class="info-label">Date d'embauche</div>
                                                    <div class="info-value">{$dateEmbauche}</div>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-icon">
                                                    <i class="fas fa-envelope"></i>
                                                </div>
                                                <div class="info-content">
                                                    <div class="info-label">Email</div>
                                                    <div class="info-value">{$email}</div>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-icon">
                                                    <i class="fas fa-phone"></i>
                                                </div>
                                                <div class="info-content">
                                                    <div class="info-label">Téléphone</div>
                                                    <div class="info-value">{$telephone}</div>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-icon">
                                                    <i class="fas fa-birthday-cake"></i>
                                                </div>
                                                <div class="info-content">
                                                    <div class="info-label">Date de naissance</div>
                                                    <div class="info-value">{$dateNaissance}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="id-card-footer">
                                        <div class="employee-id">
                                            ID: {$idFormate}
                                        </div>
                                        <div class="badge-department">
                                            {$badgeDepartement}
                                        </div>
                                    </div>
                                </div>
                            </div>
    HTML;

        return $html;
    }
}
