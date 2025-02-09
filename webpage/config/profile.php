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
    <style>
        html {
            background: linear-gradient(180deg, rgba(48, 47, 47, 0.61) 0%, rgba(19, 18, 18, 0.75) 50%);
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            justify-content: center;
            align-items: center;
        }
        .table {
            
            border-collapse: collapse;
            width: 100%;
            display: flex;
            justify-content: center;

        }
        tbody {   
            
        }
        tr {
            border-bottom: 1px solid #ddd;
            
        }
        th, td {;
            padding: 8px;
            background: whitesmoke;
            background-clip: padding-box;
        }
        .head {
            text-align: center;
            text-transform: uppercase;
            padding: 10px;
        }


    </style>
</head>
<body>
    <div class="head">
        <h1>Welcome to Your Profile</h1>
        <p>Here you can view your profile information</p>
    </div>
    <div class="container" >
        <table class="table" >
            <tbody>
                <tr>
                    <th>Name</th>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                </tr>
                <tr>
                    <th>Coures Enrolled or Teaching</th>
                    <td><?php echo htmlspecialchars($courses); ?></td>
                </tr>
                <tr>
                    <th>Account Created</th>
                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                </tr>
            </tbody>
        </table>  
    </div>
</body>
</html>