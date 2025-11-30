<?php include "headerA.php"; ?>
<div class="container-fluid">

    <!-- En-tête de page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails de l'Évaluation</h1>
        <div>
            <a href="/evaluations" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <?php if ($evaluation['statut'] == 'PREVUE' || $evaluation['statut'] == 'EN_COURS'): ?>
                <a href="/evaluation/<?= $evaluation['id_evaluation'] ?>/saisie" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Saisir les notes
                </a>
            <?php endif; ?>
            <?php if ($evaluation['statut'] == 'TERMINEE'): ?>
                <a href="/evaluation/<?= $evaluation['id_evaluation'] ?>/score" class="btn btn-success">
                    <i class="fas fa-chart-bar"></i> Voir le score détaillé
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informations employé</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Nom complet</th>
                                    <td><?= htmlspecialchars($evaluation['employe_nom'] . ' ' . $evaluation['employe_prenom']) ?></td>
                                </tr>
                                <tr>
                                    <th>Poste</th>
                                    <td><?= htmlspecialchars($evaluation['employe_poste'] ?? 'Non spécifié') ?></td>
                                </tr>
                                <tr>
                                    <th>Département</th>
                                    <td><?= htmlspecialchars($evaluation['departement_nom'] ?? 'Non spécifié') ?></td>
                                </tr>
                                <tr>
                                    <th>Date d'embauche</th>
                                    <td><?= htmlspecialchars($evaluation['employe_date_embauche'] ?? 'Non spécifiée') ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Informations évaluation</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Période</th>
                                    <td><?= htmlspecialchars($evaluation['periode_nom']) ?></td>
                                </tr>
                                <tr>
                                    <th>Description période</th>
                                    <td><?= htmlspecialchars($evaluation['periode_description'] ?? 'Aucune description') ?></td>
                                </tr>
                                <tr>
                                    <th>Manager</th>
                                    <td>
                                        <?php if ($evaluation['manager_nom']): ?>
                                            <?= htmlspecialchars($evaluation['manager_nom'] . ' ' . $evaluation['manager_prenom']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Non assigné</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <span class="badge 
                                            <?= $evaluation['statut'] == 'TERMINEE' ? 'badge-success' : '' ?>
                                            <?= $evaluation['statut'] == 'EN_COURS' ? 'badge-warning' : '' ?>
                                            <?= $evaluation['statut'] == 'PREVUE' ? 'badge-secondary' : '' ?>
                                        ">
                                            <?= htmlspecialchars($evaluation['statut']) ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Dates et score -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dates et score</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Date de génération</th>
                            <td><?= htmlspecialchars($evaluation['date_generation']) ?></td>
                        </tr>
                        <tr>
                            <th>Date d'évaluation</th>
                            <td><?= htmlspecialchars($evaluation['date_evaluation']) ?></td>
                        </tr>
                        <tr>
                            <th>Score total</th>
                            <td>
                                <?php if ($evaluation['score_total']): ?>
                                    <span class="font-weight-bold 
                                        <?= $evaluation['score_total'] >= 80 ? 'text-success' : '' ?>
                                        <?= $evaluation['score_total'] >= 60 && $evaluation['score_total'] < 80 ? 'text-warning' : '' ?>
                                        <?= $evaluation['score_total'] < 60 ? 'text-danger' : '' ?>
                                    ">
                                        <?= number_format($evaluation['score_total'], 2) ?> / 100
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Non évalué</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Créé le</th>
                            <td><?= htmlspecialchars($evaluation['created_at']) ?></td>
                        </tr>
                        <tr>
                            <th>Modifié le</th>
                            <td><?= htmlspecialchars($evaluation['updated_at']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($evaluation['statut'] == 'PREVUE' || $evaluation['statut'] == 'EN_COURS'): ?>
                            <a href="/evaluation/<?= $evaluation['id_evaluation'] ?>/saisie" 
                               class="btn btn-primary btn-block">
                                <i class="fas fa-edit"></i> Saisir les notes
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($evaluation['statut'] == 'TERMINEE'): ?>
                            <a href="/evaluation/<?= $evaluation['id_evaluation'] ?>/score" 
                               class="btn btn-success btn-block">
                                <i class="fas fa-chart-bar"></i> Voir le score détaillé
                            </a>
                            
                        <?php endif; ?>
                        
                        <a href="/employee/<?= $evaluation['employe_id'] ?>/historique" 
                           class="btn btn-secondary btn-block">
                            <i class="fas fa-history"></i> Historique employé
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section critères si évaluation terminée -->
    <?php if ($evaluation['statut'] == 'TERMINEE' && isset($details) && !empty($details)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Détail des critères évalués</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Critère</th>
                                    <th>Poids</th>
                                    <th>Note</th>
                                    <th>Score calculé</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $detail): ?>
                                <tr>
                                    <td><?= htmlspecialchars($detail['critere']) ?></td>
                                    <td><?= htmlspecialchars($detail['poids']) ?>%</td>
                                    <td><?= htmlspecialchars($detail['note']) ?>/10</td>
                                    <td><?= number_format(($detail['note'] ?? 0) * ($detail['poids'] ?? 0) / 10, 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation pour les badges de statut
    const statusBadge = document.querySelector('.badge');
    if (statusBadge) {
        statusBadge.style.transition = 'all 0.3s ease';
        statusBadge.addEventListener('mouseover', function() {
            this.style.transform = 'scale(1.1)';
        });
        statusBadge.addEventListener('mouseout', function() {
            this.style.transform = 'scale(1)';
        });
    }
});
</script>

<?php include "footer.php"; ?>