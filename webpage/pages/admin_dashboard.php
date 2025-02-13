<!--Purpose: Admin Dashboard-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width
    , initial-scale=1.0">
    <title>Administrator</title>
    <style>
        html {
            background: linear-gradient(180deg, rgba(48, 47, 47, 0.61) 0%, rgba(19, 18, 18, 0.75) 50%);
        }
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
            height: 100vh;
            color: white;
        }
        .Welcome {
            font-size: 3rem;
        }
    </style>
</head>
<body>
<?php include '../components/header.php'; ?>

<main>
    <section>
        <p><h1 class="Welcome">Welcome to Admin Dashboard</h1></p>
    </section>
</main>
    
</body>
</html>