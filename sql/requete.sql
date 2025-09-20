--recuperer les planning_entretiens 
SELECT * 
    FROM planning_entretien pe
    JOIN (
        SELECT id_candidat,
                score_test 
            FROM tests         
    )
    as te
    ON pe.id_candidat = te.id_candidat
    WHERE pe.etat != 'rejete' 
    and 
        pe.date_heure_entretien>=now();

--recuperer les temps de disponibilites pour l'entretien dans un département
CREATE OR REPLACE VIEW v_disponibilite_employe_valide as(
    SELECT de.*,em.poste
    FROM disponibilite_employe de  
    JOIN (
        SELECT * 
        FROM employes 
        where id_departement = 1
    )em
    ON de.id_employe = em.id_employe
    and de.est_valide
);
    --recuperer candidats dans un departement apres une date
    -- en utilisant la table candidat en jointure avec les 
    -- tables planning_entretien,employes
CREATE OR REPLACE VIEW v_entretien_candidat_apres_une_date as(
SELECT  pe.*,e.id_departement
    FROM candidats c
    JOIN planning_entretien pe
    ON pe.id_candidat = c.id_candidat
    JOIN (
        SELECT
            poste,id_departement
        FROM
            employes 
        WHERE id_departement = 1
        )e
    ON e.poste = c.poste
    WHERE pe.date_heure_entretien >=""
);
--Recuperation disponibilite_employe valide pour tous les id_departements
CREATE OR REPLACE VIEW v_disponibilite_employe_valide as(
SELECT de.*,em.id_departement,em.poste,CASE jour
    WHEN 'Lundi'THEN 1
    WHEN 'Mardi'THEN 2
    WHEN 'Mercredi'THEN 3
    WHEN 'Jeudi'THEN 4
    WHEN 'Vendredi'THEN 5
    WHEN 'Samedi'THEN 6
    WHEN 'Dimanche'THEN 7
    END as numero_jour
    FROM  disponibilite_entretien de 
    JOIN (
        SELECT 
        id_employe,poste,id_departement
        FROM employes
    )em
    ON em.id_employe = de.id_employe
    and de.est_valide
);

--Recuperation disponibilite_employe valide pour un id_deparement par ordre chronologique
select  id_employe,
        heure_debut,
        heure_fin,
        numero_jour,
        est_valide,
        id_departement,
        poste
    FROM 
        v_disponibilite_employe_valide
    where id_departement = ? ;

--
--Recuperer les candidats qui ont reussi les tests 

SELECT *
FROM (
    SELECT 
        ca.*,
        te.score_test,
        te.date_test,
        ROW_NUMBER() OVER (PARTITION BY ca.id_profil ORDER BY te.score_test DESC) AS rang
    FROM candidats ca
    JOIN tests te ON ca.id_candidat = te.id_candidat
) t
WHERE rang <= ?
ORDER BY id_profil, rang;


-- Recuperer les responsables de l'entretien d'un candidat ayant reussi le test en utilisant l'id profil du candidat
SELECT * FROM responsable_entretien where id_profil = ?;

--recuperer  le departement de chaque employe responsable
CREATE OR REPLACE VIEW v_responsable_avec_departement AS
    SELECT 
        re.id_responsable,
        re.id_employe,
        em_de.nom as nom_departement,
        em_de.id_departement
    FROM responsable_entretien re
    JOIN (
        SELECT EM.*,de.nom
        FROM (SELECT *
        FROM employes em
        )Em
        JOIN departements de
        ON de.id_departement = Em.id_departement
    )em_de
    ON em_de.id_employe = re.id_employe;


--Recuperer les configurations des entretiens par un responsable

