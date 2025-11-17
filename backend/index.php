<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

// sve klase, PAKET rute
require_once __DIR__ . '/rest/dao/BaseDao.php';
require_once __DIR__ . '/rest/services/BaseServices.php';

// Ostale DAO
require_once __DIR__ . '/rest/dao/UsersDao.php';
require_once __DIR__ . '/rest/dao/CategoriesDao.php';
require_once __DIR__ . '/rest/dao/MoviesDao.php';
require_once __DIR__ . '/rest/dao/ReviewsDao.php';
require_once __DIR__ . '/rest/dao/MovieCategoriesDao.php';

// service
require_once __DIR__ . '/rest/services/UserServices.php';
require_once __DIR__ . '/rest/services/CategoriesService.php';
require_once __DIR__ . '/rest/services/MoviesServices.php';
require_once __DIR__ . '/rest/services/ReviewsServices.php';
require_once __DIR__ . '/rest/services/MovieCategoriesServices.php';

// ucitaj rute
require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/CategoriesRoutes.php';
require_once __DIR__ . '/rest/routes/MovieRoutes.php';
require_once __DIR__ . '/rest/routes/MovieCategoriesRoutes.php';
require_once __DIR__ . '/rest/routes/ReviewsRoutes.php';

Flight::start();