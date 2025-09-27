INSERT INTO diplomes (nom, niveau) VALUES
('Bac', 0),
('Bac+2', 2),
('Bac+3', 3),
('BTS Commerce', 2),
('DUT GEA', 2),
('Licence Management', 3),
('Master Management', 5),
('CAP Logistique', 0),
('BEP Logistique', 0);

INSERT INTO filieres (nom) VALUES
('Commerce'),
('Vente'),
('Gestion/Management'),
('Comptabilite/Finance'),
('Logistique'),
('Litterature/Sciences humaines'),
('Informatique'),
('Marketing'),
('Administration');

INSERT INTO departements (nom) VALUES
('Vente'),
('Stock'),
('Comptabilite'),
('RH'),
('Direction');

INSERT INTO type_contrats (nom) VALUES
('CDI'),
('CDD'),
('Mi-temps'),
('Stage');


INSERT INTO profils 
(titre, competences, skills, loisirs, id_diplome, id_filiere, experience_pro, certifications, langues, id_type_contrat, id_departement, est_minimum)
VALUES
('Vendeur', 
 'Relation client, conseil, presentation des produits', 
 'Communication, patience, ecoute, dynamisme', 
 'Lecture, activites sociales', 
 1, 2, 'Debutant accepte, experience relation client souhaitee', '', 'Français', 1, 1, TRUE),

('Caissier', 
 'Encaissement, gestion caisse, rapidite', 
 'Rigueur, honnêtete, gestion du stress', 
 'Jeux de logique, precision', 
 1, 1, '1 an en caisse ou grande surface', '', 'Français', 1, 1, TRUE),

('Magasinier', 
 'Gestion du stock, inventaire, manutention', 
 'Organisation, fiabilite, resistance physique, esprit d’equipe', 
 'Sport, bricolage', 
 8, 5, '1-2 ans en gestion de stock', '', 'Français', 1, 2, TRUE),

('Comptable', 
 'Gestion financière, facturation, suivi comptable', 
 'Confidentialite, esprit analytique, minutie, gestion des priorites', 
 'Sudoku, jeux strategiques', 
 5, 4, '2-3 ans d’experience cabinet ou PME', '', 'Français', 1, 3, TRUE),

('Gerant', 
 'Supervision, gestion globale, prise de decision', 
 'Leadership, vision strategique, gestion des conflits', 
 'Lecture economie, sport collectif', 
 7, 3, '3-5 ans experience commerce/gestion', '', 'Français', 1, 4, TRUE);

INSERT INTO annonces (id_profil, titre, date_publication, date_expiration, nombre_poste, lien) VALUES
(1, 'Vendeur(se) en librairie', CURRENT_DATE, NULL, 2, 'https://librairie.example.com/annonce/vendeur'),
(2, 'Caissier/Caissière', CURRENT_DATE, NULL, 1, 'https://librairie.example.com/annonce/caissier'),
(3, 'Magasinier', CURRENT_DATE, NULL, 1, 'https://librairie.example.com/annonce/magasinier'),
(4, 'Comptable', CURRENT_DATE, NULL, 1, 'https://librairie.example.com/annonce/comptable'),
(5, 'Gerant', CURRENT_DATE, NULL, 1, 'https://librairie.example.com/annonce/gerant');


-- Employes (exemple)
INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) VALUES
-- Gérant
('Rakoto', 'Jean', '1985-03-15', '0341234567', 'images/jean.jpg'),
-- Comptable admin / RH
('Rasoanaivo', 'Claire', '1990-07-22', '0349876543', 'images/claire.jpg'),
-- Magasinier principal
('Randria', 'Paul', '1995-11-05', '0341928374', 'images/paul.jpg'),
-- Vendeuse
('Ravel', 'Sophie', '1998-02-12', '0345647382', 'images/sophie.jpg'),
-- Caissier
('Rak', 'Lucas', '1997-09-01', '0348765432', 'images/lucas.jpg'),
-- RH distinct
('Andrianarisoa', 'Lina', '1992-04-10', '0345556677', 'images/lina.jpg');

-- Candidats (20 exemples)
INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) VALUES
('Rakotomalala','Alice','1995-02-10','0331234001',''),
('Rasendra','Bob','1994-06-15','0331234002',''),
('Rakotoniaina','Caroline','1996-08-20','0331234003',''),
('Ranaivo','David','1993-12-05','0331234004',''),
('Ranaivomanana','Evelyne','1997-03-22','0331234005',''),
('Andrianarisoa','Fabrice','1995-07-19','0331234006',''),
('Rasamoelina','Gina','1996-01-11','0331234007',''),
('Raharimalala','Hery','1992-09-25','0331234008',''),
('Rafanomezantsoa','Isabelle','1994-04-30','0331234009',''),
('Rabetokotany','Jules','1993-11-12','0331234010',''),
('Rakotomavo','Karen','1995-05-05','0331234011',''),
('Rasoa','Leo','1996-08-18','0331234012',''),
('Rajaonarison','Mireille','1997-02-28','0331234013',''),
('Rakotondrainy','Nicolas','1993-06-09','0331234014',''),
('Rasolofo','Olivia','1994-10-17','0331234015',''),
('Randrianarisoa','Patrick','1995-03-27','0331234016',''),
('Rabe','Quentin','1996-12-03','0331234017',''),
('Rasamison','Rita','1997-07-22','0331234018',''),
('Rakotomaharo','Samuel','1994-01-14','0331234019',''),
('Rasolo','Therese','1993-05-30','0331234020','');

INSERT INTO utilisateurs (nom, mdp) VALUES
('alice', 'hashedpwd_alice'),
('bob', 'hashedpwd_bob'),
('caroline', 'hashedpwd_caroline'),
('david', 'hashedpwd_david'),
('evelyne', 'hashedpwd_evelyne'),
('fabrice', 'hashedpwd_fabrice'),
('gina', 'hashedpwd_gina'),
('hery', 'hashedpwd_hery'),
('isabelle', 'hashedpwd_isabelle'),
('jules', 'hashedpwd_jules'),
('karen', 'hashedpwd_karen'),
('leo', 'hashedpwd_leo'),
('mireille', 'hashedpwd_mireille'),
('nicolas', 'hashedpwd_nicolas'),
('olivia', 'hashedpwd_olivia'),
('patrick', 'hashedpwd_patrick'),
('quentin', 'hashedpwd_quentin'),
('rita', 'hashedpwd_rita'),
('samuel', 'hashedpwd_samuel'),
('therese', 'hashedpwd_therese');


INSERT INTO candidats (id_personne, id_annonce, id_profil, cv_url, poste, id_utilisateur) VALUES
(6, 1, 1, 'https://cv.example.com/alice.pdf','Vendeur', 1),
(7, 1, 1, 'https://cv.example.com/bob.pdf','Vendeur', 2),
(8, 1, 1, 'https://cv.example.com/caroline.pdf','Vendeur', 3),
(9, 1, 1, 'https://cv.example.com/david.pdf','Vendeur', 4),
(10, 2, 2, 'https://cv.example.com/evelyne.pdf','Caissier', 5),
(11, 2, 2, 'https://cv.example.com/fabrice.pdf','Caissier', 6),
(12, 3, 3, 'https://cv.example.com/gina.pdf','Magasinier', 7),
(13, 3, 3, 'https://cv.example.com/hery.pdf','Magasinier', 8),
(14, 4, 4, 'https://cv.example.com/isabelle.pdf','Comptable', 9),
(15, 4, 4, 'https://cv.example.com/jules.pdf','Comptable', 10),
(16, 5, 5, 'https://cv.example.com/karen.pdf','Gerant', 11),
(17, 5, 5, 'https://cv.example.com/leo.pdf','Gerant', 12),
(18, 1, 1, 'https://cv.example.com/mireille.pdf','Vendeur', 13),
(19, 1, 1, 'https://cv.example.com/nicolas.pdf','Vendeur', 14),
(20, 2, 2, 'https://cv.example.com/olivia.pdf','Caissier', 15),
(21, 3, 3, 'https://cv.example.com/patrick.pdf','Magasinier', 16),
(22, 1, 1, 'https://cv.example.com/quentin.pdf','Vendeur', 17),
(23, 1, 1, 'https://cv.example.com/rita.pdf','Vendeur', 18),
(24, 4, 4, 'https://cv.example.com/samuel.pdf','Comptable', 19),
(25, 5, 5, 'https://cv.example.com/therese.pdf','Gerant', 20);


INSERT INTO contrats (id_candidat, id_type_contrat, url_contrat) VALUES
-- Contrats employes existants
(1, 1, 'https://contrats.example.com/contrat_jean.pdf'),    -- Vendeur
(2, 1, 'https://contrats.example.com/contrat_marie.pdf'),   -- Caissier
(3, 1, 'https://contrats.example.com/contrat_luc.pdf'),     -- Magasinier
(4, 1, 'https://contrats.example.com/contrat_sofia.pdf'),   -- Comptable
(5, 1, 'https://contrats.example.com/contrat_paul.pdf'),    -- Gerant
(6, 1, 'https://contrats.example.com/contrat_lina.pdf');      -- RH

-- -- Contrats candidats embauches (exemple)
-- (6, 1, 'https://contrats.example.com/contrat_alice.pdf'),   -- Vendeur
-- (10, 1, 'https://contrats.example.com/contrat_evelyne.pdf'), -- Caissier
-- (12, 1, 'https://contrats.example.com/contrat_gina.pdf'),   -- Magasinier
-- (14, 1, 'https://contrats.example.com/contrat_isabelle.pdf'), -- Comptable
-- (16, 1, 'https://contrats.example.com/contrat_karen.pdf');  -- Gerant


INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche) VALUES
(1, 5, 4, 'Gérant', '2020-01-10'),            -- Jean
(2, 4, 3, 'Comptable', '2021-06-01'),        -- Claire
(3, 3, 2, 'Magasinier principal', '2022-03-15'), -- Paul
(4, 1, 1, 'Vendeuse', '2023-05-20'),         -- Sophie
(5, 2, 1, 'Caissier', '2023-08-10'),         -- Lucas
(6, 6, 3, 'RH', '2023-01-05');    

INSERT INTO admins(id_employe, nom, mdp) VALUES
(1, 'admin_jean', 'hashedpwd1'),   -- Gérant
(2, 'admin_claire', 'hashedpwd2'), -- Comptable admin
(6, 'admin_lina', 'hashedpwd3');  


INSERT INTO tests (id_candidat, id_annonce, score_test, date_test) VALUES
(1, 1, 78.50, '2024-02-05'),
(2, 1, 65.20, '2024-02-05'),
(3, 2, 82.75, '2024-02-07'),
(4, 2, 59.40, '2024-02-07'),
(5, 3, 91.10, '2024-02-10'),
(6, 3, 73.30, '2024-02-10'),
(7, 4, 88.90, '2024-02-12'),
(8, 4, 54.25, '2024-02-12'),
(9, 5, 79.60, '2024-02-15'),
(10, 5, 62.45, '2024-02-15'),
(11, 1, 84.75, '2024-02-18'),
(12, 1, 70.10, '2024-02-18'),
(13, 2, 92.30, '2024-02-20'),
(14, 2, 66.85, '2024-02-20'),
(15, 3, 77.50, '2024-02-22'),
(16, 3, 81.40, '2024-02-22'),
(17, 4, 69.25, '2024-02-25'),
(18, 4, 85.60, '2024-02-25'),
(19, 5, 90.00, '2024-02-28'),
(20, 5, 72.15, '2024-02-28');


INSERT INTO config_entretien (id_departement, duree_entretien) VALUES
(1, INTERVAL '00:30:00'), -- Vente : entretiens courts (30 min)
(2, INTERVAL '00:40:00'), -- Stock : un peu plus long pour tester organisation et logistique
(3, INTERVAL '01:00:00'), -- Comptabilite : plus technique, 1 heure
(4, INTERVAL '01:15:00'); -- Direction : entretien approfondi (1h15)

-- Vendeur (profil 1)
INSERT INTO responsable_entretien (id_profil, id_admin, ordre_passage) VALUES
(1, 1, 1),  -- Jean (Gérant)
(1, 3, 2);  -- Lina (RH)

-- Caissier (profil 2)
INSERT INTO responsable_entretien (id_profil, id_admin, ordre_passage) VALUES
(2, 1, 1),  -- Jean (Gérant)
(2, 3, 2);  -- Lina (RH)

-- Magasinier (profil 3)
INSERT INTO responsable_entretien (id_profil, id_admin, ordre_passage) VALUES
(3, 1, 1),  -- Jean (Gérant)
(3, 3, 2);  -- Lina (RH)

-- Comptable (profil 4)
INSERT INTO responsable_entretien (id_profil, id_admin, ordre_passage) VALUES
(4, 2, 1),  -- Claire (Comptable admin)
(4, 3, 2);  -- Lina (RH)

-- Gérant (profil 5)
INSERT INTO responsable_entretien (id_profil, id_admin, ordre_passage) VALUES
(5, 1, 1),  -- Jean (Gérant senior)
(5, 3, 2);  -- Lina (RH)



-- Responsable 1 (Vendeur senior)
-- Vendeur, Jean (principal) = id_responsable 1
INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour) VALUES
(1, '09:00', '11:00', 1), -- Lundi
(1, '09:00', '11:00', 3), -- Mercredi

-- Vendeur, Lina (RH) = id_responsable 2
(2, '13:00', '15:00', 1),
(2, '13:00', '15:00', 3),

-- Caissier, Jean (principal) = id_responsable 3
(3, '09:30', '11:30', 2),
(3, '09:30', '11:30', 4),

-- Caissier, Lina (RH) = id_responsable 4
(4, '13:00', '15:00', 2),
(4, '13:00', '15:00', 4),

-- Magasinier, Paul (principal) = id_responsable 5
(5, '10:00', '12:00', 1),
(5, '10:00', '12:00', 3),

-- Magasinier, Lina (RH) = id_responsable 6
(6, '13:00', '15:00', 1),
(6, '13:00', '15:00', 3),

-- Comptable, Claire (principal) = id_responsable 7
(7, '09:00', '11:00', 2),
(7, '09:00', '11:00', 4),

-- Comptable, Lina (RH) = id_responsable 8
(8, '13:00', '15:00', 2),
(8, '13:00', '15:00', 4),

-- Gérant, Jean (principal) = id_responsable 9
(9, '09:00', '11:00', 1),
(9, '09:00', '11:00', 3),

-- Gérant, Lina (RH) = id_responsable 10
(10, '13:00', '15:00', 1),
(10, '13:00', '15:00', 3);


INSERT INTO jour_ferie("date") VALUES
('2025-01-01'), -- Jour de l'an
('2025-03-29'), -- Fête nationale
('2025-05-01'), -- Fête du travail
('2025-06-26'), -- Indépendance
('2025-08-15'), -- Assomption
('2025-11-01'), -- Toussaint
('2025-12-25'); -- Noël
('2025-02-20'); -- Noël


INSERT INTO etat (nom) VALUES
  ('rejete'),
  ('reporte'),
  ('en cours'),
  ('à venir'),
  ('acceptee');
