<?php
require 'db_config.php';

// Check if the 'id' is set in the URL (to edit a specific user)
if (!isset($_GET['id'])) {
    die("User ID is required.");
}

$user_id = $_GET['id'];

// Fetch the user's current details from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Handle form submission for updating the user details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $active = isset ($_POST['active']) ? 1 : 0;

    // Validate input
    if (empty($username)) {
        $error = "All fields are required!";
    } else {
        try {
            // Update the user's details
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, active = ? WHERE id = ?");
            $stmt->execute([$username, $email, $active, $user_id]);

            // Redirect back to the admin dashboard or show a success message
            header("Location: admin_dashboard.php"); // Adjust this to your dashboard page
            exit;
        } catch (PDOException $e) {
            $error = "An error occurred while updating the user. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Edit User - Admin Dashboard</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <h2 class="mt-5">Edit User</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
                </div>
                <!-- Active/Inactive Toggle -->
                <div class="mb-3 form-check">
                    <input type="checkbox" id="active" name="active" class="form-check-input" <?= $user['active'] == 1 ? 'checked' : ''; ?>>
                    <label for="active" class="form-check-label">Active</label>
                </div>
                <button type="submit" class="btn btn-primary">Update User</button>
            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>