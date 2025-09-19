<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Contrat de Travail - Liste des candidats</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

    <div class="container-fluid">
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Liste des Candidats retenu pour le poste</h1>
        <p class="mb-4">Informations sur les candidats, leurs scores, CV et contrats.</p>

        <!-- DataTables Example -->
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
                                <th>Adresse</th>
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
                                <th>Adresse</th>
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
                                        <td><?= htmlspecialchars($row['adresse'] ?? '') ?></td>
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
                                            <a href="<?= htmlspecialchars($row['contrat_url']) ?>" class="btn btn-sm <?= $row['contrat_class'] ?>">
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

       <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../js/demo/datatables-demo.js"></script>
</body>
</html>
