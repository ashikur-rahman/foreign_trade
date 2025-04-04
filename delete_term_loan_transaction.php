<?php
$conn = new mysqli('localhost', 'root', '', 'foreign_trade');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Delete transaction from database
    $query = "DELETE FROM term_loan_entry_transactions WHERE id = ?";
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