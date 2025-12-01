<?php include "headerA.php"; ?>

<div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">Évaluations périodiques</h6>

    
</div>

<div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Manager</th>
                    <th>Période</th>
                    <th>Statut</th>
                    <th>Score</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($evaluations as $eval): ?>
                <tr data-dept="<?= htmlspecialchars($eval['id_departement'] ?? 'all') ?>">
                    <td><?= htmlspecialchars($eval['employe'] . ' ' . $eval['prenom_employe']) ?></td>
                    <td>
                        <?php if (!empty($eval['manager'])): ?>
                            <?= htmlspecialchars($eval['manager'] . ' ' . $eval['prenom_manager']) ?>
                        <?php else: ?>
                            Non assigné
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($eval['periode']) ?></td>
                    <td>
                        <span class="statut-<?= strtolower($eval['statut']) ?>">
                            <?= htmlspecialchars($eval['statut']) ?>
                        </span>
                    </td>
                    <td>
                        <?= isset($eval['score_total']) ? number_format($eval['score_total'], 2) : '-' ?>
                    </td>
                    <td>
                        <a href="/evaluation/<?= $eval['id_evaluation'] ?>">Voir</a>
                        <?php if ($eval['statut'] === 'PREVUE' || $eval['statut'] === 'EN_COURS'): ?>
                            <a href="/evaluation/<?= $eval['id_evaluation'] ?>/saisie">Évaluer</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Filtrage par département
function applyFilters() {
    const select = document.getElementById('deptFilter');
    const selectedValue = select.value;
    const rows = document.querySelectorAll('#dataTable tbody tr');

    rows.forEach(row => {
        const rowDept = row.dataset.dept || 'all';
        row.style.display = (selectedValue === 'all' || rowDept === selectedValue) ? '' : 'none';
    });
}
</script>

<?php include "footer.php"; ?>
