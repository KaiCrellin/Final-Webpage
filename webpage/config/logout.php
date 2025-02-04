<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo "Invalid CSRF Token";
        exit();
    }

    $_SESSION = array();

    session_destroy();

    header('Location: /webpage');
    exit();
} else {
    http_response_code(405);
    echo "invalid request method";
    exit();
}

?>