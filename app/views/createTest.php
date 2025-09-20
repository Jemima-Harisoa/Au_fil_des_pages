<?php include "header.php" ?>
<style>
h2 {
  text-align: center;
  margin: 20px 0;
  font-size: 28px;
  color: #333;
}

.container {
  width: 80%;
  max-width: 800px;
  margin: 0 auto;
  background: #ffffff;
  padding: 20px 30px;
  border-radius: 10px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

.container label {
  display: block;
  margin-bottom: 8px;
  font-size: 20px;
  font-weight: 500;
}

.container input[type="text"],
.container input[type="number"] {
  width: 100%;
  padding: 10px 15px;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-size: 18px;
  background: #f8f9fc;
}

.actions {
  display: flex;
  gap: 15px;
  justify-content: center;
  margin: 20px 0;
}

.actions button {
  font-size: 18px;
  color: grey;
  border: 1px solid gray;
  background: #ffffff;
  border-radius: 5px;
  padding: 10px 18px;
  cursor: pointer;
  transition: background 0.2s;
}

.actions button:hover {
  background: #f1f1f1;
}

.reponse {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 10px 0;
}

.reponse input[type="text"] {
  flex: 1;
}

.reponse label {
  font-size: 16px;
  margin: 0;
  background: none;
  padding: 0;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
}

.modal {
  display: none; 
  position: fixed;
  z-index: 1000;
  inset: 0;
  background: rgba(0,0,0,0.5);
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
  max-width: 400px;
  margin: 15% auto;
  text-align: center;
}

.modal-buttons button {
  margin: 5px;
  padding: 8px 15px;
  font-size: 18px;
  color: grey;
  border: 1px solid gray;
  background-color: #ffffff;
  border-radius: 5px;
  cursor: pointer;
}

select {
  font-size: 20px;
  color: white;
  margin-bottom:20px;
  border-color: blue;
  background-color: #4e73df;
  border-radius: 10px;
  padding: 5px 10px;
}

</style>
<h2>Créer un Test</h2>

<div class="container">
  
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
  

  <div>
    <label for="question">Question :</label>
    <input type="text" id="question">
    <label for="points">Nombre de points :</label>
    <input type="number" id="points">
  </div>

  <div style="margin-top: 15px;">
    <label for="nbReponses">Nombre de réponses :</label>
    <div class="actions">
      <input type="number" id="nbReponses" min="2" max="10" value="4" style="width:100px;">
      <button type="button" id="btnGen">Générer</button>
    </div>
  </div>

  <div id="zoneReponses" style="margin-top: 15px;"></div>

  <div class="actions">
    <button type="button" id="btnSave">Enregistrer le test</button>
  </div>
</div>

<script>
const btnGen = document.getElementById("btnGen");
const zoneReponses = document.getElementById("zoneReponses");
const btnSave = document.getElementById("btnSave");

btnGen.onclick = () => {
  zoneReponses.innerHTML = "";
  const nb = parseInt(document.getElementById("nbReponses").value);

  for (let i = 1; i <= nb; i++) {
    const div = document.createElement("div");
    div.className = "reponse";
    div.innerHTML = `
      <input type="text" id="rep${i}" placeholder="Réponse ${i}">
      <label><input type="checkbox" id="chk${i}"> Correcte ?</label>
    `;
    zoneReponses.appendChild(div);
  }
};

btnSave.onclick = () => {
  const question = document.getElementById("question").value.trim();
  const point = parseInt(document.getElementById("points").value);
  const job = document.getElementById("metier").value;

  if (!question) { alert("Veuillez entrer une question !"); return; }
  if (!job) { alert("Veuillez choisir un métier !"); return; }

  const reps = [];
  const checks = [];
  const inputs = zoneReponses.querySelectorAll(".reponse");
  inputs.forEach((div) => {
    const val = div.querySelector("input[type=text]").value.trim();
    const chk = div.querySelector("input[type=checkbox]").checked;
    if (val) {
      reps.push(val);
      checks.push(chk ? 1 : 0);
    }
  });

  if (reps.length < 2) { alert("Veuillez entrer au moins deux réponses !"); return; }

  const payload = { question, point, job, reps, checks };
  console.log("Payload préparé :", payload);

  fetch("/creationQst", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  })
  .then(r => r.json())
  .then(res => {
    console.log("Réponse serveur :", res);
    alert(res.success ? "Test enregistré !" : "Erreur lors de l'enregistrement");
  });
};
</script>



<?php include "footer.php" ?>
