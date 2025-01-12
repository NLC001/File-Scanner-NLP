<?php
session_start();
require 'db_config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php"); // Redirect to admin login if unauthorized
    exit();
}

$errors = []; // Array to store any errors that occur
// Fetch total file uploads and total users
try {
    // Fetch the total number of uploaded files from the 'documents' table
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_uploads FROM documents");
    $stmt->execute();
    $totalUploads = $stmt->fetch()['total_uploads'];
} catch (PDOException $e) {
    $errors[] = "Error retrieving total file uploads.";
}

try {
    // Fetch the total number of users from the 'users' table
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM users");
    $stmt->execute();
    $totalUsers = $stmt->fetch()['total_users'];
} catch (PDOException $e) {
    $errors[] = "Error retrieving total user count.";
}

// Fetch users
try {
    $stmt = $pdo->prepare("SELECT id, username FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors[] = "Error retrieving users data.";
}

// Fetch file uploads
try {
    $stmt = $pdo->prepare("SELECT id, filename, uploaded_at FROM documents");
    $stmt->execute();
    $files = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors[] = "Error retrieving files data.";
}

// Fetch messages
try {
    $stmt = $pdo->prepare("SELECT name, email, message, created_at FROM messages ORDER BY created_at DESC");
    $stmt->execute();
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors[] = "Error retrieving messages.";
}

// Fetch additional statistics
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE active = 1");
    $stmt->execute();
    $totalActiveUsers = $stmt->fetchColumn();
} catch (PDOException $e) {
    $errors[] = "Error retrieving active user count.";
}

try {
    $stmt = $pdo->prepare("SELECT filename FROM documents ORDER BY uploaded_at DESC LIMIT 1");
    $stmt->execute();
    $recentUpload = $stmt->fetchColumn();
} catch (PDOException $e) {
    $errors[] = "Error retrieving the most recent upload.";
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <style>
            body {
                background-color: #f8f9fa;
            }
            .dashboard-header {
                margin-top: 2rem;
                margin-bottom: 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .card {
                border: none;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            .card .card-body {
                font-size: 1.25rem;
                color: #495057;
            }
            .card-title {
                font-weight: 600;
                color: #007bff;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="dashboard-header">
                <div class="text-center">
                    <h2 class="display-4">Admin Dashboard</h2>
                    <p class="text-muted">Overview of system activity and user engagement</p>
                </div>
                <a href="logout.php" class="btn btn-danger btn-lg">Logout</a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= implode("<br>", array_map('htmlspecialchars', $errors)); ?>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Files Uploaded</h5>
                                <p class="card-text display-5"><?= htmlspecialchars($totalUploads ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Registered Users</h5>
                                <p class="card-text display-5"><?= htmlspecialchars($totalUsers ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Manage Users Section -->
            <h3>Manage Users</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_user.php?id=<?= $user['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Manage Files Section -->
            <h3>Manage Files</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Uploaded At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                        <tr>
                            <td><?= htmlspecialchars($file['filename']); ?></td>
                            <td><?= htmlspecialchars($file['uploaded_at']); ?></td>
                            <td>
                                <a href="delete_file.php?id=<?= $file['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Messages Section -->
            <h3>Messages</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td><?= htmlspecialchars($msg['name']); ?></td>
                            <td><?= htmlspecialchars($msg['email']); ?></td>
                            <td><?= htmlspecialchars($msg['message']); ?></td>
                            <td><?= htmlspecialchars($msg['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Statistics Section -->
            <div class="statistics-section mt-4">
                <h3>Statistics</h3>
                <ul class="list-group">
                    <li class="list-group-item">Total Active Users: <?= htmlspecialchars($totalActiveUsers ?? 'N/A'); ?></li>
                    <li class="list-group-item">Total Files Uploaded: <?= htmlspecialchars($totalUploads ?? 'N/A'); ?></li>
                    <li class="list-group-item">Most Recent Upload: <?= htmlspecialchars($recentUpload ?? 'N/A'); ?></li>
                </ul>
            </div>

            <!-- Data Export Form -->
            <form action="export_data.php" method="post" class="mt-4">
                <button type="submit" class="btn btn-info">Export User Data as CSV</button>
            </form>

            <!-- Search Functionality -->
            <div class="input-group mb-4 mt-4">
                <input type="text" id="searchInput" placeholder="Search..." class="form-control">
                <button id="searchButton" class="btn btn-primary">Search</button>
            </div>
            <div id="searchResults"></div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                document.getElementById('searchButton').addEventListener('click', function () {
                    const button = this;
                    button.disabled = true; // Disable the button to prevent multiple clicks
                    const query = document.getElementById('searchInput').value;
                    document.getElementById('searchResults').innerHTML = '<p>Loading...</p>';
                    fetch(`search.php?query=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                button.disabled = false; // Re-enable the button
                                if (data.success && data.data.length > 0) {
                                    // Map through the data and display detailed user info
                                    const results = data.data.map(item => `
<p><strong>Username:</strong> ${item.username}</p>
<p><strong>Email:</strong> ${item.email}</p>
<p><strong>Created At:</strong> ${item.created_at}</p>
<p><strong>Upadted At:</strong> ${item.updated_at}</p>
<p><strong>Status:</strong> ${item.active ? 'Active' : 'Inactive'}</p>
<hr>
                `).join('');
                                    document.getElementById('searchResults').innerHTML = results;
                                } else {
                                    document.getElementById('searchResults').innerHTML = '<p>No results found.</p>';
                                }
                            })
                            .catch(error => {
                                button.disabled = false; // Re-enable the button
                                console.error('Error fetching search results:', error);
                                document.getElementById('searchResults').innerHTML = '<p>An error occurred. Please try again later.</p>';
                            });
                });
            </script>
        </div>
    </body>
</html>
