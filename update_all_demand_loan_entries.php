<?php
// Database connection
include 'db_connect.php';



// If the update button is pressed
if (isset($_POST['update_all_entries'])) {
    $sql = "
        UPDATE demand_loan_entry dle
        JOIN (
            SELECT 
                dle.id AS demand_loan_id,
                SUM(CASE WHEN dlet.type = 'debit' THEN dlet.amount ELSE 0 END) AS total_debit, 
                SUM(CASE WHEN dlet.type = 'credit' THEN dlet.amount ELSE 0 END) AS total_credit, 
                (
                    COALESCE(SUM(CASE WHEN dlet.type = 'debit' THEN dlet.amount ELSE 0 END), 0) 
                    - COALESCE(SUM(CASE WHEN dlet.type = 'credit' THEN dlet.amount ELSE 0 END), 0)
                ) AS final_balance 
            FROM demand_loan_entry dle 
            LEFT JOIN demand_loan_entry_transactions dlet 
                ON dlet.demand_loan_id = dle.id 
            GROUP BY dle.id
        ) AS loan_summary ON dle.id = loan_summary.demand_loan_id
        SET 
            dle.present_outstanding = loan_summary.final_balance,
            dle.total_recovery = loan_summary.total_credit,
            dle.sub_total = loan_summary.final_balance;
    ";

    if ($conn->query($sql) === TRUE) {
       // exit('aa');
        echo "<script>alert('Records updated successfully');</script>";

      //  $_SESSION['msg']=" Demand Loan present outstanding and total recovery  data updated successful!";
       // header("location:dashboard.php?page=demand_loan"); 

    } else {
        echo "<script>alert('Error updating records: " . $conn->error . "');</script>";
    }
}

$conn->close();
?>
