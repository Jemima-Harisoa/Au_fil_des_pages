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

INSERT INTO type_contrats (nom) VALUES
 ('CDI'),
 ('CDD'),
 ('Stage'),
 ('Freelance'),
 ('Intérim'),
 ('Alternance'),
 ('Consultant');


INSERT INTO diplomes (nom, niveau) VALUES
('Brevet', -3),
('Bac', 0),
('BTS / DUT (Bacc+2)', 2),
('Licence (Bacc+3)', 3),
('Master (Bacc+5)', 5),
('Doctorat ', 6);


-- 1. Caissier / Caissiere
INSERT INTO profils (
  titre,  competences, skills, loisirs, 
  id_diplome, filiere , experience_pro, certifications, langues,
  id_type_contrat, est_minimum
) VALUES (
  'Caissier / Caissiere',
  'Accueillir et encaisser les clients; Assurer la rapidite et la fiabilite des transactions; Maintenir un espace de caisse organise et propre; Appliquer les procedures de securite et de controle',
  'Rigueur et honnetete; Rapidite d''execution; Gestion du stress',
  'Jeux de logique; Activites demandant precision',
  2,
  'Toutes series',
  '1 an en caisse ou grande surface',
  NULL,
  NULL,
  1,
  TRUE
);

-- 2. Comptable
INSERT INTO profils (
  titre,  competences, skills, loisirs, 
  id_diplome, filiere , experience_pro, certifications, langues,
  id_type_contrat, est_minimum
) VALUES (
  'Comptable',
  'Assurer la tenue de la comptabilite generale et analytique; Etablir les bilans et declarations fiscales; Analyser les flux financiers; Conseiller la direction sur la gestion budgetaire',
  'Confidentialite; Esprit analytique; Minutie; Gestion des priorites',
  'Jeux strategiques; Sudoku; Activites de gestion',
  3,
  'Comptabilite , gestion , finance',
  '2 a 3 ans d''experience en cabinet ou PME',
  NULL,
  NULL,
  1,
  TRUE
);

-- 3. Gerant / Manager
INSERT INTO profils (
  titre,  competences, skills, loisirs, 
  id_diplome, filiere , experience_pro, certifications, langues,
  id_type_contrat, est_minimum
) VALUES (
  'Gerant / Manager',
  'Superviser et coordonner les equipes; Prendre des decisions strategiques; Assurer la rentabilite et le developpement de l''activite; Gerer les conflits et favoriser la cohesion',
  'Leadership; Prise de decision; Gestion des conflits; Vision strategique',
  'Lecture sur l''economie et entrepreneuriat; Sport collectif (leadership)',
  4,
  'management, commerce',
  '3 a 5 ans d''experience en commerce ou gestion',
  NULL,
  NULL,
  1,
  TRUE
);

-- 4. Magasinier
INSERT INTO profils (
  titre,  competences, skills, loisirs, 
  id_diplome, filiere , experience_pro, certifications, langues,
  id_type_contrat, est_minimum
) VALUES (
  'Magasinier',
  'Receptionner et stocker les marchandises; Preparer les commandes; Assurer le suivi des inventaires; Respecter les consignes de securite',
  'Organisation; Fiabilite; Resistance physique; Esprit d''equipe',
  'Sport (endurance, fitness); Bricolage (sens pratique)',
  1,
  'Logistique',
  '1 a 2 ans en gestion de stock',
  NULL,
  NULL,
  1,
  TRUE
);

-- 5. Vendeur / Vendeuse
INSERT INTO profils (
  titre,  competences, skills, loisirs, 
  id_diplome, filiere , experience_pro, certifications, langues,
  id_type_contrat, est_minimum
) VALUES (
  'Vendeur / Vendeuse',
  'Accueillir et conseiller les clients; Assurer la mise en rayon et l''attractivite du magasin; Conclure les ventes et fideliser la clientele; Participer aux inventaires et a la gestion des stocks',
  'Sens du relationnel; Communication claire; Patience et ecoute; Dynamisme',
  'Lecture (interet pour les livres); Activites sociales (theatre, clubs de lecture)',
  2,
  'Toutes series',
  'Debutant accepte, experience en relation client est un plus',
  NULL,
  NULL,
  1,
  TRUE
);
