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
    <title>Tutor Dashboard</title>
    <link rel="stylesheet" href="/acetraining/assets/css/tutor.css">
</head>

<body>
    <div class="tutor_dash_head">
        <h1>Tutor Dashboard</h1>
        <h2>Welcome To your dashboard, <?php echo htmlspecialchars($name) ?> </h2>
    </div>
    <div class="tutor_description">#
        <h3>Information</h3>
        <p>Here you can view your courses, students and assignments</p>
        <p2>Within your dashboard you can; Create new assignments, Quizzes,Create new blocks for course
            information,adding
            and deleting information,adding and removing students from courses, View student information inside courses.
            For instance, You can check participants of a specific course.
        </p2>
    </div>

</body>

</html>
<?php include __DIR__ . '/../components/footer.php'; ?>