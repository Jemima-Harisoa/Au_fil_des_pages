-- ========================================
-- INSERTIONS DANS L'ORDRE LOGIQUE - CORRIGÉ
-- ========================================

-- Tables de base sans dépendances
INSERT INTO sexe (type_sexe) VALUES 
  ('Masculin'),
  ('Feminin'),
  ('Autre');

INSERT INTO departements (nom) VALUES 
  ('Direction'),
  ('Comptabilite'),
  ('Stock'),
  ('Vente');

INSERT INTO filieres (nom) VALUES
  ('Toutes series'),
  ('Comptabilite et Finance'),
  ('Management et Commerce'),
  ('Logistique');

INSERT INTO type_contrats (nom) VALUES
 ('CDI'),
 ('CDD'),
 ('Stage'),
 ('Freelance'),
 ('Interim'),
 ('Alternance'),
 ('Consultant');

INSERT INTO diplomes (nom, niveau) VALUES
('Brevet', -3),
('Bac', 0),
('BTS / DUT (Bacc+2)', 2),
('Licence (Bacc+3)', 3),
('Master (Bacc+5)', 5),
('Doctorat', 6);

INSERT INTO utilisateurs (nom, mdp) VALUES
('ema', 'mdp1'),
('jean', 'mdp2'),
('sophie', 'mdp3');

INSERT INTO message_automatique (message) 
VALUES ('Merci d avoir complete le test. Vos reponses ont ete enregistrees.Les responsables d Au fil des Page vont analyser vos resultats et vous serez recontacte prochainement.');

INSERT INTO jour_ferie("date") VALUES
('2025-01-01'), ('2025-03-29'), ('2025-05-01'), ('2025-06-26'), 
('2025-08-15'), ('2025-11-01'), ('2025-12-25'), ('2025-02-20');

INSERT INTO etat (nom) VALUES
('En attente'), ('Validé'), ('Refusé'), ('En cours'), ('Terminé');

INSERT INTO appreciation (type_appreciation, code) VALUES
('Excellent', 5), ('Très bien', 4), ('Bien', 3), ('Passable', 2), ('Insuffisant', 1);

INSERT INTO evenements (nom_evenement) VALUES
('Embauche'), ('Promotion'), ('Mutation'), ('Démission'), 
('Licenciement'), ('Fin de contrat'), ('Retraite');

INSERT INTO status_validation_cv (statut) VALUES
('En attente'), ('Validé'), ('Refusé'), ('À revoir');

INSERT INTO type_prime (libelle) VALUES
('Prime de rendement'), ('Prime d''ancienneté'), ('Prime de fin d''année'), 
('Prime exceptionnelle'), ('Prime de responsabilité');

INSERT INTO postes (nom, description) VALUES 
('Développeur Fullstack', 'PHP + Vue.js'), 
('Chargé RH', 'Recrutement et paie'), 
('Comptable', 'Saisie et bilan'), 
('Commercial', 'Prospection B2B'), 
('Technicien maintenance', 'Maintenance machines');

INSERT INTO type_competence (libelle, description) VALUES
    ('Hard Skill', 'Compétences techniques spécifiques et mesurables'),
    ('Soft Skill', 'Compétences comportementales et relationnelles'),
    ('Langue', 'Compétences linguistiques'),
    ('Certification', 'Compétences certifiées par un organisme')
ON CONFLICT DO NOTHING;

INSERT INTO source_evaluation (libelle, description) VALUES
    ('auto-evaluation', 'Evaluation par le employé lui-même'),
    ('manager-evaluation', 'Evaluation par le manager direct'),
    ('rh-evaluation', 'Evaluation par les ressources humaines'),
    ('formation', 'Validation via une formation'),
    ('certification', 'Validation par une certification'),
    ('test-technique', 'Validation par un test technique')
ON CONFLICT DO NOTHING;

-- Personnes (16 personnes pour avoir assez d'employés)
INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image, id_sexe) VALUES
  ('Rakoto', 'Jean', '1985-03-12', '0341234567', 'images/jean.jpg', 1),
  ('Rasoanaivo', 'Marie', '1990-07-25', '0342345678', 'images/marie.jpg', 2),
  ('Randriamahenina', 'Paul', '1988-11-02', '0343456789', 'images/paul.jpg', 1),
  ('Andriantsitoha', 'Lova', '1995-01-15', '0344567890', 'images/lova.jpg', 2),
  ('Rakotondrazaka', 'Hery', '1992-05-30', '0345678901', 'images/hery.jpg', 1),
  ('Lia', 'Mia', '1995-05-12', '0341234560', 'https://img.com/lia.jpg', 2),
  ('Rasoa', 'Sophie', '1998-09-21', '0349876543', 'https://img.com/sophie.jpg', 2),
  ('Andry', 'Michel', '1990-11-03', '0345556667', 'https://img.com/michel.jpg', 1),
  ('Hanitra', 'Lina', '2000-01-15', '0342223334', 'https://img.com/lina.jpg', 2),
  ('Dupont', 'Claire', '1992-04-12', 'claire.dupont@gmail.com', '/img/claire.jpg', 2),
  ('Martin', 'Julien', '1988-09-23', 'julien.m@gmail.com', '/img/julien.jpg', 1),
  ('Traore', 'Aïcha', '1995-06-15', 'aicha.traore@outlook.com', '/img/aicha.jpg', 2),
  ('Rivoire', 'Lucas', '1990-11-30', 'lucas.rivoire@free.fr', '/img/lucas.jpg', 1),
  ('Bernard', 'Sophie', '1985-07-22', 'sophie.bernard@entreprise.com', '/img/sophie2.jpg', 2),
  ('Durand', 'Bob', '1993-08-10', 'bob.durand@mail.com', '/img/bob.jpg', 1),
  ('Petit', 'Caroline', '1991-03-25', 'caroline.petit@mail.com', '/img/caroline.jpg', 2);

-- Employes (12 employés au total)
INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche, nombre_conge, salaire_base) VALUES
  (1, NULL, 1, 'Directeur', '2020-01-15', 30, 5500000),
  (2, NULL, 2, 'Comptable', '2021-06-01', 25, 3500000),
  (3, NULL, 3, 'Caissier', '2023-05-01', 20, 1500000),
  (4, NULL, 4, 'Magasinier', '2024-01-15', 15, 1800000), 
  (5, NULL, 4, 'Magasinier', '2024-01-15', 15, 1800000),
  (6, NULL, 1, 'Directrice RH', '2020-09-01', 30, 5500000),
  (10, NULL, 2, 'Développeur Fullstack', '2024-03-01', 25, 3800000),
  (11, NULL, 1, 'Chargé RH', '2023-06-15', 25, 3500000),
  (12, NULL, 2, 'Comptable', '2022-01-10', 28, 3200000),
  (15, NULL, 4, 'Vendeur', '2023-03-20', 20, 2000000),
  (16, NULL, 4, 'Vendeuse', '2023-04-10', 20, 2000000),
  (8, NULL, 3, 'Magasinier', '2023-02-15', 20, 1900000);

-- Admins
INSERT INTO admins (id_employe, nom, mdp, date_affiliation) VALUES
  (1, 'RD', 'mdp1', '2020-01-15'),
  (2, 'RC', 'mdp2', '2021-06-01'),
  (3, 'admin_stock', 'stock2024', '2022-03-10');

-- Connexion employes
INSERT INTO connexEmployes (idEmploye, mdp) VALUES
(1, 'emp123'), (2, 'emp123'), (3, 'emp123'), (4, 'emp123'), (5, 'emp123'),
(6, 'emp123'), (7, 'emp123'), (8, 'emp123'), (9, 'emp123'), (10, 'emp123'),
(11, 'emp123'), (12, 'emp123');

-- Profils
INSERT INTO profils (titre, competences, skills, loisirs, id_diplome, id_filiere, experience_pro, certifications, langues, id_type_contrat, est_minimum) VALUES
('Caissier / Caissiere', 'Accueillir et encaisser', 'Rigueur', 'Jeux de logique', 2, 1, '1 an', NULL, NULL, 1, TRUE),
('Comptable', 'Comptabilite generale', 'Analytique', 'Sudoku', 3, 2, '2 ans', NULL, NULL, 1, TRUE),
('Gerant / Manager', 'Superviser equipes', 'Leadership', 'Lecture', 4, 3, '3 ans', NULL, NULL, 1, TRUE),
('Magasinier', 'Gestion stock', 'Organisation', 'Sport', 2, 4, '1 an', NULL, NULL, 1, TRUE),
('Vendeur / Vendeuse', 'Conseiller clients', 'Communication', 'Lecture', 2, 1, 'Debutant', NULL, NULL, 1, TRUE);

-- Questions
INSERT INTO questions (question, id_profil, note) VALUES
('Comment gerer une file d attente ?', 1, 4.50),
('Que faire si un billet est suspect ?', 1, 5.00),
('Quelles sont les obligations fiscales ?', 2, 5.50),
('Comment preparer un bilan ?', 2, 6.00),
('Comment motiver une equipe ?', 3, 6.00);

-- Reponses
INSERT INTO reponses_question (id_question, reponse, est_correct) VALUES
(1, 'Rester courtois et rapide', TRUE),
(1, 'Ignorer les clients', FALSE),
(2, 'Verifier avec un detecteur', TRUE),
(2, 'Accepter sans verification', FALSE);

-- Congé types
INSERT INTO conge_type (nom, description, nombre_jour, deductible_sur_salaire, deductible_sur_conge) VALUES
('Congé annuel', 'Congé annuel payé', 30, FALSE, FALSE),
('Congé maladie', 'Congé pour maladie', 30, FALSE, FALSE),
('Congé exceptionnel', 'Congé exceptionnel', 5, FALSE, FALSE),
('Congé sans solde', 'Congé non payé', 0, TRUE, TRUE),
('Congé maternité', 'Congé maternité', 90, FALSE, FALSE);

INSERT INTO abscence_type_penalite (nom, description, montant) VALUES
('Avertissement verbal', 'Avertissement', 0),
('Retenue sur salaire', 'Retenue salaire', 50000);


-- Compétences
INSERT INTO competences (nom, description, domaine, id_type_competence) VALUES
('PHP', 'Programmation PHP', 'Informatique', 1),
('JavaScript', 'Programmation JS', 'Informatique', 1),
('SQL', 'Bases de données', 'Informatique', 1),
('Gestion de projet', 'Méthodologies agiles', 'Management', 2),
('Communication', 'Communication interpersonnelle', 'Soft Skills', 2),
('Anglais', 'Langue anglaise', 'Langues', 3),
('Python', 'Programmation Python', 'Informatique', 1)
ON CONFLICT DO NOTHING;

-- CORRECTION: Utiliser id_employe au lieu de employe_id
INSERT INTO employe_competences (id_employe, id_competence, niveau, id_source, date_mesure, valide, id_employe_validateur, date_validation) VALUES
(1, 1, 5, 2, '2024-02-01 09:00:00', TRUE, 1, '2024-02-02 10:00:00'),
(1, 6, 4, 1, '2024-02-05 11:15:00', TRUE, 1, '2024-02-06 09:30:00'),
(2, 3, 4, 2, '2024-03-02 14:00:00', TRUE, 2, '2024-03-03 10:00:00'),
(7, 7, 3, 6, '2024-03-05 09:00:00', FALSE, NULL, NULL),
(8, 3, 4, 2, '2024-03-06 10:30:00', TRUE, 1, '2024-03-07 08:30:00');

-- Candidats pour historique_mobilite
INSERT INTO candidats (id_personne, poste, cv_url) VALUES
(13, 'Développeur', '/cv/lucas.pdf'),
(14, 'RH Manager', '/cv/sophie.pdf');

-- CORRECTION: historique_mobilite utilise id_candidat, pas id_employe
INSERT INTO historique_mobilite (id_employe, id_evenement, id_profil, id_departement, date_evenement, support) VALUES
(1, 1, NULL, 2, '2024-03-01', 'Embauche initiale'),
(1, 3, 1, 2, '2025-06-15', 'Promotion Senior'),
(2, 1, NULL, 1, '2023-06-15', 'Embauche RH');

-- Managers
INSERT INTO managers (employe_id, date_nomination) VALUES
(1, '2025-10-27'),
(2, '2025-10-27'),
(6, '2025-10-27');

-- CORRECTION: Utiliser seulement des IDs d'employés existants (1-12)
INSERT INTO manager_employes (manager_id, employe_id) VALUES
(1, 7),  -- Jean manage employé 7
(1, 8),  -- Jean manage employé 8
(2, 9),  -- Marie manage employé 9
(2, 10), -- Marie manage employé 10
(3, 11); -- Lina manage employé 11

INSERT INTO manager_admins (id_manager, id_admin) VALUES
(1, 1), (2, 2), (3, 3);

-- Périodes et critères d'évaluation
INSERT INTO employe_evaluation_periodes (nom, description, frequence_mois) VALUES
('Mensuel', 'Evaluation mensuelle', 1), 
('Trimestriel', 'Evaluation trimestrielle', 3), 
('Annuel', 'Evaluation annuelle', 12);

INSERT INTO employe_criteres_evaluation (nom, poids) VALUES
('Qualité du travail', 30),
('Respect des délais', 25),
('Autonomie', 20),
('Esprit d''équipe', 15),
('Initiative', 10);

-- Formations
INSERT INTO formations (titre, description, competence_id, niveau_cible) VALUES
('Formation Python Avancé', 'Python projets complexes', 7, 5), 
('Leadership', 'Gestion d''équipe', 4, 5);

-- Pointages
INSERT INTO horaires_employe (id_employe, jour_semaine, debut_travail, fin_travail, seuil_retard) VALUES
-- Lundi (1)
(1, 1, '08:00:00', '12:00:00', '00:05:00'),
(1, 1, '13:00:00', '17:00:00', '00:05:00'),

-- Mardi (2)
(1, 2, '08:00:00', '12:00:00', '00:05:00'),
(1, 2, '13:00:00', '17:00:00', '00:05:00'),

-- Mercredi (3)
(1, 3, '08:00:00', '12:00:00', '00:05:00'),
(1, 3, '13:00:00', '17:00:00', '00:05:00'),

-- Jeudi (4)
(1, 4, '08:00:00', '12:00:00', '00:05:00'),
(1, 4, '13:00:00', '17:00:00', '00:05:00'),

-- Vendredi (5)
(1, 5, '08:00:00', '12:00:00', '00:05:00'),
(1, 5, '13:00:00', '17:00:00', '00:05:00'),

-- Samedi (6)
(1, 6, '08:00:00', '12:00:00', '00:05:00'),
(1, 6, '13:00:00', '17:00:00', '00:05:00');

INSERT INTO pointage (id_employe, connexion, deconnexion) VALUES
(1, '2025-11-18 08:05:00', '2025-11-18 12:00:00'),
(1, '2025-11-18 13:00:00', '2025-11-18 16:45:00'),

(1, '2025-11-19 08:10:00', '2025-11-19 12:10:00'),
(1, '2025-11-19 13:15:00', '2025-11-19 17:00:00'),

(1, '2025-11-20 08:00:00', '2025-11-20 12:00:00'),
(1, '2025-11-20 14:00:00', '2025-11-20 18:20:00'),

(1, '2025-11-21 08:10:00', '2025-11-21 12:45:00'),
(1, '2025-11-21 13:50:00', '2025-11-21 17:10:00'),

(1, '2025-11-22 08:05:00', '2025-11-22 12:30:00'),
(1, '2025-11-22 13:35:00', '2025-11-22 16:55:00'),

(1, '2025-11-25 08:00:00', '2025-11-25 12:00:00'),
(1, '2025-11-25 13:30:00', '2025-11-25 17:00:00'),

(1, '2025-11-26 08:20:00', '2025-11-26 12:10:00'),
(1, '2025-11-26 14:00:00', '2025-11-26 18:00:00'),

(1, '2025-11-27 08:00:00', '2025-11-27 12:00:00'),
(1, '2025-11-27 13:00:00', '2025-11-27 17:15:00'),

(1, '2025-11-28 08:15:00', '2025-11-28 12:20:00'),
(1, '2025-11-28 13:20:00', '2025-11-28 17:30:00'),

(1, '2025-11-29 03:00:00', '2025-11-29 05:00:00'), -- ton fameux test ^^

(1, '2025-11-30 08:00:00', '2025-11-30 12:00:00'),
(1, '2025-11-30 13:00:00', '2025-11-30 16:45:00');


-- =====================================================
-- ÉVALUATIONS DE PERFORMANCE
-- =====================================================

INSERT INTO employe_evaluation_periodes (nom, frequence_mois) VALUES
('Mensuel',1),
('Trimestriel',3),
('Annuel',12);


INSERT INTO employe_evaluations 
(employe_id, periode_id, date_generation, date_evaluation, statut, score_total, manager_id, created_at, updated_at) VALUES
(1, 1, '2025-11-30', '2025-11-01', 'TERMINEE', 54.00, 3, '2025-11-30 07:33:14.857214', '2025-11-30 16:03:57.081917'),
(2, 1, '2025-11-30', '2025-11-01', 'TERMINEE', 55.00, 3, '2025-11-30 07:33:14.857214', '2025-11-30 16:03:57.081917'),
(3, 2, '2025-11-30', '2025-11-10', 'PREVUE', 0.00, 1, '2025-11-30 07:33:14.857214', '2025-11-30 16:11:38.723333'),
(4, 2, '2025-11-30', '2025-11-12', 'PREVUE', 0.00, 1, '2025-11-30 07:33:14.857214', '2025-11-30 16:11:38.723333');

INSERT INTO employe_evaluations_details (evaluation_id, critere_id, note, commentaire) VALUES
(1, 1, 8.5, 'Code propre et bien documenté'),
(1, 2, 9.0, 'Toujours dans les temps'),
(1, 3, 7.5, NULL),
(1, 4, 9.5, 'Très bonne ambiance'),
(1, 5, 8.0, 'Propose régulièrement des améliorations'),
(2, 1, 7.5, 'Travail correct, quelques oublis'),
(2, 2, 8.0, 'Respect des délais satisfaisant'),
(2, 3, 7.0, NULL),
(2, 4, 8.5, 'Bonne collaboration'),
(2, 5, 7.5, 'Suggestions pertinentes');



INSERT INTO manager_admins (id_manager, id_admin)
VALUES
(1, 1),
(2, 2),
(3, 3);
INSERT INTO employe_score_trends (employe_id, mois, annee, score) VALUES
-- Employé 1
(1, 8, 2025, 78.0),
(1, 9, 2025, 80.0),
(1, 10, 2025, 80.0),
(1, 11, 2025, 85.0),
-- Employé 2
(2, 8, 2025, 68.0),
(2, 9, 2025, 70.0),
(2, 10, 2025, 70.0),
(2, 11, 2025, 75.0),
-- Employé 3
(3, 8, 2025, 58.0),
(3, 9, 2025, 60.0),
(3, 10, 2025, 60.0),
(3, 11, 2025, 65.0),
-- Employé 4
(4, 8, 2025, 72.0),
(4, 9, 2025, 75.0),
(4, 10, 2025, 78.0),
(4, 11, 2025, 79.0); 

INSERT INTO employe_evaluation_calendrier (employe_id, periode_id, date_prevue, date_limite) VALUES
-- ===================== Évaluations Mensuelles (id_periode = 1) =====================
-- Employé 1 : chaque mois de sept à novembre
(1, 1, '2025-07-01', '2025-07-05'),
(1, 1, '2025-08-01', '2025-08-05'),
(1, 1, '2025-09-01', '2025-09-05'),
(1, 1, '2025-10-01', '2025-10-05'),
(1, 1, '2025-11-01', '2025-11-05'),

-- Employé 2
(2, 1, '2025-07-01', '2025-07-05'),
(2, 1, '2025-08-01', '2025-08-05'),
(2, 1, '2025-09-01', '2025-09-05'),
(2, 1, '2025-10-01', '2025-10-05'),
(2, 1, '2025-11-01', '2025-11-05'),

-- Employé 3
(3, 1, '2025-07-01', '2025-07-05'),
(3, 1, '2025-08-01', '2025-08-05'),
(3, 1, '2025-09-01', '2025-09-05'),
(3, 1, '2025-10-01', '2025-10-05'),
(3, 1, '2025-11-01', '2025-11-05'),

-- Employé 4
(4, 1, '2025-07-01', '2025-07-05'),
(4, 1, '2025-08-01', '2025-08-05'),
(4, 1, '2025-09-01', '2025-09-05'),
(4, 1, '2025-10-01', '2025-10-05'),
(4, 1, '2025-11-01', '2025-11-05'),



-- ===================== Évaluations Trimestrielles (id_periode = 2) =====================
-- Employé 1
(1, 2, '2025-01-15', '2025-01-20'),
(1, 2, '2025-04-15', '2025-04-20'),
(1, 2, '2025-07-15', '2025-07-20'),
(1, 2, '2025-10-15', '2025-10-20'),

-- Employé 2
(2, 2, '2025-01-15', '2025-01-20'),
(2, 2, '2025-04-15', '2025-04-20'),
(2, 2, '2025-07-15', '2025-07-20'),
(2, 2, '2025-10-15', '2025-10-20'),

-- Employé 3
(3, 2, '2025-01-15', '2025-01-20'),
(3, 2, '2025-04-15', '2025-04-20'),
(3, 2, '2025-07-15', '2025-07-20'),
(3, 2, '2025-10-15', '2025-10-20'),

-- Employé 4
(4, 2, '2025-01-15', '2025-01-20'),
(4, 2, '2025-04-15', '2025-04-20'),
(4, 2, '2025-07-15', '2025-07-20'),
(4, 2, '2025-10-15', '2025-10-20'),



-- ===================== Évaluations Annuelles (id_periode = 3) =====================
-- Employé 1
(1, 3, '2025-12-01', '2025-12-10'),

-- Employé 2
(2, 3, '2025-12-01', '2025-12-10'),

-- Employé 3
(3, 3, '2025-12-01', '2025-12-10'),

-- Employé 4
(4, 3, '2025-12-01', '2025-12-10');


CREATE OR REPLACE VIEW vue_employe_performances_par_critere AS
SELECT 
    e.employe_id,
    c.nom AS critere,
    AVG(ed.note * c.poids / 10) AS score_pondere,
    EXTRACT(YEAR FROM e.date_evaluation) AS annee
FROM employe_evaluations e
JOIN employe_evaluations_details ed ON ed.evaluation_id = e.id_evaluation
JOIN employe_criteres_evaluation c ON c.id_critere = ed.critere_id
WHERE e.statut = 'TERMINEE'
GROUP BY e.employe_id, c.nom, annee;





INSERT INTO notifications (id_personne, message, date_notification) VALUES
(1, 'Nouvelle politique de congé publiée', '2024-06-01 08:00:00'),
(3, 'Formation obligatoire', '2024-05-12 12:00:00');

INSERT INTO heure_supplementaire_config (date_creation, nombre_premieres_heures) VALUES
('2024-01-01 00:00:00', 12);

INSERT INTO salaire_historique (salaire, date_creation, id_employe) VALUES
(5200000, '2024-06-01 09:00:00', 1),
(2100000, '2024-07-01 09:00:00', 2);


-- Absences (après employes)
INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
(2, '2024-03-05 08:00:00', '2024-03-05 17:00:00', FALSE, 'Absence non justifiee'),
(4, '2024-03-10 08:00:00', '2024-03-10 12:00:00', TRUE, 'Rendez-vous medical'),
(5, '2024-03-15 08:00:00', '2024-03-15 17:00:00', FALSE, 'Retard non justifie'),
(3, '2024-03-20 13:00:00', '2024-03-20 17:00:00', TRUE, 'Demarches administratives'),
(1, '2024-03-25 08:00:00', '2024-03-25 10:00:00', FALSE, 'Absence courte non autorisee');

-- Congés (après employes et conge_type)
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Conge annuel famille', 1, '2024-02-15', '2024-04-01', '2024-04-15', 2, 1),
('Conge maladie', 2, '2024-02-20', '2024-03-10', '2024-03-12', 2, 2),
('Conge exceptionnel mariage', 3, '2024-02-25', '2024-05-01', '2024-05-03', 1, 3),
('Conge sans solde projet perso', 4, '2024-03-01', '2024-06-01', '2024-06-07', 2, 4),
('Conge maternite', 2, '2024-03-05', '2024-07-01', '2024-09-28', 2, 5);

INSERT INTO abscence_conge_suivi (id_demande, id_abscence, id_type, id_employe, nombre_conge, annee, penalite_appliquee, id_type_penalite, dateMouvement) VALUES
(NULL, 5, NULL, 1, 0, 2024, TRUE, 1, '2024-03-25 10:00:00'),
(1, NULL, 1, 1, 15, 2024, FALSE, NULL, '2024-02-16'),
(NULL, 1, NULL, 2, 0, 2024, TRUE, 2, '2024-03-06'),
(2, NULL, 2, 2, 3, 2024, FALSE, NULL, '2024-02-21'),
(NULL, 3, NULL, 4, 0, 2024, TRUE, 1, '2024-03-16'),
(3, NULL, 3, 3, 3, 2024, FALSE, NULL, '2024-02-26'); 
-- FIN DES DONNÉES

SELECT 'Données insérées avec succès!' AS message;

