<?php
$host = 'localhost';
$db = 'foreign_trade';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$company_id = $_GET['term_loan_id'];
$sql = "SELECT id, details, transaction_date, amount, type FROM term_loan_entry_transactions WHERE term_loan_id = ? ORDER BY transaction_date ASC";
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
                    <td><a href='#' class='btn btn-danger btn-sm delete-term-transaction' data-id='".$row['id']."'><i class='bi bi-trash'></i></a></td>
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