<?php
header('Content-Type: text/html; charset=utf-8');
require 'vendor/autoload.php'; // Include PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "foreign_trade";

// Connect to MySQL database
$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset("utf8mb4"); // Ensure UTF-8 encoding

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if file is uploaded
if (isset($_FILES["file"]["name"])) {
    $fileName = $_FILES["file"]["tmp_name"];

    // Load Excel file
    $spreadsheet = IOFactory::load($fileName);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(); // Convert Excel to an array
     
    // Skip header row and start from second row
    for ($i = 0; $i < count($rows); $i++) {
        $company_id = $rows[$i][0];
        $sanction_no = $rows[$i][1];
        $tl_position = $rows[$i][2];
        $tl_no = $rows[$i][3];
        $dl_nos = $rows[$i][4];
        $sanction_date = $rows[$i][5];
        $reschedule_date = $rows[$i][6];
        $reschedule_amount = $rows[$i][7];
        $installment_frequency = $rows[$i][8];
        $installment_amount = $rows[$i][9];
        $first_installment_date = $rows[$i][10];
        $grace_period = $rows[$i][11];
        $grace_period_details = $rows[$i][12];
        $last_installment_date = $rows[$i][13];
        $special_condition = $rows[$i][14];
        $present_outstanding = $rows[$i][15];
        $total_recovery = $rows[$i][16];
        $sub_total = $rows[$i][17];
        $classification = $rows[$i][18];
        $register_index_no = $rows[$i][19];
        $reschedule_no = $rows[$i][20];
        $lc_type = $rows[$i][21];
        $passing_authority = $rows[$i][22];
        $branch_code = $rows[$i][23];
        $interest_rate = $rows[$i][24];
        $remarks = $rows[$i][25];
        $latest_state = $rows[$i][26];
        $loan_documents = $rows[$i][27];

        // Debug: Print query with values
$query = "INSERT INTO term_loan 
(company_id, sanction_no, tl_position, tl_no, dl_nos, sanction_date, reschedule_date, reschedule_amount, 
installment_frequency, installment_amount, first_installment_date, grace_period, grace_period_details, 
last_installment_date, special_condition, present_outstanding, total_recovery, sub_total, classification, 
register_index_no, reschedule_no, lc_type, passing_authority, branch_code, interest_rate, remarks, latest_state, loan_documents)
VALUES ('$company_id', '$sanction_no', '$tl_position', '$tl_no', '$dl_nos', '$sanction_date', '$reschedule_date', '$reschedule_amount',
'$installment_frequency', '$installment_amount', '$first_installment_date', '$grace_period', '$grace_period_details',
'$last_installment_date', '$special_condition', '$present_outstanding', '$total_recovery', '$sub_total', '$classification',
'$register_index_no', '$reschedule_no', '$lc_type', '$passing_authority', '$branch_code', '$interest_rate', '$remarks', '$latest_state', '$loan_documents')";

$result = $conn->query($query);

    }

    echo "Data imported successfully!";
} else {
    echo "No file uploaded!";
}

$conn->close();
?>
