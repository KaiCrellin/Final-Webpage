<?php
session_start();
require_once __DIR__ . '/../lib/db.php';

$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background: whitesmoke;

        }
        form {
            background: white;
            display: block;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            margin: 0 auto;
            margin-top: 75px;
        }
        label {
            text-size-adjust: 100%;
            display: block;
            font-size: 16px;
            margin-top: 10px;
        }
        input {
            margin-top: 10px;
            padding 10px;
            box-shadow: 10 10 10px rgba(0, 0, 0, 5);
            width: 60%;
            margin: 0 auto;
            display: block;

        }
        button {
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            font-size: 16px;
            text-decoration-thickness: none;
            color: white;
            text-transform: uppercase;
        }
        button:hover {
            background-color: #0056b3;
        }
       
    </style>
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById('password');
            var toggleCheckbox = document.getElementById('togglePassword');
            if (toggleCheckbox.checked) {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li>
                    <a href="/../webpage/index.php">Home</a>
                </li>
            </ul>
        </nav>
    </header>
    <form action="../config/login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" placeholder="Please enter your Email" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Please enter your password" required>
        <br>
        <input type="checkbox" id="togglePassword" onclick="togglePasswordVisibility()">
        <label for="togglePassword">Show Password</label>
        <br>
        <p><a href="request_pass.php">Forgot Password?</a></p>
        <button type="submit">Login</button>
    </form>

    <?php if (isset($_GET['error'])) echo "<p style='color:red;'>". htmlspecialchars($_GET['error']) ."</p>"; ?>

    <h2>All Users . Just enter normal example pass if testing as hashed passwords are dispalyed</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Password</th>
        </tr>
        <?php
        try {
            $stmt = $pdo->query("SELECT id, name, email, password FROM users");
            while ($row = $stmt->fetch()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['password']) . "</td>";
                echo "</tr>";
            }
        } catch (PDOException $e) {
            echo "Database error: " . htmlspecialchars($e->getMessage());
        }
        ?>
    </table>
</body>
</html>