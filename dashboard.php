<?php
session_start();
$host = 'localhost';
$db = 'foreign_trade';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Super') {
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">


    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- 
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> -->

<style>
        .wrapper {
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            padding: 20px;
            height: 100%;
        }
        .sidebar a {
            color: white;
            display: flex;
            align-items: center;
            padding: 10px;
            text-decoration: none;
            border-bottom: 1px solid #495057;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .sidebar i {
            margin-right: 10px;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }

    </style>
</head>
<body>
<div class="wrapper">
        <div class="sidebar">
            <h5> <?php echo "Welcome, " . $_SESSION['role']." ". $_SESSION['user']; ?></h5>
            <hr>
            <a href="dashboard.php?page=admin_panel"><i class="bi bi-gear-fill"></i>Dashboard</a>
            <a href="dashboard.php?page=company"><i class="bi bi-buildings"></i>Manage Company</a>
            <!-- <a href="dashboard.php?page=lc"><i class="bi bi-file-earmark-bar-graph-fill"></i>Manage LC</a> -->
            <!-- <a href="dashboard.php?page=lc"><i class="bi bi-file-earmark-bar-graph-fill"></i>Manage Letter of Credit</a> -->
            <a href="dashboard.php?page=demand_loan"><i class="bi bi-file-text"></i>Manage Demand Loan</a>
            <a href="dashboard.php?page=term_loan"><i class="bi bi-file-spreadsheet"></i>Manage Term Loan</a>
            <a href="dashboard.php?page=demand_loan_reports"><i class="bi bi-file-earmark-bar-graph-fill"></i>Demand Loan Reports</a> 
            <a href="dashboard.php?page=report_term_loan"><i class="bi bi-file-earmark-bar-graph-fill"></i>Term Loan Reports</a> 
            <!-- <a href="dashboard.php?page=reports"><i class="bi bi-file-earmark-bar-graph-fill"></i>Report (Demand)</a>    
            <a href="dashboard.php?page=reports_term"><i class="bi bi-file-earmark-bar-graph-fill"></i>Report (Term)</a>  -->
            <a href="dashboard.php?page=user"><i class="bi bi-people-fill"></i> Manage User</a>
            <?php
            echo "<a href='index.php?logout=true' style='color:white; font-weight:bold;'>Logout</a>";
            ?>
        </div>
        <div class="content">
            <h4>Foreign Trade Management System (Demand Loans and Term Loans)</h4>
            <h6>One-Time Entry, Lifetime Service – Effortless Software, Endless Value!</h6>
            <h6> From 01/10/2024 to till date Interest Rate 13.40% .If you have any qustions regarding the operation of the software, please ask to Md. Ashikur Rahman </h6>
            <!-- <marquee> From 01/10/2024 to till date Interest Rate 13.40% .If you have any qustions regarding the operation of the software, please ask to Md. Ashikur Rahman, Principal Officer </marquee> -->
            <hr>
           
            <?php
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                if ($page == "lc") {
                    echo "<h4>Manage Letter of Credit</h4>";
                    // User management logic here
                } elseif ($page == "company") {
                    echo "<h4>Manage Party</h4><hr><hr>";
                    include("company.php");
                } elseif ($page == "admin_panel") {
                    // echo "<h4>Dashboard</h4>";
                    include("admin_panel.php");
                } elseif ($page == "demand_loan") {
                    echo "<h4>Manage Demand Loan</h4>";
                    include("demand_loan.php");
                }   elseif ($page == "term_loan") {  
                    echo "<h4>Manage Term Loan</h4>";
                    include("term_loan.php");
                }   elseif ($page == "debit_credit_entry") { 
                    echo "<h4>Manage Debit Credit of the loan</h4>";
                    // Reports logic here
                }   elseif ($page == "reports") { 
                    echo "<h4>Manage Reports for Demand Loans</h4>";
                    include("report.php");
                }   elseif ($page == "demand_loan_reports") { 
                    echo "<h4>Manage Reports for Demand Loans</h4>";
                    include("demand_loan_reports.php");
                    
                }  elseif ($page == "report_term_loan") { 
                    echo "<h4>Manage Reports for Term Loans</h4>";
                    include("report_term_loan.php");
                    
                } 
                elseif ($page == "reports_term") { 
                    echo "<h4>Manage Reports for Term Loans</h4>";
                    include("report_term.php");
                } elseif ($page == "user") {

                            echo "<h4>Manage Users</h4>";
                            echo "<table id='userTable' class='display'>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                        </tr>
                                    </thead>
                                    <tbody>";
                            $query = "SELECT * FROM users";
                            $result = $conn->query($query);
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['username']}</td>
                                        <td>{$row['role']}</td>
                                      </tr>";
                            }
                            echo "</tbody>
                                </table>";
                       
                   
                    
                }else {
                    echo "<h2>Page not found</h2>";
                }
            } else {
                //echo "<p>Welcome to the Admin Dashboard</p>";
                include("admin_panel.php");
            }
            ?>
        </div>
    </div>

    
    <script>
        $(document).ready(function() {


    //var gArrayFonts = ['Amethysta','Poppins','Poppins-Bold','Poppins-Black','Poppins-Extrabold','Poppins-Extralight','Poppins-Light','Poppins-Medium','Poppins-Semibold','Poppins-Thin'];

      $('#address').summernote({ 
            // fontNames: gArrayFonts,
            // fontNamesIgnoreCheck: gArrayFonts,  
            popover: {
            image: [
                ['custom',['imageAttributes','imageShapes','captionIt']],
                ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                ['remove', ['removeMedia']]
            ],
            link: [
                ['link', ['linkDialogShow', 'unlink']]
            ],
            table: [
                ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
                ['delete', ['deleteRow', 'deleteCol', 'deleteTable']],
            ],
            air: [
                ['color', ['color']],
                ['font', ['bold', 'underline', 'clear']],
                ['para', ['ul', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']]
            ]
            },
            imageAttributes:{
                icon:'<i class="note-icon-pencil"/>',
                removeEmpty:false, // true = remove attributes | false = leave empty if present
                disableUpload: false // true = don't display Upload Options | Display Upload Options
            },
           
            toolbar: [
           
            ['style', ['bold', 'italic', 'underline', 'clear']],
           // ['fontname', ['fontname']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['undo'],['redo'],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']],
            ['misc', ['print']],

            ],    
            placeholder: 'Write full content...',
            tabsize: 2,
            height: 100,
            callbacks: {
                onImageUpload: function(files){
                that = $(this);
                sendFile(files[0], that);
                }
            }
        
        });



        $('.address').summernote({ 
            // fontNames: gArrayFonts,
            // fontNamesIgnoreCheck: gArrayFonts,  
            
            popover: {
            image: [
                ['custom',['imageAttributes','imageShapes','captionIt']],
                ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                ['remove', ['removeMedia']]
            ],
            link: [
                ['link', ['linkDialogShow', 'unlink']]
            ],
            table: [
                ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
                ['delete', ['deleteRow', 'deleteCol', 'deleteTable']],
            ],
            air: [
                ['color', ['color']],
                ['font', ['bold', 'underline', 'clear']],
                ['para', ['ul', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']]
            ]
            },
            imageAttributes:{
                icon:'<i class="note-icon-pencil"/>',
                removeEmpty:false, // true = remove attributes | false = leave empty if present
                disableUpload: false // true = don't display Upload Options | Display Upload Options
            },
           
            toolbar: [
           
            ['style', ['bold', 'italic', 'underline', 'clear']],
           // ['fontname', ['fontname']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['undo'],['redo'],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']],
            ['misc', ['print']],

            ],    
            placeholder: 'Write full content...',
            tabsize: 2,
            height: 100,
            callbacks: {
                onImageUpload: function(files){
                that = $(this);
                sendFile(files[0], that);
                }
            }
        
        });





            $('#userTable').DataTable({

                responsive: true,
        destroy: true,
        autoWidth: false,
        processing: true,
        searching: true,
        ordering: true,
       

                paging: false,
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'excel', 'pdf'
                ]
            });
            $('#demandLoanTable').DataTable({


                responsive: true,
                destroy: true,
                autoWidth: false,
                processing: true,
                searching: true,
                ordering: true,
       
                dom: 'Bfrtip',
                paging: false,
                buttons: [
                    'csv', 'excel', 'pdf'
                ],

                "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api(), data;
                        
                        // Helper function to sum up a column
                        var intVal = function (i) {
                            return typeof i === 'string' 
                                ? i.replace(/[\$,]/g, '') * 1 
                                : typeof i === 'number' 
                                ? i 
                                : 0;
                        };

                        // Columns index (Update these based on your table structure)
                        var rescheduleAmountIndex = 6;  // "Reschedule Amount" column index
                        var totalRecoveryIndex = 7;  // "Total Recovery" column index
                        var presentOutstandingIndex = 8;  // "Present Outstanding" column index

                        // Calculate total for each field
                        var totalReschedule = api.column(rescheduleAmountIndex, { page: 'current' }).data()
                            .reduce((a, b) => intVal(a) + intVal(b), 0);

                        var totalRecovery = api.column(totalRecoveryIndex, { page: 'current' }).data()
                            .reduce((a, b) => intVal(a) + intVal(b), 0);

                        var presentOutstanding = api.column(presentOutstandingIndex, { page: 'current' }).data()
                            .reduce((a, b) => intVal(a) + intVal(b), 0);

                        // Update the footer
                        $(api.column(rescheduleAmountIndex).footer()).html(totalReschedule.toLocaleString());
                        $(api.column(totalRecoveryIndex).footer()).html(totalRecovery.toLocaleString());
                        $(api.column(presentOutstandingIndex).footer()).html(presentOutstanding.toLocaleString());
                    }
           
           
           
            });

            $('#demandLoanTable_term').DataTable({


            responsive: true,
            destroy: true,
            autoWidth: false,
            processing: true,
            searching: true,
            ordering: true,

            dom: 'Bfrtip',
            paging: false,
            buttons: [
                'csv', 'excel', 'pdf'
            ],

            "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    
                    // Helper function to sum up a column
                    var intVal = function (i) {
                        return typeof i === 'string' 
                            ? i.replace(/[\$,]/g, '') * 1 
                            : typeof i === 'number' 
                            ? i 
                            : 0;
                    };

                    // Columns index (Update these based on your table structure)
                    var rescheduleAmountIndex = 11;  // "Reschedule Amount" column index
                    var totalRecoveryIndex = 12;  // "Total Recovery" column index
                    var presentOutstandingIndex = 13;  // "Present Outstanding" column index

                    // Calculate total for each field
                    var totalReschedule = api.column(rescheduleAmountIndex, { page: 'current' }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    var totalRecovery = api.column(totalRecoveryIndex, { page: 'current' }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    var presentOutstanding = api.column(presentOutstandingIndex, { page: 'current' }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Update the footer
                    $(api.column(rescheduleAmountIndex).footer()).html(totalReschedule.toLocaleString());
                    $(api.column(totalRecoveryIndex).footer()).html(totalRecovery.toLocaleString());
                    $(api.column(presentOutstandingIndex).footer()).html(presentOutstanding.toLocaleString());
                }



            });
            
            $('#companyTable').DataTable({
                responsive: true,
        destroy: true,
        autoWidth: false,
        processing: true,
        searching: true,
        ordering: true,
      
                dom: 'Bfrtip',
                paging: false,
                buttons: [
                    'csv', 'excel', 'pdf'
                ]
            });

            $('.open-demand-loan-modal').click(function() {
                $('#demandLoanEntryModal').modal('show');
            });

            $('.open-term-loan-modal').click(function() {
                $('#termLoanEntryModal').modal('show');
            });

            $('.open-company-entry-modal').click(function() {
                $('#companyEntryModal').modal('show');
            });


            $('.open-modal').click(function() {
                let companyId = $(this).data('id');
                let companyName = $(this).data('name');
                $('#modalLabel').text('Add Transaction for ' + companyName);
                $('#company_id').val(companyId);
                $('#transactionModal').modal('show');
            });

            $('.open-term-modal').click(function() {
                let term_load_id = $(this).data('id');
                let companyName = $(this).data('name');
                $('#termModalLabel').text('Add Term Loan Transaction for ' + companyName);
                $('#term_loan_id').val(term_load_id);
                $('#termLoanTransactionModal').modal('show');
            });

            $('.open-termloan-modal').click(function() {
                let companyId = $(this).data('id');
                let companyName = $(this).data('name');

                console.log(companyId);
                $('#modalLabelTerm').text('Transfer the Demand Loan to Term Loan for ' + companyName);
                $('#company_id_term').val(companyId);
                $('#termLoanEntryModal').modal('show');
            });

            
           
            $('.view-details').click(function() {
                let companyId = $(this).data('id');
                let companyName = $(this).data('name');
                $('#detailsModalLabel').text('Transaction details for ' + companyName);

                $.ajax({
                    url: 'fetch_transactions.php',
                    type: 'GET',
                    data: { company_id: companyId },
                    success: function(response) {
                        $('#transactionDetailsBody').html(response);
                        $('#transactionDetailsModal').modal('show');
                    }
                });
            });

            

            $('.view-term-loan-details').click(function() {
                let companyId = $(this).data('id');
                let companyName = $(this).data('name');
                $('#detailsTermModalLabel').text('Transaction details for ' + companyName);

                $.ajax({
                    url: 'fetch_term_transactions.php',
                    type: 'GET',
                    data: { term_loan_id: companyId },
                    success: function(response) {
                        $('#transactionDetailsBodyTerm').html(response);
                        $('#transactionDetailsModalforTermLoan').modal('show');
                    }
                });
            });


        });


        $(document).ready(function() {
            // Delete Transaction
            $(document).on("click", ".delete-transaction", function() {
                let transactionId = $(this).data("id");
                let row = $(this).closest("tr");

                if (confirm("Are you sure you want to delete this transaction?")) {
                    $.ajax({
                        url: "delete_transaction.php", // Server-side script for deletion
                        type: "POST",
                        data: { id: transactionId },
                        success: function(response) {
                            if (response === "success") {
                                row.remove(); // Remove row from table
                                alert("Transaction deleted successfully.");
                            } else {
                                alert("Error deleting transaction.");
                            }
                        }
                    });
                }
            });
        });

        $(document).ready(function() {
            // Delete Transaction
            $(document).on("click", ".delete-term-transaction", function() {
                let transactionId = $(this).data("id");
                let row = $(this).closest("tr");

                if (confirm("Are you sure you want to delete this term loan transaction?")) {
                    $.ajax({
                        url: "delete_term_loan_transaction.php", // Server-side script for deletion
                        type: "POST",
                        data: { id: transactionId },
                        success: function(response) {
                            if (response === "success") {
                                row.remove(); // Remove row from table
                                alert("Transaction deleted successfully.");
                            } else {
                                alert("Error deleting transaction.");
                            }
                        }
                    });
                }
            });
        });


    </script>

    <script>

    $(document).ready(function() {

        $(".view-loan-details").click(function() {
        let companyId = $(this).data("id");

        $.ajax({
            url: "fetch_loan_details.php",
            type: "POST",
            data: { id: companyId },
            success: function(response) {

                let data = JSON.parse(response);
               
                $("#companyName").text(data.company.company_name);
                $("#companyType").text(data.company.company_type);
                $("#companyContact").text(data.company.contact_number);


             

                let loanHtml = "";
                data.loans.forEach(loan => {
                    loanHtml += `<tr>
                        <td>${loan.dl_no}</td>
                        <td>${loan.disburse_date}</td>
                        <td>${loan.expiry_date}</td>
                      
                    </tr>`;
                });
                $("#loanDetails").html(loanHtml);

                let balance = 0;
                let transactionHtml = "";
                data.transactions.forEach(trans => {
                    if (trans.type === "debit") balance += parseFloat(trans.amount);
                    else balance -= parseFloat(trans.amount);

                    transactionHtml += `<tr>
                        <td>${trans.transaction_date}</td>
                        <td>${trans.details}</td>
                        <td>${(trans.type==='debit'?trans.amount:'')}</td>
                        <td>${(trans.type!=='debit'?trans.amount:'')}</td>
                        <td>${balance.toFixed(2)}</td>
                    </tr>`;
                });

                $("#transactionDetails").html(transactionHtml);


                let totalDebit = 0, totalCredit = 0;
        
        data.transactions.forEach(trans => {
            let amount = parseFloat(trans.amount);
            if (trans.type === "debit") {
               // balance += amount;
                totalDebit += amount;
                
            } else {
               // balance -= amount;
                totalCredit += amount;
               
            }

            // transactionHtml += `<tr>
            
            //     <td>${trans.transaction_date}</td>
            //     <td>${trans.details}</td>
            //     <td>${(trans.type==='debit' ? amount.toFixed(2) : '')}</td>
            //     <td>${(trans.type!=='debit' ? amount.toFixed(2) : '')}</td>
            //     <td>${balance.toFixed(2)}</td>
            // </tr>`;
        });

        transactionHtml += `<tr class='table-success'>
            <td colspan="2"><strong>Final Total</strong></td>
            <td><strong>${totalDebit.toFixed(2)}</strong></td>
            <td><strong>${totalCredit.toFixed(2)}</strong></td>
            <td><strong>${balance.toFixed(2)}</strong></td>
        </tr>`;

        $("#transactionDetails").html(transactionHtml);

                











                $("#finalBalance").text(balance.toFixed(2));

                $("#loanModal").modal("show");
            }
        });
    });

    $(".view-loan-term-details").click(function() {
        let companyId = $(this).data("id");

        $.ajax({
            url: "fetch_term_loan_details.php",
            type: "POST",
            data: { id: companyId },
            success: function(response) {

                let data = JSON.parse(response);
               
                $("#companyName").text(data.company.company_name);
                $("#companyType").text(data.company.company_type);
                $("#companyContact").text(data.company.contact_number);
                $("#companyAddress").html(data.company.address);


             

                let loanHtml = "";
                data.loans.forEach(loan => {
                    loanHtml += `<tr>
                        <td>${loan.dl_nos}</td>
                        <td>${loan.sanction_date}</td>
                         <td>${loan.reschedule_amount}</td>
                        <td>${loan.installment_amount}</td>
                        <td>${loan.installment_frequency} month</td>
                        <td>${loan.first_installment_date}</td>
                        <td>${loan.grace_period}</td>
                        <td>${loan.last_installment_date}</td> 
                    </tr>`;
                });
                $("#termLoanDetails").html(loanHtml);

                let balance = 0;
                let transactionHtml = "";
                data.transactions.forEach(trans => {
                    if (trans.type === "debit") balance += parseFloat(trans.amount);
                    else balance -= parseFloat(trans.amount);

                    transactionHtml += `<tr>
                        <td>${trans.transaction_date}</td>
                        <td>${trans.details}</td>
                        <td>${(trans.type==='debit'?trans.amount:'')}</td>
                        <td>${(trans.type!=='debit'?trans.amount:'')}</td>
                        <td>${balance.toFixed(2)}</td>
                    </tr>`;
                });


                let totalDebit = 0, totalCredit = 0;
        
                data.transactions.forEach(trans => {
                    let amount = parseFloat(trans.amount);
                    if (trans.type === "debit") {
                        totalDebit += amount;
                    } else {
                        totalCredit += amount;
                    }
                });

                transactionHtml += `<tr class='table-success'>
                    <td colspan="2"><strong>Final Total</strong></td>
                    <td><strong>${totalDebit.toFixed(2)}</strong></td>
                    <td><strong>${totalCredit.toFixed(2)}</strong></td>
                    <td><strong>${balance.toFixed(2)}</strong></td>
                </tr>`;

    


                $("#transactionDetails").html(transactionHtml);
                $("#finalBalance").text(balance.toFixed(2));

                $("#termloanModal").modal("show");
            }
        });
    });

    $("#printPdf_all_company").click(function() {
        var printContents = $("#allCompanyLoanModal .modal-body").html(); // Get modal content
        var originalContents = document.body.innerHTML; // Save original page content

        document.body.innerHTML = "<html><head><title>Print</title></head><body>" + printContents + "</body></html>";

        window.print(); // Trigger print

        document.body.innerHTML = originalContents; // Restore original content
        location.reload(); // Reload page to restore events and styling
    });


    $("#printPdf").click(function() {
        var printContents = $("#loanModal .modal-body").html(); // Get modal content
        var originalContents = document.body.innerHTML; // Save original page content

        document.body.innerHTML = "<html><head><title>Print</title></head><body>" + printContents + "</body></html>";

        window.print(); // Trigger print

        document.body.innerHTML = originalContents; // Restore original content
        location.reload(); // Reload page to restore events and styling
    });

    $("#printPdf_company").click(function() {
        var printContents = $("#allLoanModal .modal-body").html(); // Get modal content
        var originalContents = document.body.innerHTML; // Save original page content

        document.body.innerHTML = "<html><head><title>Print</title></head><body>" + printContents + "</body></html>";

        window.print(); // Trigger print

        document.body.innerHTML = originalContents; // Restore original content
        location.reload(); // Reload page to restore events and styling
    });

   

    $("#downloadExcel").click(function () {
    // Extract the modal data
            var tableData = [];
            $("#loanModal .modal-body table tr").each(function () {

                var rowData = [];
                $(this).find("th, td").each(function () {
                    // Wrap data in double quotes and escape internal quotes
                    var cellData = $(this).text().trim().replace(/"/g, '""'); // Escape existing quotes
                    rowData.push('"' + cellData + '"'); // Wrap in double quotes
                });
                tableData.push(rowData.join(","));
            });

            // Convert data to CSV format
            let csvContent = "data:text/csv;charset=utf-8," + tableData.join("\n");

            // Create a download link
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "ModalData.csv");
            document.body.appendChild(link);

            link.click(); // Trigger download
        });

        $("#downloadExcel_").click(function () {

                // Extract the modal data
                var tableData = [];
                $("#allLoanModal .modal-body table tr").each(function () {
                    var rowData = [];
                    $(this).find("th, td").each(function () {
                        // Wrap data in double quotes and escape internal quotes
                        var cellData = $(this).text().trim().replace(/"/g, '""'); // Escape existing quotes
                        rowData.push('"' + cellData + '"'); // Wrap in double quotes
                    });
                    tableData.push(rowData.join(","));
                });

                // Convert data to CSV format
                let csvContent = "data:text/csv;charset=utf-8," + tableData.join("\n");

                // Create a download link
                var encodedUri = encodeURI(csvContent);
                var link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "ModalData.csv");
                document.body.appendChild(link);

                link.click(); // Trigger download

        });


        $("#printTermPdf").click(function() {
        var printContents = $("#termloanModal .modal-body").html(); // Get modal content
        //var printContents = $("#dlTransactionTable").html(); 
        
        var originalContents = document.body.innerHTML; // Save original page content

        document.body.innerHTML = "<html><head><title>Print</title></head><body>" + printContents + "</body></html>";

        window.print(); // Trigger print

        document.body.innerHTML = originalContents; // Restore original content
        location.reload(); // Reload page to restore events and styling
        });

        $("#downloadTermExcel").click(function () {
            // Extract the modal data
            var tableData = [];
            $("#termloanModal .modal-body table tr").each(function () {
                var rowData = [];
                $(this).find("th, td").each(function () {
                    rowData.push($(this).text().trim());
                });
                tableData.push(rowData);
            });

            // Convert data to CSV format
            let csvContent = tableData.map(e => e.join(",")).join("\n");

            // Create a Blob with UTF-8 BOM for correct encoding
            var blob = new Blob(["\uFEFF" + csvContent], { type: "text/csv;charset=utf-8;" });

            // Create a download link
            var link = document.createElement("a");
            var currentdate = new Date().toISOString().slice(0, 10); // Get YYYY-MM-DD format
            link.href = URL.createObjectURL(blob);
            link.download = "Term_loan_details_" + currentdate + ".csv";
            document.body.appendChild(link);

            link.click(); // Trigger download
        });

$(".view-all-loan-details").click(function() {
    let companyId = $(this).data("id");

$.ajax({
    url: "fetch_all_loan_details.php",
    type: "POST",
    data: { id: companyId },
    success: function(response) {
        let data = JSON.parse(response);
        console.log(data);
        $("#companyName").text(data.company.company_name);
        $("#companyType").text(data.company.company_type);
        $("#companyContact").text(data.company.contact_number);
        $("#companyAddress").html(data.company.address);

        // Demand Loan Data
        let loanHtml = "";
        data.loans.forEach(loan => {
            loanHtml += `<tr>
                <td>${loan.dl_no}</td>
                <td>${loan.disburse_date}</td>
                <td>${loan.expiry_date}</td>
            </tr>`;
        });
        $("#loanDetails").html(loanHtml);

        let balance = 0, totalDebit = 0, totalCredit = 0;
        let transactionHtml = "";
        data.transactions.forEach(trans => {
            let amount = parseFloat(trans.amount);
            if (trans.type === "debit") {
                balance += amount;
                totalDebit += amount;
                
            } else {
                balance -= amount;
                totalCredit += amount;
               
            }

            transactionHtml += `<tr>
                <td>${trans.demand_lone_number}</td>
                <td>${trans.transaction_date}</td>
                <td>${trans.details}</td>
                <td>${(trans.type==='debit' ? amount.toFixed(2) : '')}</td>
                <td>${(trans.type!=='debit' ? amount.toFixed(2) : '')}</td>
                <td>${balance.toFixed(2)}</td>
            </tr>`;
        });

        transactionHtml += `<tr class='table-success'>
            <td colspan="3"><strong>Final Total</strong></td>
            <td><strong>${totalDebit.toFixed(2)}</strong></td>
            <td><strong>${totalCredit.toFixed(2)}</strong></td>
            <td><strong>${balance.toFixed(2)}</strong></td>
        </tr>`;

        $("#transactionDetails").html(transactionHtml);

        // Term Loan Data
        let termLoanHtml = "";
        data.term_loans.forEach(loan => {
            termLoanHtml += `<tr>
                <td>${loan.sanction_no}</td>
                <td>${loan.sanction_date}</td>
                <td>${loan.reschedule_amount}</td>
                <td>${loan.installment_amount}</td>
                <td>${loan.last_installment_date}</td>
            </tr>`;
        });
        $("#termLoanDetails").html(termLoanHtml);

        let termBalance = 0, termTotalDebit = 0, termTotalCredit = 0;
        let termTransactionHtml = "";
        data.term_transactions.forEach(trans => {
            let amount = parseFloat(trans.amount);
            if (trans.type === "debit") {
                termBalance += amount;
                termTotalDebit += amount;
                
            } else {
                termBalance -= amount;
                termTotalCredit += amount;
              
            }

            termTransactionHtml += `<tr>
                <td>${trans.term_lone_number}</td>
                <td>${trans.transaction_date}</td>
                <td>${trans.details}</td>
                <td>${(trans.type==='debit' ? amount.toFixed(2) : '')}</td>
                <td>${(trans.type!=='debit' ? amount.toFixed(2) : '')}</td>
                <td>${termBalance.toFixed(2)}</td>
            </tr>`;
        });

        termTransactionHtml += `<tr class='table-success'>
            <td colspan="3"><strong> Final Total</strong></td>
            <td><strong>${termTotalDebit.toFixed(2)}</strong></td>
            <td><strong>${termTotalCredit.toFixed(2)}</strong></td>
            <td><strong>${termBalance.toFixed(2)}</strong></td>
        </tr>`;

        $("#termTransactionDetails").html(termTransactionHtml);
        $("#allLoanModal").modal("show");
    }
});

});




        $(".view-all-company-loan-details").click(function () {
        let companyId = $(this).data("id");

        $.ajax({
            url: "fetch_full_company_details.php",
            type: "POST",
            data: { id: companyId },
            dataType: "json",
            success: function (data) {
                console.log(data);
                populateLoanModal(data);
                $("#allCompanyLoanModal").modal("show");
            },
            error: function () {
                alert("Failed to fetch data. Please try again.");
            }
        });
    });

function populateLoanModal(data) {
    $("#allCompanyName").text(data.company.company_name);
    $("#allCompanyType").text(data.company.company_type);
    $("#allCompanyAddress").html(data.company.address);
    $("#allCompanyContact").text(data.company.contact_number);

    let accordionContent = '';
    data.child_companies.forEach((child, index) => {
        let childCompanyId = `childCompany-${index}`;

        accordionContent += `
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading${index}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${childCompanyId}" aria-expanded="false">
                        ${child.company_name}
                    </button>
                </h2>
                <div id="${childCompanyId}" class="accordion-collapse collapse" aria-labelledby="heading${index}" data-bs-parent="#companyAccordion">
                    <div class="accordion-body">
                        <ul class="nav nav-tabs" id="loanTabs${index}">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#demandLoan${index}">Demand Loan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#termLoan${index}">Term Loan</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-3">
                            <!-- Demand Loan Section -->
                            <div class="tab-pane fade show active" id="demandLoan${index}">
                                <h5>Demand Loan Details</h5>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>D/L No</th>
                                            <th>Disbursement Date</th>
                                            <th>Expiry Date</th>
                                            <th>Loan Creation Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${child.demand_loans.map(loan => `
                                            <tr>
                                                <td>${loan.dl_no}</td>
                                                <td>${loan.disburse_date}</td>
                                                <td>${loan.expiry_date}</td>
                                                <td>${loan.loan_creation_amount}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                                <h5>Demand Loan Transactions</h5>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>D/L No</th>
                                            <th>Transaction Date</th>
                                            <th>Details</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${child.demand_loans.flatMap(loan => loan.transactions.map(transaction => `
                                            <tr>
                                                <td>${transaction.demand_loan_id}</td>
                                                <td>${transaction.transaction_date}</td>
                                                <td>${transaction.details}</td>
                                                <td>${transaction.type}</td>
                                                <td>${transaction.amount}</td>
                                            </tr>
                                        `)).join('')}
                                    </tbody>
                                </table>
                            </div>

                            <!-- Term Loan Section -->
                            <div class="tab-pane fade" id="termLoan${index}">
                                <h5>Term Loan Details</h5>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sanction No</th>
                                            <th>Sanction Date</th>
                                            <th>Reschedule Amount</th>
                                            <th>Installment Amount</th>
                                            <th>Last Installment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${child.term_loans.map(loan => `
                                            <tr>
                                                <td>${loan.sanction_no}</td>
                                                <td>${loan.sanction_date}</td>
                                                <td>${loan.reschedule_amount}</td>
                                                <td>${loan.installment_amount}</td>
                                                <td>${loan.last_installment_date}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                                <h5>Term Loan Transactions</h5>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Transaction Date</th>
                                            <th>Details</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${child.term_loans.flatMap(loan => loan.transactions.map(transaction => `
                                            <tr>
                                                <td>${transaction.transaction_date}</td>
                                                <td>${transaction.details}</td>
                                                <td>${transaction.type}</td>
                                                <td>${transaction.amount}</td>
                                            </tr>
                                        `)).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
    });

    $("#companyAccordion").html(accordionContent);
}

});


$(document).ready(function () {
    // Populate update modal fields
    $(".update-company").click(function () {
        $("#updateCompanyId").val($(this).data("id"));
        $("#updateCompanyName").val($(this).data("name"));
        $("#updateCompanyType").val($(this).data("type"));
        $("#updateCompanyAddress").val($(this).data("address"));
        $("#updateCompanyContact").val($(this).data("contact"));
    });

    // Submit update form via AJAX
    $("#updateCompanyForm").submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: "update_company.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (response) {
                if (response === "success") {
                    alert("Company updated successfully!");
                    location.reload();
                } else {
                    alert("Update failed!");
                }
            }
        });
    });

    // Populate delete modal
    $(".delete-company").click(function () {
        $("#deleteCompanyId").val($(this).data("id"));
    });

    // Handle delete confirmation
    $("#confirmDelete").click(function () {
        let companyId = $("#deleteCompanyId").val();
        $.ajax({
            url: "delete_company.php",
            type: "POST",
            data: { id: companyId },
            success: function (response) {
                if (response === "success") {
                    alert("Company deleted successfully!");
                    location.reload();
                } else {
                    alert("Deletion failed!");
                }
            }
        });
    });
});

</script>
<script>
        function toggleParentCompany() {
            let companyType = document.getElementById("company_type").value;
            console.log(companyType);
            document.getElementById("parentCompany").style.display = (companyType === "Group") ? "block" : "none";
        }
    </script>

<script>
function editDemandLoan(data) {

    console.log(data);
    document.getElementById("update_id").value = data.id;
    document.getElementById("update_company_id").value = data.company_id;
    document.getElementById("update_type").value = data.type;
    document.getElementById("update_dl_no").value = data.dl_no;
    document.getElementById("update_disburse_date").value = data.disburse_date;
    document.getElementById("update_expiry_date").value = data.expiry_date;
    document.getElementById("update_loan_creation_amount").value = data.loan_creation_amount;
    document.getElementById("update_present_outstanding").value = data.present_outstanding;
    document.getElementById("update_sub_total").value = data.sub_total;
    document.getElementById("update_moad").value = data.moad;
    document.getElementById("update_classification").value = data.classification;
    document.getElementById("update_rf").value = data.rf;
    document.getElementById("update_latest_state").value = data.latest_state;
    document.getElementById("update_exchange_rate_of_dl").value = data.exchange_rate_of_dl;
    document.getElementById("update_reason_for_dl").value = data.reason_for_dl;
    document.getElementById("update_lc_nos").value = data.lc_nos;
    document.getElementById("update_lc_amount_usd").value = data.lc_amount_usd;
    document.getElementById("current_documents").textContent = data.loan_documents;
    document.getElementById("current_documents").value = data.loan_documents;
    //document.getElementsByClassName("update_view_id").textContent = data.id;
    document.getElementsByClassName("update_view_id")[0].textContent = data.id;
   
  
    var modal = new bootstrap.Modal(document.getElementById("updateDemandLoanModal"));
    document.getElementById('updateDemandLoanModal').addEventListener('shown.bs.modal', sendDocumentData);
    modal.show();
}

// Example: Get the content of dynamically loaded .current_documents class
function sendDocumentData() {
    const content = document.querySelector('.current_documents').textContent.trim();

    // Send the data to PHP using fetch or AJAX
    fetch('save_data.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'document_data=' + encodeURIComponent(content)
    })
    .then(response => response.text())
    .then(data => console.log('Data saved:', data))
    .catch(error => console.error('Error:', error));
}

// Call the function when modal is shown
document.getElementById('updateDemandLoanModal').addEventListener('shown.bs.modal', sendDocumentData);


function confirmDelete(id) {
    document.getElementById("delete_id").value = id;
    var modal = new bootstrap.Modal(document.getElementById("deleteDemandLoanModal"));
    modal.show();
}
</script>



<script>
$(document).ready(function () {

    $('.address').summernote( {
        dialogsInBody: true
    });
    // Load Data in Edit Modal
    $(".editTermLoan").click(function () {
        let id = $(this).data("id");
        $.ajax({
            url: "fetch_term.php",
            method: "POST",
            data: { id: id },
            dataType: "json",
            success: function (response) {

                $("#edit_id").val(response.id);
                $("#edit_company_id").val(response.company_id);
                $("#edit_sanction_no").val(response.sanction_no ?? "");
                $("#edit_sanction_date").val(response.sanction_date ?? "");
                $("#edit_reschedule_date").val(response.reschedule_date ?? "");
                $("#edit_reschedule_amount").val(response.reschedule_amount ?? "");

                // Handle radio buttons safely (for installment_frequency)
                const installmentFrequency = response.installment_frequency ?? "";
                $("input[name='installment_frequency'][value='" + installmentFrequency + "']").prop("checked", true);
                
                $("#edit_installment_amount").val(response.installment_amount ?? "");
                $("#edit_first_installment_date").val(response.first_installment_date ?? "");
                $("#edit_grace_period").val(response.grace_period ?? "");
                $("#edit_last_installment_date").val(response.last_installment_date ?? "");
                $("#edit_special_condition").val(response.special_condition ?? "");
                $("#edit_present_outstanding").val(response.present_outstanding ?? "");
                $("#edit_total_recovery").val(response.total_recovery ?? "");
                $("#edit_grace_period_details").val(response.grace_period_details ?? "");
                $("#edit_sub_total").val(response.sub_total ?? "");
                $("#edit_passing_authority").val(response.passing_authority ?? "");
                $("#edit_branch_code").val(response.branch_code ?? "");
                $("#edit_interest_rate").val(response.interest_rate ?? "");
                $("#edit_remarks").val(response.remarks ?? "");
                $("#edit_latest_state").val(response.latest_state ?? "");
                $("#edit_classification").val(response.classification ?? "");
                $("#edit_register_index_no").val(response.register_index_no ?? "");
                $("#edit_reschedule_no").val(response.reschedule_no ?? "");
                $("#edit_lc_type").val(response.lc_type ?? "");

                // Populate current documents
                // let documentsHtml = "";
                // if (response.loan_documents && response.loan_documents.length > 0) {
                //     response.loan_documents.forEach(function(doc) {
                //         documentsHtml += `<a href="${doc}" target="_blank">${doc}</a><br>`;
                //     });
                // }
              //  $("#current_documents_term").html(documentsHtml);

                document.getElementById("current_documents_term").value = response.loan_documents;

                $("#editTermLoanModal").modal("show");
            }
        });
    });

    // Update Term Loan
    $("#editTermLoanForm").submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: "update_term.php",
            method: "POST",
            data: $(this).serialize(),
            success: function (response) {
                location.reload();
            }
        });
    });

    // Delete Term Loan
    $(".deleteBtn").click(function () {
        let id = $(this).data("id");
        $("#confirmDelete").data("id", id);
        $("#deleteTermLoanModal").modal("show");
    });

    $("#confirmDelete").click(function () {
        let id = $(this).data("id");
        $.ajax({
            url: "delete_term.php",
            method: "POST",
            data: { id: id },
            success: function (response) {
                location.reload();
            }
        });
    });
});
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    
</body>
</html>
<?php } ?>