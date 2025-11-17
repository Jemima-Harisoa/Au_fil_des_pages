<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .validation-container {
        background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }
    
    .validation-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 20px;
        border-radius: 10px 10px 0 0;
        text-align: center;
    }
    
    .validation-body {
        padding: 30px;
        background: white;
        border-radius: 0 0 10px 10px;
    }
    
    .btn-validate {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        border: none;
        border-radius: 10px;
        padding: 8px 20px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(28, 200, 138, 0.3);
        color: white;
    }
    
    .btn-validate:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(28, 200, 138, 0.4);
        color: white;
    }
    
    .btn-validate:disabled {
        background: linear-gradient(135deg, #858796 0%, #60616f 100%);
        cursor: not-allowed;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .status-pending {
        background-color: #f8f9fc;
        color: #858796;
        border: 1px solid #e3e6f0;
    }
    
    .status-approved {
        background-color: #e8f5e8;
        color: #1cc88a;
        border: 1px solid #1cc88a;
    }
    
    .demande-card {
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .demande-card:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .demande-header {
        background: #f8f9fc;
        padding: 15px 20px;
        border-bottom: 1px solid #e3e6f0;
        border-radius: 10px 10px 0 0;
    }
    
    .demande-body {
        padding: 20px;
    }
    
    .info-item {
        margin-bottom: 10px;
    }
    
    .info-label {
        font-weight: 600;
        color: #5a5c69;
        font-size: 0.9rem;
    }
    
    .info-value {
        color: #858796;
    }
    
    .progress-validation {
        height: 8px;
        border-radius: 4px;
        margin-top: 5px;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #858796;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #dddfeb;
    }
</style>

<!-- Container principal de validation des congés -->
<div class="validation-container">
    <div class="validation-header">
        <h4><i class="fas fa-clipboard-check mr-2"></i>Validation des Demandes de Congé</h4>
        <p class="mb-0">Liste des demandes de congé en attente de validation</p>
    </div>
    
    <div class="validation-body">
        <?php if (empty($demandes_en_attente)): ?>
            <!-- État vide -->
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h5>Aucune demande en attente</h5>
                <p>Toutes les demandes de congé ont été traitées.</p>
            </div>
        <?php else: ?>
            <!-- Liste des demandes en attente -->
            <div class="row">
                <?php foreach ($demandes_en_attente as $demande): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="demande-card">
                            <div class="demande-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-user mr-2"></i>
                                    <?= htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']) ?>
                                </h6>
                                <span class="status-badge status-pending">
                                    <i class="fas fa-clock mr-1"></i>
                                    En attente
                                </span>
                            </div>
                            
                            <div class="demande-body">
                                <!-- Informations de la demande -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Type de congé</div>
                                            <div class="info-value">
                                                <i class="fas fa-calendar-alt mr-2"></i>
                                                <?= htmlspecialchars($demande['type_conge']) ?>
                                            </div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">Période</div>
                                            <div class="info-value">
                                                <i class="fas fa-calendar-day mr-2"></i>
                                                <?= date('d/m/Y', strtotime($demande['date_debut'])) ?> - <?= date('d/m/Y', strtotime($demande['date_fin'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Poste</div>
                                            <div class="info-value">
                                                <i class="fas fa-briefcase mr-2"></i>
                                                <?= htmlspecialchars($demande['poste']) ?>
                                            </div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">Date de demande</div>
                                            <div class="info-value">
                                                <i class="fas fa-paper-plane mr-2"></i>
                                                <?= date('d/m/Y H:i', strtotime($demande['date_demande'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Description -->
                                <div class="info-item">
                                    <div class="info-label">Description</div>
                                    <div class="info-value">
                                        <i class="fas fa-file-alt mr-2"></i>
                                        <?= htmlspecialchars($demande['description'] ?? 'Aucune description') ?>
                                    </div>
                                </div>
                                
                                <!-- Barre de progression des validations -->
                                <div class="info-item">
                                    <div class="info-label">
                                        Validations 
                                        (<?= $demande['validations_obtenues'] ?>/<?= $demande['niveau_validation'] ?>)
                                    </div>
                                    <div class="progress progress-validation">
                                        <div class="progress-bar bg-success" 
                                             role="progressbar" 
                                             style="width: <?= ($demande['validations_obtenues'] / $demande['niveau_validation']) * 100 ?>%"
                                             aria-valuenow="<?= $demande['validations_obtenues'] ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="<?= $demande['niveau_validation'] ?>">
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <?= $demande['validations_manquantes'] ?> validation(s) manquante(s)
                                    </small>
                                </div>
                                
                                <!-- Bouton de validation -->
                                <div class="text-right mt-3">
                                    <button class="btn btn-validate" 
                                            onclick="validerDemande(<?= $demande['id_demande'] ?>)"
                                            data-id="<?= $demande['id_demande'] ?>">
                                        <i class="fas fa-check mr-2"></i>
                                        Valider cette demande
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Statistiques -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Demandes en attente</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= count($demandes_en_attente) ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Validations aujourd'hui</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Taux de validation</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= $this->calculerTauxValidation($demandes_en_attente) ?>%
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Fonction pour valider une demande de congé
    function validerDemande(idDemande) {
        const bouton = $(`button[data-id="${idDemande}"]`);
        
        // Désactiver le bouton pendant le traitement
        bouton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Validation en cours...');
        
        $.ajax({
            url: '/conge/validation/' + idDemande,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.validation_complete) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Validation complète !',
                            text: response.message,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#1cc88a'
                        }).then(() => {
                            // Recharger la page pour mettre à jour la liste
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Validation enregistrée',
                            text: response.message,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#1cc88a'
                        }).then(() => {
                            // Mettre à jour l'interface sans recharger
                            mettreAJourInterface(idDemande);
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: response.error || 'Une erreur est survenue lors de la validation',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#e74a3b'
                    });
                    // Réactiver le bouton en cas d'erreur
                    bouton.prop('disabled', false).html('<i class="fas fa-check mr-2"></i>Valider cette demande');
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Erreur de connexion lors de la validation',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#e74a3b'
                });
                // Réactiver le bouton en cas d'erreur
                bouton.prop('disabled', false).html('<i class="fas fa-check mr-2"></i>Valider cette demande');
            }
        });
    }
    
    // Fonction pour mettre à jour l'interface après validation partielle
    function mettreAJourInterface(idDemande) {
        // Ici, on pourrait mettre à jour la barre de progression sans recharger
        // Pour l'instant, on recharge simplement la page
        location.reload();
    }
    
    // Fonction pour calculer le taux de validation global
    function calculerTauxValidation() {
        // Cette fonction pourrait être utilisée pour des statistiques en temps réel
        // Pour l'instant, le calcul est fait côté serveur
        return 0;
    }
</script>