<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Contrat de Travail - Formulaire</title>

    <!-- Fonts et styles -->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-5 p-3">
                        <h4 class="text-center mb-3">Aperçu du Contrat</h4>
                        <div id="apercuContrat">Le contrat s'affichera ici au fur et à mesure du remplissage...</div>
                    </div>

                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Formulaire - Contrat de Travail</h1>
                            </div>

                            <form class="user" method="post" action="/migration/contrat/register?id_candidat=<?= htmlspecialchars($data['candidat']['id_candidat'] ?? '') ?>">
                                <!-- Type de contrat -->
                                <h5 class="mb-3">Type de contrat</h5>
                                <div class="form-group mb-3">
                                    <select class="form-control" id="typeContrat">
                                        <option value="">Sélectionner le type</option>
                                        <?php if (!empty($data['type_contrats'])): ?>
                                            <?php foreach ($data['type_contrats'] as $tc): ?>
                                                <option value="<?= htmlspecialchars($tc['id_type_contrat']) ?>">
                                                    <?= htmlspecialchars($tc['nom']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Informations Travailleur -->
                                <h5 class="mb-3">Informations sur le travailleur</h5>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" placeholder="Noms et prénoms"
                                           value="<?= htmlspecialchars(($data['personne']['nom'] ?? '') . ' ' . ($data['personne']['prenom'] ?? '')) ?>">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="date" class="form-control form-control-user" placeholder="Date de naissance"
                                               value="<?= htmlspecialchars($data['personne']['date_naissance'] ?? '') ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" placeholder="Lieu de naissance"
                                               value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" placeholder="Fils ou fille de"
                                           value="">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" placeholder="Nationalité"
                                               value="">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" placeholder="Domicile à Madagascar"
                                               value="">
                                    </div>
                                </div>

                                <!-- Généralités -->
                                <h5 class="mb-3">Dispositions générales</h5>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="date" class="form-control form-control-user" placeholder="Date de prise d’effet"
                                               value="">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control form-control-user" placeholder="Durée période d’essai (mois)"
                                               value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" placeholder="Lieu d’emploi"
                                           value="<?= htmlspecialchars($data['candidat']['poste'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" placeholder="Poste occupé / Fonctions"
                                           value="<?= htmlspecialchars($data['candidat']['poste'] ?? '') ?>">
                                </div>

                                <!-- Rémunération -->
                                <h5 class="mb-3">Rémunération</h5>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="number" class="form-control form-control-user" id="salaire"
                                               placeholder="Salaire mensuel (Ar)" value="">
                                    </div>
                                </div>

                                <!-- Avantages -->
                                <h6 class="mb-2">Avantages</h6>
                                <div class="form-group row align-items-center">
                                    <div class="col-sm-4 mb-3 mb-sm-0">
                                        <select class="form-control" id="typeAvantage">
                                            <option value="">Type</option>
                                            <option value="nature">Nature</option>
                                            <option value="espece">Espèce</option>
                                            <option value="social">Social</option>
                                            <option value="exceptionnel">Exceptionnel</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" id="descAvantage" placeholder="Description de l’avantage">
                                    </div>
                                    <div class="col-sm-2 text-center d-none d-md-inline">
                                        <button type="button" class="rounded-circle border-0 btn btn-primary" id="btnAddAvantage"
                                                onclick="ajouterAvantage()">+</button>
                                    </div>
                                </div>
                                <ul id="listeAvantages" class="list-group mb-3"></ul>

                                <input type="submit" value="Générer le contrat" class="btn btn-primary btn-user btn-block">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../../js/sb-admin-2.min.js"></script>

    <script>
        function ajouterAvantage() {
            const type = document.getElementById("typeAvantage").value;
            const desc = document.getElementById("descAvantage").value.trim();
            if (!type || !desc) { alert("Veuillez sélectionner un type et entrer une description."); return; }

            const liste = document.getElementById("listeAvantages");
            const li = document.createElement("li");
            li.className = "list-group-item d-flex justify-content-between align-items-center";
            li.innerHTML = `<span><strong>${type.toUpperCase()}:</strong> ${desc}</span>
                            <a href="#" class="btn btn-danger btn-circle" onclick="this.closest('li').remove()">
                                <i class="fas fa-trash"></i>
                            </a>
                            <input type="hidden" name="avantages[]" value="${type}:${desc}">`;
            liste.appendChild(li);

            document.getElementById("typeAvantage").value = "";
            document.getElementById("descAvantage").value = "";
        }

        function updateContrat() {
            const getVal = ph => {
                const el = document.querySelector(`input[placeholder="${ph}"]`);
                return el ? el.value.trim() : '';
            };

            const typeContrat = document.getElementById("typeContrat").value || 'Indéterminée';
            const nom = getVal("Noms et prénoms");
            const dateNaiss = getVal("Date de naissance");
            const lieuNaiss = getVal("Lieu de naissance");
            const parent = getVal("Fils ou fille de");
            const nationalite = getVal("Nationalité");
            const domicile = getVal("Domicile à Madagascar");
            const dateEffet = getVal("Date de prise d’effet");
            const essai = getVal("Durée période d’essai (mois)");
            const dureeCDD = getVal("Durée CDD (si applicable)");
            const lieuEmploi = getVal("Lieu d’emploi");
            const poste = getVal("Poste occupé / Fonctions");
            const salaire = document.getElementById("salaire").value || '';
            const avantages = Array.from(document.querySelectorAll('#listeAvantages li span')).map(s => s.innerText);
            const avantagesText = avantages.length ? '- ' + avantages.join('\n- ') : 'Aucun';

            // Partie Travailleurs recrutés à l’extérieur
            const residence = getVal("Résidence habituelle (embauche/rapatriement)");
            const adresseExterieure = getVal("Adresse exacte dans ce pays");

            const today = new Date();
            const dateJour = `${String(today.getDate()).padStart(2,'0')}/${String(today.getMonth()+1).padStart(2,'0')}/${today.getFullYear()}`;

            let partieExterieur = '';
            if (residence || adresseExterieure) {
                partieExterieur = `
II – PARTIE RESERVEE AUX TRAVAILLEURS RECRUTES A L’EXTERIEUR DU TERRITOIRE :
Résidence habituelle : ${residence || '……………'}
Adresse exacte : ${adresseExterieure || '……………'}

Art.10 : L’employeur prendra en charge / ne prendra pas en charge les frais de déplacement...
Art.11 : L’employeur prend en charge les frais de déplacement du travailleur et des membres de sa famille...
Art.12 : Le travailleur bénéficiera d’un congé payé...`;
            }

            const texte = `
CONTRAT DE TRAVAIL

TRAVAILLEUR :
Noms et prénoms : ${nom}
Né le : ${dateNaiss} à ${lieuNaiss}
Fils ou fille de : ${parent}
De nationalité : ${nationalite}
Domicile à Madagascar : ${domicile}

I – DISPOSITIONS GENERALES :
Art.1 : Le contrat prend effet le ${dateEffet}
Durée : ${typeContrat === 'CDD' ? dureeCDD + ' /an (CDD)' : 'Indéterminée'}
Période d’essai : ${essai} mois
Lieu d’emploi : ${lieuEmploi}
Poste : ${poste}
Salaire : ${salaire} Ar
Avantages : ${avantagesText}

${partieExterieur}

Fait à Antananarivo, le ${dateJour}

SIGNATURE DU TRAVAILLEUR          SIGNATURE DE L’EMPLOYEUR`;

            document.getElementById('apercuContrat').innerText = texte;
        }

        document.querySelectorAll('.form-control-user, #salaire, #typeContrat').forEach(input => {
            input.addEventListener('input', updateContrat);
        });
        const observer = new MutationObserver(updateContrat);
        observer.observe(document.getElementById('listeAvantages'), { childList: true });
    </script>
</body>
</html>
