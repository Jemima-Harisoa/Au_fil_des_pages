<?php include "headerA.php" ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    
                    <!-- Page Heading -->
                    <button class="btn btn-primary btn-icon-split" id="bouton-planification" type="button">
                        <span class="icon text-white-50">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <span class="text">Planifier entretien</span>
                    </button>       
                    <div id="message-container">

                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Planning Entretien</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Candidats</th>
                                            <th>Age Candidats</th>
                                            <th>Responsables</th>
                                            <th>Profil</th>
                                            <th>ScoreTest</th>
                                            <th>DateTest</th>
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

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

          <?php include "footer.php" ?>
