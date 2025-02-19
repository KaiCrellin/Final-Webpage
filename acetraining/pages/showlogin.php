<?php
session_start();

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Clear any existing user session
if (isset($_SESSION['user_id'])) {
    session_destroy();
    session_start();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
include __DIR__ . '/../components/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ace Training</title>
    <link rel="stylesheet" href="/acetraining/assets/css/login.css">
    <style>
        .test-accounts {
            max-width: 400px;
            margin: 2rem auto;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
        }

        .test-accounts table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .test-accounts th,
        .test-accounts td {
            padding: 0.5rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .test-accounts h3 {
            color: #666;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <!-- Login -->
    <div class="login-container">
        <div class="login-header">
            <h1 class="login-title">Login</h1>
            <?php if (isset($_GET['error'])): ?>
                <div class="login-error">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
        </div>

        <form action="/acetraining/config/login.php" method="POST" class="login-form">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required
                    value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="login-button">Login</button>
        </form>

        <div class="forgot-password">
            <a href="/acetraining/pages/request_pass.php">Forgot your password?</a>
        </div>
    </div>
    <!-- Test Accounts -->
    <div class="test-accounts">
        <h3>Test Accounts</h3>
        <table>
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Password</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Tutor</td>
                    <td>john@test.com</td>
                    <td>test123</td>
                </tr>
                <tr>
                    <td>Tutor</td>
                    <td>jane@test.com</td>
                    <td>test123</td>
                </tr>
                <tr>
                    <td>Student</td>
                    <td>mike@test.com</td>
                    <td>test123</td>
                </tr>
                <tr>
                    <td>Student</td>
                    <td>sarah@test.com</td>
                    <td>test123</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script src="../acetraining/assets/js/main.js"></script>
</body>

</html>
<?php include '../components/footer.php'; ?>