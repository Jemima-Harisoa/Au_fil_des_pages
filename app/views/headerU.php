<?php 
$nbNonLus = 0;
if(!empty($_SESSION['messagerie'])) {
    foreach($_SESSION['messagerie'] as $msg) {
        if(($msg['dernier_auteur'] ?? '') === 'Admin' && empty($msg['lu'])) {
            $nbNonLus++;
        }
    }
} ?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?=  $_SESSION['utilisateur']['nom'] ?></title>

    <!-- Custom fonts for this template-->
    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="/css/sb-admin-2.min.css" rel="stylesheet">
    
    <!-- Custom styles for this page -->
    <link href="/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- In the head section, add the styles -->
    <style>
.dropdown-header {
    display: flex !important;
    align-items: center !important;
    padding: 0.5rem 1rem !important;
}

#messageCenterSearch {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    font-size: 0.85rem;
    padding: 0.25rem 0.5rem;
}

#messageCenterSearch::placeholder {
    color: rgba(255,255,255,0.7);
}

#messageCenterSearch:focus {
    outline: none;
    background: rgba(255,255,255,0.3);
}

.message-item {
    transition: all 0.2s;
}

.no-results-message {
    font-style: italic;
    color: #6c757d !important;
}

.hidden-by-search {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
}
</style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3"><?=  $_SESSION['utilisateur']['nom'] ?></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="/accueilU">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Accueil</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">


            <li class="nav-item">
                <a class="nav-link" href="/annonces/readU">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Annonces</span></a>
            </li>


            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                        </li>

                    
                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <?php if($nbNonLus > 0): ?>
                                    <span class="badge badge-danger badge-counter"><?= $nbNonLus ?></span>
                                <?php endif; ?>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span id="messageCenterTitle">Message Center</span>
                                    <input type="text" id="messageCenterSearch" 
                                           class="form-control form-control-sm d-none" 
                                           placeholder="Rechercher..." 
                                           style="width: 150px;">
                                    <button id="toggleMessageSearch" class="btn btn-link text-white p-0 ml-2">
                                        <i class="fas fa-search fa-sm"></i>
                                    </button>
                                </h6>
                                <?php if(!empty($_SESSION['messagerie'])): ?>
    <?php foreach($_SESSION['messagerie'] as $msg): ?>
        <a class="dropdown-item d-flex align-items-center" href="/messagerieU/<?= $msg['id_candidat'] ?>/<?= $msg['id_annonce'] ?>">
            <div class="dropdown-list-image mr-3">
                <img class="rounded-circle" src="/img/undraw_profile_1.svg" alt="Profil">
                <?php if(($msg['dernier_auteur'] ?? '') === 'Admin' && empty($msg['lu'])): ?>
                    <span class="badge badge-danger badge-counter" style="position:absolute;top:0;right:0;font-size:0.7rem;">●</span>
                <?php endif; ?>
            </div>
            <div class="<?= (($msg['dernier_auteur'] ?? '') === 'Admin' && empty($msg['lu'])) ? 'font-weight-bold' : '' ?>">
                <div class="text-truncate"><?= htmlspecialchars($msg['titre']) ?></div>
                <div class="small text-gray-500"><?= htmlspecialchars($msg['nom_entreprise']) ?></div>
            </div>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <div class="dropdown-item text-center text-muted">
        Aucune conversation
    </div>
<?php endif; ?>
                            </div>
                        </li>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $_SESSION['utilisateur']['nom'] ?> <br></span>
                                <img class="img-profile rounded-circle"
                                    src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="deconnexionU" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
