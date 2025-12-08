<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .badge-niveau { padding: 5px 10px; border-radius: 15px; color: white; font-weight: bold; }
        .table-hover tbody tr:hover { background-color: rgba(0,123,255,0.1); }
        .card-header { background-color: #4e73df; }
        .pending-count { 
            position: absolute; 
            top: -10px; 
            right: -10px; 
            background: #e74a3b; 
            color: white; 
            border-radius: 50%; 
            width: 25px; 
            height: 25px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 12px; 
        }
        .filter-card {
            border-left: 4px solid #4e73df;
        }
        .stats-card {
            border-left: 4px solid #1cc88a;
        }
        .stats-icon {
            font-size: 2rem;
            opacity: 0.7;
        }
        .filter-active {
            background-color: #e3f2fd !important;
            border-color: #2196f3 !important;
        }
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }
    </style>
<?php 
    if (isset($_SESSION['infoAdmin'])) {
        Flight::render("headerA");
    }
    else if (isset($_SESSION['employe'])) {
        Flight::render("headerE");
    }
    else {
        Flight::render("headerU");
    }

// Extraire les options de filtres des données disponibles
$departements = [];
$domaines = [];
$competencesList = [];
$employesList = [];

foreach ($pendingValidations as $validation) {
    if (!empty($validation['departement_nom']) && !in_array($validation['departement_nom'], $departements)) {
        $departements[] = $validation['departement_nom'];
    }
    if (!empty($validation['domaine']) && !in_array($validation['domaine'], $domaines)) {
        $domaines[] = $validation['domaine'];
    }
    if (!empty($validation['competence_nom']) && !in_array($validation['competence_nom'], $competencesList)) {
        $competencesList[] = $validation['competence_nom'];
    }
}

sort($departements);
sort($domaines);
sort($competencesList);
?>

    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <!-- Cartes de statistiques -->
            <div class="col-md-3 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="text-uppercase text-muted mb-0">En attente</h6>
                                <h2 class="mb-0 text-primary"><?php echo count($pendingValidations); ?></h2>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-clock stats-icon text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="text-uppercase text-muted mb-0">Employés</h6>
                                <h2 class="mb-0 text-success">
                                    <?php 
                                    $uniqueEmployes = [];
                                    foreach ($pendingValidations as $v) {
                                        $key = $v['employe_nom'] . ' ' . $v['employe_prenom'];
                                        if (!in_array($key, $uniqueEmployes)) {
                                            $uniqueEmployes[] = $key;
                                        }
                                    }
                                    echo count($uniqueEmployes);
                                    ?>
                                </h2>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-users stats-icon text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="text-uppercase text-muted mb-0">Départements</h6>
                                <h2 class="mb-0 text-warning"><?php echo count($departements); ?></h2>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-building stats-icon text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="text-uppercase text-muted mb-0">Domaines</h6>
                                <h2 class="mb-0 text-info"><?php echo count($domaines); ?></h2>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-tags stats-icon text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte de filtres -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow filter-card">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-filter me-2"></i>
                            Filtres de recherche
                        </h6>
                        <button class="btn btn-sm btn-outline-secondary" id="resetFilters">
                            <i class="fas fa-redo me-1"></i>Réinitialiser
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Département</label>
                                <select class="form-control select2-filter" id="filterDepartement" multiple>
                                    <option value="">Tous les départements</option>
                                    <?php foreach ($departements as $dept): ?>
                                        <option value="<?php echo htmlspecialchars($dept); ?>">
                                            <?php echo htmlspecialchars($dept); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Domaine</label>
                                <select class="form-control select2-filter" id="filterDomaine" multiple>
                                    <option value="">Tous les domaines</option>
                                    <?php foreach ($domaines as $domaine): ?>
                                        <option value="<?php echo htmlspecialchars($domaine); ?>">
                                            <?php echo htmlspecialchars($domaine); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Compétence</label>
                                <select class="form-control select2-filter" id="filterCompetence" multiple>
                                    <option value="">Toutes les compétences</option>
                                    <?php foreach ($competencesList as $competence): ?>
                                        <option value="<?php echo htmlspecialchars($competence); ?>">
                                            <?php echo htmlspecialchars($competence); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Niveau auto-évalué</label>
                                <select class="form-control" id="filterNiveau">
                                    <option value="">Tous les niveaux</option>
                                    <?php foreach ($niveaux as $niveau): ?>
                                        <option value="<?php echo $niveau['niveau']; ?>">
                                            <?php echo $niveau['niveau'] . ' - ' . $niveau['libelle']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date début</label>
                                <input type="date" class="form-control" id="filterDateStart">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date fin</label>
                                <input type="date" class="form-control" id="filterDateEnd">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Recherche employé</label>
                                <input type="text" class="form-control" id="filterEmploye" 
                                       placeholder="Nom, prénom ou poste...">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary w-100" id="applyFilters">
                                    <i class="fas fa-search me-1"></i> Appliquer les filtres
                                </button>
                            </div>
                        </div>
                        
                        <!-- Filtres actifs -->
                        <div class="mt-3" id="activeFilters" style="display: none;">
                            <small class="text-muted d-block mb-2">Filtres actifs :</small>
                            <div class="d-flex flex-wrap gap-2" id="filterTags"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Validation Managériale - Auto-évaluations en attente
                        </h6>
                        <div class="d-flex gap-2">
                            <div class="position-relative me-2">
                                <button class="btn btn-success" id="btnBulkValidate">
                                    <i class="fas fa-check-double me-1"></i>Valider la sélection
                                </button>
                                <span class="pending-count" id="pendingCount"><?php echo count($pendingValidations); ?></span>
                            </div>
                            <button class="btn btn-outline-primary" id="exportBtn">
                                <i class="fas fa-file-export me-1"></i>Exporter
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pendingValidations)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Aucune auto-évaluation en attente de validation.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="validationTable">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th>Employé</th>
                                            <th>Département</th>
                                            <th>Compétence</th>
                                            <th>Niveau auto-évalué</th>
                                            <th>Date</th>
                                            <th>Niveau validé</th>
                                            <th>Commentaire</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingValidations as $validation): ?>
                                            <tr data-entry-id="<?php echo $validation['entry_id']; ?>"
                                                data-departement="<?php echo htmlspecialchars($validation['departement_nom'] ?? ''); ?>"
                                                data-domaine="<?php echo htmlspecialchars($validation['domaine']); ?>"
                                                data-competence="<?php echo htmlspecialchars($validation['competence_nom']); ?>"
                                                data-niveau="<?php echo $validation['niveau_auto']; ?>"
                                                data-date="<?php echo date('Y-m-d', strtotime($validation['date_mesure'])); ?>"
                                                data-employe="<?php echo htmlspecialchars(strtolower($validation['employe_nom'] . ' ' . $validation['employe_prenom'] . ' ' . $validation['employe_poste'])); ?>">
                                                <td>
                                                    <input type="checkbox" class="select-entry" 
                                                           data-entry-id="<?php echo $validation['entry_id']; ?>">
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($validation['employe_nom'] . ' ' . $validation['employe_prenom']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($validation['employe_poste']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($validation['departement_nom'] ?? 'Non spécifié'); ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($validation['competence_nom']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($validation['domaine']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge-niveau" 
                                                          style="background-color: <?=  $validation['niveau_couleur'] ?>">
                                                        <?php echo $validation['niveau_auto']; ?> - <?php echo $validation['niveau_libelle']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo date('d/m/Y', strtotime($validation['date_mesure'])); ?><br>
                                                    <small class="text-muted"><?php echo date('H:i', strtotime($validation['date_mesure'])); ?></small>
                                                </td>
                                                <td>
                                                    <select class="form-control form-control-sm niveau-select" 
                                                            data-entry-id="<?php echo $validation['entry_id']; ?>">
                                                        <?php foreach ($niveaux as $niveau): ?>
                                                            <option value="<?php echo $niveau['niveau']; ?>"
                                                                    <?php echo $niveau['niveau'] == $validation['niveau_auto'] ? 'selected' : ''; ?>
                                                                    style="color: <?= $niveau['couleur']; ?>; font-weight: bold;">
                                                                <?php echo $niveau['niveau'] . ' - ' . $niveau['libelle']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea class="form-control form-control-sm commentaire-input" 
                                                              data-entry-id="<?php echo $validation['entry_id']; ?>"
                                                              rows="2" placeholder="Commentaire..."></textarea>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-success btn-validate" 
                                                                data-action="valide"
                                                                data-entry-id="<?php echo $validation['entry_id']; ?>">
                                                            <i class="fas fa-check"></i> 
                                                        </button>
                                                        <button class="btn btn-warning btn-validate" 
                                                                data-action="ajuste"
                                                                data-entry-id="<?php echo $validation['entry_id']; ?>">
                                                            <i class="fas fa-adjust"></i> 
                                                        </button>
                                                        <button class="btn btn-danger btn-reject" 
                                                                data-entry-id="<?php echo $validation['entry_id']; ?>">
                                                            <i class="fas fa-times"></i> 
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Affichage de <span id="visibleCount"><?php echo count($pendingValidations); ?></span> sur 
                                    <?php echo count($pendingValidations); ?> évaluations
                                </div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item"><a class="page-link" href="#" id="prevPage">Précédent</a></li>
                                        <li class="page-item"><span class="page-link" id="currentPage">1</span></li>
                                        <li class="page-item"><a class="page-link" href="#" id="nextPage">Suivant</a></li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Formulaire d'ajout de compétence observée -->
                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <i class="fas fa-plus-circle me-2"></i>Ajouter une compétence observée
                        </div>
                        <div class="card-body">
                            <form id="addObservedForm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Employé</label>
                                            <select class="form-control select2-employee" id="observedEmployee" required
                                                    data-placeholder="Rechercher un employé...">
                                                <option value=""></option>
                                                <?php foreach ($employes as $employe): ?>
                                                    <option value="<?php echo $employe['id_employe']; ?>"
                                                            data-departement="<?php echo htmlspecialchars($employe['departement_nom'] ?? ''); ?>"
                                                            data-poste="<?php echo htmlspecialchars($employe['poste']); ?>">
                                                        <?php echo htmlspecialchars($employe['nom_personne'] . ' ' . $employe['prenom']); ?>
                                                        - <?php echo htmlspecialchars($employe['poste']); ?>
                                                        (<?php echo htmlspecialchars($employe['nom_departement'] ?? 'Non spécifié'); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="form-text text-muted">
                                                Sélectionnez l'employé à qui attribuer cette compétence
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Compétence</label>
                                            <select class="form-control" id="observedCompetence" required>
                                                <option value="">Sélectionner une compétence...</option>
                                                <?php foreach ($competences as $competence): ?>
                                                    <option value="<?php echo $competence['id_competence']; ?>"
                                                            data-domaine="<?php echo htmlspecialchars($competence['domaine']); ?>"
                                                            data-type="<?php echo htmlspecialchars($competence['type_competence'] ?? ''); ?>">
                                                        <?php echo htmlspecialchars($competence['nom']); ?>
                                                        <?php if ($competence['domaine']): ?>
                                                            (<?php echo $competence['domaine']; ?>)
                                                        <?php endif; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">Niveau</label>
                                            <select class="form-control" id="observedNiveau" required>
                                                <option value="">Niveau...</option>
                                                <?php foreach ($niveaux as $niveau): ?>
                                                    <option value="<?php echo $niveau['niveau']; ?>"
                                                            style="color: <?php echo $niveau['couleur']; ?>; font-weight: bold;">
                                                        <?php echo $niveau['niveau']; ?> - <?php echo $niveau['libelle']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="mb-3">
                                            <label class="form-label">Commentaire d'observation</label>
                                            <textarea class="form-control" id="observedComment" 
                                                    rows="2" placeholder="Décrire le contexte d'observation, exemple précis, justification du niveau..."></textarea>
                                            <small class="form-text text-muted">
                                                Optionnel : décrivez une situation concrète justifiant cette évaluation
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100" id="btnAddObserved">
                                            <i class="fas fa-plus me-1"></i> Ajouter la compétence
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialiser Select2
        $('.select2-employee').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        $('.select2-filter').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: $(this).attr('placeholder') || 'Sélectionnez...'
        });

        // Variables pour la pagination
        let currentPage = 1;
        const rowsPerPage = 10;
        let filteredRows = [];

        // Initialiser les filtres
        initFilters();

        // Sélection/désélection de toutes les lignes
        $('#selectAll').change(function() {
            $('.select-entry:visible').prop('checked', this.checked);
        });

        // Appliquer les filtres
        $('#applyFilters').click(function() {
            applyFilters();
        });

        // Réinitialiser les filtres
        $('#resetFilters').click(function() {
            resetFilters();
        });

        // Exporter les données
        $('#exportBtn').click(function() {
            exportData();
        });

        // Validation individuelle
        $('.btn-validate').click(function() {
            const entryId = $(this).data('entry-id');
            const action = $(this).data('action');
            const niveauFinal = $(`select[data-entry-id="${entryId}"]`).val();
            const commentaire = $(`textarea[data-entry-id="${entryId}"]`).val();
            
            validateEntry(entryId, niveauFinal, commentaire, action);
        });

        // Rejet d'une compétence
        $('.btn-reject').click(function() {
            const entryId = $(this).data('entry-id');
            const commentaire = $(`textarea[data-entry-id="${entryId}"]`).val();
            
            if (confirm('Rejeter cette auto-évaluation ?')) {
                validateEntry(entryId, 0, commentaire, 'rejete');
            }
        });

        // Validation en masse
        $('#btnBulkValidate').click(function() {
            const selectedEntries = [];
            
            $('.select-entry:checked:visible').each(function() {
                const entryId = $(this).data('entry-id');
                const niveauFinal = $(`select[data-entry-id="${entryId}"]`).val();
                const commentaire = $(`textarea[data-entry-id="${entryId}"]`).val();
                
                selectedEntries.push({
                    entryId: entryId,
                    niveauFinal: niveauFinal,
                    commentaire: commentaire,
                    action: 'valide'
                });
            });
            
            if (selectedEntries.length === 0) {
                alert('Veuillez sélectionner au moins une compétence à valider.');
                return;
            }
            
            if (confirm(`Valider ${selectedEntries.length} compétence(s) ?`)) {
                bulkValidate(selectedEntries);
            }
        });

        // Ajout d'une compétence observée
        $('#addObservedForm').submit(function(e) {
            e.preventDefault();
            
            const employeeId = $('#observedEmployee').val();
            const competenceId = $('#observedCompetence').val();
            const niveau = $('#observedNiveau').val();
            const comment = $('#observedComment').val();
            
            addObservedCompetence(employeeId, competenceId, niveau, comment);
        });

        // Navigation pagination
        $('#nextPage').click(function(e) {
            e.preventDefault();
            if (currentPage < Math.ceil(filteredRows.length / rowsPerPage)) {
                currentPage++;
                updateTableDisplay();
            }
        });

        $('#prevPage').click(function(e) {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                updateTableDisplay();
            }
        });

        function initFilters() {
            // Définir les dates par défaut (30 derniers jours)
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(today.getDate() - 30);
            
            $('#filterDateStart').val(thirtyDaysAgo.toISOString().split('T')[0]);
            $('#filterDateEnd').val(today.toISOString().split('T')[0]);
            
            // Appliquer les filtres initiaux
            applyFilters();
        }

        function applyFilters() {
            const selectedDepartements = $('#filterDepartement').val() || [];
            const selectedDomaines = $('#filterDomaine').val() || [];
            const selectedCompetences = $('#filterCompetence').val() || [];
            const selectedNiveau = $('#filterNiveau').val();
            const dateStart = $('#filterDateStart').val();
            const dateEnd = $('#filterDateEnd').val();
            const employeSearch = $('#filterEmploye').val().toLowerCase();
            
            // Réinitialiser la pagination
            currentPage = 1;
            
            // Filtrer les lignes
            filteredRows = [];
            $('#validationTable tbody tr').each(function() {
                let showRow = true;
                const row = $(this);
                
                // Filtrer par département
                if (selectedDepartements.length > 0 && selectedDepartements[0] !== '') {
                    const rowDept = row.data('departement');
                    if (!selectedDepartements.includes(rowDept)) {
                        showRow = false;
                    }
                }
                
                // Filtrer par domaine
                if (selectedDomaines.length > 0 && selectedDomaines[0] !== '') {
                    const rowDomaine = row.data('domaine');
                    if (!selectedDomaines.includes(rowDomaine)) {
                        showRow = false;
                    }
                }
                
                // Filtrer par compétence
                if (selectedCompetences.length > 0 && selectedCompetences[0] !== '') {
                    const rowCompetence = row.data('competence');
                    if (!selectedCompetences.includes(rowCompetence)) {
                        showRow = false;
                    }
                }
                
                // Filtrer par niveau
                if (selectedNiveau && selectedNiveau !== '') {
                    const rowNiveau = row.data('niveau');
                    if (rowNiveau != selectedNiveau) {
                        showRow = false;
                    }
                }
                
                // Filtrer par date
                if (dateStart && dateEnd) {
                    const rowDate = row.data('date');
                    if (rowDate < dateStart || rowDate > dateEnd) {
                        showRow = false;
                    }
                }
                
                // Filtrer par recherche employé
                if (employeSearch) {
                    const rowEmploye = row.data('employe');
                    if (!rowEmploye.includes(employeSearch)) {
                        showRow = false;
                    }
                }
                
                if (showRow) {
                    filteredRows.push(row);
                }
            });
            
            // Mettre à jour l'affichage
            updateTableDisplay();
            
            // Mettre à jour les filtres actifs
            updateActiveFilters(selectedDepartements, selectedDomaines, selectedCompetences, 
                              selectedNiveau, dateStart, dateEnd, employeSearch);
        }

        function resetFilters() {
            $('#filterDepartement').val(null).trigger('change');
            $('#filterDomaine').val(null).trigger('change');
            $('#filterCompetence').val(null).trigger('change');
            $('#filterNiveau').val('');
            $('#filterEmploye').val('');
            
            // Réinitialiser les dates
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(today.getDate() - 30);
            $('#filterDateStart').val(thirtyDaysAgo.toISOString().split('T')[0]);
            $('#filterDateEnd').val(today.toISOString().split('T')[0]);
            
            applyFilters();
        }

        function updateTableDisplay() {
            // Cacher toutes les lignes
            $('#validationTable tbody tr').hide();
            
            // Afficher les lignes de la page courante
            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            
            for (let i = startIndex; i < endIndex && i < filteredRows.length; i++) {
                filteredRows[i].show();
            }
            
            // Mettre à jour les compteurs
            $('#visibleCount').text(filteredRows.length);
            $('#currentPage').text(currentPage);
            
            // Gérer les boutons de pagination
            $('#prevPage').parent().toggleClass('disabled', currentPage === 1);
            $('#nextPage').parent().toggleClass('disabled', currentPage >= Math.ceil(filteredRows.length / rowsPerPage));
            
            // Mettre à jour le compteur d'attente
            updatePendingCount();
        }

        function updateActiveFilters(depts, domaines, competences, niveau, dateStart, dateEnd, employe) {
            const filterTags = $('#filterTags');
            filterTags.empty();
            
            let hasActiveFilters = false;
            
            // Ajouter les tags de filtre
            if (depts && depts.length > 0 && depts[0] !== '') {
                depts.forEach(dept => {
                    filterTags.append(`
                        <span class="badge bg-primary">
                            Département: ${dept}
                            <button type="button" class="btn-close btn-close-white btn-sm ms-1" 
                                    onclick="removeFilter('departement', '${dept}')"></button>
                        </span>
                    `);
                });
                hasActiveFilters = true;
            }
            
            if (domaines && domaines.length > 0 && domaines[0] !== '') {
                domaines.forEach(domaine => {
                    filterTags.append(`
                        <span class="badge bg-info">
                            Domaine: ${domaine}
                            <button type="button" class="btn-close btn-close-white btn-sm ms-1" 
                                    onclick="removeFilter('domaine', '${domaine}')"></button>
                        </span>
                    `);
                });
                hasActiveFilters = true;
            }
            
            if (employe) {
                filterTags.append(`
                    <span class="badge bg-success">
                        Employé: ${employe}
                        <button type="button" class="btn-close btn-close-white btn-sm ms-1" 
                                onclick="$('#filterEmploye').val(''); applyFilters();"></button>
                    </span>
                `);
                hasActiveFilters = true;
            }
            
            // Afficher/masquer la section des filtres actifs
            $('#activeFilters').toggle(hasActiveFilters);
        }

        // Fonction pour supprimer un filtre
        window.removeFilter = function(type, value) {
            switch(type) {
                case 'departement':
                    const currentDepts = $('#filterDepartement').val();
                    $('#filterDepartement').val(currentDepts.filter(v => v !== value)).trigger('change');
                    break;
                case 'domaine':
                    const currentDomaines = $('#filterDomaine').val();
                    $('#filterDomaine').val(currentDomaines.filter(v => v !== value)).trigger('change');
                    break;
            }
            applyFilters();
        };

        function exportData() {
            const filters = {
                departements: $('#filterDepartement').val(),
                domaines: $('#filterDomaine').val(),
                competences: $('#filterCompetence').val(),
                niveau: $('#filterNiveau').val(),
                dateStart: $('#filterDateStart').val(),
                dateEnd: $('#filterDateEnd').val(),
                employe: $('#filterEmploye').val()
            };
            
            // Construire l'URL d'export avec les filtres
            let exportUrl = '/api/validations/export?';
            const params = [];
            
            if (filters.departements && filters.departements.length > 0) {
                params.push(`departements=${encodeURIComponent(filters.departements.join(','))}`);
            }
            if (filters.domaines && filters.domaines.length > 0) {
                params.push(`domaines=${encodeURIComponent(filters.domaines.join(','))}`);
            }
            if (filters.dateStart) {
                params.push(`dateStart=${filters.dateStart}`);
            }
            if (filters.dateEnd) {
                params.push(`dateEnd=${filters.dateEnd}`);
            }
            
            exportUrl += params.join('&');
            
            // Télécharger le fichier
            window.location.href = exportUrl;
        }

        // Fonction de validation individuelle
        function validateEntry(entryId, niveauFinal, commentaire, action) {
            $.ajax({
                url: `/api/validations/${entryId}/${action === 'rejete' ? 'reject' : 'validate'}`,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    niveauFinal: niveauFinal,
                    commentaire: commentaire,
                    action: action
                }),
                success: function(response) {
                    if (response.success) {
                        $(`tr[data-entry-id="${entryId}"]`).fadeOut(400, function() {
                            $(this).remove();
                            updatePendingCount();
                            applyFilters(); // Re-filtrer après suppression
                        });
                        showToast('success', 'Validation enregistrée avec succès !');
                    } else {
                        showToast('error', 'Erreur: ' + response.error);
                    }
                },
                error: function() {
                    showToast('error', 'Erreur de communication avec le serveur.');
                }
            });
        }

        // Fonction de validation en masse
        function bulkValidate(entries) {
            $.ajax({
                url: '/api/validations/bulk-validate',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ entries: entries }),
                success: function(response) {
                    if (response.success) {
                        response.data.forEach(function(result) {
                            if (result.success) {
                                $(`tr[data-entry-id="${result.entryId}"]`).remove();
                            }
                        });
                        updatePendingCount();
                        applyFilters(); // Re-filtrer après suppression
                        showToast('success', `${entries.length} compétence(s) validée(s) avec succès !`);
                    } else {
                        showToast('error', 'Erreur: ' + response.error);
                    }
                },
                error: function() {
                    showToast('error', 'Erreur de communication avec le serveur.');
                }
            });
        }

        // Fonction d'ajout de compétence observée
        function addObservedCompetence(employeeId, competenceId, niveau, comment) {
            $.ajax({
                url: `/api/employees/${employeeId}/competences/manager`,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    id_competence: competenceId,
                    niveau: niveau,
                    commentaire: comment
                }),
                success: function(response) {
                    if (response.success) {
                        showToast('success', 'Compétence ajoutée avec succès !');
                        $('#addObservedForm')[0].reset();
                        $('.select2-employee').val(null).trigger('change');
                    } else {
                        showToast('error', 'Erreur: ' + response.error);
                    }
                },
                error: function() {
                    showToast('error', 'Erreur de communication avec le serveur.');
                }
            });
        }

        // Mettre à jour le compteur
        function updatePendingCount() {
            const count = filteredRows.length;
            $('#pendingCount').text(count);
        }

        // Fonction pour afficher les toasts
        function showToast(type, message) {
            // Créer un toast Bootstrap
            const toastHtml = `
                <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" 
                     role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            const toastContainer = $('#toastContainer');
            if (toastContainer.length === 0) {
                $('body').append('<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
            }
            
            $('#toastContainer').append(toastHtml);
            const toastElement = $('#toastContainer .toast:last-child');
            const toast = new bootstrap.Toast(toastElement[0]);
            toast.show();
            
            // Supprimer le toast après sa disparition
            toastElement.on('hidden.bs.toast', function () {
                $(this).remove();
            });
        }
    });
    </script>
<?php Flight::render('footer')?>