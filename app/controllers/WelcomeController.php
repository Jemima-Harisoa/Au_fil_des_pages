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
        Flight::render('accueilA');
    }
    public function AppelAccueilU() {
        Flight::render('accueilU');
    }
}

?>