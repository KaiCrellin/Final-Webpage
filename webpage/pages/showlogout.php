<?php
session_start();
require_once __DIR__ . '/../lib/db.php';

$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/webpage/assets/js/styles.css">
</head>
<body>
<?php include __DIR__ . '/../components/header.php'; ?>

    <?php if (isset($_GET['error'])) echo "<p style='color:red;'>". htmlspecialchars($_GET['error']) ."</p>"; ?>
    <script src="/webpage/assets/js/main.js"></script>
</body>
</html>