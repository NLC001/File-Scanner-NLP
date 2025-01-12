<?php
session_start();
require 'db_config.php';

// Check if an admin already exists
$stmt = $pdo->prepare("SELECT COUNT(*) as admin_count FROM admins");
$stmt->execute();
$adminExists = $stmt->fetch()['admin_count'] > 0;

if ($adminExists) {
    // Redirect to login page if admin already exists
    header("Location: admin_login.php?error=Admin account already exists. Please contact the existing admin for further assistance.");
    exit();
}

$error = ''; // To store any error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert the admin user into the admins table
        $stmt = $pdo->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?)");
        if ($stmt->execute([$username, $email, $hashedPassword])) {
            // Redirect to admin login page after successful registration
            header("Location: admin_login.php?success=Admin registration successful. Please log in.");
            exit();
        } else {
            $error = "Error registering admin. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Register as Admin</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="admin_register.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Register as Admin</button>
        </form>
    </div>
</body>
</html>
