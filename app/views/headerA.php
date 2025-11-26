<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?=  $_SESSION['departement']['nom'] ?? 'Inconnu'?></title>

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
</style>
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
                <div class="sidebar-brand-text mx-3"><?=  $_SESSION['departement']['nom'] ?? 'Inconnu' ?></div>
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

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="/allTests"  aria-expanded="true"
                    aria-controls="collapsePages">
                     <i class="fas fa-fw fa-pen"></i>
                    <span>Tests</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/pointage">
                <i class="fas fa-clock"></i> <!-- Icône horloge -->
                    <span>Pointage</span></a>
            </li>

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="/listeCV">
                <i class="fas fa-file-alt"></i> <!-- Icône document -->
                    <span>Liste CV</span></a>
            </li>

    
            <!-- Parite pour la migration -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRH" aria-expanded="false" aria-controls="collapseRH">
                    <i class="fas fa-fw fa-briefcase"></i>
                    <span>RH / Migration</span>
                </a>
                <div id="collapseRH" class="collapse" aria-labelledby="headingRH" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Gestion RH:</h6>
                        
                        <!-- Candidats -->
                        <a class="collapse-item" href="/migration/candidats">Candidats retenus</a>
                        
                        <!-- Contrats -->
                        <a class="collapse-item" href="/migration/contrats">Liste des contrats</a>
                        <a class="collapse-item" href="/migration/contrat/create">Créer un contrat</a>
                        
                        <!-- Action -->
                        <!-- <a class="collapse-item" href="/migration/contrat/edit">Éditer un contrat</a> -->
                    </div>
                </div>
            </li>

            <!-- Nav Item - Congés -->
            <li class="nav-item active">
                <a class="nav-link" href="/conge">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Suivi Congés</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/absence/liste">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Suivi absences</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/conge/demande">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Demande de congé</span></a>
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
                                    <?php if(isset($_SESSION['nbNonLus']) && $_SESSION['nbNonLus'] > 0): ?>
                                        <span id="unreadBadge" class="badge badge-danger badge-counter"><?= $_SESSION['nbNonLus'] ?></span>
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
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $_SESSION['infoAdmin']['nom'] ?? 'Inconnu'?> <br> <?= $_SESSION['infoAdmin']['prenom'] ?? 'Inconnu' ?></span>
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
                    <!-- Votre contenu ici -->
                </div>
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2023</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form method="post" action="/deconnexionE" style="display:inline;">
                        <input type="hidden" name="Nom" value="<?= htmlspecialchars($_SESSION['employe']['prenom']) ?>">
                        <input type="hidden" name="mdp" value="<?= htmlspecialchars($_SESSION['employe']['mdp_login']) ?>">
                        <button class="btn btn-primary" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="/vendor/jquery/jquery.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="/js/sb-admin-2.min.js"></script>
    
    <!-- Page level plugins -->
    <script src="/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="/js/demo/datatables-demo.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const idEmploye = <?= $_SESSION['employe']['id_employe'] ?? 'null' ?>;
    
    if (!idEmploye) {
        console.error('ID employé non trouvé');
        return;
    }
    
    // Charger les conversations au démarrage
    loadConversations(idEmploye);
    
    // Rafraîchir toutes les 10 secondes
    setInterval(() => loadConversations(idEmploye), 10000);
    
    // === GESTION DE LA RECHERCHE ===
    const searchInput = document.getElementById('messageCenterSearch');
    const messageCenterTitle = document.getElementById('messageCenterTitle');
    const toggleBtn = document.getElementById('toggleMessageSearch');
    const conversationsContainer = document.getElementById('conversationsEmployeContainer');
    const searchResultsContainer = document.getElementById('employeSearchResults');
    const messagesDropdown = document.getElementById('messagesDropdown');
    const dropdownMenu = document.querySelector('.dropdown-list.dropdown-menu');
    
    // Empêcher la fermeture du dropdown quand on clique sur l'input ou le header
    if (dropdownMenu) {
        dropdownMenu.addEventListener('click', function(e) {
            // Ne fermer le dropdown QUE si on clique sur un lien de conversation
            if (!e.target.closest('a[href*="/messagerieE/"]') && 
                !e.target.closest('.dropdown-item[onclick]')) {
                e.stopPropagation();
            }
        });
    }
    
    // Toggle recherche au clic sur loupe/X
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const isSearchVisible = !searchInput.classList.contains('d-none');
        
        if (isSearchVisible) {
            // Fermer la recherche (clic sur X)
            searchInput.value = '';
            searchInput.classList.add('d-none');
            messageCenterTitle.classList.remove('d-none');
            searchResultsContainer.classList.add('d-none');
            conversationsContainer.classList.remove('d-none');
            toggleBtn.innerHTML = '<i class="fas fa-search fa-sm"></i>';
        } else {
            // Ouvrir la recherche (clic sur loupe)
            messageCenterTitle.classList.add('d-none');
            searchInput.classList.remove('d-none');
            toggleBtn.innerHTML = '<i class="fas fa-times fa-sm"></i>';
            
            setTimeout(() => {
                searchInput.focus();
            }, 100);
        }
    });
    
    // Empêcher la fermeture lors des interactions avec l'input
    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    searchInput.addEventListener('mousedown', function(e) {
        e.stopPropagation();
    });
    
    searchInput.addEventListener('focus', function(e) {
        e.stopPropagation();
    });
    
    searchInput.addEventListener('keydown', function(e) {
        e.stopPropagation();
    });
    
    // Recherche d'employés avec debounce
    let searchTimeout;
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchResultsContainer.classList.add('d-none');
            conversationsContainer.classList.remove('d-none');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch(`/messagerie/searchEmployes/${idEmploye}?q=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.employes && data.employes.length > 0) {
                        conversationsContainer.classList.add('d-none');
                        searchResultsContainer.classList.remove('d-none');
                        
                        let html = '<h6 class="dropdown-header" style="background-color: #f8f9fc; color: #4e73df;">Résultats de recherche</h6>';
                        data.employes.forEach(emp => {
                            html += `
                                <a class="dropdown-item d-flex align-items-center" 
                                   href="#" 
                                   onclick="startConversation(${idEmploye}, ${emp.id_employe}); return false;">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" 
                                             src="${emp.lien_image || 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'}" 
                                             alt="..." style="width: 40px; height: 40px; object-fit: cover;">
                                    </div>
                                    <div>
                                        <div class="text-truncate font-weight-bold">${emp.prenom} ${emp.nom_personne || ''}</div>
                                        <div class="small text-gray-500">${emp.poste || 'N/A'} - ${emp.nom_departement || 'N/A'}</div>
                                    </div>
                                </a>
                            `;
                        });
                        searchResultsContainer.innerHTML = html;
                    } else {
                        conversationsContainer.classList.add('d-none');
                        searchResultsContainer.classList.remove('d-none');
                        searchResultsContainer.innerHTML = '<a class="dropdown-item text-center small text-gray-500 no-results-message">Aucun employé trouvé</a>';
                    }
                })
                .catch(err => {
                    console.error('Erreur recherche:', err);
                    searchResultsContainer.classList.remove('d-none');
                    searchResultsContainer.innerHTML = '<a class="dropdown-item text-center small text-danger">Erreur de recherche</a>';
                });
        }, 300);
    });
    
    // Réinitialiser SEULEMENT quand le dropdown se ferme complètement
    $(messagesDropdown).on('hidden.bs.dropdown', function () {
        searchInput.value = '';
        searchInput.classList.add('d-none');
        messageCenterTitle.classList.remove('d-none');
        searchResultsContainer.classList.add('d-none');
        conversationsContainer.classList.remove('d-none');
        toggleBtn.innerHTML = '<i class="fas fa-search fa-sm"></i>';
    });
});

function loadConversations(idEmploye) {
    fetch(`/messagerie/conversationsE/${idEmploye}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('conversationsEmployeContainer');
                const badge = document.getElementById('unreadBadge');
                
                // Mettre à jour le badge
                if (data.nbNonLus > 0) {
                    if (badge) {
                        badge.textContent = data.nbNonLus;
                        badge.classList.remove('d-none');
                    } else {
                        const newBadge = document.createElement('span');
                        newBadge.id = 'unreadBadge';
                        newBadge.className = 'badge badge-danger badge-counter';
                        newBadge.textContent = data.nbNonLus;
                        document.getElementById('messagesDropdown').appendChild(newBadge);
                    }
                } else if (badge) {
                    badge.classList.add('d-none');
                }
                
                // Afficher les conversations
                if (data.conversations.length === 0) {
                    container.innerHTML = '<a class="dropdown-item text-center small text-gray-500">Aucune conversation</a>';
                    return;
                }
                
                let html = '';
                data.conversations.forEach(conv => {
                    const unreadClass = conv.nouveaux_messages ? 'font-weight-bold' : '';
                    const unreadIcon = conv.nouveaux_messages ? '<span class="badge badge-danger badge-counter ml-2">Nouveau</span>' : '';
                    
                    html += `
                        <a class="dropdown-item d-flex align-items-center message-item ${unreadClass}" 
                           href="/messagerieE/${idEmploye}/${conv.partenaire_id}">
                            <div class="dropdown-list-image mr-3">
                                <img class="rounded-circle" src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" 
                                     alt="..." style="width: 40px; height: 40px; object-fit: cover;">
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div class="text-truncate">${conv.prenom_partenaire} ${conv.nom_partenaire || ''}</div>
                                <div class="small text-gray-500">${conv.derniere_modification || 'Pas de message'} ${unreadIcon}</div>
                            </div>
                        </a>
                    `;
                });
                
                container.innerHTML = html;
            }
        })
        .catch(err => console.error('Erreur chargement conversations:', err));
}

function startConversation(idEmploye, partenaireId) {
    window.location.href = `/messagerieE/${idEmploye}/${partenaireId}`;
}
</script>

</body>

</html>