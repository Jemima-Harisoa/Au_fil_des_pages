<?php 
// Utiliser le même système d'en-tête que competences_admin.php
if (isset($_SESSION['infoAdmin'])) {
    Flight::render("headerA", ['extra_css' => $extra_css ?? '']);
} else if (isset($_SESSION['employe'])) {
    Flight::render("headerE", ['extra_css' => $extra_css ?? '']);
} else {
    Flight::render("headerU", ['extra_css' => $extra_css ?? '']);
}
?>

<style>
    /* Style pour les 5 niveaux */
    .badge-niveau-1 {
        background-color: #e74a3b;
        color: white;
    }
    .badge-niveau-2 {
        background-color: #f6c23e;
        color: #2c3e50;
    }
    .badge-niveau-3 {
        background-color: #4e73df;
        color: white;
    }
    .badge-niveau-4 {
        background-color: #1cc88a;
        color: white;
    }
    .badge-niveau-5 {
        background-color: #6f42c1;
        color: white;
    }
    
    /* Badge de niveau */
    .badge-niveau {
        padding: 5px 10px;
        border-radius: 15px;
        color: white;
        font-weight: bold;
        min-width: 80px;
        text-align: center;
        display: inline-block;
    }
    
    /* Compteur en attente */
    .pending-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #e74a3b;
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
    }
    
    /* Filtres */
    .filter-card {
        border-left: 4px solid #4e73df;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    /* Table responsive */
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    
    /* Boutons d'action */
    .btn-action {
        padding: 5px 8px;
        font-size: 0.85rem;
    }
    
    /* Hover sur les lignes */
    .table-hover tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.05);
        cursor: pointer;
    }
    
    /* Styles pour les filtres rapides */
    .filter-badge {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .filter-badge:hover {
        transform: scale(1.05);
        opacity: 0.9;
    }
    
    /* Sélection multiple */
    .select-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    /* Type de compétence */
    .type-competence {
        font-size: 0.8rem;
        padding: 2px 8px;
        border-radius: 10px;
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        color: #6e707e;
    }
    
    /* Tooltip pour description */
    .description-tooltip {
        cursor: help;
        border-bottom: 1px dotted #6e707e;
    }
    
    /* Styles pour la pagination personnalisée */
    .pagination-custom {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    
    .page-item {
        margin: 0 2px;
    }
    
    .page-link {
        color: #4e73df;
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
        color: white;
    }
    
    .page-item.disabled .page-link {
        color: #6c757d;
        cursor: not-allowed;
    }
    
    /* Tri des colonnes */
    .sortable {
        cursor: pointer;
        position: relative;
        padding-right: 20px !important;
    }
    
    .sortable::after {
        content: "↕";
        position: absolute;
        right: 8px;
        color: #6c757d;
        font-size: 12px;
    }
    
    .sortable.asc::after {
        content: "↑";
        color: #4e73df;
    }
    
    .sortable.desc::after {
        content: "↓";
        color: #4e73df;
    }
    
    /* Loading overlay */
    .loading-overlay {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<!-- Begin Page Content -->
<div class="container-fluid">
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clipboard-check text-primary mr-2"></i>
            Validation Managériale
        </h1>
        <div class="btn-group">
            <button class="btn btn-sm btn-primary" onclick="exporterValidations('csv')">
                <i class="fas fa-file-csv"></i> Exporter CSV
            </button>
            <button class="btn btn-sm btn-success" onclick="exporterValidations('pdf')">
                <i class="fas fa-file-pdf"></i> Exporter PDF
            </button>
        </div>
    </div>

    <!-- Filtres rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card filter-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="searchFilter">Recherche</label>
                                <input type="text" class="form-control" id="searchFilter" 
                                       placeholder="Employé, compétence, département..."
                                       oninput="appliquerFiltres()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="departementFilter">Département</label>
                                <select class="form-control" id="departementFilter" onchange="appliquerFiltres()">
                                    <option value="">Tous</option>
                                    <?php
                                    $departements = array_unique(array_column($pendingValidations, 'departement_nom'));
                                    foreach ($departements as $dept):
                                        if (!empty($dept)):
                                    ?>
                                    <option value="<?= htmlspecialchars($dept) ?>">
                                        <?= htmlspecialchars($dept) ?>
                                    </option>
                                    <?php endif; endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="typeFilter">Type de compétence</label>
                                <select class="form-control" id="typeFilter" onchange="appliquerFiltres()">
                                    <option value="">Tous</option>
                                    <?php
                                    $types = array_unique(array_column($pendingValidations, 'type_competence'));
                                    foreach ($types as $type):
                                        if (!empty($type)):
                                    ?>
                                    <option value="<?= htmlspecialchars($type) ?>">
                                        <?= htmlspecialchars($type) ?>
                                    </option>
                                    <?php endif; endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="niveauFilter">Niveau min</label>
                                <select class="form-control" id="niveauFilter" onchange="appliquerFiltres()">
                                    <option value="">Tous</option>
                                    <option value="1">Niveau 1+</option>
                                    <option value="2">Niveau 2+</option>
                                    <option value="3">Niveau 3+</option>
                                    <option value="4">Niveau 4+</option>
                                    <option value="5">Niveau 5</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="dateFilter">Période</label>
                                <select class="form-control" id="dateFilter" onchange="appliquerFiltres()">
                                    <option value="">Toutes</option>
                                    <option value="7">7 derniers jours</option>
                                    <option value="30">30 derniers jours</option>
                                    <option value="90">3 derniers mois</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button class="btn btn-outline-secondary w-100" onclick="reinitialiserFiltres()" title="Réinitialiser les filtres">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Filtres rapides par badges -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <small class="text-muted">Filtres rapides :</small>
                            <span class="badge badge-primary filter-badge mx-1" onclick="filtrerParNiveau(1)">Niveau 1</span>
                            <span class="badge badge-warning filter-badge mx-1" onclick="filtrerParNiveau(2)">Niveau 2</span>
                            <span class="badge badge-info filter-badge mx-1" onclick="filtrerParNiveau(3)">Niveau 3</span>
                            <span class="badge badge-success filter-badge mx-1" onclick="filtrerParNiveau(4)">Niveau 4</span>
                            <span class="badge badge-dark filter-badge mx-1" onclick="filtrerParNiveau(5)">Niveau 5</span>
                            <button class="btn btn-sm btn-outline-secondary float-right" onclick="reinitialiserFiltres()">
                                <i class="fas fa-redo mr-1"></i> Réinitialiser tout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clock mr-2"></i>
                Auto-évaluations en attente de validation
                <span class="badge badge-light ml-2" id="resultsCount"><?php echo count($pendingValidations); ?> résultat(s)</span>
            </h6>
            <div class="position-relative">
                <button class="btn btn-success" id="btnBulkValidate">
                    <i class="fas fa-check-double mr-1"></i>Valider la sélection
                </button>
                <span class="pending-count" id="pendingCount"><?php echo count($pendingValidations); ?></span>
            </div>
        </div>
        <div class="card-body position-relative">
            <!-- Loading overlay -->
            <div class="loading-overlay" id="loadingOverlay">
                <div class="loading-spinner"></div>
            </div>
            
            <?php if (empty($pendingValidations)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Aucune auto-évaluation en attente de validation.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="validationTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll" class="select-checkbox">
                                </th>
                                <th class="sortable" onclick="sortTable(1)">Employé</th>
                                <th class="sortable" onclick="sortTable(2)">Département</th>
                                <th class="sortable" onclick="sortTable(3)">Compétence</th>
                                <th class="sortable" onclick="sortTable(4)">Type</th>
                                <th class="sortable" onclick="sortTable(5)">Niveau auto-évalué</th>
                                <th class="sortable" onclick="sortTable(6)">Date</th>
                                <th>Niveau validé</th>
                                <th>Commentaire</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php foreach ($pendingValidations as $validation): 
                                // Déterminer la classe CSS du badge en fonction du niveau
                                $niveau = (int)$validation['niveau_auto'];
                                $niveauClass = 'badge-niveau-' . min(5, max(1, $niveau));
                                
                                // Formater la date
                                $dateFormatted = date('d/m/Y', strtotime($validation['date_mesure']));
                                $timeFormatted = date('H:i', strtotime($validation['date_mesure']));
                                $timestamp = strtotime($validation['date_mesure']);
                                
                                // Information de validation
                                $valide = $validation['valide'] ?? 0;
                                $hasValidator = !empty($validation['id_employe_validateur']);
                            ?>
                                <tr data-entry-id="<?= $validation['entry_id'] ?>"
                                    data-departement="<?= htmlspecialchars($validation['departement_nom'] ?? '') ?>"
                                    data-type="<?= htmlspecialchars($validation['type_competence'] ?? '') ?>"
                                    data-niveau="<?= $niveau ?>"
                                    data-date="<?= $timestamp ?>"
                                    data-employe="<?= htmlspecialchars($validation['employe_nom'] . ' ' . $validation['employe_prenom']) ?>"
                                    data-competence="<?= htmlspecialchars($validation['competence_nom']) ?>"
                                    data-poste="<?= htmlspecialchars($validation['employe_poste']) ?>"
                                    data-domaine="<?= htmlspecialchars($validation['domaine']) ?>"
                                    data-valide="<?= $valide ?>">
                                    <td>
                                        <input type="checkbox" class="select-checkbox select-entry" 
                                               data-entry-id="<?= $validation['entry_id'] ?>">
                                    </td>
                                    <td data-sort="<?= htmlspecialchars(strtolower($validation['employe_nom'] . ' ' . $validation['employe_prenom'])) ?>">
                                        <strong><?= htmlspecialchars($validation['employe_nom'] . ' ' . $validation['employe_prenom']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($validation['employe_poste']) ?></small>
                                    </td>
                                    <td data-sort="<?= htmlspecialchars(strtolower($validation['departement_nom'] ?? '')) ?>">
                                        <?php if (!empty($validation['departement_nom'])): ?>
                                            <span class="badge badge-light">
                                                <?= htmlspecialchars($validation['departement_nom']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Non spécifié</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-sort="<?= htmlspecialchars(strtolower($validation['competence_nom'])) ?>">
                                        <strong class="description-tooltip" 
                                                title="<?= htmlspecialchars($validation['competence_description'] ?? 'Aucune description') ?>">
                                            <?= htmlspecialchars($validation['competence_nom']) ?>
                                        </strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($validation['domaine']) ?></small>
                                    </td>
                                    <td data-sort="<?= htmlspecialchars(strtolower($validation['type_competence'] ?? '')) ?>">
                                        <?php if (!empty($validation['type_competence'])): ?>
                                            <span class="type-competence">
                                                <?= htmlspecialchars($validation['type_competence']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-sort="<?= $niveau ?>">
                                        <span class="badge badge-niveau <?= $niveauClass ?>" 
                                              style="background-color: <?= $validation['niveau_couleur'] ?? '#6e707e' ?>">
                                            Niveau <?= $niveau ?> - <?= htmlspecialchars($validation['niveau_libelle']) ?>
                                        </span>
                                    </td>
                                    <td data-sort="<?= $timestamp ?>">
                                        <?= $dateFormatted ?><br>
                                        <small class="text-muted"><?= $timeFormatted ?></small>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm niveau-select" 
                                                data-entry-id="<?= $validation['entry_id'] ?>">
                                            <?php for ($i = 1; $i <= 5; $i++): 
                                                $optionClass = 'badge-niveau-' . $i;
                                            ?>
                                                <option value="<?= $i ?>" 
                                                        <?= $i == $niveau ? 'selected' : '' ?>
                                                        class="<?= $optionClass ?>">
                                                    Niveau <?= $i ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm commentaire-input" 
                                                  data-entry-id="<?= $validation['entry_id'] ?>"
                                                  rows="1" placeholder="Commentaire..."></textarea>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-success btn-action btn-validate" 
                                                    data-action="valide"
                                                    data-entry-id="<?= $validation['entry_id'] ?>"
                                                    title="Valider">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-warning btn-action btn-validate" 
                                                    data-action="ajuste"
                                                    data-entry-id="<?= $validation['entry_id'] ?>"
                                                    title="Ajuster">
                                                <i class="fas fa-adjust"></i>
                                            </button>
                                            <button class="btn btn-danger btn-action btn-reject" 
                                                    data-entry-id="<?= $validation['entry_id'] ?>"
                                                    title="Rejeter">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <?php if ($hasValidator): ?>
                                            <small class="d-block mt-1 text-muted">
                                                Validé par: <?= htmlspecialchars($validation['validateur_nom'] . ' ' . $validation['validateur_prenom']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Page navigation" id="paginationContainer">
                    <ul class="pagination pagination-custom" id="pagination">
                        <!-- La pagination sera générée par JavaScript -->
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- Formulaire d'ajout de compétence observée -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-plus-circle mr-2"></i>
                Ajouter une compétence observée
            </h6>
        </div>
        <div class="card-body">
            <form id="addObservedForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Employé</label>
                            <select class="form-control" id="observedEmployee" required>
                                <option value="">Sélectionner un employé...</option>
                                <?php foreach ($employes as $employe): ?>
                                    <option value="<?= $employe['id_employe'] ?>">
                                        <?= htmlspecialchars($employe['nom_personne'] . ' ' . $employe['prenom']) ?>
                                        - <?= htmlspecialchars($employe['poste']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Compétence</label>
                            <select class="form-control" id="observedCompetence" required>
                                <option value="">Sélectionner une compétence...</option>
                                <?php foreach ($competences as $competence): ?>
                                    <option value="<?= $competence['id_competence'] ?>"
                                            data-domaine="<?= htmlspecialchars($competence['domaine']) ?>"
                                            data-type="<?= htmlspecialchars($competence['type_competence'] ?? '') ?>">
                                        <?= htmlspecialchars($competence['nom']) ?>
                                        <?php if ($competence['domaine']): ?>
                                            (<?= $competence['domaine'] ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" id="observedType">
                                <option value="">Type...</option>
                                <?php
                                $types = array_unique(array_column($competences, 'type_competence'));
                                foreach ($types as $type):
                                    if (!empty($type)):
                                ?>
                                <option value="<?= htmlspecialchars($type) ?>">
                                    <?= htmlspecialchars($type) ?>
                                </option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Niveau</label>
                            <select class="form-control" id="observedNiveau" required>
                                <option value="">Niveau...</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?= $i ?>">
                                        Niveau <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100" title="Ajouter la compétence">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-11">
                        <div class="form-group">
                            <label>Commentaire d'observation</label>
                            <textarea class="form-control" id="observedComment" 
                                      rows="2" placeholder="Décrire le contexte d'observation, exemple précis, justification du niveau..."></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<script>
// Variables globales
let allRows = [];
let currentRows = [];
let currentPage = 1;
let rowsPerPage = 10;
let sortColumn = 6; // Colonne date par défaut
let sortDirection = 'desc'; // Descendant par défaut

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer toutes les lignes du tableau
    const tableRows = document.querySelectorAll('#validationTable tbody tr');
    allRows = Array.from(tableRows);
    currentRows = [...allRows];
    
    // Initialiser les compteurs
    updateCounters();
    
    // Appliquer le tri initial
    sortTable(sortColumn);
    
    // Gestion de la sélection "Tout sélectionner"
    document.getElementById('selectAll').addEventListener('click', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.select-entry').forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        updateSelectionCount();
    });
    
    // Mettre à jour la case "Tout sélectionner" quand on coche/décoche individuellement
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('select-entry')) {
            const total = document.querySelectorAll('.select-entry').length;
            const checked = document.querySelectorAll('.select-entry:checked').length;
            document.getElementById('selectAll').checked = checked === total && total > 0;
            updateSelectionCount();
        }
    });
    
    // Validation individuelle avec SweetAlert
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-validate')) {
            const button = e.target.closest('.btn-validate');
            const entryId = button.dataset.entryId;
            const action = button.dataset.action;
            const actionText = action === 'valide' ? 'Valider' : 'Ajuster';
            const niveauSelect = document.querySelector(`select[data-entry-id="${entryId}"]`);
            const niveauFinal = niveauSelect ? niveauSelect.value : 1;
            const commentaireInput = document.querySelector(`textarea[data-entry-id="${entryId}"]`);
            const commentaire = commentaireInput ? commentaireInput.value : '';
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: `${actionText} cette évaluation ?`,
                    text: `Cette action enregistrera le niveau ${niveauFinal} pour cette compétence.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: action === 'valide' ? '#1cc88a' : '#f6c23e',
                    cancelButtonColor: '#6e707e',
                    confirmButtonText: `Oui, ${actionText}`,
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        validateEntry(entryId, niveauFinal, commentaire, action);
                    }
                });
            } else {
                if (confirm(`${actionText} cette évaluation ?`)) {
                    validateEntry(entryId, niveauFinal, commentaire, action);
                }
            }
        }
        
        // Rejet d'une compétence
        if (e.target.closest('.btn-reject')) {
            const button = e.target.closest('.btn-reject');
            const entryId = button.dataset.entryId;
            const commentaireInput = document.querySelector(`textarea[data-entry-id="${entryId}"]`);
            const commentaire = commentaireInput ? commentaireInput.value : '';
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Rejeter cette auto-évaluation ?',
                    text: "L'employé devra refaire son auto-évaluation.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#6e707e',
                    confirmButtonText: 'Oui, rejeter',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        validateEntry(entryId, 0, commentaire, 'rejete');
                    }
                });
            } else {
                if (confirm('Rejeter cette auto-évaluation ?')) {
                    validateEntry(entryId, 0, commentaire, 'rejete');
                }
            }
        }
    });
    
    // Validation en masse
    document.getElementById('btnBulkValidate').addEventListener('click', function() {
        const selectedEntries = [];
        
        document.querySelectorAll('.select-entry:checked').forEach(checkbox => {
            if (checkbox.id !== 'selectAll') {
                const entryId = checkbox.dataset.entryId;
                const niveauSelect = document.querySelector(`select[data-entry-id="${entryId}"]`);
                const niveauFinal = niveauSelect ? niveauSelect.value : 1;
                const commentaireInput = document.querySelector(`textarea[data-entry-id="${entryId}"]`);
                const commentaire = commentaireInput ? commentaireInput.value : '';
                
                selectedEntries.push({
                    entryId: entryId,
                    niveauFinal: niveauFinal,
                    commentaire: commentaire,
                    action: 'valide'
                });
            }
        });
        
        if (selectedEntries.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aucune sélection',
                    text: 'Veuillez sélectionner au moins une compétence à valider.',
                    confirmButtonColor: '#f6c23e'
                });
            } else {
                alert('Veuillez sélectionner au moins une compétence à valider.');
            }
            return;
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: `Valider ${selectedEntries.length} compétence(s) ?`,
                html: `Vous êtes sur le point de valider <strong>${selectedEntries.length}</strong> auto-évaluation(s).`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1cc88a',
                cancelButtonColor: '#6e707e',
                confirmButtonText: 'Valider tout',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    bulkValidate(selectedEntries);
                }
            });
        } else {
            if (confirm(`Valider ${selectedEntries.length} compétence(s) ?`)) {
                bulkValidate(selectedEntries);
            }
        }
    });
    
    // Ajout d'une compétence observée
    document.getElementById('addObservedForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const employeeId = document.getElementById('observedEmployee').value;
        const competenceId = document.getElementById('observedCompetence').value;
        const niveau = document.getElementById('observedNiveau').value;
        const comment = document.getElementById('observedComment').value;
        
        if (!employeeId || !competenceId || !niveau) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Champs manquants',
                    text: 'Veuillez remplir tous les champs obligatoires.',
                    confirmButtonColor: '#e74a3b'
                });
            } else {
                alert('Veuillez remplir tous les champs obligatoires.');
            }
            return;
        }
        
        addObservedCompetence(employeeId, competenceId, niveau, comment);
    });
    
    // Mettre à jour le type de compétence quand on change la compétence
    document.getElementById('observedCompetence').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const type = selectedOption.dataset.type;
        
        if (type) {
            document.getElementById('observedType').value = type;
        }
    });
    
    // Activer les tooltips pour les descriptions
    document.querySelectorAll('.description-tooltip').forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            const title = this.getAttribute('title');
            if (title) {
                // Créer un tooltip personnalisé
                const tooltip = document.createElement('div');
                tooltip.className = 'custom-tooltip';
                tooltip.textContent = title;
                tooltip.style.position = 'absolute';
                tooltip.style.backgroundColor = '#333';
                tooltip.style.color = '#fff';
                tooltip.style.padding = '5px 10px';
                tooltip.style.borderRadius = '4px';
                tooltip.style.zIndex = '1000';
                tooltip.style.maxWidth = '300px';
                tooltip.style.whiteSpace = 'normal';
                
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.left = (rect.left + window.scrollX) + 'px';
                tooltip.style.top = (rect.top + window.scrollY - tooltip.offsetHeight - 10) + 'px';
                
                this._tooltip = tooltip;
            }
        });
        
        element.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                this._tooltip.remove();
                this._tooltip = null;
            }
        });
    });
});

// Fonction pour appliquer les filtres
function appliquerFiltres() {
    showLoading(true);
    
    // Récupérer les valeurs des filtres
    const searchValue = document.getElementById('searchFilter').value.toLowerCase();
    const deptValue = document.getElementById('departementFilter').value;
    const typeValue = document.getElementById('typeFilter').value;
    const niveauValue = document.getElementById('niveauFilter').value;
    const dateValue = document.getElementById('dateFilter').value;
    
    // Filtrer les lignes
    currentRows = allRows.filter(row => {
        let showRow = true;
        
        // Récupérer les données de la ligne
        const employe = row.dataset.employe.toLowerCase();
        const poste = row.dataset.poste.toLowerCase();
        const departement = row.dataset.departement;
        const type = row.dataset.type;
        const niveau = parseInt(row.dataset.niveau);
        const dateTimestamp = parseInt(row.dataset.date);
        const competence = row.dataset.competence.toLowerCase();
        const domaine = row.dataset.domaine.toLowerCase();
        
        // Filtre de recherche globale
        if (searchValue) {
            const searchText = searchValue.toLowerCase();
            if (!employe.includes(searchText) && 
                !poste.includes(searchText) && 
                !departement.toLowerCase().includes(searchText) && 
                !competence.includes(searchText) && 
                !domaine.includes(searchText) && 
                !type.toLowerCase().includes(searchText)) {
                showRow = false;
            }
        }
        
        // Filtre par département
        if (deptValue && departement !== deptValue) {
            showRow = false;
        }
        
        // Filtre par type
        if (typeValue && type !== typeValue) {
            showRow = false;
        }
        
        // Filtre par niveau minimum
        if (niveauValue && niveau < parseInt(niveauValue)) {
            showRow = false;
        }
        
        // Filtre par date
        if (dateValue) {
            const dateLimite = new Date();
            dateLimite.setDate(dateLimite.getDate() - parseInt(dateValue));
            const dateRow = new Date(dateTimestamp * 1000);
            if (dateRow < dateLimite) {
                showRow = false;
            }
        }
        
        return showRow;
    });
    
    // Réinitialiser à la première page
    currentPage = 1;
    
    // Réappliquer le tri
    sortTable(sortColumn, true);
    
    // Mettre à jour l'affichage
    updateTableDisplay();
    updateCounters();
    
    showLoading(false);
}

// Fonction pour trier le tableau
function sortTable(columnIndex, keepDirection = false) {
    // Mettre à jour les classes de tri
    const headers = document.querySelectorAll('#validationTable th.sortable');
    headers.forEach((header, index) => {
        header.classList.remove('asc', 'desc');
        if (index === columnIndex - 1) { // -1 car la première colonne est les checkbox
            if (!keepDirection && sortColumn === columnIndex) {
                // Inverser la direction si on clique sur la même colonne
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else if (!keepDirection) {
                sortDirection = 'asc';
            }
            header.classList.add(sortDirection);
        }
    });
    
    sortColumn = columnIndex;
    
    // Trier les lignes
    currentRows.sort((a, b) => {
        let aValue, bValue;
        
        // Récupérer la valeur de tri
        if (columnIndex === 1) { // Employé
            aValue = a.cells[columnIndex].dataset.sort || a.cells[columnIndex].textContent.toLowerCase();
            bValue = b.cells[columnIndex].dataset.sort || b.cells[columnIndex].textContent.toLowerCase();
        } else if (columnIndex === 2) { // Département
            aValue = a.cells[columnIndex].dataset.sort || a.cells[columnIndex].textContent.toLowerCase();
            bValue = b.cells[columnIndex].dataset.sort || b.cells[columnIndex].textContent.toLowerCase();
        } else if (columnIndex === 3) { // Compétence
            aValue = a.cells[columnIndex].dataset.sort || a.cells[columnIndex].textContent.toLowerCase();
            bValue = b.cells[columnIndex].dataset.sort || b.cells[columnIndex].textContent.toLowerCase();
        } else if (columnIndex === 4) { // Type
            aValue = a.cells[columnIndex].dataset.sort || a.cells[columnIndex].textContent.toLowerCase();
            bValue = b.cells[columnIndex].dataset.sort || b.cells[columnIndex].textContent.toLowerCase();
        } else if (columnIndex === 5) { // Niveau
            aValue = parseInt(a.dataset.niveau);
            bValue = parseInt(b.dataset.niveau);
        } else if (columnIndex === 6) { // Date
            aValue = parseInt(a.dataset.date);
            bValue = parseInt(b.dataset.date);
        } else {
            aValue = a.cells[columnIndex].textContent.toLowerCase();
            bValue = b.cells[columnIndex].textContent.toLowerCase();
        }
        
        // Comparaison
        if (typeof aValue === 'number' && typeof bValue === 'number') {
            return sortDirection === 'asc' ? aValue - bValue : bValue - aValue;
        } else {
            if (aValue < bValue) return sortDirection === 'asc' ? -1 : 1;
            if (aValue > bValue) return sortDirection === 'asc' ? 1 : -1;
            return 0;
        }
    });
    
    // Mettre à jour l'affichage
    updateTableDisplay();
}

// Fonction pour mettre à jour l'affichage du tableau avec pagination
function updateTableDisplay() {
    const tbody = document.getElementById('tableBody');
    const paginationContainer = document.getElementById('paginationContainer');
    
    if (!tbody) return;
    
    // Vider le tbody
    tbody.innerHTML = '';
    
    // Calculer les indices de pagination
    const totalRows = currentRows.length;
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = Math.min(startIndex + rowsPerPage, totalRows);
    
    // Ajouter les lignes de la page courante
    for (let i = startIndex; i < endIndex; i++) {
        tbody.appendChild(currentRows[i]);
    }
    
    // Mettre à jour la pagination
    updatePagination(totalPages);
    
    // Afficher/masquer la pagination
    if (paginationContainer) {
        paginationContainer.style.display = totalPages > 1 ? 'block' : 'none';
    }
}

// Fonction pour mettre à jour la pagination
function updatePagination(totalPages) {
    const pagination = document.getElementById('pagination');
    if (!pagination) return;
    
    pagination.innerHTML = '';
    
    if (totalPages <= 1) return;
    
    // Bouton Précédent
    const prevItem = document.createElement('li');
    prevItem.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevItem.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Précédent</a>`;
    pagination.appendChild(prevItem);
    
    // Pages
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    // Première page
    if (startPage > 1) {
        const firstItem = document.createElement('li');
        firstItem.className = 'page-item';
        firstItem.innerHTML = `<a class="page-link" href="#" onclick="changePage(1)">1</a>`;
        pagination.appendChild(firstItem);
        
        if (startPage > 2) {
            const ellipsisItem = document.createElement('li');
            ellipsisItem.className = 'page-item disabled';
            ellipsisItem.innerHTML = '<span class="page-link">...</span>';
            pagination.appendChild(ellipsisItem);
        }
    }
    
    // Pages numérotées
    for (let i = startPage; i <= endPage; i++) {
        const pageItem = document.createElement('li');
        pageItem.className = `page-item ${i === currentPage ? 'active' : ''}`;
        pageItem.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
        pagination.appendChild(pageItem);
    }
    
    // Dernière page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            const ellipsisItem = document.createElement('li');
            ellipsisItem.className = 'page-item disabled';
            ellipsisItem.innerHTML = '<span class="page-link">...</span>';
            pagination.appendChild(ellipsisItem);
        }
        
        const lastItem = document.createElement('li');
        lastItem.className = 'page-item';
        lastItem.innerHTML = `<a class="page-link" href="#" onclick="changePage(${totalPages})">${totalPages}</a>`;
        pagination.appendChild(lastItem);
    }
    
    // Bouton Suivant
    const nextItem = document.createElement('li');
    nextItem.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    nextItem.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Suivant</a>`;
    pagination.appendChild(nextItem);
}

// Fonction pour changer de page
function changePage(page) {
    const totalPages = Math.ceil(currentRows.length / rowsPerPage);
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    updateTableDisplay();
    
    // Scroll vers le haut du tableau
    const table = document.getElementById('validationTable');
    if (table) {
        table.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Fonction pour réinitialiser les filtres
function reinitialiserFiltres() {
    document.getElementById('searchFilter').value = '';
    document.getElementById('departementFilter').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('niveauFilter').value = '';
    document.getElementById('dateFilter').value = '';
    
    appliquerFiltres();
}

// Fonction pour filtrer par niveau (badges)
function filtrerParNiveau(niveau) {
    document.getElementById('niveauFilter').value = niveau;
    appliquerFiltres();
}

// Fonction pour mettre à jour les compteurs
function updateCounters() {
    const resultsCount = document.getElementById('resultsCount');
    const pendingCount = document.getElementById('pendingCount');
    
    if (resultsCount) {
        resultsCount.textContent = `${currentRows.length} résultat(s)`;
    }
    
    if (pendingCount) {
        pendingCount.textContent = currentRows.length;
    }
}

// Fonction pour mettre à jour le compteur de sélection
function updateSelectionCount() {
    const selectedCount = document.querySelectorAll('.select-entry:checked').length;
    const btnBulkValidate = document.getElementById('btnBulkValidate');
    
    if (btnBulkValidate) {
        if (selectedCount > 0) {
            btnBulkValidate.innerHTML = `<i class="fas fa-check-double mr-1"></i>Valider (${selectedCount})`;
        } else {
            btnBulkValidate.innerHTML = '<i class="fas fa-check-double mr-1"></i>Valider la sélection';
        }
    }
}

// Fonction pour afficher/masquer le loading
function showLoading(show) {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = show ? 'flex' : 'none';
    }
}

// Les fonctions suivantes restent inchangées car elles utilisent AJAX
// (validateEntry, bulkValidate, addObservedCompetence, exporterValidations)

// Fonction de validation individuelle
function validateEntry(entryId, niveauFinal, commentaire, action) {
    showLoading(true);
    
    // Simuler une requête AJAX (à remplacer par votre vraie requête)
    setTimeout(() => {
        showLoading(false);
        
        // Supprimer la ligne du tableau
        const row = document.querySelector(`tr[data-entry-id="${entryId}"]`);
        if (row) {
            // Retirer de toutes les listes
            allRows = allRows.filter(r => r !== row);
            currentRows = currentRows.filter(r => r !== row);
            
            // Mettre à jour l'affichage
            updateTableDisplay();
            updateCounters();
        }
        
        // Afficher un message de succès
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Succès !',
                text: 'Validation enregistrée avec succès.',
                confirmButtonColor: '#1cc88a',
                timer: 1500
            });
        } else {
            alert('Validation enregistrée avec succès.');
        }
    }, 500);
}

// Fonction de validation en masse
function bulkValidate(entries) {
    showLoading(true);
    
    // Simuler une requête AJAX (à remplacer par votre vraie requête)
    setTimeout(() => {
        showLoading(false);
        
        // Supprimer les lignes sélectionnées
        entries.forEach(entry => {
            const row = document.querySelector(`tr[data-entry-id="${entry.entryId}"]`);
            if (row) {
                allRows = allRows.filter(r => r !== row);
                currentRows = currentRows.filter(r => r !== row);
            }
        });
        
        // Mettre à jour l'affichage
        updateTableDisplay();
        updateCounters();
        
        // Afficher un message de succès
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Validation terminée !',
                html: `<strong>${entries.length}/${entries.length}</strong> compétence(s) validée(s) avec succès.`,
                confirmButtonColor: '#1cc88a'
            });
        } else {
            alert(`Validation terminée ! ${entries.length} compétence(s) validée(s) avec succès.`);
        }
    }, 800);
}

// Fonction d'ajout de compétence observée
function addObservedCompetence(employeeId, competenceId, niveau, comment) {
    showLoading(true);
    
    // Simuler une requête AJAX (à remplacer par votre vraie requête)
    setTimeout(() => {
        showLoading(false);
        
        // Réinitialiser le formulaire
        document.getElementById('addObservedForm').reset();
        document.getElementById('observedType').value = '';
        
        // Afficher un message de succès
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Succès !',
                text: 'Compétence ajoutée avec succès.',
                confirmButtonColor: '#1cc88a',
                timer: 1500
            });
        } else {
            alert('Compétence ajoutée avec succès.');
        }
    }, 500);
}

// Fonction pour exporter les validations
function exporterValidations(format) {
    // Récupérer les filtres actuels
    const params = {
        search: document.getElementById('searchFilter').value,
        departement: document.getElementById('departementFilter').value,
        type: document.getElementById('typeFilter').value,
        niveau: document.getElementById('niveauFilter').value,
        periode: document.getElementById('dateFilter').value
    };
    
    // Construire l'URL d'export avec les paramètres
    let queryString = Object.keys(params)
        .filter(key => params[key])
        .map(key => key + '=' + encodeURIComponent(params[key]))
        .join('&');
    
    // Afficher un message de chargement
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Export en cours...',
            text: 'Préparation du fichier ' + format.toUpperCase(),
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
    }
    
    // Simuler l'export (à remplacer par votre vraie logique)
    setTimeout(() => {
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
        
        // Créer un lien de téléchargement
        const data = "Employé,Département,Compétence,Type,Niveau,Date\n" +
                    currentRows.map(row => {
                        const cells = row.cells;
                        return `"${cells[1].textContent}","${cells[2].textContent}","${cells[3].textContent}","${cells[4].textContent}","${cells[5].textContent}","${cells[6].textContent}"`;
                    }).join('\n');
        
        const blob = new Blob([data], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `validations_${new Date().toISOString().slice(0,10)}.${format}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }, 1500);
}
</script>

<?php
Flight::render("footer", ['extra_js' => '
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
']);