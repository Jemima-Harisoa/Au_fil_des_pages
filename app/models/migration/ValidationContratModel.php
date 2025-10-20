<?php
namespace app\models\migration;

use PDO;

class ValidationContratModel {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
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
    public function verifierPermission($id_contrat, $role_utilisateur) {
        $statut_actuel = $this->getStatutActuel($id_contrat);
        $statut_nom = $statut_actuel['nom'] ?? 'brouillon';
        
        $permissions = [
            'rh' => ['brouillon', 'en_attente_etape2'], // RH peut valider étape 1 et étape 3
            'service' => ['en_attente_etape1'], // Service valide la 1re étape
            'candidat' => ['en_attente_etape3'] // Candidat valide la dernière étape
        ];
        
        return in_array($statut_nom, $permissions[$role_utilisateur] ?? []);
    }
}
