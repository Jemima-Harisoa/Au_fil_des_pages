-- ========================================
-- Tables pour la gestion des compétences
-- ========================================

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
    ev.nom as validateur_nom,
    ev.prenom as validateur_prenom,
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
SELECT 
    e.id_employe,
    p.nom,
    p.prenom,
    d.nom as departement,
    e.poste,
    COUNT(ec.id_competence) as nb_competences,
    COUNT(CASE WHEN ec.valide = true THEN 1 END) as nb_competences_validees,
    ROUND(AVG(ec.niveau)::numeric, 2) as niveau_moyen,
    ncl.libelle as libelle_niveau_moyen,
    COUNT(CASE WHEN ec.niveau >= 4 THEN 1 END) as nb_competences_expert,
    COUNT(CASE WHEN tc.libelle = 'Hard Skill' THEN 1 END) as nb_hard_skills,
    COUNT(CASE WHEN tc.libelle = 'Soft Skill' THEN 1 END) as nb_soft_skills,
    MAX(ec.date_mesure) as derniere_mise_a_jour
FROM employes e
JOIN personnes p ON e.id_personne = p.id_personne
LEFT JOIN departements d ON e.id_departement = d.id_departement
LEFT JOIN employe_competences ec ON e.id_employe = ec.id_employe
LEFT JOIN competences c ON ec.id_competence = c.id_competence
LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
LEFT JOIN niveau_competence_libelle ncl ON 
    CASE 
        WHEN ROUND(AVG(ec.niveau)::numeric, 0) BETWEEN 1 AND 5 THEN ROUND(AVG(ec.niveau)::numeric, 0)
        ELSE 1
    END = ncl.niveau
GROUP BY e.id_employe, p.nom, p.prenom, d.nom, e.poste, ncl.libelle;

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

-- Insertion des données de référence
INSERT INTO type_competence (libelle, description) VALUES
    ('Hard Skill', 'Compétences techniques spécifiques et mesurables'),
    ('Soft Skill', 'Compétences comportementales et relationnelles'),
    ('Langue', 'Compétences linguistiques'),
    ('Certification', 'Compétences certifiées par un organisme')
ON CONFLICT DO NOTHING;

INSERT INTO source_evaluation (libelle, description) VALUES
    ('auto-evaluation', 'Évaluation par le employé lui-même'),
    ('manager-evaluation', 'Évaluation par le manager direct'),
    ('rh-evaluation', 'Évaluation par les ressources humaines'),
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