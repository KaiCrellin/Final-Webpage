<?php
session_start();
require_once __DIR__ . '/../lib/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header('Location: /acetraining/pages/unauthorized.php');
    exit();
}

// Add error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /acetraining/pages/error.php?message=Invalid request method');
    exit();
}

try {
    // Debug information
    error_log("POST data: " . print_r($_POST, true));

    $pdo->beginTransaction();

    // Validate tutor_id
    if (!isset($_POST['tutor_id']) || empty($_POST['tutor_id'])) {
        throw new Exception("Tutor ID is required");
    }

    // Create the course with basic information
    $stmt = $pdo->prepare("
        INSERT INTO courses (name, description, tutor_id, status) 
        VALUES (:name, :description, :tutor_id, 'active')
    ");

    $result = $stmt->execute([
        'name' => $_POST['course_name'],
        'description' => $_POST['description'],
        'tutor_id' => $_POST['tutor_id']
    ]);

    if (!$result) {
        throw new Exception("Failed to insert course");
    }

    $course_id = $pdo->lastInsertId();
    error_log("Created course with ID: " . $course_id);



    // Enroll selected students if any are selected
    if (isset($_POST['students']) && is_array($_POST['students'])) {
        $enroll_stmt = $pdo->prepare("
            INSERT INTO course_enrollments (course_id, student_id, enrollment_date) 
            VALUES (:course_id, :student_id, CURRENT_TIMESTAMP)
        ");

        foreach ($_POST['students'] as $student_id) {
            $enroll_result = $enroll_stmt->execute([
                'course_id' => $course_id,
                'student_id' => $student_id
            ]);

            if (!$enroll_result) {
                throw new Exception("Failed to enroll student ID: " . $student_id);
            }
        }
    }

    $pdo->commit();
    $_SESSION['success_message'] = 'Course created successfully';
    header('Location: /acetraining/pages/course_details.php?id=' . $course_id);
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Course creation error: " . $e->getMessage());
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: /acetraining/pages/create_course.php');
    exit();
}
?>