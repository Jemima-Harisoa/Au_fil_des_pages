<?php include "headerU.php"; ?>
<?php
    $idUtilisateur = $_SESSION['utilisateur']['id_utilisateur'];
?>

<style>
.hover-shadow:hover {
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    transform: translateY(-2px);
    transition: 0.3s;
    border: 4px solid #619ffc88; /* contour bleu */
}

/* Pour que le contour ne disparaisse pas au départ */
.card {
    border: 2px solid transparent;
    transition: border 0.3s, box-shadow 0.3s, transform 0.3s;
}
</style>

<div class="container my-4">
    <?php foreach ($Annonces as $annonce): ?>
    <?php
        $jsonPath = $_SERVER['DOCUMENT_ROOT'] . $annonce['lien'];
        $contenuExtrait = "Aucune description disponible.";
        $nomEntreprise = "";
        if (file_exists($jsonPath)) {
            $details = json_decode(file_get_contents($jsonPath), true);
            if (!empty($details['contenu'])) {
                $contenuExtrait = mb_strimwidth($details['contenu'], 0, 200, "...");
            }
            if (!empty($details['nom_entreprise'])) {
                $nomEntreprise = $details['nom_entreprise'];
            }
        }
    ?>
    <div class="card shadow-sm mb-3 rounded-3 hover-shadow">
        <div class="card-body d-flex align-items-start">
            <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" 
                 class="rounded-circle mr-3" alt="Anonyme" 
                 style="width:50px; height:50px; object-fit:cover;">

            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div>
                        <h5 class="mb-1" style="font-weight:600; font-size:1rem;"><?= htmlspecialchars($annonce['titre']) ?></h5>
                        <?php if ($nomEntreprise): ?>
                            <small class="text-muted">Entreprise: <?= htmlspecialchars($nomEntreprise) ?></small>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted"><?= htmlspecialchars($annonce['date_publication']) ?></small>
                </div>
                <p class="card-text mb-1" style="max-height:80px; overflow:hidden; white-space:pre-line; font-size:0.9rem;">
                    <?= nl2br(htmlspecialchars($contenuExtrait)) ?>
                </p>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">
                        Expiration : <?= $annonce['date_expiration'] ?? 'Non précisée' ?>
                    </small>
                    <button class="btn btn-sm btn-outline-primary voirPlusBtn" data-json="<?= htmlspecialchars($annonce['lien'], ENT_QUOTES) ?>">
                        Voir plus
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal Bootstrap pour l'annonce complète -->
<div class="modal fade" id="annonceModal" tabindex="-1" role="dialog" aria-labelledby="annonceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="annonceModalLabel">Annonce complète</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="annonceModalContent" style="white-space:pre-line; font-size:0.95rem; line-height:1.5;">
            </div>
            <div class="modal-footer">
                <!--  -->
                <!-- /@idUser/Annonce/@idAnnonce/fillCV -->
            
                    <a href="#" id="postulerButton" class="btn btn-primary">Postuler</a>
                
                <!--  -->
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
// Quand on clique sur "Voir plus"
document.querySelectorAll('.voirPlusBtn').forEach(button => {
    button.addEventListener('click', function () {
        const jsonPath = button.getAttribute('data-json');
        fetch(jsonPath)
            .then(res => res.json())
            .then(data => {
                $('#annonceModal').modal('show');
                let entreprise = data.nom_entreprise ? "Entreprise: " + data.nom_entreprise + "\n\n" : "";
                document.getElementById('annonceModalLabel').textContent = data.titre;
                document.getElementById('annonceModalContent').textContent = entreprise + data.contenu;
                document.getElementById('postulerButton').setAttribute('href', '/<?= $idUtilisateur?>/Annonce/<?= $annonce['id_annonce']?>/<?= $annonce['id_profil']?>/fillCV'); // mettre lien réel si besoin
            })
            .catch(err => {
                console.error(err);
                alert("Impossible de charger l'annonce complète.");
            });
    });
});
</script>

<style>
.hover-shadow:hover {
    box-shadow: 0 6px 15px blue;
    transform: translateY(-2px);
    transition: 0.3s;
}
</style>

<?php include "footerU.php"; ?>
