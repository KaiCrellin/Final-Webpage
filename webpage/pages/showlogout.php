//Purpose: logout page for uses to log out of their account.
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    
</head>
<body>

<?php
session_start();
$crsf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<form id="logout_form" method="POST" action="../config/logout.php">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <button type="submit" id="logout_button" aria-label="Log Out of your Account">Logout</button>
</form>

<script>
    const logoutButton = document.getElementById("logout_button");


    logoutButton.addEventListener("click", function(event) {
        if(confirm("Are you sure you want to log out?")) {
            document.getElementById("logout_form").submit();
        } else {
            event.preventDefault();
        }
    });
</script>
    
    
</body>
</html>