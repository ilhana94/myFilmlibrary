<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/lib/flight/Flight.php';

// Učitaj sve rute
require_once __DIR__ . '/rest/routes/CategoriesRoutes.php';
require_once __DIR__ . '/rest/routes/MovieRoutes.php';
require_once __DIR__ . '/rest/routes/MovieCategoriesRoutes.php';
require_once __DIR__ . '/rest/routes/ReviewsRoutes.php';
require_once __DIR__ . '/rest/routes/UserRoutes.php';

// Test ruta
Flight::route('/', function() {
    echo 'Flight framework radi!';
});

Flight::start();
