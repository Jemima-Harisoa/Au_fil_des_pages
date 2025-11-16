create or replace view v_employes_essais as
(select em.*,e.id_essai,e.date_debut,e.date_fin,e.
id_etat
from employes em
left join essais e 
on e.id_personne = em.id_personne);

CREATE OR REPLACE VIEW v_anciennete_employe as(
select 
    id_employe,
	-- séparation année / mois / jours à partir de l'interval obtenu par age()
	date_part('year', age(current_date, COALESCE(date_debut::date, date_embauche::date)))::int AS anciennete_annees,
	date_part('month', age(current_date, COALESCE(date_debut::date, date_embauche::date)))::int AS anciennete_mois,
	date_part('day', age(current_date, COALESCE(date_debut::date, date_embauche::date)))::int AS anciennete_jours,
	(current_date - COALESCE(date_debut::date, date_embauche::date))::int AS anciennete_total_jours
FROM v_employes_essais
);
