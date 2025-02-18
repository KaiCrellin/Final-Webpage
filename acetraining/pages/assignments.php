<?php
session_start();
include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /acetraining/pages/showlogin.php');
    exit();
}
$user_id = $_SESSION['user_id'];
try {
    global $pdo;

    $stmt = $pdo->prepare("SELECT name, email, created_at FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

if (!$user) {
    echo "user not found";
    exit();
}

if ($user) {
    try {
        $role = '';

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        if ($stmt->fetchColumn() > 0) {
            $role = 'Admin';
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tutors WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        if ($stmt->fetchColumn() > 0) {
            $role = 'Tutor';
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        if ($stmt->fetchColumn() > 0) {
            $role = 'Student';
        }

        $user['role'] = $role;

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }

    switch ($user['role']) {
        case 'Student':
            try {
                $stmt = $pdo->prepare("SELECT a.name, a.description, a.due_date, a.file_path
                                       FROM assignments a
                                       JOIN students s ON a.student_id = s.id
                                       WHERE s.user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($assignments) {
                    $user['name'] = $assignments[0]['name'];
                    $user['description'] = $assignments[0]['description'];
                    $user['due_date'] = $assignments[0]['due_date'];
                    $user['file_path'] = $assignments[0]['file_path'];
                } else {
                    $user['name'] = 'No Assignments';
                    $user['description'] = 'No Assignments';
                    $user['due_date'] = 'No Assignments';
                    $user['file_path'] = 'No Assignments';
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                exit();
            }
            break;
        case 'Tutor':
            try {
                $stmt = $pdo->prepare("SELECT a.name, a.description, a.due_date, a.file_path
                                        FROM assignments a
                                        JOIN tutors t ON a.tutor_id = t.id
                                        WHERE t.user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($assignments) {
                    $user['name'] = $assignments[0]['name'];
                    $user['description'] = $assignments[0]['description'];
                    $user['due_date'] = $assignments[0]['due_date'];
                    $user['file_path'] = $assignments[0]['file_path'];
                } else {
                    $user['name'] = 'No Assignments';
                    $user['description'] = 'No Assignments';
                    $user['due_date'] = 'No Assignments';
                    $user['file_path'] = 'No Assignments';
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                exit();
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/acetraining/assets/css/assignments.css">
</head>

<body>
    // create button and form to Create Assignments. IF TUTOR.
    <div class="assignments-container">
        <h1>Welcome to your Assignments, Here you can see your Active Assignments</h1>
    </div>
    <div class="assignments-content">
        <table class="assignments-table">
            <tbody class="assignments-body">
                <tr class="table-row">
                    <th>Assignment</th>
                    <td class="table-data"><?php echo htmlspecialchars($user['name']) ?>
                    </td>
                </tr>
                <tr class="table-row">
                    <th class="table-head">Assignment Descriptiom</th>
                    <td class="table-data"><?php echo htmlspecialchars($user['description']) ?></td>
                </tr>
                <tr>
                    <th class="table-head">Due Date</th>
                    <td class="table-data"><?php echo htmlspecialchars($user['due_date']) ?></td>
                </tr class=table-row>
                <tr class="table-row">
                    <th class="table-head">Assignment Files</th>
                    <td class="table-data"><?php echo htmlspecialchars($user['file_path']) ?></td>
            </tbody>
        </table>
    </div>
    <script src="/acetraining/assets/js/main.js"></script>
</body>

</html>
<?php include __DIR__ . '/../components/footer.php'; ?>