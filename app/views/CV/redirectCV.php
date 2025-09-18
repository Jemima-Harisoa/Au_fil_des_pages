<?php
    $rqt = "SELECT * FROM annonces";
    $stmt = Flight::db()->prepare($rqt);
    $stmt->execute();
    $annonces = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="/CV/fillCV">temp publish</a>

    
    <div class="annonces">
    <?php foreach ($annonces as $annonce): ?>
        <div class="annonce">
            <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
            <p><strong>Lien :</strong> <a href="<?= htmlspecialchars($annonce['lien']) ?>" target="_blank"><?= htmlspecialchars($annonce['lien']) ?></a></p>
            <p><strong>Date de publication :</strong> <?= htmlspecialchars($annonce['date_publication']) ?></p>
            <p><strong>Date d'expiration :</strong> <?= htmlspecialchars($annonce['date_expiration']) ?></p>
            <p><strong>Nombre de postes :</strong> <?= htmlspecialchars($annonce['nombre_poste']) ?></p>
        </div>
        <hr>
    <?php endforeach; ?>
</div>




</body>
</html>