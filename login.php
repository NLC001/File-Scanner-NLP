<?php
session_start();
require 'db_config.php';

$error = null;

// Check if an admin user exists in the `admins` table
$stmt = $pdo->prepare("SELECT COUNT(*) as admin_count FROM admins");
$stmt->execute();
$adminExists = $stmt->fetch()['admin_count'] > 0;

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve user information from `users` table
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // If user not found in `users` table, check `admins` table
    if (!$user) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        // If user is found in `admins` table, set role to 'admin'
        if ($user) {
            $user['role'] = 'admin';
        }
    } else {
        // Regular user role
        $user['role'] = 'user';
    }

    // Verify credentials
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true); // Prevent session hijacking
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p class='text-danger'>" . htmlspecialchars($error) . "</p>"; ?>
        
        <form method="post">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary">Login</button>
        </form>

        <!-- Conditionally display Register/Login links for admin -->
        <?php if (!$adminExists): ?>
            <p><a href="admin_register.php">Register as Admin</a></p>
        <?php else: ?>
            <p><a href="admin_login.php">Login as Admin</a></p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
