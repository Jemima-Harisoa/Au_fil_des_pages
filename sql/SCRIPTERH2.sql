-- Réorganised schema for database "aufildespages"
-- Each table created with IF NOT EXISTS and ordered to satisfy foreign-key dependencies.
-- Vérifie les extensions / versions PostgreSQL si tu utilises des GENERATED columns (Postgres >= 12).

-- =========================
-- Lookup / reference tables
-- =========================

CREATE TABLE IF NOT EXISTS sexe (
    id_sexe SERIAL PRIMARY KEY,
    type_sexe VARCHAR
);

CREATE TABLE IF NOT EXISTS departements (
    id_departement SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE IF NOT EXISTS filieres (
    id_filiere SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE IF NOT EXISTS type_contrats (
    id_type_contrat SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE IF NOT EXISTS diplomes (
    id_diplome SERIAL PRIMARY KEY,
    nom VARCHAR,
    niveau INT
);

CREATE TABLE IF NOT EXISTS etat (
    id_etat SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE IF NOT EXISTS appreciation (
    id_appreciation SERIAL PRIMARY KEY,
    type_appreciation TEXT,
    code INT
);

CREATE TABLE IF NOT EXISTS conge_type (
    id_type SERIAL PRIMARY KEY,
    nom VARCHAR,
    description TEXT,
    nombre_jour INT,
    deductible_sur_salaire BOOLEAN DEFAULT TRUE,
    deductible_sur_conge BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS abscence_type_penalite (
    id_type_penalite SERIAL PRIMARY KEY,
    nom VARCHAR,
    description TEXT,
    montant DOUBLE PRECISION
);

CREATE TABLE IF NOT EXISTS status_validation_cv (
    id_status_validation_cv SERIAL PRIMARY KEY,
    statut VARCHAR
);

CREATE TABLE IF NOT EXISTS type_prime (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR
);

CREATE TABLE IF NOT EXISTS parametre (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR,
    pourcentage NUMERIC(5,2)
);

CREATE TABLE IF NOT EXISTS smig (
    id SERIAL PRIMARY KEY,
    montant NUMERIC(10,2) NOT NULL,
    date_application DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS irsa (
    id SERIAL PRIMARY KEY,
    min DOUBLE PRECISION,
    max DOUBLE PRECISION,
    pourcentage NUMERIC(5,2),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS treshold (
    id_treshold SERIAL PRIMARY KEY,
    valeur NUMERIC(5,2),
    date_treshold TIMESTAMP
);

CREATE TABLE IF NOT EXISTS message_automatique (
    id_message_automatique SERIAL PRIMARY KEY,
    message TEXT
);

CREATE TABLE IF NOT EXISTS api (
    id_api SERIAL PRIMARY KEY,
    nom VARCHAR,
    cle_api VARCHAR
);



CREATE TABLE IF NOT EXISTS postes (
    id_poste SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS type_competence (
    id_type_competence SERIAL PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL,
    description TEXT
);

-- Table des compétences
CREATE TABLE IF NOT EXISTS competences (
    id_competence SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    domaine VARCHAR(100),
    id_type_competence INT REFERENCES type_competence(id_type_competence),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Core person / auth tables
-- =========================

CREATE TABLE IF NOT EXISTS personnes (
    id_personne SERIAL PRIMARY KEY,
    nom VARCHAR,
    prenom VARCHAR,
    date_naissance DATE,
    contact VARCHAR,
    lien_image VARCHAR, 
    id_sexe INT,    
    CONSTRAINT fk_personnes_sexe FOREIGN KEY (id_sexe) REFERENCES sexe(id_sexe)
);

CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur SERIAL PRIMARY KEY,
    nom VARCHAR,
    mdp VARCHAR,
    date_inscription TIMESTAMP,
    date_sortie TIMESTAMP
);

-- =========================
-- Recruitment / CV module
-- =========================

CREATE TABLE IF NOT EXISTS profils (
    id_profil SERIAL PRIMARY KEY,
    titre VARCHAR,
    competences TEXT,
    skills TEXT,
    loisirs TEXT,
    id_diplome INT,
    id_filiere INT,
    experience_pro TEXT,
    certifications TEXT,
    langues TEXT,
    id_type_contrat INT,
    id_departement INT,
    est_minimum BOOLEAN,
    CONSTRAINT fk_profils_diplome FOREIGN KEY (id_diplome) REFERENCES diplomes(id_diplome),
    CONSTRAINT fk_profils_filiere FOREIGN KEY (id_filiere) REFERENCES filieres(id_filiere),
    CONSTRAINT fk_profils_type_contrat FOREIGN KEY (id_type_contrat) REFERENCES type_contrats(id_type_contrat),
    CONSTRAINT fk_profils_departement FOREIGN KEY (id_departement) REFERENCES departements(id_departement)
);

CREATE TABLE IF NOT EXISTS annonces (
    id_annonce SERIAL PRIMARY KEY,
    id_profil INT,
    titre VARCHAR,
    date_publication DATE,
    date_expiration DATE,
     nombre_poste INT,
    lien TEXT
);

CREATE TABLE IF NOT EXISTS questions (
    id_question SERIAL PRIMARY KEY,
    question TEXT,
    id_profil INT,
    note NUMERIC(5,2),
    CONSTRAINT fk_questions_profil FOREIGN KEY (id_profil) REFERENCES profils(id_profil)
);

CREATE TABLE IF NOT EXISTS reponses_question (
    id_reponse SERIAL PRIMARY KEY,
    id_question INT,
    reponse TEXT,
    est_correct BOOLEAN,
    CONSTRAINT fk_reponses_question FOREIGN KEY (id_question) REFERENCES questions(id_question)
);

CREATE TABLE IF NOT EXISTS candidats (
    id_candidat SERIAL PRIMARY KEY,
    id_personne INT,
    id_annonce INT,
    id_profil INT,
    cv_url VARCHAR,
    poste VARCHAR,
    id_utilisateur INT,
    CONSTRAINT fk_candidats_personne FOREIGN KEY (id_personne) REFERENCES personnes(id_personne),
    CONSTRAINT fk_candidats_annonce FOREIGN KEY (id_annonce) REFERENCES annonces(id_annonce),
    CONSTRAINT fk_candidats_profil FOREIGN KEY (id_profil) REFERENCES profils(id_profil)
);

CREATE TABLE IF NOT EXISTS tests (
    id_test SERIAL PRIMARY KEY,
    id_candidat INT,
    id_annonce INT,
    score_test NUMERIC(5,2),
    date_test DATE,
    CONSTRAINT fk_tests_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat),
    CONSTRAINT fk_tests_annonce FOREIGN KEY (id_annonce) REFERENCES annonces(id_annonce)
);

CREATE TABLE IF NOT EXISTS planning_entretien (
    id_entretien SERIAL PRIMARY KEY,
    id_candidat INT,
    id_responsable INT,
    date_heure_entretien TIMESTAMP,
    score_entretien NUMERIC(5,2),
    etat INT,
    id_appreciation INT,
    CONSTRAINT fk_planning_entretien_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat),
    CONSTRAINT fk_planning_entretien_appreciation FOREIGN KEY (id_appreciation) REFERENCES appreciation(id_appreciation)
);

CREATE TABLE IF NOT EXISTS contrats (
    id_contrat SERIAL PRIMARY KEY,
    id_candidat INT,
    id_type_contrat INT,
    url_contrat VARCHAR,
    CONSTRAINT fk_contrats_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat),
    CONSTRAINT fk_contrats_type_contrat FOREIGN KEY (id_type_contrat) REFERENCES type_contrats(id_type_contrat)
);

CREATE TABLE IF NOT EXISTS cv_candidats (
    id_cv_candidats SERIAL PRIMARY KEY,
    id_candidat INT,
    competences TEXT,
    skills TEXT,
    loisirs TEXT,
    id_diplome INT,
    filiere TEXT,
    experience_pro TEXT,
    certifications TEXT,
    langues TEXT,
    date_deposition TIMESTAMP,
    CONSTRAINT fk_cv_candidats_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat)
);

CREATE TABLE IF NOT EXISTS validation_cv (
    id_validation_cv SERIAL PRIMARY KEY,
    id_candidat INT,
    id_cv_candidat INT,
    id_status_validation_cv INT,
    similarite NUMERIC(5,2),
    CONSTRAINT fk_validation_cv_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat),
    CONSTRAINT fk_validation_cv_cv FOREIGN KEY (id_cv_candidat) REFERENCES cv_candidats(id_cv_candidats),
    CONSTRAINT fk_validation_cv_status FOREIGN KEY (id_status_validation_cv) REFERENCES status_validation_cv(id_status_validation_cv)
);

CREATE TABLE IF NOT EXISTS employes (
    id_employe SERIAL PRIMARY KEY,
    id_personne INT,
    id_contrat INT,
    id_departement INT,
    poste VARCHAR,
    date_embauche DATE,
    nombre_conge INT,
    salaire_base DOUBLE PRECISION,
    CONSTRAINT fk_employes_personne FOREIGN KEY (id_personne) REFERENCES personnes(id_personne),
    CONSTRAINT fk_employes_contrat FOREIGN KEY (id_contrat) REFERENCES contrats(id_contrat),
    CONSTRAINT fk_employes_departement FOREIGN KEY (id_departement) REFERENCES departements(id_departement)
);

CREATE TABLE IF NOT EXISTS admins (
    id_admin SERIAL PRIMARY KEY,
    id_employe INT,
    nom VARCHAR,
    mdp VARCHAR,
    date_affiliation TIMESTAMP,
    date_fin_affiliation TIMESTAMP,
    CONSTRAINT fk_admins_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

CREATE TABLE IF NOT EXISTS connexEmployes (
    idEmploye INT PRIMARY KEY REFERENCES employes(id_employe),
    mdp VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS horaires_employe (
    id_employe INT NOT NULL REFERENCES employes(id_employe),
    jour_semaine INT NOT NULL,           -- 1 = lundi ... 7 = dimanche
    debut_travail TIME NOT NULL,
    fin_travail TIME NOT NULL,
    seuil_retard INTERVAL DEFAULT '00:05:00'
);

CREATE TABLE IF NOT EXISTS disponibilite_employe (
    id_dispo SERIAL PRIMARY KEY,
    id_employe INT,
    heure_debut TIME,
    heure_fin TIME,
    CONSTRAINT fk_disponibilite_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

CREATE TABLE IF NOT EXISTS historique_validation (
    id_historique_validation SERIAL PRIMARY KEY,
    id_employe INT,
    id_candidat INT,
    date_heure_validation TIMESTAMP,
    id_etat INT,
    CONSTRAINT fk_historique_validation_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe),
    CONSTRAINT fk_historique_validation_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat),
    CONSTRAINT fk_historique_validation_etat FOREIGN KEY (id_etat) REFERENCES etat(id_etat)
);

CREATE TABLE IF NOT EXISTS profilsCV (
    id_profil SERIAL PRIMARY KEY,
    titre VARCHAR,
    competences TEXT,
    skills TEXT,
    loisirs TEXT,
    id_diplome INT,
    filiere TEXT,
    experience_pro TEXT,
    certifications TEXT,
    langues TEXT,
    id_type_contrat INT,
    est_minimum BOOLEAN,
    id_departement INT,
    CONSTRAINT fk_profilCV_diplome FOREIGN KEY (id_diplome) REFERENCES diplomes(id_diplome),
    CONSTRAINT fk_profilCV_type_contrat FOREIGN KEY (id_type_contrat) REFERENCES type_contrats(id_type_contrat),
    CONSTRAINT fk_profilCV_departement FOREIGN KEY (id_departement) REFERENCES departements(id_departement)
);

CREATE TABLE IF NOT EXISTS pointage_journalier (
    id_pointage_journalier SERIAL PRIMARY KEY,
    id_employe INT NOT NULL REFERENCES employes(id_employe),
    date_pointage DATE NOT NULL,
    retard interval,
    heures_supp interval,
    pause interval,
    heures_travaillees interval
);

CREATE TABLE IF NOT EXISTS heure_supplementaire_config (
    id SERIAL PRIMARY KEY,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    nombre_premieres_heures INT NOT NULL
);

CREATE TABLE IF NOT EXISTS heures_supplementaire (
    id SERIAL PRIMARY KEY,
    id_employe INT NOT NULL REFERENCES employes(id_employe) ON DELETE CASCADE,
    nombre_heure_effectue NUMERIC(5,2) NOT NULL,
    mois INT NOT NULL CHECK (mois >= 1 AND mois <= 12),
    annee INT NOT NULL,
    numero_semaine INT NOT NULL CHECK (numero_semaine >= 1 AND numero_semaine <= 52),
    date_enregistrement TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS heures_supplementaire_historique (
    id SERIAL PRIMARY KEY,
    id_heure_supp INT NOT NULL REFERENCES heures_supplementaire(id) ON DELETE CASCADE,
    nombre_heure_effectue NUMERIC(5,2) NOT NULL,
    mois INT NOT NULL CHECK (mois >= 1 AND mois <= 12),
    annee INT NOT NULL,
    numero_semaine INT NOT NULL CHECK (numero_semaine >= 1 AND numero_semaine <= 53),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS prime (
    id SERIAL PRIMARY KEY,
    id_type_prime INT,
    pourcentage NUMERIC(5,2),
    id_employe INT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_prime_type FOREIGN KEY (id_type_prime) REFERENCES type_prime(id),
    CONSTRAINT fk_prime_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

CREATE TABLE IF NOT EXISTS preavis (
    id SERIAL PRIMARY KEY,
    id_employe INT NOT NULL REFERENCES employes(id_employe) ON DELETE CASCADE,
    date_debut_preavis DATE NOT NULL,
    date_fin_preavis DATE NOT NULL,
    est_termine BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Congés / absences
-- =========================



CREATE TABLE IF NOT EXISTS conge_demande (
    id_demande SERIAL PRIMARY KEY,
    description TEXT,
    id_employe INT,
    date_demande TIMESTAMP,
    date_debut TIMESTAMP,
    date_fin TIMESTAMP,
    niveau_validation INT DEFAULT 2,
    id_type_conge INT,
    CONSTRAINT fk_conge_demande_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe),
    CONSTRAINT fk_conge_demande_type_conge FOREIGN KEY (id_type_conge) REFERENCES conge_type(id_type)
);

CREATE TABLE IF NOT EXISTS conge_historique_validation (
    id_historique_validation SERIAL PRIMARY KEY,
    id_demande INT,
    id_employe INT,
    date_validation TIMESTAMP,
    CONSTRAINT fk_conge_historique_validation_demande FOREIGN KEY (id_demande) REFERENCES conge_demande(id_demande),
    CONSTRAINT fk_conge_historique_validation_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);


CREATE TABLE IF NOT EXISTS conge_solde (
    id_solde SERIAL PRIMARY KEY,
    id_employe INT,
    id_type_conge INT,
    solde DOUBLE PRECISION,
    annee INT,
    CONSTRAINT fk_conge_solde_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe),
    CONSTRAINT fk_conge_solde_type FOREIGN KEY (id_type_conge) REFERENCES conge_type(id_type)
);

CREATE TABLE IF NOT EXISTS abscence (
    id_abscence SERIAL PRIMARY KEY,
    id_employe INT,
    debut TIMESTAMP,
    fin TIMESTAMP,
    est_autorise BOOLEAN,
    justificatif TEXT,
    CONSTRAINT fk_abscence_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);


CREATE TABLE IF NOT EXISTS abscence_conge_suivi (
    id_suivi SERIAL PRIMARY KEY,
    id_demande INT,
    id_abscence INT,
    id_type INT,
    id_employe INT,
    nombre_conge INT,
    annee INT,
    penalite_appliquee BOOLEAN DEFAULT FALSE,
    id_type_penalite INT,
    dateMouvement TIMESTAMP,
    CONSTRAINT fk_conge_suivi_demande FOREIGN KEY (id_demande) REFERENCES conge_demande(id_demande),
    CONSTRAINT fk_conge_suivi_type FOREIGN KEY (id_type) REFERENCES conge_type(id_type),
    CONSTRAINT fk_conge_suivi_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe),
    CONSTRAINT fk_conge_suivi_abscence FOREIGN KEY (id_abscence) REFERENCES abscence(id_abscence),
    CONSTRAINT fk_conge_suivi_type_penalite FOREIGN KEY (id_type_penalite) REFERENCES abscence_type_penalite(id_type_penalite)
);

CREATE TABLE IF NOT EXISTS responsable_entretien (
    id_responsable SERIAL PRIMARY KEY,
    id_profil INT,
    id_employe INT,
    ordre_passage INT,
    CONSTRAINT fk_responsable_entretien_profil FOREIGN KEY (id_profil) REFERENCES profils(id_profil),
    CONSTRAINT fk_responsable_entretien_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

CREATE TABLE IF NOT EXISTS disponibilite_entretien (
    id_dispo SERIAL PRIMARY KEY,
    id_responsable INT,
    heure_debut TIME,
    heure_fin TIME,
    jour INT,
    est_valide BOOLEAN,
    CONSTRAINT fk_disponibilite_entretien_responsable FOREIGN KEY (id_responsable) REFERENCES responsable_entretien(id_responsable)
);

CREATE TABLE IF NOT EXISTS jour_ferie (
    id_jour_ferie SERIAL PRIMARY KEY,
    date DATE
);

CREATE TABLE IF NOT EXISTS config_entretien (
    id_config_entretien SERIAL PRIMARY KEY,
    id_departement INT,
    duree_entretien VARCHAR,
    CONSTRAINT fk_config_entretien_departement FOREIGN KEY (id_departement) REFERENCES departements(id_departement)
);


CREATE TABLE IF NOT EXISTS evenements (
    id_evenement SERIAL PRIMARY KEY,
    nom_evenement VARCHAR
);

CREATE TABLE IF NOT EXISTS historique_mobilite (
    id_mobilite SERIAL PRIMARY KEY,
    id_candidat INT,
    id_evenement INT,
    id_profil INT,
    id_departement INT,
    date_evenement DATE,
    support TEXT,
    CONSTRAINT fk_historique_mobilite_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat),
    CONSTRAINT fk_historique_mobilite_evenement FOREIGN KEY (id_evenement) REFERENCES evenements(id_evenement),
    CONSTRAINT fk_historique_mobilite_profil FOREIGN KEY (id_profil) REFERENCES profils(id_profil),
    CONSTRAINT fk_historique_mobilite_departement FOREIGN KEY (id_departement) REFERENCES departements(id_departement)
);

CREATE TABLE IF NOT EXISTS conge_historique (
    id_conge_historique SERIAL PRIMARY KEY,
    nombres_abscence_attribue DOUBLE PRECISION,
    id_employe INT,
    CONSTRAINT fk_conge_historique_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);


CREATE TABLE IF NOT EXISTS seuil_tolerance (
    id_seuil SERIAL PRIMARY KEY,
    valeur NUMERIC(5,2) DEFAULT 5,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS salaire_historique (
    id_salaire_historique SERIAL PRIMARY KEY,
    salaire DOUBLE PRECISION,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_employe INT,
    CONSTRAINT fk_salaire_historique_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);


CREATE TABLE IF NOT EXISTS essais (
    id_essai SERIAL PRIMARY KEY,
    id_personne INT,
    id_contrat INT,
    id_etat INT,
    date_debut DATE,
    date_fin DATE,
    CONSTRAINT fk_essais_personne FOREIGN KEY (id_personne) REFERENCES personnes(id_personne),
    CONSTRAINT fk_essais_contrat FOREIGN KEY (id_contrat) REFERENCES contrats(id_contrat),
    CONSTRAINT fk_essais_etat FOREIGN KEY (id_etat) REFERENCES etat(id_etat)
);

CREATE TABLE IF NOT EXISTS notifications (
    id_notification SERIAL PRIMARY KEY,
    id_personne INT,
    message TEXT,
    date_notification TIMESTAMP,
    CONSTRAINT fk_notifications_personne FOREIGN KEY (id_personne) REFERENCES personnes(id_personne)
);

-- (end of cleaned canonical schema)
-- ========================================
-- Tables pour la gestion des compétences
-- ========================================

-- Table des types de compétences

-- Table des sources d'évauation
CREATE TABLE IF NOT EXISTS source_evaluation (
    id_source SERIAL PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL,
    description TEXT
);

-- Table de référence pour les libellés des niveaux
CREATE TABLE IF NOT EXISTS niveau_competence_libelle (
    niveau INT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL,
    description TEXT,
    couleur VARCHAR(20)
);

-- Insertion des libellés de niveaux
INSERT INTO niveau_competence_libelle (niveau, libelle, description, couleur) VALUES
    (1, 'Débutant', 'Connaissances de base, en cours d''apprentissage', 'rouge'),
    (2, 'Intermédiaire', 'Capable de travailler de manière autonome sur des tâches courantes', 'orange'),
    (3, 'Avancé', 'Maîtrise solide, capable de résoudre des problèmes complexes', 'jaune'),
    (4, 'Expert', 'Référence dans le domaine, capable de former les autres', 'vert'),
    (5, 'Maître', 'Niveau exceptionnel, contribution à l''avancement du domaine', 'bleu')
ON CONFLICT (niveau) DO UPDATE SET 
    libelle = EXCLUDED.libelle,
    description = EXCLUDED.description,
    couleur = EXCLUDED.couleur;



-- Table d'historique des compétences
CREATE TABLE IF NOT EXISTS competences_historique (
    id_historique SERIAL PRIMARY KEY,
    id_competence INT NOT NULL,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    domaine VARCHAR(100),
    id_type_competence INT,
    operation_type VARCHAR(10) NOT NULL CHECK (operation_type IN ('INSERT', 'UPDATE', 'DELETE')),
    operation_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_employe_operation INT REFERENCES employes(id_employe),
    CONSTRAINT fk_competences_hist_employe FOREIGN KEY (id_employe_operation) REFERENCES employes(id_employe)
);

-- Table de liaison employé-compétence
CREATE TABLE IF NOT EXISTS employe_competences (
    id SERIAL PRIMARY KEY,
    id_employe INT NOT NULL REFERENCES employes(id_employe) ON DELETE CASCADE,
    id_competence INT NOT NULL REFERENCES competences(id_competence) ON DELETE CASCADE,
    niveau INT CHECK (niveau >= 1 AND niveau <= 5),
    id_source INT REFERENCES source_evaluation(id_source),
    date_mesure TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valide BOOLEAN DEFAULT FALSE,
    id_employe_validateur INT REFERENCES employes(id_employe),
    date_validation TIMESTAMP,
    UNIQUE(id_employe, id_competence),
    CONSTRAINT fk_employe_competences_source FOREIGN KEY (id_source) REFERENCES source_evaluation(id_source),
    CONSTRAINT fk_employe_competences_validateur FOREIGN KEY (id_employe_validateur) REFERENCES employes(id_employe),
    CONSTRAINT fk_employe_competences_niveau FOREIGN KEY (niveau) REFERENCES niveau_competence_libelle(niveau)
);

-- Table d'historique des compétences employés
CREATE TABLE IF NOT EXISTS employe_competences_historique (
    id_historique SERIAL PRIMARY KEY,
    id_liaison INT NOT NULL,
    id_employe INT NOT NULL,
    id_competence INT NOT NULL,
    niveau INT CHECK (niveau >= 1 AND niveau <= 5),
    id_source INT,
    date_mesure TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valide BOOLEAN DEFAULT FALSE,
    id_employe_validateur INT,
    operation_type VARCHAR(10) NOT NULL CHECK (operation_type IN ('INSERT', 'UPDATE', 'DELETE')),
    operation_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_employe_operation INT REFERENCES employes(id_employe),
    CONSTRAINT fk_emp_comp_hist_employe_op FOREIGN KEY (id_employe_operation) REFERENCES employes(id_employe)
);

-- Index pour améliorer les performances
CREATE INDEX idx_employe_competences_employe ON employe_competences(id_employe);
CREATE INDEX idx_employe_competences_competence ON employe_competences(id_competence);
CREATE INDEX idx_competences_domaine ON competences(domaine);
CREATE INDEX idx_competences_type ON competences(id_type_competence);
CREATE INDEX idx_competences_hist_competence ON competences_historique(id_competence);
CREATE INDEX idx_competences_hist_timestamp ON competences_historique(operation_timestamp);
CREATE INDEX idx_emp_comp_hist_liaison ON employe_competences_historique(id_liaison);
CREATE INDEX idx_emp_comp_hist_timestamp ON employe_competences_historique(operation_timestamp);
CREATE INDEX idx_emp_comp_source ON employe_competences(id_source);

CREATE OR REPLACE VIEW v_competence_cartographie AS
WITH agg AS (
  SELECT
    ec.id_competence,
    COUNT(DISTINCT ec.id_employe)                          AS nb_employes,
    AVG(ec.niveau)                                        AS avg_niveau,
    ROUND(AVG(ec.niveau)::numeric, 2)                     AS niveau_moyen,
    ROUND(AVG(ec.niveau)::numeric, 0)::int                AS niv_moy_rounded,
    COUNT(DISTINCT CASE WHEN ec.valide = true THEN ec.id_employe END) AS nb_employes_valides,
    COUNT(*) FILTER (WHERE ec.niveau = 1)                 AS nb_debutants,
    COUNT(*) FILTER (WHERE ec.niveau = 2)                 AS nb_intermediaires,
    COUNT(*) FILTER (WHERE ec.niveau = 3)                 AS nb_avances,
    COUNT(*) FILTER (WHERE ec.niveau = 4)                 AS nb_experts,
    COUNT(*) FILTER (WHERE ec.niveau = 5)                 AS nb_maitres,
    MAX(ec.date_mesure)                                   AS derniere_maj
  FROM employe_competences ec
  GROUP BY ec.id_competence
)
SELECT
  c.id_competence,
  c.nom,
  c.description,
  c.domaine,
  tc.libelle AS type_competence,
  COALESCE(a.nb_employes, 0)            AS nb_employes,
  a.niveau_moyen                        AS niveau_moyen,
  COALESCE(ncl.libelle, 'N/A')          AS libelle_niveau_moyen,
  COALESCE(a.nb_employes_valides, 0)    AS nb_employes_valides,
  COALESCE(a.nb_debutants, 0)           AS nb_debutants,
  COALESCE(a.nb_intermediaires, 0)      AS nb_intermediaires,
  COALESCE(a.nb_avances, 0)             AS nb_avances,
  COALESCE(a.nb_experts, 0)             AS nb_experts,
  COALESCE(a.nb_maitres, 0)             AS nb_maitres,
  -- Pourcentages (éviter division par zéro)
  CASE WHEN COALESCE(a.nb_employes,0) = 0 THEN 0
       ELSE ROUND(a.nb_debutants * 100.0 / a.nb_employes, 2) END AS pourcentage_debutants,
  CASE WHEN COALESCE(a.nb_employes,0) = 0 THEN 0
       ELSE ROUND(a.nb_intermediaires * 100.0 / a.nb_employes, 2) END AS pourcentage_intermediaires,
  CASE WHEN COALESCE(a.nb_employes,0) = 0 THEN 0
       ELSE ROUND(a.nb_avances * 100.0 / a.nb_employes, 2) END AS pourcentage_avances,
  CASE WHEN COALESCE(a.nb_employes,0) = 0 THEN 0
       ELSE ROUND(a.nb_experts * 100.0 / a.nb_employes, 2) END AS pourcentage_experts,
  CASE WHEN COALESCE(a.nb_employes,0) = 0 THEN 0
       ELSE ROUND(a.nb_maitres * 100.0 / a.nb_employes, 2) END AS pourcentage_maitres,
  a.derniere_maj
FROM competences c
LEFT JOIN agg a ON c.id_competence = a.id_competence
LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
LEFT JOIN niveau_competence_libelle ncl ON a.niv_moy_rounded = ncl.niveau;

-- Vue pour les compétences par employé avec détails COMPLETS
CREATE OR REPLACE VIEW v_employe_competences AS
SELECT 
    ec.id,
    e.id_employe,
    p.nom,
    p.prenom,
    d.nom as departement,
    e.poste,
    c.id_competence,
    c.nom as competence_nom,
    c.domaine,
    tc.libelle as type_competence,
    ec.niveau,
    ncl.libelle as libelle_niveau,
    ncl.description as description_niveau,
    ncl.couleur as couleur_niveau,
    s.libelle as source_evaluation,
    ec.date_mesure,
    ec.valide,
    evp.nom as validateur_nom,
    evp.prenom as validateur_prenom,
    ec.date_validation,
    -- Calcul de l'ancienneté de la compétence
    EXTRACT(YEAR FROM age(CURRENT_DATE, ec.date_mesure)) as anciennete_annees
FROM employe_competences ec
JOIN employes e ON ec.id_employe = e.id_employe
JOIN personnes p ON e.id_personne = p.id_personne
LEFT JOIN departements d ON e.id_departement = d.id_departement
JOIN competences c ON ec.id_competence = c.id_competence
LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
LEFT JOIN niveau_competence_libelle ncl ON ec.niveau = ncl.niveau
LEFT JOIN source_evaluation s ON ec.id_source = s.id_source
LEFT JOIN employes ev ON ec.id_employe_validateur = ev.id_employe
LEFT JOIN personnes evp ON ev.id_personne = evp.id_personne;

-- Vue pour le tableau de bord des compétences
CREATE OR REPLACE VIEW v_tableau_bord_competences AS
WITH stats_employe AS (
    SELECT
        e.id_employe,
        COUNT(ec.id_competence) as nb_competences,
        COUNT(CASE WHEN ec.valide = true THEN 1 END) as nb_competences_validees,
        ROUND(AVG(ec.niveau)::numeric, 2) as niveau_moyen,
        ROUND(AVG(ec.niveau)::numeric, 0) as niveau_moyen_arrondi,
        COUNT(CASE WHEN ec.niveau >= 4 THEN 1 END) as nb_competences_expert,
        COUNT(CASE WHEN tc.libelle = 'Hard Skill' THEN 1 END) as nb_hard_skills,
        COUNT(CASE WHEN tc.libelle = 'Soft Skill' THEN 1 END) as nb_soft_skills,
        MAX(ec.date_mesure) as derniere_mise_a_jour
    FROM employes e
    LEFT JOIN employe_competences ec ON e.id_employe = ec.id_employe
    LEFT JOIN competences c ON ec.id_competence = c.id_competence
    LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
    GROUP BY e.id_employe
)
SELECT
    e.id_employe,
    p.nom,
    p.prenom,
    d.nom as departement,
    e.poste,
    COALESCE(se.nb_competences, 0) as nb_competences,
    COALESCE(se.nb_competences_validees, 0) as nb_competences_validees,
    COALESCE(se.niveau_moyen, 0) as niveau_moyen,
    ncl.libelle as libelle_niveau_moyen,
    COALESCE(se.nb_competences_expert, 0) as nb_competences_expert,
    COALESCE(se.nb_hard_skills, 0) as nb_hard_skills,
    COALESCE(se.nb_soft_skills, 0) as nb_soft_skills,
    se.derniere_mise_a_jour
FROM employes e
JOIN personnes p ON e.id_personne = p.id_personne
LEFT JOIN departements d ON e.id_departement = d.id_departement
LEFT JOIN stats_employe se ON e.id_employe = se.id_employe
LEFT JOIN niveau_competence_libelle ncl ON 
    CASE
        WHEN se.niveau_moyen_arrondi BETWEEN 1 AND 5 THEN se.niveau_moyen_arrondi
        ELSE 1
    END = ncl.niveau;
    
-- Les fonctions trigger et triggers restent identiques...
-- Fonctions trigger pour mettre à jour updated_at
CREATE OR REPLACE FUNCTION update_competence_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Fonctions trigger pour l'historique des compétences
CREATE OR REPLACE FUNCTION track_competence_changes()
RETURNS TRIGGER AS $$
BEGIN
    IF (TG_OP = 'DELETE') THEN
        INSERT INTO competences_historique (
            id_competence, nom, description, domaine, id_type_competence, operation_type
        ) VALUES (
            OLD.id_competence, OLD.nom, OLD.description, OLD.domaine, OLD.id_type_competence, 'DELETE'
        );
        RETURN OLD;
    ELSIF (TG_OP = 'UPDATE') THEN
        INSERT INTO competences_historique (
            id_competence, nom, description, domaine, id_type_competence, operation_type
        ) VALUES (
            NEW.id_competence, NEW.nom, NEW.description, NEW.domaine, NEW.id_type_competence, 'UPDATE'
        );
        RETURN NEW;
    ELSIF (TG_OP = 'INSERT') THEN
        INSERT INTO competences_historique (
            id_competence, nom, description, domaine, id_type_competence, operation_type
        ) VALUES (
            NEW.id_competence, NEW.nom, NEW.description, NEW.domaine, NEW.id_type_competence, 'INSERT'
        );
        RETURN NEW;
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

-- Fonctions trigger pour l'historique des compétences employés
CREATE OR REPLACE FUNCTION track_employe_competence_changes()
RETURNS TRIGGER AS $$
BEGIN
    IF (TG_OP = 'DELETE') THEN
        INSERT INTO employe_competences_historique (
            id_liaison, id_employe, id_competence, niveau, id_source, 
            date_mesure, valide, id_employe_validateur, operation_type
        ) VALUES (
            OLD.id, OLD.id_employe, OLD.id_competence, OLD.niveau, OLD.id_source,
            OLD.date_mesure, OLD.valide, OLD.id_employe_validateur, 'DELETE'
        );
        RETURN OLD;
    ELSIF (TG_OP = 'UPDATE') THEN
        INSERT INTO employe_competences_historique (
            id_liaison, id_employe, id_competence, niveau, id_source, 
            date_mesure, valide, id_employe_validateur, operation_type
        ) VALUES (
            NEW.id, NEW.id_employe, NEW.id_competence, NEW.niveau, NEW.id_source,
            NEW.date_mesure, NEW.valide, NEW.id_employe_validateur, 'UPDATE'
        );
        RETURN NEW;
    ELSIF (TG_OP = 'INSERT') THEN
        INSERT INTO employe_competences_historique (
            id_liaison, id_employe, id_competence, niveau, id_source, 
            date_mesure, valide, id_employe_validateur, operation_type
        ) VALUES (
            NEW.id, NEW.id_employe, NEW.id_competence, NEW.niveau, NEW.id_source,
            NEW.date_mesure, NEW.valide, NEW.id_employe_validateur, 'INSERT'
        );
        RETURN NEW;
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

-- Triggers pour la mise à jour automatique
CREATE TRIGGER competence_update_timestamp
    BEFORE UPDATE ON competences
    FOR EACH ROW
    EXECUTE FUNCTION update_competence_timestamp();

-- Triggers pour l'historique des compétences
CREATE TRIGGER competence_history_tracking
    AFTER INSERT OR UPDATE OR DELETE ON competences
    FOR EACH ROW
    EXECUTE FUNCTION track_competence_changes();

-- Triggers pour l'historique des compétences employés
CREATE TRIGGER employe_competence_history_tracking
    AFTER INSERT OR UPDATE OR DELETE ON employe_competences
    FOR EACH ROW
    EXECUTE FUNCTION track_employe_competence_changes();


CREATE TABLE pointage (
    id_pointage   SERIAL PRIMARY KEY,
    id_employe    INTEGER NOT NULL,
    connexion     TIMESTAMP NOT NULL,
    deconnexion   TIMESTAMP,
    duree_session INTERVAL GENERATED ALWAYS AS (deconnexion - connexion) STORED,
    
    FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);
CREATE TABLE employe_evaluation_periodes (
    id_periode SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    description TEXT,
    frequence_mois INT NOT NULL,     -- Exemple : 1 = mensuel, 3 = trimestriel
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table : Critères d’évaluation
-- Exemple : Ponctualité (30%), Productivité (40%), Communication (30%)
CREATE TABLE employe_criteres_evaluation (
    id_critere SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    poids NUMERIC(5,2) NOT NULL CHECK (poids >= 0),   -- en pourcentage (0–100)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table : Évaluations générées pour les employés
-- Chaque évaluation appartient à un employé et à une période
CREATE TABLE employe_evaluations (
    id_evaluation SERIAL PRIMARY KEY,
    employe_id INT NOT NULL REFERENCES employes(id_employe) ON DELETE CASCADE,
    periode_id INT NOT NULL REFERENCES employe_evaluation_periodes(id_periode),
    date_generation DATE NOT NULL DEFAULT CURRENT_DATE,
    date_evaluation DATE,
    statut VARCHAR(20) NOT NULL DEFAULT 'PREVUE', 
        -- PREVUE | EN_COURS | TERMINEE
    score_total NUMERIC(6,2),
    manager_id INT,  -- si tu gères des comptes managers ailleurs
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Trigger pour updated_at
CREATE OR REPLACE FUNCTION employe_update_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER employe_trg_evaluations_updated
BEFORE UPDATE ON employe_evaluations
FOR EACH ROW EXECUTE FUNCTION employe_update_timestamp();

-- Table : Détails de l’évaluation (1 ligne par critère)
CREATE TABLE employe_evaluations_details (
    id_detail SERIAL PRIMARY KEY,
    evaluation_id INT NOT NULL REFERENCES employe_evaluations(id_evaluation) ON DELETE CASCADE,
    critere_id INT NOT NULL REFERENCES employe_criteres_evaluation(id_critere),
    note NUMERIC(5,2) CHECK (note >= 0 AND note <= 10),
    commentaire TEXT
);

-- Trigger pour calcul automatique du score total
CREATE OR REPLACE FUNCTION employe_calcul_score_total()
RETURNS TRIGGER AS $$
DECLARE
    total NUMERIC(6,2);
BEGIN
    SELECT SUM(ed.note * c.poids / 10)
    INTO total
    FROM employe_evaluations_details ed
    JOIN employe_criteres_evaluation c ON c.id_critere = ed.critere_id
    WHERE ed.evaluation_id = NEW.evaluation_id;

    UPDATE employe_evaluations
    SET score_total = total
    WHERE id_evaluation = NEW.evaluation_id;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER employe_trg_update_score
AFTER INSERT OR UPDATE ON employe_evaluations_details
FOR EACH ROW EXECUTE FUNCTION employe_calcul_score_total();

CREATE TABLE employe_performance_aggregations (
    id_aggregation SERIAL PRIMARY KEY,
    employe_id INT NOT NULL REFERENCES employes(id_employe),
    annee INT NOT NULL,
    score_moyen NUMERIC(6,2),
    score_max NUMERIC(6,2),
    score_min NUMERIC(6,2),
    total_evaluations INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE OR REPLACE VIEW vue_employe_performances_par_critere AS
SELECT 
    e.employe_id,
    c.nom AS critere,
    AVG(ed.note * c.poids / 10) AS score_pondere,
    EXTRACT(YEAR FROM e.date_evaluation) AS annee
FROM employe_evaluations e
JOIN employe_evaluations_details ed ON ed.evaluation_id = e.id_evaluation
JOIN employe_criteres_evaluation c ON c.id_critere = ed.critere_id
WHERE e.statut = 'TERMINEE'
GROUP BY e.employe_id, c.nom, annee;

CREATE TABLE competences_postes (
    id SERIAL PRIMARY KEY,
    poste_id INT NOT NULL REFERENCES postes(id_poste) ON DELETE CASCADE,
    competence_id INT NOT NULL REFERENCES competences(id_competence),
    niveau_requis INT CHECK(niveau_requis >= 1 AND niveau_requis <= 5)
);

CREATE TABLE formations (
    id_formation SERIAL PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    competence_id INT REFERENCES competences(id_competence),
    niveau_cible NUMERIC(4,2)
);
CREATE TABLE employe_formations (
    id SERIAL PRIMARY KEY,
    employe_id INT NOT NULL REFERENCES employes(id_employe),
    formation_id INT NOT NULL REFERENCES formations(id_formation),
    statut VARCHAR(20) DEFAULT 'PLANIFIE', -- PLANIFIE, EN_COURS, TERMINEE
    date_assignation DATE DEFAULT CURRENT_DATE
);
CREATE TABLE employe_evaluations_statuts (
    id SERIAL PRIMARY KEY,
    evaluation_id INT REFERENCES employe_evaluations(id_evaluation) ON DELETE CASCADE,
    statut VARCHAR(20) CHECK (statut IN ('PREVUE','EN_COURS','TERMINEE')),
    date_changement TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE employe_score_trends (
    id SERIAL PRIMARY KEY,
    employe_id INT REFERENCES employes(id_employe),
    mois INT,
    annee INT,
    score NUMERIC(6,2)
);
CREATE TABLE employe_evaluation_calendrier (
    id SERIAL PRIMARY KEY,
    employe_id INT REFERENCES employes(id_employe),
    periode_id INT REFERENCES employe_evaluation_periodes(id_periode),
    date_prevue DATE,
    date_limite DATE
);
CREATE TABLE manager_employes (
    id SERIAL PRIMARY KEY,
    manager_id INT REFERENCES employes(id_employe),
    employe_id INT REFERENCES employes(id_employe)
);
CREATE TABLE IF NOT EXISTS managers (
    id_manager SERIAL PRIMARY KEY,
    employe_id INT NOT NULL REFERENCES employes(id_employe),
    date_nomination DATE DEFAULT CURRENT_DATE
);

CREATE TABLE manager_admins (
    id_manager_admin SERIAL PRIMARY KEY,
    id_manager INT NOT NULL REFERENCES managers(id_manager), 
    id_admin INT NOT NULL REFERENCES admins(id_admin),
    date_lien TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
