-- ========================================
-- Données d'insertion d'exemple pour Au fil des pages
-- Complément à DonneesTenaIzy.sql
-- ========================================

-- Tables de base (paramètres, énumérations)
-- Note: parametre est déjà fourni en données existantes, on ne le re-fait pas ici

INSERT INTO parametre (libelle, pourcentage) VALUES
('Cotisation sécurité sociale', 8.00),
('Taxe impôt sur revenu', 5.00);

INSERT INTO irsa (min, max, pourcentage) VALUES
(0, 50000, 1.5),
(50000.01, 100000, 2.5),
(100000.01, 200000, 3.5);

INSERT INTO type_prime (libelle) VALUES
('Prime de performance'),
('Prime d''ancienneté'),
('Prime de risque'),
('Prime de transport');

INSERT INTO etat (nom) VALUES
('Planifié'),
('Terminé'),
('Annulé'),
('En attente'),
('Validé'),
('Rejeté');

INSERT INTO appreciation (type_appreciation, code) VALUES
('Très bon', 1),
('Bon', 2),
('Moyen', 3),
('Insuffisant', 4),
('Excellent', 0);

INSERT INTO api (nom, cle_api) VALUES
('ServiceCV', 'sk_live_cv_afd_001'),
('ServiceTest', 'sk_live_test_afd_002'),
('ServiceNotif', 'sk_live_notif_afd_003');

INSERT INTO seuil_tolerance (valeur, date_creation) VALUES
(5.00, '2025-01-01 08:00:00');

INSERT INTO treshold (valeur, date_treshold) VALUES
(75.00, '2025-01-01 00:00:00'),
(85.50, '2025-06-01 00:00:00');

INSERT INTO evenements (nom_evenement) VALUES
('Promotion interne'),
('Mutation'),
('Démission'),
('Retraite'),
('Licenciement');

INSERT INTO status_validation_cv (statut) VALUES
('Validé'),
('Rejeté'),
('En attente'),
('À réviser');

-- ========================================
-- ANNONCES ET CANDIDATS (qui deviendront employés)
-- ========================================

-- Annonces pour les postes
INSERT INTO annonces (id_profil, titre, date_publication, date_expiration, nombre_poste, lien) VALUES
(2, 'Assistant Comptable requis', '2022-02-01', '2022-03-01', 1, 'https://aufildespages.example/jobs/assistant-comptable'),
(5, 'Vendeur(euse) polyvalent', '2021-08-01', '2021-09-01', 1, 'https://aufildespages.example/jobs/vendeur'),
(3, 'Magasinier H/F - Stock', '2020-10-01', '2020-11-01', 1, 'https://aufildespages.example/jobs/magasinier');

-- Candidats = Personnes 3, 4, 5 (qui seront des employés après)
-- Randriamahenina Paul -> Candidat pour Assistant Comptable
INSERT INTO candidats (id_personne, id_annonce, id_profil, cv_url, poste, id_utilisateur) VALUES
(3, 1, 2, '/cv/randriamahenina_paul_comptable.pdf', 'Assistant Comptable', NULL),
(4, 2, 5, '/cv/andriantsitoha_lova_vendeur.pdf', 'Vendeur', NULL),
(5, 3, 3, '/cv/rakotondrazaka_hery_magasinier.pdf', 'Magasinier', NULL);

-- Tests pour les candidats
INSERT INTO tests (id_candidat, id_annonce, score_test, date_test) VALUES
(1, 1, 85.00, '2022-02-15'),  -- Paul, Assistant Comptable
(2, 2, 78.50, '2021-08-15'),  -- Lova, Vendeur
(3, 3, 82.00, '2020-10-15');  -- Hery, Magasinier

-- CVs des candidats
INSERT INTO cv_candidats (id_candidat, competences, skills, loisirs, id_diplome, filiere, experience_pro, certifications, langues, date_deposition) VALUES
(1, 'Comptabilité générale; Déclarations; Bilans; Suivi clients', 'Confidentialité; Minutie; Rigueur', 'Jeux stratégiques; Lecture', 3, 'Comptabilité et Finance', '2 ans en PME', NULL, 'Français, Anglais', '2022-02-10 10:00:00'),
(2, 'Accueil; Mise en rayon; Ventes; Relation client; Inventaire', 'Dynamisme; Écoute; Rapidité', 'Lecture; Activités sociales', 2, 'Toutes series', '1 an en retail', NULL, 'Français', '2021-08-12 14:00:00'),
(3, 'Réception; Stockage; Inventaire; Sécurité; Préparation commandes', 'Organisation; Fiabilité; Résistance physique', 'Sport endurance; Bricolage', 2, 'Logistique', '2 ans en entrepôt', 'Formation Supply Chain', 'Français', '2020-10-08 09:00:00');

-- Validation CVs
INSERT INTO validation_cv (id_candidat, id_cv_candidat, id_status_validation_cv, similarite) VALUES
(1, 1, 1, 90.50),
(2, 2, 1, 87.00),
(3, 3, 1, 88.50);

-- ========================================
-- CONTRATS pour les candidats acceptés
-- ========================================

INSERT INTO contrats (id_candidat, id_type_contrat, url_contrat) VALUES
(1, 1, '/contrats/contrat_paul_assistant_comptable_cdi.pdf'),
(2, 1, '/contrats/contrat_lova_vendeur_cdi.pdf'),
(3, 1, '/contrats/contrat_hery_magasinier_cdi.pdf');

-- ========================================
-- EMPLOYÉS (recrutés depuis les candidats)
-- ========================================

INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche, nombre_conge, salaire_base) VALUES
(3, 1, 2, 'Assistant Comptable', '2022-03-10', 20, 1800.00),
(4, 2, 4, 'Vendeur', '2021-09-01', 22, 1500.00),
(5, 3, 3, 'Magasinier', '2020-11-15', 20, 1600.00);

-- Disponibilités des employés
INSERT INTO disponibilite_employe (id_employe, heure_debut, heure_fin) VALUES
(1, '08:00:00', '17:00:00'),  -- Directeur (Rakoto Jean)
(2, '09:00:00', '17:30:00'),  -- Comptable (Rasoanaivo Marie)
(3, '09:00:00', '17:30:00'),  -- Assistant Comptable (Randriamahenina Paul - ancien candidat)
(4, '08:30:00', '18:00:00'),  -- Vendeur (Andriantsitoha Lova - ancien candidat)
(5, '08:00:00', '16:30:00');  -- Magasinier (Rakotondrazaka Hery - ancien candidat)

-- Essais (période d'essai pour les nouveaux employés)
INSERT INTO essais (id_personne, id_contrat, id_etat, date_debut, date_fin) VALUES
(3, 1, 2, '2022-03-10', '2022-06-10'),  -- Paul en période d'essai CDI
(4, 2, 2, '2021-09-01', '2021-12-01'),  -- Lova en période d'essai CDI
(5, 3, 2, '2020-11-15', '2021-02-15');  -- Hery en période d'essai CDI

-- ========================================
-- PLANNING ENTRETIEN (candidats évalués)
-- ========================================

INSERT INTO planning_entretien (id_candidat, id_responsable, date_heure_entretien, score_entretien, etat, id_appreciation) VALUES
(1, 4, '2022-02-20 10:00:00', 85.00, 2, 1),  -- Paul (candidat 1) - Entretien Comptable, Très bon, ACCEPTÉ
(2, 1, '2021-08-18 09:30:00', 78.50, 2, 2),  -- Lova (candidat 2) - Entretien Vendeur senior, Bon, ACCEPTÉ
(3, 3, '2020-10-20 08:00:00', 82.00, 2, 1);  -- Hery (candidat 3) - Entretien Magasinier, Très bon, ACCEPTÉ

-- Historique validation (validations par les responsables RH/managers)
INSERT INTO historique_validation (id_employe, id_candidat, date_heure_validation, id_etat) VALUES
(1, 1, '2022-03-01 11:00:00', 5),  -- Directeur (employé 1) valide candidat 1 (Paul)
(1, 2, '2021-08-25 14:30:00', 5),  -- Directeur (employé 1) valide candidat 2 (Lova)
(1, 3, '2020-10-25 09:00:00', 5);  -- Directeur (employé 1) valide candidat 3 (Hery)

-- Historique mobilité (promotions, mutations, etc.)
INSERT INTO historique_mobilite (id_candidat, id_evenement, id_profil, id_departement, date_evenement, support) VALUES
(1, 1, 2, 2, '2022-03-10', 'Email officiel'),     -- Paul embauché comme Assistant Comptable, Département Comptabilité
(2, 1, 5, 4, '2021-09-01', 'Email officiel'),     -- Lova embauché comme Vendeur, Département Vente
(3, 1, 3, 3, '2020-11-15', 'Email officiel');     -- Hery embauché comme Magasinier, Département Stock

-- Demandes de congé
INSERT INTO conge_demande (description, id_employe, niveau_validation) VALUES
('Congé annuel - 2 semaines', 2, 2),
('Congé maladie - 3 jours', 3, 2),
('Congé parental', 4, 1),
('RTT - 1 jour', 5, 2);

-- Historique validation congés
INSERT INTO conge_historique_validation (id_demande, id_employe, date_validation) VALUES
(1, 1, '2025-08-01 10:00:00'),  -- Directeur valide congé comptable
(2, 1, '2025-08-05 14:30:00'),  -- Directeur valide congé assistant comptable
(3, 1, '2025-07-20 09:00:00'),  -- Directeur valide congé parental vendeur
(4, 1, '2025-08-10 11:00:00');  -- Directeur valide RTT magasinier

-- Historique congés (jours attribués)
INSERT INTO conge_historique (nombres_abscence_attribue, id_employe) VALUES
(20.00, 1),  -- Directeur : 20 jours
(22.00, 2),  -- Comptable : 22 jours
(20.00, 3),  -- Assistant Comptable : 20 jours
(22.00, 4),  -- Vendeur : 22 jours
(20.00, 5);  -- Magasinier : 20 jours

-- Absences
INSERT INTO abscence (debut, fin, est_autorise, justificatif) VALUES
('2025-08-01 09:00:00', '2025-08-01 17:00:00', TRUE, 'Certificat médical - consultation'),
('2025-08-05 09:00:00', '2025-08-05 12:30:00', TRUE, 'Rdv dentaire urgent'),
('2025-09-02 09:00:00', '2025-09-03 17:00:00', FALSE, NULL),
('2025-08-20 14:00:00', '2025-08-20 17:00:00', TRUE, 'Formation obligatoire');

-- Pointage (entrées/sorties des employés)
INSERT INTO pointage (id_employe, connexion, deconnexion, duree_session) VALUES
(1, '2025-09-01 08:00:00', '2025-09-01 17:00:00', '09:00:00'),
(2, '2025-09-01 09:00:00', '2025-09-01 17:30:00', '08:30:00'),
(3, '2025-09-01 09:00:00', '2025-09-01 17:30:00', '08:30:00'),
(4, '2025-09-01 08:30:00', '2025-09-01 18:00:00', '09:30:00'),
(5, '2025-09-01 08:00:00', '2025-09-01 16:30:00', '08:30:00'),
(1, '2025-09-02 08:05:00', '2025-09-02 16:55:00', '08:50:00'),
(2, '2025-09-02 09:00:00', '2025-09-02 17:30:00', '08:30:00');

-- Historique salaires
INSERT INTO salaire_historique (salaire, date_creation, id_employe) VALUES
(2500.00, '2025-01-01 00:00:00', 1),  -- Directeur
(2200.00, '2025-01-01 00:00:00', 2),  -- Comptable
(1800.00, '2025-03-10 00:00:00', 3),  -- Assistant Comptable
(1500.00, '2025-09-01 00:00:00', 4),  -- Vendeur
(1600.00, '2025-11-15 00:00:00', 5),  -- Magasinier
(1800.00, '2025-06-01 00:00:00', 3);  -- Augmentation Assistant Comptable

-- Primes
INSERT INTO prime (id_type_prime, pourcentage, id_employe, date_creation) VALUES
(1, 5.00, 1, '2025-06-30 00:00:00'),   -- Directeur : Prime performance 5%
(1, 3.50, 2, '2025-06-30 00:00:00'),   -- Comptable : Prime performance 3.5%
(2, 2.00, 2, '2025-01-01 00:00:00'),   -- Comptable : Prime ancienneté 2%
(1, 2.50, 4, '2025-08-01 00:00:00'),   -- Vendeur : Prime performance 2.5%
(3, 1.50, 5, '2025-06-01 00:00:00'),   -- Magasinier : Prime risque 1.5%
(4, 2.00, 3, '2025-03-01 00:00:00');   -- Assistant Comptable : Prime transport 2%

-- Notifications
INSERT INTO notifications (id_personne, message, date_notification) VALUES
(3, 'Félicitations Paul ! Vous êtes accepté(e) comme Assistant Comptable. Bienvenue dans l''équipe !', '2022-03-01 10:00:00'),
(4, 'Félicitations Lova ! Vous êtes accepté(e) comme Vendeur. Bienvenue dans l''équipe !', '2021-08-25 14:00:00'),
(5, 'Félicitations Hery ! Vous êtes accepté(e) comme Magasinier. Bienvenue dans l''équipe !', '2020-10-25 08:00:00'),
(3, 'Votre entretien a été planifié le 2022-02-20 à 10:00.', '2022-02-18 10:00:00'),
(4, 'Votre entretien a été planifié le 2021-08-18 à 09:30.', '2021-08-16 14:00:00'),
(5, 'Votre entretien a été planifié le 2020-10-20 à 08:00.', '2020-10-18 09:00:00');

-- ========================================
-- FIN DES DONNÉES D'INSERTION
-- Récapitulatif:
-- Candidats recrutés (devenant employés avec contrats):
-- 1. Paul (Randriamahenina) - Candidat 1 -> Employé 3 - Assistant Comptable - Contrat CDI
-- 2. Lova (Andriantsitoha) - Candidat 2 -> Employé 4 - Vendeur - Contrat CDI
-- 3. Hery (Rakotondrazaka) - Candidat 3 -> Employé 5 - Magasinier - Contrat CDI
-- ========================================

-- ========================================
-- GESTION DE PAIE / RETARDS / CONGÉS (Données d'exemple)
-- Hypothèses :
-- - Heure de début officielle : 08:00:00
-- - Durée normale de travail par jour : 8 heures
-- - Valeur dans `seuil_tolerance.valeur` est en minutes (ex : 5 signifie 5 minutes de tolérance)
-- ========================================

-- 1) Types de congés
INSERT INTO conge_type (libelle) VALUES
('Congé normal'),
('Congé exceptionnel');

-- 2) Initialiser les soldes annuels pour l'année 2025 : 30 jours normaux + 10 jours exceptionnels
-- NOTE : conge_solde.id_type_conge référence conge_type.id_type (créé juste au-dessus)
INSERT INTO conge_solde (id_employe, id_type_conge, solde, annee) VALUES
(1, 1, 30, 2025), (1, 2, 10, 2025),
(2, 1, 30, 2025), (2, 2, 10, 2025),
(3, 1, 30, 2025), (3, 2, 10, 2025),
(4, 1, 30, 2025), (4, 2, 10, 2025),
(5, 1, 30, 2025), (5, 2, 10, 2025);

-- 3) Pointages d'exemple (plusieurs jours) montrant arrivées à l'heure et retards
-- Seuil de tolérance (existant) = 5 minutes -> retard si connexion > 08:00 + 5 minutes
INSERT INTO pointage (id_employe, connexion, deconnexion, duree_session) VALUES
-- 2025-11-10
(1, '2025-11-10 08:00:00', '2025-11-10 16:00:00', '08:00:00'), -- à l'heure
(2, '2025-11-10 08:07:00', '2025-11-10 16:15:00', '08:08:00'), -- retard (08:07 > 08:05)
(3, '2025-11-10 08:03:00', '2025-11-10 16:05:00', '08:02:00'), -- à l'heure (08:03 <= 08:05)
(4, '2025-11-10 08:12:00', '2025-11-10 16:10:00', '07:58:00'), -- retard
(5, '2025-11-10 08:00:00', '2025-11-10 16:30:00', '08:30:00'), -- à l'heure
-- 2025-11-11
(1, '2025-11-11 08:06:00', '2025-11-11 16:05:00', '07:59:00'), -- retard (décalage 1)
(2, '2025-11-11 08:01:00', '2025-11-11 16:01:00', '08:00:00'), -- à l'heure
(3, '2025-11-11 08:10:00', '2025-11-11 16:10:00', '08:00:00'), -- retard
(4, '2025-11-11 08:00:00', '2025-11-11 16:00:00', '08:00:00'), -- à l'heure
(5, '2025-11-11 08:20:00', '2025-11-11 16:15:00', '07:55:00'); -- retard

-- 4) Demandes de congé depuis des employés (exemples)
-- Paul (employé 3) : demande 5 jours congé normal (01/07/2025 - 05/07/2025)
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Congé annuel pour vacances (5 jours)', 3, '2025-06-01 09:00:00', '2025-07-01 00:00:00', '2025-07-05 23:59:59', 2, 1),
-- Lova (employé 4) : demande 2 jours congé exceptionnel (mariage proche)
('Congé exceptionnel (2 jours) pour mariage', 4, '2021-08-01 10:00:00', '2021-08-20 00:00:00', '2021-08-21 23:59:59', 2, 2),
-- Hery (employé 5) : demande 10 jours congé normal
('Congé annuel pour déménagement (10 jours)', 5, '2020-10-01 09:00:00', '2020-11-10 00:00:00', '2020-11-19 23:59:59', 2, 1);

-- 5) Validations (niveau_validation = 2 -> 2 validations : chef de département + RH)
-- Ici on enregistre deux validations par demande (ex : employé 1 = Directeur valide, employé 2 = Responsable RH valide)
-- Pour la demande de Paul (qui a été insérée en premier ci-dessus) -> id_demande = currval('conge_demande_id_demande_seq') - but on ne connaît pas l'id ici, on suppose ordre d'insertion :
-- Pour robustesse dans un script réel on utiliserait RETURNING id_demande. Ici on insère manuellement en considérant les IDs dans l'ordre d'insertion.

-- Hypothèse d'IDs : la première conge_demande insérée ci-dessus a id_demande = (dernier + 1). Pour simplicité, nous retrouvons les demandes par (id_employe, date_debut).

INSERT INTO conge_historique_validation (id_demande, id_employe, date_validation) VALUES
((SELECT id_demande FROM conge_demande WHERE id_employe=3 AND date_debut='2025-07-01 00:00:00' LIMIT 1), 1, '2025-06-05 11:00:00'), -- Directeur valide
((SELECT id_demande FROM conge_demande WHERE id_employe=3 AND date_debut='2025-07-01 00:00:00' LIMIT 1), 2, '2025-06-06 15:00:00'), -- RH valide

((SELECT id_demande FROM conge_demande WHERE id_employe=4 AND date_debut='2021-08-20 00:00:00' LIMIT 1), 1, '2021-08-05 09:00:00'),
((SELECT id_demande FROM conge_demande WHERE id_employe=4 AND date_debut='2021-08-20 00:00:00' LIMIT 1), 2, '2021-08-06 10:30:00'),

((SELECT id_demande FROM conge_demande WHERE id_employe=5 AND date_debut='2020-11-10 00:00:00' LIMIT 1), 1, '2020-10-10 09:00:00'),
((SELECT id_demande FROM conge_demande WHERE id_employe=5 AND date_debut='2020-11-10 00:00:00' LIMIT 1), 2, '2020-10-12 14:00:00');

-- 6) Quand la demande est validée, on crée une entrée d'absence correspondante (autorisé)
INSERT INTO abscence (debut, fin, est_autorise, justificatif) VALUES
('2025-07-01 00:00:00', '2025-07-05 23:59:59', TRUE, 'Congé annuel approuvé (5 jours) - Demande Paul'),
('2021-08-20 00:00:00', '2021-08-21 23:59:59', TRUE, 'Congé exceptionnel approuvé (2 jours) - Demande Lova'),
('2020-11-10 00:00:00', '2020-11-19 23:59:59', TRUE, 'Congé annuel approuvé (10 jours) - Demande Hery');

-- 7) Mettre à jour les soldes via UPDATE (déduire les jours pris)
-- Paul (employé 3) : -5 jours (congé normal)
UPDATE conge_solde SET solde = solde - 5 WHERE id_employe = 3 AND id_type_conge = 1 AND annee = 2025;
-- Lova (employé 4) : -2 jours (congé exceptionnel)
UPDATE conge_solde SET solde = solde - 2 WHERE id_employe = 4 AND id_type_conge = 2 AND annee = 2025;
-- Hery (employé 5) : -10 jours (congé normal)
UPDATE conge_solde SET solde = solde - 10 WHERE id_employe = 5 AND id_type_conge = 1 AND annee = 2025;

-- 8) Exemple de requête pour détecter les retards (à exécuter en SQL)
-- On récupère la valeur actuelle du seuil (en minutes) et on identifie les pointages où l'heure de connexion dépasse 08:00 + seuil minutes
-- SELECT p.*, (p.connexion::time > (time '08:00:00' + (st.valeur || ' minutes')::interval)) AS est_en_retard
-- FROM pointage p CROSS JOIN (SELECT valeur FROM seuil_tolerance ORDER BY id_seuil DESC LIMIT 1) st
-- WHERE p.connexion::time > (time '08:00:00' + (st.valeur || ' minutes')::interval);

-- 9) Exemple simple de fiche de paie de démonstration (insert dans salaire_historique déjà présent)
-- (Les calculs - net à payer, retenues - seront faits dans l'application réelle)

-- FIN DES AJOUTS POUR GESTION PAIE / RETARDS / CONGÉS
