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

if (isset($_SESSION['infoAdmin'])) {
    // Rediriger vers une page d'erreur ou de connexion
    Flight::render("headerA", ['extra_css' => $extra_css]);
}
else if (isset($_SESSION['employe'])) {
    // Rediriger vers une page d'erreur ou de connexion
    Flight::render("headerE", ['extra_css' => $extra_css]);
}
else {
    // Rediriger vers une page d'erreur ou de connexion
    Flight::render("headerU");

}
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
console.log("=== SCRIPT CHARGÉ ===");

// TEST DE DÉBOGAGE - À ajouter immédiatement
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM chargé - Recherche des boutons notifier...");
    
    // Vérifier si les boutons existent
    const boutons = document.querySelectorAll('.notifier-absence');
    console.log(`Nombre de boutons notifier trouvés: ${boutons.length}`);
    
    boutons.forEach((bouton, index) => {
        console.log(`Bouton ${index}:`, bouton);
        console.log(`Data absence-id: ${bouton.dataset.absenceId}`);
        
        // Ajouter un événement simple pour tester
        bouton.addEventListener('click', function(e) {
            e.preventDefault();
            console.log("CLICK DIRECT - Bouton cliqué!", this.dataset.absenceId);
            alert("Test click - ID: " + this.dataset.absenceId);
        });
    });
});
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

// Fonction pour notifier un employé d'une absence non justifiée
function notifierAbsence(idAbsence) {
    console.log('notifierAbsence appelée avec ID:', idAbsence);
    
    // Confirmation avant d'envoyer
    if (!confirm('Voulez-vous envoyer une notification à cet employé pour justifier son absence ?')) {
        return;
    }
    
    // Afficher un indicateur de chargement
    Swal.fire({
        title: 'Envoi en cours...',
        text: 'Veuillez patienter',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Envoyer la requête
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
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data && data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: data.message || 'Notification envoyée avec succès',
                confirmButtonColor: '#4e73df'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: data.error || 'Erreur lors de l\'envoi de la notification',
                confirmButtonColor: '#e74a3b'
            });
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Erreur de connexion: ' + error.message,
            confirmButtonColor: '#e74a3b'
        });
    });
}
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

<script>
// ============================
// FILTRAGE TABLEAU ABSENCES UNIFIÉ
// ============================

class UnifiedAbsenceFilter {
    constructor(tableId) {
        this.table = document.getElementById(tableId);
        if (!this.table) {
            console.error(`Table avec l'ID "${tableId}" non trouvée`);
            return;
        }
        
        // Mappage des colonnes avec les filtres
        this.columnMap = {
            'employeFilter': 0,      // Employé
            'posteFilter': 1,        // Poste
            'periodeFilter': 2,      // Période
            'joursPrisFilter': 3,    // Jours pris
            'descriptionFilter': 4,   // Description
            'justificatifFilter': 5,  // Justification
            'penaliteFilter': 6      // Pénalité
        };
        
        this.init();
        this.setupEventListeners();
    }
    
    init() {
        // Ajouter le CSS pour les lignes masquées
        this.addStyles();
        
        // Créer l'affichage du statut des filtres
        this.createFilterStatus();
    }
    
    addStyles() {
        if (!document.querySelector('#absence-filter-styles')) {
            const style = document.createElement('style');
            style.id = 'absence-filter-styles';
            style.textContent = `
                .hidden-row {
                    display: none;
                }
                .filter-status {
                    font-size: 0.875rem;
                    color: #6c757d;
                    margin-top: 0.5rem;
                    padding: 5px 10px;
                    background: #f8f9fc;
                    border-radius: 4px;
                    display: inline-block;
                }
                .filters-container .row {
                    margin-bottom: 10px;
                }
                .filter-active {
                    border-color: #4e73df !important;
                    background-color: #f8f9fc;
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    createFilterStatus() {
        // Créer l'élément de statut s'il n'existe pas
        let statusDiv = this.table.parentNode.querySelector('.filter-status');
        if (!statusDiv) {
            statusDiv = document.createElement('div');
            statusDiv.className = 'filter-status';
            this.table.parentNode.insertBefore(statusDiv, this.table.nextSibling);
        }
        this.updateStatus();
    }
    
    setupEventListeners() {
        // Écouter les événements sur tous les filtres
        for (const filterId in this.columnMap) {
            const filterElement = document.getElementById(filterId);
            if (filterElement) {
                if (filterElement.type === 'text' || filterElement.tagName === 'INPUT') {
                    filterElement.addEventListener('input', () => this.applyFilters());
                } else if (filterElement.tagName === 'SELECT') {
                    filterElement.addEventListener('change', () => this.applyFilters());
                }
                
                // Ajouter une classe quand le filtre est actif
                filterElement.addEventListener('input', () => this.updateFilterState(filterElement));
                filterElement.addEventListener('change', () => this.updateFilterState(filterElement));
            }
        }
        
        // Bouton de réinitialisation
        const resetBtn = document.getElementById('resetFilters');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => this.resetFilters());
        }
    }
    
    updateFilterState(element) {
        if (element.value && element.value !== '') {
            element.classList.add('filter-active');
        } else {
            element.classList.remove('filter-active');
        }
    }
    
    applyFilters() {
        const tbody = this.table.querySelector('tbody');
        if (!tbody) return;
        
        const rows = tbody.querySelectorAll('tr');
        let anyFilterActive = false;
        
        // Récupérer toutes les valeurs de filtres
        const filterValues = {};
        for (const filterId in this.columnMap) {
            const element = document.getElementById(filterId);
            if (element) {
                filterValues[filterId] = element.value;
                if (element.value && element.value !== '') {
                    anyFilterActive = true;
                }
            }
        }
        
        rows.forEach(row => {
            let showRow = true;
            const cells = row.querySelectorAll('td');
            
            // Vérifier chaque filtre
            for (const filterId in this.columnMap) {
                if (!showRow) break; // Si la ligne est déjà masquée, on arrête
                
                const filterValue = filterValues[filterId];
                const columnIndex = this.columnMap[filterId];
                
                if (filterValue && filterValue !== '' && cells[columnIndex]) {
                    const cellValue = cells[columnIndex].textContent.toLowerCase().trim();
                    const filterLower = filterValue.toLowerCase();
                    
                    // Traitement spécial pour les jours pris
                    if (filterId === 'joursPrisFilter') {
                        const jours = parseInt(cellValue) || 0;
                        switch(filterValue) {
                            case '1':
                                if (jours !== 1) showRow = false;
                                break;
                            case '2-4':
                                if (jours < 2 || jours > 4) showRow = false;
                                break;
                            case '5+':
                                if (jours < 5) showRow = false;
                                break;
                        }
                    }
                    // Traitement pour les sélecteurs (valeur exacte)
                    else if (filterId === 'justificatifFilter' || filterId === 'penaliteFilter') {
                        if (cellValue !== filterLower) {
                            showRow = false;
                        }
                    }
                    // Traitement pour les champs texte (recherche partielle)
                    else {
                        if (!cellValue.includes(filterLower)) {
                            showRow = false;
                        }
                    }
                }
            }
            
            if (showRow) {
                row.classList.remove('hidden-row');
            } else {
                row.classList.add('hidden-row');
            }
        });
        
        this.updateStatus();
    }
    
    updateStatus() {
        const tbody = this.table.querySelector('tbody');
        if (!tbody) return;
        
        const visibleRows = tbody.querySelectorAll('tr:not(.hidden-row)').length;
        const totalRows = tbody.querySelectorAll('tr').length;
        
        let statusDiv = this.table.parentNode.querySelector('.filter-status');
        if (statusDiv) {
            if (visibleRows === totalRows) {
                statusDiv.textContent = `${totalRows} lignes affichées`;
            } else {
                statusDiv.textContent = `${visibleRows} sur ${totalRows} lignes affichées (${Math.round((visibleRows/totalRows)*100)}%)`;
            }
        }
    }
    
    resetFilters() {
        // Réinitialiser tous les champs de filtre
        for (const filterId in this.columnMap) {
            const element = document.getElementById(filterId);
            if (element) {
                if (element.type === 'text' || element.tagName === 'INPUT') {
                    element.value = '';
                } else if (element.tagName === 'SELECT') {
                    element.selectedIndex = 0;
                }
                element.classList.remove('filter-active');
            }
        }
        
        // Afficher toutes les lignes
        const tbody = this.table.querySelector('tbody');
        if (tbody) {
            tbody.querySelectorAll('tr').forEach(row => {
                row.classList.remove('hidden-row');
            });
        }
        
        this.updateStatus();
        
        // Afficher un message temporaire
        this.showResetMessage();
    }
    
    showResetMessage() {
        // Créer un message temporaire
        const message = document.createElement('div');
        message.className = 'alert alert-info alert-dismissible fade show';
        message.style.position = 'fixed';
        message.style.top = '20px';
        message.style.right = '20px';
        message.style.zIndex = '10000';
        message.innerHTML = `
            Filtres réinitialisés
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        
        document.body.appendChild(message);
        
        // Supprimer automatiquement après 3 secondes
        setTimeout(() => {
            if (message.parentNode) {
                message.remove();
            }
        }, 3000);
    }
    
    // Méthode pour obtenir les filtres actifs
    getActiveFilters() {
        const active = [];
        for (const filterId in this.columnMap) {
            const element = document.getElementById(filterId);
            if (element && element.value && element.value !== '') {
                active.push({
                    id: filterId,
                    value: element.value,
                    column: this.columnMap[filterId]
                });
            }
        }
        return active;
    }
}

// ============================
// FILTRAGE DÉTAIL ABSENCES
// ============================

class DetailAbsenceFilter {
    constructor(tableId) {
        this.table = document.getElementById(tableId);
        if (!this.table) return;
        
        this.columnMap = {
            'detail_periodeFilter': 0,
            'detail_joursPrisFilter': 1,
            'detail_descriptionFilter': 2,
            'detail_justificatifFilter': 3,
            'detail_penaliteFilter': 4
        };
        
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        // Filtre période
        const periodeFilter = document.getElementById('detail_periodeFilter') || document.querySelector('[name="periodeFilter"]');
        if (periodeFilter) {
            periodeFilter.addEventListener('input', () => this.applyFilters());
        }
        
        // Filtre jours pris
        const joursPrisFilter = document.getElementById('detail_joursPrisFilter') || document.querySelector('[name="joursPrisFilter"]');
        if (joursPrisFilter) {
            joursPrisFilter.addEventListener('change', () => this.applyFilters());
        }
        
        // Filtre description
        const descriptionFilter = document.getElementById('detail_descriptionFilter') || document.querySelector('[name="descriptionFilter"]');
        if (descriptionFilter) {
            descriptionFilter.addEventListener('input', () => this.applyFilters());
        }
        
        // Filtre justification
        const justificatifFilter = document.getElementById('detail_justificatifFilter') || document.querySelector('[name="justificatifFilter"]');
        if (justificatifFilter) {
            justificatifFilter.addEventListener('change', () => this.applyFilters());
        }
        
        // Filtre pénalité
        const penaliteFilter = document.getElementById('detail_penaliteFilter') || document.querySelector('[name="penaliteFilter"]');
        if (penaliteFilter) {
            penaliteFilter.addEventListener('change', () => this.applyFilters());
        }
        
        // Bouton réinitialiser
        const resetBtn = document.getElementById('detail_resetFilters') || document.querySelector('[name="resetFilters"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => this.resetFilters());
        }
    }
    
    applyFilters() {
        const tbody = this.table.querySelector('tbody');
        if (!tbody) return;
        
        const rows = tbody.querySelectorAll('tr');
        
        // Récupérer les valeurs des filtres
        const periodeValue = this.getFilterValue('periodeFilter')?.toLowerCase() || '';
        const joursPrisValue = this.getFilterValue('joursPrisFilter') || '';
        const descriptionValue = this.getFilterValue('descriptionFilter')?.toLowerCase() || '';
        const justificatifValue = this.getFilterValue('justificatifFilter') || '';
        const penaliteValue = this.getFilterValue('penaliteFilter') || '';
        
        rows.forEach(row => {
            if (row.cells.length < 5) return;
            
            const periode = row.cells[0].textContent.toLowerCase();
            const joursPris = parseInt(row.cells[1].textContent) || 0;
            const description = row.cells[2].textContent.toLowerCase();
            const justificatif = row.cells[3].textContent;
            const penalite = row.cells[4].textContent;
            
            let showRow = true;
            
            // Filtre période
            if (periodeValue && !periode.includes(periodeValue)) {
                showRow = false;
            }
            
            // Filtre jours pris
            if (joursPrisValue === '1' && joursPris !== 1) {
                showRow = false;
            } else if (joursPrisValue === '2-4' && (joursPris < 2 || joursPris > 4)) {
                showRow = false;
            } else if (joursPrisValue === '5+' && joursPris < 5) {
                showRow = false;
            }
            
            // Filtre description
            if (descriptionValue && !description.includes(descriptionValue)) {
                showRow = false;
            }
            
            // Filtre justification
            if (justificatifValue && justificatif !== justificatifValue) {
                showRow = false;
            }
            
            // Filtre pénalité
            if (penaliteValue && penalite !== penaliteValue) {
                showRow = false;
            }
            
            if (showRow) {
                row.classList.remove('hidden-row');
            } else {
                row.classList.add('hidden-row');
            }
        });
        
        this.updateStatus();
    }
    
    getFilterValue(filterName) {
        const element = document.getElementById(`detail_${filterName}`) || 
                       document.querySelector(`[name="${filterName}"]`) ||
                       document.getElementById(filterName);
        return element ? element.value : '';
    }
    
    resetFilters() {
        // Réinitialiser tous les champs
        const filters = ['periodeFilter', 'joursPrisFilter', 'descriptionFilter', 'justificatifFilter', 'penaliteFilter'];
        
        filters.forEach(filterName => {
            const element = document.getElementById(`detail_${filterName}`) || 
                           document.querySelector(`[name="${filterName}"]`) ||
                           document.getElementById(filterName);
            if (element) {
                if (element.type === 'select-one') {
                    element.selectedIndex = 0;
                } else {
                    element.value = '';
                }
            }
        });
        
        // Afficher toutes les lignes
        const tbody = this.table.querySelector('tbody');
        if (tbody) {
            tbody.querySelectorAll('tr').forEach(row => {
                row.classList.remove('hidden-row');
            });
        }
        
        this.updateStatus();
    }
    
    updateStatus() {
        const tbody = this.table.querySelector('tbody');
        if (!tbody) return;
        
        const visibleRows = tbody.querySelectorAll('tr:not(.hidden-row)').length;
        const totalRows = tbody.querySelectorAll('tr').length;
        
        let statusDiv = this.table.parentNode.querySelector('.filter-status');
        if (!statusDiv) {
            statusDiv = document.createElement('div');
            statusDiv.className = 'filter-status mt-2 text-muted small';
            this.table.parentNode.insertBefore(statusDiv, this.table.nextSibling);
        }
        
        statusDiv.textContent = `${visibleRows} sur ${totalRows} lignes affichées`;
    }
}

// ============================
// INITIALISATION
// ============================

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le filtre pour la table principale des absences
    const mainTable = document.getElementById('listeAbsencesTable');
    if (mainTable) {
        window.absenceFilter = new UnifiedAbsenceFilter('listeAbsencesTable');
        console.log('Filtre principal initialisé');
    }
    
    // Initialiser le filtre pour le détail des absences
    const detailTable = document.getElementById('detailAbsencesTable');
    if (detailTable) {
        window.detailAbsenceFilter = new DetailAbsenceFilter('detailAbsencesTable');
        console.log('Filtre détail initialisé');
    }
    
    // Supprimer les anciens écouteurs jQuery pour éviter les conflits
    const oldScript = document.querySelector('script[src*="jquery"]');
    if (oldScript) {
        console.log('jQuery détecté, suppression des anciens écouteurs');
        // Vous pouvez ajouter ici la suppression des écouteurs jQuery si nécessaire
    }
});

// Fonctions utilitaires
function getFilteredRowsCount(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return 0;
    
    const tbody = table.querySelector('tbody');
    if (!tbody) return 0;
    
    return tbody.querySelectorAll('tr:not(.hidden-row)').length;
}

function getTotalRowsCount(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return 0;
    
    const tbody = table.querySelector('tbody');
    if (!tbody) return 0;
    
    return tbody.querySelectorAll('tr').length;
}
</script>