-- Fichier de données de test divers et concrets pour la base "aufildespages"
-- Contient INSERTs ordonnés pour respecter les contraintes de clés étrangères.
-- Utilise ids explicites pour faciliter la lecture et les références.

-- ================== TABLES DE RÉFÉRENCE ==================

-- sexe
INSERT INTO sexe (id_sexe, type_sexe) VALUES
(1, 'Masculin'),
(2, 'Féminin'),
(3, 'Non-binaire');

-- diplomes
INSERT INTO diplomes (id_diplome, nom, niveau) VALUES
(1, 'Baccalauréat', 0),
(2, 'Licence Professionnelle', 2),
(3, 'Master en Informatique', 5),
(4, 'Doctorat', 8);

-- filieres
INSERT INTO filieres (id_filiere, nom) VALUES
(1, 'Informatique'),
(2, 'Management'),
(3, 'Ressources Humaines'),
(4, 'Comptabilité');

-- departements
INSERT INTO departements (id_departement, nom) VALUES
(1, 'Informatique'),
(2, 'Ressources Humaines'),
(3, 'Finance'),
(4, 'Marketing');

-- type_contrats
INSERT INTO type_contrats (id_type_contrat, nom) VALUES
(1, 'CDI'),
(2, 'CDD'),
(3, 'Stage'),
(4, 'Freelance');

-- etat
INSERT INTO etat (id_etat, nom) VALUES
(1, 'ACTIF'),
(2, 'INACTIF'),
(3, 'EN ATTENTE');

-- appreciation
INSERT INTO appreciation (id_appreciation, type_appreciation, code) VALUES
(1, 'Excellent', 5),
(2, 'Bon', 4),
(3, 'Satisfaisant', 3),
(4, 'Insuffisant', 2),
(5, 'Non conforme', 1);

-- evenements
INSERT INTO evenements (id_evenement, nom_evenement) VALUES
(1, 'Recrutement massif Q1'),
(2, 'Evaluation annuelle 2024'),
(3, 'Séminaire équipe dev');

-- conge_type
INSERT INTO conge_type (id_type, nom, description, nombre_jour, deductible_sur_salaire, deductible_sur_conge) VALUES
(1, 'Congé annuel', 'Congé payé annuel', 20, TRUE, FALSE),
(2, 'Congé maladie', 'Congé pour raison médicale', 30, FALSE, FALSE),
(3, 'Congé sans solde', 'Congé non payé', 0, TRUE, TRUE);

-- abscence_type_penalite
INSERT INTO abscence_type_penalite (id_type_penalite, nom, description, montant) VALUES
(1, 'Arrivée tardive', 'Retard de moins de 30 minutes', 5.00),
(2, 'Absence non justifiée', 'Absence sans justificatif', 50.00);

-- status_validation_cv
INSERT INTO status_validation_cv (id_status_validation_cv, statut) VALUES
(1, 'A_VALIDER'),
(2, 'VALIDE'),
(3, 'REJETE');

-- type_prime
INSERT INTO type_prime (id, libelle) VALUES
(1, 'Prime de performance'),
(2, 'Prime d’ancienneté'),
(3, 'Prime exceptionnelle');

-- parametre
INSERT INTO parametre (id, libelle, pourcentage) VALUES
(1, 'Cotisation sociale', 15.00),
(2, 'Contribution employeur', 7.50);

-- smig
INSERT INTO smig (id, montant, date_application) VALUES
(1, 250.00, '2024-01-01'),
(2, 300.00, '2025-01-01');

-- irsa
INSERT INTO irsa (id, min, max, pourcentage, date_creation) VALUES
(1, 0, 1000, 0.00, NOW()),
(2, 1000.01, 5000, 5.00, NOW()),
(3, 5000.01, 10000, 10.00, NOW());

-- seuil_tolerance
INSERT INTO seuil_tolerance (id_seuil, valeur, date_creation) VALUES
(1, 5.00, NOW());

-- treshold
INSERT INTO treshold (id_treshold, valeur, date_treshold) VALUES
(1, 2.50, NOW());

-- message_automatique
INSERT INTO message_automatique (id_message_automatique, message) VALUES
(1, 'Votre demande a bien été reçue.'),
(2, 'Rappel: pensez à mettre à jour votre profil.');

-- api
INSERT INTO api (id_api, nom, cle_api) VALUES
(1, 'API Recrutement', 'cle-test-1234'),
(2, 'API Pointage', 'pointage-key-9876');

-- jour_ferie
INSERT INTO jour_ferie (id_jour_ferie, date) VALUES
(1, '2025-01-01'),
(2, '2025-05-01'),
(3, '2025-12-25');

-- ================== DONNÉES PERSONNES & PROFILS ==================

-- personnes
INSERT INTO personnes (id_personne, nom, prenom, date_naissance, contact, lien_image, id_sexe) VALUES
(1, 'Andrianarisoa', 'Jean', '1990-04-12', '+261336001122', 'https://example.com/img/jean.jpg', 1),
(2, 'Rabe', 'Marie', '1988-11-30', '+261336001133', 'https://example.com/img/marie.png', 2),
(3, 'Razafindrakoto', 'Hery', '1995-07-05', '+261336004455', NULL, 1),
(4, 'Rakoto', 'Aina', '1992-09-20', '+261336007788', 'https://example.com/img/aina.jpg', 2),
(5, 'Solo', 'Sam', '1998-02-02', '+261336009900', NULL, 3);

-- profils
INSERT INTO profils (id_profil, titre, competences, skills, loisirs, id_diplome, id_filiere, experience_pro, certifications, langues, id_type_contrat, id_departement, est_minimum) VALUES
(1, 'Développeur Back-end', 'SQL, PostgreSQL, API design', 'Python, Node.js', 'Randonnée, Lecture', 3, 1, '5 ans en développement backend', 'OCI, AWS', 'Français, Anglais', 1, 1, TRUE),
(2, 'Chargé RH', 'Recrutement, Paie', 'Excel, Paie', 'Théâtre', 2, 3, '3 ans en RH', '', 'Français', 1, 2, FALSE),
(3, 'Comptable', 'Comptabilité générale, TVA', 'Sage, Excel', 'Football', 2, 4, '4 ans cabinet', 'IFRS Basic', 'Français', 1, 3, FALSE),
(4, 'Stagiaire Marketing', 'Community management', 'Canva, Social Media', 'Photographie', 1, 4, 'Stage de 6 mois', '', 'Français', 3, 4, TRUE);

-- ================== ANNONCES, QUESTIONS ET RÉPONSES ==================

-- annonces
INSERT INTO annonces (id_annonce, id_profil, titre, date_publication, date_expiration, nombre_poste, lien) VALUES
(1, 1, 'Développeur Back-end senior', '2025-02-01', '2025-03-01', 2, 'https://jobs.example.com/1'),
(2, 2, 'Chargé RH généraliste', '2025-01-15', '2025-02-15', 1, 'https://jobs.example.com/2'),
(3, 4, 'Stage Marketing été 2025', '2025-03-01', '2025-04-01', 3, 'https://jobs.example.com/3');

-- questions
INSERT INTO questions (id_question, question, id_profil, note) VALUES
(1, 'Expliquez la normalisation en base de données', 1, 5.00),
(2, 'Quelles sont les étapes du recrutement?', 2, 3.00),
(3, 'Comment mesurer le ROI d’une campagne social media?', 4, 4.00);

-- reponses_question
INSERT INTO reponses_question (id_reponse, id_question, reponse, est_correct) VALUES
(1, 1, 'Séparer les données pour éviter la redondance', TRUE),
(2, 1, 'Mettre tout en une seule table', FALSE),
(3, 2, 'Publication, sélection, entretien, intégration', TRUE),
(4, 2, 'Publier uniquement sur LinkedIn', FALSE),
(5, 3, 'Comparer coût vs conversions et lifetime value', TRUE);

-- ================== CANDIDATS, TESTS, CONTRATS ==================

-- candidats
INSERT INTO candidats (id_candidat, id_personne, id_annonce, id_profil, cv_url, poste, id_utilisateur) VALUES
(1, 1, 1, 1, 'https://cv.example.com/jean.pdf', 'Backend Developer', 10),
(2, 2, 2, 2, 'https://cv.example.com/marie.pdf', 'HR Officer', 11),
(3, 5, 3, 4, 'https://cv.example.com/sam.pdf', 'Marketing Intern', 12);

-- tests
INSERT INTO tests (id_test, id_candidat, id_annonce, score_test, date_test) VALUES
(1, 1, 1, 85.50, '2025-02-10'),
(2, 2, 2, 72.00, '2025-01-25'),
(3, 3, 3, 68.00, '2025-03-15');

-- contrats
INSERT INTO contrats (id_contrat, id_candidat, id_type_contrat, url_contrat) VALUES
(1, 1, 1, 'https://contracts.example.com/contrat-jean.pdf'),
(2, 2, 2, 'https://contracts.example.com/contrat-marie.pdf');

-- ================== EMPLOYÉS, CONNEXION & HORAIRES ==================

-- employes
INSERT INTO employes (id_employe, id_personne, id_contrat, id_departement, poste, date_embauche, nombre_conge, salaire_base) VALUES
(1, 1, 1, 1, 'Lead Backend', '2023-06-01', 18, 1500.00),
(2, 2, 2, 2, 'Responsable RH', '2022-03-15', 20, 1200.00),
(3, 4, NULL, 4, 'Assistant Marketing', '2024-07-01', 15, 600.00);

-- admins (note: pas de contrainte FK dans la définition fournie)
INSERT INTO admins (id_admin, id_employe, nom, mdp, date_affiliation, date_fin_affiliation) VALUES
(1, 1, 'AdminJean', 'hashedpwd1', NOW(), NULL);

-- connexEmployes
INSERT INTO connexEmployes (idEmploye, mdp) VALUES
(1, 'hashedpwd_emp1'),
(2, 'hashedpwd_emp2'),
(3, 'hashedpwd_emp3');

-- horaires_employe
INSERT INTO horaires_employe (id_employe, jour_semaine, debut_travail, fin_travail, seuil_retard) VALUES
(1, 1, '08:30:00', '17:30:00', '00:10:00'),
(1, 2, '08:30:00', '17:30:00', '00:10:00'),
(2, 1, '09:00:00', '17:00:00', '00:05:00'),
(3, 1, '10:00:00', '16:00:00', '00:15:00');

-- ================== POINTAGE ET JOURNALIER ==================

-- pointage (exemples de sessions)
INSERT INTO pointage (id_pointage, id_employe, connexion, deconnexion) VALUES
(1, 1, '2025-02-11 08:25:00', '2025-02-11 17:40:00'),
(2, 2, '2025-02-11 09:10:00', '2025-02-11 17:05:00'),
(3, 1, '2025-02-12 08:35:00', '2025-02-12 17:20:00');

-- pointage_journalier
INSERT INTO pointage_journalier (id_pointage_journalier, id_employe, date_pointage, retard, heures_supp, pause, heures_travaillees) VALUES
(1, 1, '2025-02-11', '00:00:00', '00:00:00', '00:45:00', '08:55:00'),
(2, 2, '2025-02-11', '00:10:00', '00:00:00', '00:30:00', '07:25:00');

-- ================== HEURES SUPPLEMENTAIRES ==================

-- heures_supplementaire_config
INSERT INTO heures_supplementaire_config (id, date_creation, nombre_premieres_heures) VALUES
(1, NOW(), 2);

-- heures_supplementaire
INSERT INTO heures_supplementaire (id, id_employe, nombre_heure_effectue, mois, annee, numero_semaine) VALUES
(1, 1, 3.50, 2, 2025, 7),
(2, 2, 1.75, 2, 2025, 7);

-- heures_supplementaire_historique
INSERT INTO heures_supplementaire_historique (id, id_heure_supp, nombre_heure_effectue, mois, annee, numero_semaine) VALUES
(1, 1, 3.50, 2, 2025, 7);

-- prime
INSERT INTO prime (id, id_type_prime, pourcentage, id_employe) VALUES
(1, 1, 10.00, 1),
(2, 2, 5.00, 2);

-- salaire_historique
INSERT INTO salaire_historique (id_salaire_historique, salaire, date_creation, id_employe) VALUES
(1, 1400.00, '2024-06-01', 1),
(2, 1200.00, '2022-03-15', 2),
(3, 600.00, '2024-07-01', 3);

-- ================== CONGÉS & ABSCENCES ==================

-- abscence
INSERT INTO abscence (id_abscence, id_employe, debut, fin, est_autorise, justificatif) VALUES
(1, 2, '2025-02-05 09:00:00', '2025-02-05 12:00:00', TRUE, 'Certificat médical'),
(2, 3, '2025-02-20 00:00:00', '2025-02-22 23:59:00', FALSE, NULL);

-- conge_demande
INSERT INTO conge_demande (id_demande, description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
(1, 'Congé annuel 5 jours', 1, '2025-01-10 10:00:00', '2025-04-15 00:00:00', '2025-04-19 23:59:00', 2, 1),
(2, 'Congé maladie 2 jours', 2, '2025-02-04 09:00:00', '2025-02-05 09:00:00', '2025-02-06 18:00:00', 1, 2);

-- conge_historique_validation
INSERT INTO conge_historique_validation (id_historique_validation, id_demande, id_employe, date_validation) VALUES
(1, 2, 1, '2025-02-04 12:00:00');

-- conge_solde
INSERT INTO conge_solde (id_solde, id_employe, id_type_conge, solde, annee) VALUES
(1, 1, 1, 15.0, 2025),
(2, 2, 1, 18.0, 2025),
(3, 3, 1, 10.0, 2025);

-- ================== PERFORMANCE / EVALUATIONS ==================

-- employe_evaluation_periodes
INSERT INTO employe_evaluation_periodes (id_periode, nom, description, frequence_mois) VALUES
(1, 'Trimestrielle', 'Evaluation tous les 3 mois', 3),
(2, 'Annuelle', 'Evaluation annuelle', 12);

-- employe_criteres_evaluation
INSERT INTO employe_criteres_evaluation (id_critere, nom, description, poids) VALUES
(1, 'Qualité du travail', 'Précision et fiabilité', 50.00),
(2, 'Respect des délais', 'Ponctualité dans les livrables', 30.00),
(3, 'Collaboration', 'Travail en équipe', 20.00);

-- employe_evaluations
INSERT INTO employe_evaluations (id_evaluation, employe_id, periode_id, date_generation, date_evaluation, statut, score_total, manager_id, created_at, updated_at) VALUES
(1, 1, 2, '2025-01-01', '2025-01-15', 'TERMINEE', 8.50, 2, NOW(), NOW()),
(2, 2, 2, '2025-01-01', NULL, 'PREVUE', NULL, 1, NOW(), NOW());

-- employe_evaluations_details
INSERT INTO employe_evaluations_details (id_detail, evaluation_id, critere_id, note, commentaire) VALUES
(1, 1, 1, 8.5, 'Travail de qualité mais quelques oublis mineurs'),
(2, 1, 2, 9.0, 'Livraison toujours à l’heure'),
(3, 1, 3, 7.0, 'Bonne collaboration mais peut s’améliorer');

-- employe_performance_aggregations
INSERT INTO employe_performance_aggregations (id_aggregation, employe_id, annee, score_moyen, score_max, score_min, total_evaluations) VALUES
(1, 1, 2025, 8.50, 9.00, 7.00, 1);

-- ================== COMPÉTENCES, POSTES, FORMATIONS ==================

-- competences
INSERT INTO competences (id_competence, nom, description, domaine) VALUES
(1, 'SQL avancé', 'Optimisation, indexation, requêtes complexes', 'Base de données'),
(2, 'Gestion de projet', 'Planification, suivi', 'Management'),
(3, 'Social Media', 'Création et suivi de campagnes', 'Marketing');

-- employe_competences
INSERT INTO employe_competences (id, employe_id, competence_id, niveau, date_obtention) VALUES
(1, 1, 1, 8.50, '2021-05-10'),
(2, 2, 2, 7.00, '2019-10-01'),
(3, 3, 3, 6.00, '2024-08-01');

-- postes
INSERT INTO postes (id_poste, nom, description) VALUES
(1, 'Lead Backend', 'Pilote technique des équipes backend'),
(2, 'Responsable RH', 'Gestion du personnel et paie'),
(3, 'Stagiaire Marketing', 'Appui aux campagnes');

-- competences_postes
INSERT INTO competences_postes (id, poste_id, competence_id, niveau_requis) VALUES
(1, 1, 1, 4),
(2, 2, 2, 3),
(3, 3, 3, 2);

-- formations
INSERT INTO formations (id_formation, titre, description, competence_id, niveau_cible) VALUES
(1, 'SQL Optimisation', 'Perfectionnement SQL', 1, 9.00),
(2, 'Leadership pour managers', 'Développement du leadership', 2, 7.00);

-- employe_formations
INSERT INTO employe_formations (id, employe_id, formation_id, statut, date_assignation) VALUES
(1, 1, 1, 'TERMINE', '2024-11-20'),
(2, 2, 2, 'PLANIFIE', '2025-03-01');

-- ================== AUTRES ==================

-- utilisateurs
INSERT INTO utilisateurs (id_utilisateur, nom, mdp, date_inscription, date_sortie) VALUES
(10, 'jean_user', 'u_hashed_10', '2023-05-01', NULL),
(11, 'marie_user', 'u_hashed_11', '2022-03-15', NULL),
(12, 'sam_user', 'u_hashed_12', '2025-01-10', NULL);

-- notifications
INSERT INTO notifications (id_notification, id_personne, message, date_notification) VALUES
(1, 1, 'Votre évaluation est disponible.', '2025-01-16 10:00:00'),
(2, 2, 'Nouveau message du service RH.', '2025-02-01 08:30:00');

-- planning_entretien
INSERT INTO planning_entretien (id_entretien, id_candidat, id_responsable, date_heure_entretien, score_entretien, etat, id_appreciation) VALUES
(1, 1, 2, '2025-02-15 10:00:00', 88.00, 1, 1),
(2, 3, 2, '2025-03-20 14:00:00', NULL, 0, 3);

-- EOF