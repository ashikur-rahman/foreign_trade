<?php
// Database Connection
include 'db_connect.php';

// Insert Data
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $company_id = $_POST["company_id"];
    $sanction_no = $_POST["sanction_no"];
    $sanction_date = $_POST["sanction_date"];
    $reschedule_date = $_POST["reschedule_date"];
    $reschedule_amount = $_POST["reschedule_amount"];
    $installment_frequency = $_POST["installment_frequency"];
    $installment_amount = $_POST["installment_amount"];
    $first_installment_date = $_POST["first_installment_date"];
    $grace_period = $_POST["grace_period"];
    $grace_period_details = $_POST["grace_period_details"];
    $last_installment_date = $_POST["last_installment_date"];
    $special_condition = $_POST["special_condition"];
    $present_outstanding = $_POST["present_outstanding"];
    $total_recovery = $_POST["total_recovery"];
    $sub_total = $_POST["sub_total"];
    $classification = $_POST["classification"];
    $register_index_no = $_POST["register_index_no"];
    $reschedule_no = $_POST["reschedule_no"];
    $lc_type = $_POST["lc_type"];
    $passing_authority = $_POST["passing_authority"];
    $branch_code = $_POST["branch_code"];
    $interest_rate = $_POST["interest_rate"];
    $remarks = $_POST["remarks"];
    $latest_state = $_POST["latest_state"];
    
    // File upload handling
    // $uploadDir = "uploads/term_loan/";
    // $uploaded_files = [];
    
    // if (isset($_FILES['loan_documents']) && !empty($_FILES['loan_documents']['name'][0])) {
    //     foreach ($_FILES['loan_documents']['name'] as $key => $fileName) {
    //         $targetFile = rtrim($uploadDir, "/") . "/" . uniqid() . "_" . basename($fileName);
    //         if (move_uploaded_file($_FILES['loan_documents']['tmp_name'][$key], $targetFile)) {
    //             $uploaded_files[] = $targetFile;
    //         }
    //     }
    // }
    
    // $files_json = !empty($uploaded_files) ? implode(",", $uploaded_files) : "No Files Added";

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

    // var_dump($_POST); 
    // var_dump($files_json);
    // exit("aa");
     
    // Prepare SQL statement
    $sql = "INSERT INTO term_loan 
        (company_id, sanction_no, sanction_date, reschedule_date, reschedule_amount, 
         installment_frequency, installment_amount, first_installment_date, grace_period, 
         grace_period_details, last_installment_date, special_condition, present_outstanding, 
         total_recovery, sub_total, classification, register_index_no, reschedule_no, 
         lc_type, passing_authority, branch_code, interest_rate, remarks, latest_state, loan_documents) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    // **FIXED: Corrected the type string**
    $stmt->bind_param("isssdiissssssssssisssssss", 
        $company_id, $sanction_no, $sanction_date, $reschedule_date, $reschedule_amount, 
        $installment_frequency, $installment_amount, $first_installment_date, $grace_period, 
        $grace_period_details, $last_installment_date, $special_condition, $present_outstanding, 
        $total_recovery, $sub_total, $classification, $register_index_no, $reschedule_no, 
        $lc_type, $passing_authority, $branch_code, $interest_rate, $remarks, $latest_state, $files_json
    );

    
    if ($stmt->execute()) {
        $is_demand_loan_active = 0;

if (isset($_POST['direct_from_term_loan']) && !empty($_POST['direct_from_term_loan'])){

    $_SESSION['msg']=" Term Loan Entry Successful!";
    header("location:dashboard.php?page=term_loan");
}else {
    
    $_SESSION['msg']="Demand Loan Transferred to Term Loan successfully!";
    header("location:dashboard.php?page=demand_loan");

}


    } else {
        $_SESSION['msg']="Error: " . $conn->error;
        header("location:dashboard.php?page=demand_loan");
    }
    $stmt->close();
}

$conn->close();
?>
