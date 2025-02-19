<?php
session_start();
include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}

try {
    // Get user details
    $stmt = $pdo->prepare("
        SELECT 
            u.*,
            CASE 
                WHEN t.id IS NOT NULL THEN 'tutor'
                WHEN s.id IS NOT NULL THEN 'student'
            END as role,
            CASE
                WHEN t.id IS NOT NULL THEN (
                    SELECT COUNT(*) 
                    FROM courses c 
                    WHERE c.tutor_id = t.id
                )
                WHEN s.id IS NOT NULL THEN (
                    SELECT COUNT(*) 
                    FROM course_enrollments ce 
                    WHERE ce.student_id = s.id
                )
                ELSE 0
            END as course_count
        FROM users u
        LEFT JOIN tutors t ON u.id = t.user_id
        LEFT JOIN students s ON u.id = s.user_id
        WHERE u.id = ?
    ");

    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get additional stats based on role
    if ($user['role'] === 'student') {
        $stats_stmt = $pdo->prepare("
            SELECT 
                COUNT(DISTINCT a.id) as assignment_count,
                COUNT(DISTINCT q.id) as quiz_count
            FROM course_enrollments ce
            JOIN students s ON ce.student_id = s.id
            LEFT JOIN assignments a ON ce.course_id = a.course_id
            LEFT JOIN quizzes q ON ce.course_id = q.course_id
            WHERE s.user_id = ?
        ");
    } else {
        $stats_stmt = $pdo->prepare("
            SELECT 
                COUNT(DISTINCT a.id) as assignment_count,
                COUNT(DISTINCT q.id) as quiz_count,
                COUNT(DISTINCT ce.student_id) as student_count
            FROM courses c
            JOIN tutors t ON c.tutor_id = t.id
            LEFT JOIN assignments a ON c.id = a.course_id
            LEFT JOIN quizzes q ON c.id = q.course_id
            LEFT JOIN course_enrollments ce ON c.id = ce.course_id
            WHERE t.user_id = ?
        ");
    }

    $stats_stmt->execute([$_SESSION['user_id']]);
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo htmlspecialchars($user['name']); ?></title>
    <link rel="stylesheet" href="/acetraining/assets/css/dashboard.css">
    <link rel="stylesheet" href="/acetraining/assets/css/profile.css">
</head>

<body>
    <!-- Navigation -->
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
            <h1 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h1>
            <div class="profile-role"><?php echo htmlspecialchars($user['role']); ?></div>
        </div>
        <!-- Profile Stats -->
        <div class="profile-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $user['course_count']; ?></div>
                <div class="stat-label">Courses</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['assignment_count']; ?></div>
                <div class="stat-label">Assignments</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['quiz_count']; ?></div>
                <div class="stat-label">Quizzes</div>
            </div>
            <?php if ($user['role'] === 'tutor'): ?>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['student_count']; ?></div>
                    <div class="stat-label">Students</div>
                </div>
            <?php endif; ?>
        </div>
        <!-- Profile Details -->
        <div class="profile-details">
            <div class="details-group">
                <div class="details-label">Email</div>
                <div class="details-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <div class="details-group">
                <div class="details-label">Member Since</div>
                <div class="details-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></div>
            </div>
            <div class="details-group">
                <div class="details-label">Last Login</div>
                <div class="details-value">
                    <?php echo $user['last_logged_in'] ? date('F j, Y g:i A', strtotime($user['last_logged_in'])) : 'Never'; ?>
                </div>
            </div>
        </div>
        <!-- Profile Actions -->
        <div class="profile-actions">
            <a href="/acetraining/pages/<?php echo $user['role']; ?>_dashboard.php" class="dashboard-button">Back to
                Dashboard</a>
        </div>
    </div>
</body>

</html>
<?php include __DIR__ . '/../components/footer.php'; ?>