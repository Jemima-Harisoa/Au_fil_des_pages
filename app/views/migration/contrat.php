<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Contrat de Travail - Formulaire</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
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
                            <!-- Aperçu du contrat à gauche -->
            
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Formulaire - Contrat de Travail (CDI)</h1>
                            </div>
                            <form class="user">
                                <!-- Type de contrat -->
                                <h5 class="mb-3">Type de contrat</h5>
                                <div class="form-group mb-3">
                                    <select class="form-control" id="typeContrat">
                                        <option value="">Sélectionner le type</option>
                                        <option value="CDI">CDI</option>
                                        <option value="CDD">CDD</option>
                                    </select>
                                </div>
                                <!-- Informations Travailleur -->
                                <h5 class="mb-3">Informations sur le travailleur</h5>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" placeholder="Noms et prénoms">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="date" class="form-control form-control-user" placeholder="Date de naissance">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" placeholder="Lieu de naissance">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" placeholder="Fils ou fille de">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" placeholder="Nationalité">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" placeholder="Domicile à Madagascar">
                                    </div>
                                </div>

                                <!-- Généralités -->
                                <h5 class="mb-3">Dispositions générales</h5>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="date" class="form-control form-control-user" placeholder="Date de prise d’effet">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control form-control-user" placeholder="Durée période d’essai (mois)">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" placeholder="Lieu d’emploi">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" placeholder="Poste occupé / Fonctions">
                                </div>

                                <!-- Rémunération -->
                                <h5 class="mb-3">Rémunération</h5>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="number" class="form-control form-control-user" id="salaire"
                                            placeholder="Salaire mensuel (Ar)">
                                    </div>
                                </div>

                                <!-- Avantages -->
                                <h6 class="mb-2">Avantages</h6>
                                <div class="form-group row align-items-center">
                                    <!-- Type d’avantage -->
                                    <div class="col-sm-4 mb-3 mb-sm-0">
                                        <select class="form-control" id="typeAvantage">
                                            <option value=""> Type </option>
                                            <option value="nature">Nature</option>
                                            <option value="espece">Espèce</option>
                                            <option value="social">Social</option>
                                            <option value="exceptionnel">Exceptionnel</option>
                                        </select>
                                    </div>

                                    <!-- Description -->
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" id="descAvantage"
                                            placeholder="Description de l’avantage">
                                    </div>

                                    <!-- Bouton Ajouter -->
                                    <div class="col-sm-2 text-center d-none d-md-inline">
                                        <button type="button" class="rounded-circle border-0 btn btn-primary" id="btnAddAvantage"
                                            onclick="ajouterAvantage()">+</button>
                                    </div>
                                </div>

                                <!-- Liste des avantages -->
                                <ul id="listeAvantages" class="list-group mb-3"></ul>


                                <!-- Partie II : Recrutés à l’extérieur -->
                                <h5 class="mb-3">Travailleurs recrutés à l’extérieur</h5>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" placeholder="Résidence habituelle (embauche/rapatriement)">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" placeholder="Adresse exacte dans ce pays">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <select class="form-control">
                                            <option>Prend en charge</option>
                                            <option>Ne prend pas en charge</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control">
                                            <option>Congé annuel</option>
                                            <option>Congé tous les 2 ans</option>
                                            <option>Congé tous les 3 ans</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Signatures -->
                                <h5 class="mb-3">Signatures</h5>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" placeholder="Nom et signature du travailleur">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" placeholder="Nom, qualité et cachet de l’employeur">
                                    </div>
                                </div>

                                <a href="#" class="btn btn-primary btn-user btn-block">
                                    Générer le contrat
                                </a>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="#">Besoin d’aide ?</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <script>
        function ajouterAvantage() {
            const type = document.getElementById("typeAvantage").value;
            const desc = document.getElementById("descAvantage").value.trim();

            if (!type || !desc) {
                alert("Veuillez sélectionner un type et entrer une description.");
                return;
            }

            const liste = document.getElementById("listeAvantages");

            // Création de l’élément li
            const li = document.createElement("li");
            li.className = "list-group-item d-flex justify-content-between align-items-center";

            li.innerHTML = `
                <span><strong>${type.toUpperCase()}:</strong> ${desc}</span>
                <a href="#" class="btn btn-danger btn-circle" onclick="this.closest('li').remove()">
                    <i class="fas fa-trash"></i>
                </a>
                <input type="hidden" name="avantages[]" value="${type}:${desc}">
            `;
            liste.appendChild(li);

            // Reset du formulaire
            document.getElementById("typeAvantage").value = "";
            document.getElementById("descAvantage").value = "";
        }
        // Mise à jour du contrat
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

                // Champs pour partie Travailleurs recrutés à l’extérieur
                const residence = getVal("Résidence habituelle (embauche/rapatriement)");
                const adresseExterieure = getVal("Adresse exacte dans ce pays");

                // Date du jour
                const today = new Date();
                const jour = String(today.getDate()).padStart(2, '0');
                const mois = String(today.getMonth() + 1).padStart(2, '0');
                const annee = today.getFullYear();
                const dateJour = `${jour}/${mois}/${annee}`;

                // Inclure la partie II seulement si un champ est rempli
                let partieExterieur = '';
                if (residence || adresseExterieure) {
                    partieExterieur = `
            II – PARTIE RESERVEE AUX TRAVAILLEURS RECRUTES A L’EXTERIEUR DU TERRITOIRE :
            Résidence habituelle (lieu d’embauche et de rapatriement) : ${residence || '……………'}
            Adresse exacte dans ce pays : ${adresseExterieure || '……………'}

            Art.10 : L’employeur prendra en charge / ne prendra pas en charge les frais de déplacement des membres de la famille du travailleur pendant la période d’essai.
            Art.11 : L’employeur prend en charge les frais de déplacement du travailleur et des membres de sa famille entre le lieu de résidence habituelle et le lieu d’emploi à l’embauche, ainsi qu’à l’expiration ou rupture du contrat.
            Condition du transport : ………………………………………
            Art.12 : Le travailleur bénéficiera d’un congé payé au lieu de sa résidence habituelle tous les ans / deux ans / trois ans.
                    `;
                }

                const texte = `
            CONTRAT DE TRAVAIL

            Entre les contractants ci-dessous,

            EMPLOYEUR :
            NOM / Dénomination :
            Statut juridique :
            Capital social :
            Adresse exacte :
            N° d’identification statistique :
            Représenté par M
            en sa qualité de :

            TRAVAILLEUR :
            Noms et prénoms : ${nom}
            Né le : ${dateNaiss} à ${lieuNaiss}
            Fils ou fille de : ${parent}
            De nationalité : ${nationalite}
            Domicile à Madagascar : ${domicile}

            I – DISPOSITIONS GENERALES :
            Art.1 : Le présent contrat prend effet à compter du ${dateEffet}
            Pour une durée : ${typeContrat === 'CDD' ? dureeCDD + ' /an (CDD)' : 'Indéterminée'}
            Le travailleur accomplira une période d’essai de ${essai} mois pendant laquelle le contrat peut être rompu sans préavis.
            Art.2 : Le lieu d’emploi est ${lieuEmploi}
            Art.3 : Le travailleur est recruté en qualité de ${poste}
            Classification professionnelle : HC
            Art.4 : Il percevra la rémunération suivante :
            Salaire de base : ${salaire} Ar /par mois
            Autres avantages :
            ${avantagesText}
            Art.5 : Le travailleur bénéficie des congés payés conformément à la réglementation en vigueur.
            Art.6 : Il appartient à l’employeur d’affilier le travailleur à la CNaPS et de prendre en charge les soins résultant de maladie du travailleur et des membres de sa famille conformément aux dispositions légales en vigueur.
            Art.7 : La résiliation du contrat à durée indéterminée est subordonnée à un préavis écrit conforme au Décret n° 2007-009 du 09 janvier 2007.
            Art.8 : Pour un CDD, chaque partie doit aviser l’autre de son intention ou non de renouveler le contrat ${typeContrat === 'CDD' ? '(durée CDD: ' + dureeCDD + ' mois)' : ''}. À défaut, une indemnité équivalente au préavis sera due.
            Art.9 : Les parties s’engagent à respecter les dispositions du Code du Travail et de ses textes d’application pour tout ce qui n’est pas prévu dans ce contrat.
            ${partieExterieur}

            Fait à Antananarivo, le ${dateJour}

            SIGNATURE DU TRAVAILLEUR                      SIGNATURE DE L’EMPLOYEUR
            (Noms et qualité, cachet)

            (1) Entreprise individuelle/SARL/Société Anonyme/EURL…
            (2) Rayer les mentions inutiles
            (3) Le CDD ne peut dépasser deux ans
            (4) La période d’essai totale ne peut dépasser six mois
            (5) Salaire minimum pour cadres HC : 187.906 Ar
            (6) Préavis minimum : selon ancienneté et groupe HC
            (7) Un mois/deux mois pour étrangers recrutés à l’extérieur
            (8) Précéder la signature de la mention manuscrite « lu et approuvé »
                `;

                document.getElementById('apercuContrat').innerText = texte;
            }

            // Mise à jour en temps réel
            document.querySelectorAll('.form-control-user, #salaire, #typeContrat').forEach(input => {
                input.addEventListener('input', updateContrat);
            });
            const observer = new MutationObserver(updateContrat);
            observer.observe(document.getElementById('listeAvantages'), { childList: true });
 </script>
</body>
</html>
