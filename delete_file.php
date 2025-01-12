<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db_config.php';

$response = ["success" => false, "message" => ""];

if (!isset($_SESSION['username'])) {
    $response["message"] = "Unauthorized access.";
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $fileId = $_POST['id'];

    try {
        // Check if file exists in documents table
        $stmt = $pdo->prepare("SELECT filename FROM documents WHERE ID = ?");
        $stmt->execute([$fileId]);
        $file = $stmt->fetch();

        // Proceed if file is found
        if ($file) {
            $filePath = 'uploads/' . $file['filename'];
            if (file_exists($filePath)) {
                unlink($filePath); // Delete the file from server
            }
            
            // Delete file entry from database
            $deleteStmt = $pdo->prepare("DELETE FROM documents WHERE ID = ?");
            $deleteStmt->execute([$fileId]);

            $response["success"] = true;
            $response["message"] = "File deleted successfully.";
        } else {
            $response["message"] = "File not found in database.";
        }
    } catch (PDOException $e) {
        $response["message"] = "Error deleting file: " . $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$response["message"] = "Invalid request.";
header('Content-Type: application/json');
echo json_encode($response);

if ($isAdmin && isset($_GET['id'])) {
    // Admin delete action via GET (e.g., from admin dashboard link)
    $fileId = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT filename FROM documents WHERE id = ?");
        $stmt->execute([$fileId]);
        $file = $stmt->fetch();

        if ($file) {
            $filePath = 'uploads/' . $file['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $deleteStmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
            $deleteStmt->execute([$fileId]);
            $_SESSION['message'] = "File deleted successfully.";
        } else {
            $_SESSION['message'] = "File not found.";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error deleting file: " . $e->getMessage();
    }

    header("Location: admin_dashboard.php");
    exit();
}

// Default response if conditions are not met
$response["message"] = "Invalid request.";
header('Content-Type: application/json');
echo json_encode($response);
?>
