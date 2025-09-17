<?php

namespace app\models\migration;

use Flight;
use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

class ContartModel {
    protected $db;

    public function __construct() {
        $this->db = Flight::db();
    }

}