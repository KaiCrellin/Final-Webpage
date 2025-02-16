<?php
session_start();
include __DIR__ . '/components/header.php';
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ace Training</title>
    <link rel="stylesheet" href="/acetraining/assets/css/index.css">
</head>

<body>
    <div class="main_home">
        <h1 class="main_home_heading1">Welcome To Our Website, Ace Training</h1>
        <p1 class="main_home_para1">Click log in to begin</p1>
    </div>
    <div class="main_home_information">
        <h2 class="main_home_heading2">Information about the website</h2>
        <p2 class="main_home_parra2">This website is a University Assignment, produced to handle
            course information for students and tutors. You can check
            course informationa and assignment deadlines, log in, log out,
            download and upload files within the website an tutors can do all the same
            while having certain privilges the student wont.</p2>
    </div>
    <script src="/acetraining/assets/js/main.js"></script>
</body>

</html>
<?php
include __DIR__ . '/components/footer.php';
?>