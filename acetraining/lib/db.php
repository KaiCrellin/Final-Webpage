<!--Purpose: Connection to the database, establishing PDO options for database manipulation-->
<?php
require_once __DIR__ . '/env.php';

try {
    // Load environment variables
    loadEnv(__DIR__ . '/../.env');

    $host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');
    $charset = getenv('DB_CHARSET');

    if (!$host || !$dbname || !$username) {
        throw new Exception("Database configuration is incomplete");
    }

    // Establish PDO connection
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    throw new Exception("Database connection failed");
} catch (Exception $e) {
    error_log("Configuration error: " . $e->getMessage());
    throw new Exception("Database configuration error");
}
?>