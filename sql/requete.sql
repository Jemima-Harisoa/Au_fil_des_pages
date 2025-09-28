--recuperer les candidats avec leurs informatiions 
CREATE OR REPLACE VIEW v_candidats_personnes AS
SELECT per.nom as nom_candidat,
        per.prenom as prenom_candidat,
        per.date_naissance,
        ca.id_candidat,
        ca.id_annonce,
        ca.id_profil,
        pr.titre,
        ca.cv_url,
        ca.poste
FROM  candidats ca 
JOIN personnes per
on ca.id_personne = per.id_personne
JOIN profils pr
    ON pr.id_profil = ca.id_profil
;

--recuperer les responsables d'entretien 
CREATE OR REPLACE VIEW v_responsable_personnes AS
SELECT per.*,
        re.*
FROM  "admins" ad
JOIN (SELECT 
        DISTINCT
        id_responsable,id_admin
        FROM responsable_entretien
        )re
ON re.id_admin = ad.id_admin
JOIN employes em
ON em.id_employe = ad.id_employe
JOIN personnes per
ON per.id_personne = em.id_personne;


--recuperer les planning_entretiens 
SELECT pe.*,
       te.*,
       vrp.nom as nom_responsable,
       vrp.prenom as prenom_responsable,
       e.nom
    FROM planning_entretien pe
    JOIN (
        SELECT vcp.*,
                te.score_test,  
                te.date_test
            FROM tests  te
        join v_candidats_personnes vcp 
        ON te.id_candidat = vcp.id_candidat
    )
    as te
    ON pe.id_candidat = te.id_candidat
    JOIN v_responsable_personnes vrp
    on vrp.id_responsable = pe.id_responsable
    JOIN etat e
    ON e.id_etat = pe.etat
    WHERE pe.etat = 3 ;
    

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
SELECT  pe.*,e.id_departement;
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
        re.id_admin,
        em_de.nom as nom_departement,
        em_de.id_departement
    FROM responsable_entretien re
    JOIN (
        SELECT Em.*,de.nom
        FROM (SELECT em.*,ad.id_admin
        FROM employes em
            join "admins" ad
            ON ad.id_employe = em.id_employe
        )Em
        JOIN departements de
        ON de.id_departement = Em.id_departement
    )em_de
    ON em_de.id_admin = re.id_admin;


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
    JOIN (
        SELECT em.*,ad.id_admin
        from admins ad
        join employes em 
        on em.id_employe = ad.id_employe
    )em
    on em.id_admin = re.id_admin
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


--recuperer les disponibilites  d'entretien pou un admin
select  
    * 
from disponibilite_entretien
where id_responsable in
(select
    id_responsable 
from responsable_entretien 
where id_admin = ?)

--recuperer le departement d'un admin
select 
    * 
from admin ad 
join
(select 
    em.*,de.nom_departement
        from employes ad
        join 
     departements de on de.id_departement = em.id_departement 
    )em
    on em.id_employe = ad.id_employe
where ad.id_admin = ?;