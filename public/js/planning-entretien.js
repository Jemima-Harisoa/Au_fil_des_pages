let button = document.getElementById("bouton-planification");
let table = $('#dataTable').DataTable();

button.addEventListener('click', function(e) {
    e.preventDefault();

    fetch("/api/planifier-entretien", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log("Données reçues :", data);

        let date = new Date();

        // vider la DataTable proprement
        table.clear();

        if (data.resultat && data.resultat.length > 0) {
            data.resultat.forEach(candidat => {
                table.row.add([
                    `${candidat.nom} ${candidat.prenom}` || "",
                    date.getFullYear() - new Date(candidat.date_naissance).getFullYear() || "",
                    `${candidat.nom_responsable} ${candidat.prenom_responsable}` || "",
                    candidat.titre || "",
                    candidat.score_test || "",
                    candidat.date_test || "",
                    candidat.date_heure_entretien || "",
                    `<button class="btn btn-sm btn-primary">Reporter</button>
                     <button class="btn btn-sm btn-primary">Rejeter</button>
                     <button class="btn btn-sm btn-primary">Accepter</button>`
                ]);
            });
        } else {
            table.row.add([
                "Aucun résultat trouvé", "", "", "", "", "", "", ""
            ]);
        }

        // re-render
        table.draw();
    })
    .catch(error => {
        console.error("Erreur fetch :", error);
    });
});
