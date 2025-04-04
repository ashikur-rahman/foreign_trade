<?php
include 'db_connect.php';
$id = $_POST['id'];
$sql = "SELECT * FROM term_loan WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
echo json_encode($result->fetch_assoc());
?>