<?php
session_start();
include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';

// Verify tutor access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}

$quiz_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$error_message = null;
$success_message = null;

try {
    // Get quiz details
    $stmt = $pdo->prepare("
        SELECT 
            q.*,
            c.name as course_name,
            COUNT(DISTINCT ce.student_id) as enrolled_students
        FROM quizzes q
        JOIN courses c ON q.course_id = c.id
        JOIN tutors t ON c.tutor_id = t.id
        LEFT JOIN course_enrollments ce ON c.id = ce.course_id
        WHERE q.id = :quiz_id 
        AND t.user_id = :user_id
        GROUP BY q.id
    ");

    $stmt->execute([
        'quiz_id' => $quiz_id,
        'user_id' => $_SESSION['user_id']
    ]);

    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) {
        throw new Exception('Quiz not found or access denied');
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $quiz_name = trim($_POST['quiz_name'] ?? '');
        $quiz_description = trim($_POST['quiz_description'] ?? '');
        $quiz_date = $_POST['quiz_date'] ?? '';
        $quiz_time = $_POST['quiz_time'] ?? '';
        $duration = filter_input(INPUT_POST, 'duration', FILTER_SANITIZE_NUMBER_INT);

        if (
            empty($quiz_name) || empty($quiz_description) || empty($quiz_date) ||
            empty($quiz_time) || empty($duration)
        ) {
            throw new Exception('All fields are required');
        }

        // Update quiz
        $update_stmt = $pdo->prepare("
            UPDATE quizzes 
            SET quiz_name = :quiz_name,
                quiz_description = :quiz_description,
                quiz_date = :quiz_date,
                quiz_time = :quiz_time,
                duration = :duration
            WHERE id = :id
        ");

        $update_stmt->execute([
            'quiz_name' => $quiz_name,
            'quiz_description' => $quiz_description,
            'quiz_date' => $quiz_date,
            'quiz_time' => $quiz_time,
            'duration' => $duration,
            'id' => $quiz_id
        ]);

        $success_message = 'Quiz updated successfully';

        // Refresh quiz data
        $quiz['quiz_name'] = $quiz_name;
        $quiz['quiz_description'] = $quiz_description;
        $quiz['quiz_date'] = $quiz_date;
        $quiz['quiz_time'] = $quiz_time;
        $quiz['duration'] = $duration;
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
    <title>Quiz Details - <?php echo htmlspecialchars($quiz['quiz_name'] ?? ''); ?></title>
    <link rel="stylesheet" href="/acetraining/assets/css/global.css">
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
    <link rel="stylesheet" href="/acetraining/assets/css/forms.css">
</head>

<body>
    <!-- Quiz details -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Quiz Details</h1>
            <a href="/acetraining/pages/course_details.php?id=<?php echo $quiz['course_id']; ?>"
                class="dashboard-button">Back to Course</a>
        </div>

        <?php if ($error_message): ?>
            <div class="dashboard-alert error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="dashboard-alert success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <!-- Quiz details form -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2 class="card-title">Edit Quiz</h2>
                <form method="POST" class="edit-form">
                    <div class="form-group">
                        <label for="course">Course</label>
                        <input type="text" id="course" value="<?php echo htmlspecialchars($quiz['course_name']); ?>"
                            class="form-input" readonly>
                    </div>

                    <div class="form-group">
                        <label for="quiz_name">Quiz Name</label>
                        <input type="text" id="quiz_name" name="quiz_name" class="form-input" required
                            value="<?php echo htmlspecialchars($quiz['quiz_name']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="quiz_description">Description</label>
                        <textarea id="quiz_description" name="quiz_description" rows="4" class="form-input"
                            required><?php echo htmlspecialchars($quiz['quiz_description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="quiz_date">Quiz Date</label>
                        <input type="date" id="quiz_date" name="quiz_date" class="form-input" required
                            value="<?php echo date('Y-m-d', strtotime($quiz['quiz_date'])); ?>">
                    </div>

                    <div class="form-group">
                        <label for="quiz_time">Start Time</label>
                        <input type="time" id="quiz_time" name="quiz_time" class="form-input" required
                            value="<?php echo date('H:i', strtotime($quiz['quiz_time'])); ?>">
                    </div>

                    <div class="form-group">
                        <label for="duration">Duration (minutes)</label>
                        <input type="number" id="duration" name="duration" min="1" max="180" class="form-input" required
                            value="<?php echo htmlspecialchars($quiz['duration']); ?>">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="dashboard-button">Save Changes</button>
                    </div>
                </form>
            </div>
            <!-- Quiz information -->
            <div class="dashboard-card">
                <h2 class="card-title">Quiz Information</h2>
                <div class="detail-row">
                    <strong>Created:</strong>
                    <?php echo date('M d, Y H:i', strtotime($quiz['created_at'] ?? 'now')); ?>
                </div>
                <div class="detail-row">
                    <strong>Students in Course:</strong>
                    <?php echo $quiz['enrolled_students']; ?>
                </div>
                <div class="form-actions">
                    <form method="POST" action="/acetraining/handlers/delete_quiz.php"
                        onsubmit="return confirm('Are you sure you want to delete this quiz? This action cannot be undone.');"
                        style="display: inline;">
                        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="dashboard-button danger">Delete Quiz</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>