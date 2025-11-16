<?php 
namespace app\controllers;
use app\models\EmployeModel;

use Flight;

class EmployeController {
    public function redirectEmploye() {
        $data = EmployeModel::listeEmployerV2();
        Flight::render('employe/employeList', ['data' => $data]);
    }   
    public function redirectEmployeDetails($id) {
        $data = EmployeModel::getEmployesWithDetails($id);
        Flight::render('employe/employeDetails', ['data' => $data]);
    }   
}