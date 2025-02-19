<?php
session_start();
require_once __DIR__ . '/../lib/db.php';
include __DIR__ . '/../components/header.php';

// Verify tutor access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}

$error_message = null;
$success_message = null;
$course_id = filter_input(INPUT_GET, 'course_id', FILTER_SANITIZE_NUMBER_INT);

try {
    // Get tutor's courses for dropdown
    $courses_stmt = $pdo->prepare("
        SELECT c.id, c.name
        FROM courses c
        JOIN tutors t ON c.tutor_id = t.id
        WHERE t.user_id = :user_id
        ORDER BY c.name ASC
    ");
    $courses_stmt->execute(['user_id' => $_SESSION['user_id']]);
    $courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $quiz_name = trim($_POST['quiz_name'] ?? '');
        $quiz_description = trim($_POST['quiz_description'] ?? '');
        $quiz_date = $_POST['quiz_date'] ?? '';
        $quiz_time = $_POST['quiz_time'] ?? '';
        $duration = filter_input(INPUT_POST, 'duration', FILTER_SANITIZE_NUMBER_INT);
        $selected_course = $_POST['course_id'] ?? '';

        // Validation checks
        if (
            empty($quiz_name) || empty($quiz_description) || empty($quiz_date) ||
            empty($quiz_time) || empty($duration) || empty($selected_course)
        ) {
            throw new Exception('All fields are required');
        }

        // Verify course belongs to tutor
        $verify_stmt = $pdo->prepare("
            SELECT 1 FROM courses c
            JOIN tutors t ON c.tutor_id = t.id
            WHERE c.id = :course_id AND t.user_id = :user_id
        ");
        $verify_stmt->execute([
            'course_id' => $selected_course,
            'user_id' => $_SESSION['user_id']
        ]);

        if (!$verify_stmt->fetch()) {
            throw new Exception('Invalid course selected');
        }

        // Create quiz
        $insert_stmt = $pdo->prepare("
            INSERT INTO quizzes (
                course_id, quiz_name, quiz_description, 
                quiz_date, quiz_time, duration
            ) VALUES (
                :course_id, :quiz_name, :quiz_description, 
                :quiz_date, :quiz_time, :duration
            )
        ");

        $insert_stmt->execute([
            'course_id' => $selected_course,
            'quiz_name' => $quiz_name,
            'quiz_description' => $quiz_description,
            'quiz_date' => $quiz_date,
            'quiz_time' => $quiz_time,
            'duration' => $duration
        ]);

        $success_message = 'Quiz created successfully';
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
    <title>Create Quiz - Ace Training</title>
    <link rel="stylesheet" href="/acetraining/assets/css/global.css">
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
    <link rel="stylesheet" href="/acetraining/assets/css/forms.css">
</head>

<body>
    <!-- Page content -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Create New Quiz</h1>
            <a href="/acetraining/pages/tutor_dashboard.php" class="dashboard-button">Back to Dashboard</a>
        </div>

        <?php if ($error_message): ?>
            <div class="dashboard-alert error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="dashboard-alert success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <!-- Create quiz form -->
        <div class="dashboard-card">
            <form method="POST" class="edit-form">
                <div class="form-group">
                    <label for="course_id">Course</label>
                    <select id="course_id" name="course_id" class="form-input" required>
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo $course['id']; ?>" <?php echo ($course_id == $course['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($course['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Quiz details -->
                <div class="form-group">
                    <label for="quiz_name">Quiz Name</label>
                    <input type="text" id="quiz_name" name="quiz_name" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="quiz_description">Description</label>
                    <textarea id="quiz_description" name="quiz_description" rows="4" class="form-input"
                        required></textarea>
                </div>

                <div class="form-group">
                    <label for="quiz_date">Quiz Date</label>
                    <input type="date" id="quiz_date" name="quiz_date" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="quiz_time">Start Time</label>
                    <input type="time" id="quiz_time" name="quiz_time" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="duration">Duration (minutes)</label>
                    <input type="number" id="duration" name="duration" min="1" max="180" class="form-input" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="dashboard-button">Create Quiz</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>