-- ========================================
-- DONNÉES DE TEST COHÉRENTES - AU FIL DES PAGES
-- ========================================

-- Tables de base sans dépendances
INSERT INTO sexe (type_sexe) VALUES
('Homme'),
('Femme'),
('Non specifie');

INSERT INTO departements (nom) VALUES 
('Direction'),
('Comptabilite'),
('Stock'),
('Vente'),
('Ressources Humaines');

INSERT INTO filieres (nom) VALUES 
('Toutes series'),
('Comptabilite et Finance'),
('Management et Commerce'),
('Logistique'),
('Informatique');

INSERT INTO type_contrats (nom) VALUES
('CDI'),
('CDD'),
('Stage'),
('Freelance'),
('Interim');

INSERT INTO diplomes (nom, niveau) VALUES
('Brevet', -3),
('Bac', 0),
('BTS / DUT (Bacc+2)', 2),
('Licence (Bacc+3)', 3),
('Master (Bacc+5)', 5);

INSERT INTO etat (nom) VALUES
('En attente'),
('Valide'),
('Refuse'),
('En cours'),
('Termine');

INSERT INTO appreciation (type_appreciation, code) VALUES
('Excellent', 5),
('Tres bon', 4),
('Bon', 3),
('Moyen', 2),
('Insuffisant', 1);

-- Tables avec caractères problématiques simplifiés
INSERT INTO evenements (nom_evenement) VALUES
('Entretien embauche'),
('Formation interne'),
('Evaluation annuelle'),
('Reunion equipe'),
('Seminaire');

INSERT INTO conge_type (nom, description, nombre_jour, deductible_sur_salaire, deductible_sur_conge) VALUES
('Conge annuel', 'Conge paye annuel', 30, FALSE, TRUE),
('Conge maladie', 'Arret maladie avec certificat medical', 15, FALSE, TRUE),
('Conge exceptionnel', 'Evenements familiaux', 5, FALSE, TRUE),
('Conge sans solde', 'Conge non paye', 0, TRUE, FALSE),
('Conge maternite', 'Conge maternite', 90, FALSE, TRUE);

INSERT INTO abscence_type_penalite (nom, description, montant) VALUES
('Retard simple', 'Retard de moins de 30 minutes', 5000),
('Absence non justifiee', 'Absence sans justification', 20000),
('Depart anticipe', 'Depart sans autorisation', 10000),
('Absence prolongee', 'Absence de plus de 3 jours', 50000),
('Retard repete', 'Plus de 3 retards dans le mois', 15000);

INSERT INTO status_validation_cv (statut) VALUES
('En attente'),
('Valide'),
('Refuse'),
('A corriger'),
('Accepte avec reserves');

INSERT INTO type_prime (libelle) VALUES
('Prime de performance'),
('Prime d anciennete'),
('Prime de fin d annee'),
('Prime de projet'),
('Prime de participation');

INSERT INTO parametre (libelle, pourcentage) VALUES
('Taux horaire normal', 100.00),
('Taux heures supplementaires', 125.00),
('Taux travail dimanche', 150.00),
('Taux jours feries', 200.00),
('Taux nuit', 120.00);

INSERT INTO smig (montant, date_application) VALUES
(250000, '2024-01-01'),
(260000, '2024-06-01'),
(270000, '2025-01-01'),
(280000, '2025-06-01'),
(290000, '2026-01-01');

INSERT INTO irsa (min, max, pourcentage) VALUES
(0, 350000, 0),
(350001, 400000, 5),
(400001, 500000, 10),
(500001, 600000, 15),
(600001, 9999999, 20);

INSERT INTO seuil_tolerance (valeur) VALUES
(5.00),
(10.00),
(15.00),
(3.00),
(7.00);

INSERT INTO treshold (valeur, date_treshold) VALUES
(80.00, '2024-01-01'),
(85.00, '2024-06-01'),
(75.00, '2025-01-01'),
(90.00, '2025-06-01'),
(82.00, '2026-01-01');

INSERT INTO message_automatique (message) VALUES
('Votre demande de conge a ete approuvee'),
('Votre absence necessite une justification'),
('Votre solde de conge est faible'),
('Rappel : Pointage requis avant 8h15'),
('Votre entretien annuel est programme');

INSERT INTO api (nom, cle_api) VALUES
('API RH', 'rh_123456_secret'),
('API Paie', 'paie_789012_secret'),
('API Pointage', 'pointage_345678_secret'),
('API Conges', 'conge_901234_secret'),
('API Recrutement', 'recrutement_567890_secret');

INSERT INTO jour_ferie (date) VALUES
('2024-01-01'),
('2024-03-29'),
('2024-05-01'),
('2024-06-26'),
('2024-08-15');

-- Personnes
INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image, id_sexe) VALUES
('Rakoto', 'Jean', '1985-03-12', '0341234567', 'images/jean.jpg', 1),
('Rasoanaivo', 'Marie', '1990-07-25', '0342345678', 'images/marie.jpg', 2),
('Randriamahenina', 'Paul', '1988-11-02', '0343456789', 'images/paul.jpg', 1),
('Andriantsitoha', 'Lova', '1995-01-15', '0344567890', 'images/lova.jpg', 1),
('Rakotondrazaka', 'Hery', '1992-05-30', '0345678901', 'images/hery.jpg', 1);

-- Utilisateurs
INSERT INTO utilisateurs (nom, mdp, date_inscription) VALUES
('admin', 'admin123', '2024-01-01'),
('manager', 'manager123', '2024-01-02'),
('comptable', 'comptable123', '2024-01-03'),
('vendeur', 'vendeur123', '2024-01-04'),
('stock', 'stock123', '2024-01-05');

-- Profils
INSERT INTO profils (titre, competences, skills, loisirs, id_diplome, id_filiere, experience_pro, id_type_contrat, est_minimum, id_departement) VALUES
('Directeur', 'Gestion d equipe, Strategie', 'Leadership, Communication', 'Lecture, Golf', 5, 3, '10 ans en management', 1, TRUE, 1),
('Comptable', 'Comptabilite generale, Fiscalite', 'Excel, Sage', 'Jeux de strategie', 4, 2, '5 ans en cabinet', 1, TRUE, 2),
('Responsable stock', 'Gestion inventaire, Logistique', 'Organisation, ERP', 'Sport, Bricolage', 3, 4, '7 ans en logistique', 1, TRUE, 3),
('Vendeur senior', 'Vente, Relation client', 'Negociation, Communication', 'Lecture, Theatre', 3, 3, '4 ans en vente', 1, TRUE, 4),
('Assistant RH', 'Recrutement, Administration', 'Organisation, Communication', 'Social, Voyages', 4, 3, '3 ans en RH', 1, TRUE, 5);

-- ProfilsCV
INSERT INTO profilsCV (titre, competences, skills, loisirs, id_diplome, filiere, experience_pro, id_type_contrat, est_minimum, id_departement) VALUES
('Directeur', 'Gestion d equipe, Strategie', 'Leadership, Communication', 'Lecture, Golf', 5, 'Management', '10 ans en management', 1, TRUE, 1),
('Comptable', 'Comptabilite generale, Fiscalite', 'Excel, Sage', 'Jeux de strategie', 4, 'Finance', '5 ans en cabinet', 1, TRUE, 2),
('Responsable stock', 'Gestion inventaire, Logistique', 'Organisation, ERP', 'Sport, Bricolage', 3, 'Logistique', '7 ans en logistique', 1, TRUE, 3),
('Vendeur senior', 'Vente, Relation client', 'Negociation, Communication', 'Lecture, Theatre', 3, 'Commerce', '4 ans en vente', 1, TRUE, 4),
('Assistant RH', 'Recrutement, Administration', 'Organisation, Communication', 'Social, Voyages', 4, 'Management', '3 ans en RH', 1, TRUE, 5);

-- Annonces
INSERT INTO annonces (id_profil, titre, date_publication, date_expiration, nombre_poste, lien) VALUES
(1, 'Recherche Directeur General', '2024-01-15', '2024-02-15', 1, 'aufildespages.com/emploi/directeur'),
(2, 'Poste de Comptable', '2024-01-20', '2024-02-20', 2, 'aufildespages.com/emploi/comptable'),
(3, 'Responsable Stock', '2024-01-25', '2024-02-25', 1, 'aufildespages.com/emploi/stock'),
(4, 'Vendeur Experimente', '2024-02-01', '2024-03-01', 3, 'aufildespages.com/emploi/vendeur'),
(5, 'Assistant RH', '2024-02-05', '2024-03-05', 1, 'aufildespages.com/emploi/rh');

-- Questions
INSERT INTO questions (question, id_profil, note) VALUES
('Comment gerez-vous un conflit entre collaborateurs ?', 1, 10.0),
('Quelles sont les principales declarations fiscales ?', 2, 8.5),
('Comment optimiser la gestion des stocks ?', 3, 9.0),
('Comment fidéliser un client mecontent ?', 4, 7.5),
('Quelle est la procedure de recrutement standard ?', 5, 8.0);

-- Réponses questions
INSERT INTO reponses_question (id_question, reponse, est_correct) VALUES
(1, 'J organise une mediation pour comprendre les positions', TRUE),
(1, 'J ignore le conflit', FALSE),
(2, 'TVA, impot sur les societes, declarations sociales', TRUE),
(2, 'Seulement la TVA', FALSE),
(3, 'Mise en place de la methode ABC et rotation des stocks', TRUE),
(3, 'Commander plus de produits', FALSE);

-- Candidats
INSERT INTO candidats (id_personne, id_annonce, id_profil, cv_url, poste, id_utilisateur) VALUES
(1, 1, 1, 'cv_jean.pdf', 'Directeur', 1),
(2, 2, 2, 'cv_marie.pdf', 'Comptable', 2),
(3, 3, 3, 'cv_paul.pdf', 'Responsable Stock', 3),
(4, 4, 4, 'cv_lova.pdf', 'Vendeur', 4),
(5, 5, 5, 'cv_hery.pdf', 'Assistant RH', 5);

-- Tests
INSERT INTO tests (id_candidat, id_annonce, score_test, date_test) VALUES
(1, 1, 85.5, '2024-02-10'),
(2, 2, 92.0, '2024-02-12'),
(3, 3, 78.5, '2024-02-15'),
(4, 4, 88.0, '2024-02-18'),
(5, 5, 91.5, '2024-02-20');

-- CV Candidats
INSERT INTO cv_candidats (id_candidat, competences, skills, loisirs, id_diplome, filiere, experience_pro, date_deposition) VALUES
(1, 'Gestion, Strategie', 'Leadership', 'Golf', 5, 'Management', '10 ans direction', '2024-01-20'),
(2, 'Comptabilite, Audit', 'Sage, Excel', 'Strategie', 4, 'Finance', '5 ans comptabilite', '2024-01-22'),
(3, 'Logistique, Stock', 'ERP, Organisation', 'Sport', 3, 'Logistique', '7 ans logistique', '2024-01-25'),
(4, 'Vente, Negociation', 'Communication', 'Theatre', 3, 'Commerce', '4 ans vente', '2024-01-28'),
(5, 'RH, Recrutement', 'Organisation', 'Voyages', 4, 'Management', '3 ans RH', '2024-01-30');

-- Validation CV
INSERT INTO validation_cv (id_candidat, id_cv_candidat, id_status_validation_cv, similarite) VALUES
(1, 1, 2, 95.5),
(2, 2, 2, 92.0),
(3, 3, 1, 85.0),
(4, 4, 2, 88.5),
(5, 5, 4, 76.0);

-- Contrats
INSERT INTO contrats (id_candidat, id_type_contrat, url_contrat) VALUES
(1, 1, 'contrats/contrat_jean.pdf'),
(2, 1, 'contrats/contrat_marie.pdf'),
(3, 1, 'contrats/contrat_paul.pdf'),
(4, 1, 'contrats/contrat_lova.pdf'),
(5, 1, 'contrats/contrat_hery.pdf');

-- Employés
INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche, nombre_conge, salaire_base) VALUES
(1, 1, 1, 'Directeur General', '2020-01-15', 30, 5000000),
(2, 2, 2, 'Comptable Principal', '2021-06-01', 25, 2000000),
(3, 3, 3, 'Responsable Stock', '2022-03-10', 25, 1800000),
(4, 4, 4, 'Vendeur Senior', '2022-08-22', 22, 1500000),
(5, 5, 5, 'Assistant RH', '2023-02-14', 20, 1200000);

-- Admins
INSERT INTO admins (id_employe, nom, mdp, date_affiliation) VALUES
(1, 'admin_directeur', 'directeur123', '2020-01-15'),
(2, 'admin_comptable', 'comptable123', '2021-06-01');

-- Connexion Employés
INSERT INTO connexEmployes (idEmploye, mdp) VALUES
(1, 'emp123'),
(2, 'emp456'),
(3, 'emp789'),
(4, 'emp012'),
(5, 'emp345');

-- Horaires Employés
INSERT INTO horaires_employe (id_employe, jour_semaine, debut_travail, fin_travail) VALUES
(1, 1, '08:00', '17:00'),
(1, 2, '08:00', '17:00'),
(1, 3, '08:00', '17:00'),
(1, 4, '08:00', '17:00'),
(1, 5, '08:00', '17:00');

-- Disponibilité Employés
INSERT INTO disponibilite_employe (id_employe, heure_debut, heure_fin) VALUES
(1, '08:00', '12:00'),
(1, '13:00', '17:00'),
(2, '08:00', '12:00'),
(2, '13:00', '17:00'),
(3, '08:00', '12:00');

-- Pointage
INSERT INTO pointage (id_employe, connexion, deconnexion, duree_session) VALUES
(1, '2024-03-01 08:00:00', '2024-03-01 17:00:00', '09:00:00'),
(2, '2024-03-01 08:05:00', '2024-03-01 16:55:00', '08:50:00'),
(3, '2024-03-01 08:10:00', '2024-03-01 17:05:00', '08:55:00'),
(4, '2024-03-01 07:55:00', '2024-03-01 17:10:00', '09:15:00'),
(5, '2024-03-01 08:15:00', '2024-03-01 17:00:00', '08:45:00');

-- Pointage Journalier
INSERT INTO pointage_journalier (id_employe, date_pointage, retard, heures_supp, pause, heures_travaillees) VALUES
(1, '2024-03-01', '00:05:00', '00:30:00', '01:00:00', '08:00:00'),
(2, '2024-03-01', '00:00:00', '00:15:00', '01:00:00', '08:15:00'),
(3, '2024-03-01', '00:10:00', '00:45:00', '01:00:00', '08:35:00'),
(4, '2024-03-01', '00:00:00', '01:00:00', '01:00:00', '09:00:00'),
(5, '2024-03-01', '00:15:00', '00:00:00', '01:00:00', '07:45:00');

-- Configuration Heures Supplémentaires
INSERT INTO heure_supplementaire_config (nombre_premieres_heures) VALUES
(10),
(15),
(20),
(25),
(30);

-- Heures Supplémentaires
INSERT INTO heures_supplementaire (id_employe, nombre_heure_effectue, mois, annee, numero_semaine) VALUES
(1, 5.5, 3, 2024, 9),
(2, 3.0, 3, 2024, 9),
(3, 8.0, 3, 2024, 9),
(4, 12.5, 3, 2024, 9),
(5, 2.0, 3, 2024, 9);

-- Historique Heures Supplémentaires
INSERT INTO heures_supplementaire_historique (id_heure_supp, nombre_heure_effectue, mois, annee, numero_semaine) VALUES
(1, 5.5, 2, 2024, 6),
(2, 3.0, 2, 2024, 7),
(3, 7.0, 2, 2024, 8),
(4, 10.0, 2, 2024, 9),
(5, 1.5, 2, 2024, 10);

-- Salaires Historique
INSERT INTO salaire_historique (salaire, id_employe) VALUES
(5000000, 1),
(2000000, 2),
(1800000, 3),
(1500000, 4),
(1200000, 5);

-- Primes
INSERT INTO prime (id_type_prime, pourcentage, id_employe) VALUES
(1, 10.0, 1),
(2, 5.0, 2),
(3, 15.0, 3),
(4, 8.0, 4),
(5, 12.0, 5);

-- Préavis
INSERT INTO preavis (id_employe, date_debut_preavis, date_fin_preavis) VALUES
(3, '2024-03-01', '2024-05-01'),
(4, '2024-04-01', '2024-06-01');

-- ========================================
-- DONNÉES SPÉCIFIQUES ABSENCES ET CONGÉS
-- ========================================

-- Absences
INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
(2, '2024-03-05 08:00:00', '2024-03-05 17:00:00', FALSE, 'Absence non justifiee'),
(4, '2024-03-10 08:00:00', '2024-03-10 12:00:00', TRUE, 'Rendez-vous medical'),
(5, '2024-03-15 08:00:00', '2024-03-15 17:00:00', FALSE, 'Retard non justifie'),
(3, '2024-03-20 13:00:00', '2024-03-20 17:00:00', TRUE, 'Demarches administratives'),
(1, '2024-03-25 08:00:00', '2024-03-25 10:00:00', FALSE, 'Absence courte non autorisee');

-- Demandes de Congé
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Conge annuel famille', 1, '2024-02-15', '2024-04-01', '2024-04-15', 2, 1),
('Conge maladie', 2, '2024-02-20', '2024-03-10', '2024-03-12', 2, 2),
('Conge exceptionnel mariage', 3, '2024-02-25', '2024-05-01', '2024-05-03', 1, 3),
('Conge sans solde projet perso', 4, '2024-03-01', '2024-06-01', '2024-06-07', 2, 4),
('Conge maternite', 5, '2024-03-05', '2024-07-01', '2024-09-28', 2, 5);

-- Historique Validation Congé
INSERT INTO conge_historique_validation (id_demande, id_employe, date_validation) VALUES
(1, 1, '2024-02-16'),
(2, 2, '2024-02-21'),
(3, 1, '2024-02-26'),
(4, 1, '2024-03-02'),
(5, 1, '2024-03-06');

-- Historique Congé
INSERT INTO conge_historique (nombres_abscence_attribue, id_employe) VALUES
(30, 1),
(25, 2),
(25, 3),
(22, 4),
(20, 5);

-- Solde Congé
INSERT INTO conge_solde (id_employe, id_type_conge, solde, annee) VALUES
(1, 1, 25.0, 2024),
(2, 1, 20.0, 2024),
(3, 1, 18.0, 2024),
(4, 1, 15.0, 2024),
(5, 1, 22.0, 2024);

-- Suivi Absence/Congé (soit absence, soit congé, rarement les deux)
INSERT INTO abscence_conge_suivi (id_demande, id_abscence, id_type, id_employe, nombre_conge, annee, penalite_appliquee, id_type_penalite, dateMouvement) VALUES
(1, NULL, 1, 1, 15, 2024, FALSE, NULL, '2024-02-16'),
(NULL, 1, NULL, 2, 0, 2024, TRUE, 2, '2024-03-06'),
(2, NULL, 2, 2, 3, 2024, FALSE, NULL, '2024-02-21'),
(NULL, 3, NULL, 5, 0, 2024, TRUE, 1, '2024-03-16'),
(3, NULL, 3, 3, 3, 2024, FALSE, NULL, '2024-02-26');

-- ========================================
-- DONNÉES COMPLÉMENTAIRES POUR LES AUTRES TABLES
-- ========================================

-- Responsables Entretien
INSERT INTO responsable_entretien (id_profil, id_employe, ordre_passage) VALUES
(1, 1, 1),
(5, 5, 2),
(1, 2, 1),
(5, 3, 2),
(1, 4, 1);

-- Disponibilité Entretien
INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour, est_valide) VALUES
(1, '09:00', '12:00', 1, TRUE),
(1, '14:00', '17:00', 1, TRUE),
(2, '10:00', '12:00', 2, TRUE),
(3, '09:00', '11:00', 3, TRUE),
(4, '15:00', '17:00', 4, TRUE);

-- Planning Entretien
INSERT INTO planning_entretien (id_candidat, id_responsable, date_heure_entretien, score_entretien, etat, id_appreciation) VALUES
(1, 1, '2024-02-28 10:00:00', 85.5, 2, 4),
(2, 2, '2024-03-01 14:30:00', 92.0, 2, 5),
(3, 3, '2024-03-05 09:00:00', 78.0, 2, 3),
(4, 4, '2024-03-08 11:00:00', 88.5, 2, 4),
(5, 5, '2024-03-12 15:30:00', 91.0, 2, 5);

-- Configuration Entretien
INSERT INTO config_entretien (id_departement, duree_entretien) VALUES
(1, '01:00:00'),
(2, '00:45:00'),
(3, '00:30:00'),
(4, '00:45:00'),
(5, '01:00:00');

-- Historique Validation
INSERT INTO historique_validation (id_employe, id_candidat, date_heure_validation, id_etat) VALUES
(1, 1, '2024-02-29', 2),
(5, 2, '2024-03-02', 2),
(1, 3, '2024-03-06', 2),
(5, 4, '2024-03-09', 2),
(1, 5, '2024-03-13', 2);

-- Historique Mobilité
INSERT INTO historique_mobilite (id_candidat, id_evenement, id_profil, id_departement, date_evenement, support) VALUES
(1, 1, 1, 1, '2024-02-28', 'Entretien physique'),
(2, 1, 2, 2, '2024-03-01', 'Entretien visio'),
(3, 1, 3, 3, '2024-03-05', 'Entretien physique'),
(4, 1, 4, 4, '2024-03-08', 'Entretien physique'),
(5, 1, 5, 5, '2024-03-12', 'Entretien visio');

-- Essais
INSERT INTO essais (id_personne, id_contrat, id_etat, date_debut, date_fin) VALUES
(1, 1, 4, '2020-01-15', '2020-04-15'),
(2, 2, 5, '2021-06-01', '2021-08-01'),
(3, 3, 5, '2022-03-10', '2022-06-10'),
(4, 4, 5, '2022-08-22', '2022-11-22'),
(5, 5, 4, '2023-02-14', '2023-05-14');

-- Notifications
INSERT INTO notifications (id_personne, message, date_notification) VALUES
(1, 'Votre conge a ete approuve', '2024-02-16'),
(2, 'Votre absence du 5 mars necessite une justification', '2024-03-06'),
(3, 'Votre entretien est programme pour le 5 mars', '2024-02-26'),
(4, 'Rappel : Reunion d equipe demain 10h', '2024-03-07'),
(5, 'Votre periode d essai se termine le 14 mai', '2024-04-14');

-- Insertion des données de référence
INSERT INTO type_competence (libelle, description) VALUES
    ('Hard Skill', 'Compétences techniques spécifiques et mesurables'),
    ('Soft Skill', 'Compétences comportementales et relationnelles'),
    ('Langue', 'Compétences linguistiques'),
    ('Certification', 'Compétences certifiées par un organisme')
ON CONFLICT DO NOTHING;

INSERT INTO source_evaluation (libelle, description) VALUES
    ('auto-evaluation', 'Evaluation par le employé lui-même'),
    ('manager-evaluation', 'Evaluation par le manager direct'),
    ('rh-evaluation', 'Evaluation par les ressources humaines'),
    ('formation', 'Validation via une formation'),
    ('certification', 'Validation par une certification'),
    ('test-technique', 'Validation par un test technique')
ON CONFLICT DO NOTHING;

-- Données de test pour les compétences
INSERT INTO competences (nom, description, domaine, id_type_competence) VALUES
    ('PHP', 'Langage de programmation côté serveur', 'Développement', 1),
    ('JavaScript', 'Langage de programmation côté client', 'Développement', 1),
    ('SQL', 'Langage de requête structuré', 'Base de données', 1),
    ('Gestion de projet', 'Méthodologies agiles et waterfall', 'Management', 2),
    ('Communication', 'Communication interpersonnelle et présentations', 'Soft Skills', 2),
    ('Anglais', 'Langue anglaise professionnelle', 'Langues', 3),
    ('Python', 'Langage de programmation polyvalent', 'Développement', 1)
ON CONFLICT DO NOTHING;

-- -----------------------------
-- 1) Employé-compétences (liaisons)
-- -----------------------------
-- On crée des compétences mesurées pour les employés (id explicites pour faciliter
-- la référence dans l'historique). Les id_competence supposés :
-- 1=PHP, 2=JavaScript, 3=SQL, 4=Gestion de projet, 5=Communication, 6=Anglais, 7=Python


INSERT INTO employe_competences (id, id_employe, id_competence, niveau, id_source, date_mesure, valide, id_employe_validateur, date_validation) VALUES
(1, 1, 1, 5, 2, '2024-02-01 09:00:00', TRUE, 1, '2024-02-02 10:00:00'),
(2, 1, 6, 4, 1, '2024-02-05 11:15:00', TRUE, 1, '2024-02-06 09:30:00'),
(3, 2, 3, 4, 2, '2024-03-02 14:00:00', TRUE, 2, '2024-03-03 10:00:00'),
(4, 2, 5, 3, 1, '2024-03-02 14:30:00', TRUE, 2, '2024-03-04 09:45:00'),
(5, 3, 7, 3, 6, '2024-03-05 09:00:00', FALSE, NULL, NULL),
(6, 3, 3, 4, 2, '2024-03-06 10:30:00', TRUE, 1, '2024-03-07 08:30:00'),
(7, 4, 4, 2, 4, '2024-03-08 16:00:00', FALSE, NULL, NULL),
(8, 4, 5, 3, 1, '2024-03-09 09:30:00', TRUE, 4, '2024-03-10 11:00:00'),
(9, 5, 6, 5, 5, '2024-03-10 08:45:00', TRUE, 1, '2024-03-11 09:00:00'),
(10,5, 2, 2, 3, '2024-03-12 13:20:00', FALSE, NULL, NULL);


-- -----------------------------
-- 2) Historique des liaisons employe_competences (simuler quelques opérations)
-- -----------------------------
INSERT INTO employe_competences_historique (id_liaison, id_employe, id_competence, niveau, id_source, date_mesure, valide, id_employe_validateur, operation_type, operation_timestamp, id_employe_operation) VALUES
(1, 1, 1, 5, 2, '2024-02-01 09:00:00', TRUE, 1, 'INSERT', '2024-02-01 09:01:00', 1),
(2, 1, 6, 4, 1, '2024-02-05 11:15:00', TRUE, 1, 'INSERT', '2024-02-05 11:16:00', 1),
(3, 2, 3, 4, 2, '2024-03-02 14:00:00', TRUE, 2, 'INSERT', '2024-03-02 14:01:00', 2),
(4, 2, 5, 3, 1, '2024-03-02 14:30:00', TRUE, 2, 'INSERT', '2024-03-02 14:31:00', 2),
(5, 3, 7, 3, 6, '2024-03-05 09:00:00', FALSE, NULL, 'INSERT', '2024-03-05 09:01:00', 3),
(6, 3, 3, 4, 2, '2024-03-06 10:30:00', TRUE, 1, 'INSERT', '2024-03-06 10:31:00', 1),
(7, 4, 4, 2, 4, '2024-03-08 16:00:00', FALSE, NULL, 'INSERT', '2024-03-08 16:01:00', 4),
(8, 4, 5, 3, 1, '2024-03-09 09:30:00', TRUE, 4, 'INSERT', '2024-03-09 09:31:00', 4),
(9, 5, 6, 5, 5, '2024-03-10 08:45:00', TRUE, 1, 'INSERT', '2024-03-10 08:46:00', 1),
(10,5, 2, 2, 3, '2024-03-12 13:20:00', FALSE, NULL, 'INSERT', '2024-03-12 13:21:00', 5);


-- Simuler quelques mises à jour (UPDATE) enregistrées dans l'historique
INSERT INTO employe_competences_historique (id_liaison, id_employe, id_competence, niveau, id_source, date_mesure, valide, id_employe_validateur, operation_type, operation_timestamp, id_employe_operation) VALUES
(3, 2, 3, 5, 2, '2024-04-01 10:00:00', TRUE, 2, 'UPDATE', '2024-04-01 10:05:00', 2),
(6, 3, 3, 3, 2, '2024-04-10 09:00:00', TRUE, 1, 'UPDATE', '2024-04-10 09:10:00', 1);


-- -----------------------------
-- 3) Historique des compétences (table competences_historique)
-- -----------------------------
-- Simuler des opérations sur les compétences (INSERT / UPDATE / DELETE)
INSERT INTO competences_historique (id_competence, nom, description, domaine, id_type_competence, operation_type, operation_timestamp, id_employe_operation) VALUES
(1, 'PHP', 'Langage de programmation côté serveur (modifié: ajout frameworks)', 'Développement', 1, 'UPDATE', '2024-03-01 12:00:00', 1),
(2, 'JavaScript', 'Langage de script côté client (ajout tests unitaires)', 'Développement', 1, 'UPDATE', '2024-03-05 15:30:00', 2),
(8, NULL, 'Compétence temporaire supprimée lors d\'un nettoyage', NULL, NULL, 'DELETE', '2024-03-15 08:00:00', 1);


-- -----------------------------
-- 4) Quelques inserts complémentaires pour d'autres tables encore vides ou utiles
-- -----------------------------
-- Ajouter un administrateur supplémentaire (utilise l'employe 3)
INSERT INTO admins (id_employe, nom, mdp, date_affiliation, date_fin_affiliation) VALUES
(3, 'admin_stock', 'stock2024', '2022-03-10 08:00:00', NULL),
(4, 'admin_vendeur', 'vendeur2024', '2022-08-22 09:00:00', '2024-12-31 23:59:59');


-- Ajouter un enregistrement dans heure_supplementaire_config pour une date historique
INSERT INTO heure_supplementaire_config (date_creation, nombre_premieres_heures) VALUES
('2024-01-01 00:00:00', 12),
('2024-06-01 00:00:00', 15);


-- Ajouter des lignes de salaire_historique additionnelles
INSERT INTO salaire_historique (salaire, date_creation, id_employe) VALUES
(5200000, '2024-06-01 09:00:00', 1),
(2100000, '2024-07-01 09:00:00', 2);


-- Ajouter quelques notifications supplémentaires
INSERT INTO notifications (id_personne, message, date_notification) VALUES
(1, 'Nouvelle politique de conge publiee', '2024-06-01 08:00:00'),
(3, 'Formation obligatoire : securite au travail', '2024-05-12 12:00:00');


-- -----------------------------
-- 5) Données pour la table competences (si besoin d'exemples plus détaillés)
-- -----------------------------
-- (ces inserts sont idempotents si la table contient déjà les competences mentionnees)
INSERT INTO competences (nom, description, domaine, id_type_competence) VALUES
('Gestion du temps', 'Priorisation, planification et respect des delais', 'Management', 2),
('Docker', 'Conteneurisation applications', 'DevOps', 1)
ON CONFLICT DO NOTHING;