-- ========================================
-- FICHIER DE TEST - Données cohérentes
-- Système de gestion RH et congés
-- ========================================

-- =========================
-- 1. DONNÉES DE RÉFÉRENCE (SANS DÉPENDANCES)
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

-- Types de prime
INSERT INTO type_prime (libelle) VALUES
('Prime de performance'),
('Prime d ancienneté'),
('Prime de transport'),
('Prime de panier'),
('Prime de fin d année');

-- Paramètres système
INSERT INTO parametre (libelle, pourcentage) VALUES
('Taux heure supplémentaire', 25.00),
('Taux majoré nuit', 50.00),
('Taux majoré dimanche', 100.00),
('Taux majoré férié', 150.00);

-- SMIG (Salaire Minimum Interprofessionnel Garanti)
INSERT INTO smig (montant, date_application) VALUES
(250000.00, '2024-01-01'),
(275000.00, '2025-01-01');

-- Tranches IRSA (Impôt sur le Revenu des Salariés)
INSERT INTO irsa (min, max, pourcentage) VALUES
(0, 350000, 0),
(350001, 400000, 5),
(400001, 500000, 10),
(500001, 600000, 15),
(600001, NULL, 20);

-- Seuil de tolérance pour les retards
INSERT INTO seuil_tolerance (valeur) VALUES
(5.00);

-- Configuration heures supplémentaires
INSERT INTO heure_supplementaire_config (nombre_premieres_heures) VALUES
(8);

-- Status validation CV
INSERT INTO status_validation_cv (statut) VALUES
('En attente'),
('Validé'),
('Rejeté'),
('À corriger');

-- Événements de mobilité
INSERT INTO evenements (nom_evenement) VALUES
('Promotion'),
('Changement de poste'),
('Mutation département'),
('Formation interne');

-- API externes
INSERT INTO api (nom, cle_api) VALUES
('API SMMT', 'smmt_sk_test_abc123xyz456'),
('API Paie Madagascar', 'paie_mg_sk_def789uvw012');

-- Seuil (treshold)
INSERT INTO treshold (valeur, date_treshold) VALUES
(80.00, '2025-01-01 00:00:00'),
(75.00, '2025-06-01 00:00:00');

-- Message automatique
INSERT INTO message_automatique (message) VALUES
('Votre demande de congé a été enregistrée. Elle sera examinée par les responsables dans les 48 heures. Vous recevrez une notification dès validation.');

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

-- Profils CV
INSERT INTO profilsCV (titre, competences, skills, loisirs, id_diplome, filiere, experience_pro, certifications, langues, id_type_contrat, est_minimum, id_departement) VALUES
('Développeur Full Stack', 'Développement web frontend et backend; Gestion de bases de données; DevOps et CI/CD; Tests unitaires et d''intégration', 'JavaScript, React, Node.js; Python, Django; PostgreSQL, MongoDB; Git, Docker', 'Coding challenges; Contribution open source; Veille technologique', 3, 'Informatique', '2-3 ans d''expérience en développement web', 'AWS Certified Developer', 'Français, Anglais technique', 1, TRUE, 2);

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
-- 8. DONNÉES CONNEXION EMPLOYÉS
-- =========================

-- Connexion employés (mdp crypté en SHA256 - tous valent "123")
INSERT INTO connexEmployes (idEmploye, mdp) VALUES
(1, 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'),
(2, 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'),
(3, 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'),
(4, 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'),
(5, 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'),
(6, 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'),
(7, 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'),
(8, 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'),
(9, 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'),
(10, 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3');

-- =========================
-- 9. HORAIRES ET POINTAGE
-- =========================

-- Horaires des employés (du lundi au vendredi)
INSERT INTO horaires_employe (id_employe, jour_semaine, debut_travail, fin_travail) VALUES
-- RH Manager
(1, 1, '08:00:00', '17:00:00'),
(1, 2, '08:00:00', '17:00:00'),
(1, 3, '08:00:00', '17:00:00'),
(1, 4, '08:00:00', '17:00:00'),
(1, 5, '08:00:00', '17:00:00'),
-- Développeuse Senior
(2, 1, '08:30:00', '17:30:00'),
(2, 2, '08:30:00', '17:30:00'),
(2, 3, '08:30:00', '17:30:00'),
(2, 4, '08:30:00', '17:30:00'),
(2, 5, '08:30:00', '17:30:00'),
-- Opérateur Logistique
(3, 1, '07:00:00', '16:00:00'),
(3, 2, '07:00:00', '16:00:00'),
(3, 3, '07:00:00', '16:00:00'),
(3, 4, '07:00:00', '16:00:00'),
(3, 5, '07:00:00', '16:00:00');

-- Pointage journalier (données pour novembre 2025)
INSERT INTO pointage_journalier (id_employe, date_pointage, retard, heures_supp, pause, heures_travaillees) VALUES
-- Développeuse Senior - ponctuelle
(2, '2025-11-10', '00:00:00', '01:30:00', '01:00:00', '08:30:00'),
(2, '2025-11-11', '00:00:00', '00:45:00', '01:00:00', '08:45:00'),
-- Opérateur - quelques retards
(3, '2025-11-10', '00:15:00', '00:00:00', '01:00:00', '07:45:00'),
(3, '2025-11-11', '00:30:00', '00:00:00', '01:00:00', '07:30:00');

-- Pointage des connexions
INSERT INTO pointage (id_employe, connexion, deconnexion, duree_session) VALUES
(2, '2025-11-10 08:28:00', '2025-11-10 17:35:00', '09:07:00'),
(2, '2025-11-11 08:25:00', '2025-11-11 17:40:00', '09:15:00'),
(3, '2025-11-10 07:12:00', '2025-11-10 16:05:00', '08:53:00');

-- Heures supplémentaires
INSERT INTO heures_supplementaire (id_employe, nombre_heure_effectue, mois, annee, numero_semaine) VALUES
(2, 4.50, 11, 2025, 45),
(2, 3.25, 11, 2025, 46),
(3, 2.00, 11, 2025, 45);

-- Historique heures supplémentaires
INSERT INTO heures_supplementaire_historique (id_heure_supp, nombre_heure_effectue, mois, annee, numero_semaine) VALUES
(1, 4.50, 11, 2025, 45),
(2, 3.25, 11, 2025, 46);

-- =========================
-- 10. GESTION DES CONGÉS - CORRIGÉ
-- =========================

-- Demande de congés (CORRIGÉ : caractères UTF8 uniquement)
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
-- Scénario 1: Congé annuel classique - EN ATTENTE (0 validation sur 2 requises)
('Congé annuel fin d''annee (Noel et Nouvel An)', 2, '2025-11-01 09:00:00', '2025-12-20 00:00:00', '2025-12-31 23:59:59', 2, 1),
-- Scénario 2: Congé maladie - PARTIELLEMENT VALIDÉ (1 validation sur 2)
('Congé maladie suite a grippe severe', 3, '2025-11-10 08:15:00', '2025-11-25 08:00:00', '2025-11-29 17:00:00', 2, 2),
-- Scénario 3: Congé maternité - COMPLÈTEMENT VALIDÉ (2 validations)
('Congé maternité - Date prevue accouchement: 15 mars', 5, '2025-02-15 09:00:00', '2025-03-01 00:00:00', '2025-06-06 23:59:59', 2, 3),
-- Scénario 4: Congé sans solde - EN ATTENTE
('Congé sans solde pour raisons personnelles', 7, '2025-11-05 10:00:00', '2025-12-01 00:00:00', '2025-12-15 23:59:59', 2, 5),
-- Scénario 5: Congé paternité - COMPLÈTEMENT VALIDÉ
('Congé paternité - Naissance de mon fils', 9, '2025-10-01 08:00:00', '2025-10-15 00:00:00', '2025-10-25 23:59:59', 2, 4),
-- Scénario 6: Congé formation - PARTIELLEMENT VALIDÉ
('Formation Python avancee et Machine Learning', 10, '2025-11-01 09:00:00', '2025-12-10 08:00:00', '2025-12-15 17:00:00', 2, 6),
-- Scénario 7: Congé annuel court - EN ATTENTE
('Congé pour mariage familial', 8, '2025-11-12 09:00:00', '2025-12-05 00:00:00', '2025-12-07 23:59:59', 2, 1),
-- Scénario 8: Congé annuel - REFUSÉ (dépassement) - CORRIGÉ : pas de caractères spéciaux
('Congé long - Voyage a l etranger', 6, '2025-10-01 10:00:00', '2025-11-01 00:00:00', '2025-11-30 23:59:59', 0, 1);

-- Historique validations congés (CORRIGÉ : IDs de demande valides)
INSERT INTO conge_historique_validation (id_demande, id_employe, date_validation) VALUES
-- Validation 1 pour congé maladie (id_demande=2)
(2, 1, '2025-11-10 10:30:00'),
-- Validations complètes pour congé maternité (id_demande=3)
(3, 1, '2025-02-15 14:00:00'),
(3, 2, '2025-02-15 16:00:00'),
-- Validations complètes pour congé paternité (id_demande=5)
(5, 1, '2025-10-01 11:00:00'),
(5, 2, '2025-10-01 15:00:00'),
-- Validation 1 pour congé formation (id_demande=6)
(6, 1, '2025-11-01 14:00:00');

-- Absences enregistrées
INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
-- Absence maternité (validée et appliquée)
(5, '2025-03-01 00:00:00', '2025-06-06 23:59:59', TRUE, 'Certificat medical de grossesse + Acte de naissance'),
-- Absence paternité (validée et appliquée)
(9, '2025-10-15 00:00:00', '2025-10-25 23:59:59', TRUE, 'Acte de naissance de l''enfant'),
-- Absence maladie récente (en cours de validation)
(3, '2025-11-25 08:00:00', '2025-11-29 17:00:00', TRUE, 'Certificat medical - Grippe severe'),
-- Absence non justifiée passée
(6, '2025-08-05 08:00:00', '2025-08-06 17:00:00', FALSE, NULL);

-- Suivi congés/absences (CORRIGÉ : IDs de demande valides)
INSERT INTO abscence_conge_suivi (id_demande, id_abscence, id_type, id_employe, nombre_conge, annee, penalite_appliquee, id_type_penalite) VALUES
-- Suivi congé maternité (déduit 0 jours car non deductible_sur_conge)
(3, 1, 3, 5, 0, 2025, FALSE, NULL),
-- Suivi congé paternité (déduit 0 jours car non deductible_sur_conge)
(5, 2, 4, 9, 0, 2025, FALSE, NULL),
-- Absence non justifiée avec pénalité
(NULL, 4, 2, 6, 2, 2025, TRUE, 2);

-- Historique congés
INSERT INTO conge_historique (nombres_abscence_attribue, id_employe) VALUES
(98, 5),  -- Congé maternité validé
(10, 9),  -- Congé paternité validé
(2, 6);   -- Absence non justifiée enregistrée

-- Solde de congés par type
INSERT INTO conge_solde (id_employe, id_type_conge, solde, annee) VALUES
-- Congés annuels 2025
(1, 1, 25, 2025),
(2, 1, 18, 2025),
(3, 1, 20, 2025),
(4, 1, 0, 2025),
(5, 1, 18, 2025),
(6, 1, 5, 2025),
-- Congés maladie 2025
(1, 2, 15, 2025),
(2, 2, 15, 2025),
(3, 2, 15, 2025);

-- =========================
-- 11. GESTION DE PAIE
-- =========================

-- Données de paie
INSERT INTO salaire_historique (salaire, id_employe) VALUES
(1200000.00, 1),
(900000.00, 2),
(800000.00, 3),
(200000.00, 4),
(700000.00, 5),
(600000.00, 6),
(750000.00, 7),
(950000.00, 8),
(1100000.00, 9),
(650000.00, 10);

-- Primes attribuées
INSERT INTO prime (id_type_prime, pourcentage, id_employe, date_creation) VALUES
(1, 10.00, 2, '2025-11-01 00:00:00'),  -- Prime performance dev senior
(2, 5.00, 1, '2025-11-01 00:00:00'),   -- Prime ancienneté RH
(3, 3.00, 3, '2025-11-01 00:00:00');   -- Prime transport opérateur

-- Préavis
INSERT INTO preavis (id_employe, date_debut_preavis, date_fin_preavis, est_termine) VALUES
(6, '2025-12-01', '2025-12-31', false);

-- =========================
-- 12. RECRUTEMENT
-- =========================

-- Annonces de recrutement
INSERT INTO annonces (id_profil, titre, date_publication, date_expiration, nombre_poste, lien) VALUES
(1, 'Développeur Full Stack Senior', '2025-11-01', '2025-12-15', 2, '/recrutement/dev-fullstack-1125'),
(1, 'Développeur Frontend', '2025-11-10', '2025-12-31', 1, '/recrutement/dev-frontend-1125');

-- Candidats
INSERT INTO candidats (id_personne, id_annonce, id_profil, cv_url, poste, id_utilisateur) VALUES
(4, 1, 1, '/cv/candidat_rajaona.pdf', 'Développeur Full Stack', 2),
(6, 1, 1, '/cv/candidat_randria.pdf', 'Développeur Full Stack', NULL);

-- Tests des candidats
INSERT INTO tests (id_candidat, id_annonce, score_test, date_test) VALUES
(1, 1, 85.50, '2025-11-15'),
(2, 1, 72.00, '2025-11-16');

-- Responsables entretien
INSERT INTO responsable_entretien (id_profil, id_employe, ordre_passage) VALUES
(1, 2, 1),  -- Développeuse Senior en premier
(1, 1, 2);  -- RH Manager en second

-- Planning entretien
INSERT INTO planning_entretien (id_candidat, id_responsable, date_heure_entretien, score_entretien, etat, id_appreciation) VALUES
(1, 1, '2025-11-20 14:00:00', 88.00, 5, 4),  -- Excellent
(1, 2, '2025-11-21 10:00:00', 92.00, 5, 5),  -- Excellent
(2, 1, '2025-11-22 14:00:00', 75.00, 5, 3);  -- Bon

-- Disponibilité entretien
INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour, est_valide) VALUES
(1, '09:00:00', '12:00:00', 1, true),
(1, '14:00:00', '17:00:00', 1, true),
(2, '10:00:00', '12:00:00', 2, true),
(2, '14:00:00', '16:00:00', 2, true);

-- Historique de validation
INSERT INTO historique_validation (id_employe, id_candidat, date_heure_validation, id_etat) VALUES
(1, 1, '2025-11-25 09:30:00', 2),  -- Candidat 1 validé par RH
(2, 1, '2025-11-25 14:15:00', 2);  -- Candidat 1 validé par Tech

-- CV candidats
INSERT INTO cv_candidats (id_candidat, competences, skills, loisirs, id_diplome, filiere, experience_pro, certifications, langues, date_deposition) VALUES
(1, 'Développement web, DevOps, Architecture logicielle', 'JavaScript, React, Node.js, Python, Docker', 'Lecture, Sport, Voyages', 4, 'Informatique', '5 ans en développement fullstack', 'AWS Certified, Scrum Master', 'Français, Anglais, Malagasy', '2025-11-14 10:00:00');

-- Validation CV
INSERT INTO validation_cv (id_candidat, id_cv_candidat, id_status_validation_cv, similarite) VALUES
(1, 1, 2, 92.50);

-- =========================
-- 13. PROFILS ET QUESTIONS
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
-- 14. MOBILITÉ ET ESSAIS
-- =========================

-- Historique de mobilité
INSERT INTO historique_mobilite (id_candidat, id_evenement, id_profil, id_departement, date_evenement, support) VALUES
(1, 1, 1, 2, '2025-11-25', 'Décision de promotion N°2025-11-PROM'),
(2, 3, 1, 3, '2025-11-26', 'Mutation validée par la direction');

-- Essais (périodes d'essai)
INSERT INTO essais (id_personne, id_contrat, id_etat, date_debut, date_fin) VALUES
(4, 4, 4, '2025-09-01', '2025-12-01'),  -- Stagiaire en période d'essai
(10, 10, 2, '2023-01-05', '2023-04-05'); -- Développeuse junior (essai terminé)

-- =========================
-- 15. CONFIGURATION SYSTÈME
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

-- Disponibilité employés
INSERT INTO disponibilite_employe (id_employe, heure_debut, heure_fin) VALUES
(2, '08:00:00', '18:00:00'),
(3, '07:00:00', '17:00:00'),
(4, '08:30:00', '17:30:00');

-- Notifications système
INSERT INTO notifications (id_personne, message, date_notification) VALUES
(2, 'Votre demande de congé a été validée', '2025-11-11 10:30:00'),
(3, 'Rappel : Vous avez une réunion à 14h00', '2025-11-10 13:45:00'),
(5, 'Votre contrat a été mis à jour', '2025-11-09 16:20:00');


INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
(1, '2025-09-10 08:00:00', '2025-09-12 17:00:00', TRUE, 'Certificat médical - Consultation et repos');

INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
(1, '2025-11-18 08:00:00', '2025-11-18 17:00:00', FALSE, NULL);

INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
(2, '2025-07-15 00:00:00', '2025-07-17 23:59:59', TRUE, 'Autorisation formation externe - Attestation fournie');

INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
(2, '2025-10-08 09:00:00', '2025-10-08 17:00:00', FALSE, NULL);

INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
(9, '2025-11-03 08:00:00', '2025-11-03 17:00:00', FALSE, NULL);
