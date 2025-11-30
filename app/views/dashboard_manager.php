<?php
// Données de test pour résumé
/*$summary = [
    'prevue' => 2,
    'en_cours' => 1,
    'terminee' => 3,
    'moyenne' => 0.87
];

// Données de test pour évaluations urgentes
$urgent = [
    [
        'id_evaluation' => 10,
        'employe' => 'Alice Durand',
        'periode' => 'Mensuel',
        'statut' => 'EN_COURS',
        'date_evaluation' => '2025-11-25'
    ],
    [
        'id_evaluation' => 11,
        'employe' => 'Pierre Lambert',
        'periode' => 'Trimestriel',
        'statut' => 'PREVUE',
        'date_evaluation' => '2025-11-20'
    ]
];

// Données de test pour le graphique des scores moyens et tendance (6 mois)
$trend = [];
$employees = ['Jean Dupont', 'Marie Martin', 'Pierre Lambert', 'Sophie Bernard'];
$now = new DateTime();

foreach ($employees as $emp) {
    for ($i = 5; $i >= 0; $i--) {
        $m = (clone $now)->modify("-$i month");
        $trend[] = [
            'employe' => $emp,
            'annee' => (int)$m->format('Y'),
            'mois' => (int)$m->format('n'),
            'score_total' => rand(60, 100) / 10, // score entre 6.0 et 10.0
            'score_moyen' => rand(60, 100) / 100 // score moyen pour bar chart entre 0.6 et 1.0
        ];
    }
}*/
?>

<?php include "headerA.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"/>
<title>Dashboard Manager</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  body{font-family:Arial,Helvetica,sans-serif;margin:18px;color:#222}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
  .card{padding:16px;border:1px solid #ddd;border-radius:8px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.03)}
  h1,h2{margin:6px 0}
  ul.stats{list-style:none;padding:0;margin:0}
  ul.stats li{padding:6px 0}
  table{width:100%;border-collapse:collapse;margin-top:10px}
  table th,table td{border:1px solid #eee;padding:8px;text-align:left}
  .urgent{color:#b00;font-weight:600}
  .controls{margin-bottom:12px}
  .small{font-size:0.9em;color:#666}
  .btn{display:inline-block;padding:8px 12px;background:#2563eb;color:#fff;border-radius:6px;text-decoration:none}
</style>
</head>
<body>


</div>
<h1>Dashboard manager</h1>
<p class="small">Vue équipe — récapitulatif & tâches urgentes</p>

<div class="grid">
  <div class="card">
    <h2>Récapitulatif</h2>
    <?php
      $prevue = $summary['PREVUE'] ?? $summary['prevue'] ?? 0;
      $encours = $summary['EN_COURS'] ?? $summary['en_cours'] ?? 0;
      $terminee = $summary['TERMINEE'] ?? $summary['terminee'] ?? 0;
      $moyenne = $summary['moyenne'] ?? 0;
    ?>
    <ul class="stats">
      <li>Évaluations prévues : <strong><?= (int)$prevue ?></strong></li>
      <li>Évaluations en cours : <strong><?= (int)$encours ?></strong></li>
      <li>Évaluations terminées : <strong><?= (int)$terminee ?></strong></li>
      <li>Moyenne de performance : <strong><?= is_numeric($moyenne) ? number_format($moyenne,2) : $moyenne ?></strong></li>
    </ul>

    <p style="margin-top:12px">
      <a class="btn" href="/perform">Générer rapport</a>
    </p>
  </div>

 <div class="card">
    <h2>Comparatif — Score moyen par employé</h2>
    <div class="chart-container">
        <canvas id="barChart"></canvas>
    </div>
    <p class="small">Données : scores moyens (TERMINEE) par employé</p>
</div>

  <div class="card" style="grid-column:1 / -1">
    <h2>Évaluations en retard / urgentes</h2>

    <?php if (empty($urgent)): ?>
      <p>Aucune évaluation en retard pour le moment. Respire</p>
    <?php else: ?>
      <table>
        <thead>
          <tr><th>Employé</th><th>Période</th><th>Statut</th><th>Date d'évaluation</th><th>Action</th></tr>
        </thead>
        <tbody>
          <?php foreach ($urgent as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['employe'] ?? $u['nom'] ?? '—') ?></td>
              <td><?= htmlspecialchars($u['periode'] ?? '—') ?></td>
              <td class="urgent"><?= htmlspecialchars($u['statut']) ?></td>
              <td><?= htmlspecialchars($u['date_evaluation'] ?? $u['date_prevue'] ?? '') ?></td>
              <td><a href="/evaluation/<?= $u['id_evaluation'] ?>">Voir</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
            <a href="/evaluations">VOIR LA LISTE D'EVALUATION</a>
            <a href="/traiterEval">PLANIFIER UNE EVALUATION</a>
  </div>

  
<div class="card" style="grid-column:1 / -1">
    <h2>Tendances (6 derniers mois)</h2>
    <div class="chart-container">
        <canvas id="trendChart"></canvas>
    </div>
    <p class="small">Graphique : evolution des scores par employé</p>
</div>
<!-- DEBUG -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // BAR CHART - Scores moyens
    const barCtx = document.getElementById('barChart');
    if (barCtx) {
        const rawData = <?= json_encode($trend ?? []) ?>;
        
        // Si pas de données, affiche un message
        if (!rawData || rawData.length === 0) {
            barCtx.style.display = 'none';
            barCtx.parentNode.innerHTML += '<p>Aucune donnée disponible pour le graphique</p>';
        } else {
            const names = [], scores = [];
            
            rawData.forEach(r => {
                const name = r.employe || r.nom || 'Inconnu';
                const score = parseFloat(r.score_moyen || r.score_total || r.score || 0);
                names.push(name);
                scores.push(score);
            });
            
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: names,
                    datasets: [{
                        label: 'Score moyen',
                        data: scores,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 1.0
                        }
                    }
                }
            });
        }
    }

    // TREND CHART - Évolution sur 6 mois
const trendCtx = document.getElementById('trendChart');
if (trendCtx) {
    const rawTrend = <?= json_encode($trend ?? []) ?>;

    // Construire les labels (6 derniers mois)
    const labels = [];
    const now = new Date();
    for (let i = 5; i >= 0; i--) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        labels.push(d.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' }));
    }

    // Préparer les datasets par employé
    const datasets = [];
    const employees = {};

    rawTrend.forEach(r => {
        const name = r.employe || r.nom || 'Inconnu';
        const month = new Date(r.annee, r.mois - 1, 1).toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
        if (!employees[name]) {
            employees[name] = Array(6).fill(null); // 6 derniers mois
        }
        const idx = labels.indexOf(month);
        if (idx !== -1) {
            employees[name][idx] = parseFloat(r.score_total || 0);
        }
    });

    for (const [name, data] of Object.entries(employees)) {
        datasets.push({
            label: name,
            data: data,
            borderColor: `hsl(${Math.random() * 360}, 70%, 50%)`,
            tension: 0.1
        });
    }

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 10 // score sur 10
                }
            }
        }
    });
}
});
</script>

</body>
</html>
<?php include "footer.php"; ?>