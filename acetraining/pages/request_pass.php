<?php
session_start();
require_once __DIR__ . '/../lib/db.php';
include __DIR__ . '/../components/header.php';
loadenv(__DIR__ . '/../.env');
$csrf_token = $_SESSION['csrf_token'] || $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/acetraining/assets/css/forgotten_pass.css">
</head>

<body>
    <div class="forgotten-container">
        <form action="/acetraining/config/request.php" id="password-reset-form" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <label for="email" class="email">Email</label required>
            <input type="email" name="email" class="forgotten_email_input" required>
            <button type="submit" class="forgotten_button">Request Password Reset</button>
        </form>
    </div>
</body>

</html>