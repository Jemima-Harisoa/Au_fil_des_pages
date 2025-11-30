<?php include "headerA.php"; ?>
<h1>Génération des performances</h1>

<form method="GET" action="/performance_dashboard">
    <!-- Année -->
    <label for="annee">Année :</label>
    <input type="number" name="annee" id="annee" value="<?= date('Y') ?>" required><br><br>

    <!-- Employé -->
    <label for="employe">Employé :</label>
    <select name="employe" id="employe">
        <option value="">Tous</option>
        <?php
// ID de l'admin connecté
$idAdmin = $_SESSION['admin']['id_admin'] ?? null;

if ($idAdmin) {
    // Récupérer l'ID manager correspondant à cet admin
    $stmt1 = Flight::db()->prepare("SELECT id_manager FROM manager_admins WHERE id_admin = ?");
    $stmt1->execute([$idAdmin]);
    $managerId = $stmt1->fetchColumn();

    if ($managerId) {
        // Récupérer les employés gérés par ce manager avec des évaluations terminées
        $stmt2 = Flight::db()->prepare("
            SELECT DISTINCT emp.id_employe, per.nom
            FROM employes emp
            JOIN personnes per ON per.id_personne = emp.id_personne
            JOIN employe_evaluations e ON e.employe_id = emp.id_employe
            WHERE e.manager_id = ? AND e.statut = 'TERMINEE'
        ");
        $stmt2->execute([$managerId]);
        $employes = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        foreach ($employes as $e) {
            echo "<option value='{$e['id_employe']}'>{$e['nom']}</option>";
        }
    } else {
        echo "<option>Aucun manager associé à cet admin</option>";
    }
} else {
    echo "<option>Admin non connecté</option>";
}
?>

    </select><br><br>

    <!-- Période A -->
    <label for="periodeA">Période A :</label>
    <select name="periodeA" id="periodeA">
        <option value="">-- Non défini --</option>
        <?php 
        $stmt = Flight::db()->query("SELECT id_periode, nom FROM employe_evaluation_periodes");
        $periodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($periodes as $p){
            echo "<option value='{$p['id_periode']}'>{$p['nom']}</option>";
        }
        ?>
    </select><br><br>

    <!-- Période B -->
    <label for="periodeB">Période B :</label>
    <select name="periodeB" id="periodeB">
        <option value="">-- Non défini --</option>
        <?php 
        foreach($periodes as $p){
            echo "<option value='{$p['id_periode']}'>{$p['nom']}</option>";
        }
        ?>
    </select><br><br>

    <button type="submit">Générer les performances</button>
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