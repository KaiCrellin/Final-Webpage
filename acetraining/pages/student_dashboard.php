<?php
session_start();
include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error_message = null;

try {
    // Get student info
    $stmt = $pdo->prepare("
        SELECT u.*, s.id as student_id 
        FROM users u 
        JOIN students s ON u.id = s.user_id 
        WHERE u.id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        throw new Exception("Student not found");
    }

    // Debug statement
    error_log("Student found: " . print_r($student, true));

    // Get enrolled courses
    $courses_stmt = $pdo->prepare("
        SELECT 
            c.*,
            u.name as tutor_name,
            ce.enrollment_date
        FROM course_enrollments ce
        JOIN courses c ON ce.course_id = c.id
        JOIN tutors t ON c.tutor_id = t.id
        JOIN users u ON t.user_id = u.id
        JOIN students s ON ce.student_id = s.id
        WHERE s.user_id = :user_id
        ORDER BY c.name ASC
    ");
    $courses_stmt->execute(['user_id' => $user_id]);
    $courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug statement
    error_log("Courses found: " . print_r($courses, true));

    // Get assignments for enrolled courses
    $assignments_stmt = $pdo->prepare("
        SELECT 
            a.id,
            a.name,
            a.description,
            a.due_date,
            a.created_at,
            c.name as course_name
        FROM assignments a
        JOIN courses c ON a.course_id = c.id
        JOIN course_enrollments ce ON c.id = ce.course_id
        JOIN students s ON ce.student_id = s.id
        WHERE s.user_id = :user_id 
        AND a.due_date >= CURRENT_DATE
        ORDER BY a.due_date ASC
    ");
    $assignments_stmt->execute(['user_id' => $user_id]);
    $assignments = $assignments_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get upcoming quizzes
    $quizzes_stmt = $pdo->prepare("
        SELECT 
            q.id,
            q.quiz_name,
            q.quiz_description,
            q.quiz_date,
            q.quiz_time,
            q.duration,
            c.name as course_name,
            CASE 
                WHEN q.quiz_date < CURRENT_DATE THEN 'Past'
                WHEN q.quiz_date = CURRENT_DATE THEN 'Today'
                ELSE 'Upcoming'
            END as status
        FROM quizzes q
        JOIN courses c ON q.course_id = c.id
        JOIN course_enrollments ce ON c.id = ce.course_id
        JOIN students s ON ce.student_id = s.id
        WHERE s.user_id = :user_id
        ORDER BY q.quiz_date ASC, q.quiz_time ASC
    ");
    $quizzes_stmt->execute(['user_id' => $user_id]);
    $quizzes = $quizzes_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Student dashboard error: " . $e->getMessage());
    $error_message = "Error loading dashboard";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - <?php echo htmlspecialchars($student['name'] ?? 'Loading...'); ?></title>
    <link rel="stylesheet" href="/acetraining/assets/css/global.css">
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Student Dashboard</h1>
            <div class="student-info">
                <p class="dashboard-welcome">Welcome, <?php echo htmlspecialchars($student['name'] ?? 'Student'); ?></p>
                <p class="email"><?php echo htmlspecialchars($student['email'] ?? ''); ?></p>
            </div>
        </div>

        <?php if ($error_message): ?>
            <div class="dashboard-alert"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <!-- My Courses Card -->
            <div class="dashboard-card">
                <h2 class="card-title">My Courses</h2>
                <div class="card-content">
                    <?php if (empty($courses)): ?>
                        <p>You are not enrolled in any courses yet.</p>
                    <?php else: ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Tutor</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($course['name']); ?></td>
                                        <td><?php echo htmlspecialchars($course['tutor_name']); ?></td>
                                        <td><?php echo htmlspecialchars($course['description']); ?></td>
                                        <td>
                                            <a href="/acetraining/pages/student_course_view.php?id=<?php echo $course['id']; ?>"
                                                class="dashboard-button">View Course</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Assignments Card -->
            <div class="dashboard-card">
                <h2 class="card-title">Upcoming Assignments</h2>
                <div class="card-content">
                    <?php if (empty($assignments)): ?>
                        <p>No upcoming assignments.</p>
                    <?php else: ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Assignment</th>
                                    <th>Course</th>
                                    <th>Description</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignments as $assignment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($assignment['name']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['description']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($assignment['due_date'])); ?></td>
                                        <td>
                                            <a href="/acetraining/pages/student_assignment_view.php?id=<?php echo $assignment['id']; ?>"
                                                class="dashboard-button">View Assignment</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dashboard-card">
                <h2 class="card-title">Recent Quizzes</h2>
                <div class="card-content">
                    <?php if (empty($quizzes)): ?>
                        <p>No quizzes available.</p>
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
                                    <th>Status</th>
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
                                        <td><?php echo htmlspecialchars($quiz['status']); ?></td>
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