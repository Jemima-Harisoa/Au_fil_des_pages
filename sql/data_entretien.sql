INSERT INTO departements (nom) VALUES
('Vente'),
('Stock'),
('Comptabilité'),
('Direction');

INSERT INTO etat(nom)VALUES ('en attente'),
('en cours'),
('reporte'),
('rejete'),
('termine');

INSERT INTO annonces (titre, lien, date_publication, date_expiration, nombre_poste) VALUES
('vendeur_2025.pdf', '/files/annonces/vendeur_2025.pdf', '2025-09-01', NULL, 2),
('caissier_2025.pdf', '/files/annonces/caissier_2025.pdf', '2025-09-03', NULL, 1),
('magasinier_2025.pdf', '/files/annonces/magasinier_2025.pdf', '2025-09-05', NULL, 1),
('comptable_2025.pdf', '/files/annonces/comptable_2025.pdf', '2025-09-07', NULL, 1),
('gerant_2025.pdf', '/files/annonces/gerant_2025.pdf', '2025-09-10', NULL, 1);

INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image, mdp, email) VALUES
('Ratsimba', 'Aina', '1985-03-11', '0321117788', '/images/aina.jpg', 'emp123', 'aina@mail.com'),
('Andrianina', 'Volana', '1990-06-23', '0332228899', '/images/volana.jpg', 'emp456', 'volana@mail.com'),
('Randriamahefa', 'Koto', '1987-12-05', '0343339900', '/images/koto.jpg', 'emp789', 'koto@mail.com'),
('Ravony', 'Elie', '1992-08-14', '0324442211', '/images/elie.jpg', 'emp321', 'elie@mail.com'),
('Rasamy', 'Noro', '1989-02-28', '0335551122', '/images/noro.jpg', 'emp654', 'noro@mail.com'),
('Rakotobe', 'Fanilo', '1991-09-07', '0346662233', '/images/fanilo.jpg', 'emp987', 'fanilo@mail.com'),
('Rahantanirina', 'Mamy', '1986-11-19', '0327773344', '/images/mamy2.jpg', 'emp159', 'mamy2@mail.com'),
('Rasoamanana', 'Tovo', '1993-05-30', '0338884455', '/images/tovo.jpg', 'emp753', 'tovo@mail.com'),
('Rasolo', 'Fanja', '1998-05-12', '0321112233', '/images/fanja.jpg', 'mdp123', 'fanja@mail.com'),
('Rakoto', 'Hery', '1995-03-20', '0334445566', '/images/hery.jpg', 'mdp456', 'hery@mail.com'),
('Randria', 'Miora', '2000-07-15', '0347778899', '/images/miora.jpg', 'mdp789', 'miora@mail.com'),
('Razan', 'Toky', '1992-11-02', '0329998877', '/images/toky.jpg', 'mdp321', 'toky@mail.com'),
('Rabe', 'Lalao', '1988-01-25', '0331234567', '/images/lalao.jpg', 'mdp654', 'lalao@mail.com'),
('Andry', 'Tiana', '1996-04-18', '0342223344', '/images/tiana.jpg', 'mdp987', 'tiana@mail.com'),
('Rakot', 'Mamy', '1993-09-05', '0328881122', '/images/mamy.jpg', 'mdp159', 'mamy@mail.com'),
('Rabear', 'Nirina', '1997-02-14', '0331114455', '/images/nirina.jpg', 'mdp753', 'nirina@mail.com'),
('Andri', 'Soa', '1994-08-30', '0345556677', '/images/soa.jpg', 'mdp852', 'soa@mail.com'),
('Ravelo', 'Jean', '1991-06-21', '0322233445', '/images/jean.jpg', 'mdp951', 'jean@mail.com'),
('Rakotond', 'Sarah', '1999-03-10', '0336667788', '/images/sarah.jpg', 'mdp357', 'sarah@mail.com'),
('Rahari', 'David', '1987-09-25', '0341122334', '/images/david.jpg', 'mdp159', 'david@mail.com'),
('Rafeno', 'Clara', '1990-12-01', '0327766554', '/images/clara.jpg', 'mdp456', 'clara@mail.com'),
('Raso', 'Jonah', '1998-11-13', '0334455667', '/images/jonah.jpg', 'mdp852', 'jonah@mail.com'),
('Andrian', 'Kelly', '1995-05-07', '0348899001', '/images/kelly.jpg', 'mdp654', 'kelly@mail.com'),
('Razanaka', 'Michel', '1989-07-18', '0323344556', '/images/michel.jpg', 'mdp321', 'michel@mail.com'),
('Rabeja', 'Anita', '1996-10-22', '0339988776', '/images/anita.jpg', 'mdp951', 'anita@mail.com'),
('Rakotona', 'Luc', '1993-01-19', '0345566778', '/images/luc.jpg', 'mdp753', 'luc@mail.com'),
('Ravony', 'Elsa', '1997-04-02', '0326677889', '/images/elsa.jpg', 'mdp159', 'elsa@mail.com'),
('Raher', 'Patrick', '1992-08-16', '0338877665', '/images/patrick.jpg', 'mdp357', 'patrick@mail.com');


INSERT INTO contrats (id_candidat, type_contrat, url_contrat) VALUES
(1, 'CDI', '/contrats/aina.pdf'),
(2, 'CDI', '/contrats/volana.pdf'),
(3, 'CDI', '/contrats/koto.pdf'),
(4, 'CDD 12 mois', '/contrats/elie.pdf'),
(5, 'CDI', '/contrats/noro.pdf'),
(6, 'CDD 6 mois', '/contrats/fanilo.pdf'),
(7, 'CDI', '/contrats/mamy2.pdf'),
(8, 'CDD 12 mois', '/contrats/tovo.pdf');

INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche) VALUES
(1, 1, 1, 'Vendeur confirmé', '2019-04-15'),
(2, 2, 1, 'Caissière principale', '2020-07-01'),
(3, 3, 2, 'Magasinier principal', '2018-09-20'),
(4, 4, 2, 'Assistant magasinier', '2021-01-10'),
(5, 5, 3, 'Comptable senior', '2019-11-25'),
(6, 6, 3, 'Assistant comptable', '2022-03-14'),
(7, 7, 4, 'Adjoint du gérant', '2020-06-05'),
(8, 8, 4, 'Responsable RH', '2021-09-18');


INSERT INTO candidats (id_personne, id_annonce, cv_url, poste) VALUES
(1, NULL, '/cv/aina.pdf', 'Vendeur confirmé'),
(2, NULL, '/cv/volana.pdf', 'Caissière principale'),
(3, NULL, '/cv/koto.pdf', 'Magasinier principal'),
(4, NULL, '/cv/elie.pdf', 'Assistant magasinier'),
(5, NULL, '/cv/noro.pdf', 'Comptable senior'),
(6, NULL, '/cv/fanilo.pdf', 'Assistant comptable'),
(7, NULL, '/cv/mamy2.pdf', 'Adjoint du gérant'),
(8, NULL, '/cv/tovo.pdf', 'Responsable RH')
(9, 1, '/cv/fanja.pdf', 'Vendeur'),
(10, 1, '/cv/hery.pdf', 'Vendeur'),
(11, 1, '/cv/miora.pdf', 'Vendeur'),
(16, 1, '/cv/nirina.pdf', 'Vendeur'),
(17, 1, '/cv/soa.pdf', 'Vendeur'),
(18, 1, '/cv/jean.pdf', 'Vendeur'),

(13, 2, '/cv/toky.pdf', 'Caissier'),
(19, 2, '/cv/sarah.pdf', 'Caissier'),
(21, 2, '/cv/david.pdf', 'Caissier'),
(22, 2, '/cv/clara.pdf', 'Caissier'),

(13, 3, '/cv/lalao.pdf', 'Magasinier'),
(15, 3, '/cv/mamy.pdf', 'Magasinier'),
(22, 3, '/cv/jonah.pdf', 'Magasinier'),
(23, 3, '/cv/kelly.pdf', 'Magasinier'),

(14, 4, '/cv/tiana.pdf', 'Comptable'),
(24, 4, '/cv/michel.pdf', 'Comptable'),
(25, 4, '/cv/anita.pdf', 'Comptable'),

(26, 5, '/cv/luc.pdf', 'Gérant'),
(27, 5, '/cv/elsa.pdf', 'Gérant'),
(28, 5, '/cv/patrick.pdf', 'Gérant');


INSERT INTO tests (id_candidat, score_test, date_test) VALUES
(1, 78.5, '2025-09-12'),
(2, 65.0, '2025-09-12'),
(3, 82.0, '2025-09-13'),
(4, 74.5, '2025-09-13'),
(5, 69.0, '2025-09-14'),
(6, 88.2, '2025-09-14'),

(7, 80.0, '2025-09-14'),
(8, 60.5, '2025-09-15'),
(9, 77.0, '2025-09-15'),
(10, 72.3, '2025-09-15'),

(11, 91.7, '2025-09-16'),
(12, 70.2, '2025-09-16'),
(13, 68.5, '2025-09-16'),
(14, 84.0, '2025-09-17'),

(15, 89.1, '2025-09-17'),
(16, 93.0, '2025-09-17'),
(17, 76.4, '2025-09-17'),

(18, 90.0, '2025-09-18'),
(19, 86.5, '2025-09-18'),
(20, 79.0, '2025-09-18');






INSERT INTO disponibilite_employe (id_employe, heure_debut, heure_fin, jour,est_valide) VALUES
-- Employé 1 (Vendeur confirmé)
(1, '09:00:00', '11:00:00', 'Lundi'),
(1, '14:00:00', '16:00:00', 'Mercredi'),

-- Employé 2 (Caissière principale)
(2, '10:00:00', '12:00:00', 'Mardi'),
(2, '15:00:00', '17:00:00', 'Jeudi'),

-- Employé 3 (Magasinier principal)
(3, '08:00:00', '10:00:00', 'Lundi'),
(3, '13:00:00', '15:00:00', 'Vendredi'),

-- Employé 4 (Assistant magasinier)
(4, '09:30:00', '11:30:00', 'Mardi'),
(4, '14:00:00', '16:00:00', 'Jeudi'),

-- Employé 5 (Comptable senior)
(5, '08:30:00', '10:30:00', 'Lundi'),
(5, '11:00:00', '13:00:00', 'Mercredi'),

-- Employé 6 (Assistant comptable)
(6, '09:00:00', '11:00:00', 'Mardi'),
(6, '14:00:00', '16:00:00', 'Jeudi'),

-- Employé 7 (Adjoint du gérant)
(7, '10:00:00', '12:00:00', 'Lundi'),
(7, '13:00:00', '15:00:00', 'Mercredi'),

-- Employé 8 (Responsable RH)
(8, '09:00:00', '11:00:00', 'Mardi'),
(8, '14:00:00', '16:00:00', 'Vendredi');


-- INSERT INTO jour_ferie ("date") VALUES
-- ('2025-01-01'),  -- Jour de l'An
-- ('2025-03-29'),  -- Fête nationale (Indépendance)
-- ('2025-05-01'),  -- Fête du Travail
-- ('2025-06-26'),  -- Fête nationale
-- ('2025-08-15'),  -- Assomption
-- ('2025-11-01'),  -- Toussaint
-- ('2025-12-25');  -- Noël