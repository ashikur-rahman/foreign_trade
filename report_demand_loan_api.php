<?php
include 'db_connect.php'; // Ensure DB connection is included

// $query = "SELECT 
//     dle.dl_no, 
//     dle.company_id, 
//     c.company_name AS company_name,
//     COALESCE(
//         (SELECT MAX(dlet.transaction_date) 
//          FROM demand_loan_entry_transactions dlet 
//          WHERE dlet.demand_loan_id = dle.id), 
//         'No transactions yet'
//     ) AS last_transaction_date,
//     SUM(CASE WHEN dlet.type = 'debit' THEN dlet.amount ELSE 0 END) AS total_debit, 
//     SUM(CASE WHEN dlet.type = 'credit' THEN dlet.amount ELSE 0 END) AS total_credit, 
//     (
//         COALESCE(SUM(CASE WHEN dlet.type = 'debit' THEN dlet.amount ELSE 0 END), 0) - 
//         COALESCE(SUM(CASE WHEN dlet.type = 'credit' THEN dlet.amount ELSE 0 END), 0)
//     ) AS final_balance 
// FROM demand_loan_entry dle
// JOIN companies c ON dle.company_id = c.id
// LEFT JOIN demand_loan_entry_transactions dlet ON dlet.demand_loan_id = dle.id
// GROUP BY dle.id, dle.dl_no, dle.company_id, c.company_name
// ORDER BY dle.id";



$query = "SELECT 
    dle.dl_no, 
    
    c.company_name AS company_name,
    COALESCE(
        (SELECT MAX(dlet.transaction_date) 
         FROM demand_loan_entry_transactions dlet 
         WHERE dlet.demand_loan_id = dle.id), 
        'No transactions yet'
    ) AS last_transaction_date,
    COALESCE(
        (SELECT dlet.amount
         FROM demand_loan_entry_transactions dlet
         WHERE dlet.demand_loan_id = dle.id
         ORDER BY dlet.transaction_date DESC
         LIMIT 1),
        'No transactions yet'
    ) AS last_transaction_amount,
    SUM(CASE WHEN dlet.type = 'debit' THEN dlet.amount ELSE 0 END) AS total_debit, 
    SUM(CASE WHEN dlet.type = 'credit' THEN dlet.amount ELSE 0 END) AS total_credit, 
    (
        COALESCE(SUM(CASE WHEN dlet.type = 'debit' THEN dlet.amount ELSE 0 END), 0) - 
        COALESCE(SUM(CASE WHEN dlet.type = 'credit' THEN dlet.amount ELSE 0 END), 0)
    ) AS final_balance 
FROM demand_loan_entry dle
JOIN companies c ON dle.company_id = c.id
LEFT JOIN demand_loan_entry_transactions dlet ON dlet.demand_loan_id = dle.id
GROUP BY dle.dl_no, dle.company_id, c.company_name
ORDER BY dle.dl_no";


$result = $conn->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
