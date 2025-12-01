<?php

namespace app\models;

class CompetenceModel
{
    /** @var \PDO */
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère la liste des compétences avec statistiques (pagination, tri, champs calculés)
     * @param array $params Paramètres de filtrage et pagination
     * @return array Tableau avec métadonnées et données
     */
    public function getCompetenceList($params = [])
    {
        // Paramètres par défaut
        $page = $params['page'] ?? 1;
        $perPage = $params['perPage'] ?? 10;
        $search = $params['search'] ?? '';
        $departement = $params['departement'] ?? '';
        $niveauMin = $params['niveau_min'] ?? 0;
        $domaine = $params['domaine'] ?? '';
        $sortField = $params['sortField'] ?? 'nb_employes';
        $sortOrder = $params['sortOrder'] ?? 'DESC';

        // Calcul de l'offset
        $offset = ($page - 1) * $perPage;

        // Construction de la requête de base
        $sql = "
            SELECT 
                c.id_competence,
                c.nom,
                c.description,
                c.domaine,
                tc.libelle as type_competence,
                vc.nb_employes,
                vc.niveau_moyen,
                vc.libelle_niveau_moyen,
                vc.nb_employes_valides,
                vc.nb_debutants,
                vc.nb_intermediaires,
                vc.nb_avances,
                vc.nb_experts,
                vc.nb_maitres,
                vc.pourcentage_debutants,
                vc.pourcentage_intermediaires,
                vc.pourcentage_avances,
                vc.pourcentage_experts,
                vc.pourcentage_maitres,
                vc.derniere_maj,
                -- Calcul du badge de niveau moyen
                CASE 
                    WHEN vc.niveau_moyen < 2 THEN 'faible'
                    WHEN vc.niveau_moyen BETWEEN 2 AND 3 THEN 'ok'
                    ELSE 'bon'
                END as badge_niveau
            FROM v_competence_cartographie vc
            JOIN competences c ON vc.id_competence = c.id_competence
            LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
            WHERE 1=1
        ";

        $countSql = "
            SELECT COUNT(*) as total
            FROM v_competence_cartographie vc
            JOIN competences c ON vc.id_competence = c.id_competence
            WHERE 1=1
        ";

        $conditions = [];
        $bindings = [];

        // Filtre de recherche
        if (!empty($search)) {
            $conditions[] = "(c.nom ILIKE :search OR c.domaine ILIKE :search OR c.description ILIKE :search)";
            $bindings['search'] = '%' . $search . '%';
        }

        // Filtre par domaine
        if (!empty($domaine)) {
            $conditions[] = "c.domaine = :domaine";
            $bindings['domaine'] = $domaine;
        }

        // Filtre par niveau minimum
        if ($niveauMin > 0) {
            $conditions[] = "vc.niveau_moyen >= :niveau_min";
            $bindings['niveau_min'] = $niveauMin;
        }

        // Application des conditions
        if (!empty($conditions)) {
            $whereClause = " AND " . implode(" AND ", $conditions);
            $sql .= $whereClause;
            $countSql .= $whereClause;
        }

        // Tri
        $allowedSortFields = ['nom', 'domaine', 'nb_employes', 'niveau_moyen', 'derniere_maj'];
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'nb_employes';
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        
        $sql .= " ORDER BY {$sortField} {$sortOrder}";
        $sql .= " LIMIT :limit OFFSET :offset";

        // Exécution de la requête de comptage
        $stmtCount = $this->db->prepare($countSql);
        foreach ($bindings as $key => $value) {
            $stmtCount->bindValue(':' . $key, $value);
        }
        $stmtCount->execute();
        $totalResult = $stmtCount->fetch(\PDO::FETCH_ASSOC);
        $total = $totalResult['total'] ?? 0;

        // Exécution de la requête principale
        $stmt = $this->db->prepare($sql);
        
        // Liaison des paramètres communs
        foreach ($bindings as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        // Liaison des paramètres de pagination
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        
        $stmt->execute();
        $competences = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Formater les données pour l'affichage
        foreach ($competences as &$competence) {
            $competence = $this->formatCompetenceData($competence);
        }

        return [
            'meta' => [
                'total' => (int)$total,
                'page' => (int)$page,
                'perPage' => (int)$perPage,
                'totalPages' => ceil($total / $perPage)
            ],
            'data' => $competences
        ];
    }

    /**
     * Recherche textuelle dans les compétences
     * @param string $keyword Mot-clé de recherche
     * @return array Résultats de recherche
     */
    public function searchCompetences($keyword)
    {
        $sql = "
            SELECT 
                c.id_competence,
                c.nom,
                c.description,
                c.domaine,
                tc.libelle as type_competence,
                vc.nb_employes,
                vc.niveau_moyen,
                vc.libelle_niveau_moyen
            FROM competences c
            LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
            LEFT JOIN v_competence_cartographie vc ON c.id_competence = vc.id_competence
            WHERE c.nom ILIKE :keyword 
               OR c.domaine ILIKE :keyword 
               OR c.description ILIKE :keyword
            ORDER BY 
                CASE 
                    WHEN c.nom ILIKE :keyword_exact THEN 1
                    WHEN c.domaine ILIKE :keyword_exact THEN 2
                    ELSE 3
                END,
                vc.nb_employes DESC
            LIMIT 50
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'keyword' => '%' . $keyword . '%',
            'keyword_exact' => $keyword . '%'
        ]);

        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($results as &$result) {
            $result = $this->formatCompetenceData($result);
        }

        return $results;
    }

    /**
     * Filtre les compétences selon plusieurs critères
     * @param array $filters Critères de filtrage
     * @return array Compétences filtrées
     */
    public function filterCompetences($filters)
    {
        $sql = "
            SELECT 
                c.id_competence,
                c.nom,
                c.description,
                c.domaine,
                tc.libelle as type_competence,
                vc.nb_employes,
                vc.niveau_moyen,
                vc.libelle_niveau_moyen,
                vc.nb_employes_valides
            FROM v_competence_cartographie vc
            JOIN competences c ON vc.id_competence = c.id_competence
            LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
            WHERE 1=1
        ";

        $conditions = [];
        $bindings = [];

        // Filtre par département (via les employés)
        if (!empty($filters['departement'])) {
            $sql = "
                SELECT DISTINCT
                    c.id_competence,
                    c.nom,
                    c.description,
                    c.domaine,
                    tc.libelle as type_competence,
                    COUNT(DISTINCT ec.id_employe) as nb_employes,
                    ROUND(AVG(ec.niveau)::numeric, 2) as niveau_moyen,
                    ncl.libelle as libelle_niveau_moyen,
                    COUNT(DISTINCT CASE WHEN ec.valide = true THEN ec.id_employe END) as nb_employes_valides
                FROM competences c
                LEFT JOIN employe_competences ec ON c.id_competence = ec.id_competence
                LEFT JOIN employes e ON ec.id_employe = e.id_employe
                LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
                LEFT JOIN niveau_competence_libelle ncl ON 
                    CASE 
                        WHEN ROUND(AVG(ec.niveau)::numeric, 0) BETWEEN 1 AND 5 THEN ROUND(AVG(ec.niveau)::numeric, 0)
                        ELSE 1
                    END = ncl.niveau
                WHERE e.id_departement = :departement
            ";

            $conditions[] = "e.id_departement = :departement";
            $bindings['departement'] = $filters['departement'];
        }

        // Filtre par niveau minimum
        if (!empty($filters['niveau_min']) && $filters['niveau_min'] > 0) {
            $conditions[] = "vc.niveau_moyen >= :niveau_min";
            $bindings['niveau_min'] = $filters['niveau_min'];
        }

        // Filtre par domaine
        if (!empty($filters['domaine'])) {
            $conditions[] = "c.domaine = :domaine";
            $bindings['domaine'] = $filters['domaine'];
        }

        // Application des conditions
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY c.id_competence, c.nom, c.description, c.domaine, tc.libelle, ncl.libelle";
        $sql .= " ORDER BY nb_employes DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);

        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($results as &$result) {
            $result = $this->formatCompetenceData($result);
        }

        return $results;
    }

    /**
     * Récupère les détails d'une compétence spécifique avec statistiques et top employés
     * @param int $idCompetence ID de la compétence
     * @return array Détails de la compétence
     */
    public function getCompetence($idCompetence)
    {
        // Informations de base de la compétence
        $sqlBase = "
            SELECT 
                c.*,
                tc.libelle as type_competence,
                vc.nb_employes,
                vc.niveau_moyen,
                vc.libelle_niveau_moyen,
                vc.nb_employes_valides,
                vc.nb_debutants,
                vc.nb_intermediaires,
                vc.nb_avances,
                vc.nb_experts,
                vc.nb_maitres,
                vc.pourcentage_debutants,
                vc.pourcentage_intermediaires,
                vc.pourcentage_avances,
                vc.pourcentage_experts,
                vc.pourcentage_maitres,
                vc.derniere_maj
            FROM competences c
            LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
            LEFT JOIN v_competence_cartographie vc ON c.id_competence = vc.id_competence
            WHERE c.id_competence = :id_competence
        ";

        $stmtBase = $this->db->prepare($sqlBase);
        $stmtBase->execute(['id_competence' => $idCompetence]);
        $competence = $stmtBase->fetch(\PDO::FETCH_ASSOC);

        if (!$competence) {
            return null;
        }

        // Top employés pour cette compétence (niveau >= 4)
        $sqlTopEmployes = "
            SELECT 
                e.id_employe,
                p.nom,
                p.prenom,
                d.nom as departement,
                e.poste,
                ec.niveau,
                ncl.libelle as libelle_niveau,
                s.libelle as source_evaluation,
                ec.valide,
                ec.date_mesure
            FROM employe_competences ec
            JOIN employes e ON ec.id_employe = e.id_employe
            JOIN personnes p ON e.id_personne = p.id_personne
            LEFT JOIN departements d ON e.id_departement = d.id_departement
            LEFT JOIN niveau_competence_libelle ncl ON ec.niveau = ncl.niveau
            LEFT JOIN source_evaluation s ON ec.id_source = s.id_source
            WHERE ec.id_competence = :id_competence
            AND ec.niveau >= 4
            ORDER BY ec.niveau DESC, ec.date_mesure DESC
            LIMIT 10
        ";

        $stmtTop = $this->db->prepare($sqlTopEmployes);
        $stmtTop->execute(['id_competence' => $idCompetence]);
        $topEmployes = $stmtTop->fetchAll(\PDO::FETCH_ASSOC);

        // Distribution par département
        $sqlDepartements = "
            SELECT 
                d.nom as departement,
                COUNT(ec.id_employe) as nb_employes,
                ROUND(AVG(ec.niveau)::numeric, 2) as niveau_moyen
            FROM employe_competences ec
            JOIN employes e ON ec.id_employe = e.id_employe
            JOIN departements d ON e.id_departement = d.id_departement
            WHERE ec.id_competence = :id_competence
            GROUP BY d.id_departement, d.nom
            ORDER BY nb_employes DESC
        ";

        $stmtDept = $this->db->prepare($sqlDepartements);
        $stmtDept->execute(['id_competence' => $idCompetence]);
        $distributionDepartements = $stmtDept->fetchAll(\PDO::FETCH_ASSOC);

        // Historique des évaluations récentes
        $sqlHistorique = "
            SELECT 
                p.nom,
                p.prenom,
                ec.niveau,
                ncl.libelle as libelle_niveau,
                s.libelle as source,
                ec.date_mesure,
                ec.valide
            FROM employe_competences ec
            JOIN employes e ON ec.id_employe = e.id_employe
            JOIN personnes p ON e.id_personne = p.id_personne
            LEFT JOIN niveau_competence_libelle ncl ON ec.niveau = ncl.niveau
            LEFT JOIN source_evaluation s ON ec.id_source = s.id_source
            WHERE ec.id_competence = :id_competence
            ORDER BY ec.date_mesure DESC
            LIMIT 20
        ";

        $stmtHist = $this->db->prepare($sqlHistorique);
        $stmtHist->execute(['id_competence' => $idCompetence]);
        $historiqueEvaluations = $stmtHist->fetchAll(\PDO::FETCH_ASSOC);

        // Formater les données
        $competence = $this->formatCompetenceData($competence);
        
        return [
            'competence' => $competence,
            'top_employes' => $topEmployes,
            'distribution_departements' => $distributionDepartements,
            'historique_evaluations' => $historiqueEvaluations
        ];
    }

    /**
     * Exporte les compétences au format CSV ou PDF
     * @param string $format Format d'export (csv ou pdf)
     * @return array Données pour l'export
     */
    public function exportCompetences($format = 'csv')
    {
        $sql = "
            SELECT 
                c.id_competence,
                c.nom,
                c.description,
                c.domaine,
                tc.libelle as type_competence,
                vc.nb_employes,
                vc.niveau_moyen,
                vc.libelle_niveau_moyen,
                vc.nb_employes_valides,
                vc.nb_debutants,
                vc.nb_intermediaires,
                vc.nb_avances,
                vc.nb_experts,
                vc.nb_maitres,
                vc.derniere_maj
            FROM v_competence_cartographie vc
            JOIN competences c ON vc.id_competence = c.id_competence
            LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
            ORDER BY vc.nb_employes DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $competences = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($competences as &$competence) {
            $competence = $this->formatCompetenceData($competence);
        }

        return [
            'format' => $format,
            'date_export' => date('Y-m-d H:i:s'),
            'total_competences' => count($competences),
            'data' => $competences
        ];
    }

    /**
     * Génère le HTML pour le tableau de la liste des compétences
     * @param array $params Paramètres de filtrage
     * @return string HTML formaté du tableau
     */
    public function getTableauListeCompetences($params = [])
    {
        $result = $this->getCompetenceList($params);
        $competences = $result['data'];
        $meta = $result['meta'];

        $titre = "Cartographie des Compétences";

        // Construction du HTML complet
        $html = '
            <!-- Content Row - Liste Compétences -->
            <div class="row" id="listeCompetences">

                <!-- Liste des Compétences -->
                <div class="col-xl-12 col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">' . $titre . '</h6>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary" onclick="exporterCompetences(\'csv\')">
                                    <i class="fas fa-file-csv"></i> CSV
                                </button>
                                <button class="btn btn-sm btn-success" onclick="exporterCompetences(\'pdf\')">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" id="resetFilters">
                                    <i class="fas fa-sync-alt"></i> Réinitialiser
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Statistiques rapides -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Total Compétences</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">' . $meta['total'] . '</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-left-success shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                        Employés compétents</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">' . array_sum(array_column($competences, 'nb_employes')) . '</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                        Niveau moyen</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">' . (count($competences) > 0 ? number_format(array_sum(array_column($competences, 'niveau_moyen')) / count($competences), 2) : '0') . '</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-left-info shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                        Validations</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">' . array_sum(array_column($competences, 'nb_employes_valides')) . '</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtres par colonne -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="searchFilter" class="small font-weight-bold">Recherche</label>
                                    <input type="text" class="form-control form-control-sm" id="searchFilter" placeholder="Nom, domaine, description..." value="' . htmlspecialchars($params['search'] ?? '') . '">
                                </div>
                                <div class="col-md-2">
                                    <label for="domaineFilter" class="small font-weight-bold">Domaine</label>
                                    <select class="form-control form-control-sm" id="domaineFilter">
                                        <option value="">Tous</option>';
        
        // Récupérer les domaines distincts
        $domaines = $this->getDomaines();
        foreach ($domaines as $domaine) {
            $selected = ($params['domaine'] ?? '') === $domaine ? 'selected' : '';
            $html .= '<option value="' . htmlspecialchars($domaine) . '" ' . $selected . '>' . htmlspecialchars($domaine) . '</option>';
        }
        
        $html .= '
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="niveauMinFilter" class="small font-weight-bold">Niveau min</label>
                                    <select class="form-control form-control-sm" id="niveauMinFilter">
                                        <option value="0"' . (($params['niveau_min'] ?? 0) == 0 ? ' selected' : '') . '>Tous</option>
                                        <option value="2"' . (($params['niveau_min'] ?? 0) == 2 ? ' selected' : '') . '>>= 2 (Intermédiaire)</option>
                                        <option value="3"' . (($params['niveau_min'] ?? 0) == 3 ? ' selected' : '') . '>>= 3 (Avancé)</option>
                                        <option value="4"' . (($params['niveau_min'] ?? 0) == 4 ? ' selected' : '') . '>>= 4 (Expert)</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="sortField" class="small font-weight-bold">Tri par</label>
                                    <select class="form-control form-control-sm" id="sortField">
                                        <option value="nb_employes"' . (($params['sortField'] ?? 'nb_employes') === 'nb_employes' ? ' selected' : '') . '>Nb employés</option>
                                        <option value="niveau_moyen"' . (($params['sortField'] ?? '') === 'niveau_moyen' ? ' selected' : '') . '>Niveau moyen</option>
                                        <option value="nom"' . (($params['sortField'] ?? '') === 'nom' ? ' selected' : '') . '>Nom</option>
                                        <option value="domaine"' . (($params['sortField'] ?? '') === 'domaine' ? ' selected' : '') . '>Domaine</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="sortOrder" class="small font-weight-bold">Ordre</label>
                                    <select class="form-control form-control-sm" id="sortOrder">
                                        <option value="DESC"' . (($params['sortOrder'] ?? 'DESC') === 'DESC' ? ' selected' : '') . '>Décroissant</option>
                                        <option value="ASC"' . (($params['sortOrder'] ?? '') === 'ASC' ? ' selected' : '') . '>Croissant</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button class="btn btn-sm btn-primary w-100" onclick="appliquerFiltresCompetences()">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered" id="listeCompetencesTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Domaine</th>
                                            <th>Nb employés</th>
                                            <th>Niveau moyen</th>
                                            <th>Distribution</th>
                                            <th>Dernière MAJ</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Domaine</th>
                                            <th>Nb employés</th>
                                            <th>Niveau moyen</th>
                                            <th>Distribution</th>
                                            <th>Dernière MAJ</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>';

        if (empty($competences)) {
            $html .= '
                                        <tr>
                                            <td colspan="7" class="text-center">Aucune compétence trouvée</td>
                                        </tr>';
        } else {
            foreach ($competences as $competence) {
                $competence = $this->formatCompetenceData($competence);
                // Badge de niveau
                $badgeClass = 'badge-' . $competence['badge_niveau']['couleur'];
                $badgeIcon = $competence['badge_niveau']['icone'];
                $badgeLibelle = $competence['badge_niveau']['libelle'];
                
                // Barre de distribution
                $distributionHtml = $this->genererBarreDistribution($competence);

                $html .= '
                                        <tr>
                                            <td>
                                                <strong>' . htmlspecialchars($competence['nom']) . '</strong>';
                
                if (!empty($competence['type_competence'])) {
                    $html .= '<br><small class="text-muted">' . htmlspecialchars($competence['type_competence']) . '</small>';
                }
                
                $html .= '
                                            </td>
                                            <td>' . htmlspecialchars($competence['domaine']) . '</td>
                                            <td class="text-center">
                                                <span class="font-weight-bold">' . $competence['nb_employes'] . '</span>';
                
                if ($competence['nb_employes_valides'] > 0) {
                    $html .= '<br><small class="text-success">' . $competence['nb_employes_valides'] . ' validés</small>';
                }
                
                $html .= '
                                            </td>
                                            <td class="text-center">
                                                <span class="badge ' . $badgeClass . '">
                                                    <i class="fas ' . $badgeIcon . '"></i> ' . $badgeLibelle . '
                                                </span>
                                                <div class="small text-muted">' . ($competence['niveau_moyen_formatted'] ?? '') . ' / 5</div>
                                            </td>
                                            <td class="small">' . $distributionHtml . '</td>
                                            <td class="text-center">' . $competence['derniere_maj_formatted'] . '</td>
                                            <td class="text-center">
                                                <button onclick="afficherDetailsCompetence(' . $competence['id_competence'] . ')" 
                                                        class="btn btn-sm btn-primary" 
                                                        title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button onclick="exporterCompetence(' . $competence['id_competence'] . ')" 
                                                        class="btn btn-sm btn-success" 
                                                        title="Exporter cette compétence">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </td>
                                        </tr>';
            }
        }

        $html .= '
                                    </tbody>
                                </table>
                            </div>';

        // Pagination
        if ($meta['totalPages'] > 1) {
            $html .= '
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="text-muted small">
                                        Affichage de ' . (($meta['page'] - 1) * $meta['perPage'] + 1) . ' à ' . min($meta['page'] * $meta['perPage'], $meta['total']) . ' sur ' . $meta['total'] . ' compétences
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination pagination-sm justify-content-end">';
            
            // Bouton précédent
            $prevDisabled = $meta['page'] <= 1 ? ' disabled' : '';
            $html .= '<li class="page-item' . $prevDisabled . '">
                         <a class="page-link" href="javascript:void(0)" onclick="changerPage(' . ($meta['page'] - 1) . ')" aria-label="Précédent">
                             <span aria-hidden="true">&laquo;</span>
                         </a>
                     </li>';
            
            // Pages
            for ($i = 1; $i <= $meta['totalPages']; $i++) {
                $active = $i == $meta['page'] ? ' active' : '';
                $html .= '<li class="page-item' . $active . '">
                             <a class="page-link" href="javascript:void(0)" onclick="changerPage(' . $i . ')">' . $i . '</a>
                         </li>';
            }
            
            // Bouton suivant
            $nextDisabled = $meta['page'] >= $meta['totalPages'] ? ' disabled' : '';
            $html .= '<li class="page-item' . $nextDisabled . '">
                         <a class="page-link" href="javascript:void(0)" onclick="changerPage(' . ($meta['page'] + 1) . ')" aria-label="Suivant">
                             <span aria-hidden="true">&raquo;</span>
                         </a>
                     </li>';
            
            $html .= '
                                        </ul>
                                    </nav>
                                </div>
                            </div>';
        }

        $html .= '
                        </div>
                    </div>
                </div>
            </div>';

        return $html;
    }

    /**
     * Génère le HTML pour les détails d'une compétence
     * @param int $idCompetence ID de la compétence
     * @return string HTML formaté des détails
     */
    public function getTableauDetailCompetence($idCompetence)
    {
        $details = $this->getCompetence($idCompetence);
        
        if (!$details) {
            return '<div class="alert alert-warning">Compétence non trouvée</div>';
        }

        $competence = $details['competence'];
        $topEmployes = $details['top_employes'];
        $distributionDepartements = $details['distribution_departements'];
        $historiqueEvaluations = $details['historique_evaluations'];

        // Utilisez le même style que competences_admin.php
        $html = '
        <!-- Modal Details Competence -->
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header card-header-custom">
                        <h5 class="modal-title text-white" id="modalDetailsCompetenceLabel">
                            <i class="fas fa-chart-bar mr-2"></i>Détails de la compétence : ' . htmlspecialchars($competence['nom']) . '
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- En-tête avec statistiques principales -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card competence-details-card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h5 class="text-primary">' . htmlspecialchars($competence['domaine']) . '</h5>
                                                <p class="text-muted">' . nl2br(htmlspecialchars($competence['description'] ?? 'Aucune description disponible')) . '</p>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row text-center">
                                                    <div class="col-4">
                                                        <div class="h3 font-weight-bold text-primary">' . $competence['nb_employes'] . '</div>
                                                        <small class="text-muted">Employés</small>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="h3 font-weight-bold text-success">' . ($competence['niveau_moyen_formatted'] ?? '') . '</div>
                                                        <small class="text-muted">Niveau moyen</small>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="h3 font-weight-bold text-info">' . $competence['nb_employes_valides'] . '</div>
                                                        <small class="text-muted">Validations</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Informations générales -->
                            <div class="col-md-6">
                                <div class="card competence-details-card">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="m-0 font-weight-bold">Informations générales</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="font-weight-bold text-primary" width="40%">Nom :</td>
                                                <td>' . htmlspecialchars($competence['nom']) . '</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold text-primary">Domaine :</td>
                                                <td>' . htmlspecialchars($competence['domaine']) . '</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold text-primary">Type :</td>
                                                <td>' . htmlspecialchars($competence['type_competence'] ?? 'Non spécifié') . '</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold text-primary">Description :</td>
                                                <td>' . nl2br(htmlspecialchars($competence['description'] ?? 'Aucune description')) . '</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistiques et distribution -->
                            <div class="col-md-6">
                                <div class="card competence-details-card">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="m-0 font-weight-bold">Statistiques et Distribution</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center mb-3">
                                            <div class="col-4">
                                                <div class="h4 font-weight-bold text-primary">' . $competence['nb_employes'] . '</div>
                                                <small class="text-muted">Employés</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="h4 font-weight-bold text-success">' . ($competence['niveau_moyen_formatted'] ?? '') . '</div>
                                                <small class="text-muted">Niveau moyen</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="h4 font-weight-bold text-info">' . $competence['nb_employes_valides'] . '</div>
                                                <small class="text-muted">Validations</small>
                                            </div>
                                        </div>
                                        <div class="mt-3">' . $this->genererBarreDistribution($competence) . '</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top employés -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card competence-details-card">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="m-0 font-weight-bold">Top employés (Experts)</h6>
                                    </div>
                                    <div class="card-body">
                                        ' . (empty($topEmployes) ? 
                                            '<div class="text-center text-muted py-4">
                                                <i class="fas fa-users fa-3x mb-3"></i>
                                                <p>Aucun expert pour cette compétence</p>
                                            </div>' :
                                            $this->genererTableauTopEmployes($topEmployes)) . '
                                    </div>
                                </div>
                            </div>

                            <!-- Distribution par département -->
                            <div class="col-md-6">
                                <div class="card competence-details-card">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="m-0 font-weight-bold">Distribution par département</h6>
                                    </div>
                                    <div class="card-body">
                                        ' . (empty($distributionDepartements) ? 
                                            '<div class="text-center text-muted py-4">
                                                <i class="fas fa-building fa-3x mb-3"></i>
                                                <p>Aucune donnée par département</p>
                                            </div>' :
                                            $this->genererTableauDistributionDepartements($distributionDepartements)) . '
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Historique des évaluations -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card competence-details-card">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="m-0 font-weight-bold">Évaluations récentes</h6>
                                    </div>
                                    <div class="card-body">
                                        ' . (empty($historiqueEvaluations) ? 
                                            '<div class="text-center text-muted py-4">
                                                <i class="fas fa-history fa-3x mb-3"></i>
                                                <p>Aucune évaluation récente</p>
                                            </div>' :
                                            $this->genererTableauHistoriqueEvaluations($historiqueEvaluations)) . '
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" onclick="exporterCompetence(' . $competence['id_competence'] . ')">
                            <i class="fas fa-download mr-2"></i>Exporter
                        </button>
                    </div>
                </div>
            </div>';

        return $html;
    }

    /**
     * Génère une barre de distribution visuelle pour les niveaux
     * @param array $competence Données de la compétence
     * @return string HTML de la barre de distribution
     */
    private function genererBarreDistribution($competence)
    {
        $total = $competence['nb_employes'];
        if ($total == 0) return '<div class="text-muted small">Aucune donnée</div>';

        $html = '<div class="distribution-bar">';
        
        $niveaux = [
            'Débutant' => $competence['nb_debutants'] ?? 0,
            'Intermédiaire' => $competence['nb_intermediaires'] ?? 0,
            'Avancé' => $competence['nb_avances'] ?? 0,
            'Expert' => $competence['nb_experts'] ?? 0,
            'Maître' => $competence['nb_maitres'] ?? 0
        ];

        $couleurs = ['#e74a3b', '#f6c23e', '#36b9cc', '#1cc88a', '#4e73df'];

        $i = 0;
        foreach ($niveaux as $libelle => $quantite) {
            if ($quantite > 0) {
                $pourcentage = ($quantite / $total) * 100;
                $html .= '<div class="distribution-segment" 
                             style="width: ' . $pourcentage . '%; background-color: ' . $couleurs[$i] . ';"
                             title="' . $libelle . ': ' . $quantite . ' (' . number_format($pourcentage, 1) . '%)">
                         </div>';
            }
            $i++;
        }

        $html .= '</div>';

        // Légende
        $html .= '<div class="distribution-legend mt-1 small">';
        $i = 0;
        foreach ($niveaux as $libelle => $quantite) {
            if ($quantite > 0) {
                $pourcentage = ($quantite / $total) * 100;
                $html .= '<span class="mr-2"><i class="fas fa-square" style="color: ' . $couleurs[$i] . '"></i> ' . 
                         $libelle . ' (' . number_format($pourcentage, 1) . '%)</span>';
            }
            $i++;
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Génère le tableau des top employés
     * @param array $employes Liste des employés
     * @return string HTML du tableau
     */
    private function genererTableauTopEmployes($employes)
    {
        $html = '<div class="table-responsive"><table class="table table-sm table-striped">';
        $html .= '<thead><tr><th>Employé</th><th>Département</th><th>Niveau</th><th>Source</th><th>Validé</th></tr></thead><tbody>';

        foreach ($employes as $employe) {
            $badgeCouleur = $this->getCouleurNiveau($employe['niveau']);
            $html .= '<tr>
                <td>' . htmlspecialchars($employe['prenom'] . ' ' . $employe['nom']) . '<br><small class="text-muted">' . htmlspecialchars($employe['poste']) . '</small></td>
                <td>' . htmlspecialchars($employe['departement'] ?? 'Non affecté') . '</td>
                <td><span class="badge badge-' . $badgeCouleur . '">' . $employe['libelle_niveau'] . ' (' . $employe['niveau'] . ')</span></td>
                <td>' . htmlspecialchars($employe['source_evaluation'] ?? 'Non spécifié') . '</td>
                <td>' . ($employe['valide'] ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>') . '</td>
            </tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    /**
     * Génère le tableau de distribution par département
     * @param array $departements Liste des départements
     * @return string HTML du tableau
     */
    private function genererTableauDistributionDepartements($departements)
    {
        $html = '<div class="table-responsive"><table class="table table-sm table-striped">';
        $html .= '<thead><tr><th>Département</th><th>Nb employés</th><th>Niveau moyen</th></tr></thead><tbody>';

        foreach ($departements as $dept) {
            $html .= '<tr>
                <td>' . htmlspecialchars($dept['departement']) . '</td>
                <td class="text-center">' . $dept['nb_employes'] . '</td>
                <td class="text-center">' . number_format($dept['niveau_moyen'], 2) . '</td>
            </tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    /**
     * Génère le tableau de l'historique des évaluations
     * @param array $evaluations Liste des évaluations
     * @return string HTML du tableau
     */
    private function genererTableauHistoriqueEvaluations($evaluations)
    {
        $html = '<div class="table-responsive"><table class="table table-sm table-striped">';
        $html .= '<thead><tr><th>Employé</th><th>Niveau</th><th>Source</th><th>Date</th><th>Validé</th></tr></thead><tbody>';

        foreach ($evaluations as $eval) {
            $badgeCouleur = $this->getCouleurNiveau($eval['niveau']);
            $dateFormatted = date('d/m/Y', strtotime($eval['date_mesure']));
            
            $html .= '<tr>
                <td>' . htmlspecialchars($eval['prenom'] . ' ' . $eval['nom']) . '</td>
                <td><span class="badge badge-' . $badgeCouleur . '">' . $eval['libelle_niveau'] . ' (' . $eval['niveau'] . ')</span></td>
                <td>' . htmlspecialchars($eval['source'] ?? 'Non spécifié') . '</td>
                <td>' . $dateFormatted . '</td>
                <td>' . ($eval['valide'] ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>') . '</td>
            </tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    /**
     * Retourne la couleur Bootstrap correspondant au niveau
     * @param int $niveau Niveau de compétence
     * @return string Classe de couleur Bootstrap
     */
    private function getCouleurNiveau($niveau)
    {
        switch ($niveau) {
            case 1: return 'danger';
            case 2: return 'warning';
            case 3: return 'info';
            case 4: return 'success';
            case 5: return 'primary';
            default: return 'secondary';
        }
    }

    /**
     * Formate les données d'une compétence pour l'affichage
     * @param array $competence Données brutes de la compétence
     * @return array Données formatées
     */
    private function formatCompetenceData($competence)
    {
        // Badge de niveau moyen
        $niveauMoyen = $competence['niveau_moyen'] ?? 0;
        if ($niveauMoyen < 2) {
            $badgeNiveau = [
                'libelle' => 'Faible',
                'couleur' => 'danger',
                'icone' => 'fa-arrow-down'
            ];
        } elseif ($niveauMoyen >= 2 && $niveauMoyen <= 3) {
            $badgeNiveau = [
                'libelle' => 'Correct',
                'couleur' => 'warning',
                'icone' => 'fa-minus'
            ];
        } else {
            $badgeNiveau = [
                'libelle' => 'Bon',
                'couleur' => 'success',
                'icone' => 'fa-arrow-up'
            ];
        }

        $competence['badge_niveau'] = $badgeNiveau;
        
        // Formatage des pourcentages
        if (isset($competence['pourcentage_debutants'])) {
            $competence['pourcentage_debutants_formatted'] = number_format($competence['pourcentage_debutants'], 1) . '%';
        }
        if (isset($competence['pourcentage_intermediaires'])) {
            $competence['pourcentage_intermediaires_formatted'] = number_format($competence['pourcentage_intermediaires'], 1) . '%';
        }
        if (isset($competence['pourcentage_avances'])) {
            $competence['pourcentage_avances_formatted'] = number_format($competence['pourcentage_avances'], 1) . '%';
        }
        if (isset($competence['pourcentage_experts'])) {
            $competence['pourcentage_experts_formatted'] = number_format($competence['pourcentage_experts'], 1) . '%';
        }
        if (isset($competence['pourcentage_maitres'])) {
            $competence['pourcentage_maitres_formatted'] = number_format($competence['pourcentage_maitres'], 1) . '%';
        }

        // Formatage de la date
        if (!empty($competence['derniere_maj'])) {
            $competence['derniere_maj_formatted'] = date('d/m/Y', strtotime($competence['derniere_maj']));
        } else {
            $competence['derniere_maj_formatted'] = 'Jamais';
        }

        // Formatage du niveau moyen
        if (isset($competence['niveau_moyen'])) {
            $competence['niveau_moyen_formatted'] = number_format($competence['niveau_moyen'], 1);
        }

        return $competence;
    }

    /**
     * Récupère la liste des domaines distincts pour les filtres
     * @return array Liste des domaines
     */
    public function getDomaines()
    {
        $sql = "SELECT DISTINCT domaine FROM competences WHERE domaine IS NOT NULL ORDER BY domaine";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Récupère les statistiques globales des compétences
     * @return array Statistiques globales
     */
    public function getStatsGlobales()
    {
        $sql = "
            SELECT 
                COUNT(*) as total_competences,
                COUNT(DISTINCT domaine) as total_domaines,
                SUM(nb_employes) as total_employes_competents,
                ROUND(AVG(niveau_moyen)::numeric, 2) as niveau_moyen_global,
                COUNT(CASE WHEN niveau_moyen >= 4 THEN 1 END) as competences_expert
            FROM v_competence_cartographie
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Génère le HTML pour la section des statistiques des compétences
     * @return string HTML formaté de la section statistiques
     */
    public function getSectionStatsCompetences()
    {
        $stats = $this->getStatsGlobales();
        
        $typesStats = [
            [
                'titre' => 'Compétences',
                'valeur' => $stats['total_competences'] ?? 0,
                'couleur' => 'primary',
                'icone' => 'fa-clipboard-list',
                'description' => 'Compétences référencées'
            ],
            [
                'titre' => 'Domaines',
                'valeur' => $stats['total_domaines'] ?? 0,
                'couleur' => 'info',
                'icone' => 'fa-tags',
                'description' => 'Domaines différents'
            ],
            [
                'titre' => 'Employés compétents',
                'valeur' => $stats['total_employes_competents'] ?? 0,
                'couleur' => 'success',
                'icone' => 'fa-users',
                'description' => 'Compétences maîtrisées'
            ],
            [
                'titre' => 'Niveau moyen',
                'valeur' => number_format($stats['niveau_moyen_global'] ?? 0, 2),
                'couleur' => 'warning',
                'icone' => 'fa-chart-line',
                'description' => 'Niveau global'
            ],
        ];
        
        $html = '
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques Globales des Compétences</h6>
                </div>
                <div class="card-body">
                    <div class="row">';
        
        foreach ($typesStats as $type) {
            $html .= '
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-' . $type['couleur'] . ' shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-' . $type['couleur'] . ' text-uppercase mb-1">
                                    ' . $type['titre'] . '</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">' . $type['valeur'] . '</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    ' . $type['description'] . '
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas ' . $type['icone'] . ' fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }
        
        $html .= '
                    </div>
                </div>
            </div>
        </div>';
        
        return $html;
    }

    /**
     * Récupère les compétences d'un employé avec niveau et historique
     * @param int $idEmploye ID de l'employé
     * @return array Compétences de l'employé
     */
    public function getCompetencesByEmployee($idEmploye)
    {
        $sql = "
            SELECT 
                ec.id,
                c.id_competence,
                c.nom,
                c.description,
                c.domaine,
                tc.libelle as type_competence,
                ec.niveau,
                ncl.libelle as libelle_niveau,
                ncl.couleur as couleur_niveau,
                s.libelle as source_evaluation,
                ec.date_mesure,
                ec.valide,
                evp.nom as validateur_nom,
                evp.prenom as validateur_prenom,
                ec.date_validation,
                EXTRACT(YEAR FROM age(CURRENT_DATE, ec.date_mesure)) as anciennete_annees
            FROM employe_competences ec
            JOIN competences c ON ec.id_competence = c.id_competence
            LEFT JOIN type_competence tc ON c.id_type_competence = tc.id_type_competence
            LEFT JOIN niveau_competence_libelle ncl ON ec.niveau = ncl.niveau
            LEFT JOIN source_evaluation s ON ec.id_source = s.id_source
            LEFT JOIN employes ev ON ec.id_employe_validateur = ev.id_employe
            LEFT JOIN personnes evp ON ev.id_personne = evp.id_personne
            WHERE ec.id_employe = :id_employe
            ORDER BY ec.niveau DESC, ec.date_mesure DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        $competences = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($competences as &$competence) {
            $competence['badge_niveau'] = $this->getBadgeNiveau($competence['niveau']);
            $competence['date_mesure_formatted'] = !empty($competence['date_mesure']) ? date('d/m/Y', strtotime($competence['date_mesure'])) : null;
            $competence['date_validation_formatted'] = !empty($competence['date_validation']) ? date('d/m/Y', strtotime($competence['date_validation'])) : null;
        }

        return $competences;
    }

    /**
     * Récupère l'historique d'une compétence spécifique pour un employé
     * @param int $idEmploye ID de l'employé
     * @param int $idCompetence ID de la compétence
     * @return array Historique de la compétence
     */
    public function getCompetenceHistory($idEmploye, $idCompetence)
    {
        $sql = "
            SELECT 
                ech.operation_timestamp as date_changement,
                ech.niveau as ancien_niveau,
                ncl_old.libelle as ancien_libelle_niveau,
                ech.valide as ancien_valide,
                ech.date_mesure as ancienne_date_mesure,
                ech.operation_type as type_operation,
                ech.operation_type as detail_operation,
                op.nom as operateur_nom,
                op.prenom as operateur_prenom
            FROM employe_competences_historique ech
            LEFT JOIN niveau_competence_libelle ncl_old ON ech.niveau = ncl_old.niveau
            LEFT JOIN employes e_op ON ech.id_employe_operation = e_op.id_employe
            LEFT JOIN personnes op ON e_op.id_personne = op.id_personne
            WHERE ech.id_employe = :id_employe 
            AND ech.id_competence = :id_competence

            UNION ALL

            SELECT 
                ec.date_mesure as date_changement,
                ec.niveau as ancien_niveau,
                ncl_now.libelle as ancien_libelle_niveau,
                ec.valide as ancien_valide,
                ec.date_mesure as ancienne_date_mesure,
                'CURRENT' as type_operation,
                'CURRENT_STATE' as detail_operation,
                NULL as operateur_nom,
                NULL as operateur_prenom
            FROM employe_competences ec
            LEFT JOIN niveau_competence_libelle ncl_now ON ec.niveau = ncl_now.niveau
            WHERE ec.id_employe = :id_employe 
            AND ec.id_competence = :id_competence

            ORDER BY date_changement DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_employe' => $idEmploye,
            'id_competence' => $idCompetence
        ]);

        $historique = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($historique as &$item) {
            $item['date_changement_formatted'] = !empty($item['date_changement']) ? date('d/m/Y H:i', strtotime($item['date_changement'])) : null;
            if (!empty($item['ancienne_date_mesure'])) {
                $item['ancienne_date_mesure_formatted'] = date('d/m/Y', strtotime($item['ancienne_date_mesure']));
            }
        }

        return $historique;
    }

    /**
     * Identifie les écarts de compétences entre un employé et un profil requis
     * @param int $idEmploye ID de l'employé
     * @param int $idProfil ID du profil
     * @return array Écarts identifiés
     */
    public function getGapForEmployee($idEmploye, $idProfil)
    {
        $sqlProfil = "SELECT competences FROM profils WHERE id_profil = :id_profil";
        $stmtProfil = $this->db->prepare($sqlProfil);
        $stmtProfil->execute(['id_profil' => $idProfil]);
        $profil = $stmtProfil->fetch(\PDO::FETCH_ASSOC);

        if (!$profil || empty($profil['competences'])) {
            return ['erreur' => 'Profil non trouvé ou sans compétences définies'];
        }

        $competencesRequises = json_decode($profil['competences'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $competencesRequises = array_map('trim', explode(',', $profil['competences']));
        }

        $competencesEmploye = $this->getCompetencesByEmployee($idEmploye);

        $gaps = [
            'competences_manquantes' => [],
            'niveaux_insuffisants' => [],
            'competences_correspondantes' => []
        ];

        foreach ($competencesRequises as $competenceReq) {
            $trouvee = false;
            $niveauSuffisant = false;

            foreach ($competencesEmploye as $compEmp) {
                if (stripos($compEmp['nom'], $competenceReq) !== false || stripos($competenceReq, $compEmp['nom']) !== false) {
                    $trouvee = true;
                    if ($compEmp['niveau'] >= 3) {
                        $niveauSuffisant = true;
                        $gaps['competences_correspondantes'][] = [
                            'competence' => $compEmp['nom'],
                            'niveau_actuel' => $compEmp['niveau'],
                            'libelle_niveau' => $compEmp['libelle_niveau']
                        ];
                    } else {
                        $gaps['niveaux_insuffisants'][] = [
                            'competence' => $compEmp['nom'],
                            'niveau_actuel' => $compEmp['niveau'],
                            'niveau_souhaite' => 3,
                            'libelle_niveau_actuel' => $compEmp['libelle_niveau']
                        ];
                    }
                    break;
                }
            }

            if (!$trouvee) {
                $gaps['competences_manquantes'][] = [
                    'competence' => $competenceReq,
                    'niveau_souhaite' => 3
                ];
            }
        }

        return $gaps;
    }

    /**
     * Suggère des formations basées sur les écarts de compétences
     * @param int $idEmploye ID de l'employé
     * @return array Suggestions de formations
     */
    public function suggestFormationsForEmployee($idEmploye)
    {
        $sql = "
            SELECT 
                c.id_competence,
                c.nom,
                c.domaine,
                COUNT(ec.id_employe) as nb_employes_competents,
                ROUND(AVG(ec.niveau)::numeric, 2) as niveau_moyen_departement
            FROM competences c
            LEFT JOIN employe_competences ec ON c.id_competence = ec.id_competence
            LEFT JOIN employes e ON ec.id_employe = e.id_employe
            WHERE e.id_departement = (
                SELECT id_departement FROM employes WHERE id_employe = :id_employe
            )
            AND c.id_competence NOT IN (
                SELECT id_competence FROM employe_competences 
                WHERE id_employe = :id_employe AND niveau >= 3
            )
            GROUP BY c.id_competence, c.nom, c.domaine
            ORDER BY nb_employes_competents DESC, niveau_moyen_departement DESC
            LIMIT 10
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        $suggestions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($suggestions as &$suggestion) {
            $suggestion['priorite'] = $this->calculerPrioriteFormation(
                $suggestion['nb_employes_competents'] ?? 0,
                $suggestion['niveau_moyen_departement'] ?? 0
            );
        }

        return $suggestions;
    }

    /**
     * Calcule la priorité d'une formation suggestionnée
     * @param int $nbEmployes Nombre d'employés compétents
     * @param float $niveauMoyen Niveau moyen du département
     * @return string Priorité (haute, moyenne, basse)
     */
    private function calculerPrioriteFormation($nbEmployes, $niveauMoyen)
    {
        if ($nbEmployes < 2 || $niveauMoyen < 2.5) {
            return 'haute';
        } elseif ($nbEmployes < 5 || $niveauMoyen < 3.5) {
            return 'moyenne';
        } else {
            return 'basse';
        }
    }

    /**
     * Génère le badge de niveau pour une compétence
     * @param int $niveau Niveau de compétence
     * @return array Configuration du badge
     */
    private function getBadgeNiveau($niveau)
    {
        switch ((int)$niveau) {
            case 1:
                return ['libelle' => 'Débutant', 'couleur' => 'danger', 'icone' => 'fa-seedling'];
            case 2:
                return ['libelle' => 'Intermédiaire', 'couleur' => 'warning', 'icone' => 'fa-leaf'];
            case 3:
                return ['libelle' => 'Avancé', 'couleur' => 'info', 'icone' => 'fa-tree'];
            case 4:
                return ['libelle' => 'Expert', 'couleur' => 'success', 'icone' => 'fa-certificate'];
            case 5:
                return ['libelle' => 'Maître', 'couleur' => 'primary', 'icone' => 'fa-crown'];
            default:
                return ['libelle' => 'Non défini', 'couleur' => 'secondary', 'icone' => 'fa-question'];
        }
    }
}