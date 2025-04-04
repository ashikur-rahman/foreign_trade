<?php
$conn = new mysqli('localhost', 'root', '', 'foreign_trade');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    $query = "DELETE FROM companies WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    
    $stmt->close();
    $conn->close();
}
?>
