<?php
session_start();
?>

<style>
       .nav {
        color: black;
    }
    .logo {
        font-size: 2rem;
        color: black;
        margin-left: 4rem;
        left: 0;
        overflow-x: hidden;
        background-color: #f4f4f4;
    }
    .logo {
        text-transform: uppercase;
        display: inline;
        margin-left: 4rem;
    }
    nav {
        display: flex;
        align-items: center;
        text-transform: uppercase;
        margin-left: 4rem;
        justify-content: space-between;
        float: right;
    }
    .nav ul {
        display: flex;
        list-style-type: none;
        padding: 0;
        margin: 0;
    }
    .nav li {
        margin: 0 1rem;
    }
    .logout-button {
        color: white;
        background-color: red;
        padding: 10px 15px;
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

<header>
    <div class="logo"><a href="/webpage">Ace Training</a></div>
    <nav class="nav">
        <ul>
            <li><a href="/webpage/pages/aboutus.php">About Us</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                if (!isset($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
                $csrf_token = $_SESSION['csrf_token'];

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
                    <li><a href="<?php echo $dashboard_url; ?>">Dashboard</a></li>
                <?php endif; ?>
                <li><a href="/webpage/config/profile.php">Profile</a></li>
                <li>
                    <form id="logout_form" action="/webpage/config/logout.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="logout-button" id="logout-button">Logout</button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="/webpage/pages/showlogin.php">Log In</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const logoutButton = document.getElementById("logout-button");

        if (logoutButton) {
            logoutButton.addEventListener("click", function(event) {
                if (!confirm("Are you sure you want to log out?")) {
                    event.preventDefault();
                }
            });
        }
    });
</script>