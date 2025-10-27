<?php
namespace app\controllers;
use app\models\DateModel;
use Flight;
class SessionController {
    public  function checkSessionAdmin(){
        if(!isset($_SESSION["admin"]["id_admin"])){
            Flight::render('connexionA',null);
            return;
        }
    }
}
?>