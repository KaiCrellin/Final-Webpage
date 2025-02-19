<?php
session_start();
require_once __DIR__ . '/../lib/db.php';

// Verify tutor access and CSRF token
if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'tutor' ||
    !isset($_POST['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    header('Location: /acetraining/pages/unauthorized.php');
    exit();
}

$course_id = filter_input(INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT);

try {
    // Start transaction
    $pdo->beginTransaction();

    // Verify course belongs to this tutor
    $verify_stmt = $pdo->prepare("
        SELECT 1 FROM courses c
        JOIN tutors t ON c.tutor_id = t.id
        WHERE c.id = :course_id 
        AND t.user_id = :user_id
    ");

    $verify_stmt->execute([
        'course_id' => $course_id,
        'user_id' => $_SESSION['user_id']
    ]);

    if (!$verify_stmt->fetch()) {
        throw new Exception('Course not found or access denied');
    }

    // Delete related records first
    $pdo->prepare("DELETE FROM course_enrollments WHERE course_id = ?")->execute([$course_id]);
    $pdo->prepare("DELETE FROM assignments WHERE course_id = ?")->execute([$course_id]);
    $pdo->prepare("DELETE FROM quizzes WHERE course_id = ?")->execute([$course_id]);
    $pdo->prepare("DELETE FROM classes WHERE course_id = ?")->execute([$course_id]);

    // Finally delete the course
    $pdo->prepare("DELETE FROM courses WHERE id = ?")->execute([$course_id]);

    // Commit transaction
    $pdo->commit();

    $_SESSION['success_message'] = 'Course deleted successfully';
    header('Location: /acetraining/pages/tutor_dashboard.php');
    exit();

} catch (Exception $e) {
    // Rollback on error
    $pdo->rollBack();
    error_log("Course deletion error: " . $e->getMessage());
    $_SESSION['error_message'] = 'Error deleting course';
    header('Location: /acetraining/pages/course_details.php?id=' . $course_id);
    exit();
}
