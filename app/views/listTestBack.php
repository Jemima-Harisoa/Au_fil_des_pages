<?php include "header.php" ?>
   
 <style>
    label{
    font-size: x-large;
   
     justify-content: space-between;
    
       
    }
    select{
        font-size: 20px;
        color: white;
        border-color: blue;
        background-color:  #4e73df;;
        border-radius: 10px ;
    }
    input{
         margin: 20px 5px 0 5px;
        font-size: 20px;
        color: grey;
        border-color: gray;
        background-color:  #ffffff;;
        border-radius: 5px ;

     }
     .tri{
        position: relative;
        margin-left: 1200px;
     }
     .triLabel{
        position: relative;
        margin-left:210px;
     }
     .choixButt{
        position: relative;
        margin-left:20px
     }
     .list {
    list-style: none;      
    padding: 0;
    margin: 20px 0;
    width: 70%;
    max-width: 800px;
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

.list .listTest:nth-child(even) {
    background: #eef1f9;     
}

.list .listTest:hover {
    background: #dce3f5;   
    cursor: pointer;
}

 </style>
    <h1>Liste des Tests</h1>
<style>
.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none; /* caché par défaut */
  position: absolute;
  background-color: #f1f1f1;
  min-width: 160px;
  box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
  border-radius: 5px;
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 10px;
  text-decoration: none;
  display: block;
}

.option{
     margin: 20px 5px 0 5px;
        font-size: 20px;
        color: grey;
        border-color: gray;
        background-color:  #ffffff;;
        border-radius: 5px ;
}
.dropdown-content a:hover {
  background-color: #ddd;
}
</style>

<div class="dropdown">
  <button id="btnMenu" class="option">OPTIONS ▼</button>
  <div class="dropdown-content" id="menu">
    <a href="/createTest">Creer un test</a>
    <a href="/listTest">Voir la liste de tous les tests</a>
  </div>
</div>

<script>
const btn = document.getElementById("btnMenu");
const menu = document.getElementById("menu");

btn.addEventListener("click", () => {
  menu.style.display = (menu.style.display === "block") ? "none" : "block";
});

document.addEventListener("click", (e) => {
  if (!btn.contains(e.target) && !menu.contains(e.target)) {
    menu.style.display = "none";
  }
});
</script>

<form action="/triageTests" method="get">
    <form action="/triMetier" method="get">
        <label for="metier"class="metier" >Choisir un métier pour commencer le triage :</label>
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
        <input type="submit" class="choixButt" value="CHOISIR">
    
    
        <label for="critere" class="triLabel">Trier par :</label>
        <select name="critere" id="critere">
            <option value="score">Score</option>
            <option value="nom">Nom</option>
            <option value="prenom">Prénom</option>
        </select>

        <select name="type" id="type">
            <option value="dec">Décroissant</option>
            <option value="croi">Croissant</option>
        </select>

        <ul class="list">
            <?php if (!empty($list)) {
                foreach ($list as $l) { ?>
                    <li  class="listTest" data-id="<?= htmlspecialchars($l['id_candidat']) ?>">
                        <?= htmlspecialchars($l['nom']) ?> 
                        <?= htmlspecialchars($l['prenom']) ?> 
                        - Score : <?= htmlspecialchars($l['score_test']) ?>
                    </li>
                <?php }
            } else { ?>
                <li>Aucun test trouvé.</li>
            <?php } ?>
        </ul>

        <input type="submit" class="tri"  value="TRIER">
    </form>
</form>

    
<?php include "footer.php" ?>