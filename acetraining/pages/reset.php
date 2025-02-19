<?php
session_start();

include __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../lib/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="/acetraining/assets/css/global.css">
    <link rel="stylesheet" type="text/css" href="/webpage/assets/css/reset.css">

</head>

<body>
    <!-- Reset Password -->
    <div class="reset_container">
        <div class="reset">
            <form action="/acetraining/config/reset_pass.php" method="POST" name="reset_pass_form"
                class="reset_pass_form">
                <label for="reset_email_check" name="reset_email_check" class="reset_email_check">Email</label>
                <input type="email" name="email" required>
                <label for="new_password" name="new_password" class="new_passowrd">New Password</label>
                <input type="password" name="new_password" required>
                <br>
                <label for="confirm_password" name="confrim_password" class="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" required>
                <br>
                <button type="submit" name="request_button" class="request_button">Request Reset</button>
            </form>
        </div>
    </div>
    <script src="/acetraining/assets/js/main.js"></script>
</body>

</html>
<?php include __DIR__ . '/../components/footer.php'; ?>