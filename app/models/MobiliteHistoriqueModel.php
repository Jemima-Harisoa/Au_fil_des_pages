<?php

namespace app\models;

use Flight;
use PDO;
use PDOException;

class MobiliteHistoriqueModel
{
    public function __construct() {}

    public static function getAllMobiliteHistorique(){
        $db = Flight::db();
        $sql = "SELECT 
            hm.id_mobilite,
            hm.date_evenement,
            hm.support,
            
            -- Informations de l'employé
            e.id_employe,
            p.nom || ' ' || p.prenom AS employe_nom_complet,
            p.nom,
            p.prenom,
            e.poste AS poste_actuel,
            d.nom AS departement_actuel,
            
            -- Détails du mouvement
            ev.nom_evenement AS type_evenement,
            pr.titre AS poste_cible,
            dep.nom AS departement_cible,

            -- Libellé prêt à afficher (très pratique)
            CASE 
                WHEN ev.nom_evenement = 'Embauche' THEN 'Embauche'
                WHEN ev.nom_evenement = 'Promotion' THEN 'Promotion → ' || COALESCE(pr.titre, 'nouveau poste')
                WHEN ev.nom_evenement = 'Mutation' THEN 'Mobilité → ' || COALESCE(dep.nom, 'nouveau département')
                WHEN ev.nom_evenement = 'Démission' THEN 'Démission'
                WHEN ev.nom_evenement = 'Licenciement' THEN 'Licenciement'
                WHEN ev.nom_evenement = 'Fin de CDD' THEN 'Fin de contrat'
                ELSE COALESCE(ev.nom_evenement, 'Mouvement')
            END AS libelle_mouvement

        FROM historique_mobilite hm
        JOIN employes e ON hm.id_employe = e.id_employe
        JOIN personnes p ON e.id_personne = p.id_personne
        LEFT JOIN evenements ev ON hm.id_evenement = ev.id_evenement
        LEFT JOIN profils pr ON hm.id_profil = pr.id_profil
        LEFT JOIN departements dep ON hm.id_departement = dep.id_departement
        LEFT JOIN departements d ON e.id_departement = d.id_departement

        ORDER BY hm.date_evenement DESC, hm.id_mobilite DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
