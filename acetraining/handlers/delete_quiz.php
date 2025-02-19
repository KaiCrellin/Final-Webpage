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

$quiz_id = filter_input(INPUT_POST, 'quiz_id', FILTER_SANITIZE_NUMBER_INT);

try {
    //start transaction
    $pdo->beginTransaction();

    // Verify quiz belongs to tutor's course
    $stmt = $pdo->prepare("
        SELECT c.id as course_id 
        FROM quizzes q
        JOIN courses c ON q.course_id = c.id
        JOIN tutors t ON c.tutor_id = t.id
        WHERE q.id = :quiz_id 
        AND t.user_id = :user_id
    ");

    $stmt->execute([
        'quiz_id' => $quiz_id,
        'user_id' => $_SESSION['user_id']
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        throw new Exception('Quiz not found or access denied');
    }

    // Delete the quiz
    $delete_stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ?");
    $delete_stmt->execute([$quiz_id]);

    //commit transaction
    $pdo->commit();

    $_SESSION['success_message'] = 'Quiz deleted successfully';
    header('Location: /acetraining/pages/course_details.php?id=' . $result['course_id']);
    exit();

    // RollBack on eorror
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Quiz deletion error: " . $e->getMessage());
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
