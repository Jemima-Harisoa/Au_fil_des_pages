-- ========================================
-- FICHIER DE TEST - Données cohérentes
-- Système de gestion RH et congés
-- ========================================

-- =========================
-- 1. DONNÉES DE RÉFÉRENCE
-- =========================

-- Départements
INSERT INTO departements (nom) VALUES 
('Ressources Humaines'),
('Informatique'),
('Opérations'),
('Finance'),
('Marketing'),
('Commercial');

-- Types de contrats
INSERT INTO type_contrats (nom) VALUES
('CDI'),
('CDD'),
('Stage'),
('Freelance');

-- Sexe
INSERT INTO sexe (type_sexe) VALUES
('Masculin'),
('Féminin');

-- Types de congés avec configuration cohérente
INSERT INTO conge_type (nom, description, nombre_jour, deductible_sur_salaire, deductible_sur_conge) VALUES
('Congé annuel', 'Congé payé annuel réglementaire', 30, FALSE, TRUE),
('Congé maladie', 'Congé pour raison de santé avec justificatif médical', 15, FALSE, FALSE),
('Congé maternité', 'Congé maternité selon législation malgache (98 jours)', 98, FALSE, FALSE),
('Congé paternité', 'Congé paternité pour les employés (10 jours)', 10, FALSE, FALSE),
('Congé sans solde', 'Congé non payé à la demande de l''employé', 0, TRUE, FALSE),
('Congé formation', 'Congé pour formation professionnelle', 10, FALSE, FALSE);

-- Types de pénalités pour absences
INSERT INTO abscence_type_penalite (nom, description, montant) VALUES
('Retard', 'Pénalité pour retard répété', 50000.00),
('Absence non justifiée', 'Retenue salariale pour absence sans justificatif', 200000.00),
('Dépassement congé', 'Retenue pour dépassement de jours de congé accordés', 150000.00),
('Non respect horaires', 'Pénalité pour non-respect des horaires de travail', 75000.00);

-- Diplômes
INSERT INTO diplomes (nom, niveau) VALUES
('Brevet', -3),
('Bac', 0),
('BTS / DUT (Bacc+2)', 2),
('Licence (Bacc+3)', 3),
('Master (Bacc+5)', 5),
('Doctorat', 6);

-- Filières
INSERT INTO filieres (nom) VALUES
('Toutes séries'),
('Comptabilité et Finance'),
('Management et Commerce'),
('Logistique'),
('Informatique');

-- États
INSERT INTO etat (nom) VALUES
('En attente'),
('Validé'),
('Refusé'),
('En cours'),
('Terminé');

-- Appréciations
INSERT INTO appreciation (type_appreciation, code) VALUES
('Excellent', 5),
('Très bon', 4),
('Bon', 3),
('Moyen', 2),
('Insuffisant', 1);

-- =========================
-- 2. PERSONNES
-- =========================

INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image, id_sexe) VALUES
('Razafindrakoto', 'Andry', '1985-04-12', '+261341234567', '/images/andry.jpg', 1),
('Rasoamanana', 'Mialy', '1992-07-01', '+261332345678', '/images/mialy.jpg', 2),
('Rakoto', 'Hery', '1990-11-20', '+261339876543', '/images/hery.jpg', 1),
('Randriatsara', 'Fanja', '2003-05-10', '+261344556677', '/images/fanja.jpg', 2),
('Razanamparany', 'Lova', '1991-02-28', '+261329998877', '/images/lova.jpg', 2),
('Rajaonarivelo', 'Thierry', '1988-09-09', '+261334443322', '/images/thierry.jpg', 1),
('Andriamasy', 'Nirina', '1995-01-18', '+261330112233', '/images/nirina.jpg', 2),
('Rakotomanga', 'Tiana', '1993-06-25', '+261339992211', '/images/tiana.jpg', 2),
('Randriamanantsoa', 'Faly', '1989-12-05', '+261334455667', '/images/faly.jpg', 1),
('Ravalomanana', 'Miora', '1996-08-14', '+261331122334', '/images/miora.jpg', 2);

-- =========================
-- 3. UTILISATEURS ET ADMINS
-- =========================

-- Utilisateurs simples
INSERT INTO utilisateurs (nom, mdp, date_inscription) VALUES
('user_hery', '123', '2019-01-05 08:00:00'),
('user_fanja', '123', '2025-09-01 08:00:00'),
('user_lova', '123', '2022-02-01 08:00:00'),
('user_nirina', '123', '2019-05-20 08:00:00'),
('user_tiana', '123', '2021-07-11 08:00:00');

-- =========================
-- 4. PROFILS
-- =========================

INSERT INTO profils (titre, competences, skills, loisirs, id_diplome, id_filiere, experience_pro, certifications, langues, id_type_contrat, id_departement, est_minimum) VALUES
('Développeur Full Stack', 
 'Développement web frontend et backend; Gestion de bases de données; DevOps et CI/CD; Tests unitaires et d''intégration',
 'JavaScript, React, Node.js; Python, Django; PostgreSQL, MongoDB; Git, Docker',
 'Coding challenges; Contribution open source; Veille technologique',
 3, 5, '2-3 ans d''expérience en développement web', 'AWS Certified Developer', 'Français, Anglais technique', 1, 2, TRUE);

-- =========================
-- 5. CONTRATS
-- =========================

INSERT INTO contrats (id_type_contrat, url_contrat) VALUES
(1, '/contracts/contrat_001.pdf'),
(1, '/contracts/contrat_002.pdf'),
(1, '/contracts/contrat_003.pdf'),
(3, '/contracts/contrat_004_stage.pdf'),
(1, '/contracts/contrat_005.pdf'),
(1, '/contracts/contrat_006.pdf'),
(2, '/contracts/contrat_007_cdd.pdf'),
(1, '/contracts/contrat_008.pdf'),
(1, '/contracts/contrat_009.pdf'),
(2, '/contracts/contrat_010_cdd.pdf');

-- =========================
-- 6. EMPLOYÉS
-- =========================

INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche, nombre_conge, salaire_base) VALUES
-- RH Manager - Responsable validation niveau 1
(1, 1, 1, 'HR Manager', '2018-03-01', 25, 1200000.00),
-- Développeuse Senior - Responsable IT, validation niveau 2
(2, 2, 2, 'Développeuse Senior', '2020-06-15', 18, 900000.00),
-- Opérateur - Employé normal
(3, 3, 3, 'Opérateur Logistique', '2019-01-05', 20, 800000.00),
-- Stagiaire - Pas de congés payés
(4, 4, 2, 'Stagiaire Développement', '2025-09-01', 0, 200000.00),
-- Analyste - Employée enceinte
(5, 5, 3, 'Analyste de Données', '2022-02-01', 18, 700000.00),
-- Technicien - A dépassé ses congés
(6, 6, 2, 'Technicien Support', '2017-11-10', 5, 600000.00),
-- Comptable
(7, 7, 4, 'Comptable', '2019-05-20', 20, 750000.00),
-- Marketing Specialist
(8, 8, 5, 'Marketing Specialist', '2021-07-11', 22, 950000.00),
-- Commercial Senior
(9, 9, 6, 'Commercial Senior', '2018-09-30', 25, 1100000.00),
-- Développeuse Junior
(10, 10, 2, 'Développeuse Junior', '2023-01-05', 15, 650000.00);

-- =========================
-- 7. ADMINS
-- =========================

INSERT INTO admins (id_employe, nom, mdp, date_affiliation) VALUES
(1, 'admin_andry', '123', '2018-03-01 08:00:00'),  -- HR Manager
(2, 'admin_mialy', '123', '2020-06-15 08:00:00'),  -- Dev Senior
(9, 'admin_faly', '123', '2018-09-30 08:00:00');   -- Commercial Senior

-- =========================
-- 8. DEMANDES DE CONGÉS
-- =========================

-- Scénario 1: Congé annuel classique - EN ATTENTE (0 validation sur 2 requises)
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Congé annuel fin d''année (Noël et Nouvel An)', 2, '2025-11-01 09:00:00', '2025-12-20 00:00:00', '2025-12-31 23:59:59', 2, 1);

-- Scénario 2: Congé maladie - PARTIELLEMENT VALIDÉ (1 validation sur 2)
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Congé maladie suite à grippe sévère', 3, '2025-11-10 08:15:00', '2025-11-25 08:00:00', '2025-11-29 17:00:00', 2, 2);

-- Scénario 3: Congé maternité - COMPLÈTEMENT VALIDÉ (2 validations)
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Congé maternité - Date prévue accouchement: 15 mars', 5, '2025-02-15 09:00:00', '2025-03-01 00:00:00', '2025-06-06 23:59:59', 2, 3);

-- Scénario 4: Congé sans solde - EN ATTENTE
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Congé sans solde pour raisons personnelles', 7, '2025-11-05 10:00:00', '2025-12-01 00:00:00', '2025-12-15 23:59:59', 2, 5);

-- Scénario 5: Congé paternité - COMPLÈTEMENT VALIDÉ
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Congé paternité - Naissance de mon fils', 9, '2025-10-01 08:00:00', '2025-10-15 00:00:00', '2025-10-25 23:59:59', 2, 4);

-- Scénario 6: Congé formation - PARTIELLEMENT VALIDÉ
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Formation Python avancée et Machine Learning', 10, '2025-11-01 09:00:00', '2025-12-10 08:00:00', '2025-12-15 17:00:00', 2, 6);

-- Scénario 7: Congé annuel court - EN ATTENTE
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Congé pour mariage familial', 8, '2025-11-12 09:00:00', '2025-12-05 00:00:00', '2025-12-07 23:59:59', 2, 1);

-- Scénario 8: Congé annuel - REFUSÉ (dépassement)
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Congé long - Voyage à l''étranger', 6, '2025-10-01 10:00:00', '2025-11-01 00:00:00', '2025-11-30 23:59:59', 0, 1);

-- =========================
-- 9. HISTORIQUE VALIDATIONS
-- =========================

-- Validation 1 pour congé maladie (id_demande=2)
INSERT INTO conge_historique_validation (id_demande, id_employe, date_validation) VALUES
(2, 1, '2025-11-10 10:30:00');

-- Validations complètes pour congé maternité (id_demande=3)
INSERT INTO conge_historique_validation (id_demande, id_employe, date_validation) VALUES
(3, 1, '2025-02-15 14:00:00'),
(3, 2, '2025-02-15 16:00:00');

-- Validations complètes pour congé paternité (id_demande=5)
INSERT INTO conge_historique_validation (id_demande, id_employe, date_validation) VALUES
(5, 1, '2025-10-01 11:00:00'),
(5, 2, '2025-10-01 15:00:00');

-- Validation 1 pour congé formation (id_demande=6)
INSERT INTO conge_historique_validation (id_demande, id_employe, date_validation) VALUES
(6, 1, '2025-11-01 14:00:00');

-- =========================
-- 10. ABSENCES ENREGISTRÉES
-- =========================

-- Absence maternité (validée et appliquée)
INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
(5, '2025-03-01 00:00:00', '2025-06-06 23:59:59', TRUE, 'Certificat médical de grossesse + Acte de naissance');

-- Absence paternité (validée et appliquée)
INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
(9, '2025-10-15 00:00:00', '2025-10-25 23:59:59', TRUE, 'Acte de naissance de l''enfant');

-- Absence maladie récente (en cours de validation)
INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
(3, '2025-11-25 08:00:00', '2025-11-29 17:00:00', TRUE, 'Certificat médical - Grippe sévère');

-- Absence non justifiée passée
INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
(6, '2025-08-05 08:00:00', '2025-08-06 17:00:00', FALSE, NULL);

-- =========================
-- 11. SUIVI CONGÉS/ABSENCES
-- =========================

-- Suivi congé maternité (déduit 0 jours car non deductible_sur_conge)
INSERT INTO abscence_conge_suivi (id_demande, id_abscence, id_type, id_employe, nombre_conge, annee, penalite_appliquee, id_type_penalite) VALUES
(3, 1, 3, 5, 0, 2025, FALSE, NULL);

-- Suivi congé paternité (déduit 0 jours car non deductible_sur_conge)
INSERT INTO abscence_conge_suivi (id_demande, id_abscence, id_type, id_employe, nombre_conge, annee, penalite_appliquee, id_type_penalite) VALUES
(5, 2, 4, 9, 0, 2025, FALSE, NULL);

-- Absence non justifiée avec pénalité
INSERT INTO abscence_conge_suivi (id_demande, id_abscence, id_type, id_employe, nombre_conge, annee, penalite_appliquee, id_type_penalite) VALUES
(NULL, 4, 2, 6, 2, 2025, TRUE, 2);

-- =========================
-- 12. HISTORIQUE CONGÉS
-- =========================

INSERT INTO conge_historique (nombres_abscence_attribue, id_employe) VALUES
(98, 5),  -- Congé maternité validé
(10, 9),  -- Congé paternité validé
(2, 6);   -- Absence non justifiée enregistrée

-- =========================
-- 13. PROFILS ET QUESTIONS (Pour recrutement)
-- =========================

INSERT INTO questions (question, id_profil, note) VALUES
('Quelle est la différence entre let et var en JavaScript ?', 1, 5.00),
('Expliquez le principe d''une API RESTful', 1, 6.00),
('Comment optimisez-vous les requêtes SQL ?', 1, 5.50);

INSERT INTO reponses_question (id_question, reponse, est_correct) VALUES
(1, 'let a une portée de bloc tandis que var a une portée de fonction', TRUE),
(1, 'Il n''y a aucune différence', FALSE),
(2, 'API basée sur HTTP avec ressources, méthodes CRUD et stateless', TRUE),
(2, 'API qui utilise uniquement GET et POST', FALSE);

-- =========================
-- 14. CONFIGURATION SYSTÈME
-- =========================

-- Configuration durée entretiens
INSERT INTO config_entretien (id_departement, duree_entretien) VALUES
(1, '00:30:00'),
(2, '00:45:00'),
(3, '00:30:00'),
(4, '01:00:00'),
(5, '00:40:00'),
(6, '00:30:00');

-- Jours fériés 2025-2026
INSERT INTO jour_ferie(date) VALUES
('2025-01-01'),  -- Nouvel An
('2025-03-29'),  -- Fête des Martyrs
('2025-04-21'),  -- Lundi de Pâques
('2025-05-01'),  -- Fête du Travail
('2025-05-29'),  -- Ascension
('2025-06-26'),  -- Fête Nationale
('2025-08-15'),  -- Assomption
('2025-11-01'),  -- Toussaint
('2025-12-25'),  -- Noël
('2026-01-01');  -- Nouvel An

-- Message automatique
INSERT INTO message_automatique (message) VALUES
('Votre demande de congé a été enregistrée. Elle sera examinée par les responsables dans les 48 heures. Vous recevrez une notification dès validation.');