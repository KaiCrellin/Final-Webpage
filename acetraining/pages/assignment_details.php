<?php
session_start();
include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';

// Verify tutor access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}

$assignment_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$error_message = null;
$success_message = null;

try {
    // Get assignment details if it belongs to this tutor's course
    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            c.name as course_name,
            COUNT(DISTINCT ce.student_id) as enrolled_students
        FROM assignments a
        JOIN courses c ON a.course_id = c.id
        JOIN tutors t ON c.tutor_id = t.id
        LEFT JOIN course_enrollments ce ON c.id = ce.course_id
        WHERE a.id = :assignment_id 
        AND t.user_id = :user_id
        GROUP BY a.id
    ");

    $stmt->execute([
        'assignment_id' => $assignment_id,
        'user_id' => $_SESSION['user_id']
    ]);

    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$assignment) {
        throw new Exception('Assignment not found or access denied');
    }

    // Handle form submission for updates
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $due_date = $_POST['due_date'] ?? '';

        if (empty($name) || empty($description) || empty($due_date)) {
            throw new Exception('All fields are required');
        }

        // Update assignment
        $update_stmt = $pdo->prepare("
            UPDATE assignments 
            SET name = :name,
                description = :description,
                due_date = :due_date
            WHERE id = :id
        ");

        $update_stmt->execute([
            'name' => $name,
            'description' => $description,
            'due_date' => $due_date,
            'id' => $assignment_id
        ]);

        $success_message = 'Assignment updated successfully';

        // Refresh assignment data
        $assignment['name'] = $name;
        $assignment['description'] = $description;
        $assignment['due_date'] = $due_date;
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
    <title>Assignment Details - <?php echo htmlspecialchars($assignment['name'] ?? ''); ?></title>
    <link rel="stylesheet" href="/acetraining/assets/css/global.css">
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
    <link rel="stylesheet" href="/acetraining/assets/css/forms.css">
</head>

<body>
    <!-- Dashboard Navigation -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Assignment Details</h1>
            <a href="/acetraining/pages/course_details.php?id=<?php echo $assignment['course_id']; ?>"
                class="dashboard-button">Back to Course</a>
        </div>

        <?php if ($error_message): ?>
            <div class="dashboard-alert error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="dashboard-alert success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <!-- Edit Assignment -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2 class="card-title">Edit Assignment</h2>
                <form method="POST" class="edit-form">
                    <div class="form-group">
                        <label for="course">Course</label>
                        <input type="text" id="course"
                            value="<?php echo htmlspecialchars($assignment['course_name']); ?>" class="form-input"
                            readonly>
                    </div>

                    <div class="form-group">
                        <label for="name">Assignment Name</label>
                        <input type="text" id="name" name="name" class="form-input" required
                            value="<?php echo htmlspecialchars($assignment['name']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" class="form-input"
                            required><?php echo htmlspecialchars($assignment['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="datetime-local" id="due_date" name="due_date" class="form-input" required
                            value="<?php echo date('Y-m-d\TH:i', strtotime($assignment['due_date'])); ?>">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="dashboard-button">Save Changes</button>
                    </div>
                </form>
            </div>
            <!-- Assignment Information -->
            <div class="dashboard-card">
                <h2 class="card-title">Assignment Information</h2>
                <div class="detail-row">
                    <strong>Created:</strong>
                    <?php echo date('M d, Y H:i', strtotime($assignment['created_at'])); ?>
                </div>
                <div class="detail-row">
                    <strong>Students in Course:</strong>
                    <?php echo $assignment['enrolled_students']; ?>
                </div>
                <div class="form-actions">
                    <form method="POST" action="/acetraining/handlers/delete_assignment.php"
                        onsubmit="return confirm('Are you sure you want to delete this assignment? This action cannot be undone.');"
                        style="display: inline;">
                        <input type="hidden" name="assignment_id" value="<?php echo $assignment_id; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="dashboard-button danger">Delete Assignment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>