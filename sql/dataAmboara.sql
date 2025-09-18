INSERT INTO departements (nom) VALUES 
  ('Direction'),
  ('Comptabilite'),
  ('Stock'),
  ('Vente');
INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) VALUES
  ('Rakoto', 'Jean', '1985-03-12', '0341234567', 'images/jean.jpg'),
  ('Rasoanaivo', 'Marie', '1990-07-25', '0342345678', 'images/marie.jpg'),
  ('Randriamahenina', 'Paul', '1988-11-02', '0343456789', 'images/paul.jpg'),
  ('Andriantsitoha', 'Lova', '1995-01-15', '0344567890', 'images/lova.jpg'),
  ('Rakotondrazaka', 'Hery', '1992-05-30', '0345678901', 'images/hery.jpg');

INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche) VALUES
  (1, NULL, 1, 'Directeur', '2020-01-15'),  
  (2, NULL, 2, 'Comptable', '2021-06-01');  

INSERT INTO admins (id_employe, nom, mdp) VALUES
  (1, 'RD', 'mdp1'),
  (2, 'RC', 'mdp2');

  -- UPDATE admins
-- SET date_fin_affiliation = NOW()
-- WHERE id_admin = 1;

INSERT INTO diplomes (nom, niveau) VALUES
('Brevet', -3),
('Bac', 0),
('BTS / DUT', 2),
('Licence', 3),
('Master', 5),
('Doctorat', 6);


