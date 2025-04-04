<?php

include 'db_connect.php';
$company_id = $_GET['company_id'];
$sql = "SELECT id, details, transaction_date, amount, type FROM demand_loan_entry_transactions WHERE demand_loan_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();

$output = "";
$totalBalance = 0;

while ($row = $result->fetch_assoc()) {
    $amount = $row['amount'];
    $type = $row['type'];

    // Calculate running total: Add for credit, Subtract for debit
    if ($type == 'debit') {
        $totalBalance += $amount;
    } else {
        $totalBalance -= $amount;
    }

    $output .= "<tr>
                    <td>{$row['details']}</td>
                    <td>{$row['transaction_date']}</td>
                    <td>{$row['amount']}</td>          
                    <td>" . (ucfirst($type)=='Credit'?'Credit to DL A/C':'Debit From DL A/C') . "</td>
                    <td><a href='#' class='btn btn-danger btn-sm delete-transaction' data-id='".$row['id']."'><i class='bi bi-trash'></i></a></td>
                </tr>";
}

// Append balance row only if there are transactions
if ($result->num_rows > 0) {
    $output .= "<tr>
                    <td colspan='2'><strong>Subtotal Balance:</strong></td>
                    <td><strong>{$totalBalance}</strong></td>
                </tr>";
}

echo $output;
$conn->close();


?>