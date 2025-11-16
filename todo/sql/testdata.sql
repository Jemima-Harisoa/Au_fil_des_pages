-- =========================
-- Données de référence complétées
-- =========================

INSERT INTO departements (nom) VALUES 
('Ressources Humaines'),
('Informatique'),
('Opérations'),
('Finance'),
('Marketing'),
('Commercial');

INSERT INTO type_contrats (nom) VALUES
('CDI'),
('CDD'),
('Stage'),
('Freelance');

INSERT INTO conge_type (nom, description) VALUES
('Congé annuel', 'Congé payé annuel'),
('Congé maladie', 'Congé pour raison de santé, justificatif requis'),
('Congé maternité', 'Congé maternité selon la réglementation'),
('Congé sans solde', 'Congé non payé'),
('Congé paternité', 'Congé paternité pour les employés'),
('Congé formation', 'Congé pour formation professionnelle');

INSERT INTO abscence_type_penalite (nom, description, montant) VALUES
('Retard', 'Pénalité pour retard répété', 50000.00),
('Absence non justifiée', 'Retenue salariale pour absence sans justificatif', 200000.00),
('Dépassement congé', 'Retenue pour dépassement de jours de congé accordés', 150000.00),
('Non respect horaires', 'Pénalité pour non-respect des horaires de travail', 75000.00);

INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) VALUES
('Razafindrakoto', 'Andry', '1985-04-12', '+261341234567', NULL),
('Rasoamanana', 'Mialy', '1992-07-01', '+261332345678', NULL),
('Rakoto', 'Hery', '1990-11-20', '+261339876543', NULL),
('Randriatsara', 'Fanja', '2003-05-10', '+261344556677', NULL),
('Razanamparany', 'Lova', '1991-02-28', '+261329998877', NULL),
('Rajaonarivelo', 'Thierry', '1988-09-09', '+261334443322', NULL),
('Andriamasy', 'Nirina', '1995-01-18', '+261330112233', NULL),
('Rakotomanga', 'Tiana', '1993-06-25', '+261339992211', NULL),
('Randriamanantsoa', 'Faly', '1989-12-05', '+261334455667', NULL),
('Ravalomanana', 'Miora', '1996-08-14', '+261331122334', NULL);

INSERT INTO contrats (id_candidat, id_type_contrat, url_contrat) VALUES
(NULL, 1, '/contracts/1.pdf'),
(NULL, 2, '/contracts/2.pdf'),
(NULL, 1, '/contracts/3.pdf'),
(NULL, 3, '/contracts/4.pdf'),
(NULL, 4, '/contracts/5.pdf'),
(NULL, 1, '/contracts/6.pdf');

INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche, nombre_conge, salaire_base) VALUES
(1, 1, 1, 'HR Manager', '2018-03-01', 24, 1200000.00),
(2, 2, 2, 'Développeuse', '2020-06-15', 18, 900000.00),
(3, 3, 3, 'Opérateur', '2019-01-05', 20, 800000.00),
(4, 4, 2, 'Stagiaire', '2025-09-01', 0, 200000.00),
(5, 1, 3, 'Analyste', '2022-02-01', 18, 700000.00),
(6, 1, 2, 'Technicien', '2017-11-10', 12, 600000.00),
(7, 5, 4, 'Comptable', '2019-05-20', 20, 750000.00),
(8, 6, 5, 'Marketing Specialist', '2021-07-11', 22, 950000.00),
(9, 2, 6, 'Commercial Senior', '2018-09-30', 25, 1100000.00),
(10, 3, 2, 'Développeuse Junior', '2023-01-05', 15, 650000.00);

INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Congé annuel fin d''année (Noël)', 2, '2025-11-01 09:00:00', '2025-12-20 00:00:00', '2025-12-31 23:59:59', 2, 1),
('Congé maladie suite à infection', 3, '2025-10-10 08:15:00', '2025-10-10 08:00:00', '2025-10-12 17:00:00', 2, 2),
('Congé annuel demandé en période d''essai', 4, '2025-09-15 09:30:00', '2025-10-01 00:00:00', '2025-10-05 23:59:59', 0, 1),
('Absence non justifiée enregistrée', 6, '2025-08-07 09:00:00', '2025-08-05 08:00:00', '2025-08-06 17:00:00', 0, 2),
('Congé long dépassant le solde', 6, '2025-07-01 10:00:00', '2025-07-10 00:00:00', '2025-07-24 23:59:59', 2, 1),
('Congé maternité', 5, '2025-02-15 09:00:00', '2025-03-01 00:00:00', '2025-08-01 23:59:59', 2, 3),
('Congé planifié, en attente de validation', 2, '2025-11-10 08:30:00', '2026-01-15 00:00:00', '2026-01-20 23:59:59', 1, 1),
('Absence justifiée (rendez-vous médical)', 3, '2025-05-12 09:10:00', '2025-05-12 09:00:00', '2025-05-12 17:00:00', 2, 2),
('Demande annulée par l''employé', 2, '2025-06-20 08:00:00', '2025-07-01 00:00:00', '2025-07-05 23:59:59', 0, 1),
('Demande de congé matin approuvée puis absence PM non justifiée', 3, '2025-09-01 09:00:00', '2025-09-03 08:00:00', '2025-09-03 12:00:00', 2, 1),
('Absence PM non justifiée', 3, '2025-09-04 09:00:00', '2025-09-03 13:00:00', '2025-09-03 17:00:00', 0, 2),
('Formation Python avancée', 5, '2025-04-01 09:00:00', '2025-04-10 08:00:00', '2025-04-15 17:00:00', 2, 6),
('Congé paternité', 9, '2025-06-01 08:00:00', '2025-06-05 00:00:00', '2025-06-10 23:59:59', 2, 5);

INSERT INTO conge_historique_validation (id_demande, id_employe, date_validation, id_etat) VALUES
(1, 1, '2025-11-02 10:15:00', NULL),
(2, 1, '2025-10-10 12:00:00', NULL),
(3, 1, '2025-09-16 11:00:00', NULL),
(6, 1, '2025-02-16 14:00:00', NULL),
(12, 1, '2025-04-05 09:00:00', NULL),
(13, 1, '2025-06-03 11:00:00', NULL);

INSERT INTO abscence (debut, fin, est_autorise, justificatif) VALUES
('2025-10-10 08:00:00', '2025-10-12 17:00:00', TRUE, 'Certificat médical fourni'),
('2025-08-05 08:00:00', '2025-08-06 17:00:00', FALSE, NULL),
('2025-07-10 00:00:00', '2025-07-24 23:59:59', TRUE, 'Demande approuvée mais dépassement constaté'),
('2025-03-01 00:00:00', '2025-08-01 23:59:59', TRUE, 'Attestation maternité'),
('2025-05-12 09:00:00', '2025-05-12 17:00:00', TRUE, 'Attestation rendez-vous médical'),
('2025-09-03 13:00:00', '2025-09-03 17:00:00', FALSE, NULL),
('2025-04-10 08:00:00', '2025-04-15 17:00:00', TRUE, 'Formation Python avancée'),
('2025-06-05 00:00:00', '2025-06-10 23:59:59', TRUE, 'Congé paternité');

INSERT INTO abscence_conge_suivi (id_demande, id_abscence, id_type, id_employe, nombre_conge, annee, penalite_appliquee, id_type_penalite) VALUES
(2, 1, 2, 3, 3, 2025, FALSE, NULL),
(4, 2, 2, 6, 2, 2025, TRUE, 2),
(5, 3, 1, 6, 15, 2025, TRUE, 3),
(6, 4, 3, 5, 153, 2025, FALSE, NULL),
(8, 5, 2, 3, 1, 2025, FALSE, NULL),
(11, 6, 2, 3, 0, 2025, TRUE, 2),
(12, 7, 6, 5, 5, 2025, FALSE, NULL),
(13, 8, 5, 9, 5, 2025, FALSE, NULL);

INSERT INTO conge_historique (nombres_abscence_attribue, id_employe) VALUES
(2, 6),
(3, 6),
(0.5, 3),
(5, 5),
(5, 9);

-- =========================
-- Données supplémentaires depuis DonneesTenaIzy.sql
-- Tables qui n'existent pas dans testdata.sql
-- =========================

-- Utilisateurs simples
INSERT INTO utilisateurs (nom, mdp) VALUES
('ali', '123'),
('bob', '123'),
('leo', '123'),
('max', '123'),
('tom', '123');

-- Admins simples
INSERT INTO admins (id_employe, nom, mdp) VALUES
(1, 'ADM', '123'),
(2, 'SYS', '123'),
(3, 'HR', '123');

-- Diplômes
INSERT INTO diplomes (nom, niveau) VALUES
('Brevet', -3),
('Bac', 0),
('BTS / DUT (Bacc+2)', 2),
('Licence (Bacc+3)', 3),
('Master (Bacc+5)', 5),
('Doctorat', 6);

-- Filieres
INSERT INTO filieres (nom) VALUES
('Toutes series'),
('Comptabilite et Finance'),
('Management et Commerce'),
('Logistique');

-- Profils CV
INSERT INTO profilsCV (
  titre, competences, skills, loisirs, id_diplome, filiere, experience_pro, 
  certifications, langues, id_type_contrat, est_minimum, id_departement
) VALUES
('Caissier / Caissiere',
 'Accueillir et encaisser les clients; Assurer la rapidite et la fiabilite des transactions; Maintenir un espace de caisse organise et propre; Appliquer les procedures de securite et de controle',
 'Rigueur et honnetete; Rapidite d''execution; Gestion du stress',
 'Jeux de logique; Activites demandant precision',
 2, 'Toutes series', '1 an en caisse ou grande surface', NULL, NULL, 1, TRUE, NULL),

('Comptable',
 'Assurer la tenue de la comptabilite generale et analytique; Etablir les bilans et declarations fiscales; Analyser les flux financiers; Conseiller la direction sur la gestion budgetaire',
 'Confidentialite; Esprit analytique; Minutie; Gestion des priorites',
 'Jeux strategiques; Sudoku; Activites de gestion',
 3, 'Comptabilite et Finance', '2 a 3 ans d''experience en cabinet ou PME', NULL, NULL, 1, TRUE, NULL),

('Gerant / Manager',
 'Superviser et coordonner les equipes; Prendre des decisions strategiques; Assurer la rentabilite et le developpement de l''activite; Gerer les conflits et favoriser la cohesion',
 'Leadership; Prise de decision; Gestion des conflits; Vision strategique',
 'Lecture sur l''economie et entrepreneuriat; Sport collectif (leadership)',
 4, 'Management et Commerce', '3 a 5 ans d''experience en commerce ou gestion', NULL, NULL, 1, TRUE, NULL),

('Magasinier',
 'Receptionner et stocker les marchandises; Preparer les commandes; Assurer le suivi des inventaires; Respecter les consignes de securite',
 'Organisation; Fiabilite; Resistance physique; Esprit d''equipe',
 'Sport (endurance, fitness); Bricolage (sens pratique)',
 2, 'Logistique', '1 a 2 ans en gestion de stock', NULL, NULL, 1, TRUE, NULL);

-- Profils
INSERT INTO profils (
  titre, competences, skills, loisirs, id_diplome, id_filiere, experience_pro, 
  certifications, langues, id_type_contrat, est_minimum
) VALUES
('Caissier / Caissiere',
 'Accueillir et encaisser les clients; Assurer la rapidite et la fiabilite des transactions; Maintenir un espace de caisse organise et propre; Appliquer les procedures de securite et de controle',
 'Rigueur et honnetete; Rapidite d''execution; Gestion du stress',
 'Jeux de logique; Activites demandant precision',
 2, 1, '1 an en caisse ou grande surface', NULL, NULL, 1, TRUE),

('Comptable',
 'Assurer la tenue de la comptabilite generale et analytique; Etablir les bilans et declarations fiscales; Analyser les flux financiers; Conseiller la direction sur la gestion budgetaire',
 'Confidentialite; Esprit analytique; Minutie; Gestion des priorites',
 'Jeux strategiques; Sudoku; Activites de gestion',
 3, 2, '2 a 3 ans d''experience en cabinet ou PME', NULL, NULL, 1, TRUE),

('Gerant / Manager',
 'Superviser et coordonner les equipes; Prendre des decisions strategiques; Assurer la rentabilite et le developpement de l''activite; Gerer les conflits et favoriser la cohesion',
 'Leadership; Prise de decision; Gestion des conflits; Vision strategique',
 'Lecture sur l''economie et entrepreneuriat; Sport collectif (leadership)',
 4, 3, '3 a 5 ans d''experience en commerce ou gestion', NULL, NULL, 1, TRUE);

-- Questions
INSERT INTO questions (question, id_profil, note) VALUES
('Comment gerer une file d attente en caisse ?', 1, 4.50),
('Que faire si un billet est suspect ?', 1, 5.00),
('Comment assurer la rapidite sans erreurs ?', 1, 5.50),
('Quelles sont les regles de securite a respecter ?', 1, 4.50),
('Comment reagir face a un client mecontent ?', 1, 6.00);

-- Réponses questions
INSERT INTO reponses_question (id_question, reponse, est_correct) VALUES
(1, 'Rester courtois et rapide, orienter si besoin', TRUE),
(1, 'Ignorer les clients et continuer lentement', FALSE),
(2, 'Verifier avec un detecteur de faux billets', TRUE),
(2, 'Accepter sans verification', FALSE),
(3, 'Scanner correctement et compter la monnaie avec attention', TRUE),
(3, 'Parler au telephone en meme temps', FALSE);

-- Message automatique
INSERT INTO message_automatique (message) 
VALUES ('Merci d avoir complete le test. Vos reponses ont ete enregistrees. Les responsables vont analyser vos resultats et vous serez recontacte prochainement.');

-- Configuration entretien
INSERT INTO config_entretien (id_departement, duree_entretien) VALUES
(1, '00:30:00'),
(2, '00:40:00'),
(3, '01:00:00'),
(4, '01:15:00');

-- Responsable entretien
INSERT INTO responsable_entretien (id_profil, id_employe, ordre_passage) VALUES
(1, 1, 1),
(1, 2, 2),
(2, 3, 1),
(2, 4, 2);

-- Disponibilité entretien
INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour) VALUES
(1, '09:00', '12:00', 1),
(1, '09:00', '12:00', 2),
(2, '14:00', '17:00', 1),
(2, '14:00', '17:00', 3);

-- Jours fériés
INSERT INTO jour_ferie("date") VALUES
('2025-01-01'),
('2025-03-29'),
('2025-05-01'),
('2025-06-26'),
('2025-08-15'),
('2025-11-01'),
('2025-12-25');