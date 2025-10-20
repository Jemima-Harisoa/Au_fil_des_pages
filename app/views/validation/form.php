<?php
// helper pour transformer d/m/Y => Y-m-d (si nécessaire)
function toInputDate($d) {
    if (empty($d)) return '';
    // essayer d/m/Y puis Y-m-d
    $dt = DateTime::createFromFormat('d/m/Y', $d);
    if ($dt) return $dt->format('Y-m-d');
    $dt = DateTime::createFromFormat('Y-m-d', $d);
    if ($dt) return $dt->format('Y-m-d');
    return '';
}

// Préparer les données provenant du JSON modèle / fallback
$employe = $data['employe'] ?? [];
$modalite = $employe['modalite'] ?? [];
$posteModele = $employe['poste'] ?? [];
$remu = $employe['remuneration'] ?? [];
$avantagesListRaw = $remu['avantages'] ?? [];
// assurer un tableau d'avantages propre
$avantagesList = [];
if (is_array($avantagesListRaw)) {
    $avantagesList = $avantagesListRaw;
} elseif (is_string($avantagesListRaw) && trim($avantagesListRaw) !== '') {
    // accepter "type:desc,type2:desc2" ou "desc1,desc2"
    if (strpos($avantagesListRaw, ':') !== false) {
        $parts = explode(',', $avantagesListRaw);
        foreach ($parts as $p) {
            $avantagesList[] = trim($p);
        }
    } else {
        $avantagesList = array_map('trim', explode(',', $avantagesListRaw));
    }
}

// Fallbacks utiles
$personne = $data['personne'] ?? [];
$candidat = $data['candidat'] ?? [];
$profil = $data['profil'] ?? [];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Édition Contrat #<?= htmlspecialchars($contrat['id_contrat'] ?? '') ?></title>
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <?php Flight::render("headerA")?>

<div class="">
    <div class="card o-hidden border-0 shadow-lg my-5">

        <div class="card-body p-0">
            <?php if (!empty($_GET['msg'])): ?>
                <?php $msg = htmlspecialchars($_GET['msg']); $type = htmlspecialchars($_GET['msg_type'] ?? 'info'); ?>
                <div class="alert alert-<?=
                    $type === 'success' ? 'success' : ($type === 'error' ? 'danger' : 'info')
                ?> alert-dismissible fade show" role="alert" style="margin:10px;">
                    <?= $msg ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <div class="row">
                <!-- Colonne Aperçu PDF -->
                <div class="col-lg-6 p-3">
                    <h4 class="text-center mb-3">Aperçu du Contrat (PDF)</h4>

                    <div id="contractPreviewInline" style="display:none; width:100%; height:100%; border:1px solid #ccc; background:#fff;">
                        <div style="text-align:right; padding:5px;">
                            <button id="closePreviewInline" class="btn btn-sm btn-danger">Fermer</button>
                        </div>
                        <iframe id="contractIframeInline" style="width:100%; height: 100%;" frameborder="0"></iframe>
                    </div>

                    <div id="noContractMessage" class="text-center">
                        <?php if (!empty($data['contrat']['url_contrat'])): ?>
                            <p>Contrat disponible : <strong><?= "../..".htmlspecialchars($data['contrat']['url_contrat']) ?></strong></p>
                            <p><small>Cet aperçu se charge automatiquement au chargement de la page.</small></p>
                        <?php else: ?>
                            <p class="text-muted">Aucun contrat JSON lié. Renseignez l'URL ci-contre. </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Colonne Formulaire (à droite) -->
                    <div class="col-lg-6">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Formulaire - Contrat de Travail</h1>
                        </div>

                        <form class="user" method="post" action="/migration/contrat/register?id_candidat=<?= htmlspecialchars($data['candidat']['id_candidat'] ?? '') ?>">
                            <!-- Type de contrat -->
                            <h5 class="mb-3">Type de contrat</h5>
                            <div class="form-group mb-3">
                                <select class="form-control" id="typeContrat" name="typeContrat">
                                    <option value="">Sélectionner le type</option>
                                    <?php if (!empty($data['type_contrats'])): ?>
                                        <?php foreach ($data['type_contrats'] as $tc): 
                                            $selected = '';
                                            // detecte si le modele indique un id de type (resilliation) ou un nom
                                            if (isset($employe['resilliation'])) {
                                                if ((string)$employe['resilliation'] === (string)$tc['id_type_contrat'] 
                                                    || strtolower($employe['resilliation']) === strtolower($tc['nom'])) {
                                                    $selected = 'selected';
                                                }
                                            } else {
                                                // fallback: préselectionner CDI si disponible
                                                if ($tc['nom'] === 'CDI') $selected = 'selected';
                                            }
                                        ?>
                                            <option value="<?= htmlspecialchars($tc['id_type_contrat']) ?>" <?= $selected ?>>
                                                <?= htmlspecialchars($tc['nom']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Informations Travailleur -->
                            <h5 class="mb-3">Informations sur le travailleur</h5>
                            <div class="form-group">
                                <label for="">Noms et prénoms</label>
                                <input type="text" class="form-control form-control-user" 
                                    name="noms_prenoms"
                                    placeholder="Noms et prénoms"
                                    value="<?= htmlspecialchars($employe['noms_prenoms'] ?? (($personne['nom'] ?? '') . ' ' . ($personne['prenom'] ?? ''))) ?>">
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="">Date de naissance</label>
                                    <input type="date" class="form-control form-control-user" 
                                        name="dateNaissance"
                                        placeholder="Date de naissance"
                                        value="<?= htmlspecialchars(toInputDate($employe['ne_le'] ?? ($personne['date_naissance'] ?? ''))) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="">Lieu de naissance</label>
                                    <input type="text" class="form-control form-control-user" 
                                        name="lieuNaissance"
                                        placeholder="Lieu de naissance"
                                        value="<?= htmlspecialchars($employe['ne_a'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="">Parents</label>
                                <input type="text" class="form-control form-control-user" 
                                    name="parents"
                                    placeholder="Fils ou fille de"
                                    value="<?= htmlspecialchars($employe['fils_ou_fille_de'] ?? '') ?>">
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="">Nationalité</label>
                                    <input type="text" class="form-control form-control-user" 
                                        name="nationalite"
                                        placeholder="Nationalité"
                                        value="<?= htmlspecialchars($employe['nationalite'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="">Domicile</label>
                                    <input type="text" class="form-control form-control-user" 
                                        name="domicile"
                                        placeholder="Domicile à Madagascar"
                                        value="<?= htmlspecialchars($employe['domicile'] ?? '') ?>">
                                </div>
                            </div>

                            <!-- Généralités -->
                            <h5 class="mb-3">Dispositions générales</h5>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="">Date de prise d'effet</label>
                                    <input type="date" class="form-control form-control-user" 
                                        name="dateDebut"
                                        placeholder="Date de prise d'effet"
                                        value="<?= htmlspecialchars(toInputDate($modalite['debut_contrat'] ?? '')) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="">Durée période d'essai (mois)</label>
                                    <input type="number" class="form-control form-control-user" 
                                        name="essai"
                                        placeholder="Durée période d'essai (mois)"
                                        value="<?= htmlspecialchars($modalite['essai'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="">Lieu d'emploi</label>
                                <input type="text" class="form-control form-control-user" 
                                    name="lieuEmploi"
                                    placeholder="Lieu d'emploi"
                                    value="<?= htmlspecialchars($employe['lieu'] ?? 'Rue des Lilas, Antananarivo') ?>">
                            </div>

                            <div class="form-group">
                                <label for="">Poste occupé / Fonctions</label>
                                <input type="text" class="form-control form-control-user" 
                                    name="poste"
                                    placeholder="Poste occupé / Fonctions"
                                    value="<?= htmlspecialchars($posteModele['qualite'] ?? ($candidat['poste'] ?? '')) ?>">
                            </div>

                            <div class="form-group">
                                <label for="">Classification (si applicable)</label>
                                <input type="text" class="form-control form-control-user" 
                                    name="classification"
                                    placeholder="Classification"
                                    value="<?= htmlspecialchars($posteModele['class'] ?? '') ?>">
                            </div>

                            <!-- Rémunération -->
                            <h5 class="mb-3">Rémunération</h5>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="">Salaire mensuel (Ar)</label>
                                    <input type="number" class="form-control form-control-user" 
                                        name="salaire"
                                        id="salaire"
                                        placeholder="Salaire mensuel (Ar)" 
                                        value="<?= htmlspecialchars($remu['salaire'] ?? ($data['profil']['salaire'] ?? '')) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="">Durée CDD (si applicable)</label>
                                    <input type="text" class="form-control form-control-user"
                                        name="dureeCDD"
                                        placeholder="Durée CDD (ex: 12 mois)"
                                        value="<?= htmlspecialchars($modalite['duree'] ?? '') ?>">
                                </div>
                            </div>

                            <!-- Avantages -->
                            <h6 class="mb-2">Avantages</h6>
                            <div class="form-group row align-items-center">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <select class="form-control" id="typeAvantage">
                                        <option value="">Type</option>
                                        <option value="nature">Nature</option>
                                        <option value="espece">Espèce</option>
                                        <option value="social">Social</option>
                                        <option value="exceptionnel">Exceptionnel</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-user" id="descAvantage" placeholder="Description de l'avantage">
                                </div>
                                <div class="col-sm-2 text-center d-none d-md-inline">
                                    <button type="button" class="rounded-circle border-0 btn btn-primary" id="btnAddAvantage" onclick="ajouterAvantage()">+</button>
                                </div>
                            </div>

                            <ul id="listeAvantages" class="list-group mb-3">
                                <?php if (!empty($avantagesList)): ?>
                                    <?php foreach ($avantagesList as $av): 
                                        // parser avantage: si "type:desc" sinon tout en description
                                        $type = 'autre';
                                        $desc = (string)$av;
                                        if (is_string($av) && strpos($av, ':') !== false) {
                                            list($type, $desc) = explode(':', $av, 2);
                                        }
                                    ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><strong><?= htmlspecialchars(strtoupper($type)) ?>:</strong> <?= htmlspecialchars($desc) ?></span>
                                            <a href="#" class="btn btn-danger btn-circle" onclick="this.closest('li').remove(); return false;">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <input type="hidden" name="avantages[]" value="<?= htmlspecialchars(trim($type) . ':' . trim($desc)) ?>">
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
  <!-- Sélection d’un employé validateur -->
                        <h5 class="mb-3">Validation du contrat</h5>
                            <div class="form-group">
                                <label for="employeValidateur">Employé validateur</label>
                                <input type="text" list="listeEmployes" class="form-control form-control-user" 
                                    name="employeValidateur"
                                    placeholder="Rechercher un employé (nom ou prénom)">
                                <datalist id="listeEmployes">
                                    <?php if (!empty($data['liste_employes'])): ?>
                                        <?php foreach ($data['liste_employes'] as $emp): ?>
                                            <option value="<?= htmlspecialchars($emp['nom'] . ' ' . $emp['prenom']) ?>" 
                                                    data-id="<?= htmlspecialchars($emp['id_employe']) ?>">
                                                <?= htmlspecialchars($emp['nom'] . ' ' . $emp['prenom'] . ' — ' . $emp['departement_nom']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </datalist>
                            </div>

                            <!-- Sélection du département -->
                            <div class="form-group">
                                <label for="departement">Département</label>
                                <input type="text" list="listeDepartements" class="form-control form-control-user"
                                    name="departement"
                                    placeholder="Choisir ou rechercher un département">
                                <datalist id="listeDepartements">
                                    <?php if (!empty($data['liste_departements'])): ?>
                                        <?php foreach ($data['liste_departements'] as $dep): ?>
                                            <option value="<?= htmlspecialchars($dep['nom']) ?>" 
                                                    data-id="<?= htmlspecialchars($dep['id_departement']) ?>">
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </datalist>
                            </div>

                            <!-- Champ lieu d'édition et signature (préremplis depuis le modele si existant) -->
                            <h5 class="mb-3">Signature</h5>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="">Employeur</label>
                                    <input type="text" class="form-control form-control-user" name="signatureTravailleur" placeholder="Nom pour signature" value="<?= htmlspecialchars($employe['signature'] ?? (($personne['nom'] ?? '') . ' ' . ($personne['prenom'] ?? ''))) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="">Travailleur</label>
                                    <input type="text" class="form-control form-control-user" name="signature" placeholder="Nom pour signature" value="">
                                </div>
                            </div>
                            <div class="form-group">
                               <label for="">Lieu d'édition</label>
                                <input type="text" class="form-control form-control-user" name="lieuEdition" placeholder="Lieu d'édition" value="<?= htmlspecialchars($employe['lieu_edition'] ?? 'Antananarivo') ?>">
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" name="action" value="valider" class="btn btn-success btn-user">
                                    Valider
                                </button>
                                <button type="submit" name="action" value="refuser" class="btn btn-danger btn-user">
                                    Ne pas valider
                                </button>
                                <button type="submit" name="action" value="attente" class="btn btn-warning btn-user">
                                    Mettre en attente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div> <!-- row -->
        </div>
    </div>
</div>
    <?php Flight::render("footer")?>


<!-- Scripts nécessaires -->
<script src="../../js/skecth/pdf.js"></script>
<script src="../../vendor/jquery/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
(function(){
    // URL fournie côté serveur
    const initialUrl = "../.." + <?= json_encode($data['contrat']['url_contrat'] ?? '') ?>;

    const $preview = $('#contractPreviewInline');
    const $iframe = $('#contractIframeInline');
    const $close = $('#closePreviewInline');
    const $noMsg = $('#noContractMessage');
    const $urlInput = $('#url_contrat');
    const $previewFromInput = $('#previewFromInput');

    function normalizeUrl(url) {
        if (!url) return '';
        // Si tu as des URLs stockées relatives (ex: "/json/...") et que fetch depuis cette page
        // nécessite un préfixe, ajuste ici. Par ex: return '..' + url;
        return url;
    }

    function loadAndShowContract(url) {
        const fullUrl = normalizeUrl(url);
        if (!fullUrl) return;

        $.getJSON(fullUrl)
            .done(function(data) {
                try {
                    // buildContractDoc doit exister (même fonction que sur la page list)
                    const docDef = buildContractDoc(
                        data.clause_general || {},
                        data.employeur || {},
                        data.employe || {}
                    );

                    pdfMake.createPdf(docDef).getBlob(function(blob) {
                        const pdfUrl = URL.createObjectURL(blob);
                        $iframe.attr('src', pdfUrl);
                        $preview.show();
                        $noMsg.hide();
                    });
                } catch (err) {
                    console.error('Erreur génération PDF:', err);
                    alert('Erreur lors de la génération du PDF depuis le JSON.');
                }
            })
            .fail(function(err) {
                console.error('Erreur chargement JSON:', err);
                alert('Impossible de charger le fichier JSON du contrat. Vérifie l\'URL.');
            });
    }

    // Charger automatiquement si initialUrl présent
    if (initialUrl && initialUrl.trim() !== '') {
        loadAndShowContract(initialUrl);
    }

    // Bouton "Voir l'URL" manuel
    $previewFromInput.on('click', function() {
        const url = $urlInput.val().trim();
        loadAndShowContract(url);
    });

    // Fermer preview
    $close.on('click', function() {
        $preview.hide();
        $iframe.attr('src', '');
        $noMsg.show();
    });

    // NOTE: buildContractDoc(...) doit être défini globalement (comme dans ta page list)
})();
</script>
</body>
</html>
