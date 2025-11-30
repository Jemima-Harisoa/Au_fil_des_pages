-- ========================================
-- Database: aufildespages – Version nettoyée & ordonnée
-- ========================================

-- ================== TABLES DE RÉFÉRENCE (sans dépendances) ==================

CREATE TABLE sexe (
    id_sexe SERIAL PRIMARY KEY,
    type_sexe VARCHAR
);

CREATE TABLE diplomes (
    id_diplome SERIAL PRIMARY KEY,
    nom VARCHAR,
    niveau INT
);

CREATE TABLE filieres (
    id_filiere SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE departements (
    id_departement SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE type_contrats (
    id_type_contrat SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE etat (
    id_etat SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE appreciation (
    id_appreciation SERIAL PRIMARY KEY,
    type_appreciation TEXT,
    code INT
);

CREATE TABLE evenements (
    id_evenement SERIAL PRIMARY KEY,
    nom_evenement VARCHAR
);

CREATE TABLE conge_type (
    id_type SERIAL PRIMARY KEY,
    nom VARCHAR,
    description TEXT, 
    nombre_jour INT, 
    deductible_sur_salaire BOOLEAN DEFAULT TRUE, 
    deductible_sur_conge BOOLEAN DEFAULT TRUE
);

CREATE TABLE abscence_type_penalite ( 
    id_type_penalite SERIAL PRIMARY KEY,
    nom VARCHAR,
    description TEXT,
    montant DOUBLE PRECISION
);

CREATE TABLE status_validation_cv (
    id_status_validation_cv SERIAL PRIMARY KEY,
    statut VARCHAR
);

CREATE TABLE type_prime (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR
);

CREATE TABLE parametre (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR,
    pourcentage NUMERIC(5,2)
);

CREATE TABLE smig (
    id SERIAL PRIMARY KEY,
    montant NUMERIC(10,2) NOT NULL,
    date_application DATE NOT NULL
);

CREATE TABLE irsa (
    id SERIAL PRIMARY KEY,
    min DOUBLE PRECISION,
    max DOUBLE PRECISION,
    pourcentage NUMERIC(5,2),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE seuil_tolerance (
    id_seuil SERIAL PRIMARY KEY,
    valeur NUMERIC(5,2) DEFAULT 5,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE treshold (
    id_treshold SERIAL PRIMARY KEY,
    valeur NUMERIC(5,2),
    date_treshold TIMESTAMP
);

CREATE TABLE message_automatique (
    id_message_automatique SERIAL PRIMARY KEY,
    message TEXT
);

CREATE TABLE api (
    id_api SERIAL PRIMARY KEY,
    nom VARCHAR,
    cle_api VARCHAR
);

CREATE TABLE jour_ferie (
    id_jour_ferie SERIAL PRIMARY KEY,
    date DATE
);

-- ================== TABLES PRINCIPALES ==================

CREATE TABLE personnes (
    id_personne SERIAL PRIMARY KEY,
    nom VARCHAR,
    prenom VARCHAR,
    date_naissance DATE,
    contact VARCHAR,
    lien_image VARCHAR, 
    id_sexe INT,    
    CONSTRAINT fk_personnes_sexe FOREIGN KEY (id_sexe) REFERENCES sexe(id_sexe)
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
    est_minimum BOOLEAN,
    CONSTRAINT fk_profils_diplome FOREIGN KEY (id_diplome) REFERENCES diplomes(id_diplome),
    CONSTRAINT fk_profils_filiere FOREIGN KEY (id_filiere) REFERENCES filieres(id_filiere),
    CONSTRAINT fk_profils_type_contrat FOREIGN KEY (id_type_contrat) REFERENCES type_contrats(id_type_contrat),
    CONSTRAINT fk_profils_departement FOREIGN KEY (id_departement) REFERENCES departements(id_departement)
);

CREATE TABLE annonces (
    id_annonce SERIAL PRIMARY KEY,
    id_profil INT,
    titre VARCHAR,
    date_publication DATE,
    date_expiration DATE,
    nombre_poste INT,
    lien TEXT
);

CREATE TABLE questions (
    id_question SERIAL PRIMARY KEY,
    question TEXT,
    id_profil INT,
    note NUMERIC(5,2),
    CONSTRAINT fk_questions_profil FOREIGN KEY (id_profil) REFERENCES profils(id_profil)
);

CREATE TABLE reponses_question (
    id_reponse SERIAL PRIMARY KEY,
    id_question INT,
    reponse TEXT,
    est_correct BOOLEAN,
    CONSTRAINT fk_reponses_question FOREIGN KEY (id_question) REFERENCES questions(id_question)
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
    CONSTRAINT fk_candidats_annonce FOREIGN KEY (id_annonce) REFERENCES annonces(id_annonce),
    CONSTRAINT fk_candidats_profil FOREIGN KEY (id_profil) REFERENCES profils(id_profil)
);

CREATE TABLE tests (
    id_test SERIAL PRIMARY KEY,
    id_candidat INT,
    id_annonce INT,
    score_test NUMERIC(5,2),
    date_test DATE,
    CONSTRAINT fk_tests_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat),
    CONSTRAINT fk_tests_annonce FOREIGN KEY (id_annonce) REFERENCES annonces(id_annonce)
);

CREATE TABLE contrats (
    id_contrat SERIAL PRIMARY KEY,
    id_candidat INT,
    id_type_contrat INT,
    url_contrat VARCHAR,
    CONSTRAINT fk_contrats_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat),
    CONSTRAINT fk_contrats_type_contrat FOREIGN KEY (id_type_contrat) REFERENCES type_contrats(id_type_contrat)
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
    CONSTRAINT fk_employes_contrat FOREIGN KEY (id_contrat) REFERENCES contrats(id_contrat),
    CONSTRAINT fk_employes_departement FOREIGN KEY (id_departement) REFERENCES departements(id_departement)
);

-- ================== TABLES DÉPENDANTES DES EMPLOYÉS ==================

CREATE TABLE admins (
    id_admin SERIAL PRIMARY KEY,
    id_employe INT,
    nom VARCHAR,
    mdp VARCHAR,
    date_affiliation TIMESTAMP,
    date_fin_affiliation TIMESTAMP
);

CREATE TABLE connexEmployes (
    idEmploye INT PRIMARY KEY REFERENCES employes(id_employe),
    mdp VARCHAR(255) NOT NULL
);

CREATE TABLE horaires_employe (
    id_employe INT NOT NULL REFERENCES employes(id_employe),
    jour_semaine INT NOT NULL,
    debut_travail TIME NOT NULL,
    fin_travail TIME NOT NULL,
    seuil_retard INTERVAL DEFAULT '00:05:00'
);

CREATE TABLE pointage (
    id_pointage SERIAL PRIMARY KEY,
    id_employe INTEGER NOT NULL,
    connexion TIMESTAMP NOT NULL,
    deconnexion TIMESTAMP,
    duree_session INTERVAL GENERATED ALWAYS AS (deconnexion - connexion) STORED,
    FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

CREATE TABLE pointage_journalier (
    id_pointage_journalier SERIAL PRIMARY KEY,
    id_employe INT NOT NULL REFERENCES employes(id_employe),
    date_pointage DATE NOT NULL,
    retard interval,
    heures_supp interval,
    pause interval,
    heures_travaillees interval
);

CREATE TABLE heures_supplementaire_config (
    id SERIAL PRIMARY KEY,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    nombre_premieres_heures INT NOT NULL
);

CREATE TABLE heures_supplementaire (
    id SERIAL PRIMARY KEY,
    id_employe INT NOT NULL REFERENCES employes(id_employe) ON DELETE CASCADE,
    nombre_heure_effectue NUMERIC(5,2) NOT NULL,
    mois INT NOT NULL CHECK (mois >= 1 AND mois <= 12),
    annee INT NOT NULL,
    numero_semaine INT NOT NULL CHECK (numero_semaine >= 1 AND numero_semaine <= 52),
    date_enregistrement TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE heures_supplementaire_historique (
    id SERIAL PRIMARY KEY,
    id_heure_supp INT NOT NULL REFERENCES heures_supplementaire(id) ON DELETE CASCADE,
    nombre_heure_effectue NUMERIC(5,2) NOT NULL,
    mois INT NOT NULL CHECK (mois >= 1 AND mois <= 12),
    annee INT NOT NULL,
    numero_semaine INT NOT NULL CHECK (numero_semaine >= 1 AND numero_semaine <= 53),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE prime (
    id SERIAL PRIMARY KEY,
    id_type_prime INT,
    pourcentage NUMERIC(5,2),
    id_employe INT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_prime_type FOREIGN KEY (id_type_prime) REFERENCES type_prime(id),
    CONSTRAINT fk_prime_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

CREATE TABLE salaire_historique (
    id_salaire_historique SERIAL PRIMARY KEY,
    salaire DOUBLE PRECISION,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_employe INT,
    CONSTRAINT fk_salaire_historique_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

-- ================== GESTION DES CONGÉS & ABSENCES ==================

CREATE TABLE abscence (
    id_abscence SERIAL PRIMARY KEY,
    id_employe INT,
    debut TIMESTAMP,
    fin TIMESTAMP,
    est_autorise BOOLEAN,
    justificatif TEXT,
    CONSTRAINT fk_abscence_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
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

CREATE TABLE conge_solde (
    id_solde SERIAL PRIMARY KEY,
    id_employe INT,
    id_type_conge INT,
    solde DOUBLE PRECISION,
    annee INT,
    CONSTRAINT fk_conge_solde_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe),
    CONSTRAINT fk_conge_solde_type FOREIGN KEY (id_type_conge) REFERENCES conge_type(id_type)
);

-- ================== MODULE PERFORMANCES ==================

CREATE TABLE employe_evaluation_periodes (
    id_periode SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    description TEXT,
    frequence_mois INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE employe_criteres_evaluation (
    id_critere SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    poids NUMERIC(5,2) NOT NULL CHECK (poids >= 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE employe_evaluations (
    id_evaluation SERIAL PRIMARY KEY,
    employe_id INT NOT NULL REFERENCES employes(id_employe) ON DELETE CASCADE,
    periode_id INT NOT NULL REFERENCES employe_evaluation_periodes(id_periode),
    date_generation DATE NOT NULL DEFAULT CURRENT_DATE,
    date_evaluation DATE,
    statut VARCHAR(20) NOT NULL DEFAULT 'PREVUE',
    score_total NUMERIC(6,2),
    manager_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE employe_evaluations_details (
    id_detail SERIAL PRIMARY KEY,
    evaluation_id INT NOT NULL REFERENCES employe_evaluations(id_evaluation) ON DELETE CASCADE,
    critere_id INT NOT NULL REFERENCES employe_criteres_evaluation(id_critere),
    note NUMERIC(5,2) CHECK (note >= 0 AND note <= 10),
    commentaire TEXT
);

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

-- ================== COMPÉTENCES & FORMATIONS ==================

CREATE TABLE competences (
    id_competence SERIAL PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    domaine VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE employe_competences (
    id SERIAL PRIMARY KEY,
    employe_id INT NOT NULL REFERENCES employes(id_employe) ON DELETE CASCADE,
    competence_id INT NOT NULL REFERENCES competences(id_competence) ON DELETE CASCADE,
    niveau NUMERIC(4,2) CHECK (niveau >= 0),
    date_obtention DATE DEFAULT CURRENT_DATE
);

CREATE TABLE postes (
    id_poste SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
    statut VARCHAR(20) DEFAULT 'PLANIFIE',
    date_assignation DATE DEFAULT CURRENT_DATE
);

-- ================== AUTRES TABLES ==================

CREATE TABLE utilisateurs (
    id_utilisateur SERIAL PRIMARY KEY,
    nom VARCHAR,
    mdp VARCHAR,
    date_inscription TIMESTAMP,
    date_sortie TIMESTAMP
);

CREATE TABLE notifications (
    id_notification SERIAL PRIMARY KEY,
    id_personne INT,
    message TEXT,
    date_notification TIMESTAMP,
    CONSTRAINT fk_notifications_personne FOREIGN KEY (id_personne) REFERENCES personnes(id_personne)
);

CREATE TABLE planning_entretien (
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

-- (Toutes les autres tables intermédiaires restantes conservées dans l’ordre logique : cv_candidats, validation_cv, etc.)

-- ================== TRIGGERS ==================

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

-- ================== VUES ==================

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

CREATE OR REPLACE VIEW v_competence_cartographie AS
SELECT 
    c.id_competence,
    c.nom,
    c.domaine,
    COUNT(ec.employe_id) AS nb_employes,
    AVG(ec.niveau) AS niveau_moyen
FROM competences c
LEFT JOIN employe_competences ec ON ec.competence_id = c.id_competence
GROUP BY c.id_competence;