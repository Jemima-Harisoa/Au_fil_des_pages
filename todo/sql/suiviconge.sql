-- ========================================
-- Database: aufildespages - Leave Management
-- ========================================

-- ================== TABLES ==================

CREATE TABLE departements (
    id_departement SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE personnes (
    id_personne SERIAL PRIMARY KEY,
    nom VARCHAR,
    prenom VARCHAR,
    date_naissance DATE,
    contact VARCHAR,
    lien_image VARCHAR
);

CREATE TABLE employes (
    id_employe SERIAL PRIMARY KEY,
    id_personne INT,
    id_contrat INT,
    id_departement INT,
    poste VARCHAR,
    date_embauche DATE,
    nombre_conge INT,
    salaire_base DOUBLE PRECISION,
    CONSTRAINT fk_employes_personne FOREIGN KEY (id_personne) REFERENCES personnes(id_personne),
    CONSTRAINT fk_employes_departement FOREIGN KEY (id_departement) REFERENCES departements(id_departement)
);

CREATE TABLE profils (
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
    est_minimum BOOLEAN
);

CREATE TABLE candidats (
    id_candidat SERIAL PRIMARY KEY,
    id_personne INT,
    id_annonce INT,
    id_profil INT,
    cv_url VARCHAR,
    poste VARCHAR,
    id_utilisateur INT,
    CONSTRAINT fk_candidats_personne FOREIGN KEY (id_personne) REFERENCES personnes(id_personne),
    CONSTRAINT fk_candidats_annonce FOREIGN KEY (id_annonce) REFERENCES annonces(id_annonce)
);

CREATE TABLE conge_demande (
    id_demande SERIAL PRIMARY KEY,
    description TEXT,
    id_employe INT,
    niveau_validation INT DEFAULT 2,
    CONSTRAINT fk_conge_demande_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

CREATE TABLE conge_historique_validation (
    id_historique_validation SERIAL PRIMARY KEY,
    id_demande INT,
    id_employe INT,
    date_validation TIMESTAMP,
    CONSTRAINT fk_conge_historique_validation_demande FOREIGN KEY (id_demande) REFERENCES conge_demande(id_demande),
    CONSTRAINT fk_conge_historique_validation_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

CREATE TABLE conge_historique (
    id_conge_historique SERIAL PRIMARY KEY,
    nombres_abscence_attribue DOUBLE PRECISION,
    id_employe INT,
    CONSTRAINT fk_conge_historique_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

CREATE TABLE conge_type (
    id_type SERIAL PRIMARY KEY,
    nom VARCHAR,
    description TEXT
);

CREATE TABLE abscence_conge_suivi (
    id_suivi SERIAL PRIMARY KEY,
    id_demande INT,
    id_type INT,
    id_employe INT,
    nombre_conge INt,
    annee INT,
    CONSTRAINT fk_conge_suivi_demande FOREIGN KEY (id_demande) REFERENCES conge_demande(id_demande),
    CONSTRAINT fk_conge_suivi_type FOREIGN KEY (id_type) REFERENCES conge_type(id_type),
    CONSTRAINT fk_conge_suivi_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

CREATE TABLE abscence (
    id_abscence SERIAL PRIMARY KEY,
    debut TIMESTAMP,
    fin TIMESTAMP,
    est_autorise BOOLEAN,
    justificatif TEXT
);