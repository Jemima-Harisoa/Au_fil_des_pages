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
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Formulaire - Contrat de Travail (CDI)</h1>
                            </div>
                            <form class="user">
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
    </script>
</body>
</html>
