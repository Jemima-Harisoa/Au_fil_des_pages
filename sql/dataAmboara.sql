INSERT INTO utilisateurs (nom, mdp) VALUES
('ema', 'mdp1'),
('jean', 'mdp2'),
('sophie', 'mdp3');

-- Departements
INSERT INTO departements (nom) VALUES 
  ('Direction'),
  ('Comptabilite'),
  ('Stock'),
  ('Vente');

-- Personnes
INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) VALUES
  ('Rakoto', 'Jean', '1985-03-12', '0341234567', 'images/jean.jpg'),
  ('Rasoanaivo', 'Marie', '1990-07-25', '0342345678', 'images/marie.jpg'),
  ('Randriamahenina', 'Paul', '1988-11-02', '0343456789', 'images/paul.jpg'),
  ('Andriantsitoha', 'Lova', '1995-01-15', '0344567890', 'images/lova.jpg'),
  ('Rakotondrazaka', 'Hery', '1992-05-30', '0345678901', 'images/hery.jpg');


-- Table candidats
INSERT INTO candidats (id_personne, id_annonce, cv_url, poste) VALUES
(1, null, '/cv/ema.pdf', 'Développeur Java'),
(2, null, '/cv/jean.pdf', 'Analyste'),
(3, null, '/cv/sophie.pdf', 'Chef de projet');
-- Employes
INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche) VALUES
  (1, NULL, 1, 'Directeur', '2020-01-15'),  
  (2, NULL, 2, 'Comptable', '2021-06-01');

-- Admins
INSERT INTO admins (id_employe, nom, mdp) VALUES
  (1, 'RD', 'mdp1'),
  (2, 'RC', 'mdp2');

-- Type contrats
INSERT INTO type_contrats (nom) VALUES
 ('CDI'),
 ('CDD'),
 ('Stage'),
 ('Freelance'),
 ('Interim'),
 ('Alternance'),
 ('Consultant');

-- Diplomes
INSERT INTO diplomes (nom, niveau) VALUES
('Brevet', -3),
('Bac', 0),
('BTS / DUT (Bacc+2)', 2),
('Licence (Bacc+3)', 3),
('Master (Bacc+5)', 5),
('Doctorat', 6);
-- Etat potentiel des entretient pour les tests (non definitif) 
INSERT INTO etat (nom) VALUES
('Planifié'),       -- 1
('Réalisé'),        -- 2
('Annulé'),         -- 3
('En attente de note'), -- 4 
('Brouillon'), --5
('Validé'), -- 6
('Non validé'); -- 7 

-- Niveau d'appreciation 
INSERT INTO appreciation (type_appreciation, code) VALUES
('Excellent', 5),
('Très Bien', 4),
('Bien', 3),
('Passable', 2),
('Insuffisant', 1);

-- Table tests (pour alimenter view_scoring)
INSERT INTO tests (id_candidat, score_test, date_test) VALUES
(1, 85.50, '2025-09-10'),
(2, 70.00, '2025-09-11'),
(3, 90.25, '2025-09-12');

-- Table planning_entretien (pour alimenter view_scoring)
INSERT INTO planning_entretien (id_candidat, date_heure_entretien, score_entretien, etat, id_appreciation) VALUES
(1, '2025-09-15 10:00:00', 80.00, 1, 2),
(2, '2025-09-16 14:30:00', 75.00, 1, 3),
(3, '2025-09-17 09:00:00', NULL, null, NULL); -- Entretien non noté

-- Filieres
INSERT INTO filieres (nom) VALUES
  ('Toutes series'),             
  ('Comptabilite et Finance'),   
  ('Management et Commerce'),    
  ('Logistique'); 


-- Autres personnes
INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) VALUES
('Lia', 'Mia', '1995-05-12', '0341234560', 'https://img.com/lia.jpg'),
('Rasoa', 'Sophie', '1998-09-21', '0349876543', 'https://img.com/sophie.jpg'),
('Andry', 'Michel', '1990-11-03', '0345556667', 'https://img.com/michel.jpg'),
('Hanitra', 'Lina', '2000-01-15', '0342223334', 'https://img.com/lina.jpg');


-- INSERT INTO candidats (id_personne, id_annonce, id_profil, poste) VALUES
-- (1, 1, 1 , 'Caissier / Caissiere');

