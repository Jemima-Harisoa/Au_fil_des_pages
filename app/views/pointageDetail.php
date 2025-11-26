<?php include "headerA.php"; ?>

<div class="container mt-4" id="pointageDetailContainer">
<script>
    function afficherPointageDetail(data) {
    const container = document.getElementById('pointageDetailContainer');
    container.innerHTML = ''; // vider avant d'ajouter

    const nom = data.nom || "l'employé";
    const prenom = data.prenom || "";
    const etat = data.etat || "Absent";
    const totalHeures = data.total_heures || "00:00:00";

    let html = `
        <h3>Pointage détaillé de ${nom} ${prenom}</h3>
        <p>État : <strong>${etat}</strong></p>
        <p>Total heures : <strong>${totalHeures}</strong></p>
    `;

    if (data.dates && Object.keys(data.dates).length > 0) {
        html += `<div class="table-responsive">
            <table class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Arrivée</th>
                        <th>Départ</th>
                        <th>Arrivée</th>
                        <th>Départ</th>
                        <th>Total journée</th>
                    </tr>
                </thead>
                <tbody>
        `;

        for (const date in data.dates) {
            const entries = data.dates[date];
            let totalSecondsDay = 0;

            entries.forEach(e => {
                if (e.depart) {
                    totalSecondsDay += new Date(`1970-01-01T${e.depart}Z`) - new Date(`1970-01-01T${e.arrivee}Z`);
                }
            });

            const totalDay = new Date(totalSecondsDay).toISOString().substr(11, 8);

            html += `<tr>
                <td>${date}</td>`;

            entries.forEach(e => {
                html += `<td>${e.arrivee}</td>
                         <td>${e.depart || '-'}</td>`;
            });

            // Si moins de 2 entrées
            if (entries.length < 2) html += `<td>-</td><td>-</td>`;

            html += `<td>${totalDay}</td></tr>`;
        }

        html += `</tbody></table></div>`;
    } else {
        html += `<p>Aucun pointage trouvé pour cet employé.</p>`;
    }

    container.innerHTML = html;
}

</script>
</div>

<?php include "footer.php"; ?>
