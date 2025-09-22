--recuperer les candidats avec leurs informatiions 
CREATE OR REPLACE VIEW v_candidats_personnes AS
SELECT per.*,
        ca.id_candidat,
        ca.id_annonce,
        ca.id_profil,
        ca.cv_url,
        ca.poste
FROM  candidats ca 
JOIN personnes per
on ca.id_personne = per.id_personne;
--recuperer les planning_entretiens 
SELECT pe.*,
       candidat.nom,
    FROM planning_entretien pe
    JOIN (
        SELECT te.id_candidat,
                te.score_test,  
            FROM tests  te,
        join v_candidats_personnes vcp 
        ON ca.id_candidat = te.id_candidat
    )
    as te
    ON pe.id_candidat = te.id_candidat
    JOIN profils pr
    WHERE pe.etat != 1 ;
    

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
        per.nom,
        per.prenom,
        ca.*,
        p.titre,
        te.score_test,
        te.date_test,
        ROW_NUMBER() OVER (PARTITION BY ca.id_profil ORDER BY te.score_test DESC) AS rang
    FROM candidats ca
    JOIN tests te ON ca.id_candidat = te.id_candidat
    JOIN profils p ON  p.id_profil = ca.id_profil
    JOIN personnes per ON per.id_personne = ca.id_personne
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


--Recuperer la configuraion d'entretiens pour un responsable

SELECT *
    FROM config_entretien 
    where id_departement = ?
    and id_config_entretien in(
        SELECT MAX(id_config_entretien) 
        FROM config_entretien ce
        GROUP BY id_departement
    ) ;

--recuperer le responsbable d'entretien avec son departement
SELECT re.*,de.*
FROM (SELECT * 
        FROM responsable_entretien  
        WHERE  id_responsable = ?
)re
JOIN employes em 
ON em.id_employe = re.id_employe
JOIN departements de 
ON de.id_departement = em.id_departement;

--recuperer le planning d'entretien le plus recent pour un responsable d'enetretien
CREATE OR REPLACE VIEW v_planning_entretien_recent_responsable as
SELECT *
FROM planning_entretien 
where date_heure_entretien in(
    SELECT MAX(date_heure_entretien)
    FROM  planning_entretien
    group by id_responsable
);


SELECT 
FROM planning_entretien