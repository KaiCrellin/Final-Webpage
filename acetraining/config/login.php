<!--Purpose: Logic for handling log in-->
<?php
session_start();
require_once __DIR__ . '/../lib/db.php';

//  CSRF check
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = trim($_POST['email']);
        $submitted_password = $_POST['password'];

        // Basic validation
        if (empty($email) || empty($submitted_password)) {
            throw new Exception('Please enter both email and password');
        }

        // Find user by email
        $stmt = $pdo->prepare("
            SELECT 
                u.id,
                u.name,
                u.email,
                u.password,
                CASE 
                    WHEN t.id IS NOT NULL THEN 'tutor'
                    WHEN s.id IS NOT NULL THEN 'student'
                END as role
            FROM users u
            LEFT JOIN tutors t ON u.id = t.user_id
            LEFT JOIN students s ON u.id = s.user_id
            WHERE u.email = ?
        ");

        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug
        error_log("Login attempt for: " . $email);
        error_log("User found: " . ($user ? "Yes" : "No"));

        // Check credentials
        if ($user && password_verify($submitted_password, $user['password'])) {
            // Update last_logged_in timestamp
            $update_stmt = $pdo->prepare("
                UPDATE users 
                SET last_logged_in = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            $update_stmt->execute([$user['id']]);

            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];

            // Redirect based on role
            if ($user['role'] === 'tutor') {
                header('Location: /acetraining/pages/tutor_dashboard.php');
            } else {
                header('Location: /acetraining/pages/student_dashboard.php');
            }
            exit();
        }

        // Invalid login
        throw new Exception('Invalid email or password');

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        header('Location: /acetraining/pages/showlogin.php?error=' . urlencode($e->getMessage()));
        exit();
    }
}

// If not POST or failed, redirect to login
header('Location: /acetraining/pages/showlogin.php');
exit();
?>