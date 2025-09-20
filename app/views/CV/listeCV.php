<?php
// Layout template (Flight's render wraps content in this)
if (!isset($layout)) {
    echo '<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Liste des CVs avec Comparaison</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    .pulse-animation {
      animation: pulse 2s ease-in-out infinite;
    }
  </style>
</head>
<body class="bg-gray-50 font-sans">';
}
?>

<div class="container mx-auto p-6 max-w-7xl">
  <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Liste des CVs Déposés</h1>
  
  <!-- Filter and Sort Section -->
  <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Statut Filter -->
    <div>
      <label for="status-filter" class="block text-sm font-medium text-gray-700">Filtrer par statut</label>
      <select id="status-filter" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        <option value="all">Tous les statuts</option>
        <option value="Validé">Validé</option>
        <option value="Rejeté">Rejeté</option>
      </select>
    </div>
    
    <!-- Sort Options -->
    <div>
      <label for="sort-by" class="block text-sm font-medium text-gray-700">Trier par</label>
      <select id="sort-by" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        <option value="date-desc">Date de dépôt (décroissant)</option>
        <option value="date-asc">Date de dépôt (croissant)</option>
        <option value="similarite-desc">Similarité (décroissant)</option>
        <option value="similarite-asc">Similarité (croissant)</option>
        <option value="nom-asc">Nom (A-Z)</option>
        <option value="nom-desc">Nom (Z-A)</option>
      </select>
    </div>
    
    <!-- Search by Name/Post/Annonce -->
    <div>
      <label for="search-text" class="block text-sm font-medium text-gray-700">Rechercher par nom/prénom/poste/annonce</label>
      <input type="text" id="search-text" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Rechercher...">
    </div>
    
    <!-- Similarité Range -->
    <div>
      <label for="min-similarite" class="block text-sm font-medium text-gray-700">Similarité min. (%)</label>
      <input type="number" id="min-similarite" min="0" max="100" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
    </div>
    
    <div>
      <label for="max-similarite" class="block text-sm font-medium text-gray-700">Similarité max. (%)</label>
      <input type="number" id="max-similarite" min="0" max="100" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="100">
    </div>
    
    <!-- Date Range -->
    <div class="col-span-1 md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label for="start-date" class="block text-sm font-medium text-gray-700">Date de dépôt à partir de</label>
        <input type="date" id="start-date" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
      </div>
      <div>
        <label for="end-date" class="block text-sm font-medium text-gray-700">Date de dépôt jusqu'à</label>
        <input type="date" id="end-date" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
      </div>
    </div>
    
    <!-- Filter and Reset Buttons -->
    <div class="col-span-1 md:col-span-3 flex justify-center space-x-4 mt-4">
      <button id="apply-filters" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Appliquer les filtres</button>
      <button id="reset-filters" class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">Tous les CVs</button>
    </div>
  </div>

  <div id="cv-list" class="space-y-6">
    <?php if (empty($data)): ?>
      <p class="text-center text-gray-500">Aucun CV trouvé.</p>
    <?php else: ?>
      <?php foreach ($data as $cv): ?>
        <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-200 cv-card" 
             data-status="<?php echo htmlspecialchars($cv['validation_statut'] ?? 'N/A'); ?>"
             data-nom="<?php echo htmlspecialchars(strtolower(($cv['nom'] ?? 'N/A') . ' ' . ($cv['prenom'] ?? 'N/A'))); ?>"
             data-poste="<?php echo htmlspecialchars(strtolower($cv['poste'] ?? 'N/A')); ?>"
             data-annonce="<?php echo htmlspecialchars(strtolower($cv['annonce_titre'] ?? 'N/A')); ?>"
             data-similarite="<?php echo ($cv['similarite'] ?? 0) * 100; ?>"
             data-date="<?php echo strtotime($cv['date_deposition'] ?? '1970-01-01'); ?>">
          <div class="flex flex-col sm:flex-row sm:items-center mb-4">
            <img src="<?php echo htmlspecialchars($cv['lien_image'] ?? 'https://via.placeholder.com/150'); ?>" alt="Photo de <?php echo htmlspecialchars(($cv['prenom'] ?? 'N/A') . ' ' . ($cv['nom'] ?? 'N/A')); ?>" class="w-20 h-20 rounded-full mb-4 sm:mb-0 sm:mr-4 object-cover">
            <div class="flex-1">
              <h2 class="text-2xl font-semibold text-gray-800"><?php echo htmlspecialchars(($cv['prenom'] ?? 'N/A') . ' ' . ($cv['nom'] ?? 'N/A')); ?></h2>
              <p class="text-gray-600">Contact: <?php echo htmlspecialchars($cv['contact'] ?? 'N/A'); ?></p>
              <p class="text-gray-600">Date de naissance: <?php echo htmlspecialchars($cv['date_naissance'] ?? 'N/A'); ?></p>
              <p class="text-gray-600">Déposé le: <span class="date-deposition"><?php echo htmlspecialchars(isset($cv['date_deposition']) ? date('d/m/Y H:i', strtotime($cv['date_deposition'])) : 'N/A'); ?></span></p>
            </div>
            <div class="mt-4 sm:mt-0">
              <span class="<?php echo ($cv['validation_statut'] ?? 'N/A') === 'Validé' ? 'bg-green-500 text-white pulse-animation' : 'bg-red-500 text-white pulse-animation'; ?> inline-flex items-center px-4 py-2 rounded-full text-lg font-medium">
                <?php if (($cv['validation_statut'] ?? 'N/A') === 'Validé'): ?>
                  <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <?php else: ?>
                  <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <?php endif; ?>
                <?php echo htmlspecialchars($cv['validation_statut'] ?? 'N/A'); ?>
              </span>
            </div>
          </div>
          <p class="mb-2 text-gray-700"><strong>Poste:</strong> <?php echo htmlspecialchars($cv['poste'] ?? 'N/A'); ?></p>
          <p class="mb-2 text-gray-700"><strong>Annonce:</strong> <?php echo htmlspecialchars($cv['annonce_titre'] ?? 'N/A'); ?></p>
          <div class="mt-4">
            <button id="toggle-profile-<?php echo htmlspecialchars($cv['id_personne'] ?? '0'); ?>" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-lg">
              Afficher/Masquer la comparaison des profils
            </button>
            <div id="profile-content-<?php echo htmlspecialchars($cv['id_personne'] ?? '0'); ?>" class="mt-4 p-4 bg-gray-100 rounded-md hidden">
              <div class="mb-4 text-center">
                <span class="inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-lg font-semibold">
                  Similarité: <?php echo htmlspecialchars(number_format(($cv['similarite'] ?? 0) * 100, 2)); ?>%
                </span>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <h3 class="text-xl font-semibold text-gray-800">Profil requis pour le poste</h3>
                  <div class="p-3 bg-white rounded-md">
                    <p><strong>Compétences:</strong> <?php echo htmlspecialchars($cv['profil_competences'] ?? 'N/A'); ?></p>
                  </div>
                  <div class="p-3 bg-gray-50 rounded-md">
                    <p><strong>Skills:</strong> <?php echo htmlspecialchars($cv['profil_skills'] ?? 'N/A'); ?></p>
                  </div>
                  <div class="p-3 bg-white rounded-md">
                    <p><strong>Loisirs:</strong> <?php echo htmlspecialchars($cv['profil_loisirs'] ?? 'N/A'); ?></p>
                  </div>
                  <div class="p-3 bg-gray-50 rounded-md">
                    <p><strong>Filière:</strong> <?php echo htmlspecialchars($cv['profil_filiere'] ?? 'N/A'); ?></p>
                  </div>
                  <div class="p-3 bg-white rounded-md">
                    <p><strong>Expérience pro:</strong> <?php echo htmlspecialchars($cv['profil_experience_pro'] ?? 'N/A'); ?></p>
                  </div>
                  <div class="p-3 bg-gray-50 rounded-md">
                    <p><strong>Certifications:</strong> <?php echo htmlspecialchars($cv['profil_certifications'] ?? 'N/A'); ?></p>
                  </div>
                  <div class="p-3 bg-white rounded-md">
                    <p><strong>Langues:</strong> <?php echo htmlspecialchars($cv['profil_langues'] ?? 'N/A'); ?></p>
                  </div>
                </div>
                <div class="space-y-2">
                  <h3 class="text-xl font-semibold text-gray-800">Profil soumis dans le CV</h3>
                  <div class="p-3 bg-white rounded-md">
                    <p><strong>Compétences:</strong> <?php echo htmlspecialchars($cv['cv_competences'] ?? 'N/A'); ?></p>
                  </div>
                  <div class="p-3 bg-gray-50 rounded-md">
                    <p><strong>Skills:</strong> <?php echo htmlspecialchars($cv['cv_skills'] ?? 'N/A'); ?></p>
                  </div>
                  <div class="p-3 bg-white rounded-md">
                    <p><strong>Loisirs:</strong> <?php echo htmlspecialchars($cv['cv_loisirs'] ?? 'N/A'); ?></p>
                  </div>
                  <div class="p-3 bg-gray-50 rounded-md">
                    <p><strong>Filière:</strong> <?php echo htmlspecialchars($cv['cv_filiere'] ?? 'N/A'); ?></p>
                  </div>
                  <div class="p-3 bg-white rounded-md">
                    <p><strong>Expérience pro:</strong> <?php echo htmlspecialchars($cv['cv_experience_pro'] ?? 'N/A'); ?></p>
                  </div>
                  <div class="p-3 bg-gray-50 rounded-md">
                    <p><strong>Certifications:</strong> <?php echo htmlspecialchars($cv['cv_certifications'] ?? 'N/A'); ?></p>
                  </div>
                  <div class="p-3 bg-white rounded-md">
                    <p><strong>Langues:</strong> <?php echo htmlspecialchars($cv['cv_langues'] ?? 'N/A'); ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script>
  // Function to apply filters and sorts
  function applyFilters() {
    const statusFilter = document.getElementById('status-filter').value;
    const sortBy = document.getElementById('sort-by').value;
    const searchText = document.getElementById('search-text').value.toLowerCase();
    const minSimilarite = parseFloat(document.getElementById('min-similarite').value) || 0;
    const maxSimilarite = parseFloat(document.getElementById('max-similarite').value) || 100;
    const startDate = document.getElementById('start-date').value ? new Date(document.getElementById('start-date').value) : new Date(0);
    const endDate = document.getElementById('end-date').value ? new Date(document.getElementById('end-date').value) : new Date();

    let cvCards = Array.from(document.querySelectorAll('.cv-card'));

    // Filter
    cvCards = cvCards.filter(card => {
      const status = card.getAttribute('data-status');
      const nom = card.getAttribute('data-nom');
      const poste = card.getAttribute('data-poste');
      const annonce = card.getAttribute('data-annonce');
      const similarite = parseFloat(card.getAttribute('data-similarite'));
      const date = parseInt(card.getAttribute('data-date')) * 1000; // Convert to ms

      return (statusFilter === 'all' || status === statusFilter) &&
             (nom.includes(searchText) || poste.includes(searchText) || annonce.includes(searchText)) &&
             (similarite >= minSimilarite && similarite <= maxSimilarite) &&
             (date >= startDate.getTime() && date <= endDate.getTime() + 86400000); // Add a day for end date inclusivity
    });

    // Sort
    cvCards.sort((a, b) => {
      if (sortBy === 'date-desc') {
        return parseInt(b.getAttribute('data-date')) - parseInt(a.getAttribute('data-date'));
      } else if (sortBy === 'date-asc') {
        return parseInt(a.getAttribute('data-date')) - parseInt(b.getAttribute('data-date'));
      } else if (sortBy === 'similarite-desc') {
        return parseFloat(b.getAttribute('data-similarite')) - parseFloat(a.getAttribute('data-similarite'));
      } else if (sortBy === 'similarite-asc') {
        return parseFloat(a.getAttribute('data-similarite')) - parseFloat(b.getAttribute('data-similarite'));
      } else if (sortBy === 'nom-asc') {
        return a.getAttribute('data-nom').localeCompare(b.getAttribute('data-nom'));
      } else if (sortBy === 'nom-desc') {
        return b.getAttribute('data-nom').localeCompare(a.getAttribute('data-nom'));
      }
    });

    // Clear and re-append sorted/filtered cards
    const cvList = document.getElementById('cv-list');
    cvList.innerHTML = '';
    if (cvCards.length === 0) {
      cvList.innerHTML = '<p class="text-center text-gray-500">Aucun CV trouvé.</p>';
    } else {
      cvCards.forEach(card => cvList.appendChild(card));
    }
  }

  // Function to reset filters and show all CVs
  function resetFilters() {
    document.getElementById('status-filter').value = 'all';
    document.getElementById('sort-by').value = 'date-desc';
    document.getElementById('search-text').value = '';
    document.getElementById('min-similarite').value = '';
    document.getElementById('max-similarite').value = '';
    document.getElementById('start-date').value = '';
    document.getElementById('end-date').value = '';
    applyFilters();
  }

  // Add event listeners for toggle buttons
  document.querySelectorAll('[id^="toggle-profile-"]').forEach(button => {
    const profileId = button.id.replace('toggle-profile-', '');
    const profileContent = document.getElementById(`profile-content-${profileId}`);
    button.addEventListener('click', () => {
      profileContent.classList.toggle('hidden');
      button.textContent = profileContent.classList.contains('hidden')
        ? 'Afficher la comparaison des profils'
        : 'Masquer la comparaison des profils';
    });
  });

  // Add event listeners for buttons
  document.getElementById('apply-filters').addEventListener('click', applyFilters);
  document.getElementById('reset-filters').addEventListener('click', resetFilters);

  // Initial application
  applyFilters();
</script>

<?php
if (!isset($layout)) {
    echo '</body></html>';
}
?>