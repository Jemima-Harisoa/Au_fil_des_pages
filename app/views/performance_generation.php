<?php include "headerA.php"; ?>
<?php
// === Données de test ===
$annee = 2025;
$employeNom = "Jean Rakoto";

// Simule les performances par période
$performanceData = [
    'evaluations' => [
        ['date_evaluation' => '2025-01-15', 'periode' => 'Trimestre 1', 'score_total' => 45.0],
        ['date_evaluation' => '2025-04-15', 'periode' => 'Trimestre 2', 'score_total' => 50.0],
        ['date_evaluation' => '2025-07-15', 'periode' => 'Trimestre 3', 'score_total' => 48.5],
        ['date_evaluation' => '2025-10-15', 'periode' => 'Trimestre 4', 'score_total' => 52.0],
    ],
    'score_annuel' => 195.5,
    'score_moyen' => 48.875,
    'par_periode' => [
        ['periode' => 'Trimestre 1', 'date_evaluation' => '2025-01-15', 'score_total' => 45.0],
        ['periode' => 'Trimestre 2', 'date_evaluation' => '2025-04-15', 'score_total' => 50.0],
        ['periode' => 'Trimestre 3', 'date_evaluation' => '2025-07-15', 'score_total' => 48.5],
        ['periode' => 'Trimestre 4', 'date_evaluation' => '2025-10-15', 'score_total' => 52.0],
    ],
];

// Simule la comparaison de deux périodes
$comparison = [
    ['periode' => 'Trimestre 1', 'score_pondere' => 9.0],
    ['periode' => 'Trimestre 2', 'score_pondere' => 9.5],
];
?>

<h1>Performance annuelle de l'employé :<?= htmlspecialchars($employeNom ?? 'Tous') ?> (<?= $performanceData['evaluations'][0]['date_evaluation'] ?? $annee ?>)</h1>

<!-- Résumé global -->
<div>
    <p><strong>Score annuel :</strong> <?= number_format($performanceData['score_annuel'],2) ?></p>
    <p><strong>Score moyen :</strong> <?= number_format($performanceData['score_moyen'],2) ?></p>
</div>

<!-- Graphique ligne - évolution des scores -->
<canvas id="lineChart"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labelsLine = <?= json_encode(array_map(fn($e)=>$e['periode'],$performanceData['par_periode'])) ?>;
const scoresLine = <?= json_encode(array_map(fn($e)=>floatval($e['score_total']),$performanceData['par_periode'])) ?>;

new Chart(document.getElementById('lineChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: labelsLine,
        datasets: [{
            label: 'Score par période',
            data: scoresLine,
            borderColor: '#3b82f6',
            fill: false,
            tension: 0.3
        }]
    },
    options: { responsive: true }
});
</script>

<!-- Comparaison deux périodes -->
<?php if(!empty($comparison)): ?>
<h2>Comparaison des périodes</h2>
<table border="1" cellpadding="5">
<tr>
    <th>Période</th>
    <th>Score pondéré moyen</th>
</tr>
<?php foreach($comparison as $row): ?>
<tr>
    <td><?= htmlspecialchars($row['periode']) ?></td>
    <td><?= number_format($row['score_pondere'],2) ?></td>
</tr>
<?php endforeach; ?>
</table>

<canvas id="barChart"></canvas>
<script>
const labelsBar = <?= json_encode(array_column($comparison,'periode')) ?>;
const scoresBar = <?= json_encode(array_map(fn($r)=>floatval($r['score_pondere']), $comparison)) ?>;

new Chart(document.getElementById('barChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: labelsBar,
        datasets: [{
            label: 'Score pondéré par période',
            data: scoresBar,
            backgroundColor: ['#3b82f6','#ef4444']
        }]
    },
    options: { responsive:true }
});
</script>
<?php else: ?>
<p>Aucune comparaison de périodes disponible.</p>
<?php endif; ?>

<!-- Tableau détaillé -->
<h2>Détails des évaluations</h2>
<table border="1" cellpadding="5">
<tr>
    <th>Période</th>
    <th>Date évaluation</th>
    <th>Score</th>
</tr>
<?php foreach($performanceData['par_periode'] as $row): ?>
<tr>
    <td><?= htmlspecialchars($row['periode']) ?></td>
    <td><?= htmlspecialchars($row['date_evaluation']) ?></td>
    <td><?= number_format($row['score_total'],2) ?></td>
</tr>
<?php endforeach; ?>
</table>

<?php include "footer.php"; ?>