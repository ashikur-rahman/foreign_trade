<?php
include 'db_connect.php';
$sql = "UPDATE companies SET company_type=?, company_name=?, address=?, contact_number=?, parent_id=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii", $_POST['company_type'], $_POST['company_name'], $_POST['address'], $_POST['contact_number'], $_POST['parent_id'], $_POST['company_id']);
$stmt->execute();

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
?>
