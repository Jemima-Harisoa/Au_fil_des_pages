<?php include "headerA.php"; ?>
<?php/*
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
];*/
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"/>
<title>Performance Annuelle</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  body{font-family:Arial,Helvetica,sans-serif;margin:20px;color:#222;background:#f8f9fa}
  .container{max-width:1400px;margin:0 auto}
  .header{background:#fff;padding:25px;border-radius:10px;margin-bottom:25px;box-shadow:0 2px 8px rgba(0,0,0,.1)}
  h1{margin:0 0 15px 0;color:#1e40af;font-size:1.8em}
  .summary{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin-top:20px}
  .summary-card{background:#f0f7ff;padding:15px;border-radius:8px;border-left:4px solid #2563eb}
  .summary-card strong{display:block;color:#666;font-size:0.9em;margin-bottom:5px}
  .summary-card span{font-size:1.8em;color:#1e40af;font-weight:bold}
  
  .chart-section{background:#fff;padding:30px;border-radius:10px;margin-bottom:25px;box-shadow:0 2px 8px rgba(0,0,0,.1)}
  .chart-section h2{margin:0 0 20px 0;color:#1e40af;font-size:1.4em;border-bottom:2px solid #e5e7eb;padding-bottom:10px}
  .chart-container{height:400px;margin-top:20px;position:relative}
  .chart-container-bar{height:350px;margin-top:20px;position:relative}
  
  table{width:100%;border-collapse:collapse;margin-top:15px;background:#fff}
  table th,table td{border:1px solid #e5e7eb;padding:12px;text-align:left}
  table th{background:#f8fafc;font-weight:600;color:#475569}
  table tr:hover{background:#f8fafc}
  
  .no-data{text-align:center;padding:40px;color:#9ca3af;font-style:italic}
  
  .badge{display:inline-block;padding:4px 10px;border-radius:4px;font-size:0.85em;font-weight:600}
  .badge-high{background:#dcfce7;color:#166534}
  .badge-medium{background:#fef3c7;color:#92400e}
  .badge-low{background:#fee2e2;color:#991b1b}
</style>
</head>
<body>

<div class="container">
  <!-- En-tête avec résumé -->
  <div class="header">
    <h1>📊 Performance annuelle de l'employé : <?= htmlspecialchars($employeNom ?? 'Tous') ?></h1>
    <p style="color:#64748b;margin:10px 0 0 0">Année : <?= $performanceData['evaluations'][0]['date_evaluation'] ?? $annee ?></p>
    
    <div class="summary">
      <div class="summary-card">
        <strong>Score Annuel</strong>
        <span><?= number_format($performanceData['score_annuel'], 2) ?>%</span>
      </div>
      <div class="summary-card">
        <strong>Score Moyen</strong>
        <span><?= number_format($performanceData['score_moyen'], 2) ?>%</span>
      </div>
      <div class="summary-card">
        <strong>Évaluations</strong>
        <span><?= count($performanceData['par_periode'] ?? []) ?></span>
      </div>
    </div>
  </div>

  <!-- Graphique ligne - Évolution des scores -->
  <div class="chart-section">
    <h2>📈 Évolution des Scores par Période</h2>
    <div class="chart-container">
      <canvas id="lineChart"></canvas>
    </div>
  </div>

  <!-- Comparaison deux périodes -->
  <?php if(!empty($comparison)): ?>
  <div class="chart-section">
    <h2>📊 Comparaison des Périodes</h2>
    
    <table>
      <thead>
        <tr>
          <th>Période</th>
          <th>Score pondéré moyen</th>
          <th>Performance</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($comparison as $row): 
          $score = floatval($row['score_pondere']);
          $badgeClass = $score >= 75 ? 'badge-high' : ($score >= 60 ? 'badge-medium' : 'badge-low');
          $badgeText = $score >= 75 ? 'Excellent' : ($score >= 60 ? 'Bien' : 'À améliorer');
        ?>
        <tr>
          <td><?= htmlspecialchars($row['periode']) ?></td>
          <td><strong><?= number_format($score, 2) ?>%</strong></td>
          <td><span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="chart-container-bar">
      <canvas id="barChart"></canvas>
    </div>
  </div>
  <?php else: ?>
  <div class="chart-section">
    <p class="no-data">Aucune comparaison de périodes disponible.</p>
  </div>
  <?php endif; ?>

  <!-- Tableau détaillé -->
  <div class="chart-section">
    <h2>📋 Détails des Évaluations</h2>
    <table>
      <thead>
        <tr>
          <th>Période</th>
          <th>Date d'évaluation</th>
          <th>Score</th>
          <th>Performance</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($performanceData['par_periode'] as $row): 
          $score = floatval($row['score_total']);
          $badgeClass = $score >= 75 ? 'badge-high' : ($score >= 60 ? 'badge-medium' : 'badge-low');
          $badgeText = $score >= 75 ? 'Excellent' : ($score >= 60 ? 'Bien' : 'À améliorer');
        ?>
        <tr>
          <td><?= htmlspecialchars($row['periode']) ?></td>
          <td><?= htmlspecialchars($row['date_evaluation']) ?></td>
          <td><strong><?= number_format($score, 2) ?>%</strong></td>
          <td><span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Graphique ligne - Évolution des scores
  const lineCtx = document.getElementById('lineChart');
  if (lineCtx) {
    const labelsLine = <?= json_encode(array_map(fn($e)=>$e['periode'],$performanceData['par_periode'])) ?>;
    const scoresLine = <?= json_encode(array_map(fn($e)=>floatval($e['score_total']),$performanceData['par_periode'])) ?>;

    // Créer un gradient pour la ligne
    const ctx = lineCtx.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labelsLine,
        datasets: [{
          label: 'Score par période',
          data: scoresLine,
          borderColor: '#2563eb',
          backgroundColor: gradient,
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointRadius: 6,
          pointHoverRadius: 9,
          pointBackgroundColor: '#2563eb',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointHoverBackgroundColor: '#1d4ed8',
          pointHoverBorderColor: '#fff',
          pointHoverBorderWidth: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: {
              font: {
                size: 14,
                weight: 'bold'
              },
              padding: 20,
              usePointStyle: true
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: {
              size: 14,
              weight: 'bold'
            },
            bodyFont: {
              size: 13
            },
            callbacks: {
              label: function(context) {
                return 'Score: ' + context.parsed.y.toFixed(2) + '%';
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            title: {
              display: true,
              text: 'Score (%)',
              font: {
                size: 14,
                weight: 'bold'
              }
            },
            ticks: {
              stepSize: 10,
              callback: function(value) {
                return value + '%';
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            title: {
              display: true,
              text: 'Période',
              font: {
                size: 14,
                weight: 'bold'
              }
            }
          }
        }
      }
    });
  }

  // Graphique barre - Comparaison
  <?php if(!empty($comparison)): ?>
  const barCtx = document.getElementById('barChart');
  if (barCtx) {
    const labelsBar = <?= json_encode(array_column($comparison,'periode')) ?>;
    const scoresBar = <?= json_encode(array_map(fn($r)=>floatval($r['score_pondere']), $comparison)) ?>;

    // Couleurs dynamiques selon le score
    const backgroundColors = scoresBar.map(score => {
      if (score >= 75) return 'rgba(34, 197, 94, 0.7)';
      if (score >= 60) return 'rgba(234, 179, 8, 0.7)';
      return 'rgba(239, 68, 68, 0.7)';
    });

    const borderColors = scoresBar.map(score => {
      if (score >= 75) return 'rgba(34, 197, 94, 1)';
      if (score >= 60) return 'rgba(234, 179, 8, 1)';
      return 'rgba(239, 68, 68, 1)';
    });

    new Chart(barCtx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: labelsBar,
        datasets: [{
          label: 'Score pondéré par période',
          data: scoresBar,
          backgroundColor: backgroundColors,
          borderColor: borderColors,
          borderWidth: 2,
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: {
              font: {
                size: 14,
                weight: 'bold'
              },
              padding: 20
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: {
              size: 14,
              weight: 'bold'
            },
            bodyFont: {
              size: 13
            },
            callbacks: {
              label: function(context) {
                return 'Score: ' + context.parsed.y.toFixed(2) + '%';
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            title: {
              display: true,
              text: 'Score pondéré (%)',
              font: {
                size: 14,
                weight: 'bold'
              }
            },
            ticks: {
              stepSize: 10,
              callback: function(value) {
                return value + '%';
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            title: {
              display: true,
              text: 'Période',
              font: {
                size: 14,
                weight: 'bold'
              }
            }
          }
        }
      }
    });
  }
  <?php endif; ?>
});
</script>

<?php include "footer.php"; ?>
</body>
</html>