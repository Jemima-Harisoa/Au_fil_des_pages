<?php include "headerA.php" ?>

<!-- Page Heading -->

                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
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
                    <button class="btn btn-primary btn-sm" onclick='showAnnonceModal(<?= htmlspecialchars(json_encode($profil), ENT_QUOTES) ?>)'>
                        Créer une annonce
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    
                                
                                </div></div>
                        </div>


<!-- Modal pour l'annonce -->
<div class="modal fade" id="annonceModal" tabindex="-1" role="dialog" aria-labelledby="annonceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="annonceForm">
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
    let annonce = `✨ Rejoignez-nous en tant que ${profil['titre']} (${profil['type_contrat']}) !
Vous êtes passionné(e) par ${profil['loisirs'] ? profil['loisirs'] : 'les nouveaux défis'} ? Notre équipe n’attend que vous !

Vos missions au quotidien :
- ${profil['competences']}
${profil['skills'] ? '- ' + profil['skills'] : ''}

Profil idéal :
- Diplôme : ${profil['diplome']}
- Filière : ${profil['filiere']}
- Expérience : ${profil['experience_pro']}
${profil['certifications'] ? '- Certifications : ' + profil['certifications'] : ''}
${profil['langues'] ? '- Langues : ' + profil['langues'] : ''}

Envie de donner du sens à votre carrière ? Postulez et vivez une aventure professionnelle stimulante avec nous ! 🚀`;

    document.getElementById('annonceText').value = annonce;
    document.getElementById('nombrePoste').value = 1;
    $('#annonceModal').modal('show');
}

// Soumission du formulaire
document.getElementById('annonceForm').onsubmit = function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/annonces/create', {   // <<< lien de la route côté Flight
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
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