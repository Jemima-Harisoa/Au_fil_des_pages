<?php include "header.php" ?>

<style>
    form {
        max-width: 1500px;
        margin: 10px auto;
        padding: 150px;
        background-color: #5c73b8ff;
        border-radius: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
        color: white;
    }

    .question {
        display: none; /* cachées par défaut */
        margin-bottom: 30px;
        padding: 15px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        color: #333;
    }

    .question.active {
        display: block; /* affiche la question courante */
    }

    .question p {
        font-weight: bold;
        margin-bottom: 50px;
        color: #4e73df;
        font-size: x-large;
    }

    .reponse-card {
        background-color: #4e73df;
        color: #fff;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        display: block;
        
    }

    .reponse-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .reponse-card input[type="checkbox"] {
        margin-right: 10px;
    }

    .btn-nav {
        display: inline-block;
        margin: 20px 5px 0 5px;
        padding: 12px 25px;
        background-color: #02f8e4ff;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .nav-buttons{
         display: flex;
    justify-content: space-between;
    margin-top: 20px;
    }

    .btn-nav:hover {
        background-color: #10a195ff;
    }

    .message {
        text-align: center;
        font-weight: bold;
        color: red;
        margin-top: 20px;
    }
</style>

<form id="qcmForm" action="/traitement-qcm" method="POST">

<?php if (!empty($qcm)) {
    foreach ($qcm as $index => $q): ?>
        <div class="question <?= $index === 0 ? 'active' : '' ?>">
            <p>Question <?= $index + 1 ?>: <?= htmlspecialchars($q['question']) ?></p>

            <?php foreach ($q['reponses'] as $r): ?>
                <label class="reponse-card">
                    <input type="checkbox" name="reponses[<?= $q['id_question'] ?>][]" value="<?= htmlspecialchars($r['reponse']) ?>">
                    <?= htmlspecialchars($r['reponse']) ?>
                </label>
            <?php endforeach; ?>

            <div class="nav-buttons">
                <?php if ($index > 0): ?>
                    <button type="button" class="btn-nav prev-btn">Précédent</button>
                <?php endif; ?>
                <?php if ($index < count($qcm) - 1): ?>
                    <button type="button" class="btn-nav next-btn">Suivant</button>
                <?php else: ?>
                    <button type="submit" class="btn-nav">ENVOYER LE TEST</button>
                <?php endif; ?>
            </div>
        </div>
<?php endforeach; ?>

</form>
<?php } ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const questions = document.querySelectorAll(".question");
        let currentIndex = 0;

        function showQuestion(index) {
            questions[currentIndex].classList.remove("active");
            currentIndex = index;
            questions[currentIndex].classList.add("active");
        }
        document.querySelectorAll(".next-btn").forEach((btn, index) => {
            btn.addEventListener("click", () => {
                showQuestion(index + 1);
            });
        });
        document.querySelectorAll(".prev-btn").forEach((btn, index) => {
            btn.addEventListener("click", () => {
                showQuestion(index);
            });
        });

    
        const form = document.getElementById("qcmForm");
        form.addEventListener("submit", function (e) {
            alert(" Test fini ! Merci d'avoir participé.");
        });
    });
</script>


<?php include "footer.php" ?>
