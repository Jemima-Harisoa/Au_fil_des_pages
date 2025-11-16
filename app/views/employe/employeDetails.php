<?php
// =============================================================
// DONNÉES DE L'EMPLOYÉ
// =============================================================
$employee = [
    'photo' => '',
    'nom' => 'Dupont',
    'prenoms' => 'Jean Marc',
    'dateNaissance' => '1985-03-15',
    'contact' => '+261 34 00 000 00',
    'poste' => 'Développeur Full Stack',
    'dateEmbauche' => '2018-06-01',
    'departement' => 'Informatique'
];

// =============================================================
// HISTORIQUE DES MOUVEMENTS (avec département)
// =============================================================
$historique = [
    [
        'id' => 1,
        'date' => '2017-08-15',
        'evenement' => 'Embauche',
        'details' => 'Embauche en CDI après stage de 3 mois',
        'support' => 'Contrat-CDI-2017-089.pdf',
        'poste' => 'Stagiaire Développeur',
        'departement' => 'Ressources Humaines'
    ],
    [
        'id' => 2,
        'date' => '2018-01-01',
        'evenement' => 'Confirmation',
        'details' => 'Fin de période d\'essai - Confirmation définitive',
        'support' => 'Avenant-Confirmation-2018.pdf',
        'poste' => 'Développeur Junior',
        'departement' => 'Informatique'
    ],
    [
        'id' => 3,
        'date' => '2019-06-10',
        'evenement' => 'Formation',
        'details' => 'Formation Laravel avancé (40h) - Certificat obtenu',
        'support' => 'Certificat-Laravel-2019.pdf',
        'poste' => 'Développeur Junior',
        'departement' => 'Informatique'
    ],
    [
        'id' => 4,
        'date' => '2020-03-01',
        'evenement' => 'Promotion',
        'details' => 'Passage au grade Développeur Confirmé suite à projet CRM',
        'support' => 'Décision-Promotion-2020-015.pdf',
        'poste' => 'Développeur Confirmé',
        'departement' => 'Informatique'
    ],
    [
        'id' => 5,
        'date' => '2021-07-20',
        'evenement' => 'Mutation',
        'details' => 'Transfert vers le pôle Intelligence Artificielle',
        'support' => 'Ordre-Mutation-2021-112.pdf',
        'poste' => 'Développeur Full Stack',
        'departement' => 'Intelligence Artificielle'
    ],
    [
        'id' => 6,
        'date' => '2022-11-05',
        'evenement' => 'Augmentation',
        'details' => 'Augmentation de 12% suite à évaluation annuelle',
        'support' => 'Avenant-Salaire-2022.pdf',
        'poste' => 'Développeur Full Stack',
        'departement' => 'Intelligence Artificielle'
    ],
    [
        'id' => 7,
        'date' => '2023-04-18',
        'evenement' => 'Congé formation',
        'details' => 'Congé de 2 mois pour MBA en Gestion de Projet IT',
        'support' => 'Demande-Conge-2023.pdf',
        'poste' => 'Développeur Full Stack',
        'departement' => 'Intelligence Artificielle'
    ],
    [
        'id' => 8,
        'date' => '2023-09-01',
        'evenement' => 'Retour de congé',
        'details' => 'Reprise après formation - Nouveau rôle de Lead Tech',
        'support' => 'Retour-Conge-2023.pdf',
        'poste' => 'Lead Developer',
        'departement' => 'Informatique'
    ],
    [
        'id' => 9,
        'date' => '2024-02-14',
        'evenement' => 'Certification',
        'details' => 'Obtention de la certification AWS Solutions Architect',
        'support' => 'Certificat-AWS-2024.pdf',
        'poste' => 'Lead Developer',
        'departement' => 'Informatique'
    ],
    [
        'id' => 10,
        'date' => '2025-01-10',
        'evenement' => 'Évaluation',
        'details' => 'Évaluation annuelle 2024 - Note A+',
        'support' => 'Fiche-Evaluation-2024.pdf',
        'poste' => 'Lead Developer',
        'departement' => 'Informatique'
    ]
];

// Fonction pour formater la date
function formatDate($dateStr) {
    return date('d/m/Y', strtotime($dateStr));
}

// Fonction pour la couleur de l'événement
function getEventColor($event) {
    $colors = [
        'Embauche' => 'text-green-700',
        'Promotion' => 'text-blue-700',
        'Mutation' => 'text-purple-700',
        'Formation' => 'text-orange-700',
        'Confirmation' => 'text-teal-700',
        'Augmentation' => 'text-yellow-700',
        'Congé formation' => 'text-indigo-700',
        'Retour de congé' => 'text-cyan-700',
        'Certification' => 'text-pink-700',
        'Évaluation' => 'text-gray-700'
    ];
    return $colors[$event] ?? 'text-gray-700';
}

// === Calcul de l'âge (années uniquement) ===
$dateNaissance = new DateTime($employee['dateNaissance']);
$aujourd = new DateTime();
$age_annees = $dateNaissance->diff($aujourd)->y;

// === Calcul de l'ancienneté (années + jours) ===
$dateEmbauche = new DateTime($employee['dateEmbauche']);
$interval = $dateEmbauche->diff($aujourd);
$anciennete_annees = $interval->y;
$anciennete_jours = $interval->days;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Employé</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête -->
        <div class="bg-white rounded-t-xl shadow-lg p-6 mb-0">
            <h1 class="text-3xl font-bold text-gray-800 text-center">Fiche Employé</h1>
        </div>

        <!-- Corps principal -->
        <div class="bg-white rounded-b-xl shadow-lg p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Colonne Photo -->
                <div class="flex flex-col items-center">
                    <div class="w-48 h-48 bg-gray-200 border-2 border-dashed rounded-xl flex items-center justify-center mb-4 overflow-hidden">
                        <?php if (!empty($employee['photo'])): ?>
                            <img src="<?= htmlspecialchars($employee['photo']) ?>" alt="Photo de l'employé" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fas fa-user text-6xl text-gray-400"></i>
                        <?php endif; ?>
                    </div>
                    <p class="text-sm font-medium text-gray-700">Photo</p>
                </div>

                <!-- Colonne Informations -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Nom -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                        <p class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($employee['nom']) ?></p>
                    </div>

                    <!-- Prénoms -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prénoms</label>
                        <p class="text-lg text-gray-800"><?= htmlspecialchars($employee['prenoms']) ?></p>
                    </div>

                    <!-- Âge (années uniquement) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Âge</label>
                        <p class="text-lg text-gray-800">
                            <?= $age_annees ?> an<?= $age_annees > 1 ? 's' : '' ?>
                        </p>
                    </div>

                    <!-- Contact -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact</label>
                        <p class="text-lg text-gray-800"><?= htmlspecialchars($employee['contact']) ?></p>
                    </div>

                    <!-- Poste -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Poste</label>
                        <p class="text-lg text-gray-800"><?= htmlspecialchars($employee['poste']) ?></p>
                    </div>

                    <!-- Ancienneté (années + jours) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ancienneté</label>
                        <p class="text-lg text-blue-600 font-medium">
                            <?= $anciennete_annees ?> an<?= $anciennete_annees > 1 ? 's' : '' ?>
                            <span class="text-sm text-gray-500 ml-2">
                                (soit <?= $anciennete_jours ?> jour<?= $anciennete_jours > 1 ? 's' : '' ?>)
                            </span>
                        </p>
                    </div>

                    <!-- Département actuel -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Département actuel</label>
                        <p class="text-lg font-medium text-blue-600"><?= htmlspecialchars($employee['departement']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Bouton Modifier (fiche employé) -->
            <div class="mt-10 flex justify-end">
                <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-md flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    Modifier
                </button>
            </div>
        </div>

        <!-- === SECTION : Historique des mouvements === -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Historique de travail / Mouvements</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Événement</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détails</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Support</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poste</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Département</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($historique as $index => $item): ?>
                            <tr class="<?= $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' ?>">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    <?= formatDate($item['date']) ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium <?= getEventColor($item['evenement']) ?>">
                                    <?= htmlspecialchars($item['evenement']) ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate" title="<?= htmlspecialchars($item['details']) ?>">
                                    <?= htmlspecialchars($item['details']) ?>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="uploads/supports/<?= htmlspecialchars($item['support']) ?>" 
                                       target="_blank" 
                                       class="text-blue-600 hover:underline">
                                        <?= htmlspecialchars($item['support']) ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <?= htmlspecialchars($item['poste']) ?>
                                </td>
                                <!-- NOUVELLE COLONNE DÉPARTEMENT -->
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <?= htmlspecialchars($item['departement']) ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-center space-x-3">
                                    <!-- VOIR -->
                                    <a href="voir_mouvement.php?id=<?= $item['id'] ?>" 
                                       class="text-indigo-600 hover:text-indigo-800" 
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <!-- ÉDITER -->
                                    <a href="editer_mouvement.php?id=<?= $item['id'] ?>" 
                                       class="text-green-600 hover:text-green-800" 
                                       title="Éditer">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>