<?php
session_start();

header("Content_Type: application/json");


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
    exit();
}

$csrf_token = $_POST['csrf_token'] ?? '';
$email =  $_POST['email'] ?? '';

if (empty($csrf_token) || $csrf_token !== $_SESSION['csrf_token']) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid CSRF Token']);
    exit();
}
if (empty($email)) {
    http_response_code(400);
    echo json_encode(['message' => 'Email is requried']);
    exit();

}

$user = getUserByEmail($email);
if (!$user) {
    http_response_code(404);
    echo json_encode(['message' => 'User Not Found']);
    exit();
}

$resetToken = bin2hex(random_bytes(16));
$resetTokenExpiry = date('Y-m-d H:i:s' , strtotime('+1 hour'));

saveResetToken($email,$resetToken, $resetTokenExpiry);

sendResetEmail($email, $resetToken);

echo json_encode(['message' => 'Password reset email sent']);

function getUserByEmail($email) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT email FROM students WHERE email = :email");
        $stmt->execute(['email'=> $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $user : false;
}   

function saveResetToken($email,$resetToken, $resetTokenExpiry) {
    global $pdo;
    if (!getUserByEmail($email)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid Email']);
        exit();
    }  else {
        $stmt = $pdo->prepare("UPDATE password_resets SET token = :token, expires = :expires WHERE email = :email");
        $stmt->execute([
            'token' => $resetToken,
            'expires' => $resetTokenExpiry,
            'email' => $email
        ]);
        return $stmt;
    }  
}

function sendResetEmail($email,$resetToken) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = :email");
        $stmt->execute(['email' => 'email']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $resetlink = "http://localhost/reset_password.php?token=$resetToken";
            $subject = "password reset";
            $message = "Click the following link to reset your password, $resetlink";
            $headers = 'FROM: no-reply@yourdomain.com' . "\r\n" . 
                        'Reply-To: no-reply@yourdomain.com' . "\r\n" . 
                        'X-Mailer: PHP/' . phpversion();


            if (mail($email,$subject,$message,$headers)) {
                echo 'Password reset link sent to your email address';
            } else {
                echo 'Failed to send email';
            } 
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid Email Address']);
        }
    } catch (PDOException $e) {
        echo 'Database Error:' . $e->getMessage();
    }
}
?>