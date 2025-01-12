<?php
session_start();
require 'db_config.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>File Scanner - Home</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fa;
                margin: 0;
                padding: 0;
            }

            .container {
                max-width: 800px;
                margin: auto;
                padding: 20px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-top: 20px;
            }

            .navbar {
                position: fixed;
                top: 0;
                width: 100%;
                z-index: 1000;
            }

            .main-content {
                margin-top: 80px; /* Space for fixed navbar */
            }

            /* Sidebar container */
            .sidebar {
                position: fixed;
                top: 80px;
                right: 0;
                width: 250px;
                background: #f8f9fa;
                padding: 20px;
                box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
                max-height: 500px; /* Set a max height to limit its size */
                overflow-y: auto; /* Allows scrolling if content exceeds max height */
            }

            /* Instruction list styling */
            .instructions-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .instructions-list li {
                margin-bottom: 15px; /* Slight space between items */
            }

            .instructions-list li strong {
                color: #007bff; /* Emphasize headers */
            }

            .instructions-list li em {
                display: block;
                color: #6c757d; /* Light grey for examples */
                font-size: 0.9em; /* Slightly smaller font */
                margin-top: 4px; /* Add a bit of space above examples */
            }

            .form-control, .btn {
                margin-top: 10px;
            }

            .upload-form, .question-form, .uploaded-files {
                margin-bottom: 20px;
            }

            .filePreview {
                display: none; /* Hide by default */
                background-color: #f8f9fa;
                padding: 10px;
                border: 1px solid #ddd;
                margin-top: 10px;
                border-radius: 5px;
                transition: opacity 0.5s;
            }


        </style>
    </head>
    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php">File Scanner</a>
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="main-content container">
            <h1>File Scanner</h1>

            <?php if (isset($_SESSION['username'])): ?>
                <p>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</p>

                <!-- Upload Files Section -->
                <div class="upload-form">
                    <h2>Upload Files</h2>
                    <form action="upload.php" method="post" enctype="multipart/form-data">
                        <input type="file" id="fileToUpload" name="files[]" class="form-control" accept=".pdf, .doc, .docx" multiple onchange="showPreview()">
                        <div id="filePreview" style="display: none; margin-top: 20px;"></div>

                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>


                <!-- Ask a Question Section -->
                <div class="question-form">
                    <h2>Ask a Question</h2>
                    <form action="ask.php" method="post">
                        <input type="text" id="question" name="question" class="form-control" required>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>

                <!-- Display Uploaded Files -->
                <div class="uploaded-files">
                    <h2>Uploaded Files</h2>
                    <ul class="list-group">
                        <?php
                        try {
                            $sql = "SELECT id, filename FROM documents";
                            $stmt = $pdo->query($sql);
                            $files = $stmt->fetchAll();

                            if ($files) {
                                foreach ($files as $file) {
                                    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                                    echo htmlspecialchars($file['filename']);
                                    echo "<button class='btn btn-danger btn-sm delete-file' data-id='" . htmlspecialchars($file['id']) . "'>Delete</button>";
                                    echo "</li>";
                                }
                            } else {
                                echo "<li class='list-group-item'>No files uploaded.</li>";
                            }
                        } catch (\PDOException $e) {
                            echo "<li class='list-group-item'>Error retrieving files. Please try again later.</li>";
                        }
                        ?>
                    </ul>
                </div>
            <?php else: ?>
                <p>Please <a href="login.php">login</a> or <a href="register.php">register</a> to access features.</p>
                <a href="forgot_password.php">Forgot password?</a>
            <?php endif; ?>
        </div>

        <!-- Sidebar only shows if user is logged in -->
        <?php if (isset($_SESSION['username'])): ?>
            <div class="sidebar">
                <h3>How to Ask Questions</h3>
                <ul class="instructions-list">
                    <ul class="instructions-list">
                        <li>
                            <strong>Definitions:</strong> Use "define" or "what is".<br>
                            <em>Example:</em> "What is computing?"
                        </li>
                        <li>
                            <strong>Yes/No Questions:</strong> Use "is" and state clearly.<br>
                            <em>Example:</em> "Is Ali a boy?"
                        </li>
                        <li>
                            <strong>Specific Questions:</strong> Use "who," "where," or "when" for specifics.<br>
                            <em>Example:</em> "Who is the CEO of the company?"
                        </li>
                    </ul>
            </div>
        <?php endif; ?>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script>
                            $(document).ready(function () {
                                $('.delete-file').on('click', function () {
                                    const fileId = $(this).data('id');
                                    const listItem = $(this).closest('li');

                                    $.ajax({
                                        url: 'delete_file.php',
                                        type: 'POST',
                                        data: {id: fileId},
                                        dataType: 'json',
                                        success: function (response) {
                                            if (response.success) {
                                                listItem.remove();
                                            } else {
                                                alert('Error deleting file.');
                                            }
                                        },
                                        error: function () {
                                            alert('Error processing request.');
                                        }
                                    });
                                });
                            });
        </script>
        <script src="scripts.js"></script>
        <?php include 'footer.php'; ?>
    </body>
</html>