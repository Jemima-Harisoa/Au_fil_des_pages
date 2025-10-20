<?php

use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
use Tracy\Debugger;

use app\models\ProductModel;
use app\models\InscriptionModel;
use app\models\ConnexionModel;
use app\models\AdminModel;
use app\models\ResponsableEntretienModel;
use app\models\DisponibiliteEntretienModel;
use app\models\ConfigEntretienModel;
use app\models\CandidatModel;
use app\models\TestModel;
use app\models\EtatModel;
use app\models\ProfilsModel;
use app\models\PlanningEntretienModel;

use app\models\migration\PersonneModel;
use app\models\migration\ScoringModel;
use app\models\migration\TypeContratModel;
use app\models\migration\ContratModel;
use app\models\EmployeModel;
use app\models\DepartementModel;
use app\models\migration\HistoriqueValidationModel;
use app\models\migration\HistoriqueContratModel;


use app\models\HistoriquePlanningEntretienModel;

/** 
 * @var array $config This comes from the returned array at the bottom of the config.php file
 * @var Engine $app
 */

// uncomment the following line for MySQL
//  $dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';

// uncomment the following line for SQLite
// $dsn = 'sqlite:' . $config['database']['file_path'];

// uncomment the following line for PostgreSQL
$dsn = 'pgsql:host=' . $config['database']['host'] . ';port=' . $config['database']['port'] . ';dbname=' . $config['database']['dbname'];

// uncomment the following line for psql

// Uncomment the below lines if you want to add a Flight::db() service
// In development, you'll want the class that captures the queries for you. In production, not so much.
$pdoClass = Debugger::$showBar === true ? PdoQueryCapture::class : PdoWrapper::class;

$app->register('db', $pdoClass, [ $dsn, $config['database']['user'] ?? null, $config['database']['password'] ?? null ]);

// Got google oauth stuff? You could register that here
// $app->register('google_oauth', Google_Client::class, [ $config['google_oauth'] ]);

// Redis? This is where you'd set that up
// $app->register('redis', Redis::class, [ $config['redis']['host'], $config['redis']['port'] ]);



//
Flight::map('responsableEntretienModel', function () {
    return new ResponsableEntretienModel(Flight::db());
});
//
Flight::map('disponibiliteEntretienModel', function () {
    return new DisponibiliteEntretienModel(Flight::db());
});
Flight::map('configEntretienModel', function () {
    return new ConfigEntretienModel(Flight::db());
});

Flight::map('candidatModel', function () {
    return new CandidatModel(Flight::db());
});
//
Flight::map('testModel', function () {
  return new TestModel(Flight::db());
});

Flight::map('profilsModel', function () {
  return new ProfilsModel(Flight::db());
});

Flight::map('planningEntretienModel', function () {
  return new PlanningEntretienModel(Flight::db());
});
//Flight::map('ConnexionModel', function () {
//    return new ConnexionModel(Flight::db());
//});
//
//Flight::map('AdminModel', function () {
//    return new AdminModel(Flight::db());
//});

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

Flight::map('Etat', function () {
    return new EtatModel(Flight::db());
});

Flight::map('HistoriqueValidation', function () {
    return new HistoriqueValidationModel(Flight::db());
});

Flight::map("Profils", function(){
    return new ProfilsModel(Flight::db());
});

Flight::map("HistoriqueContrat", function(){
    return new HistoriqueContratModel(Flight::db());
});

Flight::map('ConnexionModel', function () {
    return new connexionModel(Flight::db());
});

Flight::map('adminModel', function () {
    return new AdminModel(Flight::db());
});

Flight::map('Employe', function () {
    return new EmployeModel(Flight::db());
});

Flight::map('Departement', function () {
    return new DepartementModel(Flight::db());
});
