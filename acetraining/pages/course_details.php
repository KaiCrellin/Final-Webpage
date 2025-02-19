<?php
session_start();
include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';

// Verify tutor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}

$course_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$course_id) {
    header('Location: /acetraining/pages/tutor_dashboard.php');
    exit();
}

try {
    // Get course details with enrollment count
    $stmt = $pdo->prepare("
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
        WHERE c.id = :course_id
        AND c.tutor_id = (
            SELECT id FROM tutors WHERE user_id = :user_id
        )
        GROUP BY c.id
    ");

    $stmt->execute([
        'course_id' => $course_id,
        'user_id' => $_SESSION['user_id']
    ]);

    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        throw new Exception('Course not found or access denied');
    }

    // Get enrolled students
    $students_stmt = $pdo->prepare("
        SELECT 
            u.name,
            u.email,
            ce.enrollment_date
        FROM course_enrollments ce
        JOIN students s ON ce.student_id = s.id
        JOIN users u ON s.user_id = u.id
        WHERE ce.course_id = :course_id
        ORDER BY u.name
    ");
    $students_stmt->execute(['course_id' => $course_id]);
    $enrolled_students = $students_stmt->fetchAll(PDO::FETCH_ASSOC);


} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details - <?php echo htmlspecialchars($course['name'] ?? ''); ?></title>
    <link rel="stylesheet" href="/acetraining/assets/css/global.css">
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Course Details</h1>
            <a href="/acetraining/pages/tutor_dashboard.php" class="dashboard-button">Back to Dashboard</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="dashboard-alert"><?php echo htmlspecialchars($error); ?></div>
        <?php else: ?>
            <div class="dashboard-grid">
                <!-- Course Overview Card -->
                <div class="dashboard-card">
                    <h2 class="card-title">Course Overview</h2>
                    <div class="course-details">
                        <div class="detail-row">
                            <strong>Course Name:</strong> <?php echo htmlspecialchars($course['name']); ?>
                        </div>
                        <div class="detail-row">
                            <strong>Description:</strong>
                            <p><?php echo htmlspecialchars($course['description']); ?></p>
                        </div>
                        <div class="detail-row">
                            <strong>Course Resources:</strong>
                            <p><?php echo htmlspecialchars($course['resources']); ?></p>
                        </div>
                        <div class="detail-row">
                            <strong>Status:</strong> <?php echo htmlspecialchars($course['status']); ?>
                        </div>
                        <div class="detail-row">
                            <strong>Students Enrolled:</strong> <?php echo $course['enrolled_students']; ?>
                        </div>
                    </div>
                </div>

                <!-- Assignments Card -->
                <div class="dashboard-card">
                    <h2 class="card-title">Course Assignments</h2>
                    <?php
                    $assignments_stmt = $pdo->prepare("
                        SELECT * FROM assignments 
                        WHERE course_id = ? 
                        ORDER BY due_date ASC
                    ");
                    $assignments_stmt->execute([$course_id]);
                    $assignments = $assignments_stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php if (empty($assignments)): ?>
                        <p>No assignments created yet.</p>
                    <?php else: ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignments as $assignment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($assignment['name']); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($assignment['due_date'])); ?></td>
                                        <td>
                                            <a href="/acetraining/pages/assignment_details.php?id=<?php echo $assignment['id']; ?>"
                                                class="dashboard-button small">Edit</a>
                                            <form method="POST" action="/acetraining/handlers/delete_assignment.php"
                                                style="display: inline;" onsubmit="return confirm('Delete this assignment?');">
                                                <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                                                <input type="hidden" name="csrf_token"
                                                    value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <button type="submit" class="dashboard-button small danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Quizzes Card -->
                <div class="dashboard-card">
                    <h2 class="card-title">Course Quizzes</h2>
                    <?php
                    $quizzes_stmt = $pdo->prepare("
                        SELECT * FROM quizzes 
                        WHERE course_id = ? 
                        ORDER BY quiz_date ASC
                    ");
                    $quizzes_stmt->execute([$course_id]);
                    $quizzes = $quizzes_stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php if (empty($quizzes)): ?>
                        <p>No quizzes created yet.</p>
                    <?php else: ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quizzes as $quiz): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($quiz['quiz_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($quiz['quiz_date'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($quiz['quiz_time'])); ?></td>
                                        <td>
                                            <a href="/acetraining/pages/quiz_details.php?id=<?php echo $quiz['id']; ?>"
                                                class="dashboard-button small">Edit</a>
                                            <form method="POST" action="/acetraining/handlers/delete_quiz.php"
                                                style="display: inline;" onsubmit="return confirm('Delete this quiz?');">
                                                <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                                                <input type="hidden" name="csrf_token"
                                                    value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <button type="submit" class="dashboard-button small danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Enrolled Students Card -->
                <div class="dashboard-card">
                    <h2 class="card-title">Enrolled Students</h2>
                    <?php if (empty($enrolled_students)): ?>
                        <p>No students enrolled yet.</p>
                    <?php else: ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Enrollment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrolled_students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($student['enrollment_date'])); ?></td>
                                        <td>
                                            <form method="POST" action="/acetraining/handlers/unenroll_student.php"
                                                style="display: inline;"
                                                onsubmit="return confirm('Remove this student from the course?');">
                                                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                                <input type="hidden" name="student_id"
                                                    value="<?php echo $student['student_id']; ?>">
                                                <input type="hidden" name="csrf_token"
                                                    value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <button type="submit" class="dashboard-button small danger">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Course Management Buttons -->
                <div class="dashboard-card">
                    <h2 class="card-title">Course Management</h2>
                    <div class="button-group">
                        <a href="/acetraining/pages/create_assignment.php?course_id=<?php echo $course_id; ?>"
                            class="dashboard-button">Add Assignment</a>
                        <a href="/acetraining/pages/create_quiz.php?course_id=<?php echo $course_id; ?>"
                            class="dashboard-button">Add Quiz</a>
                        <a href="/acetraining/pages/edit_course.php?id=<?php echo $course_id; ?>"
                            class="dashboard-button">Edit Course Details</a>
                        <form method="POST" action="/acetraining/handlers/delete_course.php"
                            onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.');"
                            style="display: inline;">
                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit" class="dashboard-button danger">Delete Course</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>