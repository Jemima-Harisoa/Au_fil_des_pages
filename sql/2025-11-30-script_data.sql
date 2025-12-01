-- =====================================================
-- 1. TABLES DE RÉFÉRENCE (lookup tables)
-- =====================================================

INSERT INTO sexe (type_sexe) VALUES 
('Homme'), ('Femme'), ('Non binaire'), ('Autre');

INSERT INTO departements (nom) VALUES 
('Ressources Humaines'), ('Informatique'), ('Finance'), ('Commercial'), ('Production'), ('Marketing');

INSERT INTO filieres (nom) VALUES 
('Informatique'), ('Gestion'), ('Comptabilité'), ('Marketing'), ('Mécanique'), ('Électricité'), ('RH');

INSERT INTO type_contrats (nom) VALUES 
('CDI'), ('CDD'), ('Stage'), ('Alternance'), ('Intérim'), ('Freelance');

INSERT INTO diplomes (nom, niveau) VALUES 
('CAP', 3), ('BAC', 4), ('BAC+2', 5), ('BAC+3', 6), ('BAC+5', 7), ('Doctorat', 8);

INSERT INTO evenements (nom_evenement) VALUES 
('Embauche'), ('Mutation'), ('Promotion'), ('Démission'), ('Licenciement'), ('Fin de CDD');

INSERT INTO etat (nom) VALUES 
('En cours'), ('Validé'), ('Refusé'), ('Annulé'), ('Terminé');

INSERT INTO appreciation (type_appreciation, code) VALUES 
('Très bon', 5), ('Bon', 4), ('Moyen', 3), ('Insuffisant', 2), ('Mauvais', 1);

INSERT INTO conge_type (nom, description, nombre_jour, deductible_sur_salaire, deductible_sur_conge) VALUES
('Congés payés annuels', 'Congés classiques', 25, FALSE, FALSE),
('RTT', 'Réduction du temps de travail', 12, FALSE, FALSE),
('Congé maladie', 'Arrêt maladie', NULL, TRUE, FALSE),
('Congé sans solde', 'Sans rémunération', NULL, FALSE, FALSE),
('Congé maternité', 'Naissance', 112, FALSE, TRUE);

INSERT INTO status_validation_cv (statut) VALUES 
('En attente'), ('Validé'), ('Rejeté'), ('À revoir');

INSERT INTO type_prime (libelle) VALUES 
('Prime de performance'), ('Prime de fin d''année'), ('Prime d''ancienneté'), ('Prime de nuit');

INSERT INTO smig (montant, date_application) VALUES 
(450000.00, '2024-01-01'), (480000.00, '2025-01-01');

INSERT INTO irsa (min, max, pourcentage) VALUES 
(0, 1000000, 0), (1000001, 2500000, 10), (2500001, 5000000, 20), (5000001, NULL, 30);

INSERT INTO jour_ferie (date) VALUES 
('2025-01-01'), ('2025-05-01'), ('2025-05-08'), ('2025-07-14'), ('2025-11-01'), ('2025-12-25');

INSERT INTO postes (nom, description) VALUES 
('Développeur Fullstack', 'PHP + Vue.js'), 
('Chargé RH', 'Recrutement et paie'), 
('Comptable', 'Saisie et bilan'), 
('Commercial', 'Prospection B2B'), 
('Technicien maintenance', 'Maintenance machines');

INSERT INTO competences (nom, description, domaine) VALUES 
('PHP 8', 'Développement backend', 'Informatique'),
('Vue.js 3', 'Framework frontend', 'Informatique'),
('SQL / PostgreSQL', 'Requêtes complexes', 'Informatique'),
('Recrutement', 'Sourcing et entretiens', 'RH'),
('Paie', 'DSN, bulletins', 'RH'),
('Comptabilité générale', 'Saisie, lettrage', 'Finance');

-- =====================================================
-- 2. PERSONNES & CANDIDATS / EMPLOYES
-- =====================================================

INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image, id_sexe) VALUES
('Dupont', 'Marie', '1992-04-12', 'marie.dupont@gmail.com', '/img/marie.jpg', 2),
('Martin', 'Julien', '1988-09-23', 'julien.m@gmail.com', '/img/julien.jpg', 1),
('Traore', 'Aïcha', '1995-06-15', 'aicha.traore@outlook.com', '/img/aicha.jpg', 2),
('Rivoire', 'Lucas', '1990-11-30', 'lucas.rivoire@free.fr', '/img/lucas.jpg', 1),
('Rakoto', 'Nirina', '1993-02-18', 'nirina.rakoto@yahoo.com', '/img/nirina.jpg', 2),
('Bernard', 'Sophie', '1985-07-22', 'sophie.bernard@entreprise.com', '/img/sophie.jpg', 2);

-- Quelques candidats
INSERT INTO candidats (id_personne, poste, cv_url) VALUES
(1, 'Développeur Fullstack', '/cv/marie_dupont.pdf'),
(2, 'Chargé RH', '/cv/julien_martin.pdf'),
(3, 'Comptable', '/cv/aicha_traore.pdf'),
(4, 'Développeur Fullstack', '/cv/lucas_rivoire.pdf');

-- Quelques employés déjà recrutés
INSERT INTO contrats (id_candidat, id_type_contrat, url_contrat) VALUES
(1, 1, '/contrats/marie_cdi.pdf'), -- Marie → CDI
(2, 1, '/contrats/julien_cdi.pdf');

INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche, nombre_conge, salaire_base) VALUES
(1, 1, 2, 'Développeur Fullstack', '2024-03-01', 25, 3800000),
(2, 2, 1, 'Chargé RH', '2023-06-15', 25, 3500000),
(5, NULL, 3, 'Comptable', '2022-01-10', 28, 3200000),
(6, NULL, 1, 'Directrice RH', '2020-09-01', 30, 5500000);

-- Admins
INSERT INTO admins (id_employe, nom, mdp, date_affiliation) VALUES
(2, 'jmartin', '$2y$10$...', '2023-06-15'),  -- Julien
(4, 'sbernard', '$2y$10$...', '2020-09-01');  -- Sophie (Directrice RH)

-- Connexion employes
INSERT INTO connexEmployes (idEmploye, mdp) VALUES
(1, '$2y$10$examplehash123'),
(2, '$2y$10$examplehash456'),
(3, '$2y$10$examplehash789'),
(4, '$2y$10$examplehash000');

-- =====================================================
-- 3. PROFILS & ANNONCES
-- =====================================================

INSERT INTO profils (titre, id_diplome, id_filiere, id_type_contrat, id_departement, est_minimum) VALUES
('Développeur Fullstack Senior', 6, 1, 1, 2, false),
('Chargé de recrutement', 6, 7, 1, 1, false),
('Comptable confirmé', 5, 3, 1, 3, false);

INSERT INTO annonces (id_profil, titre, date_publication, date_expiration, nombre_poste, lien) VALUES
(1, 'Développeur Fullstack (H/F)', '2025-01-15', '2025-03-15', 2, 'https://aufildespages.com/jobs/dev-2025'),
(2, 'Chargé RH expérimenté', '2025-02-01', '2025-04-01', 1, 'https://aufildespages.com/jobs/rh-2025');

-- =====================================================
-- 4. POINTAGES & HORAIRES
-- =====================================================

INSERT INTO horaires_employe (id_employe, jour_semaine, debut_travail, fin_travail, seuil_retard) VALUES
(1, 1, '08:30:00', '17:30:00', '00:15:00'),
(1, 2, '08:30:00', '17:30:00', '00:15:00'),
(1, 3, '08:30:00', '17:30:00', '00:15:00'),
(1, 4, '08:30:00', '17:30:00', '00:15:00'),
(1, 5, '08:30:00', '17:30:00', '00:15:00');

INSERT INTO pointage (id_employe, connexion, deconnexion) VALUES
(1, '2025-03-10 08:28:00', '2025-03-10 17:45:00'),
(1, '2025-03-11 08:35:00', '2025-03-11 19:15:00'),
(2, '2025-03-10 08:55:00', '2025-03-10 17:32:00');

-- =====================================================
-- 5. CONGÉS
-- =====================================================

INSERT INTO conge_demande (id_employe, description, date_demande, date_debut, date_fin, id_type_conge) VALUES
(1, 'Vacances été', '2025-05-20', '2025-07-20', '2025-08-03', 1),
(2, 'Congé maladie', '2025-03-05', '2025-03-06', '2025-03-10', 3),
(3, 'Congé sans solde', '2025-04-01', '2025-06-01', '2025-06-15', 4);

INSERT INTO conge_solde (id_employe, id_type_conge, solde, annee) VALUES
(1, 1, 18.5, 2025),
(1, 2, 8, 2025),
(2, 1, 22, 2025);

-- =====================================================
-- 6. ÉVALUATIONS DE PERFORMANCE
-- =====================================================

INSERT INTO employe_evaluation_periodes (nom, frequence_mois) VALUES
('Évaluation annuelle', 12),
('Bilan semestriel', 6);

INSERT INTO employe_criteres_evaluation (nom, poids) VALUES
('Qualité du travail', 30),
('Respect des délais', 25),
('Autonomie', 20),
('Esprit d''équipe', 15),
('Initiative', 10);

-- Exemple d’évaluation pour Marie
INSERT INTO employe_evaluations (employe_id, periode_id, date_evaluation, statut, manager_id) VALUES
(1, 1, '2025-01-15', 'TERMINEE', 4);

INSERT INTO employe_evaluations_details (evaluation_id, critere_id, note, commentaire) VALUES
(1, 1, 8.5, 'Code propre et bien documenté'),
(1, 2, 9.0, 'Toujours dans les temps'),
(1, 3, 7.5, NULL),
(1, 4, 9.5, 'Très bonne ambiance'),
(1, 5, 8.0, 'Propose régulièrement des améliorations');

-- =====================================================
-- 7. COMPÉTENCES & FORMATIONS
-- =====================================================

INSERT INTO employe_competences (employe_id, competence_id, niveau, date_obtention) VALUES
(1, 1, 4.5, '2023-06-01'), -- Marie → PHP
(1, 2, 4.8, '2024-01-10'), -- Vue.js
(1, 3, 4.2, '2022-09-01'),
(2, 4, 4.7, '2023-01-01'), -- Julien → Recrutement
(3, 6, 4.9, '2021-05-01'); -- Comptable → Comptabilité

INSERT INTO competences_postes (poste_id, competence_id, niveau_requis) VALUES
(1, 1, 4), (1, 2, 4), (1, 3, 3),
(2, 4, 4);

-- =====================================================
-- 8. NOTIFICATIONS (exemples)
-- =====================================================

INSERT INTO notifications (id_personne, message, date_notification) VALUES
(1, 'Votre demande de congés a été validée', '2025-05-22 14:30:00'),
(2, 'Rappel : évaluation annuelle le 15 janvier', '2025-01-10 09:00:00');

-- =====================================================
-- FIN DES DONNÉES DE TEST
-- =====================================================

-- SELECT 'Données de test insérées avec succès ! ' ||
--        (SELECT COUNT(*) FROM personnes) || ' personnes, ' ||
--        (SELECT COUNT(*) FROM employes) || ' employés, ' ||
--        (SELECT COUNT(*) FROM pointage) || ' pointages';