<?php include "headerA.php"; ?>

<div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">Pointage des employés</h6>

    <!-- Filtre département -->
    <div class="form-inline">
        <label class="mr-2">Département :</label>
        <select id="deptFilter" class="form-control mr-3" onchange="applyFilters()">
            <option value="all">Tous les départements</option>
            <?php foreach ($allDepts as $d): ?>
                <option value="<?= htmlspecialchars($d['id_departement']) ?>">
                    <?= htmlspecialchars($d['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label class="mr-2">Du :</label>
        <input type="date" id="debutPeriode" class="form-control mr-2">
        <label class="mr-2">Au :</label>
        <input type="date" id="finPeriode" class="form-control mr-2">

        <button type="button" class="btn btn-sm btn-success" onclick="releverPresenceGroupeAvecPeriode()">
            Relève présence groupe
        </button>
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
                            <button type="button" class="btn btn-sm btn-info" 
                                    onclick="releverPresenceIndAvecPeriode(<?= $emp['id_employe'] ?>)">
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
    return (s || '').toString().normalize('NFKD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, ' ').trim().toLowerCase();
}

function applyFilters() {
    const select = document.getElementById('deptFilter');
    const selectedValue = select.value;
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

// Fonction d'affichage du relevé détaillé
function afficherPointageDetail(data) {
    const modalBody = document.getElementById('pointageModalBody');
    modalBody.innerHTML = '';

    const nom = data.nom || "l'employé";
    const prenom = data.prenom || "";
    const totalHeures = data.total_heures || "00:00:00";
    const totalRetard = data.retard || "00:00:00";
    const totalPause = data.pause || "00:00:00";
    const totalSup = data.heures_supp || "00:00:00";

    let html = `
        <h4>Pointage détaillé de ${nom} ${prenom}</h4>
        <p>Total heures : <strong>${totalHeures}</strong></p>
        <p>Total retard : <strong>${totalRetard}</strong></p>
        <p>Total pause : <strong>${totalPause}</strong></p>
        <p>Total heures sup : <strong>${totalSup}</strong></p>
    `;

    html += `<div class="table-responsive">
                <table class="table table-bordered table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Période</th>
                            <th>Arrivée</th>
                            <th>Départ</th>
                            <th>Total période</th>
                            <th>Retard</th>
                            <th>Pause</th>
                            <th>Heures sup</th>
                            <th>État</th>
                        </tr>
                    </thead>
                    <tbody>`;

    for (const date in data.dates) {
        const dayData = data.dates[date];
        ['matin','apres_midi'].forEach(periode => {
            const sessData = dayData[periode];
            const sessions = (sessData && sessData.sessions) || [];
            const isAbsent = (!sessions || sessions.length === 0);
            const etat = isAbsent ? (dayData.etat === 'À venir' ? 'À venir' : 'Absent') : 'Présent';

            let totalSec = 0, retardSec = 0, pauseSec = 0, supSec = 0;
            sessions.forEach(s => {
                totalSec += s.depart && s.arrivee ? (new Date(`1970-01-01T${s.depart}Z`).getTime()/1000 - new Date(`1970-01-01T${s.arrivee}Z`).getTime()/1000) : 0;
                retardSec += s.retardSec || 0;
                supSec += s.supSec || 0;
                pauseSec += s.pauseSec || 0;
            });

            const totalStr = isAbsent ? '00:00:00' : new Date(totalSec*1000).toISOString().substr(11,8);
            const retardStr = isAbsent ? '00:00:00' : new Date(retardSec*1000).toISOString().substr(11,8);
            const pauseStr = isAbsent ? '00:00:00' : new Date(pauseSec*1000).toISOString().substr(11,8);
            const supStr = isAbsent ? '00:00:00' : new Date(supSec*1000).toISOString().substr(11,8);

            const arriveeStr = sessions[0]?.arrivee || '-';
            const departStr = sessions[0]?.depart || '-';

            html += `<tr>
                <td>${date}</td>
                <td>${periode === 'matin' ? 'Matin' : 'Après-midi'}</td>
                <td>${arriveeStr}</td>
                <td>${departStr}</td>
                <td>${totalStr}</td>
                <td>${retardStr}</td>
                <td>${pauseStr}</td>
                <td>${supStr}</td>
                <td>${etat}</td>
            </tr>`;
        });
    }

    html += `</tbody></table></div>`;
    modalBody.innerHTML = html;
    $('#pointageModal').modal('show');
}
function releverPresenceIndAvecPeriode(idEmploye) {
    const debut = document.getElementById('debutPeriode').value;
    const fin = document.getElementById('finPeriode').value;

    let url = `/presence/individuelle/${idEmploye}`;

    if (debut && fin) url += `?debut=${debut}&fin=${fin}`;

    fetch(url)
        .then(res => res.json())
        .then(data => afficherPointageDetail(data.success))
        .catch(err => alert('Erreur réseau ou JSON : ' + err));
}

function releverPresenceGroupeAvecPeriode() {
    const dept = document.getElementById('deptFilter').value;
    const debut = document.getElementById('debutPeriode').value;
    const fin = document.getElementById('finPeriode').value;

    let url = `/presence/groupe/${dept}`;

    if (debut && fin) url += `?debut=${debut}&fin=${fin}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            const firstId = Object.keys(data.success)[0];
            afficherPointageDetail(data.success[firstId]);
        })
        .catch(err => alert('Erreur réseau ou JSON : ' + err));
}

</script>

<?php include "footer.php" ?>
