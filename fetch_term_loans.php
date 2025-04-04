<?php
include 'db_connection.php';

$sql = "SELECT * FROM term_loan";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Sanction No</th>
                <th>Sanction Date</th>
                <th>Reschedule Amount</th>
                <th>Installment Amount</th>
                <th>Installment Frequency</th>
                <th>First Installment Date</th>
                <th>Grace Period</th>
                <th>Last Installment Date</th>
                <th>Special Condition</th>
                <th>Actions</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['sanction_no']}</td>
                <td>{$row['sanction_date']}</td>
                <td>{$row['reschedule_amount']}</td>
                <td>{$row['installment_amount']}</td>
                <td>{$row['installment_frequency']}</td> 
                <td>{$row['first_installment_date']}</td> 
                <td>{$row['grace_period']}</td>
                <td>{$row['last_installment_date']}</td>
                <td>{$row['special_condition']}</td>
                <td>
                    <a href='update_term_loan.php?id={$row['id']}'>Edit</a> |
                    <a href='delete_term_loan.php?id={$row['id']}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No records found.";
}

$conn->close();
?>
