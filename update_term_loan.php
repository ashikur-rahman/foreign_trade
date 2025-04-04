<?php
include 'db_connect.php';

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $sql = "SELECT * FROM term_loan_entry WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $term_loan_id = $_POST['term_loan_id'];
    $company_id = $_POST['company_id'];
    $sanction_no = $_POST['sanction_no'];
    $sanction_date = $_POST['sanction_date'];
    $reschedule_date = $_POST['reschedule_date'];
    $reschedule_amount = $_POST['reschedule_amount'];
    $installment_frequency = $_POST['installment_frequency'];
    $installment_amount = $_POST['installment_amount'];
    $first_installment_date = $_POST['first_installment_date'];
    $grace_period = $_POST['grace_period'];
    $last_installment_date = $_POST['last_installment_date'];
    $special_condition = $_POST['special_condition'];
    $present_outstanding = $_POST['present_outstanding'];
    $total_recovery = $_POST['total_recovery'];
    $grace_period_details = $_POST['grace_period_details'];
    $sub_total = $_POST['sub_total'];
    $passing_authority = $_POST['passing_authority'];
    $branch_code = $_POST['branch_code'];
    $interest_rate = $_POST['interest_rate'];
    $remarks = $_POST['remarks'];
    $latest_state = $_POST['latest_state'];
    $classification = $_POST['classification'];
    $register_index_no = $_POST['register_index_no'];
    $reschedule_no = $_POST['reschedule_no'];
    $lc_type = $_POST['lc_type'];

    /*
    // File upload handling
    $uploadDir = "uploads/";  
    $uploaded_files = [];

    // File upload handling
    if (isset($_FILES['loan_documents']) && !empty($_FILES['loan_documents']['name'][0])) {
        foreach ($_FILES['loan_documents']['name'] as $key => $fileName) {
            // Ensure no double slashes
            $targetFile = rtrim($uploadDir) . uniqid() . "_" . basename($fileName);

            if (move_uploaded_file($_FILES['loan_documents']['tmp_name'][$key], $targetFile)) {
                $uploaded_files[] = $targetFile; // Store properly formatted file path
            }
        }
    }

    if (!empty($uploaded_files)) {
        $loan_documents_json = implode(",", $uploaded_files); 
    }
    else{
        $loan_documents_json ="";
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
    // Update query
    $sql = "UPDATE term_loan SET
                company_id = ?,
                sanction_no = ?,
                sanction_date = ?,
                reschedule_date = ?,
                reschedule_amount = ?,
                installment_frequency = ?,
                installment_amount = ?,
                first_installment_date = ?,
                grace_period = ?,
                last_installment_date = ?,
                special_condition = ?,
                present_outstanding = ?,
                total_recovery = ?,
                grace_period_details = ?,
                sub_total = ?,
                passing_authority = ?,
                branch_code = ?,
                interest_rate = ?,
                remarks = ?,
                latest_state = ?,
                classification = ?,
                register_index_no = ?,
                reschedule_no = ?,
                lc_type = ?,
                loan_documents = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssdsssdsssssdsssssisss", 
        $company_id, $sanction_no, $sanction_date, $reschedule_date, 
        $reschedule_amount, $installment_frequency, $installment_amount, 
        $first_installment_date, $grace_period, $last_installment_date, 
        $special_condition, $present_outstanding, $total_recovery, 
        $grace_period_details, $sub_total, $passing_authority, 
        $branch_code, $interest_rate, $remarks, $latest_state, 
        $classification, $register_index_no, $reschedule_no, $lc_type, 
        $files_json, $term_loan_id);

    if ($stmt->execute()) {
        //echo "Record updated successfully.";

        $_SESSION['msg']=" Term Loan Entry Updated Successful!";
        header("location:dashboard.php?page=term_loan");
        
    } else {
        echo "Error: " . $stmt->error;
    }
$stmt->close();
}
$conn->close();
?>

