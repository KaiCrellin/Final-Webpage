<?php
session_start();
include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';

// Verify student access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}

$assignment_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$error_message = null;

try {
    // Get assignment details for enrolled student
    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            c.name as course_name,
            c.id as course_id,
            u.name as tutor_name,
            u.email as tutor_email
        FROM assignments a
        JOIN courses c ON a.course_id = c.id
        JOIN tutors t ON c.tutor_id = t.id
        JOIN users u ON t.user_id = u.id
        JOIN course_enrollments ce ON c.id = ce.course_id
        JOIN students s ON ce.student_id = s.id
        WHERE a.id = :assignment_id 
        AND s.user_id = :user_id
    ");

    $stmt->execute([
        'assignment_id' => $assignment_id,
        'user_id' => $_SESSION['user_id']
    ]);

    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$assignment) {
        throw new Exception('Assignment not found or not enrolled in course');
    }

    // Calculate time remaining
    $due_date = new DateTime($assignment['due_date']);
    $now = new DateTime();
    $time_remaining = $now < $due_date ? $now->diff($due_date) : null;

} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment: <?php echo htmlspecialchars($assignment['name'] ?? ''); ?></title>
    <link rel="stylesheet" href="/acetraining/assets/css/global.css">
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
</head>

<body>
    <!-- Assignment details -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Assignment Details</h1>
            <a href="/acetraining/pages/student_dashboard.php" class="dashboard-button">Back to Dashboard</a>
        </div>

        <?php if ($error_message): ?>
            <div class="dashboard-alert error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php else: ?>
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h2 class="card-title"><?php echo htmlspecialchars($assignment['name']); ?></h2>
                    <div class="course-details">
                        <div class="detail-row">
                            <strong>Course:</strong>
                            <p><?php echo htmlspecialchars($assignment['course_name']); ?></p>
                        </div>
                        <div class="detail-row">
                            <strong>Description:</strong>
                            <p><?php echo htmlspecialchars($assignment['description']); ?></p>
                        </div>
                        <div class="detail-row">
                            <strong>Due Date:</strong>
                            <p><?php echo date('l, F j, Y \a\t g:i A', strtotime($assignment['due_date'])); ?></p>
                        </div>
                        <?php if ($time_remaining && $time_remaining->invert === 0): ?>
                            <div class="detail-row">
                                <strong>Time Remaining:</strong>
                                <p class="time-remaining">
                                    <?php
                                    if ($time_remaining->days > 0) {
                                        echo $time_remaining->days . ' days, ';
                                    }
                                    echo $time_remaining->h . ' hours, ' . $time_remaining->i . ' minutes';
                                    ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="detail-row">
                                <p class="status-past-due">Assignment Past Due</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Course Information -->
                <div class="dashboard-card">
                    <h2 class="card-title">Course Information</h2>
                    <div class="course-details">
                        <div class="detail-row">
                            <strong>Tutor:</strong>
                            <p><?php echo htmlspecialchars($assignment['tutor_name']); ?></p>
                            <p><?php echo htmlspecialchars($assignment['tutor_email']); ?></p>
                        </div>
                        <div class="detail-row">
                            <a href="/acetraining/pages/student_course_view.php?id=<?php echo $assignment['course_id']; ?>"
                                class="dashboard-button">View Course</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
<?php include __DIR__ . '/../components/footer.php'; ?>