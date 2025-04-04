<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Term Loan Transactions Report</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>


    <h2>Term Loan Transactions Report</h2>
    <label>From Date:</label> 
    <input type="text" id="from_date" placeholder="YYYY-MM-DD">
    <label>To Date:</label> 
    <input type="text" id="to_date" placeholder="YYYY-MM-DD">
    <button id="filterBtn">Filter</button>

    <table id="reportTable" class="display">
        <thead>
            <tr>
                <th>Company Name</th>
                <th>Term Loan No</th>
             
                <th>Transaction Date</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

<script>
$(document).ready(function() {
    $("#from_date, #to_date").datepicker({ dateFormat: 'yy-mm-dd' });

    var table = $('#reportTable').DataTable({
        paging: false,
        dom: 'Bfrtip',
        buttons: [
                    'csv', 'excel', 'pdf'
                ],
        columns: [
            { data: "company_name" },
            { data: "dl_no" },
           
            { data: "transaction_date" },
            { data: "type" },
            { data: "amount" },
            { data: "details" }
        ]
    });

    $('#filterBtn').click(function() {
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();

        if (fromDate && toDate) {
            $.ajax({
                url: 'fetch_term_loan_transactions.php',
                method: 'POST',
                data: { from_date: fromDate, to_date: toDate },
                dataType: 'json',
                success: function(response) {
                    table.clear().rows.add(response.data).draw();
                }
            });
        } else {
            alert("Please select both From and To dates.");
        }
    });
});
</script>