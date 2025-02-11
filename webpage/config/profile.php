<?php 
session_start();
include '../components/header.php';
require_once __DIR__ . '/../lib/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /webpage/pages/showlogin.php');
    exit();
}
$user_id = $_SESSION['user_id'];
try {
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
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <link rel="stylesheet" type="text/css" href="/webpage/assets/js/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            justify-content: center;
            align-items: center;

        }

    </style>
</head>
<body>
    <div class="head-container">
        <h1>Welcome to Your Profile</h1>
        <p>Here you can view your profile information</p>
    </div>
    <div class="profile-container" >
        <table class="profile-table">
            <tbody class="table-body">
                <tr = class="table-row">
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
                    <th class="table-head">Coures Enrolled or Teaching</th>
                    <td class="table-data"><?php echo htmlspecialchars($courses); ?></td>
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
<?php include '../components/footer.php'; ?>