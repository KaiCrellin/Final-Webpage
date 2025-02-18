<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'tutor') {
    echo "Access denied. Only tutors can create assignments.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_title = $_POST['title'];
    $course_file = $_FILES['file'];
    $course_chosen_students = $_POST['students'];
    $course_description = $_POST['description'];


    $target_dir = "uploads/";
    $target_file = $target_dir . basename($course_file["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }


    if ($course_file["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }


    if ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
        echo "Sorry, only PDF, DOC & DOCX files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($course_file["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($course_file["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    }

    try {
        global $pdo;

        $stmt = $pdo->prepare("INSERT INTO courses (title, file, students, description) VALUES (:title, :file, :students, :description)");
        $stmt->bindValue(':title', $course_title);
        $stmt->bindValue(':file', $target_file);
        $stmt->bindValue(':students', $course_chosen_students);
        $stmt->bindValue(':description', $course_description);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}
?>