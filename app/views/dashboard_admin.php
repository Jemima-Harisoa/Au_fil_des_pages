 <!-- Bootstrap 4 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    
<?php 
if (isset($_SESSION['infoAdmin'])) {
    Flight::render("headerA");
} else if (isset($_SESSION['employe'])) {
    Flight::render("headerE");
} else {
    Flight::render("headerU");
}?>
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --info-color: #36b9cc;
        }
        
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }
        
        .badge-priority {
            font-size: 0.8em;
            padding: 4px 8px;
        }
        
        .badge-priority-1 { background-color: #d4edda; color: #155724; }
        .badge-priority-2 { background-color: #fff3cd; color: #856404; }
        .badge-priority-3 { background-color: #f8d7da; color: #721c24; }
        .badge-priority-4 { background-color: #dc3545; color: white; }
        .badge-priority-5 { background-color: #721c24; color: white; }
        
        .heatmap-cell {
            text-align: center;
            font-weight: bold;
            border-radius: 4px;
            padding: 5px;
            min-width: 80px;
        }
        
        .heatmap-0 { background-color: #f0f9ff; color: #0369a1; }
        .heatmap-1 { background-color: #e0f2fe; color: #075985; }
        .heatmap-2 { background-color: #bae6fd; color: #0c4a6e; }
        .heatmap-3 { background-color: #7dd3fc; color: #082f49; }
        .heatmap-4 { background-color: #38bdf8; color: #082f49; }
        .heatmap-5 { background-color: #0ea5e9; color: white; }
        .heatmap-6 { background-color: #0284c7; color: white; }
        .heatmap-7 { background-color: #0369a1; color: white; }
        .heatmap-8 { background-color: #075985; color: white; }
        .heatmap-9 { background-color: #0c4a6e; color: white; }
        .heatmap-10 { background-color: #082f49; color: white; }
        
        .trend-up { color: var(--success-color); }
        .trend-down { color: var(--danger-color); }
        .trend-stable { color: var(--warning-color); }
        
        .action-btn {
            margin: 2px;
            font-size: 0.8em;
        }
        
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .tab-content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        
        .nav-tabs .nav-link.active {
            font-weight: bold;
            border-bottom: 3px solid var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-chart-network mr-2"></i>
                        Dashboard Cartographie des Compétences
                    </h1>
                    <div class="btn-group">
                        <button class="btn btn-primary" onclick="executerJobBatch()">
                            <i class="fas fa-sync-alt mr-1"></i> Exécuter Job Batch
                        </button>
                        <button class="btn btn-success" onclick="rafraichirVues()">
                            <i class="fas fa-redo mr-1"></i> Rafraîchir les Vues
                        </button>
                        <button class="btn btn-info" onclick="genererRapport()">
                            <i class="fas fa-file-export mr-1"></i> Exporter Rapport
                        </button>
                    </div>
                </div>
                <p class="text-muted">Analyse en temps réel des compétences, détection des gaps et recommandations</p>
            </div>
        </div>

        <!-- Cartes de statistiques -->
        <div class="row mb-4" id="stats-cards">
            <?php if (isset($dashboardData) && !empty($dashboardData)): ?>
                <?php $stats = $dashboardData['statistiques_globales'] ?? []; ?>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Compétences référencées</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['total_competences'] ?? 0; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Niveau moyen global</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($stats['niveau_moyen_global'] ?? 0, 2); ?>/5
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Couverture moyenne</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($stats['couverture_moyenne'] ?? 0, 1); ?>%
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Alertes actives</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo isset($alertesActives['statistiques']['total']) ? $alertesActives['statistiques']['total'] : 0; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="loader"></div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Onglets principaux -->
        <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview">
                    <i class="fas fa-tachometer-alt mr-1"></i> Vue d'ensemble
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="gaps-tab" data-toggle="tab" href="#gaps">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Gaps Critiques
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="alerts-tab" data-toggle="tab" href="#alerts">
                    <i class="fas fa-bell mr-1"></i> Alertes Actives
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="recommendations-tab" data-toggle="tab" href="#recommendations">
                    <i class="fas fa-lightbulb mr-1"></i> Recommandations
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="trends-tab" data-toggle="tab" href="#trends">
                    <i class="fas fa-chart-line mr-1"></i> Tendances
                </a>
            </li>
        </ul>

        <!-- Contenu des onglets -->
        <div class="tab-content" id="dashboardTabsContent">
            
            <!-- Onglet 1: Vue d'ensemble -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row">
                    <!-- Graphique 1: Distribution des évaluations -->
                    <div class="col-md-6 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-pie mr-2"></i>
                                    Distribution des Évaluations
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartEvaluationDistribution"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphique 2: Top 10 compétences -->
                    <div class="col-md-6 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-star mr-2"></i>
                                    Top 10 Compétences
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartTopCompetences"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphique 3: Distribution par domaine -->
                    <div class="col-md-6 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-tags mr-2"></i>
                                    Distribution par Domaine
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartDomainDistribution"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphique 4: Répartition de maturité -->
                    <div class="col-md-6 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-bar mr-2"></i>
                                    Répartition de Maturité
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartMaturityDistribution"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 2: Gaps Critiques -->
            <div class="tab-pane fade" id="gaps" role="tabpanel">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Statistiques des Gaps Critiques
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($gapsCritiques['statistiques'])): ?>
                                    <?php $gapsStats = $gapsCritiques['statistiques']; ?>
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="h4 font-weight-bold text-primary"><?php echo $gapsStats['total_gaps'] ?? 0; ?></div>
                                            <small class="text-muted">Total Gaps</small>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="h4 font-weight-bold text-danger"><?php echo $gapsStats['total_gaps_critiques'] ?? 0; ?></div>
                                            <small class="text-muted">Gaps Critiques</small>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="h4 font-weight-bold text-info"><?php echo $gapsStats['departements_concernes'] ?? 0; ?></div>
                                            <small class="text-muted">Départements</small>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="h4 font-weight-bold text-success"><?php echo $gapsStats['competences_concernes'] ?? 0; ?></div>
                                            <small class="text-muted">Compétences</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h6>Distribution par Sévérité</h6>
                                        <div class="row">
                                            <?php if (isset($gapsStats['severite_distribution'])): ?>
                                                <div class="col-md-4">
                                                    <div class="card border-left-danger shadow h-100 py-2">
                                                        <div class="card-body">
                                                            <div class="text-center">
                                                                <div class="h3 font-weight-bold text-danger"><?php echo $gapsStats['severite_distribution']['haute'] ?? 0; ?></div>
                                                                <small class="text-muted">Haute Sévérité</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card border-left-warning shadow h-100 py-2">
                                                        <div class="card-body">
                                                            <div class="text-center">
                                                                <div class="h3 font-weight-bold text-warning"><?php echo $gapsStats['severite_distribution']['moyenne'] ?? 0; ?></div>
                                                                <small class="text-muted">Moyenne Sévérité</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card border-left-info shadow h-100 py-2">
                                                        <div class="card-body">
                                                            <div class="text-center">
                                                                <div class="h3 font-weight-bold text-info"><?php echo $gapsStats['severite_distribution']['faible'] ?? 0; ?></div>
                                                                <small class="text-muted">Faible Sévérité</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                        <p>Aucune donnée de gaps disponible</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card dashboard-card">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-list mr-2"></i>
                                    Liste Détailée des Gaps Critiques
                                </h5>
                                <div class="float-right">
                                    <select id="filterDepartement" class="form-control form-control-sm" onchange="chargerGapsCritiques()">
                                        <option value="">Tous les départements</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="tableGapsCritiques" class="table table-bordered table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Département</th>
                                            <th>Compétence</th>
                                            <th>Nb Employés avec Gap</th>
                                            <th>Gap Moyen</th>
                                            <th>Gaps Critiques</th>
                                            <th>Sévérité</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($gapsCritiques['gaps']) && !empty($gapsCritiques['gaps'])): ?>
                                            <?php foreach ($gapsCritiques['gaps'] as $gap): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($gap['departement_nom'] ?? 'Non spécifié'); ?></td>
                                                    <td><?php echo htmlspecialchars($gap['nom_competence'] ?? 'Non spécifié'); ?></td>
                                                    <td class="text-center"><?php echo $gap['nb_employes_avec_gap'] ?? 0; ?></td>
                                                    <td class="text-center"><?php echo number_format($gap['gap_moyen'] ?? 0, 2); ?></td>
                                                    <td class="text-center"><?php echo $gap['nb_gaps_critiques'] ?? 0; ?></td>
                                                    <td class="text-center">
                                                        <?php 
                                                        $severite = $gap['severite_departement'] ?? 'faible';
                                                        $badgeClass = $severite === 'haute' ? 'danger' : ($severite === 'moyenne' ? 'warning' : 'success');
                                                        ?>
                                                        <span class="badge badge-<?php echo $badgeClass; ?>">
                                                            <?php echo ucfirst($severite); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Aucun gap critique trouvé</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 3: Alertes Actives -->
            <div class="tab-pane fade" id="alerts" role="tabpanel">
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Filtres</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Type d'alerte:</label>
                                    <select id="filterTypeAlerte" class="form-control" onchange="chargerAlertes()">
                                        <option value="">Tous</option>
                                        <option value="soft_skills_faible">Soft Skills Faibles</option>
                                        <option value="recrutement_requis">Recrutement Requis</option>
                                        <option value="competence_critique">Compétence Critique</option>
                                        <option value="incoherence_statistique">Incohérence Statistique</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Sévérité:</label>
                                    <select id="filterSeverite" class="form-control" onchange="chargerAlertes()">
                                        <option value="">Toutes</option>
                                        <option value="critique">Critique</option>
                                        <option value="haute">Haute</option>
                                        <option value="moyenne">Moyenne</option>
                                        <option value="faible">Faible</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Département:</label>
                                    <select id="filterAlerteDepartement" class="form-control" onchange="chargerAlertes()">
                                        <option value="">Tous</option>
                                    </select>
                                </div>
                                <button class="btn btn-secondary btn-block" onclick="resetFiltresAlertes()">
                                    <i class="fas fa-undo mr-1"></i> Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="card dashboard-card">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-bell mr-2"></i>
                                    Alertes Actives
                                    <span id="badgeAlertesCount" class="badge badge-light ml-2">
                                        <?php echo isset($alertesActives['statistiques']['total']) ? $alertesActives['statistiques']['total'] : 0; ?>
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($alertesActives['statistiques'])): ?>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="card border-left-danger shadow h-100 py-2">
                                                <div class="card-body">
                                                    <div class="text-center">
                                                        <div class="h5 font-weight-bold text-danger">
                                                            <?php echo $alertesActives['statistiques']['par_severite']['critique'] ?? 0; ?>
                                                        </div>
                                                        <small class="text-muted">Critiques</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-left-warning shadow h-100 py-2">
                                                <div class="card-body">
                                                    <div class="text-center">
                                                        <div class="h5 font-weight-bold text-warning">
                                                            <?php echo $alertesActives['statistiques']['par_severite']['haute'] ?? 0; ?>
                                                        </div>
                                                        <small class="text-muted">Hautes</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-left-info shadow h-100 py-2">
                                                <div class="card-body">
                                                    <div class="text-center">
                                                        <div class="h5 font-weight-bold text-info">
                                                            <?php echo ($alertesActives['statistiques']['par_severite']['moyenne'] ?? 0) + ($alertesActives['statistiques']['par_severite']['faible'] ?? 0); ?>
                                                        </div>
                                                        <small class="text-muted">Autres</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <table id="tableAlertes" class="table table-bordered table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Message</th>
                                            <th>Compétence</th>
                                            <th>Sévérité</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($alertesActives['alertes']) && !empty($alertesActives['alertes'])): ?>
                                            <?php foreach ($alertesActives['alertes'] as $alerte): ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y', strtotime($alerte['date_creation'])); ?></td>
                                                    <td>
                                                        <?php 
                                                        $icons = [
                                                            'soft_skills_faible' => 'fa-comments',
                                                            'recrutement_requis' => 'fa-user-plus',
                                                            'competence_critique' => 'fa-exclamation-circle',
                                                            'incoherence_statistique' => 'fa-exclamation-triangle'
                                                        ];
                                                        $icon = $icons[$alerte['type_alerte']] ?? 'fa-bell';
                                                        ?>
                                                        <i class="fas <?php echo $icon; ?> mr-1"></i> <?php echo $alerte['type_alerte']; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($alerte['message'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($alerte['competence_nom'] ?? ''); ?></td>
                                                    <td>
                                                        <?php 
                                                        $severite = $alerte['severite'] ?? 'faible';
                                                        $badgeClass = $severite === 'critique' || $severite === 'haute' ? 'danger' : 
                                                                    ($severite === 'moyenne' ? 'warning' : 'info');
                                                        ?>
                                                        <span class="badge badge-<?php echo $badgeClass; ?>">
                                                            <?php echo ucfirst($severite); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info action-btn" onclick="voirDetailAlerte(<?php echo $alerte['id_alerte']; ?>)">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-success action-btn" onclick="resoudreAlerte(<?php echo $alerte['id_alerte']; ?>)">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Aucune alerte active</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 4: Recommandations -->
            <div class="tab-pane fade" id="recommendations" role="tabpanel">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-lightbulb mr-2"></i>
                                    Recommandations d'Actions
                                    <span id="badgeRecommandationsCount" class="badge badge-light ml-2">0</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-filter"></i>
                                                </span>
                                            </div>
                                            <select id="filterTypeAction" class="form-control" onchange="chargerRecommandations()">
                                                <option value="">Tous les types</option>
                                                <option value="formation">Formation</option>
                                                <option value="team_building">Team Building</option>
                                                <option value="recrutement">Recrutement</option>
                                                <option value="mentorat">Mentorat</option>
                                                <option value="reaffectation">Réaffectation</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-sort-amount-down"></i>
                                                </span>
                                            </div>
                                            <select id="filterPriorite" class="form-control" onchange="chargerRecommandations()">
                                                <option value="">Toutes priorités</option>
                                                <option value="5">Priorité 5 (Max)</option>
                                                <option value="4">Priorité 4</option>
                                                <option value="3">Priorité 3</option>
                                                <option value="2">Priorité 2</option>
                                                <option value="1">Priorité 1 (Min)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary" onclick="appliquerToutesRecommandations()">
                                            <i class="fas fa-check-double mr-1"></i> Appliquer toutes les recommandations
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="recommandationsContainer">
                                    <!-- Les recommandations seront chargées via AJAX -->
                                    <div class="text-center py-4">
                                        <div class="loader"></div>
                                        <p class="text-muted mt-2">Chargement des recommandations...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 5: Tendances -->
            <div class="tab-pane fade" id="trends" role="tabpanel">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Évolution Temporelle
                                </h5>
                                <div class="float-right">
                                    <select id="filterCompetenceTrend" class="form-control form-control-sm" onchange="chargerTendances()">
                                        <option value="">Sélectionner une compétence</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartTendances"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-arrow-up mr-2"></i>
                                    Top 5 Progression
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="topProgressionList">
                                    <!-- Les tendances seront chargées via AJAX -->
                                    <div class="text-center py-2">
                                        <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-arrow-down mr-2"></i>
                                    Top 5 Régression
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="topRegressionList">
                                    <!-- Les tendances seront chargées via AJAX -->
                                    <div class="text-center py-2">
                                        <div class="spinner-border spinner-border-sm text-danger" role="status"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour détails d'alerte -->
    <div class="modal fade" id="modalAlerteDetail" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Détails de l'alerte</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalAlerteContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-success" onclick="resoudreAlerte()">
                        <i class="fas fa-check mr-1"></i> Marquer comme résolue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour application de recommandation -->
    <div class="modal fade" id="modalApplyReco" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Appliquer la recommandation</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalApplyRecoContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-success" onclick="confirmerApplicationReco()">
                        <i class="fas fa-check mr-1"></i> Confirmer l'application
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS et Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Script principal -->
    <script>
        // Variables globales
        let currentAlerteId = null;
        let currentRecoId = null;
        let selectedRecos = new Set();
        let chartInstances = {};
        
        // Données PHP passées au template
        const dashboardData = <?php echo isset($dashboardData) ? json_encode($dashboardData) : '{}'; ?>;
        const gapsCritiques = <?php echo isset($gapsCritiques) ? json_encode($gapsCritiques) : '{}'; ?>;
        const alertesActives = <?php echo isset($alertesActives) ? json_encode($alertesActives) : '{}'; ?>;

        // Fonction d'initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initialiserCharts();
            chargerListeDepartements();
            chargerListeCompetences();
            chargerRecommandations();
            chargerTendances();
        });

        // Initialiser les graphiques avec les données PHP
        function initialiserCharts() {
            // Graphique 1: Distribution des évaluations
            const ctxEvaluation = document.getElementById('chartEvaluationDistribution').getContext('2d');
            if (dashboardData.repartition_evaluation) {
                const labels = dashboardData.repartition_evaluation.map(e => e.evaluation_globale);
                const data = dashboardData.repartition_evaluation.map(e => e.nb_competences || 0);
                
                chartInstances.evaluation = new Chart(ctxEvaluation, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            title: {
                                display: true,
                                text: 'Distribution des Évaluations'
                            }
                        }
                    }
                });
            }

            // Graphique 2: Top 10 compétences
            const ctxTop = document.getElementById('chartTopCompetences').getContext('2d');
            if (dashboardData.top_10_competences) {
                const labels = dashboardData.top_10_competences.slice(0, 10).map(c => c.nom.substring(0, 20) + '...');
                const data = dashboardData.top_10_competences.slice(0, 10).map(c => c.score_maturite || 0);
                
                chartInstances.top = new Chart(ctxTop, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Score de maturité',
                            data: data,
                            backgroundColor: '#4e73df',
                            borderColor: '#2e59d9',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Top 10 Compétences par Maturité'
                            }
                        }
                    }
                });
            }

            // Graphique 3: Distribution par domaine
            const ctxDomain = document.getElementById('chartDomainDistribution').getContext('2d');
            if (dashboardData.distribution_domaines) {
                const labels = dashboardData.distribution_domaines.map(d => d.domaine);
                const data = dashboardData.distribution_domaines.map(d => d.nb_competences || 0);
                
                chartInstances.domain = new Chart(ctxDomain, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: [
                                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                                '#858796', '#6f42c1', '#20c9a6', '#fd7e14', '#d63384'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            title: {
                                display: true,
                                text: 'Distribution par Domaine'
                            }
                        }
                    }
                });
            }

            // Graphique 4: Répartition de maturité
            const ctxMaturity = document.getElementById('chartMaturityDistribution').getContext('2d');
            if (dashboardData.repartition_maturite) {
                const labels = dashboardData.repartition_maturite.map(m => m.niveau_maturite);
                const data = dashboardData.repartition_maturite.map(m => m.nb_competences || 0);
                
                chartInstances.maturity = new Chart(ctxMaturity, {
                    type: 'radar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nombre de compétences',
                            data: data,
                            backgroundColor: 'rgba(78, 115, 223, 0.2)',
                            borderColor: '#4e73df',
                            borderWidth: 2,
                            pointBackgroundColor: '#4e73df'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Répartition par Niveau de Maturité'
                            }
                        }
                    }
                });
            }

            // Graphique 5: Tendances
            const ctxTendances = document.getElementById('chartTendances').getContext('2d');
            chartInstances.tendances = new Chart(ctxTendances, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Niveau moyen',
                        data: [],
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 1,
                            max: 5
                        }
                    }
                }
            });
        }

        // Charger les recommandations via AJAX
        async function chargerRecommandations() {
            try {
                const response = await fetch('/api/competences/recommandations/en-attente');
                const data = await response.json();
                
                if (data.success) {
                    afficherRecommandations(data.data);
                    document.getElementById('badgeRecommandationsCount').textContent = data.count || 0;
                }
            } catch (error) {
                console.error('Erreur lors du chargement des recommandations:', error);
                document.getElementById('recommandationsContainer').innerHTML = `
                    <div class="alert alert-danger">
                        Erreur lors du chargement des recommandations
                    </div>
                `;
            }
        }

        // Afficher les recommandations
        function afficherRecommandations(recommandations) {
            if (!recommandations || recommandations.length === 0) {
                document.getElementById('recommandationsContainer').innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-lightbulb fa-3x mb-3"></i>
                        <p>Aucune recommandation en attente</p>
                    </div>
                `;
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm table-striped">';
            html += '<thead><tr><th><input type="checkbox" id="selectAllReco" onchange="toggleSelectAllReco()"></th>';
            html += '<th>Priorité</th><th>Description</th><th>Employé</th><th>Département</th><th>Actions</th></tr></thead><tbody>';
            
            recommandations.forEach(reco => {
                const badgeClass = `badge-priority-${reco.priorite || 3}`;
                html += `
                    <tr>
                        <td><input type="checkbox" class="reco-checkbox" value="${reco.id_recommandation}" onchange="toggleRecoSelection(${reco.id_recommandation})"></td>
                        <td><span class="badge ${badgeClass}">${reco.priorite || 3}</span></td>
                        <td>${reco.description || ''}<br><small class="text-muted">${reco.competence_nom || ''}</small></td>
                        <td>${reco.employe_nom || ''}</td>
                        <td>${reco.departement_nom || ''}</td>
                        <td>
                            <button class="btn btn-sm btn-success action-btn" onclick="appliquerRecommandation(${reco.id_recommandation})">
                                <i class="fas fa-check mr-1"></i> Appliquer
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table></div>';
            document.getElementById('recommandationsContainer').innerHTML = html;
        }

        // Appliquer une recommandation
        async function appliquerRecommandation(id) {
            currentRecoId = id;
            
            try {
                const response = await fetch(`/api/competences/recommandations/${id}/appliquer`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Recommandation appliquée avec succès !');
                    chargerRecommandations();
                } else {
                    alert('Erreur: ' + (result.error || 'Erreur inconnue'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'application de la recommandation');
            }
        }

        // Appliquer toutes les recommandations sélectionnées
        async function appliquerToutesRecommandations() {
            if (selectedRecos.size === 0) {
                alert('Veuillez sélectionner au moins une recommandation');
                return;
            }
            
            if (confirm(`Voulez-vous appliquer ${selectedRecos.size} recommandation(s) sélectionnée(s) ?`)) {
                try {
                    const response = await fetch('/api/competences/recommandations/bulk-apply', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ids: Array.from(selectedRecos) })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert(`${result.applied || 0} recommandation(s) appliquée(s) avec succès !`);
                        selectedRecos.clear();
                        if (document.getElementById('selectAllReco')) {
                            document.getElementById('selectAllReco').checked = false;
                        }
                        chargerRecommandations();
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur lors de l\'application groupée');
                }
            }
        }

        // Toggle sélection des recommandations
        function toggleRecoSelection(id) {
            if (selectedRecos.has(id)) {
                selectedRecos.delete(id);
            } else {
                selectedRecos.add(id);
            }
        }

        // Sélectionner toutes les recommandations
        function toggleSelectAllReco() {
            const selectAll = document.getElementById('selectAllReco');
            if (!selectAll) return;
            
            const isChecked = selectAll.checked;
            const checkboxes = document.querySelectorAll('.reco-checkbox');
            
            checkboxes.forEach(cb => {
                cb.checked = isChecked;
                const id = parseInt(cb.value);
                if (isChecked) {
                    selectedRecos.add(id);
                } else {
                    selectedRecos.delete(id);
                }
            });
        }

        // Charger les tendances via AJAX
        async function chargerTendances() {
            const competenceId = document.getElementById('filterCompetenceTrend')?.value;
            
            if (!competenceId) {
                await chargerTendancesGlobales();
                return;
            }
            
            try {
                const response = await fetch(`/api/competences/${competenceId}/trend`);
                const data = await response.json();
                
                if (data.success) {
                    afficherGraphiqueTendances(data.data);
                }
            } catch (error) {
                console.error('Erreur lors du chargement des tendances:', error);
            }
        }

        // Charger les tendances globales
        async function chargerTendancesGlobales() {
            try {
                const response = await fetch('/api/competences/dashboard/charts');
                const data = await response.json();
                
                if (data.success) {
                    afficherListesTendances(data.data);
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Afficher les listes de progression/régression
        function afficherListesTendances(data) {
            if (!data.competences_critiques) return;
            
            const competences = data.competences_critiques;
            const progression = competences.filter(c => c.score_maturite > 60).slice(0, 5);
            const regression = competences.filter(c => c.score_maturite < 40).slice(0, 5);
            
            // Afficher progression
            let htmlProgression = '<ul class="list-group">';
            if (progression.length > 0) {
                progression.forEach(item => {
                    htmlProgression += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${item.nom.substring(0, 25)}${item.nom.length > 25 ? '...' : ''}
                            <span class="badge badge-success">
                                <i class="fas fa-arrow-up mr-1"></i>${Math.round(item.score_maturite)}/100
                            </span>
                        </li>
                    `;
                });
            } else {
                htmlProgression += '<li class="list-group-item text-center text-muted">Aucune donnée</li>';
            }
            htmlProgression += '</ul>';
            document.getElementById('topProgressionList').innerHTML = htmlProgression;
            
            // Afficher régression
            let htmlRegression = '<ul class="list-group">';
            if (regression.length > 0) {
                regression.forEach(item => {
                    htmlRegression += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${item.nom.substring(0, 25)}${item.nom.length > 25 ? '...' : ''}
                            <span class="badge badge-danger">
                                <i class="fas fa-arrow-down mr-1"></i>${Math.round(item.score_maturite)}/100
                            </span>
                        </li>
                    `;
                });
            } else {
                htmlRegression += '<li class="list-group-item text-center text-muted">Aucune donnée</li>';
            }
            htmlRegression += '</ul>';
            document.getElementById('topRegressionList').innerHTML = htmlRegression;
        }

        // Charger la liste des départements
        async function chargerListeDepartements() {
            try {
                const response = await fetch('/api/departements');
                const data = await response.json();
                
                if (data.success) {
                    const selectGaps = document.getElementById('filterDepartement');
                    const selectAlertes = document.getElementById('filterAlerteDepartement');
                    
                    if (selectGaps && selectAlertes) {
                        data.departements.forEach(dept => {
                            const option = `<option value="${dept.id_departement}">${dept.nom}</option>`;
                            selectGaps.innerHTML += option;
                            selectAlertes.innerHTML += option;
                        });
                    }
                }
            } catch (error) {
                console.error('Erreur lors du chargement des départements:', error);
            }
        }

        // Charger la liste des compétences
        async function chargerListeCompetences() {
            try {
                const response = await fetch('/api/competences');
                const data = await response.json();
                
                if (data.success) {
                    const select = document.getElementById('filterCompetenceTrend');
                    if (select && data.data && data.data.length > 0) {
                        data.data.slice(0, 20).forEach(comp => {
                            select.innerHTML += `<option value="${comp.id_competence}">${comp.nom}</option>`;
                        });
                    }
                }
            } catch (error) {
                console.error('Erreur lors du chargement des compétences:', error);
            }
        }

        // Exécuter le job batch
        async function executerJobBatch() {
            if (confirm('Voulez-vous exécuter le job batch de traitement des compétences ?')) {
                try {
                    const response = await fetch('/api/competences/job/batch', {
                        method: 'POST'
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Job batch exécuté avec succès !');
                        location.reload();
                    } else {
                        alert('Erreur: ' + (result.error || 'Erreur inconnue'));
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur lors de l\'exécution du job batch');
                }
            }
        }

        // Rafraîchir les vues matérialisées
        async function rafraichirVues() {
            try {
                const response = await fetch('/api/competences/refresh-vues', {
                    method: 'POST'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Vues matérialisées rafraîchies avec succès !');
                    location.reload();
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors du rafraîchissement des vues');
            }
        }

        // Résoudre une alerte
        async function resoudreAlerte(id) {
            if (!id && currentAlerteId) {
                id = currentAlerteId;
            }
            
            if (!id) {
                alert('Aucune alerte sélectionnée');
                return;
            }
            
            if (confirm('Voulez-vous marquer cette alerte comme résolue ?')) {
                try {
                    const response = await fetch(`/api/competences/alertes/${id}/resoudre`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Alerte résolue avec succès !');
                        location.reload();
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la résolution de l\'alerte');
                }
            }
        }

        // Voir le détail d'une alerte
        function voirDetailAlerte(id) {
            currentAlerteId = id;
            
            // Trouver l'alerte dans les données PHP
            const alerte = alertesActives.alertes?.find(a => a.id_alerte == id);
            
            if (alerte) {
                const content = `
                    <h6>Détails de l'alerte #${id}</h6>
                    <p><strong>Date de création:</strong> ${new Date(alerte.date_creation).toLocaleDateString('fr-FR')}</p>
                    <p><strong>Type:</strong> ${alerte.type_alerte}</p>
                    <p><strong>Sévérité:</strong> <span class="badge badge-${alerte.severite === 'critique' || alerte.severite === 'haute' ? 'danger' : 'warning'}">${alerte.severite}</span></p>
                    <p><strong>Compétence:</strong> ${alerte.competence_nom}</p>
                    <p><strong>Domaine:</strong> ${alerte.domaine}</p>
                    <p><strong>Département:</strong> ${alerte.departement_nom || 'Non spécifié'}</p>
                    <p><strong>Employé:</strong> ${alerte.employe_nom || 'Non spécifié'}</p>
                    <p><strong>Poste:</strong> ${alerte.poste || 'Non spécifié'}</p>
                    <p><strong>Message:</strong></p>
                    <div class="alert alert-${alerte.severite === 'critique' ? 'danger' : alerte.severite === 'haute' ? 'danger' : 'warning'}">
                        ${alerte.message}
                    </div>
                `;
                
                document.getElementById('modalAlerteContent').innerHTML = content;
                $('#modalAlerteDetail').modal('show');
            }
        }

        // Générer un rapport
        function genererRapport() {
            window.open('/competences/export?format=pdf', '_blank');
        }

        // Réinitialiser les filtres des alertes
        function resetFiltresAlertes() {
            document.getElementById('filterTypeAlerte').value = '';
            document.getElementById('filterSeverite').value = '';
            document.getElementById('filterAlerteDepartement').value = '';
            location.reload();
        }

        // Filtrer les gaps critiques
        async function chargerGapsCritiques() {
            const departementId = document.getElementById('filterDepartement')?.value;
            
            if (departementId) {
                try {
                    const response = await fetch(`/api/gaps/critiques/${departementId}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        // Mettre à jour le tableau avec les données filtrées
                        afficherGapsFiltres(data.data.gaps);
                    }
                } catch (error) {
                    console.error('Erreur lors du chargement des gaps:', error);
                }
            }
        }

        // Afficher les gaps filtrés
        function afficherGapsFiltres(gaps) {
            const tbody = document.querySelector('#tableGapsCritiques tbody');
            if (!tbody) return;
            
            tbody.innerHTML = '';
            
            if (gaps && gaps.length > 0) {
                gaps.forEach(gap => {
                    const severite = gap.severite_departement || 'faible';
                    const badgeClass = severite === 'haute' ? 'danger' : (severite === 'moyenne' ? 'warning' : 'success');
                    
                    tbody.innerHTML += `
                        <tr>
                            <td>${escapeHtml(gap.departement_nom || 'Non spécifié')}</td>
                            <td>${escapeHtml(gap.nom_competence || 'Non spécifié')}</td>
                            <td class="text-center">${gap.nb_employes_avec_gap || 0}</td>
                            <td class="text-center">${(gap.gap_moyen || 0).toFixed(2)}</td>
                            <td class="text-center">${gap.nb_gaps_critiques || 0}</td>
                            <td class="text-center">
                                <span class="badge badge-${badgeClass}">
                                    ${severite.charAt(0).toUpperCase() + severite.slice(1)}
                                </span>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">Aucun gap trouvé avec ce filtre</td></tr>';
            }
        }

        // Fonction utilitaire pour échapper le HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Initialiser DataTables pour les tables
        $(document).ready(function() {
            $('#tableGapsCritiques').DataTable({
                pageLength: 10,
                order: [[4, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                }
            });
            
            $('#tableAlertes').DataTable({
                pageLength: 10,
                order: [[0, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                }
            });
        });
    </script>
<?php Flight::render('footer'); ?>