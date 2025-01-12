<?php

session_start();
require 'db_config.php';
require_once 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['files'])) {
    $files = $_FILES['files'];
    $uploadDir = 'uploads/';

    // Ensure the uploads directory exists and is writable
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $allowedExtensions = ['pdf'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB limit

    for ($i = 0; $i < count($files['name']); $i++) {
        $fileTmpName = $files['tmp_name'][$i];
        $fileName = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', basename($files['name'][$i]));
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $uploadFile = $uploadDir . $fileName;

        // Validate file type and size
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo "File type not allowed for '$fileName'. Only PDF files are allowed.\n";
            continue;
        }
        if ($files['size'][$i] > $maxFileSize) {
            echo "File '$fileName' is too large.\n";
            continue;
        }

        // Move uploaded file
        if (move_uploaded_file($fileTmpName, $uploadFile)) {
            echo "File '$fileName' is valid, and was successfully uploaded.\n";
            $extractedText = extractTextFromPDF($uploadFile);
            saveToDatabase($pdo, $fileName, $extractedText);
        } else {
            echo "Possible file upload attack on '$fileName'!\n";
        }
    }

    // Redirect to the home page after uploading
    header("Location: index.php");
    exit();
}

function extractTextFromPDF($filePath) {
    $parser = new \Smalot\PdfParser\Parser();
    $pdf = $parser->parseFile($filePath);
    $text = $pdf->getText();
    return $text;
}

function saveToDatabase($pdo, $filename, $content) {
    $sql = "INSERT INTO documents (filename, content) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$filename, $content]);
}
?>

