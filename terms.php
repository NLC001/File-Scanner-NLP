<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Terms of Service - File Scanner</title>
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
    <h2>Terms of Service</h2>
    <p>By using File Scanner, you agree to the following terms and conditions:</p>
    <h3>Use of Service</h3>
    <p>File Scanner is provided for personal and non-commercial use. Misuse or unauthorized distribution of files is prohibited.</p>
    <h3>Limitations of Liability</h3>
    <p>File Scanner is not responsible for any damages that may result from the use of this service.</p>
    <h3>Changes to Terms</h3>
    <p>We reserve the right to update these terms at any time. Continued use of the site constitutes acceptance of the new terms.</p>

    <a href="index.php" class="btn btn-primary mt-3">Go Back to Home</a>
</div>

<footer class="bg-dark text-center text-white p-3 mt-5">
    <p>&copy; 2024 File Scanner. All rights reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
