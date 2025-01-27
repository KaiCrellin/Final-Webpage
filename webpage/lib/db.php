//This file conntaines the connection to the database and PDO Exception handling
<?php
require_once 'config/config.php';


try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => "Database connection failed" . $e->getMessage()]);
    exit;
}
?>