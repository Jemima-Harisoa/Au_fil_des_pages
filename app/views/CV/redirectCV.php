<?php
    session_start();
    $rqt = "SELECT * FROM annonces";
    $stmt = Flight::db()->prepare($rqt);
    $stmt->execute();
    $annonces = $stmt->fetchAll();
    $_SESSION['idUser'] = 1;
    $idUser = $_SESSION['idUser'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <div class="annonces">
    <?php foreach ($annonces as $annonce): ?>
        <?= $annonce['id_annonce']?>
        <div class="annonce">
            <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
            <p><strong>Lien source :</strong> 
                <a href="<?= htmlspecialchars($annonce['lien']) ?>" target="_blank">
                    <?= htmlspecialchars($annonce['lien']) ?>
                </a>
            </p>
            <p><strong>Date de publication :</strong> <?= htmlspecialchars($annonce['date_publication']) ?></p>
            <p><strong>Date d'expiration :</strong> <?= htmlspecialchars($annonce['date_expiration']) ?></p>
            <p><strong>Nombre de postes :</strong> <?= htmlspecialchars($annonce['nombre_poste']) ?></p>

            <!-- Lien pour postuler -->
            <p>
                <a href="/<?= $idUser?>/Annonce/<?= urlencode($annonce['id_annonce']) ?>/fillCV" class="btn-postuler">
                    Postuler
                </a>
            </p>
        </div>
        <hr>
    <?php endforeach; ?>
</div>





</body>
</html>