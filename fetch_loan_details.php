<?php
$conn = new mysqli('localhost', 'root', '', 'foreign_trade');
$id = $_POST['id'];


$query_1 = "SELECT dl.*
FROM demand_loan_entry dl 
WHERE dl.id =  $id";

//var_dump($query_1);exit();
$result = $conn->query($query_1);

$company = [];
$loans = [];
$transactions = [];

while ($row = $result->fetch_assoc()) {
    if (!empty($row['dl_no'])) {
        $loans[] = [
            "id" => $row['id'],
            "company_id" => $row['company_id'],
            "dl_no" => $row['dl_no'],
            "disburse_date" => $row['disburse_date'],
            "expiry_date" => $row['expiry_date'],
            "loan_creation_amount" => $row['loan_creation_amount']
        ];
    }
}
//
$dl_id = $loans[0]['id'];
$company_id = $loans[0]['company_id'];

$query_2 = "SELECT c.*
FROM companies c
WHERE c.id =  $company_id";
//
$result_1 = $conn->query($query_2);

while ($row = $result_1->fetch_assoc()) {
if (empty($company)) {
    $company = [
        "id"  => $row['id'],
        "company_name" => $row['company_name'],
        "company_type" => $row['company_type'],
        "contact_number" => $row['contact_number']
    ];
}
}

$query_3 = "SELECT t.* 
FROM demand_loan_entry_transactions t
WHERE t.demand_loan_id = $dl_id ORDER BY t.transaction_date ASC";
//var_dump($query_3); exit();
$result_2 = $conn->query($query_3);

while ($row = $result_2->fetch_assoc()) {
if (!empty($row['id'])) {
    $transactions[] = [
        "transaction_date" => $row['transaction_date'],
        "details" => $row['details'],
        "type" => $row['type'],
        "amount" => $row['amount']
    ];
}
}

echo json_encode(["company" => $company, "loans" => $loans, "transactions" => $transactions]);
?>
