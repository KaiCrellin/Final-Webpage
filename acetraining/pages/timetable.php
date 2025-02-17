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

    $class = null;
    switch ($user['role']) {
        case 'Student':
            try {
                $stmt = $pdo->prepare("SELECT c.class_name, c.class_date, c.class_time, c.duration 
                                       FROM classes c 
                                       JOIN students s ON c.student_id = s.id 
                                       WHERE s.user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                $class = $stmt->fetch(PDO::FETCH_ASSOC);
                $user['class_name'] = $class['class_name'] ?? 'No Class Assigned';
                $user['class_date'] = $class['class_date'] ?? 'No Class Assigned';
                $user['class_time'] = $class['class_time'] ?? 'No Class Assigned';
                $user['duration'] = $class['duration'] ?? 'No Class Assigned';

            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                exit();
            }
            break;
        case 'Tutor':
            try {
                $stmt = $pdo->prepare("SELECT c.class_name, c.class_date, c.class_time, c.duration 
                                       FROM classes c 
                                       JOIN tutors t ON c.tutor_id = t.id 
                                       WHERE t.user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                $class = $stmt->fetch(PDO::FETCH_ASSOC);
                $user['class_name'] = $class['class_name'] ?? 'No Class Assigned';
                $user['class_date'] = $class['class_date'] ?? 'No Class Assigned';
                $user['class_time'] = $class['class_time'] ?? 'No Class Assigned';
                $user['duration'] = $class['duration'] ?? 'No Class Assigned';
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
</head>

<body>
    <div class="timetable-container">
        <h1>Welcome to your Timeline, Here you can see your classes</h1>
    </div>
    <div class="timetable-content">
        <table class="timetable-table">
            <tbody class="timetable-body">
                <tr class="table-row">
                    <th>Class</th>
                    <td class="table-data"><?php echo htmlspecialchars($user['class_name']) ?>
                    </td>
                </tr>
                <tr>
                    <th>Class Date</th>
                    <td><?php echo htmlspecialchars($user['class_date']) ?></td>
                </tr>
                <tr>
                    <th>Class Time</th>
                    <td><?php echo htmlspecialchars($user['class_time']) ?></td>
                </tr>
                <tr>
                    <th>Duration</th>
                    <td><?php echo htmlspecialchars($user['duration']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php include __DIR__ . '/../components/footer.php'; ?>