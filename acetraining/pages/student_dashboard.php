<?php
session_start();
include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';
$csrf_token = $_SESSION['csrf_token'];

if (!isset($_SESSION['user_id'])) {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}
$user_id = $_SESSION['user_id'];
try {
    global $pdo;

    $stmt = $pdo->prepare("SELECT name FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
$name = $user['name'];




?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="student_dash_head">
        <h1>Student Dashboard</h1>
        <h2>Welcome to your dashboard, <?php echo htmlspecialchars($name) ?></h2>
    </div>
    <div class="student_description">
        <h3>information</h3>
        <p>Here you can view your information.</p>
        <p2>Within your dashboard you can:</p2>
        <div class="information_table">
            <ul>
                <li>Download Resources</li>
                <li>View your course information</li>
                <li>Submit assignments and quizzes</li>
                <li>Check your timetable</li>
                <li>Check your calendar</li>
                <li>Check your profile</li>
            </ul>
        </div>
    </div>

</body>

</html>