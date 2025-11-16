<?php include "headerA.php"; ?>

<div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">Pointage des employés</h6>

    <!-- Bouton relève groupe -->
    <button type="button" class="btn btn-sm btn-success" onclick="releverPresenceGroupe()">Relève présence groupe</button>

    <!-- Filtre département -->
    <div class="form-inline">
        <label class="mr-2">Département :</label>
        <select id="deptFilter" class="form-control" onchange="applyFilters()">
            <option value="all">Tous les départements</option>
            <?php foreach ($allDepts as $d): ?>
                <option value="<?= htmlspecialchars($d['id_departement']) ?>">
                    <?= htmlspecialchars($d['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <?php 
                    $displayCols = [
                        'nom_personne' => 'Nom',
                        'prenom' => 'Prénom',
                        'date_naissance' => 'Date de naissance',
                        'contact' => 'Contact',
                        'lien_image' => 'Photo',
                        'nom_departement' => 'Département',
                        'poste' => 'Poste',
                        'date_embauche' => 'Date embauche',
                        'nombre_conge' => 'Congés',
                        'salaire_base' => 'Salaire'
                    ];
                    foreach ($displayCols as $label): ?>
                        <th><?= htmlspecialchars($label) ?></th>
                    <?php endforeach; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allPresence as $emp): ?>
                    <tr>
                        <?php foreach ($displayCols as $key => $_): ?>
                            <td>
                                <?php 
                                if ($key === 'lien_image' && !empty($emp[$key])) {
                                    echo '<img src="'.htmlspecialchars($emp[$key]).'" width="50" class="rounded-circle">';
                                } else {
                                    echo htmlspecialchars($emp[$key] ?? '');
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>
                        <td>
                            <button type="button" class="btn btn-sm btn-info" onclick="releverPresenceInd(<?= $emp['id_employe'] ?>)">
                                Présence individuelle
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal pour afficher le pointage -->
<div class="modal fade" id="pointageModal" tabindex="-1" role="dialog" aria-labelledby="pointageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pointageModalLabel">Détail du pointage</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="pointageModalBody">
        <!-- Contenu injecté via JS -->
      </div>
    </div>
  </div>
</div>

<script>
// Normalisation pour filtre département
function normalize(s) {
    return (s || '')
        .toString()
        .normalize('NFKD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/\s+/g, ' ')
        .trim()
        .toLowerCase();
}

function applyFilters() {
    const select = document.getElementById('deptFilter');
    const selectedValue = select.value; // ici c'est l'ID du département
    const rows = document.querySelectorAll('#dataTable tbody tr');

    rows.forEach(row => {
        const deptCell = row.cells[5] ? row.cells[5].textContent.trim() : '';
        let show = true;

        if (selectedValue !== 'all') {
            const option = select.querySelector(`option[value="${selectedValue}"]`);
            const selectedText = option ? option.textContent.trim() : '';
            show = deptCell === selectedText;
        }

        row.style.display = show ? '' : 'none';
    });
}

function afficherPointageDetail(data) {
    const modalBody = document.getElementById('pointageModalBody');
    modalBody.innerHTML = '';

    const nom = data.nom || "l'employé";
    const prenom = data.prenom || "";
    const etat = data.etat || "Absent";
    const totalHeures = data.total_heures || "00:00:00";
    const totalRetard = data.retard || "00:00:00";
    const totalPause = data.pause || "00:00:00";
    const totalSup = data.heures_supp || "00:00:00";

    let html = `
        <h4>Pointage détaillé de ${nom} ${prenom}</h4>
        <p>État : <strong>${etat}</strong></p>
        <p>Total heures : <strong>${totalHeures}</strong></p>
        <p>Total retard : <strong>${totalRetard}</strong></p>
        <p>Total pause : <strong>${totalPause}</strong></p>
        <p>Total heures sup : <strong>${totalSup}</strong></p>
    `;

    // déterminer le nombre maximum de sessions pour construire l'entête
    let maxSessions = 0;
    for (const date in data.dates) {
        maxSessions = Math.max(maxSessions, data.dates[date].sessions.length);
    }

    html += `
        <div class="table-responsive">
            <table class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th>Date</th>
    `;

    for (let i = 1; i <= maxSessions; i++) {
        html += `<th>Arrivée ${i}</th><th>Départ ${i}</th>`;
    }

    html += `<th>Total journée</th><th>Retard</th><th>Pause</th><th>Heures sup</th>`;
    html += `</tr></thead><tbody>`;

    for (const date in data.dates) {
        const dayData = data.dates[date];
        const sessions = dayData.sessions || [];
        const isAbsent = dayData.etat === 'Absent';

        let totalSecDay = 0, retardDay = 0, pauseDay = 0, supDay = 0;
        let prevDepart = null;

        sessions.forEach(s => {
            const arriveeSec = new Date(`1970-01-01T${s.arrivee}Z`).getTime() / 1000;
            const departSec = s.depart ? new Date(`1970-01-01T${s.depart}Z`).getTime() / 1000 : 0;

            totalSecDay += (departSec - arriveeSec);
            if (prevDepart) pauseDay += Math.max(0, arriveeSec - prevDepart);
            prevDepart = departSec;

            retardDay += s.retardSec || 0;
            supDay += s.supSec || 0;
        });

        const totalDayStr = isAbsent ? '00:00:00' : new Date(totalSecDay * 1000).toISOString().substr(11,8);
        const retardStr = isAbsent ? '00:00:00' : new Date(retardDay * 1000).toISOString().substr(11,8);
        const pauseStr = isAbsent ? '00:00:00' : new Date(pauseDay * 1000).toISOString().substr(11,8);
        const supStr = isAbsent ? '00:00:00' : new Date(supDay * 1000).toISOString().substr(11,8);

        html += `<tr><td>${date}</td>`;

        sessions.forEach(s => {
            html += `<td>${s.arrivee}</td><td>${s.depart || '-'}</td>`;
        });

        // colonnes vides si moins de sessions
        const missing = maxSessions - sessions.length;
        for (let i = 0; i < missing; i++) html += `<td>-</td><td>-</td>`;

        html += `<td>${totalDayStr}</td><td>${retardStr}</td><td>${pauseStr}</td><td>${supStr}</td></tr>`;
    }

    html += `</tbody></table></div>`;
    modalBody.innerHTML = html;

    $('#pointageModal').modal('show');
}

// Fetch pointage individuel
function releverPresenceInd(idEmploye) {
    fetch(`/presence/individuelle/${idEmploye}`, { method: 'POST' })
        .then(res => res.json())
        .then(data => afficherPointageDetail(data.success))
        .catch(err => alert('Erreur réseau ou JSON : ' + err));
}

// Fetch pointage groupe (affiche premier employé pour exemple)
function releverPresenceGroupe() {
    const dept = document.getElementById('deptFilter').value;
    fetch(`/presence/groupe/${dept}`, { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            const firstId = Object.keys(data.success)[0];
            afficherPointageDetail(data.success[firstId]);
        })
        .catch(err => alert('Erreur réseau ou JSON : ' + err));
}
</script>

<?php include "footer.php" ?>
