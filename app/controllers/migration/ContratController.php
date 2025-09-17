<?php
namespace app\controllers\migration;
use app\models\migration\ContratModel;


use Flight;

class ContratController {
    public function RedactionContrat() {
        Flight::render('migration/contrat');
    }
}

?>