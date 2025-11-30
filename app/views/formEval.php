<?php
include "headerA.php";

// Connexion à la base
$db = Flight::db();


?>

<h1>Planifier une évaluation</h1>

<?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

<form method="post" action="/evaluation/planifier">
    <label for="employe_id">Employé :</label>
    <select name="employe_id" id="employe_id" required>
        <option value="">-- Sélectionner --</option>
        <?php foreach ($employes as $e): ?>
            <option value="<?= $e['id_employe'] ?>">
                <?= htmlspecialchars($e['nom'] . ' ' . $e['prenom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label for="periode_id">Période :</label>
    <select name="periode_id" id="periode_id" required>
        <option value="">-- Sélectionner --</option>
        <?php foreach ($periodes as $p): ?>
            <option value="<?= $p['id_periode'] ?>"><?= htmlspecialchars($p['nom']) ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label for="date_evaluation">Date prévue :</label>
    <input type="date" name="date_evaluation" id="date_evaluation" value="<?= date('Y-m-d') ?>" required>
    <br><br>

    <input type="hidden" name="manager_id" id="manager_id" value="1" required>
    <br><br>

    <button type="submit">Planifier l'évaluation</button>
</form>
<style>
body {
    font-family: Arial, Helvetica, sans-serif;
    margin: 20px;
    background: #f4f6f8;
    color: #222;
}

h1 {
    margin-bottom: 24px;
    font-size: 1.8em;
    color: #333;
}

form {
    background: #fff;
    padding: 24px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-width: 600px;
}

label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
}

input[type="number"],
select {
    width: 100%;
    padding: 8px 10px;
    margin-bottom: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1em;
    box-sizing: border-box;
}

button {
    padding: 10px 18px;
    background-color: #2563eb;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 1em;
    cursor: pointer;
    transition: background 0.2s;
}

button:hover {
    background-color: #1e4bb8;
}

option {
    padding: 4px;
}

@media (max-width: 640px) {
    form {
        padding: 16px;
    }
}
</style>
<?php include "footer.php"; ?>
