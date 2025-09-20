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
('Rakoto', 'Jean', '1985-03-15', '0321234567', ''),
('Andriamatoa', 'Marie', '1990-07-22', '0322234567', ''),
('Rasoa', 'Luc', '1988-11-05', '0323234567', ''),
('Randria', 'Sofia', '1992-01-17', '0324234567', ''),
('Rabe', 'Paul', '1983-09-30', '0325234567', '');

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


INSERT INTO candidats (id_personne, id_annonce, id_profil, cv_url, poste) VALUES
(6, 1, 1, 'https://cv.example.com/alice.pdf','Vendeur'),
(7, 1, 1, 'https://cv.example.com/bob.pdf','Vendeur'),
(8, 1, 1, 'https://cv.example.com/caroline.pdf','Vendeur'),
(9, 1, 1, 'https://cv.example.com/david.pdf','Vendeur'),
(10, 2, 2, 'https://cv.example.com/evelyne.pdf','Caissier'),
(11, 2, 2, 'https://cv.example.com/fabrice.pdf','Caissier'),
(12, 3, 3, 'https://cv.example.com/gina.pdf','Magasinier'),
(13, 3, 3, 'https://cv.example.com/hery.pdf','Magasinier'),
(14, 4, 4, 'https://cv.example.com/isabelle.pdf','Comptable'),
(15, 4, 4, 'https://cv.example.com/jules.pdf','Comptable'),
(16, 5, 5, 'https://cv.example.com/karen.pdf','Gerant'),
(17, 5, 5, 'https://cv.example.com/leo.pdf','Gerant'),
(18, 1, 1, 'https://cv.example.com/mireille.pdf','Vendeur'),
(19, 1, 1, 'https://cv.example.com/nicolas.pdf','Vendeur'),
(20, 2, 2, 'https://cv.example.com/olivia.pdf','Caissier'),
(21, 3, 3, 'https://cv.example.com/patrick.pdf','Magasinier'),
(22, 1, 1, 'https://cv.example.com/quentin.pdf','Vendeur'),
(23, 1, 1, 'https://cv.example.com/rita.pdf','Vendeur'),
(24, 4, 4, 'https://cv.example.com/samuel.pdf','Comptable'),
(25, 5, 5, 'https://cv.example.com/therese.pdf','Gerant');

INSERT INTO contrats (id_candidat, id_type_contrat, url_contrat) VALUES
-- Contrats employes existants
(1, 1, 'https://contrats.example.com/contrat_jean.pdf'),    -- Vendeur
(2, 1, 'https://contrats.example.com/contrat_marie.pdf'),   -- Caissier
(3, 1, 'https://contrats.example.com/contrat_luc.pdf'),     -- Magasinier
(4, 1, 'https://contrats.example.com/contrat_sofia.pdf'),   -- Comptable
(5, 1, 'https://contrats.example.com/contrat_paul.pdf'),    -- Gerant

-- Contrats candidats embauches (exemple)
(6, 1, 'https://contrats.example.com/contrat_alice.pdf'),   -- Vendeur
(10, 1, 'https://contrats.example.com/contrat_evelyne.pdf'), -- Caissier
(12, 1, 'https://contrats.example.com/contrat_gina.pdf'),   -- Magasinier
(14, 1, 'https://contrats.example.com/contrat_isabelle.pdf'), -- Comptable
(16, 1, 'https://contrats.example.com/contrat_karen.pdf');  -- Gerant


INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche) VALUES
(1, 1, 1, 'Vendeur', '2023-01-10'),
(2, 2, 1, 'Caissier', '2022-12-05'),
(3, 3, 2, 'Magasinier', '2023-02-20'),
(4, 4, 3, 'Comptable', '2022-11-15'),
(5, 5, 4, 'Gerant', '2022-10-01');


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

-- Vente
INSERT INTO responsable_entretien (id_profil, id_employe, ordre_passage) VALUES
(1, 1, 1),  -- Vendeur senior evalue en 1er
(5, 5, 2);  -- Gerant valide ensuite

-- Stock
INSERT INTO responsable_entretien (id_profil, id_employe, ordre_passage) VALUES
(3, 3, 1),  -- Magasinier principal
(5, 5, 2);  -- Gerant valide ensuite

-- Comptabilite
INSERT INTO responsable_entretien (id_profil, id_employe, ordre_passage) VALUES
(4, 4, 1),  -- Comptable principal
(5, 5, 2);  -- Gerant valide ensuite

-- Direction
INSERT INTO responsable_entretien (id_profil, id_employe, ordre_passage) VALUES
(5, 5, 1);  -- Gerant en direct (pas besoin de 2e passage ici)


-- Responsable 1 (Vendeur senior)
INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour) VALUES
(1, '09:00', '12:00', 1),
(1, '09:00', '12:00', 2),
(1, '09:00', '12:00', 3),
(1, '09:00', '12:00', 4),
(1, '09:00', '12:00', 5);

-- Responsable 2 (Gerant pour Vente)
INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour) VALUES
(2, '14:00', '17:00', 1),
(2, '14:00', '17:00', 2),
(2, '14:00', '17:00', 3),
(2, '14:00', '17:00', 4),
(2, '14:00', '17:00', 5);

-- Responsable 3 (Magasinier principal)
INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour) VALUES
(3, '08:00', '11:00', 1),
(3, '08:00', '11:00', 2),
(3, '08:00', '11:00', 3),
(3, '08:00', '11:00', 4),
(3, '08:00', '11:00', 5);

-- Responsable 4 (Comptable)
INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour) VALUES
(4, '10:00', '13:00', 2),
(4, '10:00', '13:00', 3),
(4, '10:00', '13:00', 4),
(4, '10:00', '13:00', 5),
(4, '10:00', '13:00', 6);

-- Responsable 5 (Gerant global)
INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour) VALUES
(5, '15:00', '18:00', 1),
(5, '15:00', '18:00', 2),
(5, '15:00', '18:00', 3),
(5, '15:00', '18:00', 4),
(5, '15:00', '18:00', 5),
(5, '15:00', '18:00', 6),
(5, '15:00', '18:00', 7);
