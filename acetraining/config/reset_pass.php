<!--Purpose: Page and logic for handling resetting password-->
<?php
require_once __DIR__ . '../lib/db.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    echo 'invalid or missing token';
    exit();
}

$stmt = $pdo->prepare("SELECT email, expires FROM password_resets WHERE token = :token");
$stmt->execute(['token' => $token]);
$resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resetRequest || strtotime($resetRequest['expires']) < time()) {
    echo 'invalid or expired token';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newpassword = $_POST['new_password'] ?? '';
    $confirmpassword = $_POST['confirm_password'] ?? '';

    if (empty($newpassword) || empty($confirmpassword)) {
        echo 'Please Fill in all fields';
    } elseif ($newpassword !== $confirmpassword) {
        echo 'Password do not match';
    } else {
        $hashedpassword = password_hash($newpassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
        $stmt->execute([
            'password' => $hashedpassword,
            'email' => $resetRequest['email']
        ]);

        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => $token]);

        echo 'password has been reset successfully';
        exit();
    }
}
?>