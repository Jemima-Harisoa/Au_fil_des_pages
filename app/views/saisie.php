<?php include "headerA.php"; ?>
<div class="container">
    <h3>Saisie des notes pour <?= htmlspecialchars($evaluation['employe_nom'] . ' ' . $evaluation['employe_prenom']) ?></h3>
    <p>Période : <?= htmlspecialchars($evaluation['periode_nom']) ?></p>

    <form id="evaluationForm">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Critère</th>
                    <th>Poids</th>
                    <th>Note (0-10)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($criteres as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nom']) ?></td>
                    <td><?= htmlspecialchars($c['poids']) ?></td>
                    <td>
                        <input type="number" name="note[<?= $c['id_critere'] ?>]" 
                               min="0" max="10" step="0.1" 
                               value="<?= htmlspecialchars($c['note'] ?? '') ?>" 
                               class="form-control" required>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="/evaluation/<?= $evaluation['id_evaluation'] ?>/score" class="btn btn-success">Terminer l'évaluation</a>
    </form>
</div>

<script>
document.getElementById('evaluationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Valider les notes
    const inputs = this.querySelectorAll('input[type="number"]');
    let valid = true;
    
    inputs.forEach(input => {
        const value = parseFloat(input.value);
        if (isNaN(value) || value < 0 || value > 10) {
            valid = false;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    if (!valid) {
        alert('Veuillez saisir des notes valides entre 0 et 10');
        return;
    }

    const formData = new FormData(this);

    fetch('/evaluation/<?= $evaluation['id_evaluation'] ?>/save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Notes enregistrées avec succès !');
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } else {
            alert('Erreur : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la sauvegarde');
    });
});
</script>
<?php include "footer.php"; ?>