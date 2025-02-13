<!--Purpose: Header-->
<?php session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<link rel="stylesheet" type="text/css" href="/webpage/assets/js/styles.css">
<style>
    .main-nav {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        background: black;
        color: white;
    }

    .header-content {
        display: flex;
        align-items: center;
        width: 100%;
        margin-left: 10px;
    }

    .home {
        color: white;
        text-decoration: none;
    }

    .right-navigation {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-left: auto;
    }

    .right-navigation a, .logout-button {
        color: white;
        text-decoration: none;
        background: none;
        border: none;
        cursor: pointer;
        font-size: large;
        margin-right: 5px;
    }

    .right-navigation a:hover, .logout-button:hover {
        text-decoration: underline;
    }
</style>

<header>
    <nav class="main-nav">
        <div class="header-content">
            <h1><a href="/webpage" class="home">Ace Training</a></h1>
            <div class="right-navigation">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    $role = $_SESSION['role'] ?? '';
                    $dashboard_url = '';
                    if ($role === 'admin') {
                        $dashboard_url = '/webpage/pages/admin_dashboard.php';
                    } elseif ($role === 'tutor') {
                        $dashboard_url = '/webpage/pages/tutor_dashboard.php';
                    } elseif ($role === 'student') {
                        $dashboard_url = '/webpage/pages/student_dashboard.php';
                    }
                    ?>
                    <?php if ($dashboard_url): ?>
                        <a href="<?php echo $dashboard_url; ?>">Dashboard</a>
                    <?php endif; ?>
                    <a href="/webpage/config/profile.php">Profile</a>
                    <a href="/webpage/config/timetable.php">Timetable</a>
                    <a href="/webpage/config/assignments.php">Assignments</a>
                    <form id="logout_form" action="/webpage/config/logout.php" method="POST" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="logout-button" id="logout-button">Logout</button>
                    </form>
                <?php else: ?>
                    <a href="/webpage/pages/showlogin.php" class="log-in">Log In</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>
<script src="/webpage/assets/js/main.js"></script>