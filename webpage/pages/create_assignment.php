<?php
session_start();
include_once __DIR__ . '/../lib/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_name  = $_POST['assignment_name'] ?? '';
    $assignment_description = $_POST['assignment_description'] ?? '';
    $assignment_due_date = $_POST['assignment_due_date'] ?? '';
    $course_id = $_SESSION['user_id'] ?? '';

    if (empty($assignment_name) || empty($assignment_description) || empty($assignment_due_date)) {
        $error =  "Please fill out all fields";
        header('Location: /webpage/pages/tutor_dashboard.php?error=' . urlencode($error));
        exit();
    } 


    $file_path = '';
    if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/';
        $file_name = basename($_FILES['assignment_file']['name']);
        $file_path = $upload_dir . $file_name;

        if (!move_uploaded_file($_FILES['assignment_file']['tmp_name'], $file_path)) {
            $error = "File Upload Failed";
            header('Location; /webpage/pages/tutor_dashboard.php?error=' . urlencode($error));
            exit();

        }

        $file_path = '/uploads/' . $file_name;
    }
    try {
        global $pdo;

        $stmt = $pdo->prepare("INSERT INTO assignments (course_id, name, description, due_date, file_path) VALUES (:course_id, :name, :description, :due_date, :file_path)");
        $stmt->execute([
            'course_id' => $course_id,
            'name' => $assignment_name,
            'description' => $assignment_description,
            'due_date' => $assignment_due_date,
            'file_path' => $file_path
        ]);
        header('Location: /webpage/pages.tutor_dashboard.php?success=Assignment Created successfully');
        exit();
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        header('Location: /webpage/pages/tutor_dashboard.php?error=' . urlencode($error));
        exit();
    }
} else {
    header('Location: /webpage/pages/tutor_dashboard.php');
    exit();
}
?>