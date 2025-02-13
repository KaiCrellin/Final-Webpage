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
    <form action="../config/forgotten_pass.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token;?>">
        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Please enter your email" required>
        <br>
        <button type="submit"><a href="reset_password.php">Request Password reset</a></button>
</form>
</body>
</html>