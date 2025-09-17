<?php
namespace app\controllers;
use app\models\AdminModel;


use Flight;

class WelcomeController {
    public function home() {
        Flight::render('index');
    }
}

?>