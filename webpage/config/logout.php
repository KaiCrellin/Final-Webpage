<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo "Invalid CSRF Token";
        exit();
    }
} else {
    echo "invalid request method";
}
echo "Logging Out...";

$_SESSION = array();
session_destroy();

header("Location: ../pages/showlogin.php");
exit();
?>