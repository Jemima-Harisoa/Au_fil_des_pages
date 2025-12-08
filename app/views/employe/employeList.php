<?php 

    use app\models\EmployeModel;
    // Les données sont déjà dans $data
    // $data[0] = liste des employés
    // $data[1] = liste des départements
    // $data[2] = liste des postes
    
    $employes = $data[0] ?? [];
    $departements_db = $data[1] ?? [];
    $postes_db = $data[2] ?? [];

    // Récupération des filtres GET
    $search = trim($_GET['search'] ?? '');
    $sort_name = $_GET['sort_name'] ?? '';
    $departement_filter = $_GET['departement'] ?? '';
    $poste_filter = $_GET['poste'] ?? '';

    // 1. Filtrage
    $filtered = array_filter($employes, function($emp) use ($search, $departement_filter, $poste_filter) {
        $in_search = empty($search) || 
            stripos($emp['nom'], $search) !== false || 
            stripos($emp['prenoms'], $search) !== false || 
            stripos($emp['departement'] ?? '', $search) !== false || 
            stripos($emp['poste'] ?? '', $search) !== false;

        $in_dept = empty($departement_filter) || ($emp['departement'] ?? '') === $departement_filter;
        $in_poste = empty($poste_filter) || ($emp['poste'] ?? '') === $poste_filter;

        return $in_search && $in_dept && $in_poste;
    });

    // 2. Tri par nom
    if ($sort_name === 'asc') {
        usort($filtered, fn($a, $b) => strcasecmp($a['nom'], $b['nom']));
    } elseif ($sort_name === 'desc') {
        usort($filtered, fn($a, $b) => strcasecmp($b['nom'], $a['nom']));
    }

    // 3. Liste des départements pour le filtre (depuis la BDD)
    $departements = array_column($departements_db, 'nom');
    sort($departements);
    
    // 4. Liste des postes pour le filtre (depuis la BDD)
    $postes = array_column($postes_db, 'nom');
    sort($postes);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Employés</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
    <style>
        tr { transition: all 0.2s ease; }
        .pulse-alert { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">
<a href="/accueilG">Accueil</a>

<div class="container mx-auto px-4 py-8 max-w-7xl">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Liste des Employés</h1>

    <!-- Formulaire de filtres -->
    <form method="GET" class="bg-white p-6 rounded-xl shadow-sm mb-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <select name="sort_name" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Trier par nom</option>
            <option value="asc" <?= $sort_name === 'asc' ? 'selected' : '' ?>>A → Z</option>
            <option value="desc" <?= $sort_name === 'desc' ? 'selected' : '' ?>>Z → A</option>
        </select>
        <select name="departement" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Tous les départements</option>
            <?php foreach ($departements as $dept): ?>
                <option value="<?= htmlspecialchars($dept) ?>" <?= $departement_filter === $dept ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dept) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="poste" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Tous les postes</option>
            <?php foreach ($postes as $p): ?>
                <option value="<?= htmlspecialchars($p) ?>" <?= $poste_filter === $p ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="bg-blue-600 text-white font-medium px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            Filtrer
        </button>
    </form>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <?php if (empty($filtered)): ?>
            <p class="p-8 text-center text-gray-500">Aucun employé trouvé.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prénom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Département</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poste</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Embauche</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Contrat</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($filtered as $emp): 
                            $enAlerte = EmployeModel::contratEnAlerte($emp['id_employe']);
                        ?>
                            <tr class="<?= $enAlerte ? 'bg-red-50 border-l-4 border-red-600 hover:bg-red-100' : 'hover:bg-blue-50' ?> transition cursor-pointer"
                                onclick="window.location='/employeDetails/<?= $emp['id_employe'] ?>'">
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #<?= htmlspecialchars($emp['id_employe']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    <?= htmlspecialchars($emp['nom']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?= htmlspecialchars($emp['prenoms']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <?= htmlspecialchars($emp['departement'] ?? '—') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?= htmlspecialchars($emp['poste'] ?? '—') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($emp['date_embauche'])) ?>
                                </td>

                                <!-- COLONNE ALERTE CONTRAT -->
                                <td class="px-6 py-4 text-center font-bold">
                                    <?php if ($enAlerte): ?>
                                        <div class="flex items-center justify-center gap-2 text-red-600 pulse-alert" title="Contrat expire dans moins de 30 jours !">
                                            <i class="fas fa-exclamation-triangle text-xl"></i>
                                            <span>ALERTE FIN CONTRAT</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-green-600 text-xs">OK</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Compteur -->
    <div class="mt-6 text-sm text-gray-600">
        <strong><?= count($filtered) ?></strong> employé(s) affiché(s) sur <strong><?= count($employes) ?></strong> au total.
        <?php if (!empty($filtered)): ?>
            <?php $alertes = array_filter($filtered, fn($e) => EmployeModel::contratEnAlerte($e['id_employe'])); ?>
            <?php if (count($alertes) > 0): ?>
                <span class="ml-4 text-red-600 font-bold">• <?= count($alertes) ?> en alerte contrat</span>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>