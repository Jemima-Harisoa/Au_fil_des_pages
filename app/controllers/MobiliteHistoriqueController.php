<?php 
namespace app\controllers;
use app\models\MobiliteHistoriqueModel;

use Flight;

class MobiliteHistoriqueController {
    
    public function redirectEmploye() {
        $data = MobiliteHistoriqueModel::getAllMobiliteHistorique();
        Flight::render('mobiliteHistorique/mobiliteHistoriqueList', ['data' => $data]);
    }  
}