<?php
// test_config.php
require_once 'config.php';

echo "<h1>Testing Config.php</h1>";

echo "<h3>Config Values:</h3>";
echo "DB_NAME: " . Config::DB_NAME() . "<br>";
echo "DB_HOST: " . Config::DB_HOST() . "<br>";
echo "DB_USER: " . Config::DB_USER() . "<br>";
echo "DB_PASSWORD: " . (Config::DB_PASSWORD() ? "SET" : "EMPTY") . "<br>";
echo "JWT_SECRET: " . Config::JWT_SECRET() . "<br>";

echo "<h3>Database Test:</h3>";
if (class_exists('Database')) {
    echo "Database class: <span style='color:green'>EXISTS</span><br>";
    
    try {
        $db = Database::connect();
        echo "Connection: <span style='color:green'>SUCCESS</span><br>";
        
        // Test query
        $stmt = $db->query("SELECT DATABASE() as db");
        $result = $stmt->fetch();
        echo "Current database: " . $result['db'] . "<br>";
    } catch (Exception $e) {
        echo "Connection: <span style='color:red'>FAILED - " . $e->getMessage() . "</span><br>";
    }
} else {
    echo "Database class: <span style='color:red'>NOT FOUND</span><br>";
}
?>