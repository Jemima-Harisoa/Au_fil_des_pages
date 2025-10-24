<?php include("headerA.php");?>
                    <button class="btn btn-primary btn-icon-split" id="bouton-planification" type="button" <?php echo $verified ? "disabled":""?>>
                        <span class="icon text-white-50">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <span class="text">Planifier entretien</span>
                    </button>       
                    <div class="my-2"></div>
                    
                    <?php 
                 if($verified){ ?>
                    <div class="alert alert-info "id="message" role="alert">
                        Les entretiens sont tous planifiés</div>  
                <?php }
                   else{?>   
                        <div id="message" role="alert"></div>
                   <?php }?>
                    <form id="form-filtre"  class="p-4 border rounded shadow-sm bg-light">
                    <div class="mb-3">
                        <label for="candidat" class="form-label">Candidat</label>
                        <input type="text" class="form-control" placeholder="ex. Rakoto Jean" name="candidat" id="candidat">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                        <label for="age_min" class="form-label">Âge minimal</label>
                        <input type="number" class="form-control" name="age_min" id="age_min">
                        </div>
                        <div class="col-md-6 mb-3">
                        <label for="age_max" class="form-label">Âge maximal</label>
                        <input type="number" class="form-control" name="age_max" id="age_max">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="profil_candidat" class="form-label">Profil Candidat</label>
                        <input type="text" class="form-control" placeholder="ex. Vendeur" name="profil_candidat" id="profil_candidat">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                        <label for="score_min" class="form-label">Score test minimal</label>
                        <input type="number" class="form-control" name="score_min" id="score_min">
                        </div>
                        <div class="col-md-6 mb-3">
                        <label for="score_max" class="form-label">Score test maximal</label>
                        <input type="number" class="form-control" name="score_max" id="score_max">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="date_test" class="form-label">Date test</label>
                        <input type="date" class="form-control" name="date_test" id="date_test">
                    </div>

                    <div class="mb-3">
                        <label for="etat" class="form-label">Etat</label>
                        <input type="text" class="form-control" name="etat" id="etat">
                    </div>
                    
                    <div class="mb-3">
                        <label for="date_heure_entretien" class="form-label">Date & Heure Entretien</label>
                        <input type="datetime-local" class="form-control" name="date_heure_entretien" id="date_heure_entretien">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                        <button type="reset" class="btn btn-secondary ms-2">Réinitialiser</button>
                    </div>
                    </form>
                    <div class="my-2"></div>
                    <div id="message-container">

                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Candidats</th>
                                            <th>Profils Candidats</th>
                                            <th>Age Candidats</th>
                                            <th>ScoreTest</th>
                                            <th>DateTest</th>
                                            <th>Etat</th>
                                            <th>dateHeureEntretien</th>
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
        <div class="modal fade" id="modalEntretien" tabindex="-1" aria-labelledby="modalEntretienLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="modalEntretienLabel">Modification entretien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <form id="form-historique">
                <input type="hidden" id="id_entretien" name="id_entretien">

                <div class="mb-3">
                    <label id="label-date" for="date_heure_modification" class="form-label">Nouvelle date et heure </label>
                    <input type="datetime-local" class="form-control" id="date_heure_modification" name="date_heure_modification">
                </div>

                <div class="mb-3">
                    <label for="raison_modification" class="form-label">Raison de modification</label>
                    <textarea class="form-control" id="raison_modification" name="raison_modification" rows="3" required></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <button id="btn-annuler" type="button" class="btn btn-secondary w-50 me-2" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn bg-gradient-primary w-50">Valider</button>
                </div>
                </form>
            </div>
            </div>
        </div>
        </div>
        <?php include("footerA.php");?>