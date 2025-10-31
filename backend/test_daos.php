<?php
require_once 'dao/UsersDao.php';
require_once 'dao/MoviesDao.php';
require_once 'dao/CategoriesDao.php';
require_once 'dao/MovieCategoriesDao.php';
require_once 'dao/ReviewsDao.php';

// Inicijalizacija DAO objekata
$usersDao = new UsersDao();
$moviesDao = new MoviesDao();
$categoriesDao = new CategoriesDao();
$movieCategoriesDao = new MovieCategoriesDao();
$reviewsDao = new ReviewsDao();

// Dohvati korisnika po emailu
echo "<h3>Korisnik sa emailom john@gmail.com</h3>";
$user = $usersDao->getByEmail('john@gmail.com');
print_r($user);
echo "<br><br>";

// Dohvati sve korisnike
echo "<h3>Svi korisnici:</h3>";
$allUsers = $usersDao->getAll();
print_r($allUsers);
echo "<br><br>";

// Dohvati sve filmove
echo "<h3>Svi filmovi:</h3>";
$allMovies = $moviesDao->getAll();
print_r($allMovies);
echo "<br><br>";

// Dohvati filmove sa kategorijama
echo "<h3>Filmovi sa kategorijama:</h3><pre>";
$moviesWithCategories = $moviesDao->getMoviesWithCategories();
print_r($moviesWithCategories);
echo "</pre>";

// Dohvati sve recenzije za film (npr. ID = 1)
echo "<h3>Recenzije za film ID 1:</h3><pre>";
$movieReviews = $reviewsDao->getReviewsByMovieId(1);
print_r($movieReviews);
echo "</pre>";

// Dohvati prosječnu ocjenu filma
echo "<h3>Prosječna ocjena za film ID 1:</h3>";
$avg = $reviewsDao->getAverageRating(1);
print_r($avg);
?>
