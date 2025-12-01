<?php include "headerE.php"; ?>

<!-- Modal -->
<div class="modal fade" id="pointageModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Détail du pointage</h5>
        <button class="btn btn-sm btn-success ml-3" id="btnExportExcel" style="display:none;">
            Exporter Excel
        </button>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body" id="pointageModalBody"></div>
    </div>
  </div>
</div>

<div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">Pointage de l'employé</h6>
</div>

<div class="card-body">
    <div class="form-inline mb-3">
        <label class="mr-2">Du :</label>
        <input type="date" id="debutPeriode" class="form-control mr-2">
        <label class="mr-2">Au :</label>
        <input type="date" id="finPeriode" class="form-control mr-2">
        <button type="button" class="btn btn-sm btn-info" onclick="releverPresenceIndAvecPeriode(<?= $allPresence['id_employe'] ?>)">
            Voir le pointage
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($allPresence['nom_personne'] ?? '') ?></td>
                    <td><?= htmlspecialchars($allPresence['prenom'] ?? '') ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
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

    const btnExport = document.getElementById('btnExportExcel');
    btnExport.style.display = "inline-block";
    btnExport.onclick = () => exporterReleveCSV(data.id_employe,debut,fin);
}

function exporterReleveCSV(idEmploye, debutPeriode = null, finPeriode = null) {
    if (!idEmploye || idEmploye === 0) {
        alert("Impossible d'exporter : ID de l'employé manquant !");
        return;
    }
    let url = `/presence/export/csv/${idEmploye}`;
    if (debutPeriode && finPeriode) {
        url += `?debut=${debutPeriode}&fin=${finPeriode}`;
    }
    window.open(url, '_blank');
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

<?php include "footer.php"; ?>
