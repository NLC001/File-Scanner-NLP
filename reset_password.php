<?php
// reset_password.php
session_start();
require 'db_config.php';

$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'], $_POST['confirm_password'])) {
    if ($_POST['password'] === $_POST['confirm_password']) {
        // Get the token from URL parameter
        $stmt = $pdo->prepare("SELECT email, expires FROM password_resets WHERE token = ? LIMIT 1");
        $stmt->execute([$token]);
        $resetData = $stmt->fetch();

        if ($resetData && $resetData['expires'] >= date("U")) {
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $email = $resetData['email'];

            // Update password for user or admin
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashedPassword, $email]);

            // If no rows were updated in users, try updating in admins
            if ($stmt->rowCount() === 0) {
                $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE email = ?");
                $stmt->execute([$hashedPassword, $email]);
            }

            // Delete the token after successful password reset
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);

            echo "Password has been reset successfully. <a href='login.php'>Login</a>";
        } else {
            echo "Reset link expired or invalid.";
        }
    } else {
        echo "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Reset Password</h2>
    <form method="POST">
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter new password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
</div>
</body>
</html>
