<?php
// competences_admin.php
$extra_css = '
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .competence-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border-left: 4px solid #4e73df;
    }
    
    .competence-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    .btn-details {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-details:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
    }
    
    .niveau-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .badge-faible { background-color: #e74a3b; color: white; }
    .badge-correct { background-color: #f6c23e; color: #2c3e50; }
    .badge-bon { background-color: #1cc88a; color: white; }
    
    .filter-section {
        background-color: #f8f9fc;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        border-left: 4px solid #4e73df;
    }
    
    .distribution-bar {
        height: 8px;
        background: #e3e6f0;
        border-radius: 4px;
        overflow: hidden;
        display: flex;
        margin: 5px 0;
    }
    
    .distribution-segment {
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .distribution-legend {
        font-size: 0.75rem;
        color: #6e707e;
    }
    
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
    }

    .competence-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        backdrop-filter: blur(5px);
    }

    .competence-modal {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 95%;
        max-width: 1200px;
        height: 90vh;
        background: white;
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: modalAppear 0.3s ease-out;
    }

    @keyframes modalAppear {
        from {
            opacity: 0;
            transform: translate(-50%, -48%);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
    }

    .competence-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-bottom: 1px solid #e3e6f0;
    }

    .competence-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.3rem;
    }

    .close-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.2rem;
    }

    .close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .competence-content {
        flex: 1;
        overflow-y: auto;
        padding: 0;
        background: #f8f9fc;
    }

    .competence-content .container {
        max-width: 100%;
        margin: 0;
        padding: 25px;
        background: white;
        min-height: 100%;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .competence-modal {
            width: 98%;
            height: 95vh;
            border-radius: 8px;
        }
        
        .competence-header {
            padding: 15px 20px;
        }
        
        .competence-content .container {
            padding: 15px;
        }
    }

    .progress {
        height: 8px;
        margin-bottom: 5px;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.05);
    }
</style>
';

Flight::render("headerA", ['extra_css' => $extra_css]);
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cartographie des Compétences</h1>
        <div class="btn-group">
            <button class="btn btn-sm btn-primary" onclick="exporterCompetences('csv')">
                <i class="fas fa-file-csv"></i> Exporter CSV
            </button>
            <button class="btn btn-sm btn-success" onclick="exporterCompetences('pdf')">
                <i class="fas fa-file-pdf"></i> Exporter PDF
            </button>
        </div>
    </div>

    <!-- Section Statistiques -->
    <?php if (isset($stats_section)): ?>
        <?= $stats_section ?>
    <?php endif; ?>

    <!-- Content Row -->
    <?= $tableau ?>

    <!-- Overlay pour afficher les détails d'une compétence -->
    <div id="competenceOverlay" class="competence-overlay">
        <div class="competence-modal">
            <div class="competence-header">
                <h4>Détails de la Compétence</h4>
                <button type="button" class="close-btn" onclick="fermerDetailsCompetence()" >
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="competence-content" id="competenceContent">
                <!-- Le contenu des détails sera chargé ici -->
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Fonction pour afficher les détails d'une compétence
function afficherDetailsCompetence(idCompetence) {
    // Afficher un indicateur de chargement dans le modal
    $('#competenceContent').html(`
        <div class="text-center" style="padding: 50px;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2">Chargement des détails...</p>
        </div>
    `);
    
    // Afficher l'overlay immédiatement
    $('#competenceOverlay').show();
    document.body.style.overflow = 'hidden';
    
    // Charger les détails via AJAX - CETTE ROUTE DOIT CORRESPONDRE À VOTRE ROUTE
    $.ajax({
        url: '/competences/details/' + idCompetence,
        type: 'GET',
        success: function(response) {
            $('#competenceContent').html(response);
            
            // Initialiser le modal Bootstrap s'il est inclus dans la réponse
            if ($('#modalDetailsCompetence').length) {
                $('#modalDetailsCompetence').modal('show');
                
                // Fermer notre overlay personnalisé quand le modal Bootstrap se ferme
                $('#modalDetailsCompetence').on('hidden.bs.modal', function () {
                    fermerDetailsCompetence();
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de charger les détails de la compétence',
                confirmButtonColor: '#e74a3b'
            });
            fermerDetailsCompetence();
        }
    });
}
// Fonction pour fermer l'affichage des détails
function fermerDetailsCompetence() {
    $('#competenceOverlay').hide();
    document.body.style.overflow = '';
}

// Fermer en cliquant en dehors du modal
document.getElementById('competenceOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        fermerDetailsCompetence();
    }
});

// Fermer avec la touche Échap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && $('#competenceOverlay').is(':visible')) {
        fermerDetailsCompetence();
    }
});

// Fonction pour exporter les compétences
function exporterCompetences(format) {
    // Afficher un indicateur de chargement
    Swal.fire({
        title: 'Export en cours...',
        text: 'Préparation du fichier ' + format.toUpperCase(),
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Rediriger vers l'URL d'export
    window.location.href = '/competences/export?format=' + format;
}

// Fonction pour appliquer les filtres
function appliquerFiltresCompetences() {
    const params = {
        search: $('#searchFilter').val(),
        domaine: $('#domaineFilter').val(),
        niveau_min: $('#niveauMinFilter').val(),
        sortField: $('#sortField').val(),
        sortOrder: $('#sortOrder').val()
    };
    
    // Construire l'URL avec les paramètres
    let queryString = Object.keys(params)
        .filter(key => params[key])
        .map(key => key + '=' + encodeURIComponent(params[key]))
        .join('&');
    
    // Recharger la page avec les nouveaux filtres
    window.location.href = '/competences/liste?' + queryString;
}

// Fonction pour changer de page
function changerPage(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('page', page);
    window.location.href = url.toString();
}

// Fonction pour réinitialiser les filtres
function reinitialiserFiltres() {
    window.location.href = '/competences/liste';
}

// Script pour les interactions de la page
$(document).ready(function() {
    // Auto-submit des filtres lorsqu'on change certains éléments
    $('#domaineFilter, #niveauMinFilter, #sortField, #sortOrder').change(function() {
        appliquerFiltresCompetences();
    });

    // Recherche avec délai pour éviter trop de requêtes
    let searchTimer;
    $('#searchFilter').on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            appliquerFiltresCompetences();
        }, 500);
    });

    // Réinitialiser les filtres
    $('#resetFilters').click(reinitialiserFiltres);
});

// Fonction pour exporter une compétence spécifique
function exporterCompetence(idCompetence) {
    Swal.fire({
        title: 'Exporter cette compétence?',
        text: 'Choisissez le format d\'export',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'CSV',
        cancelButtonText: 'PDF',
        showDenyButton: true,
        denyButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/competences/export?format=csv&id=' + idCompetence;
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            window.location.href = '/competences/export?format=pdf&id=' + idCompetence;
        }
    });
}
</script>

<?php
Flight::render("footer", ['extra_js' => '']);
?>