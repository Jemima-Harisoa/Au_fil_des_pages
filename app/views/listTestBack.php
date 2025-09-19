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
    <form action="/triMetier" method="get">
        <label for="metier">Choisir un métier pour commencer le triage :</label>
        <select name="metier" id="metier">
            <?php if (!empty($jobs)) {
                foreach ($jobs as $job) { ?>
                    <option value="<?= htmlspecialchars($job['titre']) ?>">
                        <?= htmlspecialchars($job['titre']) ?>
                    </option>
                <?php }
            } else { ?>
                <option value="">Aucun métier trouvé</option>
            <?php } ?>
        </select>
        <input type="submit" value="CHOISIR">
    
    
        <label for="critere">Trier par :</label>
        <select name="critere" id="critere">
            <option value="score">Score</option>
            <option value="nom">Nom</option>
            <option value="prenom">Prénom</option>
        </select>

        <select name="type" id="type">
            <option value="dec">Décroissant</option>
            <option value="croi">Croissant</option>
        </select>

        <ul>
            <?php if (!empty($list)) {
                foreach ($list as $l) { ?>
                    <li data-id="<?= htmlspecialchars($l['id_candidat']) ?>">
                        <?= htmlspecialchars($l['nom']) ?> 
                        <?= htmlspecialchars($l['prenom']) ?> 
                        - Score : <?= htmlspecialchars($l['score_test']) ?>
                    </li>
                <?php }
            } else { ?>
                <li>Aucun test trouvé.</li>
            <?php } ?>
        </ul>

        <input type="submit" value="TRIER">
    </form>
</form>

</body>
</html>
