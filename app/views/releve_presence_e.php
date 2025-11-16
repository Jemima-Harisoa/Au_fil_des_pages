<?php include "headerE.php"; ?>

<!-- Modal -->
<div class="modal fade" id="pointageModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Détail du pointage</h5>
<button class="btn btn-sm btn-success ml-3"
        id="btnExportExcel"
        style="display:none;">
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
    <div class="table-responsive">
        <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($allPresence['nom_personne'] ?? '') ?></td>
                    <td><?= htmlspecialchars($allPresence['prenom'] ?? '') ?></td>
                    <td>
                        <button type="button" 
                                class="btn btn-sm btn-info" 
                                onclick="releverPresenceInd(<?= $allPresence['id_employe'] ?>)">
                            Voir le pointage
                        </button>
                    </td>
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


    // export Excel
    const btnExport = document.getElementById('btnExportExcel');
    btnExport.style.display = "inline-block";
    btnExport.onclick = () => exporterReleveCSV(data.id_employe);
}



function exporterReleveCSV(idEmploye) {
    console.log("Exporter appelé avec ID :", idEmploye); // <-- debug
    if (!idEmploye || idEmploye === 0) {
        alert("Impossible d'exporter : ID de l'employé manquant !");
        return;
    }
    window.open(`/presence/export/csv/${idEmploye}`, '_blank');
}



function releverPresenceInd(idEmploye) {
    fetch(`/presence/individuelle/${idEmploye}`, { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            afficherPointageDetail(data.success);
        })
        .catch(err => alert('Erreur réseau ou JSON : ' + err));
}
</script>

<?php include "footer.php"; ?>
