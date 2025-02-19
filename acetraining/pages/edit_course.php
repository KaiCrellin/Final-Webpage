<?php
session_start();
require_once __DIR__ . '/../lib/db.php';

// Verify tutor access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}



$course_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$error_message = null;
$success_message = null;

try {
    // Get course details if it belongs to this tutor
    $stmt = $pdo->prepare("
        SELECT c.*
        FROM courses c
        JOIN tutors t ON c.tutor_id = t.id
        WHERE c.id = :course_id
        AND t.user_id = :user_id
    ");

    $stmt->execute([
        'course_id' => $course_id,
        'user_id' => $_SESSION['user_id']
    ]);

    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        throw new Exception('Course not found or access denied');
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'active';

        if (empty($name)) {
            throw new Exception('Course name is required');
        }

        // Update course details
        $update_stmt = $pdo->prepare("
            UPDATE courses 
            SET name = :name,
                description = :description,
                status = :status
            WHERE id = :id
        ");



        $update_stmt->execute([
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'id' => $course_id
        ]);

        $success_message = 'Course updated successfully';

        // Refresh course data
        $course['name'] = $name;
        $course['description'] = $description;
        $course['status'] = $status;
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
    <title>Edit Course - <?php echo htmlspecialchars($course['name'] ?? ''); ?></title>
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
    <link rel="stylesheet" href="/acetraining/assets/css/forms.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Edit Course</h1>
            <a href="/acetraining/pages/course_details.php?id=<?php echo $course_id; ?>" class="dashboard-button">Back
                to Course Details</a>
        </div>

        <?php if ($error_message): ?>
            <div class="dashboard-alert error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="dashboard-alert success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <div class="dashboard-card">
            <form method="POST" class="edit-form">
                <div class="form-group">
                    <label for="name">Course Name</label>
                    <input type="text" id="name" name="name" required
                        value="<?php echo htmlspecialchars($course['name']); ?>" class="form-input">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"
                        class="form-input"><?php echo htmlspecialchars($course['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-input">
                        <option value="active" <?php echo $course['status'] === 'active' ? 'selected' : ''; ?>>Active
                        </option>
                        <option value="inactive" <?php echo $course['status'] === 'inactive' ? 'selected' : ''; ?>>
                            Inactive</option>
                    </select>
                </div>


                <div class="form-actions">
                    <button type="submit" class="dashboard-button">Save Changes</button>
                </div>
            </form>
        </div>

        <div class="dashboard-card">
            <h2 class="card-title">Enrolled Students</h2>
            <?php
            // Get enrolled students
            $students_stmt = $pdo->prepare("
                SELECT 
                    s.id as student_id,
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
            ?>

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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="dashboard-card">
            <h2 class="card-title">Manage Students</h2>
            <?php
            // Get all available students and their enrollment status
            $students_stmt = $pdo->prepare("
                SELECT 
                    s.id as student_id,
                    u.name,
                    u.email,
                    CASE WHEN ce.course_id IS NOT NULL THEN 1 ELSE 0 END as is_enrolled,
                    ce.enrollment_date
                FROM students s
                JOIN users u ON s.user_id = u.id
                LEFT JOIN course_enrollments ce ON s.id = ce.student_id AND ce.course_id = :course_id
                ORDER BY u.name ASC
            ");
            $students_stmt->execute(['course_id' => $course_id]);
            $all_students = $students_stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <form method="POST" action="/acetraining/handlers/update_course_enrollments.php" class="edit-form">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="student-list">
                    <?php foreach ($all_students as $student): ?>
                        <div class="student-item">
                            <input type="checkbox" id="student_<?php echo $student['student_id']; ?>" name="students[]"
                                value="<?php echo $student['student_id']; ?>" <?php echo $student['is_enrolled'] ? 'checked' : ''; ?>>
                            <label for="student_<?php echo $student['student_id']; ?>">
                                <?php echo htmlspecialchars($student['name']); ?>
                                (<?php echo htmlspecialchars($student['email']); ?>)
                                <?php if ($student['is_enrolled']): ?>
                                    <span class="enrollment-date">
                                        Enrolled: <?php echo date('M d, Y', strtotime($student['enrollment_date'])); ?>
                                    </span>
                                <?php endif; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="dashboard-button">Update Enrollments</button>
                </div>
            </form>
        </div>
</body>

</html>