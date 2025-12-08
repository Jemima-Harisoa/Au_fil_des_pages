<?php

use app\models\EmployeModel;
// Récupération des données passées par le contrôleur
$data = $data ?? [];
$historiqueMouvement = $historiqueMouvement ?? [];

// Fonction pour formater la date
function formatDate($dateStr) {
    return $dateStr ? date('d/m/Y', strtotime($dateStr)) : '—';
}

// Calcul de l'âge
$dateNaissance = $data['date_naissance'] ? new DateTime($data['date_naissance']) : null;
$aujourd = new DateTime();
$age_annees = $dateNaissance ? $dateNaissance->diff($aujourd)->y : '—';

// Calcul de l'ancienneté
$dateEmbauche = $data['date_embauche'] ? new DateTime($data['date_embauche']) : null;
$anciennete_annees = $dateEmbauche ? $dateEmbauche->diff($aujourd)->y : '—';
$anciennete_jours = $dateEmbauche ? $dateEmbauche->diff($aujourd)->days : '—';

// ALERTE FIN DE CONTRAT (30 jours)
$enAlerteContrat = !empty($data['id_employe']) && EmployeModel::contratEnAlerte((int)$data['id_employe']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Employé - <?= htmlspecialchars($data['nom_personne'] ?? '') . ' ' . htmlspecialchars($data['prenom'] ?? '') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .pulse-alert { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        
        /* Modale pour l'arborescence */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
        }
        .modal.active { display: flex; align-items: center; justify-content: center; }
        .modal-content {
            background-color: white;
            border-radius: 1rem;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
    </style>
    <script>
        function copierChemin(chemin) {
            navigator.clipboard.writeText(chemin).then(() => {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-3';
                toast.innerHTML = '<i class="fas fa-check-circle"></i><span>Chemin copié ! Collez-le dans votre explorateur de fichiers.</span>';
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 4000);
            });
        }

        // Afficher l'arborescence d'un dossier dans une modale
        function afficherArborescenceDossier(chemin, nomDossier) {
            fetch(`/Documents/documentsOption?action=arborescence&chemin=${encodeURIComponent(chemin)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        ouvrirModalArborescence(data.arborescence, nomDossier);
                    } else {
                        alert('Impossible de charger l\'arborescence');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Erreur lors du chargement de l\'arborescence');
                });
        }

        function ouvrirModalArborescence(arborescence, nomDossier) {
            const modal = document.getElementById('modalArborescence');
            const titre = document.getElementById('modalTitre');
            const contenu = document.getElementById('modalContenu');
            
            titre.textContent = nomDossier;
            contenu.innerHTML = genererHTML(arborescence);
            modal.classList.add('active');
        }

        function fermerModal() {
            document.getElementById('modalArborescence').classList.remove('active');
        }

        function genererHTML(items, niveau = 0) {
            let html = '<ul class="space-y-1">';
            items.forEach(item => {
                const marginLeft = niveau * 4;
                if (item.type === 'dir') {
                    html += `<li style="margin-left: ${marginLeft * 4}px;">
                        <div class="flex items-center gap-2 py-1 px-2 hover:bg-blue-50 rounded">
                            <i class="fas fa-folder text-yellow-500"></i>
                            <span class="font-medium">${item.name}</span>
                            ${item.children ? `<span class="text-xs text-gray-500">(${item.children.length})</span>` : ''}
                        </div>
                        ${item.children ? genererHTML(item.children, niveau + 1) : ''}
                    </li>`;
                } else {
                    const icons = {
                        'pdf': 'fa-file-pdf text-red-600',
                        'doc': 'fa-file-word text-blue-600',
                        'docx': 'fa-file-word text-blue-600',
                        'xls': 'fa-file-excel text-green-600',
                        'xlsx': 'fa-file-excel text-green-600',
                        'jpg': 'fa-file-image text-purple-600',
                        'jpeg': 'fa-file-image text-purple-600',
                        'png': 'fa-file-image text-purple-600',
                    };
                    const iconClass = icons[item.ext] || 'fa-file text-gray-600';
                    html += `<li style="margin-left: ${marginLeft * 4}px;">
                        <div class="flex items-center gap-2 py-1 px-2 hover:bg-blue-50 rounded">
                            <i class="fas ${iconClass}"></i>
                            <span>${item.name}</span>
                            <span class="text-xs text-gray-500 ml-auto">${(item.size / 1024).toFixed(1)} Ko</span>
                        </div>
                    </li>`;
                }
            });
            html += '</ul>';
            return html;
        }
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen py-10 px-4">

<!-- Modale pour afficher l'arborescence -->
<div id="modalArborescence" class="modal" onclick="if(event.target === this) fermerModal()">
    <div class="modal-content p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 id="modalTitre" class="text-2xl font-bold text-gray-800"></h3>
            <button onclick="fermerModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div id="modalContenu" class="text-sm"></div>
    </div>
</div>

<div class="max-w-7xl mx-auto">

    <!-- ALERTE FIN DE CONTRAT - TRÈS VISIBLE -->
    <?php if ($enAlerteContrat): ?>
        <div class="bg-red-50 border-l-8 border-red-600 rounded-r-2xl p-8 mb-8 shadow-2xl pulse-alert">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="text-red-600">
                        <i class="fas fa-exclamation-triangle text-6xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-red-800 mb-2">
                            ALERTE : FIN DE CONTRAT PROCHE
                        </h2>
                        <p class="text-xl text-red-700 font-medium">
                            Le contrat de cet employé expire dans moins de 30 jours ou est déjà expiré.
                        </p>
                        <p class="text-red-600 mt-2">
                            Action urgente requise !
                        </p>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <a href="renouveler_contrat.php?id=<?= $data['id_employe'] ?>"
                       class="bg-red-600 hover:bg-red-700 text-white font-bold text-lg px-8 py-4 rounded-xl shadow-lg transform hover:scale-105 transition flex items-center gap-3 pulse-alert">
                        <i class="fas fa-file-contract text-2xl"></i>
                        Renouveler le contrat maintenant
                    </a>
                    <a href="modifier_employe.php?id=<?= $data['id_employe'] ?>#contrat"
                       class="text-red-700 underline hover:text-red-900 text-sm">
                        → Modifier les dates du contrat
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- En-tête -->
    <div class="bg-white rounded-t-2xl shadow-xl p-6 border-b-4 <?= $enAlerteContrat ? 'border-red-600' : 'border-blue-500' ?>">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-user-tie"></i>
                Fiche Employé
                <?php if ($enAlerteContrat): ?>
                    <span class="ml-4 inline-flex items-center gap-2 text-red-600 font-bold">
                        <i class="fas fa-bell animate-pulse"></i> CONTRAT EN ALERTE
                    </span>
                <?php endif; ?>
            </h1>
            <span class="bg-gradient-to-r <?= $enAlerteContrat ? 'from-red-600 to-red-700' : 'from-blue-600 to-indigo-600' ?> text-white px-6 py-2 rounded-full text-lg font-bold shadow-lg">
                ID: <?= $data['id_employe'] ?? '—' ?>
            </span>
        </div>
    </div>

    <!-- Corps principal -->
    <div class="bg-white rounded-b-2xl shadow-xl p-8 mb-8 -mt-1">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Photo + Nom -->
            <div class="flex flex-col items-center text-center">
                <div class="relative">
                    <div class="w-48 h-48 rounded-2xl overflow-hidden shadow-2xl border-4 border-white mb-4 bg-gradient-to-br from-blue-100 to-indigo-200">
                        <?php if (!empty($data['lien_image']) && file_exists($data['lien_image'])): ?>
                            <img src="<?= htmlspecialchars($data['lien_image']) ?>" alt="Photo" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-user text-7xl text-blue-400"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($enAlerteContrat): ?>
                        <div class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-3 shadow-xl animate-pulse">
                            <i class="fas fa-exclamation text-xl"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($data['nom_personne'] ?? '') ?></h2>
                <p class="text-lg text-gray-600"><?= htmlspecialchars($data['prenom'] ?? '') ?></p>
                <div class="mt-2 inline-flex items-center gap-2 <?= $enAlerteContrat ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' ?> px-6 py-2 rounded-full text-sm font-bold">
                    <i class="fas fa-briefcase"></i>
                    <?= htmlspecialchars($data['poste'] ?? 'Non renseigné') ?>
                </div>
            </div>

            <!-- Infos générales -->
            <div class="lg:col-span-2 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">Âge</label>
                        <p class="text-lg font-medium text-gray-900">
                            <?= is_numeric($age_annees) ? $age_annees . ' an' . ($age_annees > 1 ? 's' : '') : '—' ?>
                            <span class="text-sm text-gray-500 block">Né(e) le <?= formatDate($data['date_naissance'] ?? '') ?></span>
                        </p>
                    </div>
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">Contact</label>
                        <p class="text-lg font-medium text-gray-900">
                            <?= !empty($data['contact']) ? '<a href="tel:' . htmlspecialchars($data['contact']) . '" class="hover:text-blue-600">' . chunk_split(htmlspecialchars($data['contact']), 3, ' ') . '</a>' : '—' ?>
                        </p>
                    </div>
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">Poste actuel</label>
                        <p class="text-lg font-medium text-gray-900"><?= htmlspecialchars($data['poste'] ?? '—') ?></p>
                    </div>
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">Département</label>
                        <p class="text-lg font-medium <?= $enAlerteContrat ? 'text-red-700 bg-red-50' : 'text-purple-700 bg-purple-50' ?> px-6 py-2 rounded-lg inline-block font-bold">
                            <?= htmlspecialchars($data['nom_departement'] ?? '—') ?>
                        </p>
                    </div>
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">Date d'embauche</label>
                        <p class="text-lg font-medium text-gray-900"><?= formatDate($data['date_embauche'] ?? '') ?></p>
                    </div>
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-600 mb-1">Ancienneté</label>
                        <p class="text-lg font-bold text-orange-600">
                            <?= is_numeric($anciennete_annees) ? $anciennete_annees . ' an' . ($anciennete_annees > 1 ? 's' : '') : '—' ?>
                            <?php if (is_numeric($anciennete_jours)): ?>
                                <span class="text-sm font-normal text-gray-500 block">(<?= $anciennete_jours ?> jour<?= $anciennete_jours > 1 ? 's' : '' ?>)</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="flex justify-end mt-8 gap-4">
                    <?php if ($enAlerteContrat): ?>
                        <a href="renouveler_contrat.php?id=<?= $data['id_employe'] ?>"
                           class="bg-red-600 hover:bg-red-700 text-white font-bold px-8 py-4 rounded-xl shadow-lg transform hover:scale-105 transition flex items-center gap-3 pulse-alert">
                            <i class="fas fa-file-contract"></i>
                            Renouveler le contrat
                        </a>
                    <?php endif; ?>
                    <a href="modifier_employe.php?id=<?= $data['id_employe'] ?? '' ?>"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition">
                        <i class="fas fa-edit"></i>
                        Modifier la fiche
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- HISTORIQUE DES MOUVEMENTS (inchangé) -->
    <!-- HISTORIQUE DES MOUVEMENTS -->
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
                            <td colspan="6" class="text-center py-12 text-gray-500">
                                Aucun mouvement enregistré pour cet employé.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($historiqueMouvement as $mvt): ?>
                            <tr class="hover:bg-blue-50 transition data-row"
                                data-date="<?= formatDate($mvt['dateEvenement'] ?? '') ?>"
                                data-event="<?= htmlspecialchars($mvt['typeEvenement'] ?? '') ?>"
                                data-detail="<?= htmlspecialchars($mvt['libelleComplet'] ?? '') ?>"
                                data-support="<?= htmlspecialchars($mvt['support'] ?? '') ?>"
                                data-poste="<?= htmlspecialchars($mvt['posteCible'] ?? '') ?>">

                                <td class="px-4 py-3 font-medium text-gray-900">
                                    <?= formatDate($mvt['dateEvenement'] ?? '') ?>
                                </td>

                                <td class="px-4 py-3">
                                    <?php
                                    $event = strtolower($mvt['typeEvenement'] ?? '');
                                    $icon = match (true) {
                                        str_contains($event, 'embauche')     => 'fa-user-plus text-green-600',
                                        str_contains($event, 'promotion')    => 'fa-trophy text-yellow-600',
                                        str_contains($event, 'mutation')     => 'fa-exchange-alt text-purple-600',
                                        str_contains($event, 'démission')    => 'fa-sign-out-alt text-red-600',
                                        str_contains($event, 'licenciement') => 'fa-ban text-red-700',
                                        str_contains($event, 'fin de cdd')   => 'fa-calendar-times text-orange-600',
                                        default                              => 'fa-info-circle text-gray-500'
                                    };
                                    ?>
                                    <span class="flex items-center gap-2">
                                        <i class="fas <?= $icon ?>"></i>
                                        <span class="font-medium"><?= htmlspecialchars($mvt['typeEvenement'] ?? 'Événement') ?></span>
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    <?= htmlspecialchars($mvt['libelleComplet'] ?? '—') ?>
                                </td>

                                <td class="px-4 py-3">
                                    <?php if (!empty($mvt['support'])): ?>
                                        <?php
                                        $supportBrut = $mvt['support'];
                                        
                                        // Nettoyage : enlever /public/ ou public/ du début
                                        $support = trim($supportBrut, '/');
                                        if (strpos($support, 'public/') === 0) {
                                            $support = substr($support, 7);
                                        }
                                        
                                        // Chemin serveur complet
                                        $projectRoot = dirname(__DIR__, 3);
                                        $fullPath = $projectRoot . '/public/' . $support;
                                        
                                        // Vérifier si c'est un dossier ou un fichier
                                        $isDirectory = is_dir($fullPath);
                                        $isFile = is_file($fullPath);
                                        
                                        if ($isDirectory) {
                                            // C'EST UN DOSSIER - Bouton pour afficher l'arborescence
                                            ?>
                                            <button onclick="afficherArborescenceDossier('<?= htmlspecialchars($fullPath) ?>', '<?= htmlspecialchars(basename($support)) ?>')"
                                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                                                <i class="fas fa-folder-open text-yellow-600"></i>
                                                <span><?= htmlspecialchars(basename($support)) ?></span>
                                                <i class="fas fa-sitemap"></i>
                                            </button>
                                            <?php
                                        } elseif ($isFile) {
                                            // C'EST UN FICHIER
                                            $webPath = '/' . $support;
                                            $ext = strtolower(pathinfo($support, PATHINFO_EXTENSION));
                                            $iconClass = match($ext) {
                                                'pdf' => 'fa-file-pdf text-red-600',
                                                'doc', 'docx' => 'fa-file-word text-blue-600',
                                                'xls', 'xlsx' => 'fa-file-excel text-green-600',
                                                'jpg', 'jpeg', 'png' => 'fa-file-image text-purple-600',
                                                default => 'fa-file-alt text-gray-600'
                                            };
                                            ?>
                                            <a href="<?= htmlspecialchars($webPath) ?>" 
                                               target="_blank"
                                               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 transition group">
                                                <i class="fas <?= $iconClass ?>"></i>
                                                <span class="max-w-xs truncate"><?= htmlspecialchars(basename($support)) ?></span>
                                                <i class="fas fa-external-link-alt opacity-0 group-hover:opacity-100 transition"></i>
                                            </a>
                                            <?php
                                        } else {
                                            ?>
                                            <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                <i class="fas fa-exclamation-triangle text-orange-500"></i>
                                                <span class="max-w-xs truncate" title="<?= htmlspecialchars($support) ?>">
                                                    <?= htmlspecialchars(basename($support)) ?>
                                                </span>
                                                <span class="text-gray-400">(introuvable)</span>
                                            </span>
                                            <?php
                                        }
                                        ?>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">—</span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-3 font-medium text-indigo-700">
                                    <?= htmlspecialchars($mvt['posteCible'] ?? 'Non précisé') ?>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <button onclick="alert('<?= htmlspecialchars($mvt['libelleComplet'] ?? 'Détail non disponible') ?>')"
                                            class="text-blue-600 hover:text-blue-800 font-medium">
                                        Voir
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div id="noResults" class="hidden text-center py-12 text-gray-400 text-lg">
                Aucun mouvement ne correspond à votre recherche.
            </div>
        </div>
    </div>
</div>
</body>
</html>