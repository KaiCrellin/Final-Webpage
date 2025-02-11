<?php
require_once __DIR__ . '/../lib/db.php';



error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<!DOCTYPE html>
<html lang="en" style="overflow: hidden;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="/webpage/assets/js/styles.css">
</head>
<?php include '../components/header.php'; ?>
<body>
   
    <div class="showlogin-container">
        <form action="/webpage/config/login.php" method="POST" class="showlogin-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <label for="email" class="label-input">Email:</label>
            <input type="text" id="email" name="email" placeholder="Please enter your Email" required>
            <label for="password" class="label-input">Password:</label>
            <input type="password" id="password" name="password" placeholder="Please enter your password" required>
            <label for="togglePassword" class="label-input">Show Password</label>
            <input type="checkbox" id="togglePassword" onclick="togglePasswordVisibility()">
            <p><a href="request_pass.php">Forgot Password?</a></p>
            <button type="submit" class="login-input">Login</button>
        </form>

        <?php if (isset($_GET['error'])): ?>
            <p class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <h2 class="information-user"></h2>
        <table class="table-information">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
            <?php
            try {
                $stmt = $pdo->query("SELECT id, name, email, password FROM users");
                while ($row = $stmt->fetch()) {
                    $user_id = $row['id'];
                    $role = '';

                    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE user_id = :user_id");
                    $stmt2->execute(['user_id' => $user_id]);
                    if ($stmt2->fetchColumn() > 0) {
                        $role = 'Admin';
                    }

                    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM tutors WHERE user_id = :user_id");
                    $stmt2->execute(['user_id' => $user_id]);
                    if ($stmt2->fetchColumn() > 0) {
                        $role = 'Tutor';
                    }

                    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM students WHERE user_id = :user_id");
                    $stmt2->execute(['user_id' => $user_id]);
                    if ($stmt2->fetchColumn() > 0) {
                        $role = 'Student';
                    }

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($role) . "</td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='4'>Database error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            }
            ?>
        </table>
    </div>
    
    <script src="/webpage/assets/js/main.js"></script>
</body>
<?php include '../components/footer.php'; ?>
</html>