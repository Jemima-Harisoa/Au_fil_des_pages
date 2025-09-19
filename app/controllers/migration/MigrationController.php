<?php
namespace app\controllers\migration;
use app\models\migration\ContratModel;


use Flight;

class MigrationController {
    public function getCandidatRetenu() {
        Flight::render('migration/contrat');
    }
}

?>