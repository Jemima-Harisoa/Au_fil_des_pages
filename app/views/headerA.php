<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= $_SESSION['departement']['nom'] ?? 'Inconnu'?></title>

    <!-- Custom fonts for this template-->
    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="/css/sb-admin-2.min.css" rel="stylesheet">
    
    <!-- Custom styles for this page -->
    <link href="/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- Styles supplémentaires -->
    <?= $extra_css ?? '' ?>
    <style>
        .dropdown-header {
            display: flex !important;
            align-items: center !important;
            padding: 0.5rem 1rem !important;
        }

        #messageCenterSearch {
            background: white;
            border: 1px solid rgba(255,255,255,0.3);
            color: #333;
            font-size: 0.85rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        #messageCenterSearch::placeholder {
            color: #999;
        }

        #messageCenterSearch:focus {
            outline: none;
            background: white;
            border-color: white;
            box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
        }

        .message-item {
            transition: all 0.2s;
        }

        .message-item:hover {
            background-color: #f8f9fc;
        }

        .no-results-message {
            font-style: italic;
            color: #6c757d !important;
        }

        .dropdown-menu {
            max-height: 400px;
            overflow-y: auto;
        }

        #employeSearchResults .dropdown-item,
        #conversationsEmployeContainer .dropdown-item {
            cursor: pointer;
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
                <div class="sidebar-brand-text mx-3"><?= $_SESSION['departement']['nom'] ?? 'Inconnu' ?></div>
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
                <a class="nav-link" href="/relevePresenceE/<?= $_SESSION['employe']['id_employe'] ?? ($_SESSION['infoAdmin']['id_employe'] ?? 'Inconnu')?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Relevé de présence actuel</span>
                </a>
            </li>

                        <!-- Divider -->
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="/employeList">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Gestion employe</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">


            <!-- Nav Item - Pages Collapse Menu -->
            <!-- Annonces -->
            <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true"
                    aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Annonces</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Suivie d'annonces:</h6>
                        <a class="collapse-item" href="/annonces/form">Creer une annonce</a>
                        <a class="collapse-item" href="/annonces/read">Voirs les annonces</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Tests -->
            <li class="nav-item">
                <a class="nav-link" href="/allTests" aria-expanded="true"
                    aria-controls="collapsePages">
                     <i class="fas fa-fw fa-pen"></i>
                    <span>Tests</span>
                </a>
            </li>
            
            <!-- Nav Item - Pointage -->
            <li class="nav-item">
                <a class="nav-link" href="/pointage">
                <i class="fas fa-clock"></i>
                    <span>Pointage</span></a>
            </li>

            <!-- Nav Item - Liste CV (Uniquement pour l'admin) -->
            <?php if(isset($_SESSION['admin'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="/listeCV">
                <i class="fas fa-file-alt"></i>
                    <span>Liste CV</span></a>
            </li>
            <?php endif; ?>

            <!-- Partie RH / Migration (Uniquement pour l'admin) -->
            <?php if(isset($_SESSION['admin'])): ?>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRH" aria-expanded="false" aria-controls="collapseRH">
                    <i class="fas fa-fw fa-briefcase"></i>
                    <span>RH / Migration</span>
                </a>
                <div id="collapseRH" class="collapse" aria-labelledby="headingRH" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Gestion RH:</h6>
                        <a class="collapse-item" href="/migration/candidats">Candidats retenus</a>
                        <a class="collapse-item" href="/migration/contrats">Liste des contrats</a>
                        <a class="collapse-item" href="/migration/contrat/create">Créer un contrat</a>
                    </div>
                </div>
            </li>
            <?php endif; ?>
            
            <!-- Nav Item - Gestion competence -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseGC" 
                aria-expanded="false" aria-controls="collapseGC">
                    <i class="fas fa-fw fa-briefcase"></i>
                    <span>Gestion competence</span>
                </a>
                <div id="collapseGC" class="collapse" aria-labelledby="headingGC" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Gestion des compétences:</h6>
                        <?php if(isset($_SESSION['admin'])): ?>
                        <a class="collapse-item" href="/competences">Liste des competences</a>
                        <?php endif; ?>
                        <a class="collapse-item" href="/competences/details/<?= $_SESSION['infoAdmin']['id_employe'] ?? ($_SESSION['employe']['id_employe'] ?? '') ?>">
                            Voir Mes competences
                        </a>
                        <?php if(isset($_SESSION['admin'])): ?>
                        <a class="collapse-item" href="/competences/statistiques">statistiques des competences</a>
                        <a class="collapse-item" href="/competences/cartographie">Cartographie</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Congés et Absences -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConges" 
                aria-expanded="false" aria-controls="collapseConges">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Congés et Absences</span>
                </a>
                <div id="collapseConges" class="collapse" aria-labelledby="headingConges" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Gestion des congés et absences:</h6>
                        <a class="collapse-item" href="/conge">Suivi Congés</a>
                        <a class="collapse-item" href="/absence/liste">Suivi absences</a>
                        <a class="collapse-item" href="/conge/demande">Demande de congé</a>
                    </div>
                </div>
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
                                <?php 
                                $nbNonLus = $_SESSION['nbNonLus'] ?? 0;
                                if($nbNonLus > 0): ?>
                                    <span id="unreadBadge" class="badge badge-danger badge-counter"><?= $nbNonLus ?></span>
                                <?php endif; ?>
                            </a>
                            <!-- Dropdown - Messages -->    
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown" style="width: 350px;">
                                <h6 class="dropdown-header d-flex justify-content-between align-items-center" style="background: linear-gradient(180deg, #4e73df 10%, #224abe 100%); color: white;">
                                    <span id="messageCenterTitle">Message Center</span>
                                    <input type="text" id="messageCenterSearch" 
                                           class="form-control form-control-sm d-none" 
                                           placeholder="Rechercher un employé..." 
                                           autocomplete="off"
                                           style="flex: 1; margin-right: 10px;">
                                    <button id="toggleMessageSearch" class="btn btn-link text-white p-0 ml-2" type="button">
                                        <i class="fas fa-search fa-sm"></i>
                                    </button>
                                </h6>
                                
                                <!-- Conversations existantes -->
                                <div id="conversationsEmployeContainer">
                                    <a class="dropdown-item text-center small text-gray-500">Chargement...</a>
                                </div>
                                
                                <!-- Résultats de recherche -->
                                <div id="employeSearchResults" class="d-none">
                                    <!-- Les résultats de recherche apparaîtront ici -->
                                </div>
                            </div>
                        </li>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php 
                                    if(isset($_SESSION['infoAdmin'])) {
                                        echo htmlspecialchars($_SESSION['infoAdmin']['nom'] ?? 'Inconnu') . ' ' . 
                                             htmlspecialchars($_SESSION['infoAdmin']['prenom'] ?? 'Inconnu');
                                    } elseif(isset($_SESSION['employe'])) {
                                        echo htmlspecialchars($_SESSION['employe']['nom'] ?? 'Inconnu') . ' ' . 
                                             htmlspecialchars($_SESSION['employe']['prenom'] ?? 'Inconnu');
                                    } else {
                                        echo 'Inconnu';
                                    }
                                    ?>
                                </span>
                                <img class="img-profile rounded-circle"
                                    src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <?php if(isset($_SESSION['employe'])): ?>
                                <form method="post" action="/deconnexionE" style="display:inline;">
                                    <input type="hidden" name="Nom" value="<?= htmlspecialchars($_SESSION['employe']['prenom'] ?? 'Inconnu') ?>">
                                    <input type="hidden" name="mdp" value="<?= htmlspecialchars($_SESSION['employe']['mdp_login'] ?? 'Inconnu') ?>">
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </button>
                                </form>
                                <?php else: ?>
                                <form method="post" action="/deconnexion" style="display:inline;">
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </li>

                    </ul>

                </nav>

                <div class="container-fluid">
                    <!-- Votre contenu ici -->