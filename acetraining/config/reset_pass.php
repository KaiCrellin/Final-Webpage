<!--Purpose: Page and logic for handling resetting password-->
<?php
require_once __DIR__ . '/../lib/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $newpassword = $_POST['new_password'] ?? '';
    $confirmpassword = $_POST['confirm_password'] ?? '';

    if (empty($email) || empty($newpassword) || empty($confirmpassword)) {
        echo 'Please fill in all fields';
    } elseif ($newpassword !== $confirmpassword) {
        echo 'Passwords do not match';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $hashedpassword = password_hash($newpassword, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmt->execute([
                'password' => $hashedpassword,
                'email' => $email
            ]);

            if ($stmt->rowCount() === 1) {
                echo 'Password changed successfully';
                header('Location: /acetraining/pages/showlogin.php');
            } else {
                echo 'Failed to change password';
            }
        } else {
            echo 'Email not found';
        }
    }
    exit();
}
?>