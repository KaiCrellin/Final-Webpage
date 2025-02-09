<?php
include_once __DIR__ . '/../pages/showlogout.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        main {
            display: flex;
            justify-content: center;
            align-items: top;
            color: black;
        }
        h1 {
            font-size: 3rem;
        }
    </style>
</head>

<body>
    <main>
        <section>
            <h1>Tutor Dashboard</h1>
            <p>Welcome to the Tutor Dashboard</p>
        </section>
    </main>
</body>
</html>