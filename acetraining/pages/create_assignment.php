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
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $due_date = $_POST['due_date'] ?? '';
        $selected_course = $_POST['course_id'] ?? '';

        // Validation checks
        if (empty($name) || empty($description) || empty($due_date) || empty($selected_course)) {
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

        // Create assignment
        $insert_stmt = $pdo->prepare("
            INSERT INTO assignments (course_id, name, description, due_date)
            VALUES (:course_id, :name, :description, :due_date)
        ");

        $insert_stmt->execute([
            'course_id' => $selected_course,
            'name' => $name,
            'description' => $description,
            'due_date' => $due_date
        ]);

        $success_message = 'Assignment created successfully';
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
    <title>Create Assignment - Ace Training</title>
    <link rel="stylesheet" href="/acetraining/assets/css/global.css">
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
    <link rel="stylesheet" href="/acetraining/assets/css/forms.css">
</head>

<body>
    <!-- Page content -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Create New Assignment</h1>
            <a href="/acetraining/pages/tutor_dashboard.php" class="dashboard-button">Back to Dashboard</a>
        </div>

        <?php if ($error_message): ?>
            <div class="dashboard-alert error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="dashboard-alert success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <!-- Create Assignment Form -->
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

                <!-- Assignment Information -->
                <div class="form-group">
                    <label for="name">Assignment Name</label>
                    <input type="text" id="name" name="name" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" class="form-input" required></textarea>
                </div>

                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="datetime-local" id="due_date" name="due_date" class="form-input" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="dashboard-button">Create Assignment</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>