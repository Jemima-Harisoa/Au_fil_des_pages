-- ================== DONNÉES DE TEST CORRIGÉES ET COMPLÉTÉES ==================

-- 1. Insertion des départements
INSERT INTO departements (nom) VALUES 
('Ressources Humaines'),
('Développement'),
('Commercial'),
('Finance'),
('Marketing');

-- 2. Insertion des personnes
INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) VALUES 
('Dupont', 'Jean', '1985-03-15', 'jean.dupont@email.com', '/images/jean.jpg'),
('Martin', 'Marie', '1990-07-22', 'marie.martin@email.com', '/images/marie.jpg'),
('Bernard', 'Pierre', '1988-11-30', 'pierre.bernard@email.com', '/images/pierre.jpg'),
('Moreau', 'Sophie', '1992-05-18', 'sophie.moreau@email.com', '/images/sophie.jpg'),
('Leroy', 'Thomas', '1987-09-14', 'thomas.leroy@email.com', '/images/thomas.jpg'),
('Petit', 'Laura', '1993-12-03', 'laura.petit@email.com', '/images/laura.jpg');

-- 3. Insertion des types de congé (table manquante)
INSERT INTO conge_type (nom, description) VALUES 
('Congé payé', 'Congé annuel rémunéré'),
('Maladie', 'Arrêt maladie avec certificat médical'),
('Maternité', 'Congé maternité'),
('Paternité', 'Congé paternité'),
('Formation', 'Congé pour formation professionnelle'),
('Familial', 'Congé pour obligations familiales'),
('Sans solde', 'Congé sans rémunération');

-- 4. Insertion des employés (sans référence à contrats)
INSERT INTO employes (id_personne, id_departement, poste, date_embauche, nombre_conge, salaire_base) VALUES 
(1, 1, 'Responsable RH', '2020-01-15', 25, 3000.00),
(2, 2, 'Développeur Senior', '2021-03-20', 22, 2800.00),
(3, 3, 'Commercial', '2022-06-10', 20, 2500.00),
(4, 4, 'Comptable', '2023-01-08', 18, 2300.00),
(5, 2, 'Développeur Frontend', '2021-08-15', 22, 2600.00),
(6, 1, 'Assistant RH', '2023-03-01', 18, 2100.00);

-- 5. Insertion des demandes de congé sur différentes années
INSERT INTO conge_demande (description, id_employe, niveau_validation) VALUES 
-- 2024
('Vacances annuelles été 2024', 1, 2),
('Formation développement web 2024', 2, 2),
('Congé parental 2024', 4, 3),
-- 2023
('Vacances Noël 2023', 3, 2),
('Maladie longue durée 2023', 5, 1),
-- 2022
('Congé sabbatique 2022', 1, 2),
('Formation management 2022', 5, 2),
('Maternité 2022', 4, 3),
-- 2021
('Congé sans solde projet personnel 2021', 2, 1),
('Vacances été 2021', 3, 2);

-- 6. Insertion dans l'historique de validation des congés
INSERT INTO conge_historique_validation (id_demande, id_employe, date_validation) VALUES 
(1, 1, '2024-06-15 09:30:00'),
(2, 2, '2024-02-20 14:15:00'),
(3, 4, '2024-03-10 10:00:00'),
(4, 3, '2023-12-01 11:20:00'),
(5, 5, '2023-08-15 16:45:00'),
(6, 1, '2022-05-10 08:30:00'),
(7, 5, '2022-09-22 13:15:00'),
(8, 4, '2022-01-15 10:30:00'),
(9, 2, '2021-11-05 14:00:00'),
(10, 3, '2021-07-12 09:45:00');

-- 7. Insertion dans l'historique des congés par année
INSERT INTO conge_historique (nombres_abscence_attribue, id_employe) VALUES 
-- 2024
(15.5, 1),
(8.0, 2),
(12.0, 3),
(5.5, 4),
(10.0, 5),
(7.5, 5),
-- 2023
(18.0, 1),
(12.5, 2),
(9.0, 3),
(14.0, 4),
(6.5, 5),
(11.0, 5),
-- 2022
(20.0, 1),
(15.0, 2),
(8.5, 3),
(22.0, 4),
(13.0, 5),
(9.5, 6);

-- 8. Insertion des absences sur différentes années
INSERT INTO abscence (debut, fin, est_autorise, justificatif) VALUES 
-- 2024
('2024-01-10 08:00:00', '2024-01-12 17:00:00', true, 'Certificat médical - grippe'),
('2024-02-05 08:00:00', '2024-02-05 17:00:00', false, 'Absence non justifiée'),
('2024-03-15 08:00:00', '2024-03-20 17:00:00', true, 'Formation professionnelle Agile'),
-- 2023
('2023-05-20 08:00:00', '2023-05-25 17:00:00', true, 'Congé maladie - opération'),
('2023-08-10 08:00:00', '2023-08-11 17:00:00', true, 'Problèmes familiaux'),
('2023-11-15 08:00:00', '2023-11-15 17:00:00', false, 'Retard non justifié'),
-- 2022
('2022-04-05 08:00:00', '2022-04-08 17:00:00', true, 'Formation sécurité informatique'),
('2022-07-18 08:00:00', '2022-07-19 17:00:00', true, 'Rendez-vous médical spécialiste'),
('2022-09-22 08:00:00', '2022-09-22 17:00:00', false, 'Absence non autorisée'),
('2022-12-20 08:00:00', '2022-12-21 17:00:00', true, 'Problèmes de transport');

-- 9. Insertion dans le suivi absence/congé avec différentes années
INSERT INTO abscence_conge_suivi (id_demande, id_type, id_employe, nombre_conge, annee) VALUES 
-- 2024
(1, 1, 1, 10, 2024),
(2, 5, 2, 3, 2024),
(3, 3, 4, 15, 2024),
-- 2023
(4, 1, 3, 8, 2023),
(5, 2, 5, 5, 2023),
-- 2022
(6, 7, 1, 20, 2022),
(7, 5, 6, 10, 2022),
(8, 3, 4, 90, 2022),
-- 2021
(9, 7, 2, 15, 2021),
(10, 1, 3, 12, 2021);