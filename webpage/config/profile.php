<?php 
session_start();
include '../components/header.php';
require_once __DIR__ . '/../lib/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /webpage/pages/showlogin.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name, email, created_at FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$user) {
    echo "user not found";
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            display: flex;
            justify-content: center;

        }
        tbody {   
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
    <table>
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
</body>
</html>