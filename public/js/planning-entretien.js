let message_container = document.getElementById("message-container");

fetch("/api/planifier-entretien", {
    method: "GET",
    headers: {
        "Content-Type": "application/json"
    }
})
.then(response => response.json())
.then(data => {
    console.log("Données reçues :", data);
    let p = document.createElement("p");

    if (data.status === 200) {
        p.style.color = "green";
    } else {
        p.style.color = "red";
    }

    p.textContent = data.message;
    message_container.appendChild(p);
})
.catch(error => {
    console.error("Erreur fetch :", error);
});
