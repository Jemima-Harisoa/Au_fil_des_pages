<?php
// validation_conge.php
$extra_css = '
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .demande-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border-left: 4px solid #4e73df;
    }
    
    .demande-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    .btn-valider {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-valider:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(28, 200, 138, 0.4);
    }
    
    .btn-valider:disabled {
        background: linear-gradient(135deg, #858796 0%, #60616f 100%);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    
    .statut-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .statut-en-attente {
        background-color: #f8f9fc;
        color: #858796;
        border: 1px solid #e3e6f0;
    }
    
    .statut-valide {
        background-color: #e8f5e8;
        color: #1cc88a;
        border: 1px solid #1cc88a;
    }
    
    .progress-bar-custom {
        height: 8px;
        border-radius: 4px;
        background-color: #eaecf4;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border-radius: 4px;
        transition: width 0.3s ease;
    }
    
    .employe-info {
        background: #f8f9fc;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .info-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }
    
    .info-label {
        font-weight: 600;
        color: #5a5c69;
    }
    
    .info-value {
        color: #858796;
    }
</style>
';

Flight::render("headerA", ['extra_css' => $extra_css]);
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Validation des Demandes de Congé</h1>
        <div class="d-flex">
            <span class="badge badge-primary mr-2">
                <i class="fas fa-clock mr-1"></i> <?= count($demandes_en_attente) ?> demande(s) en attente
            </span>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Demandes en Attente de Validation</h6>
                    <div class="d-flex">
                        <span class="badge badge-success mr-2">
                            <i class="fas fa-check-circle mr-1"></i> 
                            Taux: <?= $taux_validation_global ?>%
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($demandes_en_attente)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">Aucune demande en attente</h5>
                            <p class="text-muted">Toutes les demandes de congé ont été validées.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($demandes_en_attente as $demande): ?>
                                <div class="col-xl-6 col-lg-6 mb-4">
                                    <div class="card demande-card h-100">
                                        <div class="card-body">
                                            <!-- En-tête avec nom et statut -->
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h6 class="card-title text-primary mb-0">
                                                    <i class="fas fa-user mr-2"></i>
                                                    <?= htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']) ?>
                                                </h6>
                                                <span class="statut-badge statut-en-attente">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    En attente
                                                </span>
                                            </div>

                                            <!-- Informations employé -->
                                            <div class="employe-info">
                                                <div class="info-line">
                                                    <span class="info-label">Poste:</span>
                                                    <span class="info-value"><?= htmlspecialchars($demande['poste']) ?></span>
                                                </div>
                                                <div class="info-line">
                                                    <span class="info-label">Date demande:</span>
                                                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($demande['date_demande'])) ?></span>
                                                </div>
                                            </div>

                                            <!-- Détails de la demande -->
                                            <div class="mb-3">
                                                <div class="info-line">
                                                    <span class="info-label">Type de congé:</span>
                                                    <span class="info-value font-weight-bold"><?= htmlspecialchars($demande['type_conge']) ?></span>
                                                </div>
                                                <div class="info-line">
                                                    <span class="info-label">Période:</span>
                                                    <span class="info-value">
                                                        <?= date('d/m/Y', strtotime($demande['date_debut'])) ?> - <?= date('d/m/Y', strtotime($demande['date_fin'])) ?>
                                                    </span>
                                                </div>
                                                <div class="info-line">
                                                    <span class="info-label">Jours demandés:</span>
                                                    <span class="info-value">
                                                        <?= $demande['jours_ouvrables'] ?> jour(s)
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Description -->
                                            <div class="mb-3">
                                                <small class="text-muted">Description:</small>
                                                <div class="border rounded p-2 bg-light">
                                                    <?= htmlspecialchars($demande['description'] ?? 'Aucune description fournie') ?>
                                                </div>
                                            </div>

                                            <!-- Barre de progression des validations -->
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted">Progression de validation:</small>
                                                    <small class="text-muted">
                                                        <?= $demande['validations_obtenues'] ?>/<?= $demande['niveau_validation'] ?>
                                                    </small>
                                                </div>
                                                <div class="progress-bar-custom">
                                                    <div class="progress-fill" style="width: <?= $demande['pourcentage_validation'] ?>%"></div>
                                                </div>
                                                <small class="text-muted mt-1">
                                                    <?= $demande['validations_manquantes'] ?> validation(s) manquante(s)
                                                </small>
                                            </div>

                                            <!-- Bouton de validation -->
                                            <button class="btn btn-valider btn-block" 
                                                    onclick="validerDemande(<?= $demande['id_demande'] ?>)"
                                                    data-id="<?= $demande['id_demande'] ?>"
                                                    <?= $demande['deja_valide'] ? 'disabled' : '' ?>>
                                                <i class="fas <?= $demande['deja_valide'] ? 'fa-check-double' : 'fa-check' ?> mr-2"></i> 
                                                <?= $demande['deja_valide'] ? 'Déjà validé par vous' : 'Valider cette demande' ?>
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

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function validerDemande(idDemande) {
    const bouton = $(`button[data-id="${idDemande}"]`);
    
    // Vérifier si le bouton est déjà désactivé
    if (bouton.prop('disabled')) {
        Swal.fire({
            icon: 'warning',
            title: 'Validation impossible',
            text: 'Vous avez déjà validé cette demande',
            confirmButtonText: 'OK',
            confirmButtonColor: '#f6c23e'
        });
        return;
    }
    
    // Désactiver le bouton pendant le traitement
    bouton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Validation en cours...');
    
    $.ajax({
        url: '/conge/validation/' + idDemande,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Recharger immédiatement la page
                location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: response.error || 'Une erreur est survenue lors de la validation',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#e74a3b'
                });
                bouton.prop('disabled', false).html('<i class="fas fa-check mr-2"></i>Valider cette demande');
            }
        },
        error: function(xhr) {
            console.error('Erreur AJAX:', {
                status: xhr.status,
                statusText: xhr.statusText,
                response: xhr.responseText
            });
            
            let errorMessage = 'Erreur de connexion lors de la validation';
            
            // Si on a une réponse JSON du serveur
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: errorMessage,
                confirmButtonText: 'OK',
                confirmButtonColor: '#e74a3b'
            });
            
            bouton.prop('disabled', false).html('<i class="fas fa-check mr-2"></i>Valider cette demande');
        }
    });
}
function mettreAJourInterface(idDemande, validationsObtenues, niveauValidation) {
    const pourcentage = (validationsObtenues / niveauValidation) * 100;
    const carte = $(`button[data-id="${idDemande}"]`).closest('.demande-card');
    
    carte.find('.progress-fill').css('width', pourcentage + '%');
    carte.find('.text-muted:contains("/")').text(validationsObtenues + '/' + niveauValidation);
    
    const validationsManquantes = niveauValidation - validationsObtenues;
    carte.find('.text-muted:contains("manquante")').text(validationsManquantes + ' validation(s) manquante(s)');
    
    if (validationsObtenues >= niveauValidation) {
        const bouton = $(`button[data-id="${idDemande}"]`);
        bouton.prop('disabled', true).html('<i class="fas fa-check-double mr-2"></i>Validé');
        bouton.closest('.statut-badge').removeClass('statut-en-attente').addClass('statut-valide')
              .html('<i class="fas fa-check mr-1"></i>Validé');
    }
}
</script>

<?php
Flight::render("footer", ['extra_js' => '']);
?>