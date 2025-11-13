<?php include("headerA.php");?>
                    <div class="my-2"></div>
                
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTableScoreEntretien" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Candidats</th>
                                            <th>Profils Candidats</th>
                                            <th>ScoreTest</th>
                                            <th>Etat</th>
                                            <th>dateHeureEntretien</th>
                                            <th>Note</th>
                                            <th>Appreciation</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

        <!-- Modal Modification Entretien -->
        <div class="modal fade" id="modalScoreEntretien" tabindex="-1" aria-labelledby="modalEntretienLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="modalEntretienLabel">Attribution Score entretien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"><i class="fa fa-times-circle"></i></button>
            </div>

            <div class="modal-body">
                <form id="form-historique">
                <input type="hidden" id="id_entretien" name="id_entretien">

                <div class="mb-3">
                    <label id="label-date" for="score" class="form-label">Score(entre 0 et 100)</label>
                    <input type="number" class="form-control"min="0" step="0.01" id="score" name="score">
                </div>
                <div class="d-flex justify-content-between">
                    <button id="btn-annuler" type="button" class="btn btn-secondary w-50 me-2" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn bg-gradient-primary text-white w-50">Valider</button>
                </div>
                </form>
            </div>
            </div>
        </div>
        </div>
        <?php include("footerA.php");?>