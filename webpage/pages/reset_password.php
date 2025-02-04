<?php
require_once '../lib/db.php';

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

    if(empty($newpassword) || empty($confirmpassword)) { 
        echo 'Please Fill in all fields';
    } elseif ($newpassword !== $confirmpassword) {
        echo 'Password do not match';
    } else {
        $hashedpassword = password_hash($newpassword,PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
        $stmt->execute([
            'password' => $hashedpassword,
            'email' => $resetRequest['email']
        ]);

        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => $token]);

        echo  'password has been reset successfully';
        exit();
    }
} 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            background: #007BFF;
            border: none;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <form action="" method="POST">
        <label for="new_password"> New Password</label>
        <input type="password" name="new_password" required>
        <br>
        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" required>
        <br>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>