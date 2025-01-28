//Purpose: login page for users to log inot their account.
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Ariel, sans serif;
            margin: 20px;
            padding: 0;
            background-color: #f9f9f9;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd ;
            border-radius: 5px;
            max-width: 400px;
            margin: auto;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        button {
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php
session_start();
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

    <form action="../config/login.php" method="POST">
        <input type="hidden" name="crsf_token" value="<?php echo $csrf_token;?>">
        <label for="username">Username</label>
        <input type="text" name="username" placeholder="Please enter your username" required>
        <br>
        <label for="password">Password</label>
        <input type="text" name="password" placeholder="Please enter your username" required>
        <br>
        <p><a href="request_pass.php">Forgot Password?</a></p>
        <button type="submit">Login</button>
    </form>

    <?php if(isset($_GET['error'])) echo "<p style='color:red;'>". htmlspecialchars($_GET['error'])."</p>"; ?>
    
</body>
</html>