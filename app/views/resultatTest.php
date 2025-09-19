<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat du Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        .score {
            padding: 15px;
            background-color: #f0f0f0;
            border-left: 5px solid #4CAF50;
            margin-bottom: 20px;
        }
        .question {
            margin-bottom: 15px;
        }
        .reponse {
            margin-left: 20px;
        }
        .correct {
            color: green;
        }
        .incorrect {
            color: red;
        }
    </style>
</head>
<body>

    <h1>Résultats du Test QCM</h1>
    <div class="score">
        <strong>Votre score :</strong> <?= htmlspecialchars($score) ?>
    </div>
    <h2>Détails de vos réponses</h2>
    <?php foreach ($reponses as $idQst => $choix): ?>
        <div class="question">
            <p><strong>Question #<?= $idQst ?></strong></p>
            <p class="reponse">Vos choix : <?= implode(", ", $choix) ?></p>
        </div>
    <?php endforeach; ?>

</body>
</html>
