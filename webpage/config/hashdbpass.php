<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../lib/db.php';

try {
    global $pdo;

    $stmt = $pdo->query("SELECT id, password FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        $user_id = $user['id'];
        $plain_password = $user['password'];

        // Check if the password is already hashed
        if (password_get_info($plain_password)['algo'] !== 0) {
            // Hash the password
            $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $update_stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update_stmt->execute(['password' => $hashed_password, 'id' => $user_id]);

            echo "Password for user ID $user_id has been hashed and updated.<br>";
            echo "Hashed Password: " . htmlspecialchars($hashed_password) . "<br>";
        } else {
            echo "Password for user ID $user_id is already hashed.<br>";
            echo "Hashed Password: " . htmlspecialchars($plain_password) . "<br>";
        }
    }

    echo "All passwords have been hashed and updated successfully.";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
exit();
?>