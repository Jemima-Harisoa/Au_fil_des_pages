<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Contrat de Travail - Liste des candidats</title>

    <!-- Fonts et styles -->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Liste des Candidats retenus pour le poste</h1>
    <p class="mb-4">Informations sur les candidats, leurs scores, CV et contrats.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Candidats</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Poste</th>
                            <th>Contact</th>
                            <th>Score Test</th>
                            <th>Score Entretien</th>
                            <th>CV</th>
                            <th>Contrat</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Nom</th>
                            <th>Poste</th>
                            <th>Contact</th>
                            <th>Score Test</th>
                            <th>Score Entretien</th>
                            <th>CV</th>
                            <th>Contrat</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php if (!empty($rows)): ?>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nom'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['poste'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['contact'] ?? '') ?></td>
                                    <td><?= htmlspecialchars((string)($row['score_test'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($row['score_entretien'] ?? '')) ?></td>
                                    <td>
                                        <?php if (!empty($row['cv'])): ?>
                                            <a href="<?= htmlspecialchars($row['cv']) ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-file-pdf"></i> CV
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- L'url ici est intercepter par le js -->
                                        <a href="<?= htmlspecialchars($row['contrat_url']) ?>" class="btn btn-sm <?=$row['contrat_class']?> btn-contrat">
                                            <i class="fas fa-file-signature"></i> <?= htmlspecialchars($row['contrat_label']) ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Aucun candidat trouvé</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Conteneur pour aperçu PDF -->
<div id="contractPreview" style="display:none; position:fixed; top:50px; left:50%; transform:translateX(-50%); z-index:1000; width:600px; height:800px; border:1px solid #ccc; background:#fff;">
    <div style="text-align:right; padding:5px;">
        <button id="closePreview" class="btn btn-sm btn-danger">Fermer</button>
    </div>
    <iframe id="contractIframe" style="width:100%; height:95%;" frameborder="0"></iframe>
</div>

<!-- Scripts -->
<script src="../js/skecth/pdf.js"></script>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="../js/demo/datatables-demo.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    // Clic sur bouton "Voir contrat"
    $(document).on("click", ".btn-contrat", function(e) {
        
        // Vérifier le texte du lien
        if ($(this).text().trim() == "Générer Contrat") return;
        
        e.preventDefault(); // Empêche le lien par défaut

        const url = $(this).attr("href"); // URL du JSON relatif
        console.log("Chargement JSON:", url);

        $.getJSON(url)
            .done(function(data) {
                console.log("Data reçue:", data);
                const docDef = buildContractDoc(
                    data.clause_general,  // clauses
                    data.employeur,       // employeur
                    data.employe          // employé
                );

                pdfMake.createPdf(docDef).getBlob(function(blob) {
                    const pdfUrl = URL.createObjectURL(blob);
                    $("#contractIframe").attr("src", pdfUrl);
                    $("#contractPreview").fadeIn();
                });
            })
            .fail(function(err) {
                console.error("Erreur lors du chargement du contrat :", err);
                alert("Impossible de charger le contrat !");
            });
    });

    // Fermer le preview
    $("#closePreview").on("click", function() {
        $("#contractPreview").fadeOut();
    });
});
</script>

</body>
</html>
