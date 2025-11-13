<?php include("headerA.php");?>
<button id="btn-ajouter" class="btn btn-primary mb-3">Ajouter une disponibilité</button>

<!-- Tableau -->
<table id="tableDisponibilite" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Jour</th>
            <th>Heure Début</th>
            <th>Heure Fin</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<!-- Modal pour Insertion / Modification -->
<div class="modal fade" id="modalDispo" tabindex="-1" aria-labelledby="modalDispoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formDispo">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDispoLabel">Nouvelle disponibilité</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_dispo">

                    <div class="form-group">
                        <label for="jour">Jour</label>
                        <select id="jour" class="form-control" required>
                            <option value="">-- Sélectionnez --</option>
                            <option value="1">Lundi</option>
                            <option value="2">Mardi</option>
                            <option value="3">Mercredi</option>
                            <option value="4">Jeudi</option>
                            <option value="5">Vendredi</option>
                            <option value="6">Samedi</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="heure_debut">Heure début</label>
                        <input type="time" id="heure_debut" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="heure_fin">Heure fin</label>
                        <input type="time" id="heure_fin" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Valider</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include("footerA.php");?>
<script src="js/disponibilite-entretien.js"></script>