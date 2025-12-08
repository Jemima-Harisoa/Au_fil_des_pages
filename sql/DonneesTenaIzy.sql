-- ========================================
-- INSERTIONS DANS L'ORDRE LOGIQUE
-- ========================================


-- Tables de base sans dépendances
-- Tables de base sans dépendances
INSERT INTO sexe (type_sexe) VALUES 
  ('Masculin'),
  ('Feminin'),
  ('Autre');

INSERT INTO departements (nom) VALUES 
  ('Direction'),
  ('Comptabilite'),
  ('Stock'),
  ('Vente');

INSERT INTO filieres (nom) VALUES
  ('Toutes series'),
  ('Comptabilite et Finance'),
  ('Management et Commerce'),
  ('Logistique');

INSERT INTO type_contrats (nom) VALUES
 ('CDI'),
 ('CDD'),
 ('Stage'),
 ('Freelance'),
 ('Interim'),
 ('Alternance'),
 ('Consultant');

INSERT INTO diplomes (nom, niveau) VALUES
('Brevet', -3),
('Bac', 0),
('BTS / DUT (Bacc+2)', 2),
('Licence (Bacc+3)', 3),
('Master (Bacc+5)', 5),
('Doctorat', 6);

INSERT INTO utilisateurs (nom, mdp) VALUES
('ema', 'mdp1'),
('jean', 'mdp2'),
('sophie', 'mdp3');

INSERT INTO message_automatique (message) 
VALUES ('Merci d avoir complete le test. Vos reponses ont ete enregistrees.Les responsables d Au fil des Page vont analyser vos resultats et vous serez recontacte prochainement.');

INSERT INTO jour_ferie("date") VALUES
('2025-01-01'), -- Jour de l'an
('2025-03-29'), -- Fête nationale
('2025-05-01'), -- Fête du travail
('2025-06-26'), -- Indépendance
('2025-08-15'), -- Assomption
('2025-11-01'), -- Toussaint
('2025-12-25'), -- Noël
('2025-02-20'); -- Noël
-- Table etat
INSERT INTO etat (nom) VALUES
('En attente'),
('Validé'),
('Refusé'),
('En cours'),
('Terminé');

-- Table appreciation
INSERT INTO appreciation (type_appreciation, code) VALUES
('Excellent', 5),
('Très bien', 4),
('Bien', 3),
('Passable', 2),
('Insuffisant', 1);

-- Table evenements
INSERT INTO evenements (nom_evenement) VALUES
('Embauche'),
('Promotion'),
('Mutation'),
('Démission'),
('Licenciement'),
('Fin de contrat'),
('Retraite');

-- Table status_validation_cv
INSERT INTO status_validation_cv (statut) VALUES
('En attente'),
('Validé'),
('Refusé'),
('À revoir');

-- Table type_prime
INSERT INTO type_prime (libelle) VALUES
('Prime de rendement'),
('Prime d''ancienneté'),
('Prime de fin d''année'),
('Prime exceptionnelle'),
('Prime de responsabilité');

-- Table mère : personnes
INSERT INTO personnes (nom, prenom, date_naissance, contact, lien_image) VALUES
  ('Rakoto', 'Jean', '1985-03-12', '0341234567', 'images/jean.jpg'),
  ('Rasoanaivo', 'Marie', '1990-07-25', '0342345678', 'images/marie.jpg'),
  ('Randriamahenina', 'Paul', '1988-11-02', '0343456789', 'images/paul.jpg'),
  ('Andriantsitoha', 'Lova', '1995-01-15', '0344567890', 'images/lova.jpg'),
  ('Rakotondrazaka', 'Hery', '1992-05-30', '0345678901', 'images/hery.jpg'),
  ('Lia', 'Mia', '1995-05-12', '0341234560', 'https://img.com/lia.jpg'),
  ('Rasoa', 'Sophie', '1998-09-21', '0349876543', 'https://img.com/sophie.jpg'),
  ('Andry', 'Michel', '1990-11-03', '0345556667', 'https://img.com/michel.jpg'),
  ('Hanitra', 'Lina', '2000-01-15', '0342223334', 'https://img.com/lina.jpg');

-- CORRECTION: Employes - utilisation de la syntaxe correcte
-- Note: Nous mettons id_contrat à NULL car les contrats ne sont pas encore insérés
INSERT INTO employes (id_personne, id_contrat, id_departement, poste, date_embauche, nombre_conge, salaire_base) VALUES
  (1, NULL, 1, 'Directeur', '2020-01-15', NULL, NULL),
  (2, NULL, 2, 'Comptable', '2021-06-01', NULL, NULL),
  (3, NULL, 1, 'Caissier', '2023-05-01', 20, 1500.5),
  (4, NULL, 2, 'Magasinier', '2024-01-15', 15, 1800.75), 
  (5, NULL, 2, 'Magasinier', '2024-01-15', 15, 1800.75);

-- Admins (après employes)
INSERT INTO admins (id_employe, nom, mdp) VALUES
  (1, 'RD', 'mdp1'),
  (2, 'RC', 'mdp2');

-- Connexion employes (après employes)
INSERT INTO connexEmployes (idEmploye, mdp) VALUES
(1, 'emp123'),
(2, 'emp123'),
(3, 'emp123'),
(4, 'emp123'),
(5, 'emp123');

-- Tables profils (après leurs tables de référence)
INSERT INTO profilsCV (
  titre, competences, skills, loisirs, id_diplome, filiere, experience_pro, certifications, langues, id_type_contrat, est_minimum, id_departement
) VALUES
('Caissier / Caissiere',
 'Accueillir et encaisser les clients; Assurer la rapidite et la fiabilite des transactions; Maintenir un espace de caisse organise et propre; Appliquer les procedures de securite et de controle',
 'Rigueur et honnetete; Rapidite d''execution; Gestion du stress',
 'Jeux de logique; Activites demandant precision',
 2, 'Commerce et Gestion', '1 an en caisse ou grande surface', NULL, NULL, 1, TRUE, NULL),

('Comptable',
 'Assurer la tenue de la comptabilite generale et analytique; Etablir les bilans et declarations fiscales; Analyser les flux financiers; Conseiller la direction sur la gestion budgetaire',
 'Confidentialite; Esprit analytique; Minutie; Gestion des priorites',
 'Jeux strategiques; Sudoku; Activites de gestion',
 3, 'Finance et Comptabilite', '2 a 3 ans d''experience en cabinet ou PME', NULL, NULL, 1, TRUE, NULL),

('Gerant / Manager',
 'Superviser et coordonner les equipes; Prendre des decisions strategiques; Assurer la rentabilite et le developpement de l''activite; Gerer les conflits et favoriser la cohesion',
 'Leadership; Prise de decision; Gestion des conflits; Vision strategique',
 'Lecture sur l''economie et entrepreneuriat; Sport collectif (leadership)',
 4, 'Management et Strategie', '3 a 5 ans d''experience en commerce ou gestion', NULL, NULL, 1, TRUE, NULL),

('Magasinier',
 'Receptionner et stocker les marchandises; Preparer les commandes; Assurer le suivi des inventaires; Respecter les consignes de securite',
 'Organisation; Fiabilite; Resistance physique; Esprit d''equipe',
 'Sport (endurance, fitness); Bricolage (sens pratique)',
 2, 'Logistique et Approvisionnement', '1 a 2 ans en gestion de stock', NULL, NULL, 1, TRUE, NULL),

('Vendeur / Vendeuse',
 'Accueillir et conseiller les clients; Assurer la mise en rayon et l''attractivite du magasin; Conclure les ventes et fideliser la clientele; Participer aux inventaires et a la gestion des stocks',
 'Sens du relationnel; Communication claire; Patience et ecoute; Dynamisme',
 'Lecture (interet pour les livres); Activites sociales (theatre, clubs de lecture)',
 2, 'Commerce et Relation Client', 'Debutant accepte, experience en relation client est un plus', NULL, NULL, 1, TRUE, NULL);

INSERT INTO profilsCV (
  titre, competences, skills, loisirs, id_diplome, filiere, experience_pro, certifications, langues, id_type_contrat, est_minimum
) VALUES
('Caissier / Caissiere',
 'Accueillir et encaisser les clients; Assurer la rapidite et la fiabilite des transactions; Maintenir un espace de caisse organise et propre; Appliquer les procedures de securite et de controle',
 'Rigueur et honnetete; Rapidite d''execution; Gestion du stress',
 'Jeux de logique; Activites demandant precision',
 2, 'Toutes series', '1 an en caisse ou grande surface', NULL, NULL, 1, TRUE),

('Comptable',
 'Assurer la tenue de la comptabilite generale et analytique; Etablir les bilans et declarations fiscales; Analyser les flux financiers; Conseiller la direction sur la gestion budgetaire',
 'Confidentialite; Esprit analytique; Minutie; Gestion des priorites',
 'Jeux strategiques; Sudoku; Activites de gestion',
 3, 'Comptabilite et Finance', '2 a 3 ans d''experience en cabinet ou PME', NULL, NULL, 1, TRUE),

('Gerant / Manager',
 'Superviser et coordonner les equipes; Prendre des decisions strategiques; Assurer la rentabilite et le developpement de l''activite; Gerer les conflits et favoriser la cohesion',
 'Leadership; Prise de decision; Gestion des conflits; Vision strategique',
 'Lecture sur l''economie et entrepreneuriat; Sport collectif (leadership)',
 4, 'Management et Commerce', '3 a 5 ans d''experience en commerce ou gestion', NULL, NULL, 1, TRUE),

('Magasinier',
 'Receptionner et stocker les marchandises; Preparer les commandes; Assurer le suivi des inventaires; Respecter les consignes de securite',
 'Organisation; Fiabilite; Resistance physique; Esprit d''equipe',
 'Sport (endurance, fitness); Bricolage (sens pratique)',
 2, 'Logistique', '1 a 2 ans en gestion de stock', NULL, NULL, 1, TRUE),

('Vendeur / Vendeuse',
 'Accueillir et conseiller les clients; Assurer la mise en rayon et l''attractivite du magasin; Conclure les ventes et fideliser la clientele; Participer aux inventaires et a la gestion des stocks',
 'Sens du relationnel; Communication claire; Patience et ecoute; Dynamisme',
 'Lecture (interet pour les livres); Activites sociales (theatre, clubs de lecture)',
 2, 'Toutes series', 'Debutant accepte, experience en relation client est un plus', NULL, NULL, 1, TRUE),

('Developpeur Web',
 'PHP, JavaScript, SQL', 
 'React, Node.js', 
 'Lecture, Jeux video', 
 4, 'Management et Commerce', '2 ans en startup', 'Certification PHP Zend', 'Francais, Anglais', 1, TRUE),

('Charge de Recrutement',
 'Sourcing, Entretiens', 
 'Communication, Negociation', 
 'Voyages', 
 5, 'Management et Commerce', '3 ans en cabinet RH', 'Certification RH CIPD', 'Francais, Anglais', 2, TRUE),

('Analyste Financier',
 'Analyse, Reporting', 
 'Excel, PowerBI', 
 'Echecs', 
 5, 'Comptabilite et Finance', '5 ans en banque', 'CFA Level 1', 'Francais, Anglais', 1, TRUE),

('Community Manager',
 'Strategie digitale', 
 'Photoshop, SEO', 
 'Photographie', 
 4, 'Management et Commerce', '2 ans en agence digitale', 'Google Digital Marketing', 'Francais, Anglais', 1, TRUE),

('Agent Logistique',
 'Gestion stock, Transport', 
 'SAP, Excel', 
 'Football', 
 3, 'Logistique', '3 ans en entrepot', 'Formation Supply Chain', 'Francais', 2, TRUE);

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

-- Tables dépendantes de profils
INSERT INTO questions (question, id_profil, note) VALUES
('Comment gerer une file d attente en caisse ?', 1, 4.50),
('Que faire si un billet est suspect ?', 1, 5.00),
('Comment assurer la rapidite sans erreurs ?', 1, 5.50),
('Quelles sont les regles de securite a respecter ?', 1, 4.50),
('Comment reagir face a un client mecontent ?', 1, 6.00),
('Quelles sont les principales obligations fiscales d une PME ?', 2, 5.50),
('Comment preparer un bilan comptable ?', 2, 6.00),
('Pourquoi la separation des comptes est importante ?', 2, 5.00),
('Comment gerer un controle fiscal ?', 2, 6.00),
('Quels outils logiciels sont utiles en comptabilite ?', 2, 4.50),
('Comment motiver une equipe en periode de forte activite ?', 3, 6.00),
('Quelle est la meilleure maniere de gerer un conflit entre employes ?', 3, 5.50),
('Comment evaluer la rentabilite d une activite ?', 3, 5.00),
('Quelle strategie adopter pour fideliser les clients ?', 3, 6.00),
('Comment deleguer efficacement des taches ?', 3, 4.50),
('Quelle est la meilleure methode pour organiser un stock ?', 4, 4.50),
('Comment assurer la securite lors de la manipulation de charges ?', 4, 5.50),
('Que faire en cas d erreur dans une preparation de commande ?', 4, 5.00),
('Quels outils utiliser pour realiser un inventaire ?', 4, 4.50),
('Comment optimiser l espace de stockage ?', 4, 6.00),
('Comment accueillir un client dans un magasin ?', 5, 4.50),
('Quelle est la meilleure technique pour convaincre un client indecis ?', 5, 5.50),
('Comment assurer une mise en rayon attractive ?', 5, 5.00),
('Que faire pour fideliser un client ?', 5, 6.00),
('Comment participer efficacement a un inventaire ?', 5, 4.50);

INSERT INTO reponses_question (id_question, reponse, est_correct) VALUES
(1, 'Rester courtois et rapide, orienter si besoin', TRUE),
(1, 'Ignorer les clients et continuer lentement', FALSE),
(1, 'Demander aux clients d attendre dehors', FALSE),
(2, 'Verifier avec un detecteur de faux billets', TRUE),
(2, 'Accepter sans verification', FALSE),
(2, 'Refuser systematiquement tous les billets', FALSE),
(3, 'Scanner correctement et compter la monnaie avec attention', TRUE),
(3, 'Parler au telephone en meme temps', FALSE),
(3, 'Encaisser sans verifier les articles', FALSE),
(4, 'Ne jamais laisser la caisse ouverte et respecter les consignes', TRUE),
(4, 'Partager son code de caisse avec un collegue', FALSE),
(4, 'Laisser la caisse ouverte pour aller vite', FALSE),
(5, 'Rester calme, ecouter et proposer une solution', TRUE),
(5, 'Repondre agressivement', FALSE),
(5, 'Ignorer totalement le client', FALSE),
(6, 'Declarer TVA, impots et charges sociales', TRUE),
(6, 'Ne rien declarer du tout', FALSE),
(6, 'Declarer uniquement les benefices', FALSE),
(7, 'Classer les operations, etablir le bilan actif/passif', TRUE),
(7, 'Deviner les chiffres sans justificatifs', FALSE),
(7, 'Mettre uniquement les depenses', FALSE),
(8, 'Eviter la confusion entre finances pro et perso', TRUE),
(8, 'Pour cacher des fonds', FALSE),
(8, 'Parce que c est facultatif', FALSE),
(9, 'Fournir les justificatifs et collaborer', TRUE),
(9, 'Ignorer les demandes de l administration', FALSE),
(9, 'Detruire les factures compromettantes', FALSE),
(10, 'Logiciels comme Sage, Ciel, ou Excel avance', TRUE),
(10, 'Bloc-notes papier uniquement', FALSE),
(10, 'Un logiciel de musique', FALSE),
(11, 'Fixer des objectifs clairs et valoriser les efforts', TRUE),
(11, 'Ignorer les plaintes et faire pression', FALSE),
(11, 'Eviter toute communication', FALSE),
(12, 'Ecouter les deux parties et trouver un compromis', TRUE),
(12, 'Sanctionner uniquement le plus faible', FALSE),
(12, 'Ignorer totalement le probleme', FALSE),
(13, 'Comparer revenus et depenses, calculer marge', TRUE),
(13, 'Se baser uniquement sur l instinct', FALSE),
(13, 'Consulter des rumeurs de marche', FALSE),
(14, 'Offrir un service de qualite un suivi', TRUE),
(14, 'Augmenter les prix sans raison', FALSE),
(14, 'Eviter tout contact apres la vente', FALSE),
(15, 'Attribuer selon competences et suivre l avancement', TRUE),
(15, 'Donner tout le travail a une seule personne', FALSE),
(15, 'Ne jamais expliquer les taches', FALSE),
(16, 'Classer, etiqueter et tenir un inventaire a jour', TRUE),
(16, 'Laisser les produits sans suivi', FALSE),
(16, 'Melanger tous les articles pour aller plus vite', FALSE),
(17, 'Utiliser un equipement adapte et respecter la posture', TRUE),
(17, 'Soulever seul meme les charges lourdes', FALSE),
(17, 'Ignorer les regles de securite', FALSE),
(18, 'Signaler et corriger l erreur rapidement', TRUE),
(18, 'Ignorer et expédier quand meme', FALSE),
(18, 'Accuser un collegue', FALSE),
(19, 'Utiliser un logiciel de gestion de stock ou Excel', TRUE),
(19, 'Tout compter de memoire', FALSE),
(19, 'Eviter de faire des inventaires', FALSE),
(20, 'Utiliser des rayonnages et organiser par categories', TRUE),
(20, 'Empiler tout sans logique', FALSE),
(20, 'Mettre tout au sol pour gagner du temps', FALSE),
(21, 'Sourire, saluer et proposer de l aide', TRUE),
(21, 'Ignorer le client jusqu a ce qu il parle', FALSE),
(21, 'Le regarder sans rien dire', FALSE),
(22, 'Ecouter ses besoins et proposer une solution adaptee', TRUE),
(22, 'Mettre la pression pour acheter vite', FALSE),
(22, 'Ignorer ses hesitations et passer au suivant', FALSE),
(23, 'Disposer les produits de facon claire et attrayante', TRUE),
(23, 'Tout empiler sans logique', FALSE),
(23, 'Laisser les rayons vides', FALSE),
(24, 'Proposer des offres personnalisees et un bon suivi', TRUE),
(24, 'Ne plus jamais parler au client apres l achat', FALSE),
(24, 'Lui vendre un produit de mauvaise qualite', FALSE),
(25, 'Compter soigneusement et verifier les references', TRUE),
(25, 'Deviner les quantites', FALSE),
(25, 'Noter uniquement les produits visibles', FALSE);

-- Tables d'entretien
INSERT INTO config_entretien (id_departement, duree_entretien) VALUES
(1, INTERVAL '00:30:00'), -- Vente : entretiens courts (30 min)
(2, INTERVAL '00:40:00'), -- Stock : un peu plus long pour tester organisation et logistique
(3, INTERVAL '01:00:00'), -- Comptabilite : plus technique, 1 heure
(4, INTERVAL '01:15:00'); -- Direction : entretien approfondi (1h15)



-- Horaires (après employes)
INSERT INTO horaires_employe (id_employe, jour_semaine, debut_travail, fin_travail, seuil_retard) VALUES 
(1, 1, '08:00:00', '12:00:00', '00:05:00'),
(1, 1, '13:00:00', '17:00:00', '00:05:00');

-- Pointage (après employes)
INSERT INTO pointage (id_employe, connexion, deconnexion) VALUES
(1, '2025-11-17 08:05:00', '2025-11-17 12:00:00'),
(1, '2025-11-17 12:45:00', '2025-11-17 17:00:00'),
(1, '2025-11-19 13:00:00', '2025-11-19 18:00:00'),
(1, '2025-11-20 08:00:00', '2025-11-20 16:30:00');

INSERT INTO pointage (id_employe, connexion, deconnexion)
VALUES
(1, '2025-11-21 08:10:00', '2025-11-21 12:45:00');

-- Absences (après employes)
INSERT INTO abscence (id_employe, debut, fin, est_autorise, justificatif) VALUES
(2, '2024-03-05 08:00:00', '2024-03-05 17:00:00', FALSE, 'Absence non justifiee'),
(4, '2024-03-10 08:00:00', '2024-03-10 12:00:00', TRUE, 'Rendez-vous medical'),
(5, '2024-03-15 08:00:00', '2024-03-15 17:00:00', FALSE, 'Retard non justifie'),
(3, '2024-03-20 13:00:00', '2024-03-20 17:00:00', TRUE, 'Demarches administratives'),
(1, '2024-03-25 08:00:00', '2024-03-25 10:00:00', FALSE, 'Absence courte non autorisee');

-- ATTENTION: Les insertions suivantes nécessitent que les tables de référence soient créées
-- Nous devons d'abord insérer les données dans les tables conge_type et abscence_type_penalite
-- avant de pouvoir insérer les données qui en dépendent

-- Insérer d'abord les types de congé nécessaires
INSERT INTO conge_type (nom, description, nombre_jour, deductible_sur_salaire, deductible_sur_conge) VALUES
('Congé annuel', 'Congé annuel payé', 30, FALSE, FALSE),
('Congé maladie', 'Congé pour maladie', 30, FALSE, FALSE),
('Congé exceptionnel', 'Congé pour événement exceptionnel', 5, FALSE, FALSE),
('Congé sans solde', 'Congé non payé', 0, TRUE, TRUE),
('Congé maternité', 'Congé maternité', 90, FALSE, FALSE);

-- Insérer les types de pénalité d'absence
INSERT INTO abscence_type_penalite (nom, description, montant) VALUES
('Avertissement verbal', 'Avertissement verbal pour absence non justifiée', 0),
('Retenue sur salaire', 'Retenue sur salaire pour absence non justifiée', 50000);

-- Maintenant nous pouvons insérer les demandes de congé
-- Congés (après employes et conge_type)
INSERT INTO conge_demande (description, id_employe, date_demande, date_debut, date_fin, niveau_validation, id_type_conge) VALUES
('Conge annuel famille', 1, '2024-02-15', '2024-04-01', '2024-04-15', 2, 1),
('Conge maladie', 2, '2024-02-20', '2024-03-10', '2024-03-12', 2, 2),
('Conge exceptionnel mariage', 3, '2024-02-25', '2024-05-01', '2024-05-03', 1, 3),
('Conge sans solde projet perso', 4, '2024-03-01', '2024-06-01', '2024-06-07', 2, 4),
('Conge maternite', 2, '2024-03-05', '2024-07-01', '2024-09-28', 2, 5);

INSERT INTO conge_historique_validation (id_demande, id_employe, date_validation) VALUES
(1, 1, '2024-02-16'),
(2, 2, '2024-02-21'),
(3, 1, '2024-02-26'),
(4, 1, '2024-03-02'),
(3, 1, '2024-03-06');

INSERT INTO conge_historique (nombres_abscence_attribue, id_employe) VALUES
(30, 1),
(25, 2),
(25, 3),
(22, 4);

INSERT INTO conge_solde (id_employe, id_type_conge, solde, annee) VALUES
(1, 1, 25.0, 2024),
(2, 1, 20.0, 2024),
(3, 1, 18.0, 2024),
(4, 1, 15.0, 2024);

INSERT INTO abscence_conge_suivi (id_demande, id_abscence, id_type, id_employe, nombre_conge, annee, penalite_appliquee, id_type_penalite, dateMouvement) VALUES
(NULL, 5, NULL, 1, 0, 2024, TRUE, 1, '2024-03-25 10:00:00'),
(1, NULL, 1, 1, 15, 2024, FALSE, NULL, '2024-02-16'),
(NULL, 1, NULL, 2, 0, 2024, TRUE, 2, '2024-03-06'),
(2, NULL, 2, 2, 3, 2024, FALSE, NULL, '2024-02-21'),
(NULL, 3, NULL, 4, 0, 2024, TRUE, 1, '2024-03-16'),
(3, NULL, 3, 3, 3, 2024, FALSE, NULL, '2024-02-26');

-- CORRECTION: L'employe 5 n'existe pas (nous n'avons que 4 employés)
INSERT INTO responsable_entretien (id_profil, id_employe, ordre_passage) VALUES
(1, 1, 1),  -- Vendeur senior evalue e
(1, 2, 2),  -- Gerant valide ensuite (utiliser employe 2 au lieu de 5)
(3, 3, 1),  -- Magasinier principal
(3, 2, 2),  -- Gerant valide ensuite (utiliser employe 2 au lieu de 5)
(4, 4, 1),  -- Comptable principal
(4, 2, 2),  -- Gerant valide ensuite (utiliser employe 2 au lieu de 5)
(5, 2, 1);  -- Gerant en direct (utiliser employe 2 au lieu de 5)

INSERT INTO disponibilite_entretien (id_responsable, heure_debut, heure_fin, jour) VALUES
(1, '09:00', '12:00', 1),
(1, '09:00', '12:00', 2),
(1, '09:00', '12:00', 3),
(1, '09:00', '12:00', 4),
(1, '09:00', '12:00', 5),
(2, '14:00', '17:00', 1),
(2, '14:00', '17:00', 2),
(2, '14:00', '17:00', 3),
(2, '14:00', '17:00', 4),
(2, '14:00', '17:00', 5),
(3, '08:00', '11:00', 1),
(3, '08:00', '11:00', 2),
(3, '08:00', '11:00', 3),
(3, '08:00', '11:00', 4),
(3, '08:00', '11:00', 5),
(4, '10:00', '13:00', 2),
(4, '10:00', '13:00', 3),
(4, '10:00', '13:00', 4),
(4, '10:00', '13:00', 5),
(4, '10:00', '13:00', 6),
(5, '15:00', '18:00', 1),
(5, '15:00', '18:00', 2),
(5, '15:00', '18:00', 3),
(5, '15:00', '18:00', 4),
(5, '15:00', '18:00', 5),
(5, '15:00', '18:00', 6),
(5, '15:00', '18:00', 7);

-- Tables de compétences (après employes)
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

INSERT INTO competences (nom, description, domaine, id_type_competence) VALUES
    ('PHP', 'Langage de programmation côté serveur', 'Développement', 1),
    ('JavaScript', 'Langage de programmation côté client', 'Développement', 1),
    ('SQL', 'Langage de requête structuré', 'Base de données', 1),
    ('Gestion de projet', 'Méthodologies agiles et waterfall', 'Management', 2),
    ('Communication', 'Communication interpersonnelle et présentations', 'Soft Skills', 2),
    ('Anglais', 'Langue anglaise professionnelle', 'Langues', 3),
    ('Python', 'Langage de programmation polyvalent', 'Développement', 1),
    ('Gestion du temps', 'Priorisation, planification et respect des delais', 'Management', 2),
    ('Docker', 'Conteneurisation applications', 'DevOps', 1)
ON CONFLICT DO NOTHING;

-- CORRECTION: Supprimer les IDs explicites pour éviter les conflits
INSERT INTO employe_competences (id_employe, id_competence, niveau, id_source, date_mesure, valide, id_employe_validateur, date_validation) VALUES
(1, 1, 5, 2, '2024-02-01 09:00:00', TRUE, 1, '2024-02-02 10:00:00'),
(1, 6, 4, 1, '2024-02-05 11:15:00', TRUE, 1, '2024-02-06 09:30:00'),
(2, 3, 4, 2, '2024-03-02 14:00:00', TRUE, 2, '2024-03-03 10:00:00'),
(2, 5, 3, 1, '2024-03-02 14:30:00', TRUE, 2, '2024-03-04 09:45:00'),
(3, 7, 3, 6, '2024-03-05 09:00:00', FALSE, NULL, NULL),
(3, 3, 4, 2, '2024-03-06 10:30:00', TRUE, 1, '2024-03-07 08:30:00'),
(4, 4, 2, 4, '2024-03-08 16:00:00', FALSE, NULL, NULL),
(4, 5, 3, 1, '2024-03-09 09:30:00', TRUE, 4, '2024-03-10 11:00:00'),
(2, 2, 2, 3, '2024-03-12 13:20:00', FALSE, NULL, NULL);

-- Données supplémentaires
INSERT INTO admins (id_employe, nom, mdp, date_affiliation, date_fin_affiliation) VALUES
(3, 'admin_stock', 'stock2024', '2022-03-10 08:00:00', NULL),
(4, 'admin_vendeur', 'vendeur2024', '2022-08-22 09:00:00', '2024-12-31 23:59:59');

INSERT INTO heure_supplementaire_config (date_creation, nombre_premieres_heures) VALUES
('2024-01-01 00:00:00', 12),
('2024-06-01 00:00:00', 15);

-- CORRECTION: Utiliser des IDs d'employés valides (1-4)
INSERT INTO salaire_historique (salaire, date_creation, id_employe) VALUES
(5200000, '2024-06-01 09:00:00', 1),
(2100000, '2024-07-01 09:00:00', 2);

INSERT INTO notifications (id_personne, message, date_notification) VALUES
(1, 'Nouvelle politique de conge publiee', '2024-06-01 08:00:00'),
(3, 'Formation obligatoire : securite au travail', '2024-05-12 12:00:00');


-- INSERTS: compétences plausibles validées (manager / RH)
INSERT INTO employe_competences (id_employe, id_competence, niveau, id_source, date_mesure, valide, id_employe_validateur, date_validation)
VALUES
-- Directeur (id_employe = 1) : Gestion, Communication, Anglais
(1, 4, 5, (SELECT id_source FROM source_evaluation WHERE libelle = 'manager-evaluation'), '2024-02-01 09:00:00', TRUE, 1, '2024-02-02 10:00:00'),
(1, 5, 4, (SELECT id_source FROM source_evaluation WHERE libelle = 'manager-evaluation'), '2024-02-01 09:15:00', TRUE, 1, '2024-02-02 10:05:00'),
(1, 6, 4, (SELECT id_source FROM source_evaluation WHERE libelle = 'rh-evaluation'),      '2024-02-01 09:30:00', TRUE, 2, '2024-02-03 11:00:00')
ON CONFLICT (id_employe, id_competence) DO NOTHING;

-- Comptable (id_employe = 2) : SQL, Communication, Anglais
INSERT INTO employe_competences (id_employe, id_competence, niveau, id_source, date_mesure, valide, id_employe_validateur, date_validation)
VALUES
(2, 3, 4, (SELECT id_source FROM source_evaluation WHERE libelle = 'manager-evaluation'), '2024-03-02 14:00:00', TRUE, 2, '2024-03-03 10:00:00'),
(2, 5, 4, (SELECT id_source FROM source_evaluation WHERE libelle = 'rh-evaluation'),      '2024-03-02 14:30:00', TRUE, 2, '2024-03-04 09:45:00'),
(2, 6, 3, (SELECT id_source FROM source_evaluation WHERE libelle = 'manager-evaluation'), '2024-03-05 08:20:00', TRUE, 2, '2024-03-06 09:00:00')
ON CONFLICT (id_employe, id_competence) DO NOTHING;

-- Caissier (id_employe = 3) : Communication, SQL (opérationnel), Anglais basique
INSERT INTO employe_competences (id_employe, id_competence, niveau, id_source, date_mesure, valide, id_employe_validateur, date_validation)
VALUES
(3, 5, 4, (SELECT id_source FROM source_evaluation WHERE libelle = 'manager-evaluation'), '2024-03-06 09:00:00', TRUE, 1, '2024-03-07 08:30:00'),
(3, 3, 3, (SELECT id_source FROM source_evaluation WHERE libelle = 'test-technique'),    '2024-03-06 10:00:00', TRUE, 1, '2024-03-07 08:45:00'),
(3, 6, 2, (SELECT id_source FROM source_evaluation WHERE libelle = 'auto-evaluation'),   '2024-03-05 09:00:00', FALSE, NULL, NULL)
ON CONFLICT (id_employe, id_competence) DO NOTHING;

-- Magasinier (id_employe = 4) : Gestion de projet (logistique), Communication, Anglais
INSERT INTO employe_competences (id_employe, id_competence, niveau, id_source, date_mesure, valide, id_employe_validateur, date_validation)
VALUES
(4, 4, 3, (SELECT id_source FROM source_evaluation WHERE libelle = 'manager-evaluation'), '2024-03-08 16:00:00', FALSE, NULL, NULL),
(4, 5, 3, (SELECT id_source FROM source_evaluation WHERE libelle = 'manager-evaluation'), '2024-03-09 09:30:00', TRUE, 4, '2024-03-10 11:00:00'),
(4, 6, 2, (SELECT id_source FROM source_evaluation WHERE libelle = 'rh-evaluation'),      '2024-03-09 10:00:00', TRUE, 4, '2024-03-11 10:30:00')
ON CONFLICT (id_employe, id_competence) DO NOTHING;

-- Magasinier (id_employe = 5) : même profil que 4 (si doublon de poste)
INSERT INTO employe_competences (id_employe, id_competence, niveau, id_source, date_mesure, valide, id_employe_validateur, date_validation)
VALUES
(5, 4, 3, (SELECT id_source FROM source_evaluation WHERE libelle = 'manager-evaluation'), '2024-01-16 09:00:00', TRUE, 4, '2024-01-17 10:00:00'),
(5, 5, 3, (SELECT id_source FROM source_evaluation WHERE libelle = 'manager-evaluation'), '2024-01-16 09:20:00', TRUE, 4, '2024-01-17 10:05:00'),
(5, 6, 2, (SELECT id_source FROM source_evaluation WHERE libelle = 'auto-evaluation'),   '2024-01-16 09:30:00', FALSE, NULL, NULL)
ON CONFLICT (id_employe, id_competence) DO NOTHING;

-- --------------------------------------------------------------------
-- Auto-évaluations NON VALIDÉES: une entrée auto-eval (valide = FALSE) par employé
-- --------------------------------------------------------------------
INSERT INTO employe_competences (id_employe, id_competence, niveau, id_source, date_mesure, valide)
VALUES
-- pour l'employé 1 (Directeur) : auto-éval Gestion de projet non validée
(1, 4, 5, (SELECT id_source FROM source_evaluation WHERE libelle = 'auto-evaluation'), '2024-02-01 08:50:00', FALSE),
-- pour l'employé 2 (Comptable) : auto-éval SQL non validée
(2, 3, 4, (SELECT id_source FROM source_evaluation WHERE libelle = 'auto-evaluation'), '2024-03-02 13:50:00', FALSE),
-- pour l'employé 3 (Caissier) : auto-éval Communication non validée
(3, 5, 4, (SELECT id_source FROM source_evaluation WHERE libelle = 'auto-evaluation'), '2024-03-05 08:50:00', FALSE),
-- pour l'employé 4 (Magasinier) : auto-éval Gestion de projet non validée
(4, 4, 3, (SELECT id_source FROM source_evaluation WHERE libelle = 'auto-evaluation'), '2024-03-08 15:30:00', FALSE),
-- pour l'employé 5 (Magasinier) : auto-éval Communication non validée
(5, 5, 3, (SELECT id_source FROM source_evaluation WHERE libelle = 'auto-evaluation'), '2024-01-15 08:45:00', FALSE)
ON CONFLICT (id_employe, id_competence) DO NOTHING;


-- Insérer quelques métiers de base
INSERT INTO metier_competences_requises (libelle_metier, id_departement, description, competences_requises) VALUES
('Comptable', 2, 'Gestion comptable et financière', 
 '{"PHP": 3, "SQL": 4, "Communication": 4, "Anglais": 3}'::jsonb),
('Magasinier', 3, 'Gestion des stocks', 
 '{"Gestion de projet": 3, "Communication": 3, "Anglais": 2}'::jsonb),
('Vendeur', 4, 'Vente et relation client', 
 '{"Communication": 5, "Gestion du temps": 4, "Anglais": 3}'::jsonb),
('Manager', 1, 'Gestion d''équipe', 
 '{"Gestion de projet": 5, "Communication": 5, "Leadership": 4}'::jsonb);

 -- ========================================
-- COMPLÉMENT POUR LA CARTOGRAPHIE DES COMPÉTENCES
-- ========================================

-- 1. Insertions pour la table profil_competences_requises (structuration des profils)
INSERT INTO profil_competences_requises (id_profil, id_competence, niveau_requis, est_critique)
VALUES
-- Caissier (profil 1)
(1, 3, 3, FALSE),   -- SQL basique pour gestion caisse
(1, 5, 4, TRUE),    -- Communication critique
(1, 8, 3, FALSE),   -- Gestion du temps

-- Comptable (profil 2)
(2, 1, 4, TRUE),    -- PHP pour outils comptables
(2, 3, 5, TRUE),    -- SQL avancé critique
(2, 6, 3, FALSE),   -- Anglais technique

-- Manager (profil 3)
(3, 4, 5, TRUE),    -- Gestion de projet critique
(3, 5, 5, TRUE),    -- Communication critique
(3, 8, 4, FALSE),   -- Gestion du temps

-- Magasinier (profil 4)
(4, 3, 3, FALSE),   -- SQL pour gestion stock
(4, 5, 3, FALSE),   -- Communication équipe
(4, 7, 2, FALSE),   -- Python pour automatisation

-- Vendeur (profil 5)
(5, 5, 5, TRUE),    -- Communication critique
(5, 6, 3, FALSE),   -- Anglais clientèle
(5, 8, 4, FALSE)    -- Gestion du temps
ON CONFLICT (id_profil, id_competence) DO NOTHING;

-- 2. Insertions pour source_priorite (priorisation des sources d'évaluation)
INSERT INTO source_priorite (id_source, priorite, description)
VALUES
(1, 1, 'Auto-évaluation - priorité basse'),
(2, 3, 'Évaluation manager - priorité moyenne'),
(3, 2, 'Évaluation RH - priorité intermédiaire'),
(4, 2, 'Validation formation - priorité intermédiaire'),
(5, 4, 'Certification - priorité haute'),
(6, 5, 'Test technique - priorité très haute')
ON CONFLICT (id_source) DO UPDATE SET
    priorite = EXCLUDED.priorite,
    description = EXCLUDED.description;

-- 3. Snapshots historiques pour analyse des tendances
INSERT INTO competence_snapshot (id_competence, nb_employes, niveau_moyen, taux_couverture, nb_experts, metadata)
SELECT 
    c.id_competence,
    COUNT(DISTINCT ec.id_employe) as nb_employes,
    ROUND(AVG(ec.niveau)::numeric, 2) as niveau_moyen,
    ROUND((COUNT(DISTINCT ec.id_employe)::numeric / 5) * 100, 2) as taux_couverture, -- 5 employés totaux
    COUNT(DISTINCT CASE WHEN ec.niveau >= 4 THEN ec.id_employe END) as nb_experts,
    JSONB_BUILD_OBJECT(
        'date_analyse', '2024-03-01',
        'type_analyse', 'manuelle',
        'source', 'test_initial'
    ) as metadata
FROM competences c
LEFT JOIN employe_competences ec ON c.id_competence = ec.id_competence AND ec.valide = TRUE
WHERE c.id_competence BETWEEN 1 AND 6
GROUP BY c.id_competence
ON CONFLICT DO NOTHING;

-- 4. Alertes de competences generees automatiquement
INSERT INTO competence_alertes (
    type_alerte,
    id_competence,
    id_departement,
    severite,
    message,
    metadata
)
VALUES
(
    'soft_skills_faible',
    5,
    2,
    'moyenne',
    'Niveau moyen de communication insuffisant dans le departement Comptabilite',
    JSONB_BUILD_OBJECT(
        'niveau_moyen_actuel', 3.2,
        'niveau_attendu', 4.0,
        'employes_concernes', ARRAY[2, 4]
    )
),
(
    'recrutement_requis',
    1,
    3,
    'haute',
    'Competence PHP critique manquante pour le developpement d outils internes',
    JSONB_BUILD_OBJECT(
        'taux_couverture', 10.0,
        'niveau_requis', 4,
        'profil_requis', 'Developpeur interne'
    )
),
(
    'gap_competence',
    6,
    4,
    'critique',
    'Ecart important entre niveau actuel et niveau requis en anglais commercial',
    JSONB_BUILD_OBJECT(
        'niveau_moyen', 2.5,
        'niveau_requis', 4.0,
        'ecart_moyen', 1.5
    )
)
ON CONFLICT DO NOTHING;

-- 5. Recommandations d actions
INSERT INTO competence_recommandations (
    id_alerte,
    type_action,
    id_employe,
    id_competence,
    priorite,
    description,
    ressources_suggerees,
    statut
)
SELECT
    a.id_alerte,
    'formation' as type_action,
    e.id_employe,
    a.id_competence,
    CASE 
        WHEN a.severite = 'critique' THEN 5
        WHEN a.severite = 'haute' THEN 4
        WHEN a.severite = 'moyenne' THEN 3
        ELSE 2
    END as priorite,
    'Formation recommandée pour améliorer la compétence ' || c.nom,
    JSONB_BUILD_OBJECT(
        'formations', ARRAY['Cours en ligne LinkedIn Learning', 'Formation interne RH'],
        'duree_estimee', '20 heures',
        'cout_estime', 500.00
    ),
    'en_attente'
FROM competence_alertes a
CROSS JOIN employes e
JOIN competences c ON a.id_competence = c.id_competence
WHERE a.est_resolue = FALSE
  AND e.id_departement = a.id_departement
  AND e.id_employe IN (2, 4) -- Exemple pour 2 employés
LIMIT 3
ON CONFLICT DO NOTHING;

-- 6. Cache pour optimisation des requêtes fréquentes
INSERT INTO competence_cache (cache_key, cache_value, ttl)
VALUES
(
    'stats_globales_2024_03',
    JSONB_BUILD_OBJECT(
        'total_competences', 9,
        'total_employes_avec_competences', 5,
        'niveau_moyen_global', 3.4,
        'taux_couverture_global', 65.2,
        'date_calcul', '2024-03-15 10:00:00'
    ),
    '2024-04-15 10:00:00'
),
(
    'dashboard_competences',
    JSONB_BUILD_OBJECT(
        'top_competences', ARRAY['Communication', 'SQL', 'Gestion de projet'],
        'competences_critiques', ARRAY['PHP', 'Anglais'],
        'derniere_mise_a_jour', '2024-03-15 09:30:00'
    ),
    '2024-03-16 09:30:00'
)
ON CONFLICT (cache_key) DO UPDATE SET
    cache_value = EXCLUDED.cache_value,
    ttl = EXCLUDED.ttl;

-- 7. Logs d audit pour traçabilité
INSERT INTO competence_audit_log (
    operation_type,
    id_employe_operateur,
    details,
    nb_lignes_affectees,
    statut
)
VALUES
(
    'import_initial',
    1, -- Directeur
    JSONB_BUILD_OBJECT(
        'source', 'fichier_excel',
        'nom_fichier', 'competences_employes.xlsx',
        'timestamp_import', '2024-03-15 08:30:00'
    ),
    15,
    'success'
),
(
    'validation_competences',
    2, -- Comptable
    JSONB_BUILD_OBJECT(
        'employes_valides', ARRAY[1, 3, 4],
        'competences_validees', ARRAY[1, 3, 5, 6],
        'duree_operation', '00:15:30'
    ),
    8,
    'success'
),
(
    'generation_rapport',
    NULL,
    JSONB_BUILD_OBJECT(
        'type_rapport', 'cartographie_mensuelle',
        'periode', '2024-03',
        'format', 'PDF'
    ),
    1,
    'success'
)
ON CONFLICT DO NOTHING;

-- 8. Normalisation des noms de compétences (pour fuzzy matching)
INSERT INTO competence_normalisation (nom_original, nom_normalise, score_similarite, valide)
VALUES
('PHP Developer', 'php', 0.85, TRUE),
('JavaScript ES6', 'javascript', 0.90, TRUE),
('SQL Server', 'sql', 0.95, TRUE),
('Project Management', 'gestion de projet', 0.80, TRUE),
('English Business', 'anglais', 0.75, FALSE)
ON CONFLICT (nom_original) DO UPDATE SET
    nom_normalise = EXCLUDED.nom_normalise,
    score_similarite = EXCLUDED.score_similarite,
    valide = EXCLUDED.valide;

-- 9. Complement pour metier_competences_requises (liens metiers-departements)
INSERT INTO metier_competences_requises (libelle_metier, id_departement, description, competences_requises)
VALUES
(
    'Chef de projet',
    1,
    'Gestion de projets transversaux',
    '{"Gestion de projet": 5, "Communication": 5, "Leadership": 4, "Anglais": 4}'::jsonb
),
(
    'Analyste donnees',
    2,
    'Analyse de donnees financieres',
    '{"SQL": 5, "Python": 4, "Excel": 4, "Communication": 3}'::jsonb
)
ON CONFLICT DO NOTHING;

-- 10. Données de test pour heures_supplementaire (liées aux compétences)
INSERT INTO heures_supplementaire (id_employe, nombre_heure_effectue, mois, annee, numero_semaine)
SELECT 
    e.id_employe,
    ROUND((RANDOM() * 20 + 5)::numeric, 2) as heures,
    3, -- Mars
    2024,
    EXTRACT(WEEK FROM CURRENT_DATE) as semaine
FROM employes e
WHERE e.id_employe BETWEEN 1 AND 3
ON CONFLICT DO NOTHING;

-- 11. Historique des heures supplémentaires
INSERT INTO heures_supplementaire_historique (id_heure_supp, nombre_heure_effectue, mois, annee, numero_semaine)
SELECT 
    hs.id,
    hs.nombre_heure_effectue,
    hs.mois,
    hs.annee,
    hs.numero_semaine
FROM heures_supplementaire hs
WHERE hs.id BETWEEN 1 AND 3
ON CONFLICT DO NOTHING;

-- ========================================
-- MISE À JOUR DES VUES MATÉRIALISÉES
-- ========================================
-- Rafraîchissement des vues matérialisées pour inclure les nouvelles données
REFRESH MATERIALIZED VIEW CONCURRENTLY mv_competence_cartographie_optimisee;
REFRESH MATERIALIZED VIEW CONCURRENTLY mv_dashboard_competences;
