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

$assignment_id = filter_input(INPUT_POST, 'assignment_id', FILTER_SANITIZE_NUMBER_INT);

try {
    $pdo->beginTransaction();

    // Verify assignment belongs to tutor's course
    $stmt = $pdo->prepare("
        SELECT c.id as course_id 
        FROM assignments a
        JOIN courses c ON a.course_id = c.id
        JOIN tutors t ON c.tutor_id = t.id
        WHERE a.id = :assignment_id 
        AND t.user_id = :user_id
    ");

    $stmt->execute([
        'assignment_id' => $assignment_id,
        'user_id' => $_SESSION['user_id']
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        throw new Exception('Assignment not found or access denied');
    }

    // Delete the assignment
    $delete_stmt = $pdo->prepare("DELETE FROM assignments WHERE id = ?");
    $delete_stmt->execute([$assignment_id]);

    $pdo->commit();
    $_SESSION['success_message'] = 'Assignment deleted successfully';
    header('Location: /acetraining/pages/course_details.php?id=' . $result['course_id']);
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Assignment deletion error: " . $e->getMessage());
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
