<?php
session_start();
include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';

// Verify tutor authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}

try {
    // Get tutor ID with error checking
    $stmt = $pdo->prepare("
        SELECT t.id, u.name 
        FROM tutors t 
        JOIN users u ON t.user_id = u.id 
        WHERE t.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $tutor = $stmt->fetch();

    if (!$tutor) {
        throw new Exception("Tutor not found");
    }

    $tutor_id = $tutor['id'];

    // Get available students
    $stmt = $pdo->prepare("
        SELECT s.id, u.name, u.email 
        FROM students s 
        JOIN users u ON s.user_id = u.id
        ORDER BY u.name
    ");
    $stmt->execute();
    $students = $stmt->fetchAll();

} catch (Exception $e) {
    error_log("Error in create course page: " . $e->getMessage());
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Course</title>
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
    <link rel="stylesheet" href="/acetraining/assets/css/create_course.css">

</head>

<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Create New Course</h1>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="dashboard-alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        <!-- Error message -->
        <div class="dashboard-card">
            <!-- Debug info for development -->
            <?php if ($_SERVER['SERVER_NAME'] === 'localhost'): ?>
                <div class="debug-info" style="margin-bottom: 20px; padding: 10px; background: #f8f9fa;">
                    <p>Tutor ID: <?php echo htmlspecialchars($tutor_id ?? 'Not set'); ?></p>
                    <p>Students Count: <?php echo count($students); ?></p>
                </div>
            <?php endif; ?>

            <form action="/acetraining/actions/create_course_action.php" method="POST" enctype="multipart/form-data"
                class="course-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="tutor_id" value="<?php echo $tutor_id; ?>">

                <div class="form-group">
                    <label for="course_name">Course Name</label>
                    <input type="text" id="course_name" name="course_name" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="description">Course Description</label>
                    <textarea id="description" name="description" rows="4" required class="form-input"></textarea>
                </div>


                <div class="form-group">
                    <label>Select Students to Enroll</label>
                    <div class="student-list">
                        <?php foreach ($students as $student): ?>
                            <div class="student-item">
                                <input type="checkbox" id="student_<?php echo $student['id']; ?>" name="students[]"
                                    value="<?php echo $student['id']; ?>">
                                <label for="student_<?php echo $student['id']; ?>">
                                    <?php echo htmlspecialchars($student['name']); ?>
                                    (<?php echo htmlspecialchars($student['email']); ?>)
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="dashboard-button">Create Course</button>
                    <a href="tutor_dashboard.php" class="dashboard-button button-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>