<?php
session_start();
include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Validate user session and role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error_message = null;

try {
    // Get tutor info
    $stmt = $pdo->prepare("
        SELECT u.*, t.id as tutor_id
        FROM users u
        JOIN tutors t ON u.id = t.user_id
        WHERE u.id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get courses with enrollment counts
    $courses_stmt = $pdo->prepare("
        SELECT 
            c.*,
            COUNT(DISTINCT ce.student_id) as enrolled_students,
            (
                SELECT COUNT(*) FROM assignments 
                WHERE course_id = c.id
            ) as assignment_count,
            (
                SELECT COUNT(*) FROM quizzes 
                WHERE course_id = c.id
            ) as quiz_count
        FROM courses c
        LEFT JOIN course_enrollments ce ON c.id = ce.course_id
        WHERE c.tutor_id = :tutor_id
        GROUP BY c.id
        ORDER BY c.name ASC
    ");
    $courses_stmt->execute(['tutor_id' => $tutor['tutor_id']]);
    $courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Modified assignments query to match schema
    $assignments_stmt = $pdo->prepare("
        SELECT 
            a.id,
            a.name,
            a.description,
            a.due_date,
            a.created_at,
            c.name as course_name,
            COUNT(ce.student_id) as enrolled_students
        FROM assignments a
        JOIN courses c ON a.course_id = c.id
        LEFT JOIN course_enrollments ce ON c.id = ce.course_id
        WHERE c.tutor_id = :tutor_id
        GROUP BY a.id, c.name
        ORDER BY a.due_date ASC
    ");
    $assignments_stmt->execute(['tutor_id' => $tutor['tutor_id']]);
    $assignments = $assignments_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Modified quizzes query to match schema
    $quizzes_stmt = $pdo->prepare("
        SELECT 
            q.id,
            q.quiz_name,
            q.quiz_description,
            q.quiz_date,
            q.quiz_time,
            q.duration,
            c.name as course_name,
            COUNT(ce.student_id) as enrolled_students
        FROM quizzes q
        JOIN courses c ON q.course_id = c.id
        LEFT JOIN course_enrollments ce ON c.id = ce.course_id
        WHERE c.tutor_id = :tutor_id
        GROUP BY q.id, c.name
        ORDER BY q.quiz_date ASC, q.quiz_time ASC
    ");
    $quizzes_stmt->execute(['tutor_id' => $tutor['tutor_id']]);
    $quizzes = $quizzes_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Tutor dashboard error: " . $e->getMessage());
    $error_message = "Error loading dashboard";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Dashboard - <?php echo htmlspecialchars($tutor['name'] ?? 'Loading...'); ?></title>
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
</head>

<body>
    <!-- Tutor Dashboard -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Tutor Dashboard</h1>
            <div class="dashboard-actions">
                <a href="/acetraining/pages/create_course.php" class="dashboard-button">Create New Course</a>
            </div>
            <div class="tutor-info">
                <p class="dashboard-welcome">Welcome, <?php echo htmlspecialchars($tutor['name'] ?? 'Tutor'); ?></p>
                <p class="email"><?php echo htmlspecialchars($tutor['email'] ?? ''); ?></p>
            </div>
        </div>

        <?php if ($error_message): ?>
            <div class="dashboard-alert"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2 class="card-title">Course Overview</h2>
                <div class="card-content">
                    <?php if (empty($courses)): ?>
                        <p>No courses assigned yet.</p>
                    <?php else: ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Course Name</th>
                                    <th>Students Enrolled</th>
                                    <th>Total Assignments</th>
                                    <th>Submissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($course['name']); ?></td>
                                        <td><?php echo htmlspecialchars($course['enrolled_students']); ?></td>
                                        <td><?php echo htmlspecialchars($course['assignment_count']); ?></td>
                                        <td><?php echo htmlspecialchars($course['quiz_count']); ?></td>
                                        <td>
                                            <a href="/acetraining/pages/course_details.php?id=<?php echo $course['id']; ?>"
                                                class="dashboard-button">Manage</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Add new Assignments card -->
            <div class="dashboard-card">
                <h2 class="card-title">
                    Assignments Overview
                    <a href="create_assignment.php" class="dashboard-button small">Create Assignment</a>
                </h2>
                <div class="card-content">
                    <?php if (empty($assignments)): ?>
                        <p>No assignments created yet.</p>
                    <?php else: ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Assignment</th>
                                    <th>Description</th>
                                    <th>Due Date</th>
                                    <th>Students</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignments as $assignment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($assignment['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['name']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['description']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($assignment['due_date'])); ?></td>
                                        <td><?php echo $assignment['enrolled_students']; ?></td>
                                        <td>
                                            <a href="assignment_details.php?id=<?php echo $assignment['id']; ?>"
                                                class="dashboard-button">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Add new Quizzes card -->
            <div class="dashboard-card">
                <h2 class="card-title">
                    Recent Quizzes
                    <a href="/acetraining/pages/create_quiz.php" class="dashboard-button small">Create Quiz</a>
                </h2>
                <div class="card-content">
                    <?php if (empty($quizzes)): ?>
                        <p>No quizzes created yet.</p>
                    <?php else: ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Quiz Name</th>
                                    <th>Course</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Duration</th>
                                    <th>Students</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quizzes as $quiz): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($quiz['quiz_name']); ?></td>
                                        <td><?php echo htmlspecialchars($quiz['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($quiz['quiz_description']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($quiz['quiz_date'])); ?></td>
                                        <td><?php echo date('g:i A', strtotime($quiz['quiz_time'])); ?></td>
                                        <td><?php echo $quiz['duration']; ?> min</td>
                                        <td><?php echo $quiz['enrolled_students']; ?></td>
                                        <td>
                                            <a href="/acetraining/pages/quiz_details.php?id=<?php echo $quiz['id']; ?>"
                                                class="dashboard-button">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php include __DIR__ . '/../components/footer.php'; ?>