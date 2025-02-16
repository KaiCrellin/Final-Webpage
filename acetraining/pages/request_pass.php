<?php
session_start();
require_once __DIR__ . '/../lib/db.php';
include __DIR__ . '/../components/header.php';
loadenv(__DIR__ . '/../.env');
$_SESSION['csrf_token'] = $csrf_token ?? bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/acetraining/assets/css/forgotten_pass.css">
</head>

<body>
    <div class="forgotten-container">
        <form id="password-reset-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <label for="email" class="forgotten_email_label">Email</label required>
            <input type="email" name="email" class="forgotten_email_input" required>
            <button type="submit" class="forgotten_button">Request Password Reset</button>
        </form>
    </div>
    <script>

        document.getElementById('password-reset-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            fetch('/acetraining/api.php/password-reset', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message)
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
</body>

</html>
<?php include __DIR__ . '/../components/footer.php'; ?>