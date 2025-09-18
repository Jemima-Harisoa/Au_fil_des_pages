<?php
namespace app\controllers;
use app\models\AdminModel;

use Flight;

class AnnoncesController {
    public function form() {
        Flight::render('formAnnonces');
    }
}

?>