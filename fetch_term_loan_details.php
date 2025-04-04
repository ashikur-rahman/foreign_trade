<?php
$conn = new mysqli('localhost', 'root', '', 'foreign_trade');
$id = $_POST['id'];


$query_1 = "SELECT tl.*
FROM term_loan tl 
WHERE tl.id =  $id";

//var_dump($query_1);exit();
$result = $conn->query($query_1);

$company = [];
$loans = [];
$transactions = [];

while ($row = $result->fetch_assoc()) {
    if (!empty($row['dl_nos'])) {
        $loans[] = [
            "id" => $row['id'],
            "company_id" => $row['company_id'],
            "sanction_no" => $row['sanction_no'],
            "dl_nos" => $row['dl_nos'],
            "sanction_date" => $row['sanction_date'],
            "installment_amount" => $row['installment_amount'],
            "installment_frequency" => $row['installment_frequency'],
            "first_installment_date" => $row['first_installment_date'],  
            "grace_period" => $row['grace_period'],
            "last_installment_date" => $row['last_installment_date'],
            "special_condition" => $row['special_condition'],
            "reschedule_amount" => $row['reschedule_amount']
        ];
    }
}
//
//var_dump($loans); exit();
$tl_id = $loans[0]['id'];
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
        "address" => $row['address'],
        "contact_number" => $row['contact_number']
    ];
}
}

$query_3 = "SELECT t.* 
FROM term_loan_entry_transactions t
WHERE t.term_loan_id = $tl_id ORDER BY t.transaction_date ASC";
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
