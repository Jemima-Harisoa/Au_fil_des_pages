-- Réorganised schema for database "aufildespages"
-- Each table created with IF NOT EXISTS and ordered to satisfy foreign-key dependencies.
-- Vérifie les extensions / versions PostgreSQL si tu utilises des GENERATED columns (Postgres >= 12).

-- =========================
-- Lookup / reference tables
-- =========================

CREATE TABLE IF NOT EXISTS evenements (
    id_evenement SERIAL PRIMARY KEY,
    nom_evenement VARCHAR
);

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

CREATE TABLE IF NOT EXISTS seuil_tolerance (
    id_seuil SERIAL PRIMARY KEY,
    valeur NUMERIC(5,2) DEFAULT 5,
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

CREATE TABLE IF NOT EXISTS jour_ferie (
    id_jour_ferie SERIAL PRIMARY KEY,
    date DATE
);

CREATE TABLE IF NOT EXISTS postes (
    id_poste SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS competences (
    id_competence SERIAL PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    domaine VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS formations (
    id_formation SERIAL PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    competence_id INT REFERENCES competences(id_competence),
    niveau_cible NUMERIC(4,2)
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

CREATE TABLE IF NOT EXISTS etat (
    id_etat SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE IF NOT EXISTS appreciation (
    id_appreciation SERIAL PRIMARY KEY,
    type_appreciation TEXT,
    code INT
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

CREATE TABLE IF NOT EXISTS message_automatique (
    id_message_automatique SERIAL PRIMARY KEY,
    message TEXT
);

CREATE TABLE IF NOT EXISTS notifications (
    id_notification SERIAL PRIMARY KEY,
    id_personne INT,
    message TEXT,
    date_notification TIMESTAMP,
    CONSTRAINT fk_notifications_personne FOREIGN KEY (id_personne) REFERENCES personnes(id_personne)
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

CREATE TABLE IF NOT EXISTS status_validation_cv (
    id_status_validation_cv SERIAL PRIMARY KEY,
    statut VARCHAR
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

CREATE TABLE IF NOT EXISTS notifications (
    id_notification SERIAL PRIMARY KEY,
    id_personne INT,
    message TEXT,
    date_notification TIMESTAMP,
    CONSTRAINT fk_notifications_personne FOREIGN KEY (id_personne) REFERENCES personnes(id_personne)
);

CREATE TABLE IF NOT EXISTS contrats (
    id_contrat SERIAL PRIMARY KEY,
    id_candidat INT,
    id_type_contrat INT,
    url_contrat VARCHAR,
    CONSTRAINT fk_contrats_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat),
    CONSTRAINT fk_contrats_type_contrat FOREIGN KEY (id_type_contrat) REFERENCES type_contrats(id_type_contrat)
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

CREATE TABLE IF NOT EXISTS status_validation_cv (
    id_status_validation_cv SERIAL PRIMARY KEY,
    statut VARCHAR
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

CREATE TABLE IF NOT EXISTS salaire_historique (
    id_salaire_historique SERIAL PRIMARY KEY,
    salaire DOUBLE PRECISION,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_employe INT,
    CONSTRAINT fk_salaire_historique_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
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

CREATE TABLE IF NOT EXISTS abscence (
    id_abscence SERIAL PRIMARY KEY,
    id_employe INT,
    debut TIMESTAMP,
    fin TIMESTAMP,
    est_autorise BOOLEAN,
    justificatif TEXT,
    CONSTRAINT fk_abscence_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

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

CREATE TABLE IF NOT EXISTS conge_historique (
    id_conge_historique SERIAL PRIMARY KEY,
    nombres_abscence_attribue DOUBLE PRECISION,
    id_employe INT,
    CONSTRAINT fk_conge_historique_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
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
    CONSTRAINT fk_conge_suivi_employe FOREIGN KEY (id_employe) REFERENCES emplees(id_employe),
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

CREATE TABLE IF NOT EXISTS conge_historique (
    id_conge_historique SERIAL PRIMARY KEY,
    nombres_abscence_attribue DOUBLE PRECISION,
    id_employe INT,
    CONSTRAINT fk_conge_historique_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
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

CREATE TABLE IF NOT EXISTS prime (
    id SERIAL PRIMARY KEY,
    id_type_prime INT,
    pourcentage NUMERIC(5,2),
    id_employe INT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_prime_type FOREIGN KEY (id_type_prime) REFERENCES type_prime(id),
    CONSTRAINT fk_prime_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
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
