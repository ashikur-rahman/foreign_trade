<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['document_data'])) {
    $files_json = htmlspecialchars($_POST['document_data']);
    
    // Example: store in session, DB, or return a response
    echo "Received data: " . $files_json;
}
?>