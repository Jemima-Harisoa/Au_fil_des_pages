<?php
namespace app\controllers\migration;


use Flight;

class ValidationController {
    public function RedactionValidation() {
        Flight::render('migration/Validation');
    }
}

?>