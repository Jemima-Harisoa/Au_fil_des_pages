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


create or replace view v_heure_supp as(
	select 
		id_pointage,
		p.id_employe,
		connexion as date_heure_debut,
		duree_session as heure_effectue,
		deconnexion as date_heure_fin,
		extract(month from connexion) mois,
		extract(year from connexion)annee,
		to_char(connexion, 'IW')numero_semaine
	from 
		pointage p
	join
	(
		select 
			id_employe, 
			jour_semaine,
			min(debut_travail) as debut_travail_inf,
			max(fin_travail) as fin_travail_sup
		from 
			horaires_employe 
		group by 
		id_employe,jour_semaine
	)he
		
	on
		he.id_employe = p.id_employe
	where 
		(
			he.jour_semaine = extract(day from p.connexion)
			and (
					(
						connexion::time <  he.debut_travail_inf
							and 
						deconnexion::time <=he.debut_travail_inf
					)
				or
					(
						connexion::time >=  he.fin_travail_sup
							and 
						deconnexion::time > he.fin_travail_sup
					)
			) 
		)
		or extract(day from connexion) >=6 
);