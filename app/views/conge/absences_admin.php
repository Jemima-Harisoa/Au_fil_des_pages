<?php
// liste_absence.php
$extra_css = '
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .absence-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border-left: 4px solid #e74a3b;
    }
    
    .absence-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    .btn-justifier {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-justifier:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
    }
    
    .penalite-badge {
        background-color: #e74a3b;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
    }
    
    .filter-section {
        background-color: #f8f9fc;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        border-left: 4px solid #4e73df;
    }
    
    .filter-row {
        margin-bottom: 10px;
    }
    
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
    }
    
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
    }

    .justificatif-overlay {
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

    .justificatif-modal {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        max-width: 1000px;
        height: 85vh;
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

    .justificatif-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-bottom: 1px solid #e3e6f0;
    }

    .justificatif-header h4 {
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

    .justificatif-content {
        flex: 1;
        overflow-y: auto;
        padding: 0;
        background: #f8f9fc;
    }

    /* Adaptation du contenu du justificatif pour le modal */
    .justificatif-content .container {
        max-width: 100%;
        margin: 0;
        padding: 25px;
        background: white;
        min-height: 100%;
    }

    .justificatif-content .header {
        border-bottom: 2px solid #4e73df;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }

    .justificatif-content .files {
        margin-top: 20px;
    }

    .justificatif-content .file-preview {
        max-height: 400px;
        overflow: auto;
    }

    .justificatif-content .file-preview iframe {
        height: 500px;
    }

    .justificatif-content .actions {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 15px 0;
        border-top: 1px solid #e3e6f0;
        margin-top: 20px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .justificatif-modal {
            width: 95%;
            height: 90vh;
            border-radius: 8px;
        }
        
        .justificatif-header {
            padding: 15px 20px;
        }
        
        .justificatif-content .container {
            padding: 20px;
        }
    }
</style>
';

Flight::render("headerA", ['extra_css' => $extra_css]);
?>


<!-- Begin Page Content -->
<div class="container-fluid">
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Absences</h1>
    </div>
    <!-- Content Row -->
    <?= $tableau ?>

    <!-- Overlay pour afficher le justificatif -->
    <div id="justificatifOverlay" class="justificatif-overlay">
        <div class="justificatif-modal">
            <div class="justificatif-header">
                <h4>Justificatif d'absence</h4>
                <button type="button" class="close-btn" onclick="fermerJustificatif()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="justificatif-content" id="justificatifContent">
                <!-- Le contenu du justificatif sera chargé ici -->
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function afficherFormulaireJustification(idAbsence) {
    // Charger le formulaire via AJAX
    $.ajax({
        url: '/absence/justificatif/view/' + idAbsence,
        type: 'GET',
        success: function(response) {
            $('#justificatifContent').html(response);
            $('#justificatifOverlay').show();
            document.body.style.overflow = 'hidden'; // Empêche le défilement de la page principale
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de charger le formulaire de justification',
                confirmButtonColor: '#e74a3b'
            });
        }
    });
}

// Nouvelle fonction pour afficher le justificatif via AJAX
function afficherJustificatif(idAbsence) {
    // Afficher un indicateur de chargement dans le modal
    $('#justificatifContent').html(`
        <div class="text-center" style="padding: 50px;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2">Chargement du justificatif...</p>
        </div>
    `);
    
    // Afficher l'overlay immédiatement
    $('#justificatifOverlay').show();
    document.body.style.overflow = 'hidden'; // Empêche le défilement de la page principale
    
    // Charger le justificatif via AJAX
    $.ajax({
        url: '/absence/justificatif/view/' + idAbsence,
        type: 'GET',
        success: function(response) {
            $('#justificatifContent').html(response);
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de charger le justificatif',
                confirmButtonColor: '#e74a3b'
            });
            fermerJustificatif();
        }
    });
}

// Fonction pour fermer l'affichage du justificatif
function fermerJustificatif() {
    $('#justificatifOverlay').hide();
    document.body.style.overflow = ''; // Rétablit le défilement
}

// Fermer en cliquant en dehors du modal
document.getElementById('justificatifOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        fermerJustificatif();
    }
});

// Fermer avec la touche Échap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && $('#justificatifOverlay').is(':visible')) {
        fermerJustificatif();
    }
});

// Gestion des notifications d'absence
$(document).on('click', '.notifier-absence', function(e) {
    e.preventDefault();
    const idAbsence = $(this).data('absence-id');
    const bouton = $(this);
    
    console.log('Click notifier-absence', {
        idAbsence: idAbsence,
        bouton: bouton,
        dataAttributes: $(this).data()
    });

    if (!idAbsence) {
        console.error('idAbsence manquant dans data-absence-id');
        afficherMessage('error', 'ID d\'absence manquant');
        return;
    }

    bouton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Envoi...');

    // Utilisez la même URL que dans vos routes
    fetch('/absence/notifier/' + idAbsence, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.json().catch(() => {
            return response.text().then(text => {
                throw new Error('Réponse non JSON: ' + text);
            });
        });
    })
    .then(data => {
        console.log('Response data:', data);
        if (data && data.success) {
            afficherMessage('success', data.message || 'Notification envoyée avec succès');
            bouton.html('<i class="fas fa-check"></i> Notifié')
                  .removeClass('btn-warning')
                  .addClass('btn-success')
                  .prop('disabled', true);
        } else {
            afficherMessage('error', data?.error || 'Erreur inconnue du serveur');
            bouton.prop('disabled', false)
                  .html('<i class="fas fa-bell"></i> Notifier');
        }
    })
    .catch(error => {
        console.error('Erreur fetch:', error);
        afficherMessage('error', 'Erreur: ' + error.message);
        bouton.prop('disabled', false)
              .html('<i class="fas fa-bell"></i> Notifier');
    });
});

// Fonction pour afficher les messages
function afficherMessage(type, message) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="close" data-dismiss="alert">' +
                    '<span>&times;</span></button></div>';
    
    // Ajouter le message en haut de la page
    $('.container-fluid').prepend(alertHtml);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
}

// Notification de tous les employés (bouton optionnel)
$('#notifier-tous').click(function() {
    if (confirm('Voulez-vous notifier tous les employés ayant des absences non justifiées ?')) {
        $.get('/absence/notifier/tous')
            .done(function(response) {
                if (response.success) {
                    afficherMessage('success', response.message);
                } else {
                    afficherMessage('error', response.error);
                }
            })
            .fail(function() {
                afficherMessage('error', 'Erreur lors de l\'envoi des notifications');
            });
    }
});
// Script pour les filtres par colonne
$(document).ready(function() {
    // Vos filtres existants...
    // Filtre pour la colonne Employé
    $('#employeFilter').on('keyup', function() {
        var valeur = this.value.toLowerCase();
        $('#listeAbsencesTable tbody tr').filter(function() {
            $(this).toggle($(this).find('td:eq(0)').text().toLowerCase().indexOf(valeur) > -1);
        });
    });

    // Filtre pour la colonne Poste
    $('#posteFilter').on('keyup', function() {
        var valeur = this.value.toLowerCase();
        $('#listeAbsencesTable tbody tr').filter(function() {
            $(this).toggle($(this).find('td:eq(1)').text().toLowerCase().indexOf(valeur) > -1);
        });
    });

    // Filtre pour la colonne Période
    $('#periodeFilter').on('keyup', function() {
        var valeur = this.value.toLowerCase();
        $('#listeAbsencesTable tbody tr').filter(function() {
            $(this).toggle($(this).find('td:eq(2)').text().toLowerCase().indexOf(valeur) > -1);
        });
    });

    // Filtre pour la colonne Jours pris
    $('#joursPrisFilter').on('change', function() {
        var valeur = this.value;
        $('#listeAbsencesTable tbody tr').filter(function() {
            var jours = parseInt($(this).find('td:eq(3)').text());
            if (valeur === '1') {
                return jours === 1;
            } else if (valeur === '2-4') {
                return jours >= 2 && jours <= 4;
            } else if (valeur === '5+') {
                return jours >= 5;
            }
            return true;
        }).toggle(true);
    });

    // Filtre pour la colonne Description
    $('#descriptionFilter').on('keyup', function() {
        var valeur = this.value.toLowerCase();
        $('#listeAbsencesTable tbody tr').filter(function() {
            $(this).toggle($(this).find('td:eq(4)').text().toLowerCase().indexOf(valeur) > -1);
        });
    });

    // Filtre pour la colonne Justification
    $('#justificatifFilter').on('change', function() {
        var valeur = this.value;
        $('#listeAbsencesTable tbody tr').filter(function() {
            var justificatif = $(this).find('td:eq(5)').text();
            if (valeur === '') return true;
            return justificatif.indexOf(valeur) > -1;
        }).toggle(true);
    });

    // Filtre pour la colonne Pénalité
    $('#penaliteFilter').on('change', function() {
        var valeur = this.value;
        $('#listeAbsencesTable tbody tr').filter(function() {
            var penalite = $(this).find('td:eq(6)').text();
            if (valeur === '') return true;
            return penalite.indexOf(valeur) > -1;
        }).toggle(true);
    });

    // Réinitialiser les filtres
    $('#resetFilters').on('click', function() {
        $('input[type="text"]').val('');
        $('select').val('');
        $('#listeAbsencesTable tbody tr').show();
    });
});
</script>

<?php
Flight::render("footer", ['extra_js' => '']);
?>