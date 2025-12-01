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
<input type="date" id="debutPeriode" class="form-control mr-2" value="2025-11-11">

<label class="mr-2">Au :</label>
<input type="date" id="finPeriode" class="form-control mr-2" value="2025-12-20">


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
    const debut = data.debut || "";
    const fin = data.fin || "";

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

        // Totaux journaliers en secondes
        let dayTotal = 0, dayRetard = 0, dayPause = 0, daySup = 0;

        ['matin','apres_midi'].forEach(periode => {
            const periodeLabel = periode === 'matin' ? 'Matin' : 'Après-midi';
            const sessData = dayData[periode] || {};
            const sessions = sessData.sessions || [];
            let etat = 'Absent';

            if (sessions.length === 0 && sessData.present === true) {
                // Présent demi-journée mais pas de session enregistrée
                etat = `Présent ${periodeLabel}`;
                html += `<tr>
                    <td>${date}</td>
                    <td>${periodeLabel}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>00:00:00</td>
                    <td>00:00:00</td>
                    <td>00:00:00</td>
                    <td>00:00:00</td>
                    <td>${etat}</td>
                </tr>`;
            } else if (sessions.length === 0) {
                // Absent
                html += `<tr>
                    <td>${date}</td>
                    <td>${periodeLabel}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>00:00:00</td>
                    <td>00:00:00</td>
                    <td>00:00:00</td>
                    <td>00:00:00</td>
                    <td>${etat}</td>
                </tr>`;
            } else {
                // Présent avec session(s)
                etat = 'Présent';
                sessions.forEach((s, index) => {
                    // Convert session time
                    const a = s.arrivee ? new Date(`1970-01-01T${s.arrivee}Z`).getTime() / 1000 : 0;
                    const d = s.depart ? new Date(`1970-01-01T${s.depart}Z`).getTime() / 1000 : 0;
                    let duration = d - a;

                    // Cas heure de nuit (si départ < arrivée, ajouter 24h)
                    if (duration < 0) duration += 24*3600;

                    // Totaux journaliers
                    dayTotal += duration;
                    dayRetard += s.retardSec || 0;
                    dayPause += s.pauseSec || 0;
                    daySup += s.supSec || 0;

                    // Convert durations to HH:MM:SS
                    const durStr = new Date(duration * 1000).toISOString().substr(11,8);

                    html += `<tr>
                        <td>${index===0 ? date : ''}</td>
                        <td>${index===0 ? periodeLabel : ''}</td>
                        <td>Arrivée ${index+1}: <strong>${s.arrivee}</strong></td>
                        <td>Départ ${index+1}: <strong>${s.depart ?? '-'}</strong></td>
                        <td>${durStr}</td>
                        <td>${new Date((s.retardSec||0)*1000).toISOString().substr(11,8)}</td>
                        <td>${new Date((s.pauseSec||0)*1000).toISOString().substr(11,8)}</td>
                        <td>${new Date((s.supSec||0)*1000).toISOString().substr(11,8)}</td>
                        <td>${etat}</td>
                    </tr>`;
                });
            }
        });

        // Ligne TOTAL JOURNÉE
        html += `<tr style="background:#dff0d8; font-weight:bold;">
                    <td colspan="2">TOTAL JOURNÉE</td>
                    <td colspan="2"></td>
                    <td>${new Date(dayTotal * 1000).toISOString().substr(11,8)}</td>
                    <td>${new Date(dayRetard * 1000).toISOString().substr(11,8)}</td>
                    <td>${new Date(dayPause * 1000).toISOString().substr(11,8)}</td>
                    <td>${new Date(daySup * 1000).toISOString().substr(11,8)}</td>
                    <td></td>
                 </tr>`;
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
