<?php
include 'components/header.php';

?>
<style>
    html {
        background: linear-gradient(180deg, rgba(48, 47, 47, 0.61) 0%, rgba(19, 18, 18, 0.75) 50%);
        overflow-x: hidden;
    }
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }   
    
    main {
        margin: 0 auto;
        width: 80%;
    }

    #home {
        text-align: center;
        margin-top: 2rem;
    }
    #home h1 {
        margin: 1rem;
        transform: uppercase;
    }
    #home p {
        margin-top: 10rem;
        align-items: right;
    }

    #information {
        text-align: center;
        margin-top: 16rem;
        height: 60vh;
    }
</style>

<main>
    <section id="home">
        <h1>Welcome to Our Website, Ace Training</h1>
        <p>Click Log in to begin.</p>
        <p>can use sections  to add information here</p>
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