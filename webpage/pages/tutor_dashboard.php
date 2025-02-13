<!--Purpose: Tutor Dashboard-->
<?php
session_start();
include_once __DIR__ . '/../pages/showlogout.php';
require_once __DIR__ . '/../lib/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/webpage/assets/js/styles.css">
    <style>
        .tutor-dashboard-head {
            display: flex;
            font-size: 1.5rem;
            justify-content: center;
            align-items: center;
        }
        .dashboard_tutor {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .dash_tutor {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 1rem;
            padding: 1rem;
            border: 1px solid black;
            border-radius: 5px;
            background-color: #f1f1f1;
        }

        .mobile_menu_courses {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .mobile_menu_courses, .mobile_menu_item {
            display: flex;
            justify-content: center;
            align-items: center;
        }
            
    </style>
</head>

<body>
    <main>
        <section class="tutor-dashboard-head">
            <h1>Tutor Dashboard</h1>
            <p>Welcome to the Tutor Dashboard</p>
        </section>
    </main>


<div class="dashboard_tutor">
    <div class="dash-tutor">
        <section class="course-head">
            <h1>Courses</h1>
            <p>Here is the Courses that you teach</p>
        </section>
        <div class="card">
            <div class="mobile_menu_courses">
                <div class="mobile_menu_item">
                    <h2>Course Name</h2>
                    <p>Course Description</p>
                </div>
                <div class="mobile_menu_item">
                    <h2>Course Name</h2>
                    <p>Course Description</p>
                </div>
                <div class="mobile_menu_item">
                    <h2>Course Name</h2>
                    <p>Course Description</p>
                </div>
            </div>
        </div>
    </div>   
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
<script src="/webpage/assets/js/main.js"></script>
</body>
</html>