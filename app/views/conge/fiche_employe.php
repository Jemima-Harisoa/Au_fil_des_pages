<?php
// Styles CSS spécifiques à la fiche employé
$extra_css = '
<style>
    .id-card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
        background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
    }
    
    .id-card-header {
        background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 20px;
        text-align: center;
    }
    
    .id-card-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid white;
        margin: 0 auto 15px;
        overflow: hidden;
        background-color: #f8f9fc;
    }
    
    .id-card-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .id-card-body {
        padding: 25px;
    }
    
    .employee-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2e59d9;
        margin-bottom: 5px;
    }
    
    .employee-position {
        font-size: 1.1rem;
        color: #6c757d;
        margin-bottom: 15px;
    }
    
    .employee-info {
        margin-bottom: 20px;
    }
    
    .info-item {
        display: flex;
        margin-bottom: 10px;
        align-items: flex-start;
    }
    
    .info-icon {
        width: 24px;
        text-align: center;
        margin-right: 10px;
        color: #4e73df;
    }
    
    .info-content {
        flex: 1;
    }
    
    .info-label {
        font-size: 0.8rem;
        color: #858796;
        margin-bottom: 2px;
    }
    
    .info-value {
        font-size: 0.95rem;
        font-weight: 500;
        color: #5a5c69;
    }
    
    .id-card-footer {
        background-color: #f8f9fc;
        border-top: 1px solid #e3e6f0;
        padding: 15px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .employee-id {
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .badge-department {
        background-color: #4e73df;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
    }
    
    .stats-card {
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
        transition: transform 0.3s;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .stats-icon {
        font-size: 2rem;
        opacity: 0.7;
    }
    
    .conge-counter {
        font-size: 2.5rem;
        font-weight: 700;
    }
</style>
';

// Scripts JavaScript spécifiques à la fiche employé
$extra_js = '
<script>
    $(document).ready(function() {
        // Initialisation de DataTable
        var table = $("#detailCongesTable").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
            },
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]]
        });
        
        // Filtre par type de congé
        $("#typeFilter").on("change", function() {
            table.column(0).search(this.value).draw();
        });
        
        // Filtre par jours pris
        $("#joursPrisFilter").on("change", function() {
            var value = this.value;
            if (value === "0") {
                table.column(2).search("^0$", true, false).draw();
            } else if (value === "1-4") {
                table.column(2).search("^[1-4]$", true, false).draw();
            } else if (value === "5+") {
                table.column(2).search("^[5-9]|[1-9][0-9]", true, false).draw();
            } else {
                table.column(2).search("").draw();
            }
        });
        
        // Filtre par jours restants
        $("#joursRestantsFilter").on("change", function() {
            var value = this.value;
            if (value === "0-5") {
                table.column(3).search("^[0-5]$", true, false).draw();
            } else if (value === "6-15") {
                table.column(3).search("^1[0-5]$|^[6-9]$", true, false).draw();
            } else if (value === "16+") {
                table.column(3).search("^1[6-9]|[2-9][0-9]", true, false).draw();
            } else {
                table.column(3).search("").draw();
            }
        });
        
        // Réinitialisation des filtres
        $("#resetFilters").on("click", function() {
            $("#typeFilter").val("");
            $("#joursPrisFilter").val("");
            $("#joursRestantsFilter").val("");
            table.search("").columns().search("").draw();
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
        <h1 class="h3 mb-0 text-gray-800">Fiche Employé</h1>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Carte d'identité Employé -->
        <?= $fiche ?>

        <!-- Statistiques Congés -->
        <div class="col-xl-4 col-lg-5">
            <div class="row">
                <!-- Congés Restants -->
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card border-left-success shadow stats-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Congés Restants</div>
                                    <div class="conge-counter text-gray-800"><?= $nombre_conge ?? 'N/A' ?></div>
                                    <div class="text-sm text-gray-500 mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>Jours disponibles
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-check stats-icon text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jours travaillés cette année -->
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card border-left-info shadow stats-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Jours travaillés (2024)</div>
                                    <div class="conge-counter text-gray-800">187</div>
                                    <div class="text-sm text-gray-500 mt-2">
                                        <i class="fas fa-briefcase mr-1"></i>Jours présents
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line stats-icon text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ancienneté -->
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card border-left-warning shadow stats-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Ancienneté</div>
                                    <div class="conge-counter text-gray-800">4</div>
                                    <div class="text-sm text-gray-500 mt-2">
                                        <i class="fas fa-clock mr-1"></i>Années dans l'entreprise
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-clock stats-icon text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Section Absences -->
    <?= $absences ?>

    <!-- Section Detail Absence -->
    <?= $detailAbsence ?? '' ?>

    <!-- Section Détail Congés -->
    <?= $listeconge ?>

    <!-- Section Détail Congés -->
    <?= $detailConge ?? '' ?>

</div>
<!-- /.container-fluid -->

<?php
// Inclure le footer avec les scripts supplémentaires
Flight::render("footer", ['extra_js' => $extra_js]);
?>