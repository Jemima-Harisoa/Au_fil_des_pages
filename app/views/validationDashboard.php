<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .badge-niveau { padding: 5px 10px; border-radius: 15px; color: white; font-weight: bold; }
        .table-hover tbody tr:hover { background-color: rgba(0,123,255,0.1); }
        .card-header { background-color: #4e73df; }
        .pending-count { 
            position: absolute; 
            top: -10px; 
            right: -10px; 
            background: #e74a3b; 
            color: white; 
            border-radius: 50%; 
            width: 25px; 
            height: 25px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 12px; 
        }
    </style>
<?php Flight::render('headerA')?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Validation Managériale - Auto-évaluations en attente
                        </h6>
                        <div class="position-relative">
                            <button class="btn btn-success" id="btnBulkValidate">
                                <i class="fas fa-check-double me-1"></i>Valider la sélection
                            </button>
                            <span class="pending-count" id="pendingCount"><?php echo count($pendingValidations); ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pendingValidations)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Aucune auto-évaluation en attente de validation.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="validationTable">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th>Employé</th>
                                            <th>Département</th>
                                            <th>Compétence</th>
                                            <th>Niveau auto-évalué</th>
                                            <th>Date</th>
                                            <th>Niveau validé</th>
                                            <th>Commentaire</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingValidations as $validation): ?>
                                            <tr data-entry-id="<?php echo $validation['entry_id']; ?>">
                                                <td>
                                                    <input type="checkbox" class="select-entry" 
                                                           data-entry-id="<?php echo $validation['entry_id']; ?>">
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($validation['employe_nom'] . ' ' . $validation['employe_prenom']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($validation['employe_poste']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($validation['departement_nom'] ?? 'Non spécifié'); ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($validation['competence_nom']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($validation['domaine']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge-niveau" 
                                                          style="background-color: <?php echo $this->getCouleurNiveau($validation['niveau_couleur']); ?>">
                                                        <?php echo $validation['niveau_auto']; ?> - <?php echo $validation['niveau_libelle']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo date('d/m/Y', strtotime($validation['date_mesure'])); ?><br>
                                                    <small class="text-muted"><?php echo date('H:i', strtotime($validation['date_mesure'])); ?></small>
                                                </td>
                                                <td>
                                                    <select class="form-control form-control-sm niveau-select" 
                                                            data-entry-id="<?php echo $validation['entry_id']; ?>">
                                                        <?php foreach ($niveaux as $niveau): ?>
                                                            <option value="<?php echo $niveau['niveau']; ?>"
                                                                    <?php echo $niveau['niveau'] == $validation['niveau_auto'] ? 'selected' : ''; ?>
                                                                    style="color: <?php echo $this->getCouleurNiveau($niveau['couleur']); ?>; font-weight: bold;">
                                                                <?php echo $niveau['niveau'] . ' - ' . $niveau['libelle']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea class="form-control form-control-sm commentaire-input" 
                                                              data-entry-id="<?php echo $validation['entry_id']; ?>"
                                                              rows="2" placeholder="Commentaire..."></textarea>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-success btn-validate" 
                                                                data-action="valide"
                                                                data-entry-id="<?php echo $validation['entry_id']; ?>">
                                                            <i class="fas fa-check"></i> Valider
                                                        </button>
                                                        <button class="btn btn-warning btn-validate" 
                                                                data-action="ajuste"
                                                                data-entry-id="<?php echo $validation['entry_id']; ?>">
                                                            <i class="fas fa-adjust"></i> Ajuster
                                                        </button>
                                                        <button class="btn btn-danger btn-reject" 
                                                                data-entry-id="<?php echo $validation['entry_id']; ?>">
                                                            <i class="fas fa-times"></i> Rejeter
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Formulaire d'ajout de compétence observée -->
                            <div class="card mt-4">
                                <div class="card-header bg-secondary text-white">
                                    <i class="fas fa-plus-circle me-2"></i>Ajouter une compétence observée
                                </div>
                                <div class="card-body">
                                    <form id="addObservedForm">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Employé</label>
                                                    <select class="form-control" id="observedEmployee" required>
                                                        <option value="">Sélectionner un employé...</option>
                                                        <!-- Les options seraient chargées dynamiquement -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Compétence</label>
                                                    <select class="form-control" id="observedCompetence" required>
                                                        <option value="">Sélectionner une compétence...</option>
                                                        <?php foreach ($competences as $competence): ?>
                                                            <option value="<?php echo $competence['id_competence']; ?>">
                                                                <?php echo htmlspecialchars($competence['nom']); ?> (<?php echo $competence['domaine']; ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label class="form-label">Niveau</label>
                                                    <select class="form-control" id="observedNiveau" required>
                                                        <?php foreach ($niveaux as $niveau): ?>
                                                            <option value="<?php echo $niveau['niveau']; ?>">
                                                                <?php echo $niveau['niveau']; ?> - <?php echo $niveau['libelle']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="mb-3">
                                                    <label class="form-label">Commentaire d'observation</label>
                                                    <textarea class="form-control" id="observedComment" 
                                                              rows="2" placeholder="Décrire l'observation..."></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="fas fa-plus me-1"></i> Ajouter
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        // Sélection/désélection de toutes les lignes
        $('#selectAll').change(function() {
            $('.select-entry').prop('checked', this.checked);
        });
        
        // Validation individuelle
        $('.btn-validate').click(function() {
            const entryId = $(this).data('entry-id');
            const action = $(this).data('action');
            const niveauFinal = $(`select[data-entry-id="${entryId}"]`).val();
            const commentaire = $(`textarea[data-entry-id="${entryId}"]`).val();
            
            validateEntry(entryId, niveauFinal, commentaire, action);
        });
        
        // Rejet d'une compétence
        $('.btn-reject').click(function() {
            const entryId = $(this).data('entry-id');
            const commentaire = $(`textarea[data-entry-id="${entryId}"]`).val();
            
            if (confirm('Rejeter cette auto-évaluation ?')) {
                validateEntry(entryId, 0, commentaire, 'rejete');
            }
        });
        
        // Validation en masse
        $('#btnBulkValidate').click(function() {
            const selectedEntries = [];
            
            $('.select-entry:checked').each(function() {
                const entryId = $(this).data('entry-id');
                const niveauFinal = $(`select[data-entry-id="${entryId}"]`).val();
                const commentaire = $(`textarea[data-entry-id="${entryId}"]`).val();
                
                selectedEntries.push({
                    entryId: entryId,
                    niveauFinal: niveauFinal,
                    commentaire: commentaire,
                    action: 'valide'
                });
            });
            
            if (selectedEntries.length === 0) {
                alert('Veuillez sélectionner au moins une compétence à valider.');
                return;
            }
            
            if (confirm(`Valider ${selectedEntries.length} compétence(s) ?`)) {
                bulkValidate(selectedEntries);
            }
        });
        
        // Ajout d'une compétence observée
        $('#addObservedForm').submit(function(e) {
            e.preventDefault();
            
            const employeeId = $('#observedEmployee').val();
            const competenceId = $('#observedCompetence').val();
            const niveau = $('#observedNiveau').val();
            const comment = $('#observedComment').val();
            
            addObservedCompetence(employeeId, competenceId, niveau, comment);
        });
        
        // Fonction de validation individuelle
        function validateEntry(entryId, niveauFinal, commentaire, action) {
            $.ajax({
                url: `/api/validations/${entryId}/validate`,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    niveauFinal: niveauFinal,
                    commentaire: commentaire,
                    action: action
                }),
                success: function(response) {
                    if (response.success) {
                        $(`tr[data-entry-id="${entryId}"]`).fadeOut(400, function() {
                            $(this).remove();
                            updatePendingCount();
                        });
                        alert('Validation enregistrée avec succès !');
                    } else {
                        alert('Erreur: ' + response.error);
                    }
                },
                error: function() {
                    alert('Erreur de communication avec le serveur.');
                }
            });
        }
        
        // Fonction de validation en masse
        function bulkValidate(entries) {
            $.ajax({
                url: '/api/validations/bulk-validate',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ entries: entries }),
                success: function(response) {
                    if (response.success) {
                        response.data.forEach(function(result) {
                            if (result.success) {
                                $(`tr[data-entry-id="${result.entryId}"]`).remove();
                            }
                        });
                        updatePendingCount();
                        alert(`${entries.length} compétence(s) validée(s) avec succès !`);
                    } else {
                        alert('Erreur: ' + response.error);
                    }
                },
                error: function() {
                    alert('Erreur de communication avec le serveur.');
                }
            });
        }
        
        // Fonction d'ajout de compétence observée
        function addObservedCompetence(employeeId, competenceId, niveau, comment) {
            $.ajax({
                url: `/api/employees/${employeeId}/competences/manager`,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    id_competence: competenceId,
                    niveau: niveau,
                    commentaire: comment
                }),
                success: function(response) {
                    if (response.success) {
                        alert('Compétence ajoutée avec succès !');
                        $('#addObservedForm')[0].reset();
                    } else {
                        alert('Erreur: ' + response.error);
                    }
                },
                error: function() {
                    alert('Erreur de communication avec le serveur.');
                }
            });
        }
        
        // Mettre à jour le compteur
        function updatePendingCount() {
            const count = $('#validationTable tbody tr').length;
            $('#pendingCount').text(count);
        }
    });
    </script>
<?php Flight::render('footer')?>