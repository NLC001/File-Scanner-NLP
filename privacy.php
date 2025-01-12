<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Privacy Policy - File Scanner</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">File Scanner</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="terms.php">Terms of Service</a></li>
            <li class="nav-item"><a class="nav-link" href="privacy.php">Privacy Policy</a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Privacy Policy</h2>
    <p>We value your privacy. Our Privacy Policy explains how we collect, use, and protect your personal information when using our File Scanner platform.</p>
    <h3>Information Collection</h3>
    <p>We may collect personal information like your username and email address when you register on our site.</p>
    <h3>Data Usage</h3>
    <p>Your data is used solely to provide you with services and improve the user experience.</p>
    <h3>Security</h3>
    <p>We take reasonable steps to protect your information from unauthorized access.</p>

    <a href="index.php" class="btn btn-primary mt-3">Go Back to Home</a>
</div>

<footer class="bg-dark text-center text-white p-3 mt-5">
    <p>&copy; 2024 File Scanner. All rights reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
