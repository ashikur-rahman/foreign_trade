<?php
$conn = new mysqli('localhost', 'root', '', 'foreign_trade');
$id = $_POST['id'];
$company = [];
$loans = [];
$transactions = [];
$term_loans = [];
$term_transactions = [];


$query_2 = "SELECT c.*
FROM companies c
WHERE c.id =  $id ";
//var_dump($query_2);
$result_1 = $conn->query($query_2);

while ($row = $result_1->fetch_assoc()) {
if (empty($company)) {
    $company = [
        "id"  => $row['id'],
        "company_name" => $row['company_name'],
        "company_type" => $row['company_type'],
        "contact_number" => $row['contact_number'],
        "address" => $row['address']
    ];
}
}


$query_1 = "SELECT dl.*
FROM demand_loan_entry dl 
WHERE dl.company_id =  $id";

//var_dump($query_1);exit();
$result = $conn->query($query_1);



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
//var_dump( $loans); exit();
if (!empty($loans)) {
    for($i=0;$i<count($loans); $i++){ 

    $dl_id = $loans[$i]['id'];

            $query_3 = "SELECT t.* 
            FROM demand_loan_entry_transactions t
            WHERE t.demand_loan_id = $dl_id 
            ORDER BY t.transaction_date ASC";
            //var_dump($query_3); exit();
            $result_2 = $conn->query($query_3);

            while ($row = $result_2->fetch_assoc()) {
            if (!empty($row['id'])) {
                $transactions[] = [
                    "demand_lone_number" => $loans[$i]['dl_no'],
                    "transaction_date" => $row['transaction_date'],
                    "details" => $row['details'],
                    "type" => $row['type'],
                    "amount" => $row['amount']
                ];
            }
            }


    }
}


//echo json_encode(["company" => $company, "loans" => $loans, "transactions" => $transactions]);


$query_4 = "SELECT tl.* FROM term_loan tl WHERE tl.company_id = $id";
$result_4 = $conn->query($query_4);

$term_loans = [];
while ($row = $result_4->fetch_assoc()) {
    $term_loans[] = [
        "term_loan_id" => $row['id'],
        "sanction_no" => $row['sanction_no'],
        "sanction_date" => $row['sanction_date'],
        "reschedule_amount" => $row['reschedule_amount'],
        "installment_amount" => $row['installment_amount'],
        "last_installment_date" => $row['last_installment_date']
    ];
}

$term_transactions = [];
foreach ($term_loans as $loan) {
    $tl_id = $loan['term_loan_id'];
    $query_5 = "SELECT * FROM term_loan_entry_transactions WHERE term_loan_id = $tl_id ORDER BY transaction_date ASC";
    $result_5 = $conn->query($query_5);

    while ($row = $result_5->fetch_assoc()) {
        $term_transactions[] = [
            "term_lone_number" => $loan['term_loan_id'],
            "transaction_date" => $row['transaction_date'],
            "details" => $row['details'],
            "type" => $row['type'],
            "amount" => $row['amount']
        ];
    }
}

echo json_encode(["company" => $company, "loans" => $loans, "transactions" => $transactions, "term_loans" => $term_loans, "term_transactions" => $term_transactions]);







?>





