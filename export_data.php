<?php
require 'db_config.php';

try {
    // Start output buffering
    ob_start();

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="users_data.csv"');

    // Open output stream for writing CSV
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Username', 'Active', 'Created_at', 'Updated_at']);

    // Fetch data securely
    $stmt = $pdo->prepare("SELECT id, username, active, created_at, updated_at FROM users");
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fclose($output);

    // End and flush output buffer
    ob_end_flush();
    
} catch (PDOException $e) {
    // Handle error and display message or log as needed
    echo "Error generating CSV: " . htmlspecialchars($e->getMessage());
}
?>
