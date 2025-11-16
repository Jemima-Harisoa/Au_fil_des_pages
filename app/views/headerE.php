


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= $_SESSION['employe']['nom_departement'] ?></title>


    <!-- Custom fonts for this template-->
    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="/css/sb-admin-2.min.css" rel="stylesheet">
    
    <!-- Custom styles for this page -->
    <link href="/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">


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
                <div class="sidebar-brand-text mx-3"><?=  $_SESSION['employe']['prenom'] ?></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="/accueilE">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Accueil</span></a>
            </li>
            <li class="nav-item">
    <a class="nav-link" href="/relevePresenceE/<?= $_SESSION['employe']['id_employe'] ?>">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Relevé de présence actuel</span>
    </a>
</li>

            <!-- Divider -->
        
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
                                    <?php if($_SESSION['nbNonLus'] > 0): ?>
                                        <span id="unreadBadge" class="badge badge-danger badge-counter"><?= $_SESSION['nbNonLus'] ?></span>
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
<!---messagerie--->

                        </li>


                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $_SESSION['employe']['prenom'] ?> <br></span>
                                <img class="img-profile rounded-circle"

                                    src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">

                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
<form method="post" action="/deconnexionE" style="display:inline;">
    <input type="hidden" name="Nom" value="<?= htmlspecialchars($_SESSION['employe']['prenom']) ?>">
    <input type="hidden" name="mdp" value="<?= htmlspecialchars($_SESSION['employe']['mdp_login']) ?>">
    <button type="submit" class="dropdown-item">
        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
        Logout
    </button>
</form>

                            </div>
                        </li>

                    </ul>

                </nav>

                <div class="container-fluid">

