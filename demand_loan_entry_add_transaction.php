<?php
include 'db_connect.php';
session_start();
/**
 * Function to delete a transaction and update demand_loan_entry totals
 * @param int $transaction_id The ID of the transaction to delete
 */
function deleteTransaction($transaction_id) {
    global $conn;

    // Get the demand_loan_id and amount of the transaction before deleting it
    $result = $conn->query("SELECT company_id FROM demand_loan_entry_transactions WHERE id = $transaction_id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $demand_loan_id = $row['company_id'];

        // Delete the transaction
        $conn->query("DELETE FROM demand_loan_entry_transactions WHERE id = $transaction_id");

        // Update the demand_loan_entry table totals
        updateDemandLoanTotals($demand_loan_id);
    } else {
        echo "Transaction not found.";
    }
}

function updateDemandLoanTotals($demand_loan_id) {
    global $conn;

    // Validate demand_loan_id is an integer
    $demand_loan_id = (int) $demand_loan_id;
    
    // Fetch total credit
    $credit_result = $conn->query("SELECT SUM(amount) as total FROM demand_loan_entry_transactions WHERE demand_loan_id = $demand_loan_id AND type = 'credit'");
    if (!$credit_result) {
        die("Error in credit query: " . $conn->error); // Debugging
    }
    $credit_row = $credit_result->fetch_assoc();
    $credit_total = $credit_row['total'] ?? 0;

    // Fetch total debit
    $debit_result = $conn->query("SELECT SUM(amount) as total FROM demand_loan_entry_transactions WHERE demand_loan_id = $demand_loan_id AND type = 'debit'");
    if (!$debit_result) {
        die("Error in debit query: " . $conn->error); // Debugging
    }
    $debit_row = $debit_result->fetch_assoc();
    $debit_total = $debit_row['total'] ?? 0;
    // var_dump($demand_loan_id);

    // Calculate sub_total and total_recovery
    $sub_total =  $debit_total - $credit_total;
    $total_recovery = $debit_total;

    // var_dump($sub_total);
    // var_dump($total_recovery); 
    // exit('bbbb');

    // Update the demand_loan_entry table
    $update_stmt = $conn->prepare("UPDATE demand_loan_entry SET sub_total = ?, total_recovery = ? WHERE id = ?");
    if (!$update_stmt) {
        die("Error preparing update statement: " . $conn->error); // Debugging
    }

    $update_stmt->bind_param("ddi", $sub_total, $total_recovery, $demand_loan_id);
    if (!$update_stmt->execute()) {
        die("Error executing update statement: " . $update_stmt->error); // Debugging
    }
    $update_stmt->close();
}

// SELECT dle.id AS demand_loan_id, dle.dl_no, dle.company_id, SUM(CASE WHEN dlet.type = 'debit' THEN dlet.amount ELSE 0 END) AS total_debit, SUM(CASE WHEN dlet.type = 'credit' THEN dlet.amount ELSE 0 END) AS total_credit, ( COALESCE(SUM(CASE WHEN dlet.type = 'debit' THEN dlet.amount ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN dlet.type = 'credit' THEN dlet.amount ELSE 0 END), 0) ) AS final_balance FROM demand_loan_entry_transactions dlet JOIN demand_loan_entry dle ON dlet.demand_loan_id = dle.id GROUP BY dle.id, dle.dl_no, dle.company_id ORDER BY dle.id; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_id = $_POST['company_id'];
    $detailsArray = $_POST['details'];
    $amountArray = $_POST['amount'];
    $typeArray = $_POST['type'];
    $dateArray = $_POST['date'];

    $conn->begin_transaction(); // Start transaction for batch insert

    try {
        $stmt = $conn->prepare("INSERT INTO demand_loan_entry_transactions (demand_loan_id, amount, details, type, transaction_date) VALUES (?, ?, ?, ?, ?)");

        for ($i = 0; $i < count($detailsArray); $i++) {
            $details = $detailsArray[$i];
            $amount = $amountArray[$i];
            $type = $typeArray[$i];
            $date = $dateArray[$i];

            $stmt->bind_param("idsss", $company_id, $amount, $details, $type, $date);
            $stmt->execute();
        }

        $stmt->close();
        updateDemandLoanTotals($company_id); // Update total amounts after insert
        $conn->commit(); // Commit the transaction

        echo json_encode(["status" => "success", "message" => "Transactions added successfully!"]);
    } catch (Exception $e) {
        $conn->rollback(); // Rollback on error
        echo json_encode(["status" => "error", "message" => "Error adding transactions: " . $e->getMessage()]);
    }

    $conn->close();
}
?>