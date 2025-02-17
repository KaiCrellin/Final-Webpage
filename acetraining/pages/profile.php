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
                $stmt = $pdo->prepare("SELECT c.name FROM courses c JOIN students s ON c.student_id = s.id WHERE s.user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                $course = $stmt->fetch(PDO::FETCH_ASSOC);
                $user['course_name'] = $course['name'] ?? 'No Course Enrolled';
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                exit();
            }
            break;
        case 'Tutor':
            try {
                $stmt = $pdo->prepare("SELECT name FROM courses WHERE tutor_id = :tutor_id");
                $stmt->execute(['tutor_id' => $user_id]);
                $course = $stmt->fetch(PDO::FETCH_ASSOC);
                $user['course_name'] = $course['name'] ?? 'No Course Assigned';
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                exit();
            }
            break;
        default:
            $user['course_name'] = 'N/A';
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
    <link rel="stylesheet" href="/acetraining/assets/css/profile.css">
</head>

<body>
    <div class="head-container">
        <h1>Welcome to Your Profile,<?php echo htmlspecialchars($user['name']); ?></h1>
        <p>Here you can view your profile information </p>
    </div>
    <div class="profile-container">
        <table class="profile-table">
            <tbody class="table-body">
                <tr class="table-row">
                    <th class="table-head">Name</th>
                    <td class="table-data"><?php echo htmlspecialchars($user['name']); ?></td>
                </tr>
                <tr class="table-row">
                    <th class="table-head">Email</th>
                    <td class="table-data"><?php echo htmlspecialchars($user['email']); ?></td>
                </tr>
                <tr class="table-row">
                    <th class="table-head">Role</th>
                    <td class="table-data"><?php echo htmlspecialchars($user['role']); ?></td>
                </tr>
                <tr class="table-row">
                    <th class="table-head">Course Enrolled or Teaching</th>
                    <td class="table-data"><?php echo htmlspecialchars($user['course_name']); ?></td>
                </tr class="table-row">
                <tr>
                    <th class="table-head">Account Created</th>
                    <td class="table-data"><?php echo htmlspecialchars($user['created_at']); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php include __DIR__ . '/../components/footer.php'; ?>