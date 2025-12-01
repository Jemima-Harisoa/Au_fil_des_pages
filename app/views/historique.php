<?php include "headerA.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des évaluations</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .header h2 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 2.2rem;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .table-container {
            overflow-x: auto;
            margin: 30px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th {
            background-color: #2c3e50;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            border: none;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }
        .btn-view {
            display: inline-block;
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .btn-view:hover {
            background-color: #2980b9;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .chart-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin: 40px 0;
            height: 400px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }
        .score-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .score-excellent { background-color: #27ae60; color: white; }
        .score-good { background-color: #3498db; color: white; }
        .score-average { background-color: #f39c12; color: white; }
        .score-poor { background-color: #e74c3c; color: white; }
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            th, td {
                padding: 8px 6px;
                font-size: 0.9rem;
            }
            .header h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h2>📊 Historique des évaluations</h2>
            <p>Suivi des performances au fil du temps</p>
        </div>

        <!-- Statistiques -->
        <?php if (!empty($evaluations)): ?>
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($evaluations) ?></div>
                <div class="stat-label">Évaluations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?= number_format(array_sum(array_column($evaluations, 'score_total')) / count($evaluations), 1) ?>%
                </div>
                <div class="stat-label">Moyenne générale</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?= number_format(max(array_column($evaluations, 'score_total')), 1) ?>%
                </div>
                <div class="stat-label">Meilleur score</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?= date('M Y', strtotime($evaluations[0]['date_evaluation'])) ?>
                </div>
                <div class="stat-label">Dernière évaluation</div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tableau des évaluations -->
        <div class="table-container">
            <?php if (!empty($evaluations)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date d'évaluation</th>
                            <th>Score total</th>
                            <th>Niveau</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($evaluations as $e): ?>
                            <?php
                            $score = floatval($e['score_total']);
                            $scoreClass = '';
                            $level = '';
                            if ($score >= 80) {
                                $scoreClass = 'score-excellent';
                                $level = 'Excellent';
                            } elseif ($score >= 60) {
                                $scoreClass = 'score-good';
                                $level = 'Bon';
                            } elseif ($score >= 40) {
                                $scoreClass = 'score-average';
                                $level = 'Moyen';
                            } else {
                                $scoreClass = 'score-poor';
                                $level = 'À améliorer';
                            }
                            ?>
                            <tr>
                                <td>
                                    <strong><?= date('d/m/Y', strtotime($e['date_evaluation'])) ?></strong>
                                    <br>
                                    <small style="color: #7f8c8d;">
                                        <?= date('H:i', strtotime($e['date_evaluation'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <strong><?= number_format($score, 1) ?>%</strong>
                                    <br>
                                    <small style="color: #7f8c8d;">
                                        (<?= number_format($score / 10, 1) ?>/10)
                                    </small>
                                </td>
                                <td>
                                    <span class="score-badge <?= $scoreClass ?>">
                                        <?= $level ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/evaluation/<?= $e['id_evaluation'] ?>/score" class="btn-view">
                                        📋 Voir le détail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <h3>Aucune évaluation trouvée</h3>
                    <p>L'historique des évaluations est vide pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Graphique d'évolution -->
        <?php if (!empty($evaluations)): ?>
        <div class="chart-container">
            <canvas id="lineChart"></canvas>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($evaluations)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('lineChart').getContext('2d');
            
            // Préparation des données
            var labels = [
                <?php foreach ($evaluations as $e): ?>
                    "<?= date('d/m/Y', strtotime($e['date_evaluation'])) ?>",
                <?php endforeach; ?>
            ];
            
            var data = [
                <?php foreach ($evaluations as $e): ?>
                    <?= number_format($e['score_total'], 1) ?>,
                <?php endforeach; ?>
            ];

            // Création du graphique
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Évolution des scores (%)',
                        data: data,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3498db',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Progression des performances',
                            font: {
                                size: 16
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Score: ' + context.parsed.y + '%';
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
                                text: 'Dates d\'évaluation'
                            }
                        }
                    }
                }
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
<?php include "footer.php"; ?>