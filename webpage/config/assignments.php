<!--Purpose: Assignment Page and logic-->
<?php 
session_start();
require_once __DIR__ . '/../lib/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments</title>
    <link rel="stylesheet" type="text/css" href="/webpage/assets/js/styles.css">
</head>
<body>
    <?php include '../components/header.php'; ?>
    <div class="assignments-container">
        <section class="assignments-header">
            <h3>Assignments</h3>
            <p>Here are your Active Assignments</p>
            <button id="assignment-create" onclick="openModel()">Create Assignment</button>
        </section>
        <table class="assignments-table">
            <thead>
                <tr>
                    <th>Assignment Name</th>
                    <th>Assignment Description</th>
                    <th>Due Date</th>
                    <th>Resources</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    global $pdo;

                    $stmt = $pdo->prepare("SELECT * FROM assignments WHERE course_id = :course_id");
                    $stmt->execute(['course_id' => $_SESSION['course_id']]);
                    while ($assignment = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($assignment['assignment_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($assignment['assignment_description']) . "</td>";
                        echo "<td>" . htmlspecialchars($assignment['assignment_due_date']) . "</td>";
                        if (!empty($assignment['file_path'])) {
                            echo "<td><a href='" . htmlspecialchars($assignment['file_path']) . "'>Download</a></td>";
                        } else {
                            echo "<td>No File</td>";
                        }
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='4'>No Assignments</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div id="assignment-model" class="model">
        <div class="model-content">
            <span class="close" onclick="closeModel()">&times;</span>
            <form action="/webpage/pages/create_assignment.php" method="POST" enctype="multipart/form-data">
                <label for="assignment-name">Assignment Name:</label>
                <input type="text" name="assignment_name" id="assignment-name" required>
                <label for="assignment-description">Assignment Description:</label>
                <input type="text" name="assignment_description" id="assignment-description" required>
                <label for="assignment-due-date">Due Date:</label>
                <input type="date" name="assignment_due_date" id="assignment-due-date" required>
                <label for="assignment-resources">Add Resources</label>
                <input type="file" name="assignment_file" id="assignment-uploads">
                <button type="submit">Create Assignment</button>
            </form>
        </div>
    </div>
    
    <script src="/webpage/assets/js/main.js"></script>
    <?php include '../components/footer.php'; ?>
</body>
</html>