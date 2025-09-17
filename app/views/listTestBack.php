<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Tests</title>
</head>
<body>
    <h1>Liste des Tests</h1>
    <form action="/triageTests" method="get">

   
    <ul>
        <?php
        
        foreach($list as $l) { ?>
            <li value="<?= htmlspecialchars($l['id_candidat']) ?>">
                <?= htmlspecialchars($l['nom']) ?> 
                <?= htmlspecialchars($l['prenom']) ?> 
                - Score : <?= htmlspecialchars($l['score_test']) ?>
            </li>
        <?php } ?>
    </ul>
    <input type="submit" value="TRIER">
     </form>
</body>
</html>
