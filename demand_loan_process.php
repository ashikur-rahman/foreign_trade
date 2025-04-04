<?php
require 'vendor/autoload.php'; 

use setasign\Fpdi\Fpdi;
use setasign\Fpdf\Fpdf;

session_start();
$host = 'localhost';
$db = 'foreign_trade';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CRUD for Demand Loan Entry
if (isset($_POST['add_demand_loan'])) {
    $company_id = $_POST['company_id'];
    $type = $_POST['type'];
    $dl_no = $_POST['dl_no'];
    $disburse_date = $_POST['disburse_date'];
    $expiry_date = $_POST['expiry_date'];
    $loan_creation_amount = $_POST['loan_creation_amount'];
    $present_outstanding = $_POST['present_outstanding'];
    $sub_total = $_POST['sub_total'];
    $moad = $_POST['moad'];
    $classification = $_POST['classification'];
    $rf = $_POST['rf'];

    $lc_nos = $_POST['lc_nos']; 
    $lc_amount_usd = $_POST['lc_amount_usd'];
    $exchange_rate_of_dl = $_POST['exchange_rate_of_dl'];
    $reason_for_dl = $_POST['reason_for_dl'];
    $latest_state = $_POST['latest_state'];

    $uploadDir = "uploads/";  // Ensure it ends with a single slash
    $uploaded_files = [];

    // File upload handling
    if (isset($_FILES['loan_documents']) && !empty($_FILES['loan_documents']['name'][0])) {
        foreach ($_FILES['loan_documents']['name'] as $key => $fileName) {
            // Ensure no double slashes
           // $filePaths = implode(",", $uploadedFiles);
            $targetFile = rtrim($uploadDir) . uniqid() . "_" . basename($fileName);

            if (move_uploaded_file($_FILES['loan_documents']['tmp_name'][$key], $targetFile)) {
                $uploaded_files[] = $targetFile; // Store properly formatted file path
            }
        }
    }

    if (!empty($uploaded_files)) {
        $files_json = implode(",", $uploaded_files); 
    }
    else{
        $files_json ="";
    }
    // Convert file paths into JSON format
    //$files_json = json_encode($uploaded_files);

    // Check for existing entry
    // $check_query = $conn->prepare("SELECT id FROM demand_loan_entry WHERE company_id = ? AND dl_no = ?");
    // $check_query->bind_param("is", $company_id, $dl_no);
    // $check_query->execute();
    // $result = $check_query->get_result();

   // if ($result->num_rows == 0) { 
        // Insert only if no duplicate exists
        $stmt = $conn->prepare("INSERT INTO demand_loan_entry (
            company_id, type, dl_no, disburse_date, expiry_date, loan_creation_amount, present_outstanding,
            sub_total, remarks, classification, rf, loan_documents, lc_nos, lc_amount_usd, exchange_rate_of_dl,
            reason_for_dl, latest_state
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "isssssddsssssdsss",
            $company_id, $type, $dl_no, $disburse_date, $expiry_date, $loan_creation_amount, $present_outstanding,
            $sub_total, $moad, $classification, $rf, $files_json, $lc_nos, $lc_amount_usd, $exchange_rate_of_dl,
            $reason_for_dl, $latest_state
        );

        if ($stmt->execute()) {
            $_SESSION['msg'] = "Demand Loan Entry added successfully!";
            header("location:dashboard.php?page=demand_loan");
        } else {
            echo "Error: " . $stmt->error;
        }
    // } else {
    //     $_SESSION['msg'] = "Error: Demand Loan Entry already exists!";
    //     header("location:dashboard.php?page=demand_loan");
    // }
}


// Update Demand Loan Entry
if (isset($_POST['update_demand_loan'])) {
    $id = $_POST['id'];
    $company_id = $_POST['company_id'];
    $type = $_POST['type'];
    $dl_no = $_POST['dl_no'];
    $disburse_date = $_POST['disburse_date'];
    $expiry_date = $_POST['expiry_date'];
    $loan_creation_amount = $_POST['loan_creation_amount'];
    $present_outstanding = $_POST['present_outstanding'];
    $sub_total = $_POST['sub_total'];
    $moad = $_POST['moad'];
    $classification = $_POST['classification'];
    $rf = $_POST['rf'];

    $lc_nos = $_POST['lc_nos'];
    $lc_amount_usd = $_POST['lc_amount_usd'];
    $exchange_rate_of_dl = $_POST['exchange_rate_of_dl'];
    $reason_for_dl = $_POST['reason_for_dl'];
    $latest_state = $_POST['latest_state'];

    //var_dump($_POST); exit();

    // $uploadDir = "uploads/";
    // $uploaded_files = [];

    // if (isset($_FILES['loan_documents']) && !empty($_FILES['loan_documents']['name'][0])) {
    //     foreach ($_FILES['loan_documents']['name'] as $key => $fileName) {
    //         $targetFile = rtrim($uploadDir) . uniqid() . "_" . basename($fileName);
    //         if (move_uploaded_file($_FILES['loan_documents']['tmp_name'][$key], $targetFile)) {
    //             $uploaded_files[] = $targetFile;
    //         }
    //     }
    // }

    // $files_json = !empty($uploaded_files) ? implode(",", $uploaded_files) : $_POST['existing_files'];
//var_dump($_FILES['loan_documents']); exit();
/*   
$uploadDir = "uploads/";  // Ensure it ends with a single slash
    $uploaded_files = [];

    // File upload handling
    if (isset($_FILES['loan_documents']) && !empty($_FILES['loan_documents']['name'][0])) {
        foreach ($_FILES['loan_documents']['name'] as $key => $fileName) {
            // Ensure no double slashes
           // $filePaths = implode(",", $uploadedFiles);
            $targetFile = rtrim($uploadDir) . uniqid() . "_" . basename($fileName);

            if (move_uploaded_file($_FILES['loan_documents']['tmp_name'][$key], $targetFile)) {
                $uploaded_files[] = $targetFile; // Store properly formatted file path
            }
        }
    }

    if (!empty($uploaded_files)) {
        // $files_json = !empty($uploaded_files) ? implode(",", $uploaded_files) : $_POST['existing_files'];
        $files_json = implode(",", $uploaded_files); 
    }
    else{
        $files_json ="";
    }
*/
$uploadDir = "uploads/";  
$uploaded_files = [];

// Retrieve existing files from the form (hidden input)
$existing_files = isset($_POST['existing_loan_documents']) ? explode(',', $_POST['existing_loan_documents']) : [];

// File upload handling — add new files
if (isset($_FILES['loan_documents']) && !empty($_FILES['loan_documents']['name'][0])) {
    foreach ($_FILES['loan_documents']['name'] as $key => $fileName) {
        $targetFile = rtrim($uploadDir, '/') . '/' . uniqid() . "_" . basename($fileName);

        if (move_uploaded_file($_FILES['loan_documents']['tmp_name'][$key], $targetFile)) {
            $uploaded_files[] = $targetFile; // Add new file path to array
        }
    }
}

// Merge existing and new files
$all_files = array_merge($existing_files, $uploaded_files);

// Remove any empty entries
$all_files = array_filter($all_files);

// Convert array to comma-separated string
$files_json = implode(",", $all_files);
// var_dump($files_json); exit();
    $stmt = $conn->prepare("UPDATE demand_loan_entry SET
        company_id = ?,
        type = ?,
        dl_no = ?,
        disburse_date = ?,
        expiry_date = ?,
        loan_creation_amount = ?,
        present_outstanding = ?,
        sub_total = ?,
        remarks = ?,
        classification = ?,
        rf = ?,
        loan_documents = ?,
        lc_nos = ?,
        lc_amount_usd = ?,
        exchange_rate_of_dl = ?,
        reason_for_dl = ?,
        latest_state = ?
        WHERE id = ?");

    $stmt->bind_param(
        "isssssddsssssdsssi",
        $company_id, $type, $dl_no, $disburse_date, $expiry_date, $loan_creation_amount, $present_outstanding,
        $sub_total, $moad, $classification, $rf, $files_json, $lc_nos, $lc_amount_usd, $exchange_rate_of_dl,
        $reason_for_dl, $latest_state, $id
    );

    // Build the query string with actual values for debugging
$debug_query = sprintf(
    "UPDATE demand_loan_entry SET
        company_id = %d,
        type = '%s',
        dl_no = '%s',
        disburse_date = '%s',
        expiry_date = '%s',
        loan_creation_amount = %f,
        present_outstanding = %f,
        sub_total = %f,
        remarks = '%s',
        classification = '%s',
        rf = '%s',
        loan_documents = '%s',
        lc_nos = '%s',
        lc_amount_usd = %f,
        exchange_rate_of_dl = %f,
        reason_for_dl = '%s',
        latest_state = '%s'
        WHERE id = %d",
    $company_id, $type, $dl_no, $disburse_date, $expiry_date, $loan_creation_amount, $present_outstanding,
    $sub_total, $moad, $classification, $rf, $files_json, $lc_nos, $lc_amount_usd, $exchange_rate_of_dl,
    $reason_for_dl, $latest_state, $id
);

// Log or output the query for debugging
error_log($debug_query);
echo "<pre>$debug_query</pre>"; 

//exit();

    if ($stmt->execute()) {
        $_SESSION['msg'] = "Demand Loan Entry updated successfully!";
        header("location:dashboard.php?page=demand_loan");
    } else {
        echo "Error: " . $stmt->error;
    }
}



// Function to compress PDF
function compressPDF($source, $destination) {
    try {
        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->setSourceFile($source);
        $tplIdx = $pdf->importPage(1);
        $pdf->AddPage();
        $pdf->useTemplate($tplIdx);
        $pdf->Output($destination, "F"); // Save compressed PDF
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// if (isset($_POST['update_demand_loan'])) {
//     $id = $_POST['id'];
//     $company_id = $_POST['comany_id'];
//     $type = $_POST['type'];
//     $dl_no = $_POST['dl_no'];
//     $disburse_date = $_POST['disburse_date'];
//     $expiry_date = $_POST['expiry_date'];
//     $loan_creation_amount = $_POST['loan_creation_amount'];
//     $present_outstanding = $_POST['present_outstanding'];
//     $sub_total = $_POST['sub_total'];
//     $moad = $_POST['moad'];
//     $classification = $_POST['classification'];
//     $rf = $_POST['rf'];

//     $query = "UPDATE demand_loan_entry SET 
//     company_id='$company_id', 
//     type='$type', 
//     dl_no='$dl_no',
//     disburse_date = '$disburse_date',
//     expiry_date = '$expiry_date',
//     loan_creation_amount = '$loan_creation_amount',
//     present_outstanding = '$present_outstanding',
//     sub_total = '$sub_total',
//     moad = '$moad',
//     classification = '$classification',
//     rf = '$rf'
//     WHERE id='$id'";
//     $conn->query($query);

//     $_SESSION['msg']="Demand Loan Entry updated successfully!";
//     header("location:dashboard.php?page=demand_loan");
// }

if (isset($_POST['delete_demand_loan'])) {
    $id = $_POST['id'];
    $conn->query("DELETE FROM demand_loan_entry WHERE id='$id'");
    
    $_SESSION['msg']="Demand Loan Entry Deleted successfully!";
    header("location:dashboard.php?page=demand_loan");
}


?>