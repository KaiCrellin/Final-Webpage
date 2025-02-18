<?php
session_start();
include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/acetraining/assets/css/create_courses.css">
</head>

<body>
    <form action="course_block.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="files">Add Files</label>
        <input type="file" id="files" name="files[]" multiple><br><br>

        <label for="students">Choose students to display to</label>
        <select id="students" name="students[]" multiple required>
            <?php
            $stmt = $pdo->prepare("SELECT id, name FROM students");
            $stmt->execute();
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($students as $student) {
                echo '<option value="' . htmlspecialchars($student['id']) . '">' . htmlspecialchars($student['name']) . '</option>';
            }
            ?>
        </select><br><br>

        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <button type="submit">Create Course Block</button>
    </form>
    <script src="/acetraining/assets/js/main.js"></script>
</body>

</html>