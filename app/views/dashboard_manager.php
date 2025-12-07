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
}*/?>
<?php include "headerA.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"/>
<title>Dashboard Manager</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  body{font-family:Arial,Helvetica,sans-serif;margin:18px;color:#222;background:#f8f9fa}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;max-width:1400px;margin:0 auto}
  .card{padding:20px;border:1px solid #ddd;border-radius:8px;background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.08)}
  h1,h2{margin:6px 0}
  ul.stats{list-style:none;padding:0;margin:0}
  ul.stats li{padding:8px 0;font-size:1.05em}
  table{width:100%;border-collapse:collapse;margin-top:10px}
  table th,table td{border:1px solid #eee;padding:10px;text-align:left}
  table th{background:#f8f9fa;font-weight:600}
  .urgent{color:#b00;font-weight:600}
  .controls{margin-bottom:12px}
  .small{font-size:0.9em;color:#666;margin-top:10px}
  .btn{display:inline-block;padding:10px 16px;background:#2563eb;color:#fff;border-radius:6px;text-decoration:none;margin-right:8px;margin-top:8px;transition:background 0.3s}
  .btn:hover{background:#1d4ed8}
  .chart-container{height:320px;margin-top:15px}
  .full-chart{height:400px}
</style>
</head>
<body>

<div style="max-width:1400px;margin:0 auto 20px">
  <h1>Dashboard Manager</h1>
  <p class="small">Vue équipe — récapitulatif & tâches urgentes</p>
</div>

<div class="grid">
  <div class="card">
    <h2>📊 Récapitulatif</h2>
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

    <p style="margin-top:16px">
      <a class="btn" href="/perform"> Générer rapport</a>
    </p>
  </div>

  <div class="card">
    <h2> Score moyen par employé</h2>
    <div class="chart-container">
        <canvas id="barChart"></canvas>
    </div>
    <p class="small">Données : scores moyens des évaluations terminées</p>
  </div>

  <div class="card" style="grid-column:1 / -1">
    <h2> Évaluations en retard / urgentes</h2>

    <?php if (empty($urgent)): ?>
      <p style="color:#059669;padding:12px;background:#d1fae5;border-radius:6px">✓ Aucune évaluation en retard pour le moment.</p>
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
              <td><a href="/evaluation/<?= $u['id_evaluation'] ?>" style="color:#2563eb">Voir détails →</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
    <div style="margin-top:16px">
      <a href="/evaluations" class="btn">📋 Voir la liste d'évaluation</a>
      <a href="/traiterEval" class="btn">➕ Planifier une évaluation</a>
    </div>
  </div>

  <div class="card" style="grid-column:1 / -1">
    <h2> Tendances (6 derniers mois)</h2>
    <div class="chart-container full-chart">
        <canvas id="trendChart"></canvas>
    </div>
    <p class="small">Graphique : évolution des scores par employé sur les 6 derniers mois</p>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // BAR CHART - Scores moyens par employé
    const barCtx = document.getElementById('barChart');
    if (barCtx) {
        const rawData = <?= json_encode($trend ?? []) ?>;
        
        if (!rawData || rawData.length === 0) {
            barCtx.parentNode.innerHTML = '<p style="text-align:center;color:#666;padding:40px">Aucune donnée disponible</p>';
        } else {
            // Regrouper par employé (score_moyen est déjà calculé par SQL)
            const employeeScores = {};
            
            rawData.forEach(r => {
                const name = r.employe || r.nom || 'Inconnu';
                const scoreMoyen = parseFloat(r.score_moyen || 0);
                
                // Comme score_moyen est le même pour tous les enregistrements d'un employé,
                // on prend juste la première valeur rencontrée
                if (!employeeScores[name]) {
                    employeeScores[name] = scoreMoyen;
                }
            });
            
            const names = Object.keys(employeeScores);
            const scores = Object.values(employeeScores);
            
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: names,
                    datasets: [{
                        label: 'Score moyen',
                        data: scores,
                        backgroundColor: 'rgba(102, 126, 234, 0.6)',
                        borderColor: 'rgba(102, 126, 234, 1)',
                        borderWidth: 2
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
                                    size: 13,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
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
                            title: {
                                display: true,
                                text: 'Score moyen (%)',
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
                            title: {
                                display: true,
                                text: 'Employés',
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
    }

    // TREND CHART - Évolution sur 6 mois
    // TREND CHART - Évolution sur 6 mois
const trendCtx = document.getElementById('trendChart');
if (trendCtx) {
    const rawTrend = <?= json_encode($trend ?? []) ?>;
    
    console.log('Données brutes pour trend:', rawTrend); // DEBUG

    if (!rawTrend || rawTrend.length === 0) {
        trendCtx.parentNode.innerHTML = '<p style="text-align:center;color:#666;padding:40px">Aucune donnée de tendance disponible</p>';
    } else {
        // APPROCHE SIMPLIFIÉE
        // 1. Créer un tableau des mois uniques
        const dateMap = {};
        rawTrend.forEach(r => {
            const month = parseInt(r.mois);
            const year = parseInt(r.annee);
            const key = `${year}-${month.toString().padStart(2, '0')}`;
            const label = new Date(year, month - 1, 1).toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
            dateMap[key] = label;
        });
        
        // 2. Trier les dates
        const labels = Object.keys(dateMap)
            .sort()
            .map(key => dateMap[key]);
        
        console.log('Labels triés:', labels);
        
        // 3. Récupérer tous les employés
        const employees = {};
        rawTrend.forEach(r => {
            const name = r.employe || r.nom || 'Inconnu';
            employees[name] = true;
        });
        
        const employeeNames = Object.keys(employees);
        console.log('Employés:', employeeNames);
        
        // 4. Créer les datasets
        const colors = [
            'rgba(102, 126, 234, 1)',
            'rgba(118, 75, 162, 1)',
            'rgba(52, 168, 83, 1)',
            'rgba(251, 188, 5, 1)',
            'rgba(234, 67, 53, 1)',
            'rgba(66, 133, 244, 1)',
            'rgba(156, 39, 176, 1)',
            'rgba(255, 109, 0, 1)'
        ];
        
        const datasets = employeeNames.map((name, index) => {
            // Pour chaque employé, créer un tableau de données pour chaque mois
            const data = labels.map(label => {
                // Trouver la donnée correspondante
                const dataPoint = rawTrend.find(r => {
                    const rName = r.employe || r.nom || 'Inconnu';
                    const rMonth = parseInt(r.mois);
                    const rYear = parseInt(r.annee);
                    const rLabel = new Date(rYear, rMonth - 1, 1).toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
                    return rName === name && rLabel === label;
                });
                
                return dataPoint ? parseFloat(dataPoint.score_total) : null;
            });
            
            console.log(`Dataset pour ${name}:`, data);
            
            return {
                label: name,
                data: data,
                borderColor: colors[index % colors.length],
                backgroundColor: colors[index % colors.length].replace('1)', '0.1)'),
                borderWidth: 3,
                tension: 0.2,
                pointRadius: 6,
                pointHoverRadius: 8,
                fill: false
            };
        });
        
        // 5. Créer le graphique
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 13,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Score (%)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Période'
                        }
                    }
                }
            }
        });
    }
}
});
</script>

</body>
</html>
<?php include "footer.php"; ?>