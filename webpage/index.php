<?php
include 'components/header.php';

?>
<style>
    * {
        overflow-x: hidden;
        color:black;
        background-color: #f4f4f4;
        
    }
    .logo
    {
        left: 0;
        height: 20rem;
    }
    .logo nav {
        text-transform: uppercase;
        display: inline;
        margin-left: 4rem;

    }
    .logo nav ul {
        padding: 0;
        justify-items: ;
    }
    #home {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 28rem;
        font-family:Arial, Helvetica, sans-serif;
        
    }
    #home h1 {
        align-content: left;
        margin-bottom: 10rem;
        
    }
    #home p {
        font-size: 20px;

    }
</style>

<main>
    <section id="home">
        <h1>Welcome to Our Website, Ace Training</h1>
        <p>Click Log in to begin.</p>
    </section>
    <section id="information">
        <h2>Information about the website</h2>
        <p1> This website is a University Assignment, produced to handle
            course information for students and tutors. You can check
            course informationa and assignment deadlines, log in, log out,
            download and upload files within the website an tutors can do all the same
            while having certain privliges the student wont.
        </p1>
    </section>
</main>



<?php
include 'components/footer.php';
?>