<?php include "headerA.php" ?>

<div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">Annonces</h6>

    <!-- Filtres -->
    <form id="filterForm" class="form-inline">
        <label class="mr-2">Statut:</label>
        <select id="statusFilter" class="form-control mr-3">
            <option value="all">Toutes</option>
            <option value="expired">Expirées</option>
            <option value="active">Non expirées</option>
        </select>

        <label class="mr-2">Création:</label>
        <input type="date" id="pubStart" class="form-control mr-1">
        <input type="date" id="pubEnd" class="form-control mr-3">

        <label class="mr-2">Expiration:</label>
        <input type="date" id="expStart" class="form-control mr-1">
        <input type="date" id="expEnd" class="form-control mr-3">

        <button type="button" class="btn btn-sm btn-primary" onclick="applyFilters()">Filtrer</button>
        <button type="button" class="btn btn-sm btn-secondary ml-2" onclick="resetFilters()">Réinitialiser</button>
    </form>
</div>

<div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <?php if (!empty($Annonces)): ?>
                        <?php foreach (array_keys($Annonces[0]) as $col): ?>
                            <th><?= ucfirst($col) ?></th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($Annonces as $annonce): ?>
                    <tr data-id="<?= $annonce['id_annonce'] ?>">
                        <?php foreach ($annonce as $key => $val): ?>
                            <td data-key="<?= $key ?>" class="editable">
                                <?php if ($key === 'lien' && $val): ?>
                                    <button class="btn btn-sm btn-info" onclick="showJsonPreview('<?= htmlspecialchars($val, ENT_QUOTES) ?>')">📄</button>
                                <?php else: ?>
                                    <?= htmlspecialchars($val ?? '') ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal pour aperçu du contenu de l'annonce -->
<div class="modal fade" id="jsonModal" tabindex="-1" role="dialog" aria-labelledby="jsonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jsonModalLabel">Aperçu de l'annonce</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="jsonModalBody" style="white-space: pre-wrap; font-family: monospace;"></div>
        </div>
    </div>
</div>

<script>
// Aperçu contenu JSON
function showJsonPreview(filePath) {
    fetch(filePath)
        .then(res => res.json())
        .then(data => {
            document.getElementById('jsonModalBody').textContent = data.contenu;
            $('#jsonModal').modal('show');
        })
        .catch(err => {
            console.error(err);
            alert('Erreur lors de la lecture du JSON.');
        });
}

// Double clic édition sur date_expiration
document.querySelectorAll('td[data-key="date_expiration"]').forEach(cell => {
    cell.addEventListener('dblclick', function () {
        if (cell.querySelector('input')) return;

        let oldValue = cell.textContent.trim();
        let id = cell.closest('tr').getAttribute('data-id');
        let input = document.createElement('input');
        input.type = 'date';
        input.className = 'form-control form-control-sm';
        input.value = oldValue || "";

        cell.textContent = '';
        cell.appendChild(input);
        input.focus();

        function save() {
            let newValue = input.value;
            if (!newValue || newValue === oldValue) {
                cell.textContent = oldValue;
                return;
            }

            fetch('/annonces/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, key: 'date_expiration', value: newValue })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    cell.textContent = newValue;
                } else {
                    alert(data.message);
                    cell.textContent = oldValue;
                }
            })
            .catch(err => {
                console.error(err);
                cell.textContent = oldValue;
            });
        }

        input.addEventListener('blur', save);
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter') save();
            else if (e.key === 'Escape') cell.textContent = oldValue;
        });
    });
});

// --- Filtres ---
function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const pubStart = document.getElementById('pubStart').value;
    const pubEnd = document.getElementById('pubEnd').value;
    const expStart = document.getElementById('expStart').value;
    const expEnd = document.getElementById('expEnd').value;

    const rows = document.querySelectorAll('#dataTable tbody tr');
    const today = new Date().toISOString().split('T')[0];

    rows.forEach(row => {
        const pubDate = row.querySelector('[data-key="date_publication"]').textContent.trim();
        const expDate = row.querySelector('[data-key="date_expiration"]').textContent.trim();

        let show = true;

        // filtre statut
        if (status === 'expired' && (!expDate || expDate > today)) show = false;
        if (status === 'active' && expDate && expDate <= today) show = false;

        // filtre dates publication
        if (pubStart && pubDate < pubStart) show = false;
        if (pubEnd && pubDate > pubEnd) show = false;

        // filtre dates expiration
        if (expStart && (!expDate || expDate < expStart)) show = false;
        if (expEnd && (!expDate || expDate > expEnd)) show = false;

        row.style.display = show ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('filterForm').reset();
    document.querySelectorAll('#dataTable tbody tr').forEach(row => row.style.display = '');
}
</script>

<?php include "footerFormAnnonces.php" ?>
