<?php
// $data est déjà remplie avec ton historique (comme ton print_r)
$data = $data ?? []; // Sécurité au cas où
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Mouvements RH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        tr:hover { background-color: #f8fafc !important; }
        .badge { @apply px-3 py-1 text-xs font-bold rounded-full; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen py-10">

<div class="max-w-7xl mx-auto px-6">

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-8">
            <h1 class="text-4xl font-bold flex items-center gap-4">
                Historique des Mouvements
            </h1>
            <p class="mt-2 text-indigo-100">Tous les événements RH : embauches, promotions, mutations...</p>
        </div>

        <!-- FILTRES -->
        <div class="p-6 bg-gray-50 border-b">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <input type="text" id="search" placeholder="Recherche globale..." class="px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <select id="filterEmploye" class="px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les employés</option>
                </select>
                <select id="filterType" class="px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les types</option>
                </select>
                <input type="date" id="dateFrom" class="px-4 py-3 border rounded-lg">
                <input type="date" id="dateTo" class="px-4 py-3 border rounded-lg">
            </div>
            <div class="mt-4 flex justify-between items-center">
                <span class="text-sm text-gray-600">
                    <strong id="count"><?= count($data) ?></strong> mouvement(s) affiché(s)
                </span>
                <button onclick="resetFilters()" class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                    Réinitialiser les filtres
                </button>
            </div>
        </div>

        <!-- TABLEAU -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Employé</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Détail</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Poste cible</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Dépt cible</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Support</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-gray-200">
                    <!-- Rempli par JS -->
                </tbody>
            </table>
        </div>

        <div id="noResults" class="text-center py-20 text-gray-400 text-xl hidden">
            Aucun mouvement trouvé avec ces critères.
        </div>
    </div>
</div>

<script>
// === TES DONNÉES PHP INJECTÉES DANS JS ===
const mouvements = <?= json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>;

// === ÉLÉMENTS DOM ===
const tableBody = document.getElementById('tableBody');
const noResults = document.getElementById('noResults');
const countSpan = document.getElementById('count');
const inputs = {
    search: document.getElementById('search'),
    employe: document.getElementById('filterEmploye'),
    type: document.getElementById('filterType'),
    from: document.getElementById('dateFrom'),
    to: document.getElementById('dateTo')
};

// === Remplir les filtres dynamiques ===
function initFilters() {
    const employes = [...new Set(mouvements.map(m => m.employe_nom_complet))].sort();
    const types = [...new Set(mouvements.map(m => m.type_evenement))].sort();

    employes.forEach(e => {
        const opt = new Option(e, e);
        inputs.employe.add(opt);
    });
    types.forEach(t => {
        const opt = new Option(t || 'Non défini', t || '');
        inputs.type.add(opt);
    });
}

// === Affichage d'une ligne ===
function renderRow(m) {
    const type = m.type_evenement || 'Inconnu';
    const badgeColor = 
        type === 'Embauche' ? 'bg-green-100 text-green-800' :
        type === 'Promotion' ? 'bg-yellow-100 text-yellow-800' :
        type === 'Mutation' ? 'bg-purple-100 text-purple-800' :
        'bg-gray-100 text-gray-800';

    return `
        <tr class="hover:bg-indigo-50 transition">
            <td class="px-6 py-4 font-medium">${formatDate(m.date_evenement)}</td>
            <td class="px-6 py-4 font-semibold text-indigo-700">${m.employe_nom_complet}</td>
            <td class="px-6 py-4">
                <span class="badge ${badgeColor}">${type}</span>
            </td>
            <td class="px-6 py-4 text-gray-700">${m.libelle_mouvement}</td>
            <td class="px-6 py-4">${m.poste_cible || '—'}</td>
            <td class="px-6 py-4">${m.departement_cible || '—'}</td>
            <td class="px-6 py-4 text-sm text-gray-600 italic">${m.support || '—'}</td>
        </tr>
    `;
}

// === Format date FR ===
function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('fr-FR');
}

// === Filtrage + affichage ===
function filterAndRender() {
    let filtered = mouvements;

    // Recherche texte
    const search = inputs.search.value.toLowerCase().trim();
    if (search) {
        filtered = filtered.filter(m => 
            (m.employe_nom_complet?.toLowerCase().includes(search)) ||
            (m.libelle_mouvement?.toLowerCase().includes(search)) ||
            (m.support?.toLowerCase().includes(search))
        );
    }

    // Filtre employé
    if (inputs.employe.value) {
        filtered = filtered.filter(m => m.employe_nom_complet === inputs.employe.value);
    }

    // Filtre type
    if (inputs.type.value) {
        filtered = filtered.filter(m => m.type_evenement === inputs.type.value);
    }

    // Dates
    if (inputs.from.value) {
        filtered = filtered.filter(m => m.date_evenement >= inputs.from.value);
    }
    if (inputs.to.value) {
        filtered = filtered.filter(m => m.date_evenement <= inputs.to.value);
    }

    // Affichage
    tableBody.innerHTML = filtered.length ? filtered.map(renderRow).join('') : '';
    noResults.classList.toggle('hidden', filtered.length > 0);
    countSpan.textContent = filtered.length;
}

// === Réinitialiser ===
function resetFilters() {
    Object.values(inputs).forEach(i => i.value = '');
    filterAndRender();
}

// === Initialisation ===
initFilters();
filterAndRender();

// === Écouteurs (filtre instantané) ===
Object.values(inputs).forEach(input => {
    input.addEventListener('input', filterAndRender);
    input.addEventListener('change', filterAndRender);
});
</script>

</body>
</html>