<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- CDN Bootstrap -->
<link rel="stylesheet" href="/vendor/fontawesome-free/css/bootstrap.min.css">

<!-- CDN Font Awesome -->
<link href="/vendor/fontawesome-free/css/all2.min.css" rel="stylesheet" type="text/css">
</head>
<style>
    body{
        margin-left: 10%;
        width: 80%;
    }
    .main-container{
        display: flex;
        flex-direction: row;
        justify-content: space-between; /* espace entre les enfants */
        gap: 24px; /* espace égal entre éléments (moderne) */
        align-items: flex-start;
        max-width: 100%;
        margin: 24px auto;
        padding: 12px;
        box-sizing: border-box;
    }

    /* faire en sorte que chaque enfant prenne un espace égal */
    .main-container > div {
        flex: 1 1 0; /* grow, shrink, basis */
        min-width: 0; /* pour éviter overflow sur texte long */
    }

    @media (max-width:700px){
        .main-container{
            flex-direction: column;
        }
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .impot{
        margin-top: 15px;
        display: flex;
        flex-direction: column;
        width: 300px;
        height: fit-content;
        border: 2px solid black;

    }
    .h1{
        width: 60%;
        text-align: center;
        margin-left: 20%;
        
    }
        .btn-pdf {
            background: #e98f53ff;
            color: white;
        }
    .td-style-commun-libelle{
        border-right: 0px !important; text-align: left;
    }
    .td-style-commun-nombre{
        text-align: right;
    }
</style>
<body>
    <h1 class="h1">Fiche de paie Novembre 2025</h1>
    <h1><?= $data["type_majoration"] ?></h1>
    <?=
    "exces: ".$data["heure_de_nuit_avec_ou_sans_exces"]["exces"]->format("H:i:s")." | ".
    "nombre_heure_exces: ".$data["heure_de_nuit_avec_ou_sans_exces"]["nombre_heure_exces"]."</br>".
    "nuit: ".$data["heure_de_nuit_avec_ou_sans_exces"]["heure_de_nuit"]->format("H:i:s")." | ".
    "nombre_heure_exces: ".$data["heure_de_nuit_avec_ou_sans_exces"]["nombre_heure_de_nuit"]."</br>"
    ."taux_secondes: ".$data["taux_en_secondes"]
    ;
    ?>
    <div class="main-container">
        <div>
            <p><strong>Nom et prenoms:</strong> <?= $data["employe"]["nom"] . " ". $data["employe"]["prenom"]?></p>
            <p><strong>matricule:</strong><?= $data["employe"]["id_employe"] ?></p>
            <p><strong>date embauche:</strong><?= $data["employe"]["date_embauche"]?></p>
            <p><strong>Annciennete : </strong><?= $data["anciennete"]?></p>
        </div>
        <div></div>
        <div>
            <p>Classification: H</p>
            <p><strong>Salaire base:</strong><?=$data["employe"]["salaire_base"]?></p>
            <p><strong>Taux journalier:</strong> <?=$data["taux_journalier"]?></p>
            <p><strong>Taux horaire:</strong> <?=$data["taux_horaire"]?></p>
        </div>
    </div>
    
    <table>
        <tr>
            <th>Designations</th>
            <th>Nombre</th>
            <th>Taux</th>
            <th>Montant</th>
        </tr>
        
        <?php  foreach($data["details_prime"] as $prime): ?>
        <tr>
            <td><?= $prime["designation"] ?></td>
            <td>1</td>
            <td class="td-style-commun-nombre"><?= $prime["montant"] ?></td>
            <td class="td-style-commun-nombre"><?= $prime["montant"] ?></td>
        </tr>
        <?php endforeach; ?>
        
        <tr>
            <td colspan="3" class="td-style-commun-libelle">
                <strong>Salaire brute  </strong>
            </td>
            <td class="td-style-commun-nombre">
            <p> <?=$data["salaire_brute"]?></p>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="td-style-commun-libelle">
                <strong>Retenu Cnaps</strong>
            </td>
            
            <td class="td-style-commun-nombre"><?=$data["cnaps"]["taux"];?></td>
            <td class="td-style-commun-nombre">
                <p> <?=$data["cnaps"]["montant"];?></p>
            </td>                
        </tr>
        <tr>
            <td colspan="2" class="td-style-commun-libelle">
                <strong>Retenu OSTIE </strong>
            </td>   
            <td class="td-style-commun-nombre"><?=$data["ostie"]["taux"];?></td>
            <td class="td-style-commun-nombre">
                <p> <?=$data["ostie"]["montant"];?></p>
            </td>                
        </tr>
        
        <?php  foreach($data["details_irsa"] as $irsa): ?>
        <tr>
            <td colspan="2" class="td-style-commun-libelle"><?= $irsa["designation"] ?></td>
            <td class="td-style-commun-nombre"><?= $irsa["pourcentage"] ?></td>
            <td class="td-style-commun-nombre"><?= $irsa["montant"] ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" class="td-style-commun-libelle"><strong>Total des IRSA</strong></td>
            <td class="td-style-commun-nombre"> <?= $data["somme_irsa"]; ?></td>
        </tr>
        <tr>
            <td colspan="3" class="td-style-commun-libelle"><strong>Total des retenus</strong></td>
            <td class="td-style-commun-nombre"> <?= $data["somme_retenus"]; ?></td>
        </tr>
   
        <tr>
            <td colspan="3" class="td-style-commun-libelle"><strong>Net à payer</strong></td>
            <td class="td-style-commun-nombre"><?= $data["net_a_payer"]; ?></td>
        </tr>
    </table>
    <div class="impot">
        <p>Avantages en nature: </p>
        <p>Deductions IRSA:</p>
        <p>Montant imposable: <?= $data["montant_imposable"] ?></p>
    </div>

    <div>
        <p>Mode de paiement: espèce</p>
    </div>    
    <button class="btn btn-pdf">
        <i class="fa-solid fa-file-pdf"></i> Export PDF
    </button>
</body>
</html>