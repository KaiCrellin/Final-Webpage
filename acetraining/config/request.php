<?php
session_start();
require_once __DIR__ . '/../lib/db.php';

$email = $_POST['email'] ?? '';

if (!empty($email)) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        header("Location: /acetraining/pages/reset.php");
        exit();
    } else {
        echo "Email not found.";
    }
} else {
    echo "Email is required.";
}
?>