<?php
// Styles CSS spécifiques à la liste des employés
$extra_css = '
<style>
    .table-employes {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .table-employes th {
        background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
        color: white;
        border: none;
        padding: 15px 12px;
        font-weight: 600;
    }
    
    .table-employes td {
        padding: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .badge-conge {
        background-color: #1cc88a;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .badge-absence-autorisee {
        background-color: #36b9cc;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .badge-absence-non-autorisee {
        background-color: #e74a3b;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .btn-cv {
        background-color: #4e73df;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.8rem;
        transition: all 0.3s;
    }
    
    .btn-cv:hover {
        background-color: #2e59d9;
        color: white;
        text-decoration: none;
    }
    
    .btn-cv-disabled {
        background-color: #858796;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.8rem;
        cursor: not-allowed;
    }
    
    .employee-id {
        font-weight: 600;
        color: #4e73df;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
        transform: translateY(-1px);
        transition: all 0.3s;
    }
    
    .stats-summary {
        background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .stat-item {
        text-align: center;
        padding: 10px;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #4e73df;
    }
    
    .stat-label {
        font-size: 0.9rem;
        color: #858796;
    }
</style>
';

// Scripts JavaScript spécifiques
$extra_js = '
<script>
    $(document).ready(function() {
        // Initialisation de DataTable
        var table = $("#employesTable").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
            },
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
            "order": [[0, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": [4, 5, 6, 7] } // Désactiver le tri sur les colonnes d\'actions et badges
            ]
        });
        
        // Filtre par poste
        $("#posteFilter").on("change", function() {
            table.column(3).search(this.value).draw();
        });
        
        // Filtre par congés restants
        $("#congeFilter").on("change", function() {
            var value = this.value;
            if (value === "0") {
                table.column(5).search("^0$", true, false).draw();
            } else if (value === "1-10") {
                table.column(5).search("^[1-9]$|^10$", true, false).draw();
            } else if (value === "11+") {
                table.column(5).search("^1[1-9]|[2-9][0-9]", true, false).draw();
            } else {
                table.column(5).search("").draw();
            }
        });
        
        // Réinitialisation des filtres
        $("#resetFilters").on("click", function() {
            $("#posteFilter").val("");
            $("#congeFilter").val("");
            table.search("").columns().search("").draw();
        });
        
        // Redirection vers la fiche employé
        $(".btn-fiche").on("click", function() {
            var idEmploye = $(this).data("id");
            window.location.href = "/conge/fiche/" + idEmploye;
        });
    });
</script>
';

// Inclure le header avec les styles supplémentaires
Flight::render("headerA", ['extra_css' => $extra_css]);
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Liste des Employés</h1>
        <a href="/conge/fiche/1" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-user fa-sm text-white-50 mr-2"></i>
            Voir exemple fiche employé
        </a>
    </div>

    <!-- Statistiques Résumé -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="stats-summary">
                <div class="row text-center">
                    <div class="col-md-3 stat-item">
                        <div class="stat-number"><?= count($employes) ?></div>
                        <div class="stat-label">Employés total</div>
                    </div>
                    <div class="col-md-3 stat-item">
                        <div class="stat-number">
                            <?= array_sum(array_column($employes, 'conges_restants')) ?>
                        </div>
                        <div class="stat-label">Congés restants totaux</div>
                    </div>
                    <div class="col-md-3 stat-item">
                        <div class="stat-number">
                            <?= array_sum(array_column($employes, 'absences_autorisees')) ?>
                        </div>
                        <div class="stat-label">Absences autorisées</div>
                    </div>
                    <div class="col-md-3 stat-item">
                        <div class="stat-number">
                            <?= array_sum(array_column($employes, 'absences_non_autorisees')) ?>
                        </div>
                        <div class="stat-label">Absences non autorisées</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="posteFilter">Filtrer par poste:</label>
                        <select class="form-control" id="posteFilter">
                            <option value="">Tous les postes</option>
                            <?php
                            $postes = array_unique(array_column($employes, 'poste'));
                            foreach ($postes as $poste) {
                                if (!empty($poste)) {
                                    echo "<option value=\"$poste\">$poste</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="congeFilter">Filtrer par congés restants:</label>
                        <select class="form-control" id="congeFilter">
                            <option value="">Tous</option>
                            <option value="0">0 jour</option>
                            <option value="1-10">1-10 jours</option>
                            <option value="11+">11+ jours</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary mr-2" id="resetFilters">
                        <i class="fas fa-sync-alt fa-sm mr-1"></i>
                        Réinitialiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des employés -->
    <div class="card shadow mb-4 table-employes">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des employés</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="employesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID Employé</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Poste</th>
                            <th>CV</th>
                            <th>Congés Restants</th>
                            <th>Absences Cumulées</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employes as $employe): ?>
                        <tr>
                            <td class="employee-id">EMP-<?= str_pad($employe['id_employe'], 3, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($employe['nom']) ?></td>
                            <td><?= htmlspecialchars($employe['prenom']) ?></td>
                            <td><?= htmlspecialchars($employe['poste']) ?></td>
                            <td>
                                <?php if ($employe['cv_url'] !== 'Non disponible'): ?>
                                    <a href="<?= htmlspecialchars($employe['cv_url']) ?>" class="btn-cv" target="_blank">
                                        <i class="fas fa-file-pdf mr-1"></i>Voir CV
                                    </a>
                                <?php else: ?>
                                    <span class="btn-cv-disabled">Non disponible</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-conge"><?= $employe['conges_restants'] ?> jours</span>
                            </td>
                            <td>
                                <span class="badge-absence-autorisee"><?= $employe['absences_cumule'] ?> jours</span>
                            </td>
                            <td>
                                <a href="/conge/fiche/<?= $employe['id_employe'] ?>">
                                    <button class="btn btn-sm btn-primary btn-fiche" data-id="<?= $employe['id_employe'] ?>">
                                    <i class="fas fa-eye fa-sm"></i>
                                        Details
                                </button>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<?php
// Inclure le footer avec les scripts supplémentaires
Flight::render("footer", ['extra_js' => $extra_js]);
?>