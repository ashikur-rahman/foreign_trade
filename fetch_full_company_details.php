<?php
$conn = new mysqli('localhost', 'root', '', 'foreign_trade');
$id = $_POST['id'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$company = [];
$child_companies = [];

// Fetch Parent Company Details
$query = "SELECT * FROM companies WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $company = [
        "id" => $row['id'],
        "company_name" => $row['company_name'],
        "company_type" => $row['company_type'],
        "contact_number" => $row['contact_number'],
        "address" => $row['address'],
        "parent_id" => $row['parent_id']
    ];
}
$stmt->close();

if ($company["parent_id"] == 0) {
    // Fetch Child Companies
    $query = "SELECT * FROM companies WHERE parent_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $company["id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($child = $result->fetch_assoc()) {
        $child_id = $child['id'];
        
        $child_company = [
            "id" => $child['id'],
            "company_name" => $child['company_name'],
            "company_type" => $child['company_type'],
            "contact_number" => $child['contact_number'],
            "address" => $child['address'],
            "demand_loans" => [],
            "term_loans" => []
        ];
        
        // Fetch Demand Loans
        $query_dl = "SELECT * FROM demand_loan_entry WHERE company_id = ?";
        $stmt_dl = $conn->prepare($query_dl);
        $stmt_dl->bind_param("i", $child_id);
        $stmt_dl->execute();
        $result_dl = $stmt_dl->get_result();
        
        while ($loan = $result_dl->fetch_assoc()) {
            $loan_id = $loan['id'];
            $demand_loan = [
                "id" => $loan['id'],
                "dl_no" => $loan['dl_no'],
                "disburse_date" => $loan['disburse_date'],
                "expiry_date" => $loan['expiry_date'],
                "loan_creation_amount" => $loan['loan_creation_amount'],
                "transactions" => []
            ];
            
            // Fetch Demand Loan Transactions
           // $query_dlt = "SELECT * FROM demand_loan_entry_transactions WHERE demand_loan_id = ?";

            $query_dlt = "SELECT t.*, e.dl_no
                            FROM demand_loan_entry_transactions t
                            JOIN demand_loan_entry e ON t.demand_loan_id = e.id
                            WHERE t.demand_loan_id = ?";
            $stmt_dlt = $conn->prepare($query_dlt);
            $stmt_dlt->bind_param("i", $loan_id);
            $stmt_dlt->execute();
            $result_dlt = $stmt_dlt->get_result();
            
            while ($txn = $result_dlt->fetch_assoc()) {
                $demand_loan["transactions"][] = [
                    "demand_loan_id" => $txn['dl_no'],
                    "transaction_date" => $txn['transaction_date'],
                    "details" => $txn['details'],
                    "type" => $txn['type'],
                    "amount" => $txn['amount']
                ];
            }
            $stmt_dlt->close();
            
            $child_company["demand_loans"][] = $demand_loan;
        }
        $stmt_dl->close();
        
        // Fetch Term Loans
        $query_tl = "SELECT * FROM term_loan WHERE company_id = ?";
        $stmt_tl = $conn->prepare($query_tl);
        $stmt_tl->bind_param("i", $child_id);
        $stmt_tl->execute();
        $result_tl = $stmt_tl->get_result();
        
        while ($loan = $result_tl->fetch_assoc()) {
            $loan_id = $loan['id'];
            $term_loan = [
                "id" => $loan['id'],
                "sanction_no" => $loan['sanction_no'],
                "sanction_date" => $loan['sanction_date'],
                "reschedule_amount" => $loan['reschedule_amount'],
                "installment_amount" => $loan['installment_amount'],
                "last_installment_date" => $loan['last_installment_date'],
                "transactions" => []
            ];
            
            // Fetch Term Loan Transactions
            $query_tlt = "SELECT * FROM term_loan_entry_transactions WHERE term_loan_id = ? ORDER BY transaction_date ASC";
            $stmt_tlt = $conn->prepare($query_tlt);
            $stmt_tlt->bind_param("i", $loan_id);
            $stmt_tlt->execute();
            $result_tlt = $stmt_tlt->get_result();
            
            while ($txn = $result_tlt->fetch_assoc()) {
                $term_loan["transactions"][] = [
                    "transaction_date" => $txn['transaction_date'],
                    "details" => $txn['details'],
                    "type" => $txn['type'],
                    "amount" => $txn['amount']
                ];
            }
            $stmt_tlt->close();
            
            $child_company["term_loans"][] = $term_loan;
        }
        $stmt_tl->close();
        
        $child_companies[] = $child_company;
    }
    $stmt->close();
}

$response = [
    "company" => $company,
    "child_companies" => $child_companies
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>