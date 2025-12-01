<?php 
namespace app\controllers;
use app\models\documentsModels;

use Flight;

class documentsController {
    
    public function redirectDocuments() {
        Flight::render('Documents/documentsOption');
    }  
}