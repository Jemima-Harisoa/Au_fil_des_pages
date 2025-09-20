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

-- Filieres
INSERT INTO filieres (nom) VALUES
  ('Toutes series'),             
  ('Comptabilite et Finance'),   
  ('Management et Commerce'),    
  ('Logistique'); 

-- Profils
INSERT INTO profils (
  titre, competences, skills, loisirs, id_diplome, id_filiere, experience_pro, certifications, langues, id_type_contrat, est_minimum
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
 4, 3, '3 a 5 ans d''experience en commerce ou gestion', NULL, NULL, 1, TRUE),
('Magasinier',
 'Receptionner et stocker les marchandises; Preparer les commandes; Assurer le suivi des inventaires; Respecter les consignes de securite',
 'Organisation; Fiabilite; Resistance physique; Esprit d''equipe',
 'Sport (endurance, fitness); Bricolage (sens pratique)',
 2, 4, '1 a 2 ans en gestion de stock', NULL, NULL, 1, TRUE),
('Vendeur / Vendeuse',
 'Accueillir et conseiller les clients; Assurer la mise en rayon et l''attractivite du magasin; Conclure les ventes et fideliser la clientele; Participer aux inventaires et a la gestion des stocks',
 'Sens du relationnel; Communication claire; Patience et ecoute; Dynamisme',
 'Lecture (interet pour les livres); Activites sociales (theatre, clubs de lecture)',
 2, 1, 'Debutant accepte, experience en relation client est un plus', NULL, NULL, 1, TRUE),
('Developpeur Web',
 'PHP, JavaScript, SQL', 'React, Node.js', 'Lecture, Jeux video', 4, 3, '2 ans en startup', 'Certification PHP Zend', 'Francais, Anglais', 1, TRUE),
('Charge de Recrutement',
 'Sourcing, Entretiens', 'Communication, Negociation', 'Voyages', 5, 3, '3 ans en cabinet RH', 'Certification RH CIPD', 'Francais, Anglais', 2, TRUE),
('Analyste Financier',
 'Analyse, Reporting', 'Excel, PowerBI', 'Echecs', 5, 2, '5 ans en banque', 'CFA Level 1', 'Francais, Anglais', 1, TRUE),
('Community Manager',
 'Strategie digitale', 'Photoshop, SEO', 'Photographie', 4, 3, '2 ans en agence digitale', 'Google Digital Marketing', 'Francais, Anglais', 1, TRUE),
('Agent Logistique',
 'Gestion stock, Transport', 'SAP, Excel', 'Football', 3, 4, '3 ans en entrepot', 'Formation Supply Chain', 'Francais', 2, TRUE);

-- Questions
INSERT INTO questions (question, id_profil, note) VALUES
('Comment gerer une file d attente en caisse ?', 1 , 4.50),
('Quelles sont les principales obligations fiscales d une PME ?',1, 5.50),
('Comment motiver une equipe en periode de forte activite ?', 1, 6.00),
('Quelle est la meilleure methode pour organiser un stock ?', 1, 4.00),
('Comment fideliser un client apres une premiere vente ?', 1, 5.00);

-- Reponses
INSERT INTO reponses_question (id_question, reponse, est_correct) VALUES
(1, 'Rester rapide et courtois, utiliser le scanner efficacement', TRUE),
(1, 'Ignorer les clients presses', FALSE),
(2, 'Tenir une comptabilite reguliere et declarer TVA/impots', TRUE),
(2, 'Reporter toutes les obligations aux annees suivantes', FALSE),
(3, 'Communiquer clairement, fixer objectifs, valoriser les efforts', TRUE),
(3, 'Sanctionner systematiquement les retards', FALSE),
(4, 'Classer, etiqueter et tenir un inventaire a jour', TRUE),
(4, 'Laisser les produits sans suivi', FALSE),
(5, 'Offrir un bon service et proposer des avantages', TRUE),
(5, 'Ne plus parler au client apres la vente', FALSE);

-- Autres personnes
INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) VALUES
('Lia', 'Mia', '1995-05-12', '0341234560', 'https://img.com/lia.jpg'),
('Rasoa', 'Sophie', '1998-09-21', '0349876543', 'https://img.com/sophie.jpg'),
('Andry', 'Michel', '1990-11-03', '0345556667', 'https://img.com/michel.jpg'),
('Hanitra', 'Lina', '2000-01-15', '0342223334', 'https://img.com/lina.jpg');


-- INSERT INTO candidats (id_personne, id_annonce, id_profil , id_utilisateur , poste) VALUES
-- (1, 1, 1 , 1, 'Caissier / Caissiere');

INSERT INTO message_automatique (message) 
VALUES ('Merci d avoir complete le test. Vos reponses ont ete enregistrees.Les responsables d Au fil des Page vont analyser vos resultats et vous serez recontacte prochainement.');

