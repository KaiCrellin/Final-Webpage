<?php
session_start();

include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}
$user_id = $_SESSION['user_id'];

try {
    global $pdo;

    $stmt = $pdo->prepare("SELECT u.name FROM users u WHERE u.id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}


if (!$user) {
    echo "user not found";
    exit();
}

if ($user) {
    try {
        $role = '';

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        if ($stmt->fetchColumn() > 0) {
            $role = 'Admin';
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tutors WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        if ($stmt->fetchColumn() > 0) {
            $role = 'Tutor';
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        if ($stmt->fetchColumn() > 0) {
            $role = 'Student';
        }

        $user['role'] = $role;

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

switch ($user['role']) {
    case 'Student':
        $stmt = $pdo->prepare("SELECT c.name AS title, c.description FROM courses c JOIN students s ON c.student_id = s.id WHERE s.user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 'Tutor':
        $stmt = $pdo->prepare("SELECT c.name AS title, c.description FROM courses c WHERE c.tutor_id = :id");
        $stmt->execute(['id' => $user_id]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;
    default:
        echo "Invalid role";
        exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    <link rel="stylesheet" href="/acetraining/assets/css/courses.css">
</head>

<body>
    <h1>Available Courses</h1>
    <?php if ($user['role'] === 'Tutor'): ?>
        <button id="showFormButton" onclick="showCourseCreate()">Create Course</button>
        <div id="assignmentForm" style="display: none;">
            <form action="create_assignment.php" method="post">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required><br><br>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea><br><br>
                <label for="file"></label>
                <input type="file" id="file" name="file"><br><br>
                <button type="submit">Create Course Block</button>
            </form>
        </div>
    <?php endif; ?>
    <div id="courses-container">
        <?php
        foreach ($courses as $course) {
            echo '<div class="course-block">';
            echo '<div class="course-header">';
            echo '<div class="course-title">' . htmlspecialchars($course['title']) . '</div>';
            echo '<button class="block-dropdown-button" onclick="toggleCourseContent(this)">☰</button>';
            echo '</div>';
            echo '<div class="course-content hidden">';
            echo '<div class="course-description">' . htmlspecialchars($course['description']) . '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
    <script src="/acetraining/assets/js/main.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            showCourseCreate();
        });
    </script>
</body>

</html>