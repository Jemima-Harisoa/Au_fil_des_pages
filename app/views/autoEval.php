<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .form-container-competence {
        background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
    }
    
    .form-header-competence {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
        padding: 20px;
        border-radius: 10px 10px 0 0;
        text-align: center;
    }
    
    .form-body-competence {
        padding: 30px;
        background: white;
        border-radius: 0 0 10px 10px;
    }
    
    .btn-submit-competence {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(28, 200, 138, 0.3);
        color: white;
    }
    
    .btn-submit-competence:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(28, 200, 138, 0.4);
        color: white;
    }
    
    .niveau-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin: 2px;
    }
    
    .competence-card {
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    
    .competence-card:hover {
        border-color: #1cc88a;
        box-shadow: 0 2px 10px rgba(28, 200, 138, 0.1);
    }
    
    .niveau-selector {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .niveau-option {
        flex: 1;
        min-width: 100px;
        text-align: center;
        padding: 10px;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .niveau-option:hover {
        border-color: #1cc88a;
        background-color: #f8f9fc;
    }
    
    .niveau-option.selected {
        border-color: #1cc88a;
        background-color: #1cc88a;
        color: white;
    }
    
    .niveau-option-select {
        border-left: 4px solid #6c757d;
        padding: 8px 12px;
        margin: 4px 0;
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

<div class="form-container-competence">
    <div class="form-header-competence">
        <h4><i class="fas fa-chart-line mr-2"></i>Auto-évaluation des Compétences</h4>
        <p class="mb-0">Évaluez votre niveau sur les compétences de votre domaine</p>
    </div>
    
    <div class="form-body-competence">
        <form id="formAutoEvaluationCompetence">
            <input type="hidden" name="id_employe" value="<?= $id_employe ?>">
            
            <!-- Sélection de la compétence -->
            <div class="form-group mb-4">
                <label for="id_competence" class="form-label">Compétence </label>
                <select class="form-control" id="id_competence" name="id_competence" required>
                    <option value="">Sélectionnez une compétence...</option>
                    <?php foreach ($competences as $competence): ?>
                        <option value="<?= $competence['id_competence'] ?>" 
                                data-domaine="<?= htmlspecialchars($competence['domaine']) ?>"
                                data-type="<?= htmlspecialchars($competence['type_competence']) ?>">
                            <?= htmlspecialchars($competence['nom']) ?> 
                            (<?= htmlspecialchars($competence['domaine']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted" id="competence-description"></small>
            </div>
            
            <!-- Informations sur la compétence sélectionnée -->
            <div id="competence-info" class="alert alert-info d-none">
                <strong id="selected-competence-name"></strong>
                <div id="selected-competence-details"></div>
            </div>
            
            <!-- Sélection du niveau (menu déroulant) -->
            <div class="form-group mb-4">
                <label for="niveau" class="form-label">Niveau </label>
                <select class="form-control" id="niveau" name="niveau" required>
                    <option value="">Sélectionnez votre niveau...</option>
                    <?php foreach ($niveaux as $niveau): 
                        $couleur = getCouleurNiveau($niveau['couleur']);
                    ?>
                        <option value="<?= $niveau['niveau'] ?>" style="border-left: 4px solid <?= $couleur ?>; padding: 8px 12px;">
                            Niveau <?= $niveau['niveau'] ?> - <?= $niveau['libelle'] ?> 
                            (<?= $niveau['description'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="mt-2">
                    <small class="text-muted">Légende : </small>
                    <?php foreach ($niveaux as $niveau): ?>
                        <span class="niveau-badge mr-2" style="background-color: <?= getCouleurNiveau($niveau['couleur']) ?>; color: white;">
                            Niv. <?= $niveau['niveau'] ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Commentaires supplémentaires -->
            <div class="form-group mb-4">
                <label for="commentaires" class="form-label">Commentaires (optionnel)</label>
                <textarea class="form-control" id="commentaires" name="commentaires" 
                    rows="3" placeholder="Précisez votre expérience avec cette compétence..."></textarea>
            </div>
            
            <!-- Boutons d'action -->
            <div class="form-group row mt-5">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <button type="submit" class="btn btn-submit-competence btn-block">
                        <i class="fas fa-paper-plane mr-2"></i> Soumettre l'auto-évaluation
                    </button>
                </div>
                <div class="col-sm-6">
                    <button type="button" class="btn btn-secondary btn-block" onclick="$('#formulaireCompetenceContainer').html('')">
                        <i class="fas fa-times mr-2"></i> Annuler
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Affichage des détails de la compétence sélectionnée
        $('#id_competence').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const domaine = selectedOption.data('domaine');
            const type = selectedOption.data('type');
            const nom = selectedOption.text();
            
            if (selectedOption.val()) {
                $('#selected-competence-name').text(nom);
                $('#selected-competence-details').html(`
                    <div>Domaine: ${domaine}</div>
                    <div>Type: ${type}</div>
                `);
                $('#competence-info').removeClass('d-none');
            } else {
                $('#competence-info').addClass('d-none');
            }
        });
        
        // Soumission du formulaire
        $('#formAutoEvaluationCompetence').on('submit', function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();
            const submitBtn = $(this).find('button[type="submit"]');
            
            // Validation
            if (!$('#niveau').val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Niveau requis',
                    text: 'Veuillez sélectionner un niveau de compétence',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            if (!$('#id_competence').val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Compétence requise',
                    text: 'Veuillez sélectionner une compétence',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Désactiver le bouton pendant l'envoi
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Envoi en cours...');
            
            $.ajax({
                url: '/employees/<?= $id_employe ?>/competences',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès !',
                            text: response.message,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#1cc88a'
                        }).then(() => {
                            // Fermer le formulaire et recharger la liste
                            $('#formulaireCompetenceContainer').html('');
                            // Optionnel: recharger la liste des compétences
                            loadCompetenceList();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: response.error || 'Une erreur est survenue',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#e74a3b'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur de connexion',
                        text: 'Impossible de contacter le serveur',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#e74a3b'
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i> Soumettre l\'auto-évaluation');
                }
            });
        });
    });
    
    function loadCompetenceList() {
        // Fonction pour recharger la liste des compétences (à implémenter si nécessaire)
        console.log('Rechargement de la liste des compétences...');
    }
</script>
<?php Flight::render('footer')?>