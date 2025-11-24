<?php

$extra_css = '
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .table-actions {
        white-space: nowrap;
    }
    .btn-notifier {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        border: none;
        color: white;
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

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function notifierEmploye(idEmploye, idAbsence) {
    Swal.fire({
        title: 'Notifier l\'employé',
        text: "Voulez-vous envoyer une notification à l'employé pour justifier son absence ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f6c23e',
        cancelButtonColor: '#d1d3e2',
        confirmButtonText: 'Oui, notifier',
        cancelButtonText: 'Annuler',
        input: 'textarea',
        inputLabel: 'Message personnalisé (optionnel)',
        inputPlaceholder: 'Veuillez justifier votre absence dans les plus brefs délais.',
        inputAttributes: {
            'aria-label': 'Message personnalisé'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Envoyer la notification
            $.ajax({
                url: '/abscence/notifier',
                type: 'POST',
                data: {
                    id_employe: idEmploye,
                    id_abscence: idAbsence,
                    message: result.value
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.message,
                            confirmButtonColor: '#1cc88a'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: response.error,
                            confirmButtonColor: '#e74a3b'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Erreur lors de l\'envoi de la notification',
                        confirmButtonColor: '#e74a3b'
                    });
                }
            });
        }
    });
}

// Script pour les filtres par colonne
$(document).ready(function() {
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