<?php
// config.php - KOMPLETNO ISPRAVLJEN

// Set the reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));

class Config
{
    public static function DB_NAME()
    {
        return 'my_film_library'; // PROMENJENO iz 'database_name'
    }
    
    public static function DB_PORT()
    {
        return 3306;
    }
    
    public static function DB_USER()
    {
        return 'root';
    }
    
    public static function DB_PASSWORD()  // OVO POSTOJI - DOBRO JE!
    {
        return '';
    }
    
    public static function DB_HOST()
    {
        return '127.0.0.1';
    }

    public static function JWT_SECRET() {
        return 'myfilmlibrary123';
    }
    
    public static function BASE_URL() {
        return 'http://localhost/my-film-library';
    }
}

// ========== DATABASE KLASA ==========
class Database {
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . Config::DB_HOST() . ";port=" . Config::DB_PORT() . ";dbname=" . Config::DB_NAME(),
                    Config::DB_USER(),
                    Config::DB_PASSWORD(),  // OVO JE ISPRAVNO!
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
                echo "<!-- Database connected successfully -->";
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
    
    // Dodatna metoda za test
    public static function testConnection() {
        try {
            $conn = self::connect();
            $stmt = $conn->query("SELECT 1 as test");
            $result = $stmt->fetch();
            return $result['test'] == 1;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
