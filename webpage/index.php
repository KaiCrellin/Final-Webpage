<!--Purpose: Main HGome Page-->
<?php
include 'components/header.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $csrf_token = $_SESSION['csrf_token'];
} else {
    $user_id = '';
    $csrf_token = '';
}

?>

<link rel="stylesheet" type="text/css" href="/webpage/assets/js/styles.css">
<main class="main_home">
    <div class="home_container">
        <section>
            <h1 class="main_home_heading">Welcome To Our Website, Ace Training</h1c>
            <p class="main_home_para">Click Log in to begin</pc>
        </section>
    </div>
    <div class="main_home_information">
        <section>
            <h2>information about the website</h2>
            <p2> This website is a University Assignment, produced to handle
            course information for students and tutors. You can check
            course informationa and assignment deadlines, log in, log out,
            download and upload files within the website an tutors can do all the same
            while having certain privliges the student wont.</p2>
        </section>
    </div>
</main>
<?php
include 'components/footer.php';
?>