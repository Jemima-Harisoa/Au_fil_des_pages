-- =========================
-- Données de référence complétées
-- =========================

INSERT INTO departements (id_departement, nom) VALUES 
(1, 'Ressources Humaines'),
(2, 'Informatique'),
(3, 'Opérations'),
(4, 'Finance'),
(5, 'Marketing'),
(6, 'Commercial');

INSERT INTO type_contrats (id_type_contrat, nom) VALUES
(1, 'CDI'),
(2, 'CDD'),
(3, 'Stage'),
(4, 'Freelance');

INSERT INTO conge_type (id_type, nom, description) VALUES
(1, 'Congé annuel', 'Congé payé annuel'),
(2, 'Congé maladie', 'Congé pour raison de santé, justificatif requis'),
(3, 'Congé maternité', 'Congé maternité selon la réglementation'),
(4, 'Congé sans solde', 'Congé non payé'),
(5, 'Congé paternité', 'Congé paternité pour les employés'),
(6, 'Congé formation', 'Congé pour formation professionnelle');

INSERT INTO abscence_type_penalite (id_type_penalite, nom, description, montant) VALUES
(1, 'Retard', 'Pénalité pour retard répété', 50000.00),
(2, 'Absence non justifiée', 'Retenue salariale pour absence sans justificatif', 200000.00),
(3, 'Dépassement congé', 'Retenue pour dépassement de jours de congé accordés', 150000.00),
(4, 'Non respect horaires', 'Pénalité pour non-respect des horaires de travail', 75000.00);

INSERT INTO personnes (id_personne, nom, prenom, date_naissance, contact, lien_image) VALUES
(1, 'Razafindrakoto', 'Andry', '1985-04-12', '+261341234567', NULL),
(2, 'Rasoamanana', 'Mialy', '1992-07-01', '+261332345678', NULL),
(3, 'Rakoto', 'Hery', '1990-11-20', '+261339876543', NULL),
(4, 'Randriatsara', 'Fanja', '2003-05-10', '+261344556677', NULL),
(5, 'Razanamparany', 'Lova', '1991-02-28', '+261329998877', NULL),
(6, 'Rajaonarivelo', 'Thierry', '1988-09-09', '+261334443322', NULL),
(7, 'Andriamasy', 'Nirina', '1995-01-18', '+261330112233', NULL),
(8, 'Rakotomanga', 'Tiana', '1993-06-25', '+261339992211', NULL),
(9, 'Randriamanantsoa', 'Faly', '1989-12-05', '+261334455667', NULL),
(10, 'Ravalomanana', 'Miora', '1996-08-14', '+261331122334', NULL);

INSERT INTO contrats (id_contrat, id_candidat, id_type_contrat, url_contrat) VALUES
(1, NULL, 1, '/contracts/1.pdf'),
(2, NULL, 2, '/contracts/2.pdf'),
(3, NULL, 1, '/contracts/3.pdf'),
(4, NULL, 3, '/contracts/4.pdf'),
(5, NULL, 4, '/contracts/5.pdf'),
(6, NULL, 1, '/contracts/6.pdf');

INSERT INTO employes (id_employe, id_personne, id_contrat, id_departement, poste, date_embauche, nombre_conge, salaire_base) VALUES
(1, 1, 1, 1, 'HR Manager', '2018-03-01', 24, 1200000.00),
(2, 2, 2, 2, 'Développeuse', '2020-06-15', 18, 900000.00),
(3, 3, 3, 3, 'Opérateur', '2019-01-05', 20, 800000.00),
(4, 4, 4, 2, 'Stagiaire', '2025-09-01', 0, 200000.00),
(5, 5, 1, 3, 'Analyste', '2022-02-01', 18, 700000.00),
(6, 6, 1, 2, 'Technicien', '2017-11-10', 12, 600000.00),
(7, 7, 5, 4, 'Comptable', '2019-05-20', 20, 750000.00),
(8, 8, 6, 5, 'Marketing Specialist', '2021-07-11', 22, 950000.00),
(9, 9, 2, 6, 'Commercial Senior', '2018-09-30', 25, 1100000.00),
(10, 10, 3, 2, 'Développeuse Junior', '2023-01-05', 15, 650000.00);

INSERT INTO conge_demande (id_demande, description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
(1, 'Congé annuel fin d''année (Noël)', 2, '2025-11-01 09:00:00', '2025-12-20 00:00:00', '2025-12-31 23:59:59', 2, 1),
(2, 'Congé maladie suite à infection', 3, '2025-10-10 08:15:00', '2025-10-10 08:00:00', '2025-10-12 17:00:00', 2, 2),
(3, 'Congé annuel demandé en période d''essai', 4, '2025-09-15 09:30:00', '2025-10-01 00:00:00', '2025-10-05 23:59:59', 0, 1),
(4, 'Absence non justifiée enregistrée', 6, '2025-08-07 09:00:00', '2025-08-05 08:00:00', '2025-08-06 17:00:00', 0, 2),
(5, 'Congé long dépassant le solde', 6, '2025-07-01 10:00:00', '2025-07-10 00:00:00', '2025-07-24 23:59:59', 2, 1),
(6, 'Congé maternité', 5, '2025-02-15 09:00:00', '2025-03-01 00:00:00', '2025-08-01 23:59:59', 2, 3),
(7, 'Congé planifié, en attente de validation', 2, '2025-11-10 08:30:00', '2026-01-15 00:00:00', '2026-01-20 23:59:59', 1, 1),
(8, 'Absence justifiée (rendez-vous médical)', 3, '2025-05-12 09:10:00', '2025-05-12 09:00:00', '2025-05-12 17:00:00', 2, 2),
(9, 'Demande annulée par l''employé', 2, '2025-06-20 08:00:00', '2025-07-01 00:00:00', '2025-07-05 23:59:59', 0, 1),
(10, 'Demande de congé matin approuvée puis absence PM non justifiée', 3, '2025-09-01 09:00:00', '2025-09-03 08:00:00', '2025-09-03 12:00:00', 2, 1),
(11, 'Absence PM non justifiée', 3, '2025-09-04 09:00:00', '2025-09-03 13:00:00', '2025-09-03 17:00:00', 0, 2),
(12, 'Formation Python avancée', 5, '2025-04-01 09:00:00', '2025-04-10 08:00:00', '2025-04-15 17:00:00', 2, 6),
(13, 'Congé paternité', 9, '2025-06-01 08:00:00', '2025-06-05 00:00:00', '2025-06-10 23:59:59', 2, 5);

INSERT INTO conge_historique_validation (id_historique_validation, id_demande, id_employe, date_validation, id_etat) VALUES
(1, 1, 1, '2025-11-02 10:15:00', NULL),
(2, 2, 1, '2025-10-10 12:00:00', NULL),
(3, 3, 1, '2025-09-16 11:00:00', NULL),
(4, 6, 1, '2025-02-16 14:00:00', NULL),
(5, 12, 1, '2025-04-05 09:00:00', NULL),
(6, 13, 1, '2025-06-03 11:00:00', NULL);

INSERT INTO abscence (id_abscence, debut, fin, est_autorise, justificatif) VALUES
(1, '2025-10-10 08:00:00', '2025-10-12 17:00:00', TRUE, 'Certificat médical fourni'),
(2, '2025-08-05 08:00:00', '2025-08-06 17:00:00', FALSE, NULL),
(3, '2025-07-10 00:00:00', '2025-07-24 23:59:59', TRUE, 'Demande approuvée mais dépassement constaté'),
(4, '2025-03-01 00:00:00', '2025-08-01 23:59:59', TRUE, 'Attestation maternité'),
(5, '2025-05-12 09:00:00', '2025-05-12 17:00:00', TRUE, 'Attestation rendez-vous médical'),
(6, '2025-09-03 13:00:00', '2025-09-03 17:00:00', FALSE, NULL),
(7, '2025-04-10 08:00:00', '2025-04-15 17:00:00', TRUE, 'Formation Python avancée'),
(8, '2025-06-05 00:00:00', '2025-06-10 23:59:59', TRUE, 'Congé paternité');

INSERT INTO abscence_conge_suivi (id_suivi, id_demande, id_abscence, id_type, id_employe, nombre_conge, annee, penalite_appliquee, id_type_penalite) VALUES
(1, 2, 1, 2, 3, 3, 2025, FALSE, NULL),
(2, 4, 2, 2, 6, 2, 2025, TRUE, 2),
(3, 5, 3, 1, 6, 15, 2025, TRUE, 3),
(4, 6, 4, 3, 5, 153, 2025, FALSE, NULL),
(5, 8, 5, 2, 3, 1, 2025, FALSE, NULL),
(6, 11, 6, 2, 3, 0, 2025, TRUE, 2),
(7, 12, 7, 6, 5, 5, 2025, FALSE, NULL),
(8, 13, 8, 5, 9, 5, 2025, FALSE, NULL);

INSERT INTO conge_historique (id_conge_historique, nombres_abscence_attribue, id_employe) VALUES
(1, 2, 6),
(2, 3, 6),
(3, 0.5, 3),
(4, 5, 5),
(5, 5, 9);
