<?php include "headerA.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Score d'évaluation</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .score-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
        }
        .main-score {
            font-size: 4rem;
            font-weight: bold;
            margin: 10px 0;
        }
        .table-container {
            overflow-x: auto;
            margin: 30px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .chart-container {
            max-width: 900px;
            height: 600px;
            margin: 40px auto;
            padding: 20px;
        }
        .btn-primary {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .warning {
            color: #856404;
            background-color: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ffeaa7;
        }
        .actions {
            text-align: center;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête du score -->
        <div class="score-header">
            <h2>Score de l'évaluation</h2>
            <div class="main-score"><?= number_format($score, 2) ?>/100</div>
            <p>Détail des critères évalués</p>
        </div>

        <!-- Tableau des détails -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Critère</th>
                        <th>Poids</th>
                        <th>Note</th>
                        <th>Score calculé</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['critere'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($d['poids'] ?? '0') ?>%</td>
                            <td><?= htmlspecialchars($d['note'] ?? '0') ?>/10</td>
                            <td><strong><?= number_format(($d['note'] ?? 0) * ($d['poids'] ?? 0) / 10, 2) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Graphique radar -->
        <div class="chart-container">
            <canvas id="radarChart"></canvas>
        </div>

        <!-- Actions -->
        <div class="actions">
            <?php if (!empty($details) && isset($details[0]['employe_id'])): ?>
                <a href="/employee/<?= $details[0]['employe_id'] ?>/historique" class="btn-primary">
                    Voir historique des évaluations
                </a>
            <?php else: ?>
                <p class="warning">ID employé non disponible pour l'historique</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Configuration du graphique radar avec données réelles
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('radarChart').getContext('2d');
            
            // Préparation des labels (noms des critères)
            var labels = [
                <?php 
                $labelArray = [];
                foreach ($details as $d) {
                    $labelArray[] = '"' . addslashes($d['critere'] ?? 'Critère') . '"';
                }
                echo implode(',', $labelArray);
                ?>
            ];
            
            // Préparation des notes (valeurs sur 10)
            var notes = [
                <?php 
                $noteArray = [];
                foreach ($details as $d) {
                    $noteArray[] = floatval($d['note'] ?? 0);
                }
                echo implode(',', $noteArray);
                ?>
            ];

            // Création du graphique radar
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Note sur 10',
                        data: notes,
                        backgroundColor: 'rgba(102, 126, 234, 0.2)',
                        borderColor: 'rgba(102, 126, 234, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(102, 126, 234, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(102, 126, 234, 1)',
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        r: {
                            angleLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            pointLabels: {
                                font: {
                                    size: 12
                                }
                            },
                            min: 0,
                            max: 10,
                            ticks: {
                                stepSize: 2,
                                backdropColor: 'transparent'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Profil de performance par critère',
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            padding: 20
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Note: ' + context.parsed.r + '/10';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
<?php include "footer.php"; ?>