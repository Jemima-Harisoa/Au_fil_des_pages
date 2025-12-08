<?php
namespace app\controllers;
use app\models\AdminModel;


use Flight;

class WelcomeController {
    public function home() {
        Flight::render('index');
    }
    public function AppelAccueilG() {
        Flight::render('accueilG');
    }
    public function AppelAccueilA() {
        Flight::render("headerA");
        Flight::render("accueilA");
        Flight::render("footer");
    }
    public function AppelAccueilU() {
        Flight::render("headerU");
        Flight::render("accueilU");
        Flight::render("footer");
    }
    public function AppelAccueilE() {
        Flight::render("headerE");
        Flight::render("accueilE");
        Flight::render("footer");
    }

}

?>