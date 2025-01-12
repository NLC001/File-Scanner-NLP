<?php
session_start();
require 'db_config.php'; // Include your database config file for DB connection

function extractKeywords($text) {
    $stopWords = array("the", "and", "is", "in", "at", "of", "a", "on", "to", "it", "for", "with");
    $words = array_filter(explode(" ", strtolower($text)), function($word) use ($stopWords) {
        return !in_array($word, $stopWords) && strlen($word) > 2;
    });

    $wordFrequency = array_count_values($words);
    arsort($wordFrequency);

    return array_slice(array_keys($wordFrequency), 0, 5);
}

function analyzeFileContent($filePath) {
    $content = file_get_contents($filePath);
    $keywords = extractKeywords($content);

    return $keywords;
}

if (isset($_FILES['fileToUpload'])) {
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['fileToUpload']['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $filePath)) {
        $keywords = analyzeFileContent($filePath);
        echo "Top Keywords: " . implode(", ", $keywords);
    } else {
        echo "File upload failed.";
    }
}
?>
