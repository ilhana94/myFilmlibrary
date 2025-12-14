<?php
// C:\xampp\htdocs\my-film-library\config.php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'my_film_library');
define('DB_USER', 'root');
define('DB_PASS', '');

// JWT Secret key
define('JWT_SECRET', 'your_super_secret_jwt_key_change_this_in_production');

// Site URL
define('BASE_URL', 'http://localhost/my-film-library');

// Timezone
date_default_timezone_set('Europe/Belgrade');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database class (ako ne postoji drugde)
class Database {
    public static function connect() {
        try {
            $conn = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            return $conn;
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}

// Session start (za web login ako koristiš sesije)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>