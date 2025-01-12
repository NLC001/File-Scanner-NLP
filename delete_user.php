<?php
session_start();
require 'db_config.php';

// Ensure the user is logged in as an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Check if 'id' parameter is present and is a valid integer
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = (int) $_GET['id'];

    try {
        // Verify that the user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if ($user) {
            // Proceed with the deletion
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success_message'] = "User successfully deleted.";
        } else {
            $_SESSION['error_message'] = "User not found.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "An error occurred. Please try again.";
    }
} else {
    $_SESSION['error_message'] = "Invalid user ID.";
}

// Redirect back to the admin dashboard with appropriate message
header("Location: admin_dashboard.php");
exit();
?>
