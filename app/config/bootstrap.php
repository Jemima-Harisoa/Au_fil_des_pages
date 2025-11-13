<?php
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;

$ds = DIRECTORY_SEPARATOR;
require(__DIR__ . $ds . '..' . $ds . '..' . $ds . 'vendor' . $ds . 'autoload.php');

if(file_exists(__DIR__. $ds . 'config.php') === false) {
    Flight::halt(500, 'Config file not found. Please create a config.php file in the app/config directory to get started.');
}

$app = Flight::app();

// Charger la config
$config = require('config.php');

// Router
$router = $app->router();
require('routes.php');

// Services
require('services.php');

/*
 * Construction du DSN PostgreSQL à partir de la config
 */
if (($config['database']['driver'] ?? null) === 'pgsql') {
    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s",
        $config['database']['host'],
        $config['database']['port'] ?? 5432,
        $config['database']['dbname']
    );
} else {
    Flight::halt(500, 'Unsupported database driver in config.php');
}

/*
 * Enregistrement du service DB
 */
Flight::register('db', PdoWrapper::class, [
    $dsn,
    $config['database']['user'] ?? null,
    $config['database']['password'] ?? null,
]);

$app->start();
