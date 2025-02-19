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
$student_id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_NUMBER_INT);

try {
    // beging transaction
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

    // Remove student enrollment
    $delete_stmt = $pdo->prepare("
        DELETE FROM course_enrollments 
        WHERE course_id = :course_id 
        AND student_id = :student_id
    ");

    $delete_stmt->execute([
        'course_id' => $course_id,
        'student_id' => $student_id
    ]);

    //commit transaction
    $pdo->commit();
    $_SESSION['success_message'] = 'Student removed from course successfully';
    //Rollback on error
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Unenroll error: " . $e->getMessage());
    $_SESSION['error_message'] = $e->getMessage();
}

header('Location: /acetraining/pages/edit_course.php?id=' . $course_id);
exit();
