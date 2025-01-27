// Purpose: Logic for logging in a user. can be moved to "api.php" later to configure handling of requests
// and responses to the database

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo "Invalid CSRF Token";
        exit();
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $pdo = new PDO('mysql:host=localhost;dbname=my_database', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare('SELECT id, name, password, role FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header('Location: ../pages/admin_dashboard.php');
            } elseif ($user['role'] == 'tutor') {
                header('Location: ../pages/tutor_dashboard.php');
            } else {
                header('Location: ../pages/student_dashboard.php');
            }
            exit();
        } else {
            $error = "Invalid email or password";
            header('Location: ../pages/showlogin.php?error=' . urlencode($error));
            exit();
        }
    } catch (PDOException $e) {
        echo 'Database error: ' . $e->getMessage();
        exit();
    }
} else {
    http_response_code(405);
    header('Location: ../pages/login.php');
    exit();
}
?>