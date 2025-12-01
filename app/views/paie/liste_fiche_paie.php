<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des fiches de paie</title>

    <link rel="stylesheet" href="/vendor/fontawesome-free/css/bootstrap.min.css">

    <!-- CDN Font Awesome -->
    <link href="/vendor/fontawesome-free/css/all2.min.css" rel="stylesheet" type="text/css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        @media (max-width:700px){
        .main-container{
            flex-direction: column;
            }
        }
        .container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 80px);
            gap: 20px;
            overflow-y: scroll;
        }

        .fiche {
            width: 80%;
            margin-left: 10%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .infos {
            margin-bottom: 15px;
            color: #555;
            font-weight: bold;
            font-size: 1.5em;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-pdf {
            background: #e98f53ff;
            color: white;
        }

        .btn-details {
            background: #0275d8;
            color: white;
        }

        .btn:hover {
            opacity: 0.85;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Liste des fiches de paie</h2>

<div class="container" id="fiche-container">
    <!-- Les fiches seront générées ici automatiquement -->
</div>

<script>
    const mois = [
        "Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
        "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
    ];

    const container = document.getElementById("fiche-container");

    const date = new Date();
    const moisActuel = date.getMonth();   // 0 = Janvier, 11 = Décembre
    const annee = date.getFullYear();

    for (let i = 0; i <= moisActuel; i++) {
        const fiche = document.createElement("div");
        fiche.className = "fiche";

        fiche.innerHTML = `
            <div class="infos">${mois[i]} ${annee}</div>

            <div class="buttons">
                <button class="btn btn-pdf">
                    <i class="fa-solid fa-file-pdf"></i> Export PDF
                </button>

                <button class="btn btn-details">
                    <i class="fas fa-eye"></i> Voir détails
                </button>
            </div>
        `;

        container.appendChild(fiche);
    }
</script>

</body>
</html>
