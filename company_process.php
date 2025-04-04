<?php
session_start();
$host = 'localhost';
$db = 'foreign_trade';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CRUD for Company Management
if (isset($_POST['add_company'])) {
    $company_name = $_POST['company_name'];
    $company_type = $_POST['company_type'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    
    $query = "INSERT INTO companies (company_name, company_type, address, contact_number) VALUES ('$company_name', '$company_type', '$address', '$contact_number')";
    if ($conn->query($query)) {
        //echo "Company added successfully!";

        $_SESSION['msg']="Company added successfully!";
        header("location:dashboard.php?page=company");

    } else {
        echo "Error: " . $conn->error;
    }
}

if (isset($_POST['update_company'])) {

   // var_dump($_POST); exit();
    $id = $_POST['id'];
    $company_name = $_POST['company_name'];
    $company_type = $_POST['company_type'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    
    $query = "UPDATE companies SET company_name='$company_name', company_type='$company_type', address='$address', contact_number='$contact_number' WHERE id='$id'";
    if ($conn->query($query)) {

        $_SESSION['msg']="Company updated successfully";
        header("location:dashboard.php?page=company");
    } else {
        echo "Error: " . $conn->error;
    }
}

if (isset($_GET['delete_company'])) {
    $id = $_GET['delete_company'];
    $query = "DELETE FROM companies WHERE id='$id'";
    if ($conn->query($query)) {
        echo "Company deleted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
