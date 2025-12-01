<?php
namespace app\controllers;
use Flight;
use app\models\EmployeModel;

class DocumentsController {
    
    public function __construct() {
        // Initialisation
    }
    
    public function redirectDocuments() {
        $data[0] = EmployeModel::list();
        $data[1] = EmployeModel::getJoinedEmployePersonnes();
        Flight::render('Documents/documentsOption', ['data' => $data]);
    }
}
