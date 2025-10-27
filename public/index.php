<?php
declare(strict_types=1);

/*
 * FlightPHP Framework
 * @copyright   Copyright (c) 2024, Mike Cao <mike@mikecao.com>, n0nag0n <n0nag0n@sky-9.com>
 * @license     MIT, http://flightphp.com/license
                                                                  .____   __ _
     __o__   _______ _ _  _                                     /     /
     \    ~\                                                  /      /
       \     '\                                         ..../      .'
        . ' ' . ~\                                      ' /       /
       .  _    .  ~ \  .+~\~ ~ ' ' " " ' ' ~ - - - - - -''_      /
      .  <#  .  - - -/' . ' \  __                          '~ - \
       .. -           ~-.._ / |__|  ( )  ( )  ( )  0  o    _ _    ~ .
     .-'                                               .- ~    '-.    -.
    <                      . ~ ' ' .             . - ~             ~ -.__~_. _ _
      ~- .       N121PP  .          . . . . ,- ~
            ' ~ - - - - =.   <#>    .         \.._
                        .     ~      ____ _ .. ..  .- .
                         .         '        ~ -.        ~ -.
                           ' . . '               ~ - .       ~-.
                                                       ~ - .      ~ .
                                                              ~ -...0..~. ____
   Cessna 402  (Wings)
   by Dick Williams, rjw1@tyrell.net
*/

$ds = DIRECTORY_SEPARATOR;

// $root = dossier racine du projet (ex: /home/.../Au_fil_des_pages)
$root = dirname(__DIR__);

// Inclure l'autoload de Composer UNE SEULE FOIS (assurez-vous d'avoir exécuté `composer install`)
$autoload = $root . $ds . 'vendor' . $ds . 'autoload.php';
if (!file_exists($autoload)) {
    // Message d'erreur explicite si vendor/autoload.php est manquant
    header('Content-Type: text/plain; charset=utf-8', true, 500);
    echo "Composer autoload introuvable. Exécutez 'composer install' à la racine du projet.\nSearched path: $autoload";
    exit(1);
}
require_once $autoload;

// Charger le bootstrap/app initialization (chemin existant dans ton projet)
require $root . $ds . 'app' . $ds . 'config' . $ds . 'bootstrap.php';

// --- démarrage de l'application ---
// Si ton bootstrap configure déjà Flight et lance Flight::start(), ne répète pas.
// Sinon, décommente la ligne suivante pour démarrer Flight :
// Flight::start();