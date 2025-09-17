<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCM</title>
</head>
<body>
    <h1>Test QCM</h1>

    <form action="/traitement-qcm" method="POST">

        <?php if (!empty($qcm)) {
    

         foreach ($qcm as $index => $q): ?>
            <div style="margin-bottom: 20px;">
                <p><strong>Question <?= $index + 1 ?>:</strong> <?= htmlspecialchars($q['question']) ?></p>

                <?php foreach ($q['reponses'] as $r): ?>
                    <label>
                        <input type="checkbox" name="reponses[<?= $q['id_question'] ?>][]" value="<?= htmlspecialchars($r['reponse']) ?>">
                        <?= htmlspecialchars($r['reponse']) ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
        <?php endforeach; 
        if (!empty($message)) {
    echo "<p>$message</p>";
    }
}
?>
        <button type="submit">ENVOYER LE TEST</button>
    </form>
</body>
</html>
