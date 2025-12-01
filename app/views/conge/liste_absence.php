<?php
// liste_absence.php - Vue pour afficher la LISTE des absences à justifier
$extra_css = '
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .absence-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border-left: 4px solid #e74a3b;
    }
    
    .absence-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    .btn-justifier {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-justifier:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
    }
    
    .penalite-badge {
        background-color: #e74a3b;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
    }
</style>
';

Flight::render("headerA", ['extra_css' => $extra_css]);
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Justification d'Absences</h1>
        <div class="d-flex align-items-center">
            <span class="mr-3 text-gray-600">
                <i class="fas fa-user mr-2"></i>
                <?= htmlspecialchars($employe['prenom'] . ' ' . $employe['nom']) ?>
            </span>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Absences Non Autorisées à Justifier</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($absences)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">Aucune absence à justifier</h5>
                            <p class="text-muted">Toutes vos absences sont déjà justifiées ou autorisées.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($absences as $absence): ?>
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card absence-card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-danger">
                                                <i class="fas fa-calendar-times mr-2"></i>
                                                Absence du <?= $absence['debut_formatted'] ?>
                                            </h6>
                                            <div class="mb-2">
                                                <small class="text-muted">Période:</small>
                                                <div class="font-weight-bold"><?= $absence['periode'] ?></div>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">Jours:</small>
                                                <div class="font-weight-bold"><?= $absence['jours_pris'] ?> jour(s)</div>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">Pénalité:</small>
                                                <div class="penalite-badge"><?= $absence['penalite'] ?></div>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-muted">Description:</small>
                                                <div class="text-truncate"><?= htmlspecialchars($absence['description'] ?? 'Non spécifié') ?></div>
                                            </div>
                                            <button class="btn btn-justifier btn-block" 
                                                    onclick="afficherFormulaireJustification(<?= $absence['id_abscence'] ?>)">
                                                <i class="fas fa-file-upload mr-2"></i> Justifier cette absence
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Section pour le formulaire de justification -->
    <div id="formulaireJustificationContainer"></div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function afficherFormulaireJustification(idAbsence) {
    // Charger le formulaire via AJAX
    $.ajax({
        url: '/absence/justifier/' + idAbsence,
        type: 'GET',
        success: function(response) {
            $('#formulaireJustificationContainer').html(response);
            
            // Scroll vers le formulaire
            $('html, body').animate({
                scrollTop: $('#formulaireJustificationContainer').offset().top
            }, 1000);
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de charger le formulaire de justification',
                confirmButtonColor: '#e74a3b'
            });
        }
    });
}
</script>

<?php
Flight::render("footer", ['extra_js' => '']);
?>