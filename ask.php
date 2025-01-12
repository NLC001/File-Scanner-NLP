<?php
include 'footer.php';
// Check if there are any uploaded files
$uploadDir = 'uploads/';
$files = glob($uploadDir . '*'); // Get all file paths

if (empty($files)) {
    echo "<div style='text-align:center; margin-top:50px;'>
            <p>No file uploaded. Please upload a file first.</p>
            <a href='index.php' style='padding:10px 15px; background-color:#007bff; color:white; border-radius:5px; text-decoration:none;'>Go Back to Upload</a>
          </div>";
    exit();
}


$host = '127.0.0.1';
$db = 'document_storage';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['question'])) {
    $question = $_POST['question'];
    $answer = findAnswer($pdo, $question);
    echo "<div class='container'><h2>Answer:</h2><p>$answer</p><a href='index.php' style='padding:10px 15px; background-color:#007bff; color:white; border-radius:5px; text-decoration:none;'>Go Back</a></div>";
}

function findAnswer($pdo, $question) {
    $sql = "SELECT content FROM documents";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();

    $bestMatch = '';
    $highestScore = 0;

    foreach ($results as $row) {
        $content = $row['content'];
        $answers = processNLP($question, $content);
        if ($answers && is_array($answers) && count($answers) > $highestScore) {
            $highestScore = count($answers);
            $bestMatch = implode("<br>", $answers);
        }
    }

    return $bestMatch ? $bestMatch : "No relevant answer found.";
}

function processNLP($question, $content) {
    // Create a unique temporary directory
    $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'nlp_' . uniqid();
    if (!mkdir($tempDir, 0777, true) && !is_dir($tempDir)) {
        error_log("Failed to create temporary directory: $tempDir");
        return null;
    }

    // Create a temporary file inside the unique directory
    $tempFile = $tempDir . DIRECTORY_SEPARATOR . 'content.txt';
    file_put_contents($tempFile, $content);

    $escapedQuestion = escapeshellarg($question);
    $pythonExecutable = 'C:\\Users\\Demo\\AppData\\Local\\Programs\\Python\\Python312\\python.exe';
    $scriptPath = 'C:\\xampp\\htdocs\\FileScanner\\nlp_processor.py';
    $command = "$pythonExecutable $scriptPath $escapedQuestion $tempFile";
    $output = [];
    $returnVar = 0;

    exec($command, $output, $returnVar);

    // Clean up: remove the temporary file and directory
    unlink($tempFile);
    rmdir($tempDir);

    error_log("Command: $command");
    error_log("Output: " . implode("\n", $output));
    error_log("Return Var: $returnVar");

    if ($returnVar !== 0) {
        error_log("Command execution failed with return code: $returnVar");
        return null;
    }

    $outputStr = implode("\n", $output);
    $decodedOutput = json_decode($outputStr, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        return null;
    }
    return $decodedOutput;
}

?>
