/* Purpose: Logic for logging out a user. can be moved to "api.php" later to configure handling of requests
and responses to the database */ 
<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo "Invalid CSRF Token";
        exit();
    }
}

$_SESSION = array();
session_destroy();

header("Location: ../pages/showlogin.php");
exit();
?>