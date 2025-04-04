<?php
session_start();
include 'db_connect.php';

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

    // Calculate the total credit and debit amounts for the given demand_loan_id
    $credit_result = $conn->query("SELECT SUM(amount) as total FROM demand_loan_entry_transactions WHERE company_id = $demand_loan_id AND type = 'credit'");
    $credit_total = $credit_result->fetch_assoc()['total'] ?? 0;

    $debit_result = $conn->query("SELECT SUM(amount) as total FROM demand_loan_entry_transactions WHERE company_id = $demand_loan_id AND type = 'debit'");
    $debit_total = $debit_result->fetch_assoc()['total'] ?? 0;

    // Calculate sub_total and total_recovery
    $sub_total = $credit_total - $debit_total;
    $total_recovery = $debit_total;

    // Update the demand_loan_entry table
    $update_stmt = $conn->prepare("UPDATE demand_loan_entry SET sub_total = ?, total_recovery = ? WHERE id = ?");
    $update_stmt->bind_param("ddi", $sub_total, $total_recovery, $demand_loan_id);
    $update_stmt->execute();
    $update_stmt->close();
}
/*
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tl_id = $_POST['term_loan_id'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO term_loan_entry_transactions (term_loan_id, amount, details, type, transaction_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("idsss", $tl_id, $amount, $details, $type, $date);

    if ($stmt->execute()) {
        // Update the demand_loan_entry table totals
        //updateDemandLoanTotals($tl_id);
        $_SESSION['msg']="A  $type , transaction added successfully in database!";
        header("location:dashboard.php?page=term_loan");

        //echo "<script>alert('Transaction added successfully!'); window.location.href = 'index.php';</script>";
    } else {

        $_SESSION['msg']="Error adding transaction!";
        header("location:dashboard.php?page=term_loan");
       // echo "<script>alert('Error adding transaction!'); window.history.back();</script>";
    }
    
    $stmt->close();
    $conn->close();
}
*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tl_id = $_POST['term_loan_id'];
    $detailsArr = $_POST['details'];
    $amountArr = $_POST['amount'];
    $typeArr = $_POST['type'];
    $dateArr = $_POST['date'];

    if (!is_array($detailsArr) || count($detailsArr) == 0) {
        echo json_encode(["status" => "error", "message" => "No transactions received"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO term_loan_entry_transactions (term_loan_id, amount, details, type, transaction_date) VALUES (?, ?, ?, ?, ?)");

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "SQL prepare failed"]);
        exit;
    }

    $conn->begin_transaction(); // Start transaction

    try {
        for ($i = 0; $i < count($detailsArr); $i++) {
            $details = $detailsArr[$i];
            $amount = (float) $amountArr[$i];
            $type = $typeArr[$i];
            $date = $dateArr[$i];

            $stmt->bind_param("idsss", $tl_id, $amount, $details, $type, $date);
            $stmt->execute();
        }

        $conn->commit(); // Commit transaction
        $stmt->close();
        $conn->close();

        echo json_encode(["status" => "success", "message" => "Transactions added successfully"]);
    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaction in case of error
        echo json_encode(["status" => "error", "message" => "Failed to add transactions: " . $e->getMessage()]);
    }
}
?>