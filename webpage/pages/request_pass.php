<!--Purpose: Page for requesting a new password-->
<?php 
session_start();
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        html {
            background: linear-gradient(180deg, rgba(48, 47, 47, 0.61) 0%, rgba(19, 18, 18, 0.75) 50%);
        }
        body {
            font-family: Ariel, sans serif;
            margin: 20px;
            padding: 0;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <form action="../config/forgotten_pass.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token;?>">
        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Please enter your email" required>
        <br>
        <button type="submit"><a href="reset_password.php">Request Password reset</a></button>
</form>



<div class="forgotten_pass_container">
    <div class="forgotten_form">
        <form action="../config/forgotten_pass.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token;?>">
            <label for="email" class="forgotten_email_label">Email</label>
            <input type="email" name="email" class="forgotten_email_input" placeholder="Please enter your email" required>
            <br>
            <button type="submit" class="forgotten_button"><a href="reset_password.php">Request Password Reset</a></button>
        </form>
    </div>
</div>
</body>
</html>