<?php

namespace app\models;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class AdminModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }

}