<?php
require 'db_config.php';
 
// Set JSON header for the response
header('Content-Type: application/json');
 
$query = $_GET['query'] ?? '';
 
$response = [
    'success' => false,
    'data' => [],
    'error' => null
];
 
if (!empty($query)) {
    try {
        // Prepare the statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT id, username, email, created_at, updated_at, active FROM users WHERE username LIKE ? AND active = 1");
        $stmt->execute(['%' . $query . '%']);
        // Fetch the results as an associative array
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all fields for each user
 
        // Populate the response data
        if ($results) {
            $response['success'] = true;
            $response['data'] = $results; // Include all user data in the response
        } else {
            $response['success'] = true;
            $response['data'] = [];
        }
    } catch (PDOException $e) {
        $response['error'] = 'An error occurred while searching. Please try again later.';
    }
} else {
    $response['error'] = 'No query provided.';
}
 
// Output the JSON-encoded response
echo json_encode($response, JSON_PRETTY_PRINT);
?>