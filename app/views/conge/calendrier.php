<?php

// Générer les années pour le filtre (5 ans avant et après l'année actuelle)
$currentYear = date('Y');
$years = [];
for ($i = $currentYear - 2; $i <= $currentYear + 3; $i++) {
    $years[] = $i;
}

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

<div class="container-fluid">
    
    <!-- En-tête de la page avec filtre -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-alt"></i> Calendrier des Congés et Absences
        </h1>
        <div class="d-flex align-items-center">
            <!-- Filtre par année -->
            <div class="mr-3">
                <label for="yearFilter" class="mr-2"><strong>Année:</strong></label>
                <select id="yearFilter" class="form-control form-control-sm">
                    <?php foreach ($years as $year): ?>
                        <option value="<?= $year ?>" <?= $year == $currentYear ? 'selected' : '' ?>>
                            <?= $year ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <a href="/conge/demande" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle demande
                </a>
                <a href="/conge/liste_absence" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Liste des absences
                </a>
            </div>
        </div>
    </div>
    
    <!-- Légende -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3 mb-2">
                    <span class="badge badge-primary p-2">■</span> Congés validés
                </div>
                <div class="col-md-3 mb-2">
                    <span class="badge badge-warning p-2">■</span> Absences
                </div>
                <div class="col-md-3 mb-2">
                    <span class="badge badge-danger p-2">■</span> Jours fériés
                </div>
                <div class="col-md-3 mb-2">
                    <small class="text-muted">Cliquez sur un événement pour plus de détails</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Calendrier -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div id='calendar'></div>
        </div>
    </div>
    
</div>

<!-- Modal pour les détails -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Détails de l'événement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Contenu dynamique -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<?php Flight::render('footer'); ?>

<!-- FullCalendar CSS depuis CDN -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />

<style>
    #calendar {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
    }
    
    .fc-event-conge { 
        background-color: #4e73df !important; 
        border-color: #4e73df !important; 
        color: white !important;
    }
    
    .fc-event-absence { 
        background-color: #f6c23e !important; 
        border-color: #f6c23e !important; 
        color: black !important;
    }
    
    .fc-event-ferie { 
        background-color: #e74a3b !important; 
        border-color: #e74a3b !important; 
        color: white !important;
    }
    
    .fc-toolbar {
        flex-wrap: wrap;
    }
    
    /* Style pour le filtre année */
    #yearFilter {
        width: 120px;
        display: inline-block;
    }
</style>

<!-- FullCalendar JS depuis CDN -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var yearFilter = document.getElementById('yearFilter');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        firstDay: 1, // Lundi comme premier jour de la semaine
        
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,dayGridDay'
        },
        
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour'
        },
        
        // Fonction pour charger les événements avec le filtre année
        events: function(fetchInfo, successCallback, failureCallback) {
            var selectedYear = yearFilter.value;
            
            fetch('/conge/calendrier/data?year=' + selectedYear)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des événements:', error);
                    failureCallback(error);
                });
        },
        
        // Quand on clique sur un événement
        eventClick: function(info) {
            var event = info.event;
            var props = event.extendedProps;
            
            var modalBody = document.getElementById('eventModalBody');
            var modalTitle = document.getElementById('eventModalLabel');
            
            modalTitle.textContent = event.title;
            
            var content = `
                <div class="mb-3">
                    <strong>Type:</strong> ${props.type}<br>
                    <strong>Du:</strong> ${event.start ? event.start.toLocaleDateString('fr-FR') : ''}<br>
                    <strong>Au:</strong> ${event.end ? new Date(event.end.getTime() - 24 * 60 * 60 * 1000).toLocaleDateString('fr-FR') : ''}
                </div>
            `;
            
            if (props.description) {
                content += `<div><strong>Description:</strong> ${props.description}</div>`;
            }
            
            if (props.employe_id) {
                content += `<div><strong>Employé ID:</strong> ${props.employe_id}</div>`;
            }
            
            modalBody.innerHTML = content;
            $('#eventModal').modal('show');
            
            info.jsEvent.preventDefault();
        },
        
        // Personnalisation de l'affichage des événements
        eventDisplay: 'block',
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        }
    });
    
    // Événement quand on change l'année
    yearFilter.addEventListener('change', function() {
        calendar.refetchEvents(); // Recharge tous les événements
    });
    
    calendar.render();
});
</script>