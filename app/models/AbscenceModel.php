<?php 

namespace app\models\conge;

class AbscenceModel extends Query
{
    public static function get($idCong_employe,$date){

    }
    public function getAbscenceByIdEmployeAndDate($id_employe,$date){
        try{
            $sql = "SELECT * FROM conge_historique zh";
        }       
    }
}