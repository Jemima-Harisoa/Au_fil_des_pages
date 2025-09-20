<?php
    session_start();
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
    <h1>A</h1>
    <a href="/<?=$idUser?>/Annonce" class="">Annonce</a>
</body>
</html>