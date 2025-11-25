

-- Désactiver temporairement les contraintes si nécessaire
-- (optionnel, TRUNCATE CASCADE gère déjà les dépendances)
CREATE OR REPLACE FUNCTION nettoyer()
RETURNS void AS $$
BEGIN
   -- Truncate toutes les tables avec remise à zéro des identifiants
    TRUNCATE TABLE 
        historique_validation,
        disponibilite_entretien,
        employes,
        essais,
        contrats,
        notifications,
        planning_entretien,
        tests,
        candidats,
        reponses_question,
        questions,
        profils,
        utilisateurs,
        admins,
        personnes,
        annonces,
        diplomes,
        departements,
        etat,
        appreciation,
        type_contrats
        
    RESTART IDENTITY CASCADE;

END;
$$ LANGUAGE plpgsql;

-- utiliser select nettoyer() pour activer

-- Remarque : CASCADE permet de supprimer toutes les lignes dépendantes automatiquement
-- sans provoquer d'erreur de contrainte étrangère.
