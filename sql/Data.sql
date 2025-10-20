-- ================== INSERTIONS DE TEST (AU FIL DES PAGES) ==================

-- ---- Départements ----
INSERT INTO departements (id_departement, nom) VALUES
(1, 'Vente'),
(2, 'Stock'),
(3, 'Comptabilite'),
(4, 'RH'),
(5, 'Direction');

-- ---- Diplômes ----
INSERT INTO diplomes (id_diplome, nom, niveau) VALUES
(1, 'Bac', 0),
(2, 'Bac+2', 2),
(3, 'Bac+3', 3),
(4, 'BTS Commerce', 2),
(5, 'DUT GEA', 2),
(6, 'Licence Management', 3),
(7, 'Master Management', 5),
(8, 'CAP Logistique', 0),
(9, 'BEP Logistique', 0);

-- ---- Filières ----
INSERT INTO filieres (id_filiere, nom) VALUES
(1, 'Commerce'),
(2, 'Vente'),
(3, 'Gestion/Management'),
(4, 'Comptabilite/Finance'),
(5, 'Logistique'),
(6, 'Litterature/SH'),
(7, 'Informatique'),
(8, 'Marketing'),
(9, 'Administration');

-- ---- Types de contrats ----
INSERT INTO type_contrats (id_type_contrat, nom) VALUES
(1, 'CDI'),
(2, 'CDD'),
(3, 'Mi-temps'),
(4, 'Stage');

-- ---- États & Appreciations ----
INSERT INTO etat (id_etat, nom) VALUES
(1, 'planifie'),
(2, 'accepte'),
(3, 'refuse'),
(4, 'reporte'),
(5, 'en cours'),
(6, 'termine');

INSERT INTO appreciation (id_appreciation, type_appreciation, code) VALUES
(1, 'Très bon', 1),
(2, 'Moyen', 2),
(3, 'Insuffisant', 3);

-- ---- Utilisateurs (pour candidats) - on fixe id_utilisateur pour facilité ----
INSERT INTO utilisateurs (id_utilisateur, nom, mdp) VALUES
(1, 'alice', 'mdp1'),
(2, 'bob', 'mdp2'),
(3, 'caroline', 'mdp3'),
(4, 'david', 'mdp4'),
(5, 'evelyne', 'mdp5'),
(6, 'fabrice', 'mdp6'),
(7, 'gina', 'mdp7'),
(8, 'hery', 'mdp8'),
(9, 'isabelle', 'mdp9'),
(10, 'jules', 'mdp10'),
(11, 'karen', 'mdp11'),
(12, 'leo', 'mdp12'),
(13, 'mireille', 'mdp13'),
(14, 'nicolas', 'mdp14'),
(15, 'olivia', 'mdp15'),
(16, 'patrick', 'mdp16'),
(17, 'quentin', 'mdp17'),
(18, 'rita', 'mdp18'),
(19, 'samuel', 'mdp19'),
(20, 'therese', 'mdp20');

-- ---- Personnes (employés puis candidats) : on fixe id_personne pour clarté ----
-- Employés (1..6)
INSERT INTO personnes (id_personne, nom, prenom, date_naissance, contact, lien_image) VALUES
(1, 'Rakoto', 'Jean', '1985-03-15', '0341234567', 'images/jean.jpg'),      -- Gérant
(2, 'Rasoanaivo', 'Claire', '1990-07-22', '0349876543', 'images/claire.jpg'), -- Comptable / admin
(3, 'Randria', 'Paul', '1995-11-05', '0341928374', 'images/paul.jpg'),      -- Magasinier principal
(4, 'Ravel', 'Sophie', '1998-02-12', '0345647382', 'images/sophie.jpg'),   -- Vendeuse
(5, 'Rak', 'Lucas', '1997-09-01', '0348765432', 'images/lucas.jpg'),       -- Caissier
(6, 'Andrianarisoa', 'Lina', '1992-04-10', '0345556677', 'images/lina.jpg');-- RH

-- Candidats (7..26) - 20 candidats
INSERT INTO personnes (id_personne, nom, prenom, date_naissance, contact, lien_image) VALUES
(7, 'Rakotomalala', 'Alice', '1995-02-10', '0331234001', ''),
(8, 'Rasendra', 'Bob', '1994-06-15', '0331234002', ''),
(9, 'Rakotoniaina', 'Caroline', '1996-08-20', '0331234003', ''),
(10, 'Ranaivo', 'David', '1993-12-05', '0331234004', ''),
(11, 'Ranaivomanana', 'Evelyne', '1997-03-22', '0331234005', ''),
(12, 'Andrianarisoa', 'Fabrice', '1995-07-19', '0331234006', ''),
(13, 'Rasamoelina', 'Gina', '1996-01-11', '0331234007', ''),
(14, 'Raharimalala', 'Hery', '1992-09-25', '0331234008', ''),
(15, 'Rafanomezantsoa', 'Isabelle', '1994-04-30', '0331234009', ''),
(16, 'Rabetokotany', 'Jules', '1993-11-12', '0331234010', ''),
(17, 'Rakotomavo', 'Karen', '1995-05-05', '0331234011', ''),
(18, 'Rasoa', 'Leo', '1996-08-18', '0331234012', ''),
(19, 'Rajaonarison', 'Mireille', '1997-02-28', '0331234013', ''),
(20, 'Rakotondrainy', 'Nicolas', '1993-06-09', '0331234014', ''),
(21, 'Rasolofo', 'Olivia', '1994-10-17', '0331234015', ''),
(22, 'Randrianarisoa', 'Patrick', '1995-03-27', '0331234016', ''),
(23, 'Rabe', 'Quentin', '1996-12-03', '0331234017', ''),
(24, 'Rasamison', 'Rita', '1997-07-22', '0331234018', ''),
(25, 'Rakotomaharo', 'Samuel', '1994-01-14', '0331234019', ''),
(26, 'Rasolo', 'Therese', '1993-05-30', '0331234020', '');

-- ---- Profils (métiers en librairie) ----
INSERT INTO profils (id_profil, titre, competences, skills, loisirs, id_diplome, id_filiere, experience_pro, certifications, langues, id_type_contrat, id_departement, est_minimum) VALUES
(1, 'Vendeur', 'Relation client, conseil produit, mise en rayon', 'Communication, patience', 'Lecture, evenements littéraires', 1, 2, 'Débutant accepté', '', 'Français', 1, 1, TRUE),
(2, 'Caissier', 'Encaissement, gestion caisse', 'Rigueur, rapidité', 'Jeux', 1, 1, '1 an en caisse souhaité', '', 'Français', 1, 1, TRUE),
(3, 'Magasinier', 'Gestion de stock, inventaire', 'Organisation, manutention', 'Sport', 8, 5, '1-2 ans en logistique', '', 'Français', 1, 2, TRUE),
(4, 'Comptable', 'Suivi facturation, compta', 'Analytique, confidentialité', 'Sudoku', 5, 4, '2 ans en PME', '', 'Français', 1, 3, TRUE),
(5, 'Gerant', 'Supervision magasin, achats', 'Leadership, planification', 'Lecture economie', 7, 3, '3-5 ans experience', '', 'Français', 1, 5, TRUE);

-- ---- Annonces (ouvertures de postes à la librairie) ----
INSERT INTO annonces (id_annonce, id_profil, titre, date_publication, date_expiration, nombre_poste, lien) VALUES
(1, 1, 'Vendeur(se) en librairie - Au Fil des Pages', CURRENT_DATE, NULL, 2, 'https://librairie.example.com/annonce/vendeur'),
(2, 2, 'Caissier/Caissière - Au Fil des Pages', CURRENT_DATE, NULL, 1, 'https://librairie.example.com/annonce/caissier'),
(3, 3, 'Magasinier - Au Fil des Pages', CURRENT_DATE, NULL, 1, 'https://librairie.example.com/annonce/magasinier'),
(4, 4, 'Comptable - Au Fil des Pages', CURRENT_DATE, NULL, 1, 'https://librairie.example.com/annonce/comptable'),
(5, 5, 'Gérant de magasin - Au Fil des Pages', CURRENT_DATE, NULL, 1, 'https://librairie.example.com/annonce/gerant');

-- ---- Candidats (on associe id_personne -> id_utilisateur) ----
-- (id_candidat auto généré, but we give the columns: id_personne, id_annonce, id_profil, cv_url, poste, id_utilisateur)
INSERT INTO candidats (id_personne, id_annonce, id_profil, cv_url, poste, id_utilisateur) VALUES
(7, 1, 1, 'https://cv.example.com/alice.pdf', 'Vendeur', 1),
(8, 1, 1, 'https://cv.example.com/bob.pdf', 'Vendeur', 2),
(9, 1, 1, 'https://cv.example.com/caroline.pdf', 'Vendeur', 3),
(10, 1, 1, 'https://cv.example.com/david.pdf', 'Vendeur', 4),
(11, 2, 2, 'https://cv.example.com/evelyne.pdf', 'Caissier', 5),
(12, 2, 2, 'https://cv.example.com/fabrice.pdf', 'Caissier', 6),
(13, 3, 3, 'https://cv.example.com/gina.pdf', 'Magasinier', 7),
(14, 3, 3, 'https://cv.example.com/hery.pdf', 'Magasinier', 8),
(15, 4, 4, 'https://cv.example.com/isabelle.pdf', 'Comptable', 9),
(16, 4, 4, 'https://cv.example.com/jules.pdf', 'Comptable', 10),
(17, 5, 5, 'https://cv.example.com/karen.pdf', 'Gerant', 11),
(18, 5, 5, 'https://cv.example.com/leo.pdf', 'Gerant', 12),
(19, 1, 1, 'https://cv.example.com/mireille.pdf', 'Vendeur', 13),
(20, 1, 1, 'https://cv.example.com/nicolas.pdf', 'Vendeur', 14),
(21, 2, 2, 'https://cv.example.com/olivia.pdf', 'Caissier', 15),
(22, 3, 3, 'https://cv.example.com/patrick.pdf', 'Magasinier', 16),
(23, 1, 1, 'https://cv.example.com/quentin.pdf', 'Vendeur', 17),
(24, 1, 1, 'https://cv.example.com/rita.pdf', 'Vendeur', 18),
(25, 4, 4, 'https://cv.example.com/samuel.pdf', 'Comptable', 19),
(26, 5, 5, 'https://cv.example.com/therese.pdf', 'Gerant', 20);

-- ---- Contrats (exemples pour quelques candidats embauchés ou en cours) ----
INSERT INTO contrats (id_contrat, id_candidat, id_type_contrat, url_contrat) VALUES
(1, 17, 1, 'https://contrats.example.com/contrat_karen.pdf'),
(2, 25, 1, 'https://contrats.example.com/contrat_samuel.pdf'),
(3, 7, 2, 'https://contrats.example.com/contrat_alice_cdd.pdf');

-- ---- Employés (personnel en place à la librairie). On fixe id_employe pour mapping facile ----
INSERT INTO employes (id_employe, id_personne, id_contrat, id_departement, poste, date_embauche) VALUES
(1, 1, NULL, 5, 'Gérant', '2020-01-10'),             -- Jean
(2, 2, NULL, 3, 'Comptable', '2021-06-01'),         -- Claire
(3, 3, NULL, 2, 'Magasinier principal', '2022-03-15'), -- Paul
(4, 4, NULL, 1, 'Vendeuse', '2023-05-20'),          -- Sophie
(5, 5, NULL, 1, 'Caissier', '2023-08-10'),          -- Lucas
(6, 6, NULL, 4, 'Responsable RH', '2023-01-05');    -- Lina (responsable RH)

-- ---- Admins (quelques comptes administrateurs) ----
INSERT INTO admins (id_admin, id_employe, nom, mdp, date_affiliation) VALUES
(1, 1, 'admin_jean', 'mdp_admin1', CURRENT_TIMESTAMP),
(2, 2, 'admin_claire', 'mdp_admin2', CURRENT_TIMESTAMP),
(3, 6, 'admin_lina', 'mdp_admin3', CURRENT_TIMESTAMP);

-- ---- Responsable d'entretien (mapping profils -> employés responsables)
-- NOTE: id_responsable est identity; on insère explicitement pour contrôler les ids
INSERT INTO responsable_entretien (id_responsable, id_profil, id_employe, ordre_passage) VALUES
(1, 1, 1, 1),  -- Pour profil Vendeur : Jean (Gérant) comme principal
(2, 1, 6, 2),  -- Pour profil Vendeur : Lina (RH) comme 2ème validateur
(3, 2, 1, 1),  -- Caissier : Jean
(4, 2, 6, 2),  -- Caissier : Lina (RH)
(5, 3, 3, 1),  -- Magasinier : Paul
(6, 3, 6, 2),  -- Magasinier : Lina (RH)
(7, 4, 2, 1),  -- Comptable : Claire
(8, 4, 6, 2),  -- Comptable : Lina (RH)
(9, 5, 1, 1),  -- Gérant : Jean (senior)
(10,5, 6, 2);  -- Gérant : Lina (RH)

-- ---- Disponibilités des responsables (jours: 1=Lundi .. 7=Dimanche) ----
INSERT INTO disponibilite_entretien (id_dispo, id_responsable, heure_debut, heure_fin, jour, est_valide) VALUES
(1, 1, '09:00:00', '11:00:00', 1, TRUE),
(2, 1, '09:00:00', '11:00:00', 3, TRUE),
(3, 2, '13:00:00', '15:00:00', 1, TRUE),
(4, 2, '13:00:00', '15:00:00', 3, TRUE),
(5, 3, '09:30:00', '11:30:00', 2, TRUE),
(6, 3, '09:30:00', '11:30:00', 4, TRUE),
(7, 4, '13:00:00', '15:00:00', 2, TRUE),
(8, 4, '13:00:00', '15:00:00', 4, TRUE),
(9, 5, '10:00:00', '12:00:00', 1, TRUE),
(10,5, '10:00:00', '12:00:00', 3, TRUE),
(11,6, '13:00:00', '15:00:00', 1, TRUE),
(12,6, '13:00:00', '15:00:00', 3, TRUE);

-- ---- Config entretien (durées par département) ----
INSERT INTO config_entretien (id_config_entretien, id_departement, duree_entretien) VALUES
(1, 1, INTERVAL '00:30:00'), -- Vente
(2, 2, INTERVAL '00:40:00'), -- Stock
(3, 3, INTERVAL '01:00:00'), -- Comptabilité
(4, 4, INTERVAL '01:15:00'), -- RH / Direction
(5, 5, INTERVAL '00:45:00'); -- Direction - autre

-- ---- Disponibilités employé (si besoin) ----
INSERT INTO disponibilite_employe (id_dispo, id_employe, heure_debut, heure_fin) VALUES
(1, 1, '08:00:00', '17:00:00'),
(2, 2, '09:00:00', '17:00:00'),
(3, 3, '09:00:00', '18:00:00');

-- ---- Tests (scores d'évaluation pour candidats) ----
INSERT INTO tests (id_test, id_candidat, id_annonce, score_test, date_test) VALUES
(1, 7, 1, 75.50, '2025-10-19'),
(2, 8, 1, 62.00, '2025-10-19'),
(3, 9, 1, 88.00, '2025-10-20'),
(4, 10,1, 55.25, '2025-10-20'),
(5, 11,2, 80.00, '2025-10-23'),
(6, 12,2, 74.25, '2025-10-23'),
(7, 13,3, 78.00, '2025-10-24'),
(8, 14,3, 60.50, '2025-10-25'),
(9, 15,4, 90.00, '2025-10-26'),
(10,16,4, 72.50, '2025-10-26'),
(11,17,5, 95.00, '2025-10-27'),
(12,18,5, 85.50, '2025-10-28');

-- ---- Planning d'entretien (quelques RDV) ----
INSERT INTO planning_entretien (id_entretien, id_candidat, id_responsable, date_heure_entretien, score_entretien, etat, id_appreciation) VALUES
(1, 7, 1, '2025-10-25 10:00:00', 15.0, 2, 1),
(2, 8, 1, '2025-10-25 10:30:00', 12.5, 1, 2),
(3, 9, 2, '2025-10-26 14:00:00', 18.0, 2, 1),
(4, 11, 4, '2025-10-27 09:00:00', 16.0, 2, 1);

-- ---- Historique validation (exemples) ----
INSERT INTO historique_validation (id_historique_validation, id_employe, id_candidat, date_heure_validation, id_etat) VALUES
(1, 1, 7, '2025-10-26 09:00:00', 2),  -- Jean a validé Alice
(2, 6, 8, '2025-10-27 09:30:00', 1);  -- Lina a planifié Bob (ex.)

-- ---- Essais (période d'essai) ----
INSERT INTO essais (id_essai, id_personne, id_contrat, id_etat, date_debut, date_fin) VALUES
(1, 7, 3, 1, '2025-10-20', '2025-11-20'),
(2, 11, 1, 2, '2025-10-21', '2025-11-21');

-- ---- Notifications ----
INSERT INTO notifications (id_notification, id_personne, message, date_notification) VALUES
(1, 7, 'Votre entretien est planifié le 25 octobre.', '2025-10-20 08:00:00'),
(2, 11, 'Votre entretien est prévu le 27 octobre.', '2025-10-22 09:00:00');

-- ---- Messages automatiques ----
INSERT INTO message_automatique (id_message_automatique, message) VALUES
(1, 'Votre candidature a bien été reçue.'),
(2, 'Votre entretien est confirmé.'),
(3, 'Votre candidature a été refusée.');

-- ---- Jours fériés (exemples) ----
INSERT INTO jour_ferie (id_jour_ferie, "date") VALUES
(1, '2025-01-01'),
(2, '2025-03-29'),
(3, '2025-05-01'),
(4, '2025-06-26'),
(5, '2025-08-15'),
(6, '2025-11-01'),
(7, '2025-12-25');

-- ---- Status validation CV (si pas déjà inséré) ----
INSERT INTO etat (nom) VALUES 
('brouillon'),
('en_attente_etape1'), 
('en_attente_etape2'),
('en_attente_etape3'),
('validé'),
('rejeté')
ON CONFLICT DO NOTHING;

-- ---- CV candidats (quelques exemples) ----
INSERT INTO cv_candidats (id_cv_candidats, id_candidat, competences, skills, loisirs, id_diplome, filiere, experience_pro, certifications, langues, date_deposition) VALUES
(1, 7, 'Service client, conseils', 'Communication', 'Lecture', 1, 'Vente', 'Stage 6 mois', '', 'Français', '2025-10-18 10:00:00'),
(2, 9, 'Merchandising, vente', 'Organisation', 'Lecture', 2, 'Vente', '1 an expérience', '', 'Français', '2025-10-19 11:00:00');

-- ---- Validation CV (exemple de similarité) ----
INSERT INTO validation_cv (id_validation_cv, id_candidat, id_cv_candidat, id_status_validation_cv, similarite) VALUES
(1, 7, 1, 1, 92.50),
(2, 9, 2, 1, 88.10);

-- ---- treshold (si besoin) - déjà inséré dans ton script mais pour sécurité ----
INSERT INTO treshold (valeur) VALUES (0.20) ON CONFLICT DO NOTHING;

-- FIN DU SCRIPT
