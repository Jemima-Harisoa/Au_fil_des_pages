<?php

use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
use Tracy\Debugger;
use app\models\migration\PersonneModel;
use app\models\migration\CandidatModel;
use app\models\migration\ScoringModel;
use app\models\migration\TypeContratModel;
use app\models\migration\ContratModel;
/** 
 * @var array $config This comes from the returned array at the bottom of the config.php file
 * @var Engine $app
 */

// uncomment the following line for MySQL
 $dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';

// uncomment the following line for SQLite
// $dsn = 'sqlite:' . $config['database']['file_path'];

// Uncomment the below lines if you want to add a Flight::db() service
// In development, you'll want the class that captures the queries for you. In production, not so much.
$pdoClass = Debugger::$showBar === true ? PdoQueryCapture::class : PdoWrapper::class;

$app->register('db', $pdoClass, [ $dsn, $config['database']['user'] ?? null, $config['database']['password'] ?? null ]);

// Got google oauth stuff? You could register that here
// $app->register('google_oauth', Google_Client::class, [ $config['google_oauth'] ]);

// Redis? This is where you'd set that up
// $app->register('redis', Redis::class, [ $config['redis']['host'], $config['redis']['port'] ]);


// Map dans features migration
Flight::map('Personne', function () {
    return new PersonneModel(Flight::db());
});

Flight::map('Candidat', function () {
    return new CandidatModel(Flight::db());
});

Flight::map('Scoring', function () {
    return new ScoringModel(Flight::db());
});

Flight::map('TypeContrat', function () {
    return new TypeContratModel(Flight::db());
});

Flight::map('Contrat', function () {
    return new ContratModel(Flight::db());
});
