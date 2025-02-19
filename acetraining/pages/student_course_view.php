<?php
session_start();
include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';

// Verify student access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}

$course_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$error_message = null;

try {
    // Verify student is enrolled in this course
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            u.name as tutor_name,
            u.email as tutor_email
        FROM courses c
        JOIN tutors t ON c.tutor_id = t.id
        JOIN users u ON t.user_id = u.id
        JOIN course_enrollments ce ON c.id = ce.course_id
        JOIN students s ON ce.student_id = s.id
        WHERE c.id = :course_id 
        AND s.user_id = :user_id
    ");

    $stmt->execute([
        'course_id' => $course_id,
        'user_id' => $_SESSION['user_id']
    ]);

    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        throw new Exception('Course not found or not enrolled');
    }

} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course: <?php echo htmlspecialchars($course['name'] ?? ''); ?></title>
    <link rel="stylesheet" href="/acetraining/assets/css/global.css">
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
</head>

<body>
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Course Details</h1>
            <a href="/acetraining/pages/student_dashboard.php" class="dashboard-button">Back to Dashboard</a>
        </div>

        <?php if ($error_message): ?>
            <div class="dashboard-alert error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php else: ?>
            <div class="dashboard-card">
                <h2 class="card-title"><?php echo htmlspecialchars($course['name']); ?></h2>
                <div class="course-details">
                    <div class="detail-row">
                        <strong>Description:</strong>
                        <p><?php echo htmlspecialchars($course['description']); ?></p>
                    </div>
                    <div class="detail-row">
                        <strong>Tutor:</strong>
                        <p><?php echo htmlspecialchars($course['tutor_name']); ?></p>
                        <p><?php echo htmlspecialchars($course['tutor_email']); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
<?php include __DIR__ . '/../components/footer.php'; ?>