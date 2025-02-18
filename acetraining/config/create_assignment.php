<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_name = $_POST['name'];
    $assignment_description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $file_path = $_POST['file_path'];

    try {
        global $pdo;

        $stmt = $pdo->prepare("INSERT INTO assignments (name, description, due_date, file_path) VALUES (:name, :description, :due_date, :file_path)");
        $stmt->bindValue(':name', $assignment_name);
        $stmt->bindValue(':description', $assignment_description);
        $stmt->bindValue(':due_date', $due_date);
        $stmt->bindValue(':file_path', $file_path);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
    echo "Assignment created successfully!";
    exit;
}
?>