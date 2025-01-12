<?php
// forgot_password.php
session_start();
require 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if email exists in either the users or admins table
    $stmt = $pdo->prepare("SELECT id, role FROM (
        SELECT id, 'user' AS role FROM users WHERE email = ?
        UNION
        SELECT id, 'admin' AS role FROM admins WHERE email = ?
    ) AS combined LIMIT 1");
    $stmt->execute([$email, $email]);
    $account = $stmt->fetch();

    if ($account) {
        $token = bin2hex(random_bytes(32));
        $expires = date("U") + 1800; // Token valid for 30 minutes

        // Save the token in the database
        $stmt = $pdo->prepare("REPLACE INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires]);

        // Construct the reset link
        $resetLink = "http://localhost/FileScanner/reset_password.php?token=$token";

        // Send the email
        $subject = "Password Reset Request";
        $message = "Please click the following link to reset your password: $resetLink";
        $headers = "From: no-reply@yourwebsite.com\r\n";

        // Uncomment the line below to send the email (ensure mail server is configured)
        // mail($email, $subject, $message, $headers);

        echo "A password reset link has been sent to your email address.";
    } else {
        echo "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Forgot Password</h2>
    <form method="POST">
        <div class="form-group">
            <label for="email">Enter your email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <button type="submit" class="btn btn-primary">Request Password Reset</button>
    </form>
</div>
</body>
</html>
