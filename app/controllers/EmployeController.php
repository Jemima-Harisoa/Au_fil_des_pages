<?php 
namespace app\controllers;
use app\models\EmployeModel;

use Flight;

class EmployeController {
    public function redirectEmploye() {
        $data = EmployeModel::getAllEmployesWithDetails();
        Flight::render('employe/employeList', ['data' => $data]);
    }   
    public function redirectEmployeDetails() {
        Flight::render('employe/employeDetails');
    }   


}