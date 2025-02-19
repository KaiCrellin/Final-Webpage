<?php
session_start();
require_once __DIR__ . '/../lib/db.php';
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ace Training</title>
    <link rel="stylesheet" href="/acetraining/assets/css/header.css">
</head>

<body>
    <header>
        <a href="/acetraining" class="home-link">
            <h1 class="home-title">Ace Training</h1>
        </a>
        <nav class="header-navigation">
            <div class="nav-content" id="nav-content-dropdown">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    $role = $_SESSION['role'] ?? '';
                    $dashboard_url = '';
                    if ($role === 'admin') {
                        $dashboard_url = '/acetraining/pages/admin_dashboard.php';
                    } elseif ($role === 'tutor') {
                        $dashboard_url = '/acetraining/pages/tutor_dashboard.php';
                    } elseif ($role === 'student') {
                        $dashboard_url = '/acetraining/pages/student_dashboard.php';
                    }
                    ?>
                    <?php if ($dashboard_url): ?>
                        <a class="nav-item" href="<?php echo $dashboard_url; ?>">Dashboard</a>
                    <?php endif; ?>
                    <a class="nav-item" href="/acetraining/pages/profile.php">Profile</a>
                    <a class="nav-item" href="/acetraining/pages/timetable.php">Timetable</a>
                    <form action="/acetraining/config/logout.php" method="POST" class="logout-form">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <button type="submit" class="nav-item logout-button">Logout</button>
                    </form>
                <?php else: ?>
                    <div class="login-button">
                        <a class="nav-item login-link" href="/acetraining/pages/showlogin.php">Log in</a>
                    </div>
                <?php endif; ?>
            </div>
        </nav>
        <button class="dropdown-button" onclick="toggleDropdown()">☰</button>
    </header>
    <script src="/acetraining/assets/js/main.js"></script>
</body>

</html>