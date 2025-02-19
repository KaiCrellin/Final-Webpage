<?php
session_start();
require_once __DIR__ . '/../lib/db.php';
include __DIR__ . '/../components/header.php';

// Verify user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}

try {
    $role = $_SESSION['role'];
    $user_id = $_SESSION['user_id'];

    // Get classes based on role
    if ($role === 'student') {
        $stmt = $pdo->prepare("
            SELECT 
                c.name as course_name,
                cl.class_name,
                cl.class_description,
                cl.class_date,
                cl.class_time,
                cl.duration,
                u.name as tutor_name
            FROM classes cl
            JOIN courses c ON cl.course_id = c.id
            JOIN course_enrollments ce ON c.id = ce.course_id
            JOIN students s ON ce.student_id = s.id
            JOIN tutors t ON c.tutor_id = t.id
            JOIN users u ON t.user_id = u.id
            WHERE s.user_id = :user_id
            ORDER BY cl.class_date ASC, cl.class_time ASC
        ");
    } else {
        $stmt = $pdo->prepare("
            SELECT 
                c.name as course_name,
                cl.class_name,
                cl.class_description,
                cl.class_date,
                cl.class_time,
                cl.duration,
                COUNT(ce.student_id) as student_count
            FROM classes cl
            JOIN courses c ON cl.course_id = c.id
            JOIN tutors t ON c.tutor_id = t.id
            LEFT JOIN course_enrollments ce ON c.id = ce.course_id
            WHERE t.user_id = :user_id
            GROUP BY cl.id
            ORDER BY cl.class_date ASC, cl.class_time ASC
        ");
    }

    $stmt->execute(['user_id' => $user_id]);
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Timetable error: " . $e->getMessage());
    $error_message = "Error loading timetable";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable - Ace Training</title>
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
    <style>
        .timetable-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1rem;
        }

        .class-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .class-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .class-time {
            color: #666;
            font-size: 0.9rem;
        }

        .no-classes {
            text-align: center;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Class Timetable</h1>
            <a href="/acetraining/pages/<?php echo $role; ?>_dashboard.php" class="dashboard-button">
                Back to Dashboard
            </a>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="dashboard-alert error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (empty($classes)): ?>
            <div class="no-classes">
                <p>No classes scheduled at this time.</p>
            </div>
        <?php else: ?>
            <div class="dashboard-card">
                <h2 class="card-title">Upcoming Classes</h2>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Class</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <?php if ($role === 'student'): ?>
                                <th>Tutor</th>
                            <?php else: ?>
                                <th>Students</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($class['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                                <td><?php echo date('D, M d, Y', strtotime($class['class_date'])); ?></td>
                                <td><?php echo date('g:i A', strtotime($class['class_time'])); ?></td>
                                <td><?php echo $class['duration']; ?> mins</td>
                                <?php if ($role === 'student'): ?>
                                    <td><?php echo htmlspecialchars($class['tutor_name']); ?></td>
                                <?php else: ?>
                                    <td><?php echo $class['student_count']; ?> enrolled</td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
<?php include __DIR__ . '/../components/footer.php'; ?>