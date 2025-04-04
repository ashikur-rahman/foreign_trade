<?php
include 'db_connect.php'; // Ensure DB connection is included




$query = "SELECT 
    tle.dl_nos, 
    c.company_name AS company_name,
    
    -- Get the last transaction date
    COALESCE(
        (SELECT MAX(tlet.transaction_date) 
         FROM term_loan_entry_transactions tlet 
         WHERE tlet.term_loan_id = tle.id), 
        'No transactions yet'
    ) AS last_transaction_date,
    
    -- Get the last transaction amount
    COALESCE(
        (SELECT tlet.amount
         FROM term_loan_entry_transactions tlet
         WHERE tlet.term_loan_id = tle.id
         ORDER BY tlet.transaction_date DESC
         LIMIT 1),
        'No transactions yet'
    ) AS last_transaction_amount,
    
    -- Sum of debit amounts (handle NULL properly)
    COALESCE(SUM(CASE WHEN tlet.type = 'debit' THEN tlet.amount ELSE 0 END), 0) AS total_debit, 
    
    -- Sum of credit amounts (handle NULL properly)
    COALESCE(SUM(CASE WHEN tlet.type = 'credit' THEN tlet.amount ELSE 0 END), 0) AS total_credit, 
    
    -- Final balance calculation
    (
        COALESCE(SUM(CASE WHEN tlet.type = 'debit' THEN tlet.amount ELSE 0 END), 0) - 
        COALESCE(SUM(CASE WHEN tlet.type = 'credit' THEN tlet.amount ELSE 0 END), 0)
    ) AS final_balance 
    
FROM term_loan tle
JOIN companies c ON tle.company_id = c.id
LEFT JOIN term_loan_entry_transactions tlet ON tlet.term_loan_id = tle.id

GROUP BY tle.id, tle.tl_no, tle.company_id, c.company_name
ORDER BY tle.tl_no;
";


$result = $conn->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
