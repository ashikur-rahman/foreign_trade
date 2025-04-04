<table id="DemandLoanReportTable" class="display">
    <thead>
        <tr>
            <!-- <th>Demand Loan ID</th> -->
            <th>DL No</th>
            <th>Company Name</th>
            <th>Last Transaction Date</th>
            <th>Last Transaction Amount</th>
            <th>Total Debit</th>
            <th>Total Credit</th>
            <th>Final Balance</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<script>
    $(document).ready(function () {
    $('#DemandLoanReportTable').DataTable({
        dom: 'Bfrtip',
                paging: false,
                buttons: [
                    'csv', 'excel', 'pdf'
                ],
        "ajax": {
            "url": "report_demand_loan_api.php", // Replace with your API endpoint or backend script
            "type": "GET",
            "dataSrc": ""
        },
        "columns": [
            // { "data": "demand_loan_id" },
            { "data": "dl_no" },
            { "data": "company_name" },
            { "data": "last_transaction_date" },
            { "data": "last_transaction_amount" },
            { "data": "total_debit" },
            { "data": "total_credit" },
            { "data": "final_balance" }
        ]
    });
});

</script>