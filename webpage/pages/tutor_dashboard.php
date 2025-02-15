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