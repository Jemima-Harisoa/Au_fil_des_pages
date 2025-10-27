<?php
namespace app\models\migration;

use PDO;

class ValidationContratModel {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }
    /**
     * Récupère les validateurs pour une étape donnée
     */
    public function getValidateursPourEtape($statut_actuel) {
        $etapes_validateurs = [
            'en_attente_etape1' => ['rh'], // RH valide l'étape 1
            'en_attente_etape2' => ['service'], // Service valide l'étape 2  
            'en_attente_etape3' => ['candidat'] // Candidat valide l'étape 3
        ];
        
        return $etapes_validateurs[$statut_actuel] ?? [];
    }

    /**
     * Récupère les ID des employés par rôle
     */
    public function getEmployesParRole($role, $id_contrat = null) {
        $sql = "";
        
        switch($role) {
            case 'rh':
                // Employés du département RH (id_departement = 4)
                $sql = "SELECT e.id_employe 
                        FROM employes e 
                        WHERE e.id_departement = 4";
                break;
            case 'service':
                // Récupérer le département concerné par le contrat
                if ($id_contrat) {
                    $id_departement = $this->getDepartementContrat($id_contrat);
                    if ($id_departement) {
                        $sql = "SELECT id_employe 
                                FROM employes 
                                WHERE id_departement = :id_departement 
                                AND id_departement != 4"; // Exclure RH pour éviter les doublons
                    } else {
                        $sql = "SELECT id_employe FROM employes WHERE 1=0"; // Aucun résultat
                    }
                } else {
                    $sql = "SELECT id_employe FROM employes WHERE id_departement != 4"; // Tous sauf RH
                }
                break;
            case 'candidat':
                // Pour le candidat, on retourne son ID de candidat (sera traité différemment)
                $sql = "SELECT id_candidat as id_employe FROM candidats WHERE 1=0"; // Vide car géré autrement
                break;
        }
        
        $stmt = $this->db->prepare($sql);
        if ($role === 'service' && $id_contrat && isset($id_departement)) {
            $stmt->execute(['id_departement' => $id_departement]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le département concerné par un contrat
     */
    public function getDepartementContrat($id_contrat) {
        $sql = "SELECT p.id_departement 
                FROM contrats c
                JOIN candidats ca ON c.id_candidat = ca.id_candidat
                JOIN profils p ON ca.id_profil = p.id_profil
                WHERE c.id_contrat = :id_contrat";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_contrat' => $id_contrat]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id_departement'] : null;
    }


    /**
     * Récupère la liste des statuts possibles
     */
    public function getStatutsValidation() {
        $sql = "SELECT * FROM etat 
                WHERE nom IN ('brouillon', 'en_attente_etape1', 'en_attente_etape2', 'en_attente_etape3', 'validé', 'rejeté')";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le dernier statut d’un contrat depuis l’historique
     */
    public function getStatutActuel($id_contrat) {
        $sql = "SELECT e.*, hv.date_heure_validation 
                FROM historique_validation hv 
                JOIN etat e ON hv.id_etat = e.id_etat 
                WHERE hv.id_candidat IN (
                    SELECT id_candidat FROM contrats WHERE id_contrat = :id_contrat
                )
                ORDER BY hv.date_heure_validation DESC 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_contrat' => $id_contrat]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Envoie un contrat à l’étape suivante de validation
     */
    public function envoyerPourValidation($id_contrat, $id_validateur, $note = '') {
        // Récupération du candidat lié au contrat
        $sql = "SELECT c.id_candidat FROM contrats c WHERE c.id_contrat = :id_contrat";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_contrat' => $id_contrat]);
        $contrat = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$contrat) {
            throw new \Exception("Contrat introuvable.");
        }

        $id_candidat = $contrat['id_candidat'];
        $statut_actuel = $this->getStatutActuel($id_contrat);

        // Déterminer le prochain statut
        $prochain_statut = $this->getProchainStatut($statut_actuel['nom'] ?? 'brouillon');

        // Enregistrement dans l’historique
        $sql = "INSERT INTO historique_validation (id_employe, id_candidat, date_heure_validation, id_etat, note) 
                VALUES (
                    :id_employe, 
                    :id_candidat, 
                    NOW(), 
                    (SELECT id_etat FROM etat WHERE nom = :nom_statut),
                    :note
                )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_employe' => $id_validateur,
            'id_candidat' => $id_candidat,
            'nom_statut' => $prochain_statut,
            'note' => $note
        ]);

        return $prochain_statut;
    }

    /**
     * Détermine le prochain statut selon le statut actuel
     */
    public function getProchainStatut($statut_actuel) {
        $statuts = [
            'brouillon' => 'en_attente_etape1',
            'en_attente_etape1' => 'en_attente_etape2',
            'en_attente_etape2' => 'en_attente_etape3',
            'en_attente_etape3' => 'validé'
        ];
        
        return $statuts[$statut_actuel] ?? 'brouillon';
    }

    /**
     * Récupère tout l’historique de validation d’un contrat
     */
    public function getHistoriqueValidation($id_contrat) {
        $sql = "SELECT hv.*, e.nom AS statut_nom, p.nom, p.prenom
                FROM historique_validation hv 
                JOIN etat e ON hv.id_etat = e.id_etat 
                LEFT JOIN employes emp ON hv.id_employe = emp.id_employe
                LEFT JOIN personnes p ON emp.id_personne = p.id_personne
                WHERE hv.id_candidat IN (
                    SELECT id_candidat FROM contrats WHERE id_contrat = :id_contrat
                )
                ORDER BY hv.date_heure_validation";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_contrat' => $id_contrat]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si un utilisateur (RH, service ou candidat) peut valider le contrat à cette étape
     */
    public function verifierPermission($id_contrat, $role_validation) {
        $statut_actuel = $this->getStatutActuel($id_contrat);
        $statut_nom = $statut_actuel['nom'] ?? 'brouillon';
        
        error_log("🔐 Vérification permission - Contrat: $id_contrat, Statut: $statut_nom, Rôle: $role_validation");
        
        $permissions = [
            'rh' => ['brouillon', 'en_attente_etape1'], // RH peut valider étape 1
            'service' => ['en_attente_etape2'], // Service valide l'étape 2
            'candidat' => ['en_attente_etape3'] // Candidat valide la dernière étape
        ];
        
        $result = in_array($statut_nom, $permissions[$role_validation] ?? []);
        error_log("🔐 Permission " . ($result ? "ACCORDÉE" : "REFUSÉE"));
        
        return $result;
    }
    /**
     * Retourne le rôle de l'utilisateur courant basé sur les sessions et la base de données.
     * 
     * Cas possibles :
     *  - 'visiteur' : non connecté
     *  - 'candidat' : utilisateur simple (postulant)
     *  - 'employe' : employé avec département associé (renvoie aussi le nom du département)
     *  - 'responsable_rh' : responsable du département RH
     *  - 'responsable_departement' : responsable d’un autre département (hors RH)
     *
     * Retourne le rôle de l'utilisateur courant basé sur les sessions et la base de données.
     * Adapté à la structure réelle de la base de données
     */
    public static function getRoleUtilisateur()
    {
        // On suppose que session_start() est déjà appelé avant
        $db = Flight::db();

        // ---- 1️⃣ Candidat (utilisateur simple) ----
        if (isset($_SESSION['utilisateur']) && !isset($_SESSION['admin']) && !isset($_SESSION['employe'])) {
            return [
                'role' => 'candidat',
                'departement' => null
            ];
        }

        // ---- 2️⃣ Employé connecté (via session employe) ----
        if (isset($_SESSION['employe'])) {
            $idEmploye = $_SESSION['employe']['id_employe'] ?? null;
            
            if ($idEmploye) {
                try {
                    // Récupérer le département de l'employé
                    $sql = "SELECT d.id_departement, d.nom as departement_nom 
                            FROM employes e 
                            JOIN departements d ON e.id_departement = d.id_departement 
                            WHERE e.id_employe = :id_employe";
                    $stmt = $db->prepare($sql);
                    $stmt->execute(['id_employe' => $idEmploye]);
                    $departement = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($departement) {
                        // Vérifier si l'employé est responsable d'entretien
                        $stmt2 = $db->prepare("SELECT 1 FROM responsable_entretien WHERE id_employe = :id LIMIT 1");
                        $stmt2->execute(['id' => $idEmploye]);
                        $isResponsableEntretien = (bool)$stmt2->fetchColumn();

                        // Vérifier si l'employé est admin (dans la table admins)
                        $stmt3 = $db->prepare("SELECT 1 FROM admins WHERE id_employe = :id LIMIT 1");
                        $stmt3->execute(['id' => $idEmploye]);
                        $isAdmin = (bool)$stmt3->fetchColumn();

                        // Déterminer le rôle en fonction du département et des privilèges
                        if ($departement['id_departement'] == 4) { // RH
                            if ($isAdmin || $isResponsableEntretien) {
                                return [
                                    'role' => 'responsable_rh',
                                    'departement' => $departement['departement_nom']
                                ];
                            }
                            return [
                                'role' => 'rh',
                                'departement' => $departement['departement_nom']
                            ];
                        } else {
                            // Autres départements
                            if ($isAdmin) {
                                return [
                                    'role' => 'responsable_departement',
                                    'departement' => $departement['departement_nom']
                                ];
                            }
                            return [
                                'role' => 'employe',
                                'departement' => $departement['departement_nom']
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    // En cas d'erreur DB
                    error_log("Erreur getRoleUtilisateur employe: " . $e->getMessage());
                }
            }

            return [
                'role' => 'employe',
                'departement' => null
            ];
        }

        // ---- 3️⃣ Admin connecté (via session admin) ----
        if (isset($_SESSION['admin'])) {
            $idAdmin = $_SESSION['admin']['id_admin'] ?? null;
            
            if ($idAdmin) {
                try {
                    // Récupérer le département de l'admin via l'employé associé
                    $sql = "SELECT d.id_departement, d.nom as departement_nom 
                            FROM admins a 
                            JOIN employes e ON a.id_employe = e.id_employe 
                            JOIN departements d ON e.id_departement = d.id_departement 
                            WHERE a.id_admin = :id_admin";
                    $stmt = $db->prepare($sql);
                    $stmt->execute(['id_admin' => $idAdmin]);
                    $departement = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($departement) {
                        // Vérifier si l'admin est aussi responsable d'entretien
                        $idEmploye = $_SESSION['admin']['id_employe'] ?? null;
                        if ($idEmploye) {
                            $stmt2 = $db->prepare("SELECT 1 FROM responsable_entretien WHERE id_employe = :id LIMIT 1");
                            $stmt2->execute(['id' => $idEmploye]);
                            $isResponsableEntretien = (bool)$stmt2->fetchColumn();

                            if ($isResponsableEntretien) {
                                return [
                                    'role' => 'responsable',
                                    'departement' => $departement['departement_nom']
                                ];
                            }
                        }

                        // Déterminer le rôle par département
                        switch ($departement['id_departement']) {
                            case 1: // Direction
                                return [
                                    'role' => 'gestion',
                                    'departement' => $departement['departement_nom']
                                ];
                            case 2: // Comptabilité
                                return [
                                    'role' => 'compta', 
                                    'departement' => $departement['departement_nom']
                                ];
                            case 3: // Stock
                                return [
                                    'role' => 'stock',
                                    'departement' => $departement['departement_nom']
                                ];
                            case 4: // RH
                                return [
                                    'role' => 'rh',
                                    'departement' => $departement['departement_nom']
                                ];
                            case 5: // Vente
                                return [
                                    'role' => 'vente',
                                    'departement' => $departement['departement_nom']
                                ];
                            default:
                                return [
                                    'role' => 'admin',
                                    'departement' => $departement['departement_nom']
                                ];
                        }
                    }
                } catch (\Exception $e) {
                    error_log("Erreur getRoleUtilisateur admin: " . $e->getMessage());
                }
            }

            return [
                'role' => 'admin',
                'departement' => null
            ];
        }

        // ---- 4️⃣ Visiteur non connecté ----
        return [
            'role' => 'visiteur',
            'departement' => null
        ];
    }

}
