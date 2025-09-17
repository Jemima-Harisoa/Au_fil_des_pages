<?php
namespace app\controllers;
use app\models\AdminModel;

use Flight;

class AnnoncesController {
    public function appelCreateAnnonce() {
        Flight::render('publierAnnonces');
    }
}

?>