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
        button {
        color: white;
        background-color: red;
        padding: 10px 15px;
        text-align: right;
        text-decoration: none;
        text-transform: uppercase;
        display: inline-block;
        cursor: pointer;
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
<?php include __DIR__ . '/../components/header.php'; ?>

    <?php if (isset($_GET['error'])) echo "<p style='color:red;'>". htmlspecialchars($_GET['error']) ."</p>"; ?>

</body>
</html>