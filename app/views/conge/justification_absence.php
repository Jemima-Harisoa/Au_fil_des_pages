<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .form-container-justification {
        background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
    }
    
    .form-header-justification {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 20px;
        border-radius: 10px 10px 0 0;
        text-align: center;
    }
    
    .form-body-justification {
        padding: 30px;
        background: white;
        border-radius: 0 0 10px 10px;
    }
    
    .btn-submit-justification {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
        color: white;
    }
    
    .btn-submit-justification:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(78, 115, 223, 0.4);
        color: white;
    }
    
    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }
    
    .file-input-wrapper input[type=file] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        cursor: pointer;
    }
    
    .file-input-label {
        display: block;
        padding: 12px 20px;
        background: #f8f9fc;
        border: 2px dashed #d1d3e2;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #6e707e;
    }
    
    .file-input-label:hover {
        background: #eaecf4;
        border-color: #4e73df;
    }
</style>

<!-- Formulaire de Justification d'Absence -->
<div class="form-container-justification">
    <div class="form-header-justification">
        <h4><i class="fas fa-file-upload mr-2"></i>Justifier une Absence</h4>
        <p class="mb-0">Veuillez fournir un justificatif pour l'absence sélectionnée</p>
    </div>
    <div class="form-body-justification">
        <form id="formJustificationAbsence" method="post" action="/abscence/justifier" enctype="multipart/form-data">
            <input type="hidden" name="id_abscence" value="<?= $absence['id_abscence'] ?>">
            
            <!-- Informations sur l'absence -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Période d'absence</label>
                    <input type="text" class="form-control" value="<?= $absence['periode'] ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nombre de jours</label>
                    <input type="text" class="form-control" value="<?= $absence['jours_pris'] ?> jour(s)" readonly>
                </div>
            </div>
            
            <!-- Description de l'absence -->
            <div class="form-group mb-4">
                <label class="form-label">Description de l'absence</label>
                <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($absence['description'] ?? 'Aucune description') ?></textarea>
            </div>
            
            <!-- Justificatif -->
            <div class="form-group mb-4">
                <label class="form-label">Justificatif</label>
                <div class="file-input-wrapper">
                    <input type="file" class="form-control" id="justificatif" name="justificatif" 
                        accept=".pdf,.jpg,.jpeg,.png" required>
                    <label for="justificatif" class="file-input-label">
                        <i class="fas fa-cloud-upload-alt mr-2"></i>
                        <span id="file-name">Choisir un fichier (PDF, JPG, PNG - Max 2MB)</span>
                    </label>
                </div>
                <small class="form-text text-muted mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Formats acceptés: PDF, JPG, PNG (taille maximale: 2MB)
                </small>
            </div>
            
            <!-- Commentaire supplémentaire -->
            <div class="form-group mb-4">
                <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                <textarea class="form-control" id="commentaire" name="commentaire" 
                    rows="3" placeholder="Ajoutez un commentaire si nécessaire..."></textarea>
            </div>
            
            <!-- Boutons d'action -->
            <div class="form-group row mt-5">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <button type="submit" class="btn btn-submit-justification btn-block">
                        <i class="fas fa-paper-plane mr-2"></i> Soumettre la justification
                    </button>
                </div>
                <div class="col-sm-6">
                    <button type="button" class="btn btn-secondary btn-block" onclick="$('#formulaireJustificationContainer').html('')">
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
        // Affichage du nom du fichier
        $('#justificatif').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('#file-name').text(fileName);
                $('.file-input-label').addClass('bg-success text-white').html(
                    `<i class="fas fa-check-circle mr-2"></i>Fichier sélectionné: ${fileName}`
                );
            }
        });

        // Soumission du formulaire via AJAX
        $('#formJustificationAbsence').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            $.ajax({
                url: '/abscence/justifier',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès !',
                            text: response.message,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4e73df'
                        }).then(() => {
                            // Recharger la page pour mettre à jour la liste
                            location.reload();
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
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Erreur lors de l\'envoi du formulaire',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#e74a3b'
                    });
                }
            });
        });
    });
</script>