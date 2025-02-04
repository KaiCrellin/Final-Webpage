<?php
session_start();
require_once __DIR__ . '/../lib/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
        header('Location: ../pages/showlogin.php?error=' . urlencode($error));
        exit;
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];

               
                $user_id = $user['id'];

                
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                if ($stmt->fetchColumn() > 0) {
                    $_SESSION['role'] = 'admin';
                    header('Location: ../pages//admin_dashboard.php');
                    exit;
                }

                
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM tutors WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                if ($stmt->fetchColumn() > 0) {
                    $_SESSION['role'] = 'tutor';
                    header('Location: ../pages/tutor_dashboard.php');
                    exit;
                }

                
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                if ($stmt->fetchColumn() > 0) {
                    $_SESSION['role'] = 'student';
                    header('Location: ../pages/student_dashboard.php');
                    exit;
                }

                $error = 'Invalid user role.';
                header('Location: ../pages/showlogin.php?error=' . urlencode($error));
                exit;
            } else {
                $error = 'Invalid email or password.';
                header('Location: ../pages/showlogin.php?error=' . urlencode($error));
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
            header('Location: ../pages/showlogin.php?error=' . urlencode($error));
            exit;
        }
    }
} else {
    header('Location: ../pages/showlogin.php');
    exit;
}
?>