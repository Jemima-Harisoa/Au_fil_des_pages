<?php
// Styles CSS spécifiques au formulaire
$extra_css = '
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .form-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 40px 0;
    }
    
    .card-form {
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        border: none;
    }
    
    .form-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .form-section {
        padding: 40px;
        background: #f8f9fc;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(78, 115, 223, 0.4);
    }
    
    .btn-cancel {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    }
    
    .alert-info {
        border-radius: 10px;
        border-left: 4px solid #36b9cc;
        background: linear-gradient(135deg, #f8f9fc 0%, #e3e6f0 100%);
        border: 1px solid #e3e6f0;
    }
    
    .form-control-user {
        border-radius: 10px;
        padding: 15px 20px;
        border: 2px solid #e3e6f0;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .form-control-user:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        transform: translateY(-1px);
    }
    
    select.form-control {
        border-radius: 10px;
        border: 2px solid #e3e6f0;
        transition: all 0.3s ease;
    }
    
    select.form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    textarea.form-control-user {
        border-radius: 10px;
        resize: vertical;
        min-height: 120px;
        border: 2px solid #e3e6f0;
        transition: all 0.3s ease;
    }
    
    textarea.form-control-user:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
    
    .stats-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #4e73df;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }
    
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        position: relative;
    }
    
    .step-indicator::before {
        content: "";
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e3e6f0;
        z-index: 1;
    }
    
    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e3e6f0;
        color: #6e707e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }
    
    .step.active .step-number {
        background: #4e73df;
        color: white;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
    }
    
    .step-label {
        font-size: 0.8rem;
        color: #6e707e;
        font-weight: 500;
    }
    
    .step.active .step-label {
        color: #4e73df;
        font-weight: 600;
    }
    
    .is-invalid {
        border-color: #e74a3b !important;
    }
</style>
';

// Scripts JavaScript spécifiques au formulaire
$extra_js = '
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Afficher le popup de résultat si présent
        ' . (isset($resultat_demande) ? "
        const result = " . json_encode($resultat_demande) . ";
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Succès !',
                text: result.message,
                confirmButtonText: 'OK',
                confirmButtonColor: '#4e73df'
            }).then(() => {
                // Redirection ou nettoyage du formulaire
                $('form')[0].reset();
                $('#infoCalcul').html(`
                    <div class=\"d-flex align-items-center\">
                        <i class=\"fas fa-info-circle fa-2x text-info mr-3\"></i>
                        <div>
                            <strong>Demande soumise avec succès</strong><br>
                            <small class=\"text-muted\">Référence: #\${result.id_demande}</small>
                        </div>
                    </div>
                `);
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: result.error || 'Une erreur est survenue',
                confirmButtonText: 'OK',
                confirmButtonColor: '#e74a3b'
            });
        }
        " : '') . '

        // Debug: Afficher les erreurs de validation
        $(\'form\').on(\'submit\', function(e) {
            console.log(\'Tentative de soumission du formulaire\');
            
            // Vérifier tous les champs requis
            let isValid = true;
            $(\'[required]\').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    console.log(\'Champ manquant:\', $(this).attr(\'name\'));
                    $(this).addClass(\'is-invalid\');
                } else {
                    $(this).removeClass(\'is-invalid\');
                }
            });

            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    icon: \'error\',
                    title: \'Champs manquants\',
                    text: \'Veuillez remplir tous les champs obligatoires.\',
                    confirmButtonColor: \'#e74a3b\'
                });
                return false;
            }

            const dateDebut = new Date($(\'#dateDebut\').val());
            const dateFin = new Date($(\'#dateFin\').val());
            const aujourdhui = new Date();
            aujourdhui.setHours(0, 0, 0, 0);
            
            const deuxSemaines = new Date(aujourdhui);
            deuxSemaines.setDate(aujourdhui.getDate() + 14);

            if (dateDebut < deuxSemaines) {
                e.preventDefault();
                Swal.fire({
                    icon: \'warning\',
                    title: \'Date invalide\',
                    text: \'La date de début doit être au moins 2 semaines après la date actuelle.\',
                    confirmButtonColor: \'#4e73df\'
                });
                return false;
            }

            if (dateFin < dateDebut) {
                e.preventDefault();
                Swal.fire({
                    icon: \'error\',
                    title: \'Dates incohérentes\',
                    text: \'La date de fin doit être après la date de début.\',
                    confirmButtonColor: \'#e74a3b\'
                });
                return false;
            }
            
            console.log(\'Formulaire validé, envoi en cours...\');
        });

        // Calcul automatique de la durée
        function calculerDuree() {
            const dateDebut = new Date($(\'#dateDebut\').val());
            const dateFin = new Date($(\'#dateFin\').val());
            
            if (dateDebut && dateFin && dateDebut <= dateFin) {
                const diffTime = Math.abs(dateFin - dateDebut);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                $(\'#infoCalcul\').html(
                    `<div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x text-info mr-3"></i>
                        <div>
                            <strong>Durée du congé: ${diffDays} jour(s)</strong><br>
                            <small class="text-muted">${dateDebut.toLocaleDateString(\'fr-FR\')} au ${dateFin.toLocaleDateString(\'fr-FR\')}</small>
                        </div>
                    </div>`
                );
            }
        }

        $(\'#dateDebut, #dateFin\').change(calculerDuree);

        // Affichage du nom du fichier
        $(\'#justificatif\').on(\'change\', function() {
            const fileName = $(this).val().split(\'\\\\\').pop();
            if (fileName) {
                $(\'#file-name\').text(fileName);
                $(\'.file-input-label\').addClass(\'bg-success text-white\').html(
                    `<i class="fas fa-check-circle mr-2"></i>Fichier sélectionné: ${fileName}`
                );
            }
        });
    });
</script>
';

if (isset($_SESSION['infoAdmin'])) {
    // Rediriger vers une page d'erreur ou de connexion
    Flight::render("headerA", ['extra_css' => $extra_css]);
}
else if (isset($_SESSION['employe'])) {
    // Rediriger vers une page d'erreur ou de connexion
    Flight::render("headerE", ['extra_css' => $extra_css]);
}
else {
    // Rediriger vers une page d'erreur ou de connexion
    Flight::render("headerU");

}
?>

<!-- Begin Page Content -->
<div class="container-fluid form-container">

    <!-- Formulaire -->
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="card card-form o-hidden border-0 shadow-lg">
                <div class="form-header">
                    <div class="text-center position-relative">
                        <h1 class="h2 text-white mb-3"><i class="fas fa-calendar-plus mr-2"></i>Nouvelle Demande de Congé</h1>
                        <p class="text-white-50 mb-0">Remplissez le formulaire ci-dessous pour soumettre votre demande de congé</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="form-section">
                        <!-- Indicateur d'étapes -->
                        <div class="step-indicator">
                            <div class="step active">
                                <div class="step-number">1</div>
                                <div class="step-label">Informations</div>
                            </div>
                            <div class="step">
                                <div class="step-number">2</div>
                                <div class="step-label">Dates</div>
                            </div>
                            <div class="step">
                                <div class="step-number">3</div>
                                <div class="step-label">Détails</div>
                            </div>
                            <div class="step">
                                <div class="step-number">4</div>
                                <div class="step-label">Validation</div>
                            </div>
                        </div>

                        <form class="user" method="post" action="/conge/demande" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-8">
                                    <!-- Informations employé (pré-remplies) -->
                                    <div class="form-group">
                                        <label for="employeInfo" class="form-label">Employé</label>
                                        <div class="icon-wrapper">
                                            <input type="text" class="form-control form-control-user" 
                                                id="employeInfo" 
                                                value="<?= htmlspecialchars(($employe['nom'] ?? '') . ' ' . ($employe['prenom'] ?? '')) ?>"
                                                readonly>
                                        </div>
                                        <input type="hidden" name="id_employe" value="<?= $employe['id_employe'] ?? '' ?>">
                                    </div>

                                    <!-- Type de congé -->
                                    <div class="form-group">
                                        <label for="typeConge" class="form-label">Type de Congé</label>
                                        <select class="form-control" id="typeConge" name="id_type_conge" required>
                                            <option value="">Sélectionner le type de congé</option>
                                            <?php if (!empty($types_conge)): ?>
                                                <?php foreach ($types_conge as $type): ?>
                                                    <option value="<?= htmlspecialchars($type['id_type']) ?>">
                                                        <?= htmlspecialchars($type['nom']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <!-- Dates -->
                                    <div class="form-group">
                                        <label class="form-label">Période de Congé</label>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="date-group">
                                                    <label for="dateDebut" class="form-label">Date de Début</label>
                                                    <input type="date" class="form-control form-control-user" 
                                                        id="dateDebut" name="date_debut" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="date-group">
                                                    <label for="dateFin" class="form-label">Date de Fin</label>
                                                    <input type="date" class="form-control form-control-user" 
                                                        id="dateFin" name="date_fin" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group">
                                        <label for="description" class="form-label">Description / Motif</label>
                                        <textarea class="form-control form-control-user" 
                                            id="description" name="description" 
                                            rows="4" placeholder="Décrivez le motif de votre demande de congé..." required></textarea>
                                    </div>

                                    <!-- Justificatif -->
                                    <div class="form-group">
                                        <label class="form-label">Justificatif</label>
                                        <div class="file-input-wrapper">
                                            <input type="file" class="form-control form-control-user" 
                                                id="justificatif" name="justificatif" 
                                                accept=".pdf,.jpg,.jpeg,.png">
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
                                </div>

                                <div class="col-lg-4">
                                    <!-- Informations calculées -->
                                    <div class="alert alert-info mb-4" id="infoCalcul">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-info-circle fa-2x text-info mr-3"></i>
                                            <div>
                                                <strong>Informations</strong><br>
                                                <small class="text-muted">La durée du congé sera calculée automatiquement après la saisie des dates.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Statistiques rapides -->
                                    <div class="stats-card">
                                        <h6 class="font-weight-bold text-primary mb-3">
                                            <i class="fas fa-chart-bar mr-2"></i>Vos Statistiques
                                        </h6>
                                        <div class="mb-3">
                                            <small class="text-muted">Congés restants</small>
                                            <div class="h4 font-weight-bold text-success"><?= $nombre_conge ?? 'N/A' ?> jours</div>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">Demandes cette année</small>
                                            <div class="h4 font-weight-bold text-info"><?= $demandes_annee ?? 0 ?> demandes</div>
                                        </div>
                                        <div>
                                            <small class="text-muted">Taux d'approbation</small>
                                            <div class="h4 font-weight-bold text-warning"><?= $taux_approbation ?? 0 ?>%</div>
                                        </div>
                                    </div>

                                    <!-- Conseils -->
                                    <div class="stats-card">
                                        <h6 class="font-weight-bold text-primary mb-3">
                                            <i class="fas fa-lightbulb mr-2"></i>Conseils
                                        </h6>
                                        <ul class="list-unstyled text-sm text-muted">
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                Soumettez votre demande 2 semaines à l'avance
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                Joignez tous les justificatifs nécessaires
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                Vérifiez vos congés restants avant de soumettre
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                Consultez le calendrier des congés de l'équipe
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Boutons d'action CORRIGÉS -->
                            <div class="form-group row mt-5">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <button type="submit" class="btn btn-submit btn-user btn-block text-white">
                                        <i class="fas fa-paper-plane mr-2"></i> Soumettre la demande
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <a href="/conge/fiche" class="btn btn-cancel btn-user btn-block text-white">
                                        <i class="fas fa-times mr-2"></i> Annuler
                                    </a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<?php
// Inclure le footer avec les scripts supplémentaires
Flight::render("footer", ['extra_js' => $extra_js]);
?>