<?php
$host = 'localhost';
$db = 'foreign_trade';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch loan stats

$demandLoanQuery =  "WITH loan_summary AS ( 
SELECT 
SUM(CASE WHEN dlet.type = 'debit' THEN dlet.amount ELSE 0 END) AS total_debit, 
SUM(CASE WHEN dlet.type = 'credit' THEN dlet.amount ELSE 0 END) AS total_credit, 
( 
COALESCE(SUM(CASE WHEN dlet.type = 'debit' THEN dlet.amount ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN dlet.type = 'credit' THEN dlet.amount ELSE 0 END), 0) ) AS final_balance 
FROM demand_loan_entry dle 
JOIN companies c ON dle.company_id = c.id 
LEFT JOIN demand_loan_entry_transactions dlet ON dlet.demand_loan_id = dle.id 
GROUP BY dle.dl_no, dle.company_id, c.company_name ) 
SELECT 
SUM(ls.total_debit) OVER () AS accumulated_total_debit, 
SUM(ls.total_credit) OVER () AS accumulated_total_credit, 
SUM(ls.final_balance) OVER () AS accumulated_final_balance 
FROM loan_summary ls 
limit 0,1";
$demandLoan = $conn->query($demandLoanQuery)->fetch_assoc();

$demandLoanQueryOne = "SELECT COUNT(*) AS total_loans  FROM demand_loan_entry";
$demandLoan_1 = $conn->query($demandLoanQueryOne)->fetch_assoc();


$termLoanQuery = "WITH loan_summary AS ( 
    SELECT 
        SUM(CASE WHEN tlet.type = 'debit' THEN tlet.amount ELSE 0 END) AS total_debit, 
        SUM(CASE WHEN tlet.type = 'credit' THEN tlet.amount ELSE 0 END) AS total_credit, 
        (
            COALESCE(SUM(CASE WHEN tlet.type = 'debit' THEN tlet.amount ELSE 0 END), 0) - 
            COALESCE(SUM(CASE WHEN tlet.type = 'credit' THEN tlet.amount ELSE 0 END), 0)
        ) AS final_balance 
    FROM term_loan tle
    JOIN companies c ON tle.company_id = c.id 
    LEFT JOIN term_loan_entry_transactions tlet ON tlet.term_loan_id = tle.id 
    GROUP BY tle.tl_no, tle.company_id, c.company_name
)

SELECT 
    SUM(ls.total_debit) OVER () AS accumulated_total_debit, 
    SUM(ls.total_credit) OVER () AS accumulated_total_credit, 
    SUM(ls.final_balance) OVER () AS accumulated_final_balance 
FROM loan_summary ls 
LIMIT 1";
$termLoan = $conn->query($termLoanQuery)->fetch_assoc();

$termLoanQuery_1 = "SELECT COUNT(*) AS total_loans FROM term_loan";
$termLoan_1 = $conn->query($termLoanQuery_1)->fetch_assoc();





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4">Loan Dashboard</h2>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-6">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Demand Loans</h5>
                        <p class="card-text fs-4"><?php echo $demandLoan_1['total_loans']; ?></p>
                        <h6>Total Outstanding Amount: <?php echo number_format($demandLoan['accumulated_final_balance'], 2); ?></h6>
                        <h6>Total Received Amount: <?php echo number_format($demandLoan['accumulated_total_credit'], 2); ?></h6>
                        <h6>Total Remaining Amount: <?php echo number_format($demandLoan['accumulated_total_debit'], 2); ?></h6>

                        <table class="table table-bordered table-striped table-hover mt-4">
    <thead class="thead-dark">
        <tr>
            <th>Loan Type</th>
            <th>Sub Total</th>
            <th>Total Recovery</th>
            <th>Present Outstanding</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Database query
        $query = "SELECT 
                    dle.type AS loan_type,
                    SUM(dle.sub_total) AS total_sub_total,
                    SUM(dle.total_recovery) AS total_recovery,
                    SUM(dle.present_outstanding) AS total_present_outstanding
                  FROM 
                    demand_loan_entry dle
                  WHERE 
                    dle.type IN ('CASH DEFERRED', 'CASH SIGHT', 'BACK TO BACK', 'Export Development Fund (EDF)', 'Other (cc)')
                  GROUP BY 
                    dle.type
                  ORDER BY 
                    FIELD(dle.type, 'CASH DEFERRED', 'CASH SIGHT', 'BACK TO BACK', 'Export Development Fund (EDF)', 'Other (cc)')";

        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['loan_type']) . "</td>";
            echo "<td class='text-end'>" . number_format($row['total_sub_total'], 2) . "</td>";
            echo "<td class='text-end'>" . number_format($row['total_recovery'], 2) . "</td>";
            echo "<td class='text-end'>" . number_format($row['total_present_outstanding'], 2) . "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Term Loans</h5>
                        <p class="card-text fs-4"><?php echo $termLoan_1['total_loans']; ?></p>
                        
                        <h6>Total Outstanding Amount: <?php echo number_format($termLoan['accumulated_final_balance'], 2); ?></h6>
                        <h6>Total Received Amount: <?php echo number_format($termLoan['accumulated_total_credit'], 2); ?></h6>
                        <h6>Total Remaining Amount: <?php echo number_format($termLoan['accumulated_total_debit'], 2); ?></h6>

                        <table class="table table-bordered table-striped table-hover mt-4">
    <thead class="thead-dark">
        <tr>
            <th>Loan Type</th>
            <th>Sub Total</th>
            <th>Total Recovery</th>
            <th>Present Outstanding</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Database query
        $query = "SELECT 
                    dle.lc_type AS loan_type,
                    SUM(dle.sub_total) AS total_sub_total,
                    SUM(dle.total_recovery) AS total_recovery,
                    SUM(dle.present_outstanding) AS total_present_outstanding
                  FROM 
                    term_loan dle
                  WHERE 
                    dle.lc_type IN ('CASH DEFERRED', 'CASH SIGHT', 'BACK TO BACK', 'Export Development Fund (EDF)', 'Other (cc)')
                  GROUP BY 
                    dle.lc_type
                  ORDER BY 
                    FIELD(dle.lc_type, 'CASH DEFERRED', 'CASH SIGHT', 'BACK TO BACK', 'Export Development Fund (EDF)', 'Other (cc)')";

        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['loan_type']) . "</td>";
            echo "<td class='text-end'>" . number_format($row['total_sub_total'], 2) . "</td>";
            echo "<td class='text-end'>" . number_format($row['total_recovery'], 2) . "</td>";
            echo "<td class='text-end'>" . number_format($row['total_present_outstanding'], 2) . "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <canvas id="loanChart"></canvas>
            </div>
        </div>

       

    <script>
        // Load Chart.js Pie Chart
        const ctx = document.getElementById('loanChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Demand Loans', 'Term Loans'],
                datasets: [{
                    data: [<?php echo $demandLoan_1['total_loans']; ?>, <?php echo $termLoan_1['total_loans']; ?>],
                    backgroundColor: ['#007bff', '#28a745']
                }]
            }
        });

        // DataTable Initialization
        $(document).ready(function() {
            $('#loanTable').DataTable();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>