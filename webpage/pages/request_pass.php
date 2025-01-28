//Purpose: To request a password reset.
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
</head>
<body>
    <form action="../config/forgotten_pass.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token;?>">]
        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Please enter your email" required>
        <br>
        <button type="submit"><a href="reset_password.php">Request Password reset</a></button>
</form>
</body>
</html>