<?php
// === ON UTILISE $data TEL QUEL (déjà défini ailleurs) ===
// $data contient déjà toutes les données de l'employé

// Fonction pour formater la date
// === DONNÉES STATIQUES : Historique des mouvements ===
// === DONNÉES STATIQUES : Historique des mouvements (à garder avant le HTML) ===
$historique_mouvements = [
    [
        'date'      => '2023-06-15',
        'event'     => 'Promotion',
        'detail'    => 'Passage de Junior à Senior',
        'support'   => 'Décision RH n°2023-045',
        'poste'     => 'Développeur Senior',
    ],
    [
        'date'      => '2022-01-10',
        'event'     => 'Embauche',
        'detail'    => 'CDI après stage',
        'support'   => 'Contrat n°EMP-2022-001',
        'poste'     => 'Développeur Junior',
    ],
    [
        'date'      => '2024-03-20',
        'event'     => 'Changement de département',
        'detail'    => 'Transfert vers Projets Spéciaux',
        'support'   => 'Note interne RH',
        'poste'     => 'Développeur Senior',
    ],
    [
        'date'      => '2025-01-05',
        'event'     => 'Augmentation',
        'detail'    => 'Revalorisation annuelle',
        'support'   => 'Avenant n°2025-003',
        'poste'     => 'Développeur Senior',
    ],
];
function formatDate($dateStr)
{
    return $dateStr ? date('d/m/Y', strtotime($dateStr)) : '—';
}

// === Calcul de l'âge ===
$dateNaissance = new DateTime($data['date_naissance']);
$aujourd = new DateTime();
$age_annees = $dateNaissance->diff($aujourd)->y;

// === Calcul de l'ancienneté ===
$dateEmbauche = new DateTime($data['date_embauche']);
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
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen py-10 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête -->
        <div class="bg-white rounded-t-2xl shadow-xl p-6 border-b-4 border-blue-500">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-id-card text-blue-600"></i>
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
                <!-- Photo -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-48 h-48 rounded-2xl overflow-hidden shadow-xl border-4 border-white mb-4 bg-gradient-to-br from-blue-100 to-indigo-200">
                        <?php if (!empty($data['lien_image']) && file_exists($data['lien_image'])): ?>
                            <img src="<?= htmlspecialchars($data['lien_image']) ?>" alt="Photo de <?= htmlspecialchars($data['prenom']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-user text-7xl text-blue-400"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($data['nom_personne']) ?></h2>
                    <p class="text-lg text-gray-600"><?= htmlspecialchars($data['prenom']) ?></p>
                    <div class="mt-2 inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-4 py-1.5 rounded-full text-sm font-semibold">
                        <i class="fas fa-briefcase"></i>
                        <?= htmlspecialchars($data['poste']) ?>
                    </div>
                </div>

                <!-- Informations -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Âge -->
                        <div>
                            <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">
                                <i class="fas fa-birthday-cake mr-2 text-pink-500"></i> Âge
                            </label>
                            <p class="text-lg font-medium text-gray-900">
                                <?= $age_annees ?> an<?= $age_annees > 1 ? 's' : '' ?>
                                <span class="text-sm text-gray-500 block">Né(e) le <?= formatDate($data['date_naissance']) ?></span>
                            </p>
                        </div>

                        <!-- Contact -->
                        <div>
                            <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">
                                <i class="fas fa-phone mr-2 text-green-500"></i> Contact
                            </label>
                            <p class="text-lg font-medium text-gray-900">
                                <a href="tel:<?= htmlspecialchars($data['contact']) ?>" class="hover:text-blue-600 transition">
                                    <?= chunk_split($data['contact'], 3, ' ') ?>
                                </a>
                            </p>
                        </div>

                        <!-- Poste -->
                        <div>
                            <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">
                                <i class="fas fa-user-tie mr-2 text-indigo-500"></i> Poste
                            </label>
                            <p class="text-lg font-medium text-gray-900"><?= htmlspecialchars($data['poste']) ?></p>
                        </div>

                        <!-- Département -->
                        <div>
                            <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">
                                <i class="fas fa-building mr-2 text-purple-500"></i> Département
                            </label>
                            <p class="text-lg font-medium text-purple-700 bg-purple-50 px-4 py-1.5 rounded-lg inline-block">
                                <?= htmlspecialchars($data['nom_departement']) ?>
                            </p>
                        </div>

                        <!-- Embauche -->
                        <div>
                            <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">
                                <i class="fas fa-calendar-check mr-2 text-teal-500"></i> Embauche
                            </label>
                            <p class="text-lg font-medium text-gray-900"><?= formatDate($data['date_embauche']) ?></p>
                        </div>

                        <!-- Ancienneté -->
                        <div>
                            <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">
                                <i class="fas fa-clock mr-2 text-orange-500"></i> Ancienneté
                            </label>
                            <p class="text-lg font-bold text-orange-600">
                                <?= $anciennete_annees ?> an<?= $anciennete_annees > 1 ? 's' : '' ?>
                                <span class="text-sm font-normal text-gray-500 block">
                                    (<?= $anciennete_jours ?> jour<?= $anciennete_jours > 1 ? 's' : '' ?>)
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Bouton Modifier -->
                    <div class="flex justify-end mt-8">
                        <a href="modifier_employe.php?id=<?= $data['id_employe'] ?>"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition duration-200">
                            <i class="fas fa-edit"></i>
                            Modifier la fiche
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Historique des mouvements (Tableau avec filtres) -->
        <div class="bg-white rounded-2xl shadow-xl p-8 overflow-hidden">
            <div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-history text-blue-600"></i>
                    Historique des mouvements
                </h2>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span id="rowCount"><?= count($historique_mouvements) ?></span> mouvement<?= count($historique_mouvements) > 1 ? 's' : '' ?>
                </div>
            </div>

            <!-- FILTRES -->
            <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <input type="text" id="filterDate" placeholder="Filtrer Date (jj/mm/aaaa)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="text" id="filterEvent" placeholder="Filtrer Événement"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="text" id="filterDetail" placeholder="Filtrer Détail"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="text" id="filterSupport" placeholder="Filtrer Support"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="text" id="filterPoste" placeholder="Filtrer Poste"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- TABLEAU -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="mouvementsTable">
                    <thead class="text-xs text-gray-600 uppercase bg-gradient-to-r from-blue-50 to-indigo-50 border-b-2 border-blue-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Date</th>
                            <th class="px-4 py-3 font-semibold">Événement</th>
                            <th class="px-4 py-3 font-semibold">Détail</th>
                            <th class="px-4 py-3 font-semibold">Support</th>
                            <th class="px-4 py-3 font-semibold">Poste</th>
                            <th class="px-4 py-3 font-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($historique_mouvements as $i => $mvt): ?>
                            <tr class="hover:bg-blue-50 transition data-row"
                                data-date="<?= formatDate($mvt['date']) ?>"
                                data-event="<?= htmlspecialchars($mvt['event']) ?>"
                                data-detail="<?= htmlspecialchars($mvt['detail']) ?>"
                                data-support="<?= htmlspecialchars($mvt['support']) ?>"
                                data-poste="<?= htmlspecialchars($mvt['poste']) ?>">
                                <!-- Date -->
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    <?= formatDate($mvt['date']) ?>
                                </td>
                                <!-- Événement -->
                                <td class="px-4 py-3">
                                    <?php
                                    $icon = match ($mvt['event']) {
                                        'Embauche' => 'fa-user-plus text-green-500',
                                        'Promotion' => 'fa-arrow-up text-blue-600',
                                        'Augmentation' => 'fa-coins text-yellow-600',
                                        'Changement de département' => 'fa-exchange-alt text-purple-600',
                                        default => 'fa-info-circle text-gray-500'
                                    };
                                    ?>
                                    <span class="flex items-center gap-2">
                                        <i class="fas <?= $icon ?>"></i>
                                        <?= htmlspecialchars($mvt['event']) ?>
                                    </span>
                                </td>
                                <!-- Détail -->
                                <td class="px-4 py-3 text-gray-600">
                                    <?= htmlspecialchars($mvt['detail']) ?>
                                </td>
                                <!-- Support -->
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        <?= htmlspecialchars($mvt['support']) ?>
                                    </span>
                                </td>
                                <!-- Poste -->
                                <td class="px-4 py-3 font-medium text-indigo-700">
                                    <?= htmlspecialchars($mvt['poste']) ?>
                                </td>
                                <!-- Actions -->
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Voir -->
                                        <button onclick="alert('Voir le mouvement #<?= $i + 1 ?> : <?= addslashes($mvt['event']) ?>')"
                                            class="text-blue-600 hover:text-blue-800 transition">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- Éditer -->
                                        <a href="modifier_mouvement.php?id=<?= $i + 1 ?>&emp=<?= $data['id_employe'] ?>"
                                            class="text-amber-600 hover:text-amber-800 transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Message vide -->
                <div id="noResults" class="hidden text-center py-12 text-gray-400">
                    <i class="fas fa-search text-5xl mb-3"></i>
                    <p class="text-lg">Aucun mouvement ne correspond à votre recherche.</p>
                </div>
            </div>
        </div>

        <!-- SCRIPT DE FILTRE (à mettre avant </body>) -->
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

                    const match =
                        (!values.date || data.date.includes(values.date)) &&
                        (!values.event || data.event.includes(values.event)) &&
                        (!values.detail || data.detail.includes(values.detail)) &&
                        (!values.support || data.support.includes(values.support)) &&
                        (!values.poste || data.poste.includes(values.poste));

                    row.style.display = match ? '' : 'none';
                    if (match) visible++;
                });

                // Afficher/masquer message vide
                noResults.classList.toggle('hidden', visible > 0);
                rowCount.textContent = visible;
            }

            // Écouteurs sur tous les filtres
            Object.values(filters).forEach(input => {
                input.addEventListener('input', applyFilters);
                input.addEventListener('keyup', applyFilters);
            });

            // Initialiser
            applyFilters();
        </script>
    </div>
</body>

</html>