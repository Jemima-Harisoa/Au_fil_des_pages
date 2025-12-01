<style>
    .competence-list-container {
        background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
    }
    
    .list-header-competence {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 20px;
        border-radius: 10px 10px 0 0;
        text-align: center;
    }
    
    .competence-item {
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    
    .competence-item:hover {
        border-color: #4e73df;
        box-shadow: 0 2px 10px rgba(78, 115, 223, 0.1);
    }
    
    .niveau-indicator {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 600;
        color: white;
    }
    
    .status-badge {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .btn-delete-competence {
        background: #e74a3b;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        font-size: 0.8rem;
    }
    
    .btn-delete-competence:hover {
        background: #c5301c;
        color: white;
    }
</style>

<?php
// Définition du tableau de couleurs directement dans la vue
$couleursNiveau = [
    'rouge' => '#e74a3b',
    'orange' => '#fd7e14',
    'jaune' => '#f6c23e',
    'vert' => '#1cc88a',
    'bleu' => '#4e73df'
];

// Fonction pour obtenir la couleur
function getCouleurNiveau($couleur) {
    global $couleursNiveau;
    return $couleursNiveau[strtolower($couleur)] ?? '#6c757d';
}
?>
<?php Flight::render('headerA')?>
<div class="competence-list-container">
    <div class="list-header-competence">
        <h4><i class="fas fa-list-alt mr-2"></i>Mes Compétences Auto-évaluées</h4>
        <p class="mb-0">Liste de toutes vos compétences déclarées</p>
    </div>
    
    <div class="p-4 bg-white rounded-bottom">
        <?php if (empty($competences)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucune compétence déclarée</h5>
                <p class="text-muted">Utilisez le formulaire d'auto-évaluation pour ajouter vos compétences.</p>
            </div>
        <?php else: ?>
            <?php foreach ($competences as $competence): ?>
                <div class="competence-item">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-1"><?= htmlspecialchars($competence['competence_nom']) ?></h6>
                            <p class="text-muted mb-1 small">
                                <?= htmlspecialchars($competence['domaine']) ?> • 
                                <?= htmlspecialchars($competence['type_competence']) ?>
                            </p>
                            <div class="d-flex align-items-center">
                                <span class="niveau-indicator" style="background-color: <?= getCouleurNiveau($competence['niveau_couleur']) ?>">
                                    Niveau <?= $competence['niveau'] ?> - <?= $competence['niveau_libelle'] ?>
                                </span>
                                <span class="status-badge ml-2 <?= $competence['valide'] ? 'bg-success' : 'bg-warning' ?> text-white">
                                    <?= $competence['valide'] ? 'Validé' : 'En attente' ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">
                                Évalué le: <?= date('d/m/Y', strtotime($competence['date_mesure'])) ?>
                            </small>
                            <?php if ($competence['valide'] && $competence['date_validation']): ?>
                                <br>
                                <small class="text-muted">
                                    Validé par: <?= htmlspecialchars($competence['validateur_nom'] . ' ' . $competence['validateur_prenom']) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-2 text-right">
                            <button class="btn btn-delete-competence" 
                                    onclick="deleteCompetence(<?= $id_employe ?>, <?= $competence['id_competence'] ?>)"
                                    title="Supprimer cette compétence">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteCompetence(idEmploye, idCompetence) {
    Swal.fire({
        title: 'Confirmer la suppression',
        text: "Êtes-vous sûr de vouloir supprimer cette compétence ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/employees/${idEmploye}/competences/${idCompetence}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Supprimé !',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: response.error,
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        }
    });
}
</script>
<?php Flight::render('footer')?>
