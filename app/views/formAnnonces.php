<?php include "headerA.php" ?>

<!-- Page Heading -->
<div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Profils</h6>
</div>
<div class="card-body">
    <div class="table-responsive">
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <?php if (!empty($Profils)): ?>
                                <?php foreach (array_keys($Profils[0]) as $col): ?>
                                    <th><?= ucfirst($col) ?></th>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($Profils as $profil): ?>
                            <tr>
                                <?php foreach ($profil as $val): ?>
                                    <td><?= htmlspecialchars($val ?? '') ?></td>
                                <?php endforeach; ?>
                                <td>
                                    <button class="btn btn-primary btn-sm"
                                        onclick='showAnnonceModal(<?= htmlspecialchars(json_encode($profil), ENT_QUOTES) ?>)'>
                                        Créer une annonce
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour l'annonce -->
<div class="modal fade" id="annonceModal" tabindex="-1" role="dialog" aria-labelledby="annonceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="annonceForm">
            <input type="hidden" id="profilData" name="profilData">
            <input type="hidden" id="titre" name="titre">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="annonceModalLabel">Créer une annonce</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombrePoste">Nombre de postes :</label>
                        <input type="number" id="nombrePoste" name="nombrePoste" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label for="annonceText">Annonce :</label>
                        <textarea id="annonceText" name="annonceText" class="form-control" rows="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Poster</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function showAnnonceModal(profil) {
    let diplomeTexte = profil['diplome'] || '';
    if (profil['est_minimum']) diplomeTexte += ' minimum';
    diplomeTexte += ' (ou équivalent)';

    const competencesListe = profil['competences']
        ? profil['competences'].split(';').map(c => '- ' + c.trim()).join('\n')
        : '- À définir';

    const skillsListe = profil['skills']
        ? profil['skills'].split(';').map(s => '- ' + s.trim()).join('\n')
        : '';

    const loisirsListe = profil['loisirs']
        ? profil['loisirs'].split(';').map(l => '- ' + l.trim()).join('\n')
        : '- Aucun loisir spécifié';

    const annonce = `✨ Rejoignez notre équipe en tant que ${profil['titre']} (${profil['type_contrat']}) !

Si vous êtes passionné(e) par :
${loisirsListe}

Vos missions principales au quotidien :
${competencesListe}
${skillsListe ? '\nCompétences complémentaires appréciées :\n' + skillsListe : ''}

Profil idéal :
- Diplôme : ${diplomeTexte}
- Filière : ${profil['filiere']}
- Expérience : ${profil['experience_pro']}

Nous vous offrons l'opportunité de donner du sens à votre carrière et de rejoindre une aventure professionnelle stimulante. Postulez dès maintenant et faites partie de notre équipe ! 🚀`;

    document.getElementById('annonceText').value = annonce;
    document.getElementById('nombrePoste').value = 1;
    document.getElementById('profilData').value = JSON.stringify(profil);
    document.getElementById('titre').value = profil['titre'];
    $('#annonceModal').modal('show');
}

// Soumission du formulaire avec confirmation
document.getElementById('annonceForm').onsubmit = function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/annonces/create', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Confirmation visuelle
            alert('✅ Annonce postée avec succès !');
            $('#annonceModal').modal('hide');
        } else {
            alert('❌ Erreur : ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('⚠️ Une erreur est survenue.');
    });
};
</script>

<?php include "footerFormAnnonces.php" ?>
