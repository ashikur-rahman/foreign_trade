<?php
include 'db_connect.php'; // Ensure this connects properly

if(isset($_POST['from_date'], $_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    $sql = "SELECT 
                c.company_name, 
                dle.dl_no, 
                dlet.transaction_date, 
                dlet.type, 
                dlet.amount, 
                dlet.details
            FROM demand_loan_entry_transactions dlet
            JOIN demand_loan_entry dle ON dlet.demand_loan_id = dle.id
            JOIN companies c ON dle.company_id = c.id
            WHERE dlet.transaction_date BETWEEN ? AND ?
            ORDER BY dlet.transaction_date DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "company_name" => $row["company_name"],
            "dl_no" => $row["dl_no"],
            "transaction_date" => $row["transaction_date"],
            "type" => $row["type"],
            "amount" => $row["amount"],
            "details" => $row["details"]
        ];
    }

    echo json_encode(["data" => $data]); // DataTable expects a key named "data"
}
?>