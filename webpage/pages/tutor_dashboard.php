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
        .logout-button {
            color: white;
            background-color: red;
            padding: 10px 15px;
            margin: 10px 2px;
            text-align: right;
            text-decoration: none;
            text-transform: uppercase;
            display: inline-block;
            cursor: pointer;
        }
        .logout-button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <header>
        <form action="../config/logout.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <button type="submit" class ="logout-button">Logout</button>
        </form>
    </header>
    <main>
        <section>
            <h1>Welcome To The Tutor Dashboard</h1>
        </section>
    </main>
</body>
</html>