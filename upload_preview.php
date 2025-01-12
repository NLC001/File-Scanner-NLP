<?php
if (isset($_FILES['fileToUpload'])) {
    $filePath = $_FILES['fileToUpload']['tmp_name'];

    if (is_readable($filePath)) {
        $previewContent = file_get_contents($filePath, false, null, 0, 1000); // Increased length
        echo nl2br(htmlspecialchars($previewContent));
        if (strlen(file_get_contents($filePath)) > 1000) {
            echo "<br><em>Note: Preview is limited to the first 1000 characters.</em>";
        }
    } else {
        echo "Error: Could not load preview due to file reading issue.";
    }
} else {
    echo "Error: No file uploaded for preview.";
}
?>
