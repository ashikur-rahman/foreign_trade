<?php
include 'db_connect.php';
$sql = "DELETE FROM term_loan WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_POST['id']);
$stmt->execute();
?>