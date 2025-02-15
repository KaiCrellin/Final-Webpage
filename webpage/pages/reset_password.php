
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" type="text/css" href="/webpage/assets/js/styles.css">

    <style>
        html {
            background: linear-gradient(180deg, rgba(48, 47, 47, 0.61) 0%, rgba(19, 18, 18, 0.75) 50%);
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
    </style>
</head>
<body>
    <div class="reset_container">
        <div class="reset">
            <form action="reset_pass.php" method="POST" name="reset_pass_form" class="reset_pass_form">
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
    <script src="/webpage/assets/js/main.js"></script>
</body>
</html>

