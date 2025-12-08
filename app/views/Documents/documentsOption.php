<?php
use app\models\Documents\documentsModels;

$data = $data ?? [];

// Nettoyage et sécurisation des paramètres GET
$_GET = array_map('trim', $_GET);
$creerId = isset($_GET['creer']) && is_numeric($_GET['creer']) ? (int)$_GET['creer'] : null;
$voirId = isset($_GET['voir']) && is_numeric($_GET['voir']) ? (int)$_GET['voir'] : null;

$message = null;
$dossierActif = null;
$arborescenceComplete = [];

// Chemin de base identique à celui du model
$basePath = $_SERVER['DOCUMENT_ROOT'] . '/Documents';

// === 1. Création du dossier ===
if ($creerId) {
    $resultat = documentsModels::creerDossierCompletEmploye($creerId);

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

// Fonction récursive pour scanner l'arborescence
function scanArborescence($dir, $baseName = '') {
    $result = [];
    if (!is_dir($dir)) return $result;
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || $item === 'index.html') continue;
        
        $path = $dir . '/' . $item;
        $relativePath = $baseName . '/' . $item;
        
        if (is_dir($path)) {
            $result[] = [
                'name' => $item,
                'type' => 'dir',
                'path' => $relativePath,
                'children' => scanArborescence($path, $relativePath)
            ];
        } else {
            $result[] = [
                'name' => $item,
                'type' => 'file',
                'path' => $relativePath,
                'size' => filesize($path),
                'ext' => strtolower(pathinfo($item, PATHINFO_EXTENSION))
            ];
        }
    }
    return $result;
}

// === 2. Affichage du contenu d'un dossier ===
if ($voirId) {
    foreach ($data[1] as $emp) {
        if ($emp['id_employe'] == $voirId) {
            $dossierNom = $emp['nom'] . '_' . $emp['prenom'] . '_' . $emp['id_employe'];
            $dossierChemin = $basePath . '/' . $dossierNom;

            if (is_dir($dossierChemin)) {
                $dossierActif = $emp;
                $dossierActif['nom_dossier'] = $dossierNom;
                $arborescenceComplete = scanArborescence($dossierChemin, $dossierNom);
            }
            break;
        }
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
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        .tree-item { cursor: pointer; transition: all 0.2s; }
        .tree-item:hover { background-color: #f0f9ff; }
        .tree-children { margin-left: 1.5rem; border-left: 2px solid #e5e7eb; padding-left: 1rem; }
        .file-icon { width: 24px; text-align: center; }
        .dropdown { position: relative; display: inline-block; }
        .dropdown-content { 
            display: none; 
            position: absolute; 
            right: 0;
            background-color: white; 
            min-width: 160px; 
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); 
            z-index: 1;
            border-radius: 8px;
            overflow: hidden;
        }
        .dropdown:hover .dropdown-content { display: block; }
        .dropdown-content a { 
            color: black; 
            padding: 12px 16px; 
            text-decoration: none; 
            display: block; 
            transition: background-color 0.2s;
        }
        .dropdown-content a:hover { background-color: #f0f9ff; }
    </style>
    <script>
        function toggleFolder(id) {
            const children = document.getElementById('children-' + id);
            const icon = document.getElementById('icon-' + id);
            if (children.classList.contains('hidden')) {
                children.classList.remove('hidden');
                icon.classList.remove('fa-folder');
                icon.classList.add('fa-folder-open');
            } else {
                children.classList.add('hidden');
                icon.classList.remove('fa-folder-open');
                icon.classList.add('fa-folder');
            }
        }

        function ouvrirFichier(path) {
            window.open('/Documents' + path, '_blank');
        }

        function telechargerFichier(path, filename) {
            const link = document.createElement('a');
            link.href = '/Documents' + path;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Fonction spéciale pour les PDF : télécharge automatiquement
        function ouvrirPDF(path, filename) {
            // Télécharge le PDF automatiquement
            telechargerFichier(path, filename);
            
            // Affiche un message à l'utilisateur
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            toast.innerHTML = '<i class="fas fa-download mr-2"></i>Téléchargement de ' + filename + '...';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Fonction pour ouvrir avec un logiciel spécifique
        function ouvrirAvecApplication(path, ext, filename) {
            // Pour les PDF, on force le téléchargement
            if (ext === 'pdf') {
                ouvrirPDF(path, filename);
                return;
            }
            
            let protocol = '';
            switch(ext) {
                case 'doc':
                case 'docx':
                    protocol = 'ms-word:ofe|u|';
                    break;
                case 'xls':
                case 'xlsx':
                    protocol = 'ms-excel:ofe|u|';
                    break;
                default:
                    telechargerFichier(path, filename);
                    return;
            }
            window.location.href = protocol + window.location.origin + '/Documents' + path;
        }
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen py-12">

<div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-10">
        <h1 class="text-5xl font-bold text-gray-800 mb-4">Gestion des Dossiers Employés</h1>
        <p class="text-xl text-gray-600">Créez et consultez les dossiers de documents</p>
    </div>

    <?php if ($message): ?>
        <div class="max-w-4xl mx-auto mb-8"><?= $message ?></div>
    <?php endif; ?>

    <!-- === MODE : ARBORESCENCE COMPLÈTE === -->
    <?php if ($dossierActif): ?>
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <a href="?" class="text-indigo-600 hover:text-indigo-800 transition">
                        <i class="fas fa-arrow-left text-2xl"></i>
                    </a>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            Dossier de <?= htmlspecialchars($dossierActif['nom'] . ' ' . $dossierActif['prenom']) ?>
                        </h2>
                        <p class="text-gray-600">ID #<?= $dossierActif['id_employe'] ?> • <?= htmlspecialchars($dossierActif['poste'] ?? '') ?></p>
                    </div>
                </div>
            </div>

            <?php if (empty($arborescenceComplete)): ?>
                <div class="text-center py-20 text-gray-500">
                    <i class="fas fa-folder-open text-6xl mb-4 text-gray-300"></i>
                    <p class="text-xl">Ce dossier est vide</p>
                </div>
            <?php else: ?>
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-700">
                        <i class="fas fa-folder-open text-yellow-500"></i>
                        <span><?= htmlspecialchars($dossierActif['nom_dossier']) ?></span>
                    </div>
                    
                    <?php 
                    function afficherArborescence($items, $niveau = 0, &$compteur = 0) {
                        foreach ($items as $item):
                            $compteur++;
                            $id = 'item-' . $compteur;
                            
                            if ($item['type'] === 'dir'):
                                $hasChildren = !empty($item['children']);
                    ?>
                        <div class="mb-1">
                            <div class="tree-item flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-blue-50" 
                                 <?= $hasChildren ? 'onclick="toggleFolder(\'' . $id . '\')"' : '' ?>>
                                <i id="icon-<?= $id ?>" class="fas fa-folder text-yellow-500 file-icon"></i>
                                <span class="font-medium text-gray-700"><?= htmlspecialchars($item['name']) ?></span>
                                <?php if ($hasChildren): ?>
                                    <span class="text-xs text-gray-500 ml-2">(<?= count($item['children']) ?>)</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($hasChildren): ?>
                                <div id="children-<?= $id ?>" class="tree-children">
                                    <?php afficherArborescence($item['children'], $niveau + 1, $compteur); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php 
                            else:
                                $icons = [
                                    'pdf' => 'fa-file-pdf text-red-600',
                                    'doc' => 'fa-file-word text-blue-600',
                                    'docx' => 'fa-file-word text-blue-600',
                                    'xls' => 'fa-file-excel text-green-600',
                                    'xlsx' => 'fa-file-excel text-green-600',
                                    'jpg' => 'fa-file-image text-purple-600',
                                    'jpeg' => 'fa-file-image text-purple-600',
                                    'png' => 'fa-file-image text-purple-600',
                                    'zip' => 'fa-file-zipper text-orange-600',
                                    'txt' => 'fa-file-lines text-gray-600',
                                ];
                                $iconClass = $icons[$item['ext']] ?? 'fa-file text-gray-600';
                                $filePath = htmlspecialchars($item['path']);
                                $fileName = htmlspecialchars($item['name']);
                                $fullServerPath = $basePath . $item['path'];
                                $isPDF = $item['ext'] === 'pdf';
                    ?>
                        <div class="tree-item flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-blue-50 mb-1 group">
                            <i class="fas <?= $iconClass ?> file-icon"></i>
                            <span class="text-gray-700 flex-1"><?= $fileName ?></span>
                            <span class="text-xs text-gray-500"><?= number_format($item['size']/1024, 1) ?> Ko</span>
                            
                            <!-- Boutons d'action -->
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <?php if ($isPDF): ?>
                                    <!-- Bouton spécial pour PDF -->
                                    <button onclick="ouvrirPDF('<?= $filePath ?>', '<?= $fileName ?>')"
                                            class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg transition"
                                            title="Télécharger et ouvrir avec Adobe Reader">
                                        <i class="fas fa-file-pdf mr-1"></i>Ouvrir PDF
                                    </button>
                                <?php else: ?>
                                    <!-- Bouton Ouvrir (navigateur) -->
                                    <button onclick="ouvrirFichier('<?= $filePath ?>')"
                                            class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-lg transition"
                                            title="Ouvrir dans le navigateur">
                                        <i class="fas fa-eye mr-1"></i>Navigateur
                                    </button>
                                    
                                    <!-- Bouton Ouvrir (application native) -->
                                    <button onclick="ouvrirAvecApplication('<?= $filePath ?>', '<?= $item['ext'] ?>', '<?= $fileName ?>')"
                                            class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded-lg transition"
                                            title="Ouvrir avec l'application par défaut">
                                        <i class="fas fa-desktop mr-1"></i>App
                                    </button>
                                <?php endif; ?>
                                
                                <!-- Menu déroulant "Plus d'options" -->
                                <div class="dropdown">
                                    <button class="px-3 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-xs rounded-lg transition"
                                            title="Plus d'options">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-content">
                                        <?php if ($isPDF): ?>
                                        <a href="/Documents<?= $filePath ?>" target="_blank">
                                            <i class="fas fa-eye mr-2"></i>Voir dans le navigateur
                                        </a>
                                        <?php endif; ?>
                                        <a href="#" onclick="event.preventDefault(); telechargerFichier('<?= $filePath ?>', '<?= $fileName ?>')">
                                            <i class="fas fa-download mr-2"></i>Télécharger
                                        </a>
                                        <a href="#" onclick="event.preventDefault(); navigator.clipboard.writeText('<?= $fullServerPath ?>')">
                                            <i class="fas fa-copy mr-2"></i>Copier le chemin
                                        </a>
                                        <?php if ($isPDF): ?>
                                        <a href="https://docs.google.com/viewer?url=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . '/Documents' . $filePath) ?>" target="_blank">
                                            <i class="fas fa-file-pdf mr-2"></i>Google PDF Viewer
                                        </a>
                                        <?php endif; ?>
                                        <?php if (in_array($item['ext'], ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])): ?>
                                        <a href="https://view.officeapps.live.com/op/view.aspx?src=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . '/Documents' . $filePath) ?>" target="_blank">
                                            <i class="fas fa-file-alt mr-2"></i>Office Online
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                            endif;
                        endforeach;
                    }
                    
                    afficherArborescence($arborescenceComplete);
                    ?>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- === MODE : LISTE DES EMPLOYÉS === -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($data[1] as $emp): ?>
                <?php
                    $dossierNom = $emp['nom'] . '_' . $emp['prenom'] . '_' . $emp['id_employe'];
                    $dossierChemin = $basePath . '/' . $dossierNom;
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
                                    <h3 class="text-xl font-bold"><?= htmlspecialchars($emp['nom'] . ' ' . $emp['prenom']) ?></h3>
                                    <p class="text-sm opacity-90"><?= htmlspecialchars($emp['poste'] ?? 'Poste non défini') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="flex justify-between"><span>ID :</span> <strong>#<?= $emp['id_employe'] ?></strong></div>
                            <div class="flex justify-between"><span>Dépt :</span> <strong><?= htmlspecialchars($emp['nom_departement'] ?? '—') ?></strong></div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <?php if ($dossierExiste): ?>
                                <a href="?voir=<?= $emp['id_employe'] ?>"
                                   class="w-full block text-center bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg transition">
                                    <i class="fas fa-folder-open mr-2"></i>
                                    Voir le dossier
                                </a>
                            <?php else: ?>
                                <a href="?creer=<?= $emp['id_employe'] ?>"
                                   class="w-full block text-center bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 rounded-xl shadow-lg transition">
                                    <i class="fas fa-folder-plus mr-2"></i>
                                    Créer le dossier complet
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="mt-12 text-center bg-white/80 backdrop-blur rounded-2xl p-6 shadow-lg">
        <p class="text-gray-600">
            <strong>Chemin de stockage :</strong><br>
            <code class="text-sm bg-gray-800 text-white px-4 py-2 rounded mt-2 inline-block">
                <?= htmlspecialchars(realpath($basePath) ?: $basePath) ?>
            </code>
        </p>
    </div>
</div>

</body>
</html>