
<style>
    header {
        background: linear-gradient(180deg, rgba(48, 47, 47, 0.61) 0%, rgba(19, 18, 18, 0.75) 50%);
        color: white;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        
    }
    .logo a {
        color: white;
        text-decoration: none;
        font-size: 1.5rem;
    }

    .nav ul {
        display: flex;
        list-style: none;
    }

    .nav ul li {
        margin-right: 1rem;
    }

    .nav ul li a {
        text-decoration: none;
        color: white;
        
    }

    .nav ul li a:hover {
        color: lightgray;
    }

    .logout-button {
        background-color: blue;
        border: none;
        color: white;
        cursor: pointer;
        text-transform: uppercase;
    }

    .logout-button:hover {
        color: lightblue;
    }
   
   
    
</style>

<header>
    <div class="logo"><a href="/webpage">Ace Training</a></div>
    <nav class="nav">
        <ul>
            <li class="input" id="input"><a href="/webpage/pages/aboutus.php">About us</a></li>
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
                    <li class="input" id="input"><a href="<?php echo $dashboard_url; ?>">Dashboard</a></li>
                <?php endif; ?>
                <li><a href="/webpage/config/profile.php">Profile</a></li>
                <li><a href="/webpage/config/timetable.php">Timetable</a></li>
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