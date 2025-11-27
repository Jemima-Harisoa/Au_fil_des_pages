<?php
namespace app\controllers\conge;

use Flight;

class CalendrierController
{

    
    public function index()
    {
        Flight::render('conge/calendrier');
    }
    
    public function getData()
    {
        try {
            $events = [];
            $model = Flight::Calendrier();
            // 1. Récupérer les congés validés via le modèle
            $conges = $model->getConges();
            foreach ($conges as $conge) {
                $events[] = [
                    'id' => 'conge_' . $conge['id_demande'],
                    'title' => '🏖️ Congé - ' . $conge['nom'] . ' ' . $conge['prenom'],
                    'start' => $conge['date_debut'],
                    'end' => $conge['date_fin'],
                    'backgroundColor' => '#4e73df',
                    'borderColor' => '#4e73df',
                    'className' => 'fc-event-conge',
                    'extendedProps' => [
                        'type' => 'conge',
                        'employe_id' => $conge['id_employe'],
                        'type_conge' => $conge['type_conge']
                    ]
                ];
            }
            
            // 2. Récupérer les absences via le modèle
            $model = Flight::Calendrier();
            $absences = $model->getAbsences();
            foreach ($absences as $absence) {
                $events[] = [
                    'id' => 'absence_' . $absence['id_abscence'],
                    'title' => '🤒 Absence - ' . $absence['nom'] . ' ' . $absence['prenom'],
                    'start' => $absence['debut'],
                    'end' => $absence['fin'],
                    'backgroundColor' => '#f6c23e',
                    'borderColor' => '#f6c23e',
                    'className' => 'fc-event-absence',
                    'extendedProps' => [
                        'type' => 'absence',
                        'employe_id' => $absence['id_employe'],
                        'autorise' => $absence['est_autorise']
                    ]
                ];
            }
            
            // 3. Récupérer les jours fériés via le modèle
            $model = Flight::Calendrier();
            $joursFeries = $model->getJoursFeries();
            foreach ($joursFeries as $ferie) {
                $events[] = [
                    'id' => 'ferie_' . $ferie['id_jour_ferie'],
                    'title' => '🎄 Jour férié',
                    'start' => $ferie['date'],
                    'allDay' => true,
                    'backgroundColor' => '#e74a3b',
                    'borderColor' => '#e74a3b',
                    'className' => 'fc-event-ferie',
                    'extendedProps' => [
                        'type' => 'ferie'
                    ]
                ];
            }
            
            Flight::json($events);
            
        } catch (\Exception $e) {
            Flight::json([
                'error' => true,
                'message' => 'Erreur lors du chargement des données : ' . $e->getMessage()
            ], 500);
        }
    }
}