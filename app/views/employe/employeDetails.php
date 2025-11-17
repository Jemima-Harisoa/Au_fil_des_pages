<?php
// Récupération des données passées par le contrôleur
$data = $data ?? []; // Données de l'employé
$historiqueMouvement = $historiqueMouvement ?? []; // Historique réel depuis la BDD

// Fonction pour formater la date
function formatDate($dateStr) {
    return $dateStr ? date('d/m/Y', strtotime($dateStr)) : '—';
}

// Calcul de l'âge
$dateNaissance = new DateTime($data['date_naissance'] ?? 'now');
$aujourd = new DateTime();
$age_annees = $dateNaissance->diff($aujourd)->y;

// Calcul de l'ancienneté
$dateEmbauche = new DateTime($data['date_embauche'] ?? 'now');
$interval = $dateEmbauche->diff($aujourd);
$anciennete_annees = $interval->y;
$anciennete_jours = $interval->days;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Employé - <?= htmlspecialchars($data['nom_personne'] . ' ' . $data['prenom']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen py-10 px-4">
<div class="max-w-6xl mx-auto">

    <!-- En-tête -->
    <div class="bg-white rounded-t-2xl shadow-xl p-6 border-b-4 border-blue-500">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                Fiche Employé
            </h1>
            <span class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-1.5 rounded-full text-sm font-semibold shadow">
                ID: <?= $data['id_employe'] ?>
            </span>
        </div>
    </div>

    <!-- Corps principal -->
    <div class="bg-white rounded-b-2xl shadow-xl p-8 mb-8 -mt-1">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Photo + Nom -->
            <div class="flex flex-col items-center text-center">
                <div class="w-48 h-48 rounded-2xl overflow-hidden shadow-xl border-4 border-white mb-4 bg-gradient-to-br from-blue-100 to-indigo-200">
                    <?php if (!empty($data['lien_image']) && file_exists($data['lien_image'])): ?>
                        <img src="<?= htmlspecialchars($data['lien_image']) ?>" alt="Photo" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-user text-7xl text-blue-400"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($data['nom_personne']) ?></h2>
                <p class="text-lg text-gray-600"><?= htmlspecialchars($data['prenom']) ?></p>
                <div class="mt-2 inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-4 py-1.5 rounded-full text-sm font-semibold">
                    <?= htmlspecialchars($data['poste']) ?>
                </div>
            </div>

            <!-- Infos générales -->
            <div class="lg:col-span-2 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">Âge</label>
                        <p class="text-lg font-medium text-gray-900">
                            <?= $age_annees ?> an<?= $age_annees > 1 ? 's' : '' ?>
                            <span class="text-sm text-gray-500 block">Né(e) le <?= formatDate($data['date_naissance']) ?></span>
                        </p>
                    </div>
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">Contact</label>
                        <p class="text-lg font-medium text-gray-900">
                            <a href="tel:<?= htmlspecialchars($data['contact']) ?>" class="hover:text-blue-600">
                                <?= chunk_split($data['contact'], 3, ' ') ?>
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">Poste</label>
                        <p class="text-lg font-medium text-gray-900"><?= htmlspecialchars($data['poste']) ?></p>
                    </div>
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">Département</label>
                        <p class="text-lg font-medium text-purple-700 bg-purple-50 px-4 py-1.5 rounded-lg inline-block">
                            <?= htmlspecialchars($data['nom_departement']) ?>
                        </p>
                    </div>
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">Embauche</label>
                        <p class="text-lg font-medium text-gray-900"><?= formatDate($data['date_embauche']) ?></p>
                    </div>
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">Ancienneté</label>
                        <p class="text-lg font-bold text-orange-600">
                            <?= $anciennete_annees ?> an<?= $anciennete_annees > 1 ? 's' : '' ?>
                            <span class="text-sm font-normal text-gray-500 block">(<?= $anciennete_jours ?> jour<?= $anciennete_jours > 1 ? 's' : '' ?>)</span>
                        </p>
                    </div>
                </div>

                <div class="flex justify-end mt-8">
                    <a href="modifier_employe.php?id=<?= $data['id_employe'] ?>"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition">
                        Modifier la fiche
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- === HISTORIQUE DES MOUVEMENTS (DYNAMIQUE) === -->
    <div class="bg-white rounded-2xl shadow-xl p-8 overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                Historique des mouvements
            </h2>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span id="rowCount"><?= count($historiqueMouvement) ?></span> mouvement<?= count($historiqueMouvement) > 1 ? 's' : '' ?>
            </div>
        </div>

        <!-- Filtres -->
        <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" id="filterDate" placeholder="Date (jj/mm/aaaa)" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="text" id="filterEvent" placeholder="Événement" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="text" id="filterDetail" placeholder="Détail" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="text" id="filterSupport" placeholder="Support" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="text" id="filterPoste" placeholder="Poste" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700" id="mouvementsTable">
                <thead class="text-xs text-gray-600 uppercase bg-gradient-to-r from-blue-50 to-indigo-50 border-b-2 border-blue-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Date</th>
                        <th class="px-4 py-3 font-semibold">Événement</th>
                        <th class="px-4 py-3 font-semibold">Détail</th>
                        <th class="px-4 py-3 font-semibold">Support</th>
                        <th class="px-4 py-3 font-semibold">Poste</th>
                        <th class="px-4 py-3 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($historiqueMouvement)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">
                                Aucun mouvement enregistré pour cet employé.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($historiqueMouvement as $mvt): ?>
                            <tr class="hover:bg-blue-50 transition data-row"
                                data-date="<?= formatDate($mvt['date_evenement'] ?? '') ?>"
                                data-event="<?= htmlspecialchars($mvt['nom_evenement'] ?? '') ?>"
                                data-detail="<?= htmlspecialchars($mvt['details'] ?? $mvt['nom_evenement'] ?? 'Mobilité interne') ?>"
                                data-support="<?= htmlspecialchars($mvt['support'] ?? '-') ?>"
                                data-poste="<?= htmlspecialchars($mvt['titre'] ?? $mvt['poste'] ?? 'Non renseigné') ?>">

                                <td class="px-4 py-3 font-medium text-gray-900"><?= formatDate($mvt['date_evenement'] ?? '') ?></td>

                                <td class="px-4 py-3">
                                    <?php
                                    $event = $mvt['nom_evenement'] ?? '';
                                    $icon = match (strtolower($event)) {
                                        'embauche'                  => 'fa-user-plus text-green-600',
                                        'promotion'                 => 'fa-arrow-up text-blue-600',
                                        'augmentation'              => 'fa-coins text-yellow-600',
                                        'mutation', 'changement de département' => 'fa-exchange-alt text-purple-600',
                                        default                     => 'fa-info-circle text-gray-500'
                                    };
                                    ?>
                                    <span class="flex items-center gap-2">
                                        <i class="fas <?= $icon ?>"></i>
                                        <?= htmlspecialchars($event ?: 'Événement') ?>
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-gray-600">
                                    <?= htmlspecialchars($mvt['details'] ?? "Mobilité vers " . ($mvt['titre'] ?? '') . " (" . ($mvt['nom_departement'] ?? '') . ")") ?>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        <?= htmlspecialchars($mvt['support'] ?? '—') ?>
                                    </span>
                                </td>

                                <td class="px-4 py-3 font-medium text-indigo-700">
                                    <?= htmlspecialchars($mvt['titre'] ?? $mvt['poste'] ?? 'Non renseigné') ?>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <button onclick="alert('Détails du mouvement du <?= formatDate($mvt['date_evenement']) ?>')"
                                            class="text-blue-600 hover:text-blue-800">
                                        Voir
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div id="noResults" class="hidden text-center py-12 text-gray-400">
                Aucun mouvement ne correspond à votre recherche.
            </div>
        </div>
    </div>

    <!-- Script de filtre (inchangé) -->
    <script>
        const filters = {
            date: document.getElementById('filterDate'),
            event: document.getElementById('filterEvent'),
            detail: document.getElementById('filterDetail'),
            support: document.getElementById('filterSupport'),
            poste: document.getElementById('filterPoste')
        };
        const rows = document.querySelectorAll('#mouvementsTable .data-row');
        const noResults = document.getElementById('noResults');
        const rowCount = document.getElementById('rowCount');

        function applyFilters() {
            let visible = 0;
            const values = {
                date: filters.date.value.trim().toLowerCase(),
                event: filters.event.value.trim().toLowerCase(),
                detail: filters.detail.value.trim().toLowerCase(),
                support: filters.support.value.trim().toLowerCase(),
                poste: filters.poste.value.trim().toLowerCase()
            };

            rows.forEach(row => {
                const data = {
                    date: row.dataset.date.toLowerCase(),
                    event: row.dataset.event.toLowerCase(),
                    detail: row.dataset.detail.toLowerCase(),
                    support: row.dataset.support.toLowerCase(),
                    poste: row.dataset.poste.toLowerCase()
                };

                const match = (!values.date || data.date.includes(values.date)) &&
                              (!values.event || data.event.includes(values.event)) &&
                              (!values.detail || data.detail.includes(values.detail)) &&
                              (!values.support || data.support.includes(values.support)) &&
                              (!values.poste || data.poste.includes(values.poste));

                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            noResults.classList.toggle('hidden', visible > 0 || rows.length === 0);
            rowCount.textContent = visible || 0;
        }

        Object.values(filters).forEach(input => {
            input.addEventListener('input', applyFilters);
            input.addEventListener('keyup', applyFilters);
        });

        applyFilters();
    </script>
</div>
</body>
</html>