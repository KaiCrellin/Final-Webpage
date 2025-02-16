<!--Purpose: logic to check if db connection succ and if tables created-->
<?php
require_once __DIR__ . '/../lib/db.php';

try {
    // Check database connection
    $pdo->query("SELECT 1");
    echo "Database connection successful.<br>";

    // List of required tables
    $requiredTables = [
        'users',
        'students',
        'tutors',
        'admins',
        'courses',
        'students_courses'
        ,
        'calendar',
        'students_courses',
        'students_courses',
        'students_courses'
    ];

    // Check if required tables exist
    foreach ($requiredTables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table' ");
        if ($stmt->rowCount() == 0) {
            echo "Table '$table' does not exist.<br>";
        } else {
            echo "Table '$table' exists.<br>";
        }
    }

    echo "Database check completed.";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>