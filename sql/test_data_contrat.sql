-- Table personnes
INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image, mdp) VALUES
('Rakoto', 'Jean', '1995-03-15', '0341234567', '/images/jean.png', 'mdp123'),
('Randria', 'Sofia', '1998-07-20', '0347654321', '/images/sofia.png', 'pass456'),
('Rasolo', 'Tiana', '1992-01-10', '0349876543', '/images/tiana.png', 'pwd789');

-- Table candidats
INSERT INTO candidats (id_personne, id_annonce, cv_url, poste) VALUES
(1, null, '/cv/jean.pdf', 'Développeur Java'),
(2, null, '/cv/sofia.pdf', 'Analyste'),
(3, null, '/cv/tiana.pdf', 'Chef de projet');

-- Table type_contrats
INSERT INTO type_contrats (nom) VALUES
('CDI'),
('CDD'),
('Stage');

-- Table contrats


-- Imaginons que seul Jean a déjà un contrat
INSERT INTO contrats (id_candidat, id_type_contrat, url_contrat) VALUES
(1, 1, '/contrats/contrat_jean.pdf');

-- Etat potentiel des entretient pour les tests 
INSERT INTO etat (nom) VALUES
('Planifié'),       -- 1
('Réalisé'),        -- 2
('Annulé'),         -- 3
('En attente de note'); -- 4

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
