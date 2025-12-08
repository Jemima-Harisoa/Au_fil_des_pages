-- ========================================
-- Database: aufildespages
-- ========================================

-- ================== TABLES ==================

-- Tables de base sans dépendances
CREATE TABLE departements (
    id_departement SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE filieres (
    id_filiere SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE type_contrats (
    id_type_contrat SERIAL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE diplomes (
    id_diplome SERIAL PRIMARY KEY,
    nom VARCHAR,
    niveau INT
);

CREATE TABLE sexe (
    id_sexe SERIAL PRIMARY KEY,
    type_sexe VARCHAR
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

-- Tables avec dépendances simples
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

CREATE TABLE utilisateurs (
    id_utilisateur SERIAL PRIMARY KEY,
    nom VARCHAR,
    mdp VARCHAR,
    date_inscription TIMESTAMP,
    date_sortie TIMESTAMP
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

CREATE TABLE profilsCV (
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

-- Tables de recrutement
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

CREATE TABLE cv_candidats (
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

CREATE TABLE validation_cv (
    id_validation_cv SERIAL PRIMARY KEY,
    id_candidat INT,
    id_cv_candidat INT,
    id_status_validation_cv INT,
    similarite NUMERIC(5,2),
    CONSTRAINT fk_validation_cv_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat),
    CONSTRAINT fk_validation_cv_cv FOREIGN KEY (id_cv_candidat) REFERENCES cv_candidats(id_cv_candidats),
    CONSTRAINT fk_validation_cv_status FOREIGN KEY (id_status_validation_cv) REFERENCES status_validation_cv(id_status_validation_cv)
);

CREATE TABLE contrats (
    id_contrat SERIAL PRIMARY KEY,
    id_candidat INT,
    id_type_contrat INT,
    url_contrat VARCHAR,
    CONSTRAINT fk_contrats_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat),
    CONSTRAINT fk_contrats_type_contrat FOREIGN KEY (id_type_contrat) REFERENCES type_contrats(id_type_contrat)
);

-- Tables employés (après personnes et contrats)
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

-- Tables dépendantes des employés
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

CREATE TABLE disponibilite_employe (
    id_dispo SERIAL PRIMARY KEY,
    id_employe INT,
    heure_debut TIME,
    heure_fin TIME,
    CONSTRAINT fk_disponibilite_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
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

CREATE TABLE pointage (
    id_pointage SERIAL PRIMARY KEY,
    id_employe INT,
    connexion TIMESTAMP,
    deconnexion TIMESTAMP,
    duree_session INTERVAL,
    CONSTRAINT fk_pointage_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

CREATE TABLE heure_supplementaire_config (
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

CREATE TABLE salaire_historique (
    id_salaire_historique SERIAL PRIMARY KEY,
    salaire DOUBLE PRECISION,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_employe INT,
    CONSTRAINT fk_salaire_historique_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
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

CREATE TABLE preavis (
    id SERIAL PRIMARY KEY,
    id_employe INT NOT NULL REFERENCES employes(id_employe) ON DELETE CASCADE,
    date_debut_preavis DATE NOT NULL,
    date_fin_preavis DATE NOT NULL,
    est_termine BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tables de gestion des congés et absences
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

CREATE TABLE conge_historique (
    id_conge_historique SERIAL PRIMARY KEY,
    nombres_abscence_attribue DOUBLE PRECISION,
    id_employe INT,
    CONSTRAINT fk_conge_historique_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
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

CREATE TABLE abscence_conge_suivi (
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

-- Tables d'entretiens et mobilité
CREATE TABLE responsable_entretien (
    id_responsable SERIAL PRIMARY KEY,
    id_profil INT,
    id_employe INT,
    ordre_passage INT,
    CONSTRAINT fk_responsable_entretien_profil FOREIGN KEY (id_profil) REFERENCES profils(id_profil),
    CONSTRAINT fk_responsable_entretien_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe)
);

CREATE TABLE disponibilite_entretien (
    id_dispo SERIAL PRIMARY KEY,
    id_responsable INT,
    heure_debut TIME,
    heure_fin TIME,
    jour INT,
    est_valide BOOLEAN,
    CONSTRAINT fk_disponibilite_entretien_responsable FOREIGN KEY (id_responsable) REFERENCES responsable_entretien(id_responsable)
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

CREATE TABLE config_entretien (
    id_config_entretien SERIAL PRIMARY KEY,
    id_departement INT,
    duree_entretien VARCHAR,
    CONSTRAINT fk_config_entretien_departement FOREIGN KEY (id_departement) REFERENCES departements(id_departement)
);

CREATE TABLE historique_validation (
    id_historique_validation SERIAL PRIMARY KEY,
    id_employe INT,
    id_candidat INT,
    date_heure_validation TIMESTAMP,
    id_etat INT,
    CONSTRAINT fk_historique_validation_employe FOREIGN KEY (id_employe) REFERENCES employes(id_employe),
    CONSTRAINT fk_historique_validation_candidat FOREIGN KEY (id_candidat) REFERENCES candidats(id_candidat),
    CONSTRAINT fk_historique_validation_etat FOREIGN KEY (id_etat) REFERENCES etat(id_etat)
);

CREATE TABLE historique_mobilite (
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

-- Tables diverses
CREATE TABLE essais (
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

CREATE TABLE notifications (
    id_notification SERIAL PRIMARY KEY,
    id_personne INT,
    message TEXT,
    date_notification TIMESTAMP,
    CONSTRAINT fk_notifications_personne FOREIGN KEY (id_personne) REFERENCES personnes(id_personne)
);

-- ========================================
-- Tables pour la gestion des compétences
-- ========================================
CREATE TABLE IF NOT EXISTS metier_competences_requises (
    id_metier SERIAL PRIMARY KEY,
    libelle_metier VARCHAR(100) NOT NULL,
    id_departement INT REFERENCES departements(id_departement),
    description TEXT,
    competences_requises JSONB, -- Stocke les compétences requises avec niveaux attendus
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des types de compétences
CREATE TABLE IF NOT EXISTS type_competence (
    id_type_competence SERIAL PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL,
    description TEXT
);

-- Table des sources d'évaluation
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
WITH stats_employes AS (
    SELECT COUNT(DISTINCT id_employe) as total_employes
    FROM employes
    WHERE date_embauche IS NOT NULL
),
competences_employes AS (
    SELECT 
        ec.id_competence,
        ec.id_employe,
        ec.niveau,
        ec.valide,
        ec.date_mesure,         -- <-- ajouté
        ec.date_validation,     -- optionnel mais utile
        e.id_departement,
        e.poste,
        e.salaire_base,
        e.date_embauche,
        EXTRACT(YEAR FROM AGE(CURRENT_DATE, e.date_embauche)) as anciennete
    FROM employe_competences ec
    JOIN employes e ON ec.id_employe = e.id_employe
    WHERE ec.valide = TRUE
),
aggregated AS (
    SELECT
        c.id_competence,
        c.nom,
        c.description,
        c.domaine,
        tc.libelle AS type_competence,
        
        -- Nombre d'employés ayant cette compétence (validée)
        COUNT(DISTINCT ce.id_employe) AS nb_employes,
        
        -- Pourcentage par rapport au total des employés
        ROUND(COUNT(DISTINCT ce.id_employe) * 100.0 / NULLIF((SELECT total_employes FROM stats_employes), 0), 2) AS pourcentage_employes,
        
        -- Niveau moyen pondéré par ancienneté et validation
        ROUND(AVG(
            CASE 
                WHEN ce.anciennete > 5 THEN ce.niveau * 1.2
                WHEN ce.anciennete BETWEEN 3 AND 5 THEN ce.niveau * 1.1
                ELSE ce.niveau * 1.0
            END
        )::numeric, 2) AS niveau_moyen_pondere,
        
        -- Niveau moyen simple
        ROUND(AVG(ce.niveau)::numeric, 2) AS niveau_moyen_simple,
        
        -- Distribution par niveau
        COUNT(*) FILTER (WHERE ce.niveau = 1) AS nb_debutants,
        COUNT(*) FILTER (WHERE ce.niveau = 2) AS nb_intermediaires,
        COUNT(*) FILTER (WHERE ce.niveau = 3) AS nb_avances,
        COUNT(*) FILTER (WHERE ce.niveau = 4) AS nb_experts,
        COUNT(*) FILTER (WHERE ce.niveau = 5) AS nb_maitres,
        
        -- Distribution par département
        ARRAY_AGG(DISTINCT d.nom) AS departements_concernes,
        COUNT(DISTINCT ce.id_departement) AS nb_departements,
        
        -- Métiers principaux concernés
        ARRAY_AGG(DISTINCT ce.poste) FILTER (WHERE ce.poste IS NOT NULL) AS postes_concernes,
        
        -- Dernière mise à jour
        MAX(ce.date_mesure) AS derniere_maj
    FROM competences c
    LEFT JOIN competences_employes ce ON c.id_competence = ce.id_competence
    LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
    LEFT JOIN departements d ON ce.id_departement = d.id_departement
    GROUP BY c.id_competence, c.nom, c.description, c.domaine, tc.libelle
)
SELECT 
    a.id_competence,
    a.nom,
    a.description,
    a.domaine,
    a.type_competence,
    
    -- Statistiques de base
    COALESCE(a.nb_employes, 0) AS nb_employes,
    a.pourcentage_employes,
    
    -- Niveaux avec vérification de cohérence
    LEAST(5.0, COALESCE(a.niveau_moyen_pondere, 0)) AS niveau_moyen,
    ROUND(LEAST(5.0, COALESCE(a.niveau_moyen_pondere, 0))::numeric, 0) AS niv_moy_rounded,
    ncl.libelle AS libelle_niveau_moyen,
    
    -- Distribution par niveau avec pourcentages
    COALESCE(a.nb_debutants, 0) AS nb_debutants,
    COALESCE(a.nb_intermediaires, 0) AS nb_intermediaires,
    COALESCE(a.nb_avances, 0) AS nb_avances,
    COALESCE(a.nb_experts, 0) AS nb_experts,
    COALESCE(a.nb_maitres, 0) AS nb_maitres,
    
    -- Pourcentages avec vérification division par zéro
    CASE WHEN COALESCE(a.nb_employes, 0) = 0 THEN 0
         ELSE ROUND(a.nb_debutants * 100.0 / a.nb_employes, 2) END AS pourcentage_debutants,
    CASE WHEN COALESCE(a.nb_employes, 0) = 0 THEN 0
         ELSE ROUND(a.nb_intermediaires * 100.0 / a.nb_employes, 2) END AS pourcentage_intermediaires,
    CASE WHEN COALESCE(a.nb_employes, 0) = 0 THEN 0
         ELSE ROUND(a.nb_avances * 100.0 / a.nb_employes, 2) END AS pourcentage_avances,
    CASE WHEN COALESCE(a.nb_employes, 0) = 0 THEN 0
         ELSE ROUND(a.nb_experts * 100.0 / a.nb_employes, 2) END AS pourcentage_experts,
    CASE WHEN COALESCE(a.nb_employes, 0) = 0 THEN 0
         ELSE ROUND(a.nb_maitres * 100.0 / a.nb_employes, 2) END AS pourcentage_maitres,
    
    -- Métadonnées
    a.departements_concernes,
    a.nb_departements,
    a.postes_concernes,
    a.derniere_maj
FROM aggregated a
LEFT JOIN niveau_competence_libelle ncl
  ON ROUND(LEAST(5.0, COALESCE(a.niveau_moyen_pondere, 0))::numeric, 0) = ncl.niveau;

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



CREATE OR REPLACE VIEW v_evaluation_competences_metier AS
WITH stats_employes AS (
    SELECT 
        e.id_employe,
        p.nom,
        p.prenom,
        e.poste,
        d.nom AS departement,
        mcr.libelle_metier,
        mcr.competences_requises,
        EXTRACT(YEAR FROM AGE(CURRENT_DATE, e.date_embauche)) as anciennete
    FROM employes e
    JOIN personnes p ON e.id_personne = p.id_personne
    JOIN departements d ON e.id_departement = d.id_departement
    LEFT JOIN metier_competences_requises mcr ON e.poste ILIKE '%' || mcr.libelle_metier || '%'
),
competences_employes AS (
    SELECT 
        ec.id_employe,
        ec.id_competence,
        c.nom AS nom_competence,
        ec.niveau AS niveau_actuel,
        ec.valide,
        ec.date_mesure
    FROM employe_competences ec
    JOIN competences c ON ec.id_competence = c.id_competence
    WHERE ec.valide = TRUE
),
evaluation AS (
    SELECT 
        se.id_employe,
        se.nom,
        se.prenom,
        se.poste,
        se.departement,
        se.libelle_metier,
        ce.nom_competence,
        ce.niveau_actuel,
        
        (se.competences_requises->>ce.nom_competence)::INTEGER AS niveau_requis,
        
        CASE 
            WHEN ce.niveau_actuel >= (se.competences_requises->>ce.nom_competence)::INTEGER THEN 0
            ELSE (se.competences_requises->>ce.nom_competence)::INTEGER - ce.niveau_actuel
        END AS ecart_niveau,
        
        CASE 
            WHEN se.anciennete > 5 THEN ce.niveau_actuel * 1.3
            WHEN se.anciennete BETWEEN 3 AND 5 THEN ce.niveau_actuel * 1.2
            WHEN se.anciennete BETWEEN 1 AND 3 THEN ce.niveau_actuel * 1.1
            ELSE ce.niveau_actuel * 1.0
        END AS score_pondere,
        
        ce.date_mesure
    FROM stats_employes se
    JOIN competences_employes ce ON se.id_employe = ce.id_employe
    WHERE se.competences_requises IS NOT NULL
      AND se.competences_requises ? ce.nom_competence
)
SELECT 
    id_employe,
    nom,
    prenom,
    poste,
    departement,
    libelle_metier,
    
    COUNT(*) AS nb_competences_evaluees,
    AVG(niveau_actuel) AS niveau_moyen_actuel,
    AVG(niveau_requis) AS niveau_moyen_requis,
    SUM(ecart_niveau) AS total_ecarts,
    
    ROUND(AVG(score_pondere)::numeric, 2) AS score_performance,
    
    CASE 
        WHEN AVG(score_pondere) >= 4.5 THEN 'Exceptionnel'
        WHEN AVG(score_pondere) >= 3.5 THEN 'Bon'
        WHEN AVG(score_pondere) >= 2.5 THEN 'Satisfaisant'
        WHEN AVG(score_pondere) >= 1.5 THEN 'À améliorer'
        ELSE 'Insuffisant'
    END AS evaluation_globale,
    
    JSON_AGG(
        JSON_BUILD_OBJECT(
            'competence', nom_competence,
            'niveau_actuel', niveau_actuel,
            'niveau_requis', niveau_requis,
            'ecart', ecart_niveau,
            'score', score_pondere
        )
    ) AS details_competences
FROM evaluation
GROUP BY id_employe, nom, prenom, poste, departement, libelle_metier;

-- ========================================
-- TABLES DE TRAITEMENT ET AGRÉGATION
-- ========================================

-- Table de configuration des priorités de sources
CREATE TABLE IF NOT EXISTS source_priorite (
    id_source INT PRIMARY KEY REFERENCES source_evaluation(id_source),
    priorite INT NOT NULL, -- Plus le chiffre est élevé, plus la priorité est haute
    description TEXT,
    UNIQUE(priorite)
);

-- Insertion des priorités (test > manager > rh > auto)
INSERT INTO source_priorite (id_source, priorite, description) 
SELECT s.id_source, 
       CASE s.libelle
           WHEN 'test' THEN 4
           WHEN 'manager-evaluation' THEN 3
           WHEN 'rh-evaluation' THEN 2
           WHEN 'auto-evaluation' THEN 1
           ELSE 0
       END,
       'Priorité automatique basée sur le type de source'
FROM source_evaluation s
ON CONFLICT (id_source) DO UPDATE SET
    priorite = EXCLUDED.priorite,
    description = EXCLUDED.description;

-- Table des snapshots pour l'historique et les trends
CREATE TABLE IF NOT EXISTS competence_snapshot (
    id_snapshot SERIAL PRIMARY KEY,
    date_snapshot TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_competence INT REFERENCES competences(id_competence),
    nb_employes INT,
    niveau_moyen NUMERIC(4,2),
    taux_couverture NUMERIC(5,2),
    nb_experts INT, -- niveau >= 4
    metadata JSONB -- stockage flexible pour autres métriques
);

-- Index pour recherches rapides sur snapshots
CREATE INDEX idx_snapshot_date ON competence_snapshot(date_snapshot);
CREATE INDEX idx_snapshot_competence ON competence_snapshot(id_competence);

-- Table des alertes générées automatiquement
CREATE TABLE IF NOT EXISTS competence_alertes (
    id_alerte SERIAL PRIMARY KEY,
    type_alerte VARCHAR(50) NOT NULL, -- 'gap', 'soft_skills_faible', 'recrutement_requis'
    id_competence INT REFERENCES competences(id_competence),
    id_employe INT REFERENCES employes(id_employe),
    id_departement INT REFERENCES departements(id_departement),
    severite VARCHAR(20) DEFAULT 'moyenne', -- 'faible', 'moyenne', 'haute', 'critique'
    message TEXT,
    metadata JSONB,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_resolution TIMESTAMP,
    est_resolue BOOLEAN DEFAULT FALSE
);

-- Index pour filtrage rapide des alertes
CREATE INDEX idx_alertes_type ON competence_alertes(type_alerte);
CREATE INDEX idx_alertes_resolue ON competence_alertes(est_resolue);
CREATE INDEX idx_alertes_severite ON competence_alertes(severite);

-- Table des recommandations d'actions
CREATE TABLE IF NOT EXISTS competence_recommandations (
    id_recommandation SERIAL PRIMARY KEY,
    id_alerte INT REFERENCES competence_alertes(id_alerte),
    type_action VARCHAR(50) NOT NULL, -- 'formation', 'team_building', 'recrutement', 'mentorat'
    id_employe INT REFERENCES employes(id_employe),
    id_competence INT REFERENCES competences(id_competence),
    priorite INT CHECK (priorite BETWEEN 1 AND 5),
    description TEXT,
    ressources_suggerees JSONB, -- liens vers formations, contacts mentors, etc.
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_application TIMESTAMP,
    statut VARCHAR(20) DEFAULT 'en_attente' -- 'en_attente', 'en_cours', 'terminee', 'annulee'
);

-- Table de cache pour les indicateurs fréquemment calculés
CREATE TABLE IF NOT EXISTS competence_cache (
    cache_key VARCHAR(255) PRIMARY KEY,
    cache_value JSONB NOT NULL,
    ttl TIMESTAMP NOT NULL, -- Time To Live
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour nettoyage automatique du cache expiré
CREATE INDEX idx_cache_ttl ON competence_cache(ttl);

-- Table de logs d'import/validation pour audit
CREATE TABLE IF NOT EXISTS competence_audit_log (
    id_log SERIAL PRIMARY KEY,
    operation_type VARCHAR(50) NOT NULL, -- 'import', 'validation', 'fusion', 'calcul_indicateurs'
    id_employe_operateur INT REFERENCES employes(id_employe),
    details JSONB, -- contexte complet de l'opération
    nb_lignes_affectees INT,
    statut VARCHAR(20) DEFAULT 'success', -- 'success', 'error', 'warning'
    message_erreur TEXT,
    date_operation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour recherche dans les logs
CREATE INDEX idx_audit_log_date ON competence_audit_log(date_operation);
CREATE INDEX idx_audit_log_type ON competence_audit_log(operation_type);
CREATE INDEX idx_audit_log_statut ON competence_audit_log(statut);

-- Table de normalisation des noms de compétences
CREATE TABLE IF NOT EXISTS competence_normalisation (
    id_normalisation SERIAL PRIMARY KEY,
    nom_original VARCHAR(255) NOT NULL,
    nom_normalise VARCHAR(255) NOT NULL,
    score_similarite NUMERIC(3,2), -- pour fuzzy matching
    valide BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(nom_original)
);

-- Table de mapping compétences requises par profil (version structurée)
CREATE TABLE IF NOT EXISTS profil_competences_requises (
    id SERIAL PRIMARY KEY,
    id_profil INT REFERENCES profils(id_profil),
    id_competence INT REFERENCES competences(id_competence),
    niveau_requis INT CHECK (niveau_requis BETWEEN 1 AND 5),
    est_critique BOOLEAN DEFAULT FALSE,
    UNIQUE(id_profil, id_competence)
);

-- ========================================
-- FONCTIONS DE TRAITEMENT ETL
-- ========================================

-- 1. FONCTION DE NORMALISATION DES NOMS DE COMPÉTENCES
CREATE OR REPLACE FUNCTION normaliser_competence(p_nom VARCHAR)
RETURNS VARCHAR AS $$
BEGIN
    -- Lowercase, trim, suppression caractères spéciaux multiples
    RETURN LOWER(TRIM(REGEXP_REPLACE(p_nom, '\s+', ' ', 'g')));
END;
$$ LANGUAGE plpgsql IMMUTABLE;

-- 2. FONCTION DE CALCUL DE SIMILARITÉ (Levenshtein simplifié)
CREATE OR REPLACE FUNCTION calculer_similarite(str1 VARCHAR, str2 VARCHAR)
RETURNS NUMERIC AS $$
DECLARE
    distance INT;
    max_length INT;
BEGIN
    -- Utilise la distance de Levenshtein si l'extension est disponible
    -- Sinon, comparaison simple
    str1 := normaliser_competence(str1);
    str2 := normaliser_competence(str2);
    
    IF str1 = str2 THEN
        RETURN 1.0;
    END IF;
    
    -- Calcul simple basé sur la longueur commune
    max_length := GREATEST(LENGTH(str1), LENGTH(str2));
    IF max_length = 0 THEN
        RETURN 0.0;
    END IF;
    
    -- Approximation : pourcentage de caractères communs
    distance := LENGTH(str1) + LENGTH(str2) - 2 * LENGTH(
        REGEXP_REPLACE(
            CONCAT(str1, str2),
            '[^' || REGEXP_REPLACE(str1, '(.)', '\1', 'g') || ']',
            '',
            'g'
        )
    );
    
    RETURN ROUND((1.0 - distance::NUMERIC / max_length), 2);
END;
$$ LANGUAGE plpgsql;

-- 3. FONCTION DE DÉTECTION ET FUSION DES DOUBLONS
CREATE OR REPLACE FUNCTION detecter_doublons_competences(p_seuil NUMERIC DEFAULT 0.9)
RETURNS TABLE(
    id_competence_1 INT,
    nom_1 VARCHAR,
    id_competence_2 INT,
    nom_2 VARCHAR,
    similarite NUMERIC
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        c1.id_competence,
        c1.nom,
        c2.id_competence,
        c2.nom,
        calculer_similarite(c1.nom, c2.nom) as sim
    FROM competences c1
    JOIN competences c2 ON c1.id_competence < c2.id_competence
    WHERE calculer_similarite(c1.nom, c2.nom) >= p_seuil
    ORDER BY sim DESC;
END;
$$ LANGUAGE plpgsql;

-- 4. FONCTION POUR OBTENIR LA MEILLEURE COMPÉTENCE PAR PRIORITÉ
CREATE OR REPLACE FUNCTION obtenir_meilleure_competence_employe(
    p_id_employe INT,
    p_id_competence INT
)
RETURNS TABLE(
    id INT,
    niveau INT,
    id_source INT,
    source_libelle VARCHAR,
    priorite INT,
    date_mesure TIMESTAMP,
    valide BOOLEAN
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        ec.id,
        ec.niveau,
        ec.id_source,
        s.libelle,
        COALESCE(sp.priorite, 0) as prio,
        ec.date_mesure,
        ec.valide
    FROM employe_competences ec
    JOIN source_evaluation s ON ec.id_source = s.id_source
    LEFT JOIN source_priorite sp ON s.id_source = sp.id_source
    WHERE ec.id_employe = p_id_employe
      AND ec.id_competence = p_id_competence
    ORDER BY 
        COALESCE(sp.priorite, 0) DESC,
        ec.date_mesure DESC
    LIMIT 1;
END;
$$ LANGUAGE plpgsql;

-- 5. FONCTION DE CALCUL DES GAPS PAR EMPLOYÉ
CREATE OR REPLACE FUNCTION calculer_gaps_employe(p_id_employe INT)
RETURNS TABLE(
    id_competence INT,
    nom_competence VARCHAR,
    niveau_actuel INT,
    niveau_requis INT,
    gap INT,
    est_critique BOOLEAN
) AS $$
BEGIN
    RETURN QUERY
    WITH employe_profil AS (
        SELECT e.id_employe, e.poste, c.id_profil
        FROM employes e
        LEFT JOIN candidats cand ON e.id_personne = cand.id_personne
        LEFT JOIN contrats co ON cand.id_candidat = co.id_candidat
        LEFT JOIN profils c ON cand.id_profil = c.id_profil
        WHERE e.id_employe = p_id_employe
    ),
    competences_requises AS (
        SELECT 
            pcr.id_competence,
            c.nom,
            pcr.niveau_requis,
            pcr.est_critique
        FROM profil_competences_requises pcr
        JOIN competences c ON pcr.id_competence = c.id_competence
        WHERE pcr.id_profil = (SELECT id_profil FROM employe_profil LIMIT 1)
    ),
    competences_actuelles AS (
        SELECT 
            ec.id_competence,
            MAX(ec.niveau) as niveau_max
        FROM employe_competences ec
        WHERE ec.id_employe = p_id_employe
          AND ec.valide = TRUE
        GROUP BY ec.id_competence
    )
    SELECT 
        cr.id_competence,
        cr.nom,
        COALESCE(ca.niveau_max, 0) as niv_actuel,
        cr.niveau_requis as niv_requis,
        GREATEST(0, cr.niveau_requis - COALESCE(ca.niveau_max, 0)) as ecart,
        cr.est_critique
    FROM competences_requises cr
    LEFT JOIN competences_actuelles ca ON cr.id_competence = ca.id_competence
    WHERE cr.niveau_requis > COALESCE(ca.niveau_max, 0)
    ORDER BY cr.est_critique DESC, ecart DESC;
END;
$$ LANGUAGE plpgsql;

-- 6. FONCTION DE CALCUL DES INDICATEURS GLOBAUX
CREATE OR REPLACE FUNCTION calculer_indicateurs_competence(p_id_competence INT)
RETURNS TABLE(
    id_competence INT,
    nb_employes INT,
    niveau_moyen NUMERIC,
    taux_couverture NUMERIC,
    nb_experts INT,
    nb_par_niveau JSONB,
    trend_3mois NUMERIC
) AS $$
DECLARE
    v_total_employes INT;
    v_niveau_moyen_actuel NUMERIC;
    v_niveau_moyen_3mois NUMERIC;
BEGIN
    -- Calculer le nombre total d'employés actifs
    SELECT COUNT(*) INTO v_total_employes
    FROM employes
    WHERE date_embauche IS NOT NULL;
    
    RETURN QUERY
    WITH stats_actuelles AS (
        SELECT 
            ec.id_competence,
            COUNT(DISTINCT ec.id_employe) as nb_emp,
            ROUND(AVG(ec.niveau)::NUMERIC, 2) as niv_moy,
            COUNT(DISTINCT CASE WHEN ec.niveau >= 4 THEN ec.id_employe END) as nb_exp,
            JSONB_BUILD_OBJECT(
                'niveau_1', COUNT(CASE WHEN ec.niveau = 1 THEN 1 END),
                'niveau_2', COUNT(CASE WHEN ec.niveau = 2 THEN 1 END),
                'niveau_3', COUNT(CASE WHEN ec.niveau = 3 THEN 1 END),
                'niveau_4', COUNT(CASE WHEN ec.niveau = 4 THEN 1 END),
                'niveau_5', COUNT(CASE WHEN ec.niveau = 5 THEN 1 END)
            ) as distribution
        FROM employe_competences ec
        WHERE ec.id_competence = p_id_competence
          AND ec.valide = TRUE
        GROUP BY ec.id_competence
    ),
    stats_passees AS (
        SELECT 
            ROUND(AVG(niveau_moyen)::NUMERIC, 2) as niv_moy_passe
        FROM competence_snapshot
        WHERE id_competence = p_id_competence
          AND date_snapshot >= CURRENT_TIMESTAMP - INTERVAL '3 months'
          AND date_snapshot < CURRENT_TIMESTAMP - INTERVAL '1 week'
    )
    SELECT 
        p_id_competence,
        COALESCE(sa.nb_emp, 0),
        COALESCE(sa.niv_moy, 0),
        ROUND((COALESCE(sa.nb_emp, 0)::NUMERIC / NULLIF(v_total_employes, 0)) * 100, 2),
        COALESCE(sa.nb_exp, 0),
        COALESCE(sa.distribution, '{}'::JSONB),
        COALESCE(sa.niv_moy, 0) - COALESCE(sp.niv_moy_passe, 0) as tendance
    FROM stats_actuelles sa
    FULL OUTER JOIN stats_passees sp ON TRUE;
END;
$$ LANGUAGE plpgsql;

-- 7. FONCTION DE GÉNÉRATION D'ALERTES
CREATE OR REPLACE FUNCTION generer_alertes_competences() --
RETURNS INT AS $$
DECLARE
    v_nb_alertes INT := 0;
    v_last_alertes INT := 0;
    v_seuil_soft_skills NUMERIC := 2.5;
    v_seuil_couverture NUMERIC := 30.0; -- 30% minimum
BEGIN
    -- Alertes pour soft skills faibles par département
    INSERT INTO competence_alertes (
        type_alerte,
        id_competence,
        id_departement,
        severite,
        message,
        metadata
    )
    SELECT
        'soft_skills_faible',
        c.id_competence,
        e.id_departement,
        CASE
            WHEN AVG(ec.niveau) < 2.0 THEN 'haute'
            WHEN AVG(ec.niveau) < 2.5 THEN 'moyenne'
            ELSE 'faible'
        END,
        'Niveau moyen de soft skill "' || c.nom || '" insuffisant dans le département ' || d.nom,
        JSONB_BUILD_OBJECT(
            'niveau_moyen', ROUND(AVG(ec.niveau)::NUMERIC, 2),
            'nb_employes', COUNT(DISTINCT e.id_employe)
        )
    FROM employes e
    JOIN employe_competences ec ON e.id_employe = ec.id_employe
    JOIN competences c ON ec.id_competence = c.id_competence
    JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
    JOIN departements d ON e.id_departement = d.id_departement
    WHERE tc.libelle ILIKE '%soft%'
      AND ec.valide = TRUE
    GROUP BY c.id_competence, c.nom, e.id_departement, d.nom
    HAVING AVG(ec.niveau) < v_seuil_soft_skills
    ON CONFLICT DO NOTHING;

    -- récupérer le nombre de lignes insérées et l'ajouter au total
    GET DIAGNOSTICS v_last_alertes = ROW_COUNT;
    v_nb_alertes := v_nb_alertes + COALESCE(v_last_alertes, 0);

    -- Alertes pour compétences critiques avec faible couverture
    INSERT INTO competence_alertes (
        type_alerte,
        id_competence,
        id_departement,
        severite,
        message,
        metadata
    )
    SELECT
        'recrutement_requis',
        pcr.id_competence,
        p.id_departement,
        'haute',
        'Compétence critique "' || c.nom || '" avec couverture insuffisante pour le profil ' || p.titre,
        JSONB_BUILD_OBJECT(
            'taux_couverture', ROUND((COUNT(DISTINCT ec.id_employe)::NUMERIC /
                                     NULLIF((SELECT COUNT(*) FROM employes WHERE id_departement = p.id_departement), 0)) * 100, 2),
            'niveau_requis', pcr.niveau_requis
        )
    FROM profil_competences_requises pcr
    JOIN profils p ON pcr.id_profil = p.id_profil
    JOIN competences c ON pcr.id_competence = c.id_competence
    LEFT JOIN employe_competences ec ON c.id_competence = ec.id_competence
        AND ec.valide = TRUE
        AND ec.niveau >= pcr.niveau_requis
    WHERE pcr.est_critique = TRUE
    GROUP BY pcr.id_competence, c.nom, p.id_departement, p.titre, pcr.niveau_requis
    HAVING (COUNT(DISTINCT ec.id_employe)::NUMERIC /
            NULLIF((SELECT COUNT(*) FROM employes WHERE id_departement = p.id_departement), 0)) * 100 < v_seuil_couverture
    ON CONFLICT DO NOTHING;

    -- récupérer le nombre de lignes insérées pour ce second INSERT
    GET DIAGNOSTICS v_last_alertes = ROW_COUNT;
    v_nb_alertes := v_nb_alertes + COALESCE(v_last_alertes, 0);

    -- Log de l'opération
    INSERT INTO competence_audit_log (operation_type, nb_lignes_affectees, details)
    VALUES ('generer_alertes', v_nb_alertes, JSONB_BUILD_OBJECT('timestamp', NOW()));

    RETURN v_nb_alertes;
END;
$$ LANGUAGE plpgsql;

-- 8. FONCTION DE GÉNÉRATION DE RECOMMANDATIONS
CREATE OR REPLACE FUNCTION generer_recommandations()
RETURNS INT AS $$
DECLARE
    v_nb_recommandations INT := 0;
    rec RECORD;
BEGIN
    -- Recommandations de formation basées sur les gaps
    FOR rec IN 
        SELECT 
            a.id_alerte,
            a.id_competence,
            e.id_employe,
            gap.gap
        FROM competence_alertes a
        CROSS JOIN LATERAL (
            SELECT id_employe FROM employes 
            WHERE id_departement = a.id_departement
        ) e
        CROSS JOIN LATERAL calculer_gaps_employe(e.id_employe) gap
        WHERE a.type_alerte IN ('soft_skills_faible', 'recrutement_requis')
          AND a.est_resolue = FALSE
          AND gap.id_competence = a.id_competence
          AND gap.gap > 0
    LOOP
        INSERT INTO competence_recommandations (
            id_alerte,
            type_action,
            id_employe,
            id_competence,
            priorite,
            description
        ) VALUES (
            rec.id_alerte,
            'formation',
            rec.id_employe,
            rec.id_competence,
            LEAST(5, rec.gap + 2), -- Priorité basée sur le gap
            'Formation recommandée pour combler l''écart de compétence'
        )
        ON CONFLICT DO NOTHING;
        
        v_nb_recommandations := v_nb_recommandations + 1;
    END LOOP;
    
    RETURN v_nb_recommandations;
END;
$$ LANGUAGE plpgsql;

-- 9. FONCTION DE NETTOYAGE DU CACHE EXPIRÉ
CREATE OR REPLACE FUNCTION nettoyer_cache_expire()
RETURNS INT AS $$
DECLARE
    v_nb_supprime INT;
BEGIN
    DELETE FROM competence_cache
    WHERE ttl < CURRENT_TIMESTAMP;
    
    GET DIAGNOSTICS v_nb_supprime = ROW_COUNT;
    
    RETURN v_nb_supprime;
END;
$$ LANGUAGE plpgsql;

-- 10. FONCTION DE CRÉATION DE SNAPSHOT
CREATE OR REPLACE FUNCTION creer_snapshot_competences()
RETURNS INT AS $$
DECLARE
    v_nb_snapshots INT := 0;
BEGIN
    INSERT INTO competence_snapshot (
        id_competence,
        nb_employes,
        niveau_moyen,
        taux_couverture,
        nb_experts,
        metadata
    )
    SELECT 
        ind.id_competence,
        ind.nb_employes,
        ind.niveau_moyen,
        ind.taux_couverture,
        ind.nb_experts,
        JSONB_BUILD_OBJECT(
            'nb_par_niveau', ind.nb_par_niveau,
            'trend_3mois', ind.trend_3mois
        )
    FROM competences c
    CROSS JOIN LATERAL calculer_indicateurs_competence(c.id_competence) ind;
    
    GET DIAGNOSTICS v_nb_snapshots = ROW_COUNT;
    
    -- Log
    INSERT INTO competence_audit_log (operation_type, nb_lignes_affectees)
    VALUES ('snapshot', v_nb_snapshots);
    
    RETURN v_nb_snapshots;
END;
$$ LANGUAGE plpgsql;

-- ========================================
-- TRIGGERS ET AUTOMATISATIONS
-- ========================================

-- 1. TRIGGER : Auto-normalisation lors de l'insertion de compétences
CREATE OR REPLACE FUNCTION trigger_normaliser_competence()
RETURNS TRIGGER AS $$
BEGIN
    NEW.nom := normaliser_competence(NEW.nom);
    
    -- Vérifier si une compétence similaire existe déjà
    IF EXISTS (
        SELECT 1 FROM competences 
        WHERE id_competence != COALESCE(NEW.id_competence, 0)
          AND calculer_similarite(nom, NEW.nom) >= 0.9
    ) THEN
        -- Logger l'alerte de doublon potentiel
        INSERT INTO competence_audit_log (
            operation_type, 
            statut, 
            message_erreur,
            details
        ) VALUES (
            'insert_competence',
            'warning',
            'Compétence similaire détectée : ' || NEW.nom,
            JSONB_BUILD_OBJECT('nom_nouveau', NEW.nom)
        );
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER before_insert_competence
    BEFORE INSERT ON competences
    FOR EACH ROW
    EXECUTE FUNCTION trigger_normaliser_competence();

-- 2. TRIGGER : Invalidation du cache lors de modifications
CREATE OR REPLACE FUNCTION trigger_invalider_cache_competence()
RETURNS TRIGGER AS $$
BEGIN
    -- Supprimer les entrées de cache liées à cette compétence
    DELETE FROM competence_cache
    WHERE cache_key LIKE '%competence_' || COALESCE(NEW.id_competence, OLD.id_competence) || '%';
    
    -- Supprimer aussi les caches globaux
    DELETE FROM competence_cache
    WHERE cache_key IN ('stats_globales', 'dashboard_competences');
    
    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER after_employe_competence_change
    AFTER INSERT OR UPDATE OR DELETE ON employe_competences
    FOR EACH ROW
    EXECUTE FUNCTION trigger_invalider_cache_competence();

-- 3. TRIGGER : Génération automatique d'alertes sur seuils
CREATE OR REPLACE FUNCTION trigger_alerte_competence_critique()
RETURNS TRIGGER AS $$
DECLARE
    v_niveau_moyen NUMERIC;
    v_competence_nom VARCHAR;
BEGIN
    -- Si validation d'une compétence, vérifier si elle crée une alerte
    IF NEW.valide = TRUE AND (OLD IS NULL OR OLD.valide = FALSE) THEN
        
        -- Calculer le niveau moyen actuel pour cette compétence
        SELECT AVG(ec.niveau) INTO v_niveau_moyen
        FROM employe_competences ec
        WHERE ec.id_competence = NEW.id_competence
          AND ec.valide = TRUE;
        
        -- Récupérer le nom de la compétence
        SELECT nom INTO v_competence_nom
        FROM competences
        WHERE id_competence = NEW.id_competence;
        
        -- Si soft skill et niveau moyen < 2.5, créer alerte
        IF EXISTS (
            SELECT 1 FROM competences c
            JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
            WHERE c.id_competence = NEW.id_competence
              AND tc.libelle ILIKE '%soft%'
        ) AND v_niveau_moyen < 2.5 THEN
            
            INSERT INTO competence_alertes (
                type_alerte,
                id_competence,
                id_employe,
                severite,
                message,
                metadata
            ) VALUES (
                'soft_skills_faible',
                NEW.id_competence,
                NEW.id_employe,
                'moyenne',
                'Niveau moyen de soft skill "' || v_competence_nom || '" en baisse',
                JSONB_BUILD_OBJECT('niveau_moyen', v_niveau_moyen)
            )
            ON CONFLICT DO NOTHING;
        END IF;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER after_competence_validation
    AFTER INSERT OR UPDATE ON employe_competences
    FOR EACH ROW
    WHEN (NEW.valide = TRUE)
    EXECUTE FUNCTION trigger_alerte_competence_critique();

-- 4. FONCTION DE JOB BATCH COMPLET (à exécuter via cron)
CREATE OR REPLACE FUNCTION job_traitement_competences_batch()
RETURNS JSONB AS $$
DECLARE
    v_start_time TIMESTAMP := CURRENT_TIMESTAMP;
    v_nb_alertes INT;
    v_nb_recommandations INT;
    v_nb_snapshots INT;
    v_nb_cache_nettoye INT;
    v_resultat JSONB;
BEGIN
    -- 1. Nettoyer le cache expiré
    v_nb_cache_nettoye := nettoyer_cache_expire();
    
    -- 2. Créer les snapshots pour l'historique
    v_nb_snapshots := creer_snapshot_competences();
    
    -- 3. Générer les alertes
    v_nb_alertes := generer_alertes_competences();
    
    -- 4. Générer les recommandations
    v_nb_recommandations := generer_recommandations();
    
    -- 5. Construire le résultat
    v_resultat := JSONB_BUILD_OBJECT(
        'succes', TRUE,
        'duree_secondes', EXTRACT(EPOCH FROM (CURRENT_TIMESTAMP - v_start_time)),
        'operations', JSONB_BUILD_OBJECT(
            'cache_nettoye', v_nb_cache_nettoye,
            'snapshots_crees', v_nb_snapshots,
            'alertes_generees', v_nb_alertes,
            'recommandations_generees', v_nb_recommandations
        ),
        'timestamp', CURRENT_TIMESTAMP
    );
    
    -- 6. Logger le résultat
    INSERT INTO competence_audit_log (
        operation_type,
        statut,
        nb_lignes_affectees,
        details
    ) VALUES (
        'job_batch_complet',
        'success',
        v_nb_alertes + v_nb_recommandations,
        v_resultat
    );
    
    RETURN v_resultat;
    
EXCEPTION WHEN OTHERS THEN
    -- En cas d'erreur, logger et retourner l'erreur
    INSERT INTO competence_audit_log (
        operation_type,
        statut,
        message_erreur,
        details
    ) VALUES (
        'job_batch_complet',
        'error',
        SQLERRM,
        JSONB_BUILD_OBJECT('timestamp', CURRENT_TIMESTAMP)
    );
    
    RETURN JSONB_BUILD_OBJECT(
        'succes', FALSE,
        'erreur', SQLERRM,
        'timestamp', CURRENT_TIMESTAMP
    );
END;
$$ LANGUAGE plpgsql;

-- 5. FONCTION DE JOB INCRÉMENTAL (webhook/import temps réel)
CREATE OR REPLACE FUNCTION job_traitement_incremental(
    p_id_employe INT,
    p_id_competence INT
)
RETURNS JSONB AS $$
DECLARE
    v_resultat JSONB;
    v_gaps JSONB;
BEGIN
    -- 1. Invalider le cache concerné
    DELETE FROM competence_cache
    WHERE cache_key LIKE '%employe_' || p_id_employe || '%'
       OR cache_key LIKE '%competence_' || p_id_competence || '%';
    
    -- 2. Recalculer les gaps pour cet employé
    SELECT JSONB_AGG(
        JSONB_BUILD_OBJECT(
            'id_competence', g.id_competence,
            'nom', g.nom_competence,
            'gap', g.gap,
            'critique', g.est_critique
        )
    ) INTO v_gaps
    FROM calculer_gaps_employe(p_id_employe) g;
    
    -- 3. Générer recommandations si gaps critiques
    IF EXISTS (
        SELECT 1 FROM calculer_gaps_employe(p_id_employe)
        WHERE est_critique = TRUE AND gap > 0
    ) THEN
        PERFORM generer_recommandations();
    END IF;
    
    v_resultat := JSONB_BUILD_OBJECT(
        'succes', TRUE,
        'id_employe', p_id_employe,
        'id_competence', p_id_competence,
        'gaps_detectes', v_gaps,
        'timestamp', CURRENT_TIMESTAMP
    );
    
    -- Logger
    INSERT INTO competence_audit_log (
        operation_type,
        id_employe_operateur,
        details
    ) VALUES (
        'traitement_incremental',
        p_id_employe,
        v_resultat
    );
    
    RETURN v_resultat;
    
EXCEPTION WHEN OTHERS THEN
    RETURN JSONB_BUILD_OBJECT(
        'succes', FALSE,
        'erreur', SQLERRM
    );
END;
$$ LANGUAGE plpgsql;

-- 6. FONCTION DE PURGE AUTOMATIQUE DES ANCIENNES DONNÉES
CREATE OR REPLACE FUNCTION purger_anciennes_donnees(
    p_jours_retention_snapshots INT DEFAULT 365,
    p_jours_retention_logs INT DEFAULT 90,
    p_jours_retention_alertes_resolues INT DEFAULT 30
)
RETURNS JSONB AS $$
DECLARE
    v_nb_snapshots_supprimes INT;
    v_nb_logs_supprimes INT;
    v_nb_alertes_supprimees INT;
BEGIN
    -- Supprimer les vieux snapshots
    DELETE FROM competence_snapshot
    WHERE date_snapshot < CURRENT_TIMESTAMP - (p_jours_retention_snapshots || ' days')::INTERVAL;
    GET DIAGNOSTICS v_nb_snapshots_supprimes = ROW_COUNT;
    
    -- Supprimer les vieux logs
    DELETE FROM competence_audit_log
    WHERE date_operation < CURRENT_TIMESTAMP - (p_jours_retention_logs || ' days')::INTERVAL;
    GET DIAGNOSTICS v_nb_logs_supprimes = ROW_COUNT;
    
    -- Supprimer les alertes résolues anciennes
    DELETE FROM competence_alertes
    WHERE est_resolue = TRUE
      AND date_resolution < CURRENT_TIMESTAMP - (p_jours_retention_alertes_resolues || ' days')::INTERVAL;
    GET DIAGNOSTICS v_nb_alertes_supprimees = ROW_COUNT;
    
    RETURN JSONB_BUILD_OBJECT(
        'snapshots_supprimes', v_nb_snapshots_supprimes,
        'logs_supprimes', v_nb_logs_supprimes,
        'alertes_supprimees', v_nb_alertes_supprimees,
        'timestamp', CURRENT_TIMESTAMP
    );
END;
$$ LANGUAGE plpgsql;

-- 7. CRÉATION D'INDEX POUR OPTIMISATION DES JOBS
CREATE INDEX IF NOT EXISTS idx_employe_competences_valide_niveau 
    ON employe_competences(id_competence, valide, niveau) 
    WHERE valide = TRUE;

CREATE INDEX IF NOT EXISTS idx_employe_competences_date_mesure 
    ON employe_competences(date_mesure DESC);

CREATE INDEX IF NOT EXISTS idx_alertes_non_resolues 
    ON competence_alertes(type_alerte, severite) 
    WHERE est_resolue = FALSE;

CREATE INDEX IF NOT EXISTS idx_recommandations_en_attente 
    ON competence_recommandations(type_action, priorite) 
    WHERE statut = 'en_attente';



-- ========================================
-- TRIGGERS ET AUTOMATISATIONS
-- ========================================

-- 1. TRIGGER : Auto-normalisation lors de l'insertion de compétences
CREATE OR REPLACE FUNCTION trigger_normaliser_competence()
RETURNS TRIGGER AS $$
BEGIN
    NEW.nom := normaliser_competence(NEW.nom);
    
    -- Vérifier si une compétence similaire existe déjà
    IF EXISTS (
        SELECT 1 FROM competences 
        WHERE id_competence != COALESCE(NEW.id_competence, 0)
          AND calculer_similarite(nom, NEW.nom) >= 0.9
    ) THEN
        -- Logger l'alerte de doublon potentiel
        INSERT INTO competence_audit_log (
            operation_type, 
            statut, 
            message_erreur,
            details
        ) VALUES (
            'insert_competence',
            'warning',
            'Compétence similaire détectée : ' || NEW.nom,
            JSONB_BUILD_OBJECT('nom_nouveau', NEW.nom)
        );
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER before_insert_competence
    BEFORE INSERT ON competences
    FOR EACH ROW
    EXECUTE FUNCTION trigger_normaliser_competence();

-- 2. TRIGGER : Invalidation du cache lors de modifications
CREATE OR REPLACE FUNCTION trigger_invalider_cache_competence()
RETURNS TRIGGER AS $$
BEGIN
    -- Supprimer les entrées de cache liées à cette compétence
    DELETE FROM competence_cache
    WHERE cache_key LIKE '%competence_' || COALESCE(NEW.id_competence, OLD.id_competence) || '%';
    
    -- Supprimer aussi les caches globaux
    DELETE FROM competence_cache
    WHERE cache_key IN ('stats_globales', 'dashboard_competences');
    
    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER after_employe_competence_change
    AFTER INSERT OR UPDATE OR DELETE ON employe_competences
    FOR EACH ROW
    EXECUTE FUNCTION trigger_invalider_cache_competence();

-- 3. TRIGGER : Génération automatique d'alertes sur seuils
CREATE OR REPLACE FUNCTION trigger_alerte_competence_critique()
RETURNS TRIGGER AS $$
DECLARE
    v_niveau_moyen NUMERIC;
    v_competence_nom VARCHAR;
BEGIN
    -- Si validation d'une compétence, vérifier si elle crée une alerte
    IF NEW.valide = TRUE AND (OLD IS NULL OR OLD.valide = FALSE) THEN
        
        -- Calculer le niveau moyen actuel pour cette compétence
        SELECT AVG(ec.niveau) INTO v_niveau_moyen
        FROM employe_competences ec
        WHERE ec.id_competence = NEW.id_competence
          AND ec.valide = TRUE;
        
        -- Récupérer le nom de la compétence
        SELECT nom INTO v_competence_nom
        FROM competences
        WHERE id_competence = NEW.id_competence;
        
        -- Si soft skill et niveau moyen < 2.5, créer alerte
        IF EXISTS (
            SELECT 1 FROM competences c
            JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
            WHERE c.id_competence = NEW.id_competence
              AND tc.libelle ILIKE '%soft%'
        ) AND v_niveau_moyen < 2.5 THEN
            
            INSERT INTO competence_alertes (
                type_alerte,
                id_competence,
                id_employe,
                severite,
                message,
                metadata
            ) VALUES (
                'soft_skills_faible',
                NEW.id_competence,
                NEW.id_employe,
                'moyenne',
                'Niveau moyen de soft skill "' || v_competence_nom || '" en baisse',
                JSONB_BUILD_OBJECT('niveau_moyen', v_niveau_moyen)
            )
            ON CONFLICT DO NOTHING;
        END IF;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER after_competence_validation
    AFTER INSERT OR UPDATE ON employe_competences
    FOR EACH ROW
    WHEN (NEW.valide = TRUE)
    EXECUTE FUNCTION trigger_alerte_competence_critique();

-- 4. FONCTION DE JOB BATCH COMPLET (à exécuter via cron)
CREATE OR REPLACE FUNCTION job_traitement_competences_batch()
RETURNS JSONB AS $$
DECLARE
    v_start_time TIMESTAMP := CURRENT_TIMESTAMP;
    v_nb_alertes INT;
    v_nb_recommandations INT;
    v_nb_snapshots INT;
    v_nb_cache_nettoye INT;
    v_resultat JSONB;
BEGIN
    -- 1. Nettoyer le cache expiré
    v_nb_cache_nettoye := nettoyer_cache_expire();
    
    -- 2. Créer les snapshots pour l'historique
    v_nb_snapshots := creer_snapshot_competences();
    
    -- 3. Générer les alertes
    v_nb_alertes := generer_alertes_competences();
    
    -- 4. Générer les recommandations
    v_nb_recommandations := generer_recommandations();
    
    -- 5. Construire le résultat
    v_resultat := JSONB_BUILD_OBJECT(
        'succes', TRUE,
        'duree_secondes', EXTRACT(EPOCH FROM (CURRENT_TIMESTAMP - v_start_time)),
        'operations', JSONB_BUILD_OBJECT(
            'cache_nettoye', v_nb_cache_nettoye,
            'snapshots_crees', v_nb_snapshots,
            'alertes_generees', v_nb_alertes,
            'recommandations_generees', v_nb_recommandations
        ),
        'timestamp', CURRENT_TIMESTAMP
    );
    
    -- 6. Logger le résultat
    INSERT INTO competence_audit_log (
        operation_type,
        statut,
        nb_lignes_affectees,
        details
    ) VALUES (
        'job_batch_complet',
        'success',
        v_nb_alertes + v_nb_recommandations,
        v_resultat
    );
    
    RETURN v_resultat;
    
EXCEPTION WHEN OTHERS THEN
    -- En cas d'erreur, logger et retourner l'erreur
    INSERT INTO competence_audit_log (
        operation_type,
        statut,
        message_erreur,
        details
    ) VALUES (
        'job_batch_complet',
        'error',
        SQLERRM,
        JSONB_BUILD_OBJECT('timestamp', CURRENT_TIMESTAMP)
    );
    
    RETURN JSONB_BUILD_OBJECT(
        'succes', FALSE,
        'erreur', SQLERRM,
        'timestamp', CURRENT_TIMESTAMP
    );
END;
$$ LANGUAGE plpgsql;

-- 5. FONCTION DE JOB INCRÉMENTAL (webhook/import temps réel)
CREATE OR REPLACE FUNCTION job_traitement_incremental(
    p_id_employe INT,
    p_id_competence INT
)
RETURNS JSONB AS $$
DECLARE
    v_resultat JSONB;
    v_gaps JSONB;
BEGIN
    -- 1. Invalider le cache concerné
    DELETE FROM competence_cache
    WHERE cache_key LIKE '%employe_' || p_id_employe || '%'
       OR cache_key LIKE '%competence_' || p_id_competence || '%';
    
    -- 2. Recalculer les gaps pour cet employé
    SELECT JSONB_AGG(
        JSONB_BUILD_OBJECT(
            'id_competence', g.id_competence,
            'nom', g.nom_competence,
            'gap', g.gap,
            'critique', g.est_critique
        )
    ) INTO v_gaps
    FROM calculer_gaps_employe(p_id_employe) g;
    
    -- 3. Générer recommandations si gaps critiques
    IF EXISTS (
        SELECT 1 FROM calculer_gaps_employe(p_id_employe)
        WHERE est_critique = TRUE AND gap > 0
    ) THEN
        PERFORM generer_recommandations();
    END IF;
    
    v_resultat := JSONB_BUILD_OBJECT(
        'succes', TRUE,
        'id_employe', p_id_employe,
        'id_competence', p_id_competence,
        'gaps_detectes', v_gaps,
        'timestamp', CURRENT_TIMESTAMP
    );
    
    -- Logger
    INSERT INTO competence_audit_log (
        operation_type,
        id_employe_operateur,
        details
    ) VALUES (
        'traitement_incremental',
        p_id_employe,
        v_resultat
    );
    
    RETURN v_resultat;
    
EXCEPTION WHEN OTHERS THEN
    RETURN JSONB_BUILD_OBJECT(
        'succes', FALSE,
        'erreur', SQLERRM
    );
END;
$$ LANGUAGE plpgsql;

-- 6. FONCTION DE PURGE AUTOMATIQUE DES ANCIENNES DONNÉES
CREATE OR REPLACE FUNCTION purger_anciennes_donnees(
    p_jours_retention_snapshots INT DEFAULT 365,
    p_jours_retention_logs INT DEFAULT 90,
    p_jours_retention_alertes_resolues INT DEFAULT 30
)
RETURNS JSONB AS $$
DECLARE
    v_nb_snapshots_supprimes INT;
    v_nb_logs_supprimes INT;
    v_nb_alertes_supprimees INT;
BEGIN
    -- Supprimer les vieux snapshots
    DELETE FROM competence_snapshot
    WHERE date_snapshot < CURRENT_TIMESTAMP - (p_jours_retention_snapshots || ' days')::INTERVAL;
    GET DIAGNOSTICS v_nb_snapshots_supprimes = ROW_COUNT;
    
    -- Supprimer les vieux logs
    DELETE FROM competence_audit_log
    WHERE date_operation < CURRENT_TIMESTAMP - (p_jours_retention_logs || ' days')::INTERVAL;
    GET DIAGNOSTICS v_nb_logs_supprimes = ROW_COUNT;
    
    -- Supprimer les alertes résolues anciennes
    DELETE FROM competence_alertes
    WHERE est_resolue = TRUE
      AND date_resolution < CURRENT_TIMESTAMP - (p_jours_retention_alertes_resolues || ' days')::INTERVAL;
    GET DIAGNOSTICS v_nb_alertes_supprimees = ROW_COUNT;
    
    RETURN JSONB_BUILD_OBJECT(
        'snapshots_supprimes', v_nb_snapshots_supprimes,
        'logs_supprimes', v_nb_logs_supprimes,
        'alertes_supprimees', v_nb_alertes_supprimees,
        'timestamp', CURRENT_TIMESTAMP
    );
END;
$$ LANGUAGE plpgsql;

-- 7. CRÉATION D'INDEX POUR OPTIMISATION DES JOBS
CREATE INDEX IF NOT EXISTS idx_employe_competences_valide_niveau 
    ON employe_competences(id_competence, valide, niveau) 
    WHERE valide = TRUE;

CREATE INDEX IF NOT EXISTS idx_employe_competences_date_mesure 
    ON employe_competences(date_mesure DESC);

CREATE INDEX IF NOT EXISTS idx_alertes_non_resolues 
    ON competence_alertes(type_alerte, severite) 
    WHERE est_resolue = FALSE;

CREATE INDEX IF NOT EXISTS idx_recommandations_en_attente 
    ON competence_recommandations(type_action, priorite) 
    WHERE statut = 'en_attente';

-- ========================================
-- VUES MATERIALISEES POUR PERFORMANCE (VERSION NETTOYEE)
-- ========================================

-- 1. VUE MATERIALISEE : Cartographie complete optimisee
CREATE MATERIALIZED VIEW IF NOT EXISTS mv_competence_cartographie_optimisee AS
WITH stats_employes AS (
    SELECT COUNT(DISTINCT id_employe) as total_employes
    FROM employes
    WHERE date_embauche IS NOT NULL
),
competences_employes AS (
    SELECT
        ec.id_competence,
        ec.id_employe,
        ec.niveau,
        ec.valide,
        ec.date_mesure,
        e.id_departement,
        e.poste,
        d.nom as departement_nom,
        s.libelle as source_libelle,
        COALESCE(sp.priorite, 0) as source_priorite,
        EXTRACT(YEAR FROM AGE(CURRENT_DATE, e.date_embauche)) as anciennete,
        ROW_NUMBER() OVER (
            PARTITION BY ec.id_employe, ec.id_competence
            ORDER BY COALESCE(sp.priorite, 0) DESC, ec.date_mesure DESC
        ) as rn
    FROM employe_competences ec
    JOIN employes e ON ec.id_employe = e.id_employe
    LEFT JOIN departements d ON e.id_departement = d.id_departement
    LEFT JOIN source_evaluation s ON ec.id_source = s.id_source
    LEFT JOIN source_priorite sp ON s.id_source = sp.id_source
    WHERE ec.valide = TRUE
),
competences_best AS (
    SELECT * FROM competences_employes WHERE rn = 1
),
aggregated AS (
    SELECT
        c.id_competence,
        c.nom,
        c.description,
        c.domaine,
        tc.libelle AS type_competence,
        
        -- Statistiques de base
        COUNT(DISTINCT ce.id_employe) AS nb_employes,
        ROUND(COUNT(DISTINCT ce.id_employe) * 100.0 / NULLIF((SELECT total_employes FROM stats_employes), 0), 2) AS taux_couverture,
        
        -- Niveau moyen pondere par anciennete
        ROUND(AVG(
            CASE
                WHEN ce.anciennete > 5 THEN ce.niveau * 1.3
                WHEN ce.anciennete BETWEEN 3 AND 5 THEN ce.niveau * 1.2
                WHEN ce.anciennete BETWEEN 1 AND 3 THEN ce.niveau * 1.1
                ELSE ce.niveau * 1.0
            END
        )::numeric, 2) AS niveau_moyen_pondere,
        
        -- Niveau moyen simple
        ROUND(AVG(ce.niveau)::numeric, 2) AS niveau_moyen_simple,
        
        -- Distribution par niveau
        COUNT(*) FILTER (WHERE ce.niveau = 1) AS nb_debutants,
        COUNT(*) FILTER (WHERE ce.niveau = 2) AS nb_intermediaires,
        COUNT(*) FILTER (WHERE ce.niveau = 3) AS nb_avances,
        COUNT(*) FILTER (WHERE ce.niveau = 4) AS nb_experts,
        COUNT(*) FILTER (WHERE ce.niveau = 5) AS nb_maitres,
        
        -- Pourcentages de distribution
        ROUND(COUNT(*) FILTER (WHERE ce.niveau = 1) * 100.0 / NULLIF(COUNT(*), 0), 2) AS pct_debutants,
        ROUND(COUNT(*) FILTER (WHERE ce.niveau = 2) * 100.0 / NULLIF(COUNT(*), 0), 2) AS pct_intermediaires,
        ROUND(COUNT(*) FILTER (WHERE ce.niveau = 3) * 100.0 / NULLIF(COUNT(*), 0), 2) AS pct_avances,
        ROUND(COUNT(*) FILTER (WHERE ce.niveau = 4) * 100.0 / NULLIF(COUNT(*), 0), 2) AS pct_experts,
        ROUND(COUNT(*) FILTER (WHERE ce.niveau = 5) * 100.0 / NULLIF(COUNT(*), 0), 2) AS pct_maitres,
        
        -- Distribution par departement
        COUNT(DISTINCT ce.id_departement) AS nb_departements,
        ARRAY_AGG(DISTINCT ce.departement_nom) FILTER (WHERE ce.departement_nom IS NOT NULL) AS departements_liste,
        
        -- Metadonnees
        MAX(ce.date_mesure) AS derniere_maj,
        ARRAY_AGG(DISTINCT ce.source_libelle) AS sources_utilisees,
        
        -- Score de maturite (0-100)
        ROUND(
            (COUNT(DISTINCT ce.id_employe) * 100.0 / NULLIF((SELECT total_employes FROM stats_employes), 0)) *
            (AVG(ce.niveau) / 5.0) * 100,
            2
        ) AS score_maturite
        
    FROM competences c
    LEFT JOIN competences_best ce ON c.id_competence = ce.id_competence
    LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
    GROUP BY c.id_competence, c.nom, c.description, c.domaine, tc.libelle
)
SELECT
    a.*,
    ncl.libelle as niveau_libelle,
    ncl.couleur as niveau_couleur,
    CASE
        WHEN a.niveau_moyen_pondere >= 4.0 AND a.taux_couverture >= 30 THEN 'Excellente'
        WHEN a.niveau_moyen_pondere >= 3.0 AND a.taux_couverture >= 20 THEN 'Bon'
        WHEN a.niveau_moyen_pondere >= 2.0 THEN 'Satisfaisant'
        ELSE 'A developper'
    END as evaluation_globale,
    CASE
        WHEN a.score_maturite >= 75 THEN 'Mature'
        WHEN a.score_maturite >= 50 THEN 'En developpement'
        WHEN a.score_maturite >= 25 THEN 'Emergent'
        ELSE 'Critique'
    END as statut_maturite
FROM aggregated a
LEFT JOIN niveau_competence_libelle ncl ON ROUND(a.niveau_moyen_simple) = ncl.niveau;

-- Index sur la vue materialisee
CREATE UNIQUE INDEX idx_mv_cartographie_id ON mv_competence_cartographie_optimisee(id_competence);
CREATE INDEX idx_mv_cartographie_domaine ON mv_competence_cartographie_optimisee(domaine);
CREATE INDEX idx_mv_cartographie_type ON mv_competence_cartographie_optimisee(type_competence);
CREATE INDEX idx_mv_cartographie_maturite ON mv_competence_cartographie_optimisee(score_maturite DESC);

-- 2. VUE MATERIALISEE : Dashboard global
CREATE MATERIALIZED VIEW IF NOT EXISTS mv_dashboard_competences AS
WITH stats_base AS (
    SELECT
        COUNT(DISTINCT id_competence) as total_competences,
        COUNT(DISTINCT domaine) as total_domaines,
        ROUND(AVG(nb_employes)::numeric, 2) as nb_employes_moyen,
        ROUND(AVG(niveau_moyen_simple)::numeric, 2) as niveau_global_moyen,
        ROUND(AVG(taux_couverture)::numeric, 2) as couverture_moyenne,
        SUM(nb_experts) as total_experts,
        ROUND(AVG(score_maturite)::numeric, 2) as score_maturite_global
    FROM mv_competence_cartographie_optimisee
),
repartition_evaluation AS (
    SELECT
        evaluation_globale,
        COUNT(*) as nb_competences,
        ROUND(COUNT(*) * 100.0 / NULLIF(SUM(COUNT(*)) OVER (), 0), 2) as pourcentage
    FROM mv_competence_cartographie_optimisee
    GROUP BY evaluation_globale
),
repartition_maturite AS (
    SELECT
        statut_maturite,
        COUNT(*) as nb_competences,
        ROUND(COUNT(*) * 100.0 / NULLIF(SUM(COUNT(*)) OVER (), 0), 2) as pourcentage
    FROM mv_competence_cartographie_optimisee
    GROUP BY statut_maturite
),
top_competences AS (
    SELECT
        id_competence,
        nom,
        domaine,
        nb_employes,
        niveau_moyen_simple,
        score_maturite,
        evaluation_globale
    FROM mv_competence_cartographie_optimisee
    ORDER BY score_maturite DESC
    LIMIT 10
),
competences_critiques AS (
    SELECT
        id_competence,
        nom,
        domaine,
        nb_employes,
        niveau_moyen_simple,
        taux_couverture,
        evaluation_globale
    FROM mv_competence_cartographie_optimisee
    WHERE evaluation_globale = 'A developper'
       OR taux_couverture < 20
    ORDER BY taux_couverture ASC, niveau_moyen_simple ASC
    LIMIT 10
),
distribution_par_domaine AS (
    SELECT
        domaine,
        COUNT(*) as nb_competences,
        ROUND(AVG(niveau_moyen_simple)::numeric, 2) as niveau_moyen,
        ROUND(AVG(taux_couverture)::numeric, 2) as couverture_moyenne,
        SUM(nb_employes) as total_employes_competents
    FROM mv_competence_cartographie_optimisee
    GROUP BY domaine
    ORDER BY nb_competences DESC
)
SELECT
    (SELECT ROW_TO_JSON(sb.*) FROM stats_base sb) as statistiques_globales,
    (SELECT JSON_AGG(re.*) FROM repartition_evaluation re) as repartition_evaluation,
    (SELECT JSON_AGG(rm.*) FROM repartition_maturite rm) as repartition_maturite,
    (SELECT JSON_AGG(tc.*) FROM top_competences tc) as top_10_competences,
    (SELECT JSON_AGG(cc.*) FROM competences_critiques cc) as competences_critiques,
    (SELECT JSON_AGG(dd.*) FROM distribution_par_domaine dd) as distribution_domaines,
    CURRENT_TIMESTAMP as date_generation;

-- Index sur la vue dashboard
CREATE INDEX idx_mv_dashboard_date ON mv_dashboard_competences(date_generation);

-- Configuration pour rafraichissement concurrent
ALTER MATERIALIZED VIEW mv_competence_cartographie_optimisee SET (autovacuum_enabled = true);
ALTER MATERIALIZED VIEW mv_dashboard_competences SET (autovacuum_enabled = true);