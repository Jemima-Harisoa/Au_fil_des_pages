<?php
// var_dump($_SESSION['employe']);
// var_dump($_SESSION['messagerie']); 
//  echo $_SESSION['nbNonLus'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?=  $_SESSION['employe']['nom_departement'] ?></title>

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
        
        /* Style pour les éléments désactivés */
        .nav-item.disabled {
            opacity: 0.5;
            pointer-events: none;
            cursor: not-allowed;
        }
        
        .disabled .nav-link {
            color: #6c757d !important;
        }
        
        .employe-item {
            cursor: pointer;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .employe-item:hover {
            background-color: #f8f9fc;
            border-left-color: #4e73df;
            transform: translateX(5px);
        }

        .employe-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        #searchEmployeModal:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
    </style>
 <!-- Styles supplémentaires -->
    <?= $extra_css ?? '' ?>
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
                <div class="sidebar-brand-text mx-3"><?=  $_SESSION['employe']['nom_departement'] ?></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="/accueilU">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Accueil</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Nav Item - Relevé de présence -->
            <li class="nav-item">
                <a class="nav-link" href="/relevePresenceE/<?= $_SESSION['employe']['id_employe'] ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Relevé de présence actuel</span>
                </a>
            </li>

            <!-- Nav Item - Gestion competence (Pour employé simple) -->
            <?php 
            // Récupérer l'ID de l'employé connecté
            $id_employe_connecte = $_SESSION['employe']['id_employe'] ?? '';
            if($id_employe_connecte): ?>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseGC" 
                aria-expanded="false" aria-controls="collapseGC">
                    <i class="fas fa-fw fa-briefcase"></i>
                    <span>Gestion competence</span>
                </a>
                <div id="collapseGC" class="collapse" aria-labelledby="headingGC" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Gestion des compétences:</h6>
                        
                        <!-- Auto-évaluation des compétences -->
                        <a class="collapse-item" href="/employees/<?= $id_employe_connecte ?>/competences/form">
                            <i class="fas fa-user-edit fa-fw mr-2"></i>Auto-évaluation
                        </a>
                        
                        <!-- Liste des compétences auto-évaluées -->
                        <a class="collapse-item" href="/employees/<?= $id_employe_connecte ?>/competences/list">
                            <i class="fas fa-list-alt fa-fw mr-2"></i>Mes compétences
                        </a>
                        
                        <!-- Statistiques (version limitée pour employé) -->
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Statistiques:</h6>
                        <a class="collapse-item" href="/competences/mes-statistiques/<?= $id_employe_connecte ?>">
                            <i class="fas fa-chart-bar fa-fw mr-2"></i>Mes statistiques
                        </a>
                    </div>
                </div>
            </li>
            <?php endif; ?>

            <!-- Nav Item - Congés et Absences (Pour employé simple) -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConges" 
                aria-expanded="false" aria-controls="collapseConges">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Congés et Absences</span>
                </a>
                <div id="collapseConges" class="collapse" aria-labelledby="headingConges" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Gestion des congés et absences:</h6>
                        <?php if($id_employe_connecte): ?>
                        <a class="collapse-item" href="/conge/employe">Suivi Congés</a>
                        <a class="collapse-item" href="/absence/liste/<?= $id_employe_connecte ?>">Suivi absences</a>
                        <a class="collapse-item" href="/conge/demande">Demande de congé</a>
                        <?php else: ?>
                        <a class="collapse-item disabled" href="#" onclick="return false;">Connectez-vous pour accéder</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Annonces (Lecture seule pour employé) -->
            <li class="nav-item">
                <a class="nav-link" href="/annonces/read/employe">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Voir les annonces</span>
                </a>
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
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span id="messageCenterTitle">Message Center</span>
                                    <div class="d-flex align-items-center">
                                        <input type="text" id="messageCenterSearch" 
                                               class="form-control form-control-sm d-none mr-2" 
                                               placeholder="Filtrer les conversations..." 
                                               style="width: 180px;">
                                        <button id="toggleMessageSearch" class="btn btn-link text-primary p-0 mr-2" title="Filtrer les conversations">
                                            <i class="fas fa-search fa-sm"></i>
                                        </button>
                                        <button id="btnDemarrerConversation" class="btn btn-primary btn-sm" title="Démarrer une conversation">
                                            <i class="fas fa-plus fa-sm"></i>
                                        </button>
                                    </div>
                                </h6>
                                <div id="conversationsEmployeContainer">
                                    <?php if(isset($_SESSION['messagerie']) && !empty($_SESSION['messagerie'])): ?>
                                        <?php foreach($_SESSION['messagerie'] as $conv): ?>
                                            <a class="dropdown-item d-flex align-items-center message-item <?= $conv['nouveaux_messages'] ? 'font-weight-bold' : '' ?>" 
                                               href="/messagerieE/<?= $_SESSION['employe']['id_employe'] ?>/<?= $conv['partenaire_id'] ?>">
                                                <div class="dropdown-list-image mr-3">
                                                    <img class="rounded-circle" 
                                                         src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" 
                                                         alt="..." 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                    <?php if($conv['nouveaux_messages']): ?>
                                                        <span class="badge badge-danger badge-counter unread-dot" style="position:absolute;top:0;right:0;font-size:0.7rem;">●</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div style="flex: 1; min-width: 0;">
                                                    <div class="text-truncate"><?= htmlspecialchars($conv['prenom_partenaire'] . ' ' . $conv['nom_partenaire']) ?></div>
                                                    <div class="small text-gray-500">
                                                        <?= htmlspecialchars($conv['derniere_modification'] ?? 'Pas de message') ?>
                                                        <?php if($conv['nouveaux_messages']): ?>
                                                            <span class="badge badge-danger badge-counter ml-2">Nouveau</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="dropdown-item text-center text-muted">
                                            Aucune conversation
                                        </div>
                                    <?php endif; ?>
                                </div>
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
                                    if(isset($_SESSION['employe'])) {
                                        echo htmlspecialchars($_SESSION['employe']['nom_personne'] ?? 'Inconnu') . ' ' . 
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
                                <a class="dropdown-item" href="deconnexionU" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>

                <div class="container-fluid">

<!-- Modal Démarrer Conversation -->
<div class="modal fade" id="modalDemarrerConversation" tabindex="-1" role="dialog" aria-labelledby="modalDemarrerConversationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDemarrerConversationLabel">
                    <i class="fas fa-comments mr-2"></i>Démarrer une conversation
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="searchEmployeModal">Rechercher un employé</label>
                    <div class="input-group">
                        <input type="text" 
                               id="searchEmployeModal" 
                               class="form-control" 
                               placeholder="Nom, prénom, poste, département..."
                               autocomplete="off">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                    <small class="form-text text-muted">Tapez au moins 2 caractères pour rechercher</small>
                </div>
                
                <div id="listeEmployesModal" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <p>Utilisez la recherche pour trouver un employé</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
    
    // === GESTION DU DROPDOWN ===
    const messagesDropdownMenu = document.querySelector('#messagesDropdown + .dropdown-menu');
    
    if (messagesDropdownMenu) {
        messagesDropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // === BOUTON DÉMARRER CONVERSATION ===
    const btnDemarrer = document.getElementById('btnDemarrerConversation');
    const modalDemarrer = $('#modalDemarrerConversation');
    const searchEmployeModal = document.getElementById('searchEmployeModal');
    const listeEmployesModal = document.getElementById('listeEmployesModal');
    
    btnDemarrer.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Fermer le dropdown des messages
        $('#messagesDropdown').dropdown('hide');
        
        // Ouvrir le modal
        modalDemarrer.modal('show');
        
        // Charger tous les employés au démarrage
        setTimeout(() => {
            searchEmployeModal.value = '';
            chargerTousEmployes();
        }, 300);
    });
    
    // Recherche dans le modal
    let searchModalTimeout;
    searchEmployeModal.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        clearTimeout(searchModalTimeout);
        
        if (query.length === 0) {
            chargerTousEmployes();
            return;
        }
        
        if (query.length < 2) {
            listeEmployesModal.innerHTML = `
                <div class="text-center text-muted py-3">
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p>Tapez au moins 2 caractères</p>
                </div>
            `;
            return;
        }
        
        searchModalTimeout = setTimeout(() => {
            rechercherEmployesModal(query);
        }, 300);
    });
    
    function chargerTousEmployes() {
        listeEmployesModal.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>';
        
        fetch(`/messagerie/searchEmployes/${idEmploye}?q=a`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.employes && data.employes.length > 0) {
                    afficherListeEmployes(data.employes);
                } else {
                    listeEmployesModal.innerHTML = `
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <p>Aucun employé disponible</p>
                        </div>
                    `;
                }
            })
            .catch(err => {
                console.error('Erreur chargement employés:', err);
                listeEmployesModal.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Erreur de chargement
                    </div>
                `;
            });
    }
    
    function rechercherEmployesModal(query) {
        listeEmployesModal.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Recherche...</div>';
        
        fetch(`/messagerie/searchEmployes/${idEmploye}?q=${encodeURIComponent(query)}`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.employes && data.employes.length > 0) {
                    afficherListeEmployes(data.employes);
                } else {
                    listeEmployesModal.innerHTML = `
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-search fa-2x mb-2"></i>
                            <p>Aucun résultat pour "${query}"</p>
                        </div>
                    `;
                }
            })
            .catch(err => {
                console.error('Erreur recherche:', err);
                listeEmployesModal.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Erreur de recherche
                    </div>
                `;
            });
    }
    
    function afficherListeEmployes(employes) {
        let html = '';
        employes.forEach(emp => {
            html += `
                <div class="employe-item p-3 border-bottom" data-id-employe="${emp.id_employe}">
                    <div class="d-flex align-items-center">
                        <img src="${emp.lien_image || 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'}" 
                             class="rounded-circle mr-3" 
                             alt="${emp.prenom || ''} ${emp.nom_personne || ''}">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold">${emp.prenom || ''} ${emp.nom_personne || ''}</h6>
                            <small class="text-muted">
                                <i class="fas fa-briefcase mr-1"></i>${emp.poste || 'N/A'}
                                ${emp.nom_departement ? `<i class="fas fa-building ml-2 mr-1"></i>${emp.nom_departement}` : ''}
                            </small>
                        </div>
                        <button class="btn btn-primary btn-sm btn-select-employe">
                            <i class="fas fa-comment-dots mr-1"></i>Discuter
                        </button>
                    </div>
                </div>
            `;
        });
        
        listeEmployesModal.innerHTML = html;
        
        // Ajouter les écouteurs d'événements
        document.querySelectorAll('.employe-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (!e.target.closest('.btn-select-employe')) {
                    this.querySelector('.btn-select-employe').click();
                }
            });
        });
        
        document.querySelectorAll('.btn-select-employe').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const item = this.closest('.employe-item');
                const partenaireId = item.dataset.idEmploye;
                
                // Fermer le modal
                modalDemarrer.modal('hide');
                
                // Rediriger vers la conversation
                window.location.href = `/messagerieE/${idEmploye}/${partenaireId}`;
            });
        });
    }
    
    // === GESTION DE LA RECHERCHE DANS LE DROPDOWN ===
    const searchInput = document.getElementById('messageCenterSearch');
    const messageCenterTitle = document.getElementById('messageCenterTitle');
    const toggleBtn = document.getElementById('toggleMessageSearch');
    const conversationsContainer = document.getElementById('conversationsEmployeContainer');
    const searchResultsContainer = document.getElementById('employeSearchResults');
    
    // Empêcher la propagation sur l'input
    ['mousedown','mouseup','click','focus','blur','keydown','keyup','keypress','input'].forEach(eventType => {
        searchInput.addEventListener(eventType, function(e) {
            e.stopPropagation();
        }, true);
    });
    
    // Toggle recherche dans conversations
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const isSearchVisible = !searchInput.classList.contains('d-none');
        
        if (isSearchVisible) {
            searchInput.value = '';
            searchInput.classList.add('d-none');
            toggleBtn.innerHTML = '<i class="fas fa-search fa-sm"></i>';
            // Réafficher toutes les conversations
            document.querySelectorAll('.message-item').forEach(item => {
                item.classList.remove('d-none');
            });
        } else {
            searchInput.classList.remove('d-none');
            toggleBtn.innerHTML = '<i class="fas fa-times fa-sm"></i>';
            requestAnimationFrame(() => searchInput.focus());
        }
    });
    
    // Recherche dans les conversations existantes
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const items = document.querySelectorAll('.message-item');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(query)) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });
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
                    container.innerHTML = '<div class="dropdown-item text-center text-muted">Aucune conversation</div>';
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
</script>