<?php
include 'db_connect.php';
$sql = "UPDATE term_loan SET sanction_no=?, sanction_date=?, reschedule_amount=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdi", $_POST['sanction_no'], $_POST['sanction_date'], $_POST['reschedule_amount'], $_POST['id']);
$stmt->execute();
?>