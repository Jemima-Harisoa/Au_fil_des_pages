-- ================== DONNÉES DE TEST ==================

-- etat
INSERT INTO etat (nom) VALUES
('brouillon'),
('en_attente_etape1'),
('en_attente_etape2'),
('en_attente_etape3'),
('validé'),
('rejeté');

-- depatements
INSERT INTO departements (nom) VALUES 
  ('Direction'),
  ('Comptabilite'),
  ('Stock'),
  ('RH'),
  ('Vente');

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

-- Type contrats
INSERT INTO type_contrats (nom) VALUES
 ('CDI'),
 ('CDD'),
 ('Stage'),
 ('Freelance'),
 ('Interim'),
 ('Alternance'),
 ('Consultant');

-- 1. Insertion des personnes (employés et candidats)
INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) VALUES
-- Direction
('Rakoto', 'Jean', '1980-05-15', 'jean.rakoto@aufildespages.mg', 'rakoto_jean.jpg'),

-- RH (Responsable + employé)
('Rasoa', 'Marie', '1985-08-20', 'marie.rasoa@aufildespages.mg', 'rasoa_marie.jpg'),
('Randria', 'Paul', '1990-03-10', 'paul.randria@aufildespages.mg', 'randria_paul.jpg'),

-- Comptabilité
('Rabe', 'Lova', '1988-11-25', 'lova.rabe@aufildespages.mg', 'rabe_lova.jpg'),
('Razafy', 'Miora', '1992-07-18', 'miora.razafy@aufildespages.mg', 'razafy_miora.jpg'),

-- Stock
('Andria', 'Tiana', '1983-12-05', 'tiana.andria@aufildespages.mg', 'andria_tiana.jpg'),
('Ravo', 'Hery', '1991-09-30', 'hery.ravo@aufildespages.mg', 'ravo_hery.jpg'),

-- Vente
('Razaka', 'Sandra', '1987-04-12', 'sandra.razaka@aufildespages.mg', 'razaka_sandra.jpg'),
('Rasolo', 'Toky', '1993-01-22', 'toky.rasolo@aufildespages.mg', 'rasolo_toky.jpg'),

-- Candidats
('Ramanana', 'Nirina', '1995-06-14', 'nirina.ramanana@email.mg', 'ramanana_nirina.jpg'),
('Randriama', 'Hajatiana', '1994-02-28', 'hajatiana.randriama@email.mg', 'randriama_hajatiana.jpg'),
('Razafindra', 'Fenitra', '1996-09-08', 'fenitra.razafindra@email.mg', 'razafindra_fenitra.jpg'),
('Andrianja', 'Mamisoa', '1993-11-17', 'mamisoa.andrianja@email.mg', 'andrianja_mamisoa.jpg'),
('Ralison', 'Voahangy', '1995-04-03', 'voahangy.ralison@email.mg', 'ralison_voahangy.jpg');

-- 2. Insertion des employés
INSERT INTO employes (id_personne, id_departement, poste, date_embauche) VALUES
-- Direction (id_departement = 1)
(1, 1, 'Directeur Général', '2015-01-15'),

-- RH (id_departement = 4)
(2, 4, 'Responsable RH', '2018-03-20'),
(3, 4, 'Assistant RH', '2020-06-10'),

-- Comptabilité (id_departement = 2)
(4, 2, 'Responsable Comptabilité', '2017-09-05'),
(5, 2, 'Comptable', '2021-02-14'),

-- Stock (id_departement = 3)
(6, 3, 'Responsable Stock', '2016-11-08'),
(7, 3, 'Gestionnaire Stock', '2019-04-25'),

-- Vente (id_departement = 5)
(8, 5, 'Responsable Vente', '2018-07-12'),
(9, 5, 'Commercial', '2022-01-30');

-- 3. Insertion des comptes utilisateurs pour les employés
INSERT INTO utilisateurs (nom, mdp) VALUES
('jean.rakoto', 'directeur123'),
('marie.rasoa', 'rhresponsable123'),
('paul.randria', 'rhassistant123'),
('lova.rabe', 'comptableresponsable123'),
('miora.razafy', 'comptable123'),
('tiana.andria', 'stockresponsable123'),
('hery.ravo', 'stock123'),
('sandra.razaka', 'venteresponsable123'),
('toky.rasolo', 'commercial123');

-- 4. Insertion des comptes admin pour les responsables
INSERT INTO admins (id_employe, nom, mdp) VALUES
(1, 'admin_direction', 'admindir123'),      -- Admin Direction
(2, 'admin_rh', 'adminrh123'),              -- Admin RH
(4, 'admin_comptabilite', 'admincompta123'), -- Admin Comptabilité
(6, 'admin_stock', 'adminstock123'),        -- Admin Stock
(8, 'admin_vente', 'adminvente123');        -- Admin Vente

-- 5. Insertion des profils de recrutement (selon DonneesTenaIzy)
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
 2, 1, 'Debutant accepte, experience en relation client est un plus', NULL, NULL, 1, TRUE);

-- 6. Insertion des annonces
INSERT INTO annonces (id_profil, titre, date_publication, date_expiration, nombre_poste, lien) VALUES
(1, 'Recherche Caissier/Caissière', '2024-01-15', '2024-02-15', 1, '/annonces/caissier-001'),
(2, 'Poste Comptable', '2024-01-20', '2024-02-20', 1, '/annonces/comptable-002'),
(3, 'Gérant Manager H/F', '2024-01-25', '2024-02-25', 1, '/annonces/manager-003'),
(4, 'Magasinier', '2024-02-01', '2024-03-01', 1, '/annonces/magasinier-004'),
(5, 'Vendeur/Vendeuse Librairie', '2024-02-05', '2024-03-05', 1, '/annonces/vendeur-005');

-- 7. Insertion des candidats avec comptes utilisateurs
INSERT INTO utilisateurs (nom, mdp) VALUES
('nirina.ramanana', 'candidat123'),
('hajatiana.randriama', 'candidat123'),
('fenitra.razafindra', 'candidat123'),
('mamisoa.andrianja', 'candidat123'),
('voahangy.ralison', 'candidat123');

INSERT INTO candidats (id_personne, id_annonce, id_profil, cv_url, poste, id_utilisateur) VALUES
(10, 1, 1, '/cv/nirina_caissiere.pdf', 'Caissière', 10),
(11, 2, 2, '/cv/hajatiana_comptable.pdf', 'Comptable', 11),
(12, 3, 3, '/cv/fenitra_manager.pdf', 'Gérant', 12),
(13, 4, 4, '/cv/mamisoa_magasinier.pdf', 'Magasinier', 13),
(14, 5, 5, '/cv/voahangy_vendeuse.pdf', 'Vendeuse', 14);

-- 8. Insertion des tests (scores > 10/20 pour réussite)
INSERT INTO tests (id_candidat, id_annonce, score_test, date_test) VALUES
(1, 1, 16.50, '2024-02-01'),
(2, 2, 15.75, '2024-02-02'),
(3, 3, 17.25, '2024-02-03'),
(4, 4, 18.00, '2024-02-04'),
(5, 5, 16.80, '2024-02-05');

-- 9. Insertion des responsables d'entretien
INSERT INTO responsable_entretien (id_profil, id_employe, ordre_passage) VALUES
(1, 8, 1),  -- Responsable vente pour profil caissier
(2, 4, 1),  -- Responsable comptabilité
(3, 1, 1),  -- Directeur pour profil manager
(4, 6, 1),  -- Responsable stock pour profil magasinier
(5, 8, 1);  -- Responsable vente pour profil vendeur

-- 10. Insertion des entretiens (scores > 12/20)
INSERT INTO planning_entretien (id_candidat, id_responsable, date_heure_entretien, score_entretien, etat, id_appreciation) VALUES
(1, 1, '2024-02-10 09:00:00', 15.50, 5, 1),
(2, 2, '2024-02-11 10:30:00', 14.75, 5, 1),
(3, 3, '2024-02-12 14:00:00', 16.25, 5, 1),
(4, 4, '2024-02-13 11:00:00', 17.00, 5, 1),
(5, 5, '2024-02-14 15:30:00', 15.80, 5, 1);

-- 11. Vérification avec la vue scoring
SELECT * FROM view_scoring WHERE id_candidat IN (1, 2, 3, 4, 5);