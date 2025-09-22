let message_container = document.getElementById("message-container");
let button = document.getElementById("bouton-planification");
let tableBody = document.querySelector("#dataTable tbody");

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

        // vider le tbody
        tableBody.innerHTML = "";

        // vérifier si des résultats existent
        if (data.resultat && data.resultat.length > 0) {
            data.resultat.forEach(candidat => {
                let row = document.createElement("tr");

                row.innerHTML = `
                    <td>${candidat.name || ""}</td>
                    <td>${candidat.profil || ""}</td>
                    <td>${candidat.score_test || ""}</td>
                    <td>${candidat.date_test || ""}</td>
                    <td>${candidat.date_heure_entretien || ""}</td>
                    <td>
                        <button class="btn btn-sm btn-primary">Action</button>
                    </td>
                `;

                tableBody.appendChild(row);
            });
        } else {
            let row = document.createElement("tr");
            row.innerHTML = `<td colspan="6" class="text-center">Aucun résultat trouvé</td>`;
            tableBody.appendChild(row);
        }

        // afficher message
        let p = document.createElement("p");
        p.style.color = (data.status === 200) ? "green" : "red";
        p.textContent = data.message;
        message_container.appendChild(p);
    })
    .catch(error => {
        console.error("Erreur fetch :", error);
    });
});
