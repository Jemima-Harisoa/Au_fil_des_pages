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
    
    /* Styles pour les compétences */
    .competence-badge {
        font-size: 0.8rem;
        padding: 5px 10px;
        margin: 2px;
    }
    
    .distribution-bar {
        height: 8px;
        background-color: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        display: flex;
    }
    
    .distribution-segment {
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .distribution-segment:hover {
        transform: scaleY(1.5);
    }
    
    .distribution-legend {
        font-size: 0.75rem;
    }
    
    .niveau-progress {
        height: 20px;
        margin-bottom: 5px;
    }
    
    .formation-suggestion {
        border-left: 4px solid #4e73df;
        padding-left: 15px;
        margin-bottom: 15px;
    }
    
    .formation-priorite-haute { border-left-color: #e74a3b; }
    .formation-priorite-moyenne { border-left-color: #f6c23e; }
    .formation-priorite-basse { border-left-color: #1cc88a; }
</style>
';

// Scripts JavaScript spécifiques à la fiche employé
$extra_js = '
<script>
    $(document).ready(function() {
        // Initialisation de DataTable pour les congés
        var tableConges = $("#detailCongesTable").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
            },
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]]
        });
        
        // Initialisation de DataTable pour les compétences
        var tableCompetences = $("#competencesTable").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
            },
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
            "order": [[2, "desc"]] // Tri par niveau décroissant par défaut
        });
        
        // Filtres pour les congés
        $("#typeFilter").on("change", function() {
            tableConges.column(0).search(this.value).draw();
        });
        
        $("#joursPrisFilter").on("change", function() {
            var value = this.value;
            if (value === "0") {
                tableConges.column(2).search("^0$", true, false).draw();
            } else if (value === "1-4") {
                tableConges.column(2).search("^[1-4]$", true, false).draw();
            } else if (value === "5+") {
                tableConges.column(2).search("^[5-9]|[1-9][0-9]", true, false).draw();
            } else {
                tableConges.column(2).search("").draw();
            }
        });
        
        $("#joursRestantsFilter").on("change", function() {
            var value = this.value;
            if (value === "0-5") {
                tableConges.column(3).search("^[0-5]$", true, false).draw();
            } else if (value === "6-15") {
                tableConges.column(3).search("^1[0-5]$|^[6-9]$", true, false).draw();
            } else if (value === "16+") {
                tableConges.column(3).search("^1[6-9]|[2-9][0-9]", true, false).draw();
            } else {
                tableConges.column(3).search("").draw();
            }
        });
        
        // Réinitialisation des filtres congés
        $("#resetFilters").on("click", function() {
            $("#typeFilter").val("");
            $("#joursPrisFilter").val("");
            $("#joursRestantsFilter").val("");
            tableConges.search("").columns().search("").draw();
        });
        
        // Fonction pour afficher l\'historique d\'une compétence
        window.afficherHistoriqueCompetence = function(idEmploye, idCompetence) {
            $.ajax({
                url: "/api/employe/" + idEmploye + "/competences/historique/" + idCompetence,
                type: "GET",
                success: function(data) {
                    // Afficher l\'historique dans une modal
                    var modalContent = "<div class=\"modal-dialog modal-lg\"><div class=\"modal-content\">";
                    modalContent += "<div class=\"modal-header\"><h5 class=\"modal-title\">Historique de la compétence</h5><button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button></div>";
                    modalContent += "<div class=\"modal-body\"><table class=\"table table-striped\"><thead><tr><th>Date</th><th>Opération</th><th>Ancien niveau</th><th>Validé</th><th>Opérateur</th></tr></thead><tbody>";
                    
                    data.forEach(function(item) {
                        modalContent += "<tr><td>" + item.date_changement_formatted + "</td><td>" + item.type_operation + "</td><td>" + (item.ancien_libelle_niveau || "N/A") + "</td><td>" + (item.ancien_valide ? "Oui" : "Non") + "</td><td>" + (item.operateur_nom ? item.operateur_prenom + " " + item.operateur_nom : "Système") + "</td></tr>";
                    });
                    
                    modalContent += "</tbody></table></div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Fermer</button></div></div></div>";
                    
                    $("#modalContainer").html(modalContent);
                    $("#modalContainer .modal").modal("show");
                },
                error: function() {
                    alert("Erreur lors du chargement de l\'historique");
                }
            });
        };
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

        <a href="/conge/validation" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-user fa-sm text-white-50 mr-2"></i>
            Voir les demandes de congé en attente de validation 
        </a>
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

                <!-- Statistiques Compétences -->
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card border-left-primary shadow stats-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Compétences</div>
                                    <div class="conge-counter text-gray-800"><?= $stats_competences['total'] ?? 0 ?></div>
                                    <div class="text-sm text-gray-500 mt-2">
                                        <i class="fas fa-check-circle mr-1"></i><?= $stats_competences['validees'] ?? 0 ?> validées
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list stats-icon text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Niveau Moyen Compétences -->
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card border-left-info shadow stats-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Niveau Moyen</div>
                                    <div class="conge-counter text-gray-800"><?= $stats_competences['niveau_moyen'] ?? 0 ?></div>
                                    <div class="text-sm text-gray-500 mt-2">
                                        <i class="fas fa-chart-line mr-1"></i>Sur 5
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-star stats-icon text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Section Compétences -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Profil de Compétences</h6>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick="exporterProfilCompetences()">
                            <i class="fas fa-download fa-sm"></i> Exporter
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Statistiques détaillées des compétences -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="h4 font-weight-bold text-primary"><?= $stats_competences['total'] ?? 0 ?></div>
                                <small class="text-muted">Total compétences</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="h4 font-weight-bold text-success"><?= $stats_competences['validees'] ?? 0 ?></div>
                                <small class="text-muted">Validées</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="h4 font-weight-bold text-info"><?= $stats_competences['niveau_moyen'] ?? 0 ?></div>
                                <small class="text-muted">Niveau moyen</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="h4 font-weight-bold text-warning"><?= $stats_competences['pourcentage_validees'] ?? 0 ?>%</div>
                                <small class="text-muted">Taux validation</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="distribution-bar mb-2">
                                    <?php
                                    $niveaux = [1 => 'Débutant', 2 => 'Intermédiaire', 3 => 'Avancé', 4 => 'Expert', 5 => 'Maître'];
                                    $couleurs = ['#e74a3b', '#f6c23e', '#36b9cc', '#1cc88a', '#4e73df'];
                                    $totalCompetences = $stats_competences['total'] ?? 1;
                                    
                                    foreach ($niveaux as $niveau => $libelle) {
                                        $pourcentage = $totalCompetences != 0 ? ($stats_competences['par_niveau'][$niveau] / $totalCompetences) * 100 : 0;
                                        if ($stats_competences['par_niveau'][$niveau] > 0) {
                                            echo '<div class="distribution-segment" style="width: ' . $pourcentage . '%; background-color: ' . $couleurs[$niveau-1] . ';" title="' . $libelle . ': ' . $stats_competences['par_niveau'][$niveau] . '"></div>';
                                        }
                                    }
                                    ?>
                                </div>
                                <small class="text-muted">Distribution par niveau</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des compétences -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="competencesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Compétence</th>
                                    <th>Domaine</th>
                                    <th>Niveau</th>
                                    <th>Source</th>
                                    <th>Validé</th>
                                    <th>Date Mesure</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($competences)): ?>
                                    <?php foreach ($competences as $competence): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($competence['nom']) ?></strong>
                                                <?php if (!empty($competence['type_competence'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($competence['type_competence']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($competence['domaine']) ?></td>
                                            <td>
                                                <span class="badge badge-<?= $competence['badge_niveau']['couleur'] ?> competence-badge">
                                                    <i class="fas <?= $competence['badge_niveau']['icone'] ?>"></i> 
                                                    <?= $competence['libelle_niveau'] ?> (<?= $competence['niveau'] ?>)
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($competence['source_evaluation'] ?? 'Non spécifié') ?></td>
                                            <td>
                                                <?php if ($competence['valide']): ?>
                                                    <span class="badge badge-success">Validé</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">En attente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $competence['date_mesure_formatted'] ?></td>
                                            <td>
                                                <button onclick="afficherHistoriqueCompetence(<?= $idEmploye ?? 0 ?>, <?= $competence['id_competence'] ?>)" 
                                                        class="btn btn-sm btn-info" 
                                                        title="Voir l'historique">
                                                    <i class="fas fa-history"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune compétence enregistrée</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Suggestions de Formations -->
    <?php if (!empty($suggestions_formations)): ?>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Suggestions de Formations</h6>
                    <span class="badge badge-primary"><?= count($suggestions_formations) ?> suggestions</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($suggestions_formations as $suggestion): ?>
                            <div class="col-md-6 mb-3">
                                <div class="formation-suggestion formation-priorite-<?= $suggestion['priorite'] ?>">
                                    <h6 class="font-weight-bold"><?= htmlspecialchars($suggestion['nom']) ?></h6>
                                    <p class="text-muted small mb-1">Domaine: <?= htmlspecialchars($suggestion['domaine']) ?></p>
                                    <div class="d-flex justify-content-between">
                                        <span class="badge badge-<?= 
                                            $suggestion['priorite'] == 'haute' ? 'danger' : 
                                            ($suggestion['priorite'] == 'moyenne' ? 'warning' : 'success') 
                                        ?>">
                                            Priorité <?= $suggestion['priorite'] ?>
                                        </span>
                                        <small class="text-muted">
                                            <?= $suggestion['nb_employes_competents'] ?> collègues compétents
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

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

<!-- Container pour les modals -->
<div id="modalContainer"></div>

<?php
// Inclure le footer avec les scripts supplémentaires
Flight::render("footer", ['extra_js' => $extra_js]);
?>