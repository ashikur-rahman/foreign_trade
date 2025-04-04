<?php
include 'db_connect.php'; // Ensure this connects properly

if(isset($_POST['from_date'], $_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    $sql = "SELECT 
                c.company_name, 
                tl.dl_nos, 
              
                tlet.transaction_date, 
                tlet.type, 
                tlet.amount, 
                tlet.details
            FROM term_loan_entry_transactions tlet
            JOIN term_loan tl ON tlet.term_loan_id = tl.id
            JOIN companies c ON tl.company_id = c.id
            WHERE tlet.transaction_date BETWEEN ? AND ?
            ORDER BY tlet.transaction_date DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "company_name" => $row["company_name"],
            "dl_no" => $row["dl_nos"],
          
            "transaction_date" => $row["transaction_date"],
            "type" => $row["type"],
            "amount" => $row["amount"],
            "details" => $row["details"]
        ];
    }

    echo json_encode(["data" => $data]); // DataTable expects a key named "data"
}
?>
