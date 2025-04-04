<?php
include 'db_connect.php';
$company_id = $_POST['company_id'];
$sql = "SELECT * FROM companies WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
echo json_encode($result->fetch_assoc());
?>
