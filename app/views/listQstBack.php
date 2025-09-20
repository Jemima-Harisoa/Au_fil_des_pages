<?php include "header.php" ?>
<style>
label {
  display: block; 
  padding: 10px;
  margin: 5px 0;
  background: #f1f1f1;
  border-radius: 5px;
  font-size: x-large;
  cursor: pointer;
}


.actions {
  display: flex;
  gap: 15px;
  align-items: center;
  margin: 20px auto;
  justify-content: center; 
}

.actions button,
.actions .lienList {
  font-size: 20px;
  color: grey;
  border: 1px solid gray;
  background-color: #ffffff;
  border-radius: 5px;
  padding: 8px 15px;
  text-decoration: none;
}

select {
  font-size: 20px;
  color: white;
  border-color: blue;
  background-color: #4e73df;
  border-radius: 10px;
  padding: 5px 10px;
}

input[type="submit"] {
  font-size: 20px;
  color: grey;
  border: 1px solid gray;
  background-color: #ffffff;
  border-radius: 5px;
  padding: 8px 15px;
  cursor: pointer;
  margin-left: 10px;
}


.list {
  list-style: none;      
  padding: 0;
  margin: 20px auto;
  width: 70%;
  max-width: 800px;
  cursor: pointer;
}

.list .listTest {
  background: #f8f9fc;     
  margin: 10px 0;
  padding: 10px 15px;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-size: 18px;
  display: flex;           
  justify-content: space-between; 
  align-items: center;
  transition: background 0.2s;
}

label.selected,
.listTest.selected {
  background: #4e73df;
  color: white;
}

.list .listTest:hover {
  background: #dce3f5;   
  cursor: pointer;
}

input{
  background: #f8f9fc;     
  margin: 10px 0;
  padding: 10px 15px;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-size: 18px;
}
.modal {
  display: none; 
  position: fixed;
  z-index: 1000;
  left: 0; top: 0;
  width: 100%; height: 100%;
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
  font-size: 20px;
  color: grey;
  border: 1px solid gray;
  background-color: #ffffff;
  border-radius: 5px;
}
#reponseVal {
  display: flex;
  align-items: center;
  gap: 8px; 
  margin-top: 15px;
  justify-content: center; 
}

#reponseVal input[type="checkbox"] {
  width: 20px;
  height: 20px;
  cursor: pointer;
}

#reponseVal label {
  background: none;       
  padding: 0;
  margin: 0;
  font-size: 18px;
  cursor: pointer;
}

</style>

<div class="actions">
  <button id="btnSupprimer">Supprimer</button>
  <a href="/listTest" class="lienList">VOIR LA LISTE</a>
  <button id="btnModifier">Modifier</button>
  
  <form action="/triMetierQst" method="get" style="display: flex; align-items: center; gap: 10px;">
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
  </form>
</div>


<form id="qcmForm" action="/traitement-qcm" method="POST">
<?php if (!empty($qcm)) {
  foreach ($qcm as $index => $q): ?>
    <div class="question <?= $index === 0 ? 'active' : '' ?>">
      <label data-id="<?= $q['id_question'] ?>">
        Question <?= $index + 1 ?>: <?= htmlspecialchars($q['question']) ?>
      </label>
      <ul class="list">
        <?php foreach ($q['reponses'] as $r): ?>
          <li class="listTest" 
              data-id="<?= $r['id_reponse'] ?>" 
              data-idqst="<?= $q['id_question'] ?>">
            <input type="checkbox" 
                   class="chkReponse"
                   value="<?= $q['id_question'] . ':' . $r['id_reponse'] ?>"
                   <?= $r['est_correct'] ? 'checked' : '' ?>>
            <?= htmlspecialchars($r['reponse']) ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
<?php endforeach; } ?>
</form>

<div id="modal" class="modal">
  <div class="modal-content">
    <h3 id="modal-title">Action</h3>
    <input type="text" id="modal-input" placeholder="Écrivez ici..." />
    <div id="reponseVal"></div>
    <div class="modal-buttons">
      <button id="btnModif">Modifier</button>
      <button id="btnAjouter">Ajouter</button>
      <button id="btnFermer">Fermer</button>
    </div>
  </div>
</div>

</style>

<script>
const btnSuppr = document.getElementById("btnSupprimer");

const btnModif = document.getElementById("btnModifier");

document.addEventListener("click", (e) => {
  if (e.target.tagName === "LABEL") {
    e.target.classList.toggle("selected");
  } else if (e.target.classList.contains("listTest")) {
    e.target.classList.toggle("selected");
  }
});

btnModif.addEventListener("click", () => {
    const selectedEls = document.querySelectorAll(".selected");

    if (selectedEls.length === 0) {
        alert("Veuillez sélectionner au moins une question ou réponse.");
        return;
    }

    const data = Array.from(selectedEls).map(el => {
        if (el.tagName === "LABEL") {
            return { type: "question", idQst: el.dataset.idQst || el.dataset.id , idRep: null,el };
        } else if (el.classList.contains("listTest")) {
            return { type: "reponse", idQst: el.dataset.idqst, idRep: el.dataset.id,el };
        }
    });

    const modal = document.getElementById("modal");
    const modalTitle = document.getElementById("modal-title");
    const modalInput = document.getElementById("modal-input");
    const btnModifier = document.getElementById("btnModif");
    const btnAjouter = document.getElementById("btnAjouter");
    const btnFermer = document.getElementById("btnFermer");
    const repVal = document.getElementById("reponseVal");

   data.forEach(item => {
        const el = item.el;
            repVal.innerHTML = "";
        if (item.type === "question") {
            modalTitle.textContent = "Modifier une question";
            modalInput.value = el.textContent.trim();
        } else if (item.type === "reponse") {

            modalTitle.textContent = "Modifier une réponse";
            modalInput.value = el.textContent.trim();
            const chk = document.createElement("input");
            chk.type = "checkbox";
            chk.id = "chkVal";
            const lbl = document.createElement("label");
            lbl.setAttribute("for", "chkVal");
            lbl.textContent = "Vrai ?";
            repVal.appendChild(chk);
            repVal.appendChild(lbl);
        }

        modal.style.display = "block";

        btnModifier.onclick = () => {
            const nouvelleValeur = modalInput.value.trim();
            let payload;

            if (item.type === "reponse") {
                const chk = document.getElementById("chkVal");
                const checkVal = chk ? chk.checked : false;

                payload = [{
                    idQst: item.idQst,
                    idRep: item.idRep,
                    value: nouvelleValeur,
                    estCorrect: checkVal
                }];
            } else {
                payload = [{
                    idQst: item.idQst,
                    idRep: item.idRep,
                    value: nouvelleValeur
                }];
            }

            console.log("Données envoyées au serveur :", payload);

            fetch("/updateQstRep", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ tabQstRep: payload })
            })
            .then(r => r.json())
            .then(res => {
                console.log("Réponse serveur :", res);
                if (res.success) {
                    el.textContent = nouvelleValeur;
                    modal.style.display = "none";
                } else {
                    alert("Erreur lors de la modification !");
                }
            })
            .catch(err => console.error("Erreur fetch :", err));
        };
   
        btnAjouter.onclick = () => {
           const nouvelleValeur = modalInput.value.trim();
            let payload;

            if (item.type === "reponse") {
                //const chk = document.getElementById("chkVal");
                const chk = document.getElementById("chkVal");
const checkVal = !!(chk && chk.checked); // force true/false


                payload = [{
                    idQst: item.idQst,
                    idRep: item.idRep,
                    value: nouvelleValeur,
                    estCorrect: checkVal
                }];
            } else {
                payload = [{
                    idQst: item.idQst,
                    idRep: item.idRep,
                    value: nouvelleValeur
                }];
            }
              console.log("Données envoyées au serveur :", payload);
              if(item.type === "question"){
    window.location.href = "/createTest"; 
} else {
            fetch("/addRep", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({tabQstRep: payload})
            })
            .then(r => r.json())
            .then(res => {
                console.log("Réponse serveur :", res);
               // if (res.success) {
                  //  el.textContent = nouvelleValeur;
                  //  modal.style.display = "none";
              
            })
           
        };
     }

        btnFermer.onclick = () => {
            modal.style.display = "none";
        };
    });

 });

btnSuppr.addEventListener("click", () => {
    const selectedEls = document.querySelectorAll(".selected");
    const data = Array.from(selectedEls).map(el => {
        if (el.tagName === "LABEL") {
            return { idQst: el.dataset.idQst || el.dataset.id, idRep: null };
        } else if (el.classList.contains("listTest")) {
            return { idQst: el.dataset.idqst, idRep: el.dataset.id };
        }
    });

    if (data.length === 0) {
        alert("Veuillez sélectionner au moins une question ou réponse à supprimer.");
        return;
    }

    fetch('/deleteAjax', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ tabQstRep: data })
    })
    .then(async response => {
       
        const text = await response.text(); 

        try {
            const result = JSON.parse(text.trim()); 

            if(result.success){
                selectedEls.forEach(el => el.remove());
            } else {
                alert("Erreur lors de la suppression !");
            }
        } catch(e){
            alert("Réponse serveur non valide. Voir console pour debug.");
        }
    })
    .catch(error => console.error("Erreur fetch :", error));
});

</script>


<?php include "footer.php" ?>
