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

CREATE TABLE abscence_type_penalite ( 
    id_type_penalite SERIAL PRIMARY KEY,
    nom VARCHAR,
    description TEXT,
    montant DOUBLE PRECISION
);

CREATE TABLE abscence_conge_suivi (
    id_suivi SERIAL PRIMARY KEY,
    id_demande INT,
    id_abscence INT,
    id_type INT,
    id_employe INT,
    nombre_conge INt,
    annee INT,
    penalite_appliquee BOOLEAN DEFAULT FALSE,
    id_type_penalite INT,
    CONSTRAINT fk_conge_suivi_demande FOREIGN KEY (id_demande) REFERENCES conge_demande(id_demande),
    CONSTRAINT fk_conge_suivi_type FOREIGN KEY (id_type) REFERENCES conge_type(id_type),
    CONSTRAINT fk_conge_suivi_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe),
    CONSTRAINT fk_conge_suivi_abscence FOREIGN KEY (id_abscence) REFERENCES abscence(id_abscence)
    CONSTRAINT fk_conge_suivi_type_penalite FOREIGN KEY (id_type_penalite) REFERENCES abscence_type_penalite(id_type_penalite)
);

CREATE TABLE abscence (
    id_abscence SERIAL PRIMARY KEY,
    debut TIMESTAMP,
    fin TIMESTAMP,
    est_autorise BOOLEAN,
    justificatif TEXT
);


CREATE TABLE conge_demande (
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

CREATE TABLE conge_historique_validation (
    id_historique_validation SERIAL PRIMARY KEY,
    id_demande INT,
    id_employe INT,
    date_validation TIMESTAMP,
    CONSTRAINT fk_conge_historique_validation_demande FOREIGN KEY (id_demande) REFERENCES conge_demande(id_demande),
    CONSTRAINT fk_conge_historique_validation_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);
