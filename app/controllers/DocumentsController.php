<?php
namespace app\controllers;
use Flight;
use app\models\EmployeModel;

class DocumentsController {
    
    public function __construct() {
        // Initialisation
    }
    
    public function redirectDocuments() {
        $data = EmployeModel::list();
        Flight::render('Documents/documentsOption', ['data' => $data]);
    }
}
