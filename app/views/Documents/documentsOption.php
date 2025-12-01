<?php
use app\models\Documents\documentsModels;

$data = $data ?? [];

// Traitement : création du dossier
$message = null;
if (isset($_GET['creer']) && is_numeric($_GET['creer'])) {
    $id = (int)$_GET['creer'];
    $resultat = documentsModels::creerDossierCompletEmploye($id);

    if ($resultat['success']) {
        $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6 shadow">
            <strong>Succès !</strong> ' . $resultat['message'] . '<br>
            <code class="text-sm">' . htmlspecialchars($resultat['path']) . '</code>
        </div>';
    } else {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6 shadow">
            <strong>Erreur :</strong> ' . htmlspecialchars($resultat['message']) . '
        </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Dossiers Employés</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen py-12">

<div class="max-w-6xl mx-auto px-6">

    <!-- Titre + Message -->
    <div class="text-center mb-10">
        <h1 class="text-5xl font-bold text-gray-800 mb-4">Gestion des Dossiers Employés</h1>
        <p class="text-xl text-gray-600">Créez automatiquement un dossier structuré pour chaque employé</p>
    </div>

    <?php if ($message): ?>
        <div class="max-w-4xl mx-auto mb-8">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- Grille des employés -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($data[1] as $emp): ?>
            <?php
                $nomComplet = trim($emp['nom'] . ' ' . $emp['prenom']);
                $dossierNom = $emp['nom'] . '_' . $emp['prenom'] . '_' . $emp['id_employe'];
                // Chemin relatif depuis views/Documents vers public/Documents
                $dossierChemin = dirname(__DIR__, 2) . '/public/Documents/' . $dossierNom;
                $dossierExiste = is_dir($dossierChemin);
            ?>
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover transition-all duration-300 <?= $dossierExiste ? 'ring-4 ring-green-400' : '' ?>">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-full flex items-center justify-center text-2xl font-bold">
                                <?= strtoupper(substr($emp['prenom'], 0, 1) . substr($emp['nom'], 0, 1)) ?>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold"><?= htmlspecialchars($nomComplet) ?></h3>
                                <p class="text-sm opacity-90"><?= htmlspecialchars($emp['poste'] ?? 'Poste non défini') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>ID Employé :</span>
                            <strong>#<?= $emp['id_employe'] ?></strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Département :</span>
                            <strong><?= htmlspecialchars($emp['nom_departement'] ?? 'Non défini') ?></strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Embauche :</span>
                            <strong><?= $emp['date_embauche'] ? date('d/m/Y', strtotime($emp['date_embauche'])) : '—' ?></strong>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <?php if ($dossierExiste): ?>
                            <div class="text-center">
                                <div class="inline-flex items-center gap-3 text-green-600 font-bold text-lg">
                                    <i class="fas fa-check-circle text-3xl"></i>
                                    <span>Dossier déjà créé</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 break-all">
                                    public/Documents/<?= htmlspecialchars($dossierNom) ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <a href="?creer=<?= $emp['id_employe'] ?>"
                               class="w-full block text-center bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 rounded-xl shadow-lg transform hover:scale-105 transition duration-200">
                                <i class="fas fa-folder-plus mr-2"></i>
                                Créer le dossier complet
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Info chemin -->
    <div class="mt-12 text-center bg-white/80 backdrop-blur rounded-2xl p-6 shadow-lg">
        <p class="text-gray-600">
            <strong>Chemin de stockage :</strong><br>
            <code class="text-sm bg-gray-800 text-white px-4 py-2 rounded mt-2 inline-block">
                <?php 
                $publicPath = dirname(__DIR__, 2) . '/public/Documents';
                echo htmlspecialchars(realpath($publicPath) ?: $publicPath); 
                ?>
            </code>
        </p>
    </div>
</div>

</body>
</html>