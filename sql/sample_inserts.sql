-- ========================================
-- sample_inserts.sql
-- Données d'insertion consolidées (Jusqu'à config_entretien)
-- Contient uniquement les INSERTs pour les tables définies avant et incluant `config_entretien`.
-- Ne contient PAS les tables suivantes du schéma (ex: evenements, conge_*, salaire_*, etc.)
-- ========================================

-- NOTE: Exécuter après la création du schéma (sql/SCRIPTERH2.sql).

-- ========================================
-- 1) UTILISATEURS / REFERENTIELS DE BASE
-- ========================================

INSERT INTO utilisateurs (nom, mdp, date_inscription) VALUES
('ema', 'mdp1', now()),
('jean', 'mdp2', now()),
('sophie', 'mdp3', now());

INSERT INTO treshold (valeur, date_treshold) VALUES
(5.00, now());

INSERT INTO departements (nom) VALUES 
('Direction'),
('Comptabilite'),
('Stock'),
('Vente');

INSERT INTO diplomes (nom, niveau) VALUES
('Brevet', -3),
('Bac', 0),
('BTS / DUT (Bacc+2)', 2),
('Licence (Bacc+3)', 3),
('Master (Bacc+5)', 5);

INSERT INTO filieres (nom) VALUES
('Toutes series'),
('Comptabilite et Finance'),
('Management et Commerce'),
('Logistique');

INSERT INTO type_contrats (nom) VALUES
('CDI'),
('CDD'),
('Stage');

-- ========================================
-- 2) PROFILS
-- ========================================

INSERT INTO profils (
  titre, competences, skills, loisirs, id_diplome, id_filiere, experience_pro, certifications, langues, id_type_contrat, id_departement, est_minimum
) VALUES
('Vendeur', 'Accueil; Vente; Mise en rayon', 'Relation client; Caissier', 'Lecture; Sport', 2, 1, '1 an expérience en magasin', NULL, 'Français', 1, 4, TRUE),
('Magasinier', 'Réception; Stock; Préparation commandes', 'Gestion stock; Inventaire', 'Bricolage', 2, 4, '2 ans en entrepôt', NULL, 'Français', 1, 3, TRUE),
('Assistant Comptable', 'Comptabilité; Saisie; Facturation', 'Excel; Rigoureux', 'Jeux de logique', 3, 2, '1-2 ans en cabinet', NULL, 'Français', 1, 2, TRUE);

-- ========================================
-- 3) ANNONCES
-- ========================================

INSERT INTO annonces (id_profil, titre, date_publication, date_expiration, nombre_poste, lien) VALUES
(1, 'Vendeur polyvalent', '2021-08-01', '2021-09-01', 1, 'https://aufildespages.example/jobs/vendeur'),
(2, 'Magasinier - Stock', '2020-10-01', '2020-11-01', 1, 'https://aufildespages.example/jobs/magasinier'),
(3, 'Assistant Comptable', '2022-02-01', '2022-03-01', 1, 'https://aufildespages.example/jobs/assistant-comptable');

-- ========================================
-- 4) PERSONNES
-- ========================================

INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) VALUES
('Rakoto', 'Jean', '1985-03-12', '0341234567', 'images/jean.jpg'),
('Rasoanaivo', 'Marie', '1990-07-25', '0342345678', 'images/marie.jpg'),
('Randriamahenina', 'Paul', '1988-11-02', '0343456789', 'images/paul.jpg'),
('Andriantsitoha', 'Lova', '1995-01-15', '0344567890', 'images/lova.jpg'),
('Rakotondrazaka', 'Hery', '1992-05-30', '0345678901', 'images/hery.jpg');

-- ========================================
-- 5) CANDIDATS
-- ========================================

INSERT INTO candidats (id_personne, id_annonce, id_profil, cv_url, poste) VALUES
(4, 1, 1, '/cv/lova.pdf', 'Vendeur'),
(5, 2, 2, '/cv/hery.pdf', 'Magasinier'),
(3, 3, 3, '/cv/paul.pdf', 'Assistant Comptable');

-- ========================================
-- 6) CONTRATS (liés à candidats qui ont signé)
-- ========================================

INSERT INTO contrats (id_candidat, id_type_contrat, url_contrat) VALUES
(1, 1, '/contrats/contrat_lova_cdi.pdf'),
(2, 1, '/contrats/contrat_hery_cdi.pdf'),
(3, 1, '/contrats/contrat_paul_cdi.pdf');

-- ========================================
-- 7) ESSAIS
-- ========================================

INSERT INTO essais (id_personne, id_contrat, id_etat, date_debut, date_fin) VALUES
(4, 1, 1, '2021-09-01', '2021-11-30'),
(5, 2, 1, '2020-11-15', '2021-02-15');

-- ========================================
-- 8) EMPLOYES
-- ========================================

INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche, nombre_conge, salaire_base) VALUES
(1, NULL, 1, 'Directeur', '2019-01-10', 30, 3000.00),
(3, 3, 2, 'Assistant Comptable', '2022-03-10', 30, 1800.00),
(4, 1, 4, 'Vendeur', '2021-09-01', 30, 1500.00),
(5, 2, 3, 'Magasinier', '2020-11-15', 30, 1600.00);

-- ========================================
-- 9) DISPONIBILITE_EMPLOYE
-- ========================================

INSERT INTO disponibilite_employe (id_employe, heure_debut, heure_fin) VALUES
(1, '08:00:00', '17:00:00'),
(2, '09:00:00', '17:30:00'),
(3, '08:30:00', '18:00:00'),
(4, '08:00:00', '16:30:00');

-- ========================================
-- 10) ADMINS
-- ========================================

INSERT INTO admins (id_employe, nom, mdp, date_affiliation) VALUES
(1, 'admin_dir', 'secret1', now()),
(2, 'admin_acc', 'secret2', now());

-- ========================================
-- 11) API, QUESTIONS, REPONSES
-- ========================================

INSERT INTO api (nom, cle_api) VALUES
('jobboard', 'APIKEY_JOBBOARD_123'),
('cvmatcher', 'APIKEY_CVMATCH_456');

INSERT INTO questions (question, id_profil, note) VALUES
('Expliquez le principe de la tenue de caisse.', 1, 10),
('Comment enregistrez-vous une facture fournisseur ?', 3, 15);

INSERT INTO reponses_question (id_question, reponse, est_correct) VALUES
(1, 'Saisir chaque opération, conserver la caisse equilibrée', TRUE),
(2, 'Enregistrer la facture en achat, lier au fournisseur et archiver.', TRUE);

-- ========================================
-- 12) TESTS
-- ========================================

INSERT INTO tests (id_candidat, id_annonce, score_test, date_test) VALUES
(1, 1, 78.5, '2021-08-15'),
(2, 2, 82.0, '2020-10-15'),
(3, 3, 85.0, '2022-02-15');

-- ========================================
-- 13) ETAT / APPRECIATION / PLANNING_ENTRETIEN
-- ========================================

INSERT INTO etat (nom) VALUES
('EN_COURS'),
('TERMINE'),
('ANNULE');

INSERT INTO appreciation (type_appreciation, code) VALUES
('POSITIF', 1),
('NEUTRE', 0),
('NEGATIF', -1);

INSERT INTO planning_entretien (id_candidat, id_responsable, date_heure_entretien, score_entretien, etat, id_appreciation) VALUES
(1, NULL, '2021-08-15 10:00:00', 78.5, 2, 1),
(2, NULL, '2020-10-15 09:00:00', 82.0, 2, 1);

-- ========================================
-- 14) MESSAGE_AUTOMATIQUE, NOTIFICATIONS
-- ========================================

INSERT INTO message_automatique (message) VALUES
('Merci pour votre candidature.'),
('Votre entretien est programmé.');

INSERT INTO notifications (id_personne, message, date_notification) VALUES
(4, 'Votre candidature a été reçue', now()),
(3, 'Test technique disponible', now());

-- ========================================
-- 15) HISTORIQUE_VALIDATION
-- ========================================

INSERT INTO historique_validation (id_employe, id_candidat, date_heure_validation, id_etat) VALUES
(1, 1, now(), 2),
(2, 2, now(), 2);

-- ========================================
-- 16) PROFILSCV, CV_CANDIDATS, STATUS_VALIDATION_CV, VALIDATION_CV
-- ========================================

INSERT INTO profilsCV (titre, competences, skills, loisirs, id_diplome, filiere, experience_pro, certifications, langues, id_type_contrat, est_minimum, id_departement) VALUES
('Profil Vendeur CV', 'Vente; Relation client', 'Rapide; Ponctuel', 'Lecture', 2, 'Toutes series', '1 an', NULL, 'Français', 1, TRUE, 4);

INSERT INTO cv_candidats (id_candidat, competences, skills, loisirs, id_diplome, filiere, experience_pro, certifications, langues, date_deposition) VALUES
(1, 'Accueil; Ventes', 'Relation client', 'Lecture', 2, 'Toutes series', '1 an en magasin', NULL, 'Français', now());

INSERT INTO status_validation_cv (statut) VALUES
('EN_ATTENTE'),
('VALIDE'),
('REJETE');

INSERT INTO validation_cv (id_candidat, id_cv_candidat, id_status_validation_cv, similarite) VALUES
(1, 1, 2, 88.5);

-- ========================================
-- 17) RESPONSABLE_ENTRETIEN / DISPONIBILITE_ENTRETIEN / JOUR_FERIE
-- ========================================

INSERT INTO responsable_entretien (id_profil, id_employe, ordre_passage) VALUES
(1, 1, 1),
(3, 2, 1);

INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour, est_valide) VALUES
(1, '09:00:00', '12:00:00', 2, TRUE),
(2, '14:00:00', '17:00:00', 4, TRUE);

INSERT INTO jour_ferie (date) VALUES
('2025-01-01'),
('2025-05-01');

-- ========================================
-- 18) CONFIG_ENTRETIEN (dernier bloc demandé)
-- ========================================

INSERT INTO config_entretien (id_departement, duree_entretien) VALUES
(1, '00:30:00'),
(2, '00:45:00');

-- FIN des INSERTs pour les tables jusqu'à `config_entretien`.
-- Les tables qui suivent dans le schéma (evenements, conge_*, etc.) ne sont pas peuplées dans ce script par demande explicite.
