<div class="row">
                <div class="col-md-12">
                <div class="row">

                <!-- <h2>Upload Excel File</h2>
                <form action="import_term_loan.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="file" accept=".xlsx" required>
                    <button type="submit">Upload & Import</button>
                </form> -->
                <form method="post" action="">
                        <button type="submit" class="btn btn-info btn-sm" name="update_all_term_entries"> <i class='bi bi-clipboard-plus'></i> Update Term Loan Data</button>
                    </form>

<?php

if (isset($_POST['update_all_term_entries'])) {
    $sql = "
       UPDATE term_loan tl
JOIN (
    SELECT 
        tl.id AS term_loan_id,
        SUM(CASE WHEN tlet.type = 'debit' THEN tlet.amount ELSE 0 END) AS total_debit, 
        SUM(CASE WHEN tlet.type = 'credit' THEN tlet.amount ELSE 0 END) AS total_credit, 
        (
            COALESCE(SUM(CASE WHEN tlet.type = 'debit' THEN tlet.amount ELSE 0 END), 0) 
            - COALESCE(SUM(CASE WHEN tlet.type = 'credit' THEN tlet.amount ELSE 0 END), 0)
        ) AS final_balance 
    FROM term_loan tl
    LEFT JOIN term_loan_entry_transactions tlet 
        ON tlet.term_loan_id = tl.id 
    GROUP BY tl.id
) AS loan_summary ON tl.id = loan_summary.term_loan_id
SET 
    tl.present_outstanding = loan_summary.final_balance,
    tl.total_recovery = loan_summary.total_credit,
    tl.sub_total = loan_summary.final_balance
    ";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Records updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating records: " . $conn->error . "');</script>";
    }
}

?>
                    <!-- <div class="col-md-5">



                    
      
                    </div> -->
                    <div class="col-md-12">
                    <p class="text-success" style="font-weight: bold;">
                        <?php
                            if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
                            { 
                                echo $_SESSION['msg'];  
                            }
                            unset($_SESSION['msg']);   
                        ?> 
                    </p>
                    <style>
                        th{ padding: 1px;  font-size: 15px;}
                        td{ padding: 1px;  font-size: 15px;}
                        tbody{font-size: 14px;}
                    </style>
                    <style>
                        table.dataTable {
                        width: 100% !important;
                        }
                        table.dataTable td {
                        font-size: .8em !important;
                        }
                        table.dataTable tr.dtrg-level-0 td {
                        font-size: 1.5em !important;
                        }
                        table.dataTable td {
                            padding: 2px !important;
                        }
                        .dataTable thead .sorting_asc,
                        .dataTable thead .sorting_desc,
                        .dataTable thead .sorting {
                            padding-left: 1.2rem !important;
                            padding-right: 0.9rem !important;
                        }
                        .dataTable > thead > tr > th.no_sort[class*="sort"]:before,
                        .dataTable > thead > tr > th.no_sort[class*="sort"]:after {
                            content: "" !important;
                        }
                        </style>
                          <button style="margin-bottom: 10px;" data-target='#termLoanEntryModal' data-target=".bd-example-modal-xl" data-toggle='modal' class='btn btn-success btn-sm open-term-loan-modal'><i class='bi bi-clipboard-plus'></i> Add Term Loan Entries</button>
                            <table class="table table-hover" id="demandLoanTable_term" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Party Name</th>
                                    <!-- <th>Sanction No</th> -->
                                    <th style="width:50px">DL No</th>
                                    <th>Type</th>
                                    <th>Sanction date</th>
                                    <th>First Ins. date</th>
                                    <th>Last Ins. date</th>

                                    <th>Ins. freq.</th>
                                    <th>Grace Period</th> 
                                    <th>Loan Class</th> 
                                    
                                    <th>Ins. amount</th>
                                    <th>Reschedule amount</th>  
                                    <th>Total Recovery</th>
                                    <th>Present Outstanding</th>                                
                               
                                    <th>Loan Doc</th>
                                    <th style="width:150px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query("SELECT c.company_name, tl.* 
                                                        FROM term_loan as tl 
                                                        INNER JOIN companies as c ON tl.`company_id`=c.id;");
                                while ($row = $result->fetch_assoc()) {
                                     $sanction_no = htmlspecialchars($row['sanction_no'], ENT_QUOTES, 'UTF-8');
                                     $modalId = "updateTermLoanModal_".$row['id'];
                                    //var_dump($sanction_no); exit();
                                    echo "<tr>
                                            <td>{$row['id']}</td>
                                            <td><a class='view-loan-term-details' href='#' data-target='#termLoanModal' data-toggle='modal' class='open-term-modal' data-id='".$row['id']."' data-name='".$row['company_name']."'>{$row['company_name']}</a></td>
                                           
                                            <td style='width:50px'>{$row['dl_nos']}</td>
                                           <td>{$row['lc_type']}</td>
                                            <td>{$row['sanction_date']}</td>
                                            <td>{$row['first_installment_date']}</td>
                                            <td>{$row['last_installment_date']}</td>

                                            <td>{$row['installment_frequency']}</td>
                                            <td>{$row['grace_period']}</td>
                                            <td>{$row['classification']}</td>

                                            <td>{$row['installment_amount']}</td>
                                            <td>{$row['reschedule_amount']}</td>
                                            <td>{$row['total_recovery']}</td>
                                            <td>{$row['sub_total']}</td>
                                            
                                            ";

                            $filePaths = explode(",", $row['loan_documents']); // Split file paths

                            echo "<td>";
                            if (!empty($row['loan_documents'])) {
                                foreach ($filePaths as $index => $filePath) {
                                    $fileName = basename($filePath);
                                    $shortName = (strlen($fileName) > 15) ? substr($fileName, 14, 20) . "..." : $fileName;
                                    echo "<a href='$filePath' target='_blank' title='$fileName'>$shortName</a><br>"; // Display multiple links
                                }
                            } else {
                                echo "No Doc";
                            }
                            echo "</td>";
                           
                            echo "
                                            <td>
                                            <a href='#' data-target='#termLoanTransactionModal' data-toggle='modal' class='btn btn-warning btn-sm open-term-modal' data-id='".$row['id']."' data-name='".$row['company_name']."'><i class='bi bi-clipboard-plus'></i></a>
                                            <a href='#' class='btn btn-success btn-sm view-term-loan-details' data-id='".$row['id']."' data-name='".$row['company_name']."'><i class='bi bi-eye'></i></a>
                                            <a class='btn btn-info btn-sm' data-bs-toggle='modal' data-bs-target='#$modalId'><i class='bi bi-database-down'></i></a>";
                                            
                                            
                                            if($_SESSION['role'] == 'Super'){
                                                    echo "  <a href='#' class='btn btn-danger btn-sm deleteBtn' data-id='".$row['id']."'><i class='bi bi-trash'></i></a>";
                                                }     
                                            echo "</td>
                                                </tr>";

// UPDATE TERM LOAN
echo "
<div class='modal fade' id='$modalId' tabindex='-1' aria-labelledby='updateModalLabel' aria-hidden='true'>
    <div class='modal-dialog modal-xl'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title'>Update Term Loan for " . htmlspecialchars($row['company_name']) . "</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>

           <div class='modal-body'>
               
            <form method='POST' action='update_term_loan.php' enctype='multipart/form-data'>
                <input type='hidden' name='direct_from_term_loan' value='1'>
                <input type='hidden' name='term_loan_id' id='edit_id' value='" . $row['id'] . "'>
                <input type='hidden' name='action' value='update'>

                    <div class='row'>
                        <div class='col-md-6'>
                            <label class='form-label'>Company</label>
                            
			    <select class='form-control' id='update_company_id_" . $row['id'] . "' name='company_id'>
                                <option value=''>-- Select Option --</option>";

                                // Fetch company options
                                $companies = $conn->query("SELECT * FROM companies");
                                while ($company = $companies->fetch_assoc()) {
                                $selected = $company['id'] == $row['company_id'] ? "selected" : "";
                                echo "<option value='" . $company['id'] . "' $selected>" . htmlspecialchars($company['company_name']) . "</option>";
                                }

                                echo "
                            </select>

                        </div>

                        <div class='col-md-6'>
                            <label class='form-label'>Sanction No</label>
                            <input type='text' class='form-control form-control-sm' name='sanction_no' id='edit_sanction_no_{$row['id']}' value='" . htmlspecialchars($row['sanction_no']) . "'>
                        </div>
                    </div>

                    <div class='row mt-2'>
                        <div class='col-md-6'>
                            <label class='form-label'>Demand Loan No</label>
                            <input type='text' class='form-control form-control-sm' name='dl_nos' id='edit_sanction_no_{$row['id']}' value='" . htmlspecialchars($row['dl_nos']) . "'>
                        </div>
                        <div class='col-md-6'>
                            <label class='form-label'>Sanction Date</label>
                            <input type='date' class='form-control form-control-sm' name='sanction_date' id='edit_sanction_date_{$row['id']}' value='" . $row['sanction_date'] . "'>
                        </div>

                        <div class='col-md-6'>
                            <label class='form-label'>Reschedule Date</label>
                            <input type='date' class='form-control form-control-sm' name='reschedule_date' id='edit_reschedule_date_{$row['id']}' value='" . $row['reschedule_date'] . "'>
                        </div>
                    </div>

                    <div class='row mt-2'>
                        <div class='col-md-6'>
                            <label class='form-label'>Reschedule Amount</label>
                            <input type='number' step='0.01' class='form-control form-control-sm' name='reschedule_amount' id='edit_reschedule_amount_{$row['id']}' value='" . $row['reschedule_amount'] . "'>
                        </div>

                        <div class='col-md-6'>
                           <label class='form-label'>Installment Frequency</label> 
			   <select class='form-control' id='update_installment_frequency_{$row['id']}' name='installment_frequency'>
                                <option value=''>--Select--</option>
                                <option value='1' " . ($row['installment_frequency'] == '1' ? 'selected' : '') . ">Monthly</option>
                                <option value='3' " . ($row['installment_frequency'] == '3' ? 'selected' : '') . ">Quarterly</option>
                            </select>
                        </div>
                    </div>

                    <div class='row mt-2'>
                        <div class='col-md-6'>
                            <label class='form-label'>Installment Amount</label>
                            <input type='number' step='0.01' class='form-control form-control-sm' name='installment_amount' id='edit_installment_amount_{$row['id']}' value='{$row['installment_amount']}'>
                        </div>

                        <div class='col-md-6'>
                            <label class='form-label'>First Installment Date</label>
                            <input type='date' class='form-control form-control-sm' name='first_installment_date' id='edit_first_installment_date_{$row['id']}' value='" . $row['first_installment_date'] . "'>
                        </div>
                    </div>

                    <div class='row mt-2'>
                        <div class='col-md-6'>
                            <label class='form-label'>Grace Period (Months)</label>
                            <input type='number' class='form-control form-control-sm' name='grace_period' id='edit_grace_period_{$row['id']}' value='{$row['grace_period']}'>
                        </div>

                        <div class='col-md-6'>
                            <label class='form-label'>Last Installment Date</label>
                            <input type='date' class='form-control form-control-sm' name='last_installment_date' id='edit_last_installment_date_{$row['id']}' value='" . $row['last_installment_date'] . "'>
                        </div>
                    </div>

                    <div class='mt-2'>
                        <label class='form-label'>Special Conditions</label>
                        <input type='text' class='form-control form-control-sm' name='special_condition' id='edit_special_condition_{$row['id']}' value='{$row['special_condition']}'>
                    </div>

                    <div class='row mt-2'>
                        <div class='col-md-6'>
                            <label class='form-label'>Present Outstanding</label>
                            <input type='number' step='0.01' class='form-control form-control-sm' name='present_outstanding' id='edit_present_outstanding_{$row['id']}' value='{$row['present_outstanding']}'>
                        </div>

                        <div class='col-md-6'>
                            <label class='form-label'>Total Recovery</label>
                            <input type='number' step='0.01' class='form-control form-control-sm' name='total_recovery' id='edit_total_recovery_{$row['id']}' value='{$row['total_recovery']}'>
                        </div>
                    </div>

                    <div class='row mt-2'>
                        <div class='col-md-6'>
                            <label class='form-label'>Grace Period Details</label>
                            <input type='text' class='form-control form-control-sm' name='grace_period_details'  id='edit_grace_period_details_{$row['id']}' value='{$row['grace_period_details']}'>
                        </div>

                        <div class='col-md-6'>
                            <label class='form-label'>Sub Total</label>
                            <input type='number' step='0.01' class='form-control form-control-sm' name='sub_total' id='edit_sub_total_{$row['id']}' value='{$row['sub_total']}'>
                        </div>
                    </div>

                    <div class='row mt-2'>
                        <div class='col-md-6'>
                            <label class='form-label'>Passing Authority</label>
                            <input type='text' class='form-control form-control-sm' name='passing_authority' id='edit_passing_authority_{$row['id']}' value='{$row['passing_authority']}'>
                        </div>

                        <div class='col-md-6'>
                            <label class='form-label'>Branch Code</label>
                            <input type='number' value='4006' class='form-control form-control-sm' name='branch_code_{$row['id']}' id='edit_branch_code'>
                        </div>
                    </div>

                    <div class='row mt-2'>
                        <div class='col-md-6'>
                            <label class='form-label'>Interest Rate</label>
                            <input type='text' class='form-control form-control-sm' name='interest_rate' id='edit_interest_rate_{$row['id']}' value='{$row['interest_rate']}'>
                        </div>

                        <div class='col-md-6'>
                            <label class='form-label'>Remarks</label>
                            <input type='number' class='form-control form-control-sm' name='remarks' id='edit_remarks_{$row['id']}' value='{$row['remarks']}'>
                        </div>
                    </div>

                    <div class='row mt-2'>
                        <div class='col-md-12'>
                            <label class='form-label'>Latest State</label>
                            <input type='text' class='form-control form-control-sm' name='latest_state' id='edit_latest_state_{$row['id']}' value='{$row['latest_state']}'>
                        </div>
                    </div>

                    <div class='mt-2'>
                        <label class='form-label'>Classification</label>
			<select class='form-control' id='update_classification_{$row['id']}' name='classification'>
                                <option value=''>--Select--</option>
                                <option value='BL' " . ($row['classification'] == 'BL' ? 'selected' : '') . ">BL (Bad/Loss Loan)</option>
                                <option value='DF' " . ($row['classification'] == 'DF' ? 'selected' : '') . ">DF (Doubtful Loan)</option>
                                <option value='SS' " . ($row['classification'] == 'SS' ? 'selected' : '') . ">SS (Substandard Loan)</option>
                                <option value='SMA' " . ($row['classification'] == 'SMA' ? 'selected' : '') . ">SMA (Special Mention Account)</option>
                                <option value='STD' " . ($row['classification'] == 'STD' ? 'selected' : '') . ">STD (Standard Loan)</option>
                                <option value='WR' " . ($row['classification'] == 'WR' ? 'selected' : '') . ">WR (Write-off)</option>
                            </select>
                    </div>

                    <div class='row mt-2'>
                        <div class='col-md-6'>
                            <label class='form-label'>Register Index No</label>
                            <input type='text' class='form-control form-control-sm' name='register_index_no' id='edit_register_index_no_{$row['id']}' value='{$row['register_index_no']}'>
                        </div>

                        <div class='col-md-6'>
                            <label class='form-label'>Reschedule No</label>
                            <input type='text' class='form-control form-control-sm' name='reschedule_no' id='edit_reschedule_no_{$row['id']}' value='{$row['reschedule_no']}'>
                        </div>
                    </div>

                    <div class='mt-2'>
                        <label class='form-label'>LC Type</label>
			                <select class='form-control' id='update_type_" . $row['id'] . "' name='lc_type'>
                                <option value='CASH DEFERRED' " . ($row['lc_type'] == 'CASH DEFERRED' ? 'selected' : '') . ">CASH DEFERRED</option>
                                <option value='CASH SIGHT' " . ($row['lc_type'] == 'CASH SIGHT' ? 'selected' : '') . ">CASH SIGHT</option>
                                <option value='BACK TO BACK' " . ($row['lc_type'] == 'BACK TO BACK' || $row['lc_type'] == 'BTB'? 'selected' : '') . ">BACK TO BACK (BTB)</option>
                                <option value='Export Development Fund (EDF)' " . ($row['lc_type'] == 'Export Development Fund (EDF)' || $row['lc_type'] == 'EDF' ? 'selected' : '') . ">Export Development Fund (EDF)</option>
                                <option value='UPAS LC' " . ($row['lc_type'] == 'UPAS LC' ? 'selected' : '') . ">UPAS LC</option>
                                <option value='Other (cc)' " . ($row['lc_type'] == 'Other (cc)' ? 'selected' : '') . ">Other (cc)</option>
                            </select>
                    </div>

                    <span id='current_documents_term'></span>

                    <div class='mt-2'>
                        <label class='form-label'>Upload Loan Documents (PDF)</label>
                        <input type='file' class='form-control form-control-sm' name='loan_documents[]' multiple>
                    </div> 
                    
                    <!-- Hidden input to retain existing file paths -->
                    <input type='hidden' name='existing_loan_documents' value='{$row['loan_documents']}'>";

                        $filePaths = explode(",", $row['loan_documents']); 
                        if (!empty($row['loan_documents'])) {
                            foreach ($filePaths as $index => $filePath) {
                                $fileName = basename($filePath);
                                $shortName = (strlen($fileName) > 15) ? substr($fileName, 14, 20) . "..." : $fileName;
                                echo "<a href='$filePath' target='_blank' title='$fileName'>$shortName</a><br>"; 
                            }
                        } else {
                            echo "No Document";
                        }
                    
                   echo " 

                    <div class='text-center mt-3'>
                        <button type='submit' class='btn btn-sm btn-primary'>Submit</button>
                    </div>

                    </form>
            </div>
        </div>
    </div>
</div>";
// EOF





                                }
                                ?>
                            </tbody>



                            <tfoot>
                                <tr>
                                    <th colspan="11" style="text-align:right">Total:</th>
                                    <th></th> <!-- Reschedule Amount -->
                                    <th></th> <!-- Total Recovery -->
                                    <th></th> <!-- Present Outstanding -->
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <!-- <td>{$row['present_outstanding']}</td> -->



           


                    </div>
                </div>
                </div>
             </div>
    <script>
        function toggleParentCompany() {
            let companyType = document.getElementById("company_type").value;
            console.log(companyType);
            document.getElementById("parentCompany").style.display = (companyType === "Group") ? "block" : "none";
        }
    </script>
                
    <script>
        $(document).ready(function() {
            $('#companyTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'excel', 'pdf'
                ]
            });
        });
    </script>  


    <!-- Modal -->
<div class="modal fade" id="termLoanTransactionModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Debit/Credit Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="transactionForm">
                    <input type="hidden" name="term_loan_id" id="term_loan_id">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Amount</th>
                                <th>Type</th>
                                
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="transactionBody">
                            <tr>
                                <td><input type="date" class="form-control form-control-sm" name="date[]" required></td>
                                <td><input type="text" class="form-control form-control-sm" name="details[]" required></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="amount[]" required></td>
                                <td>
                                    <select class="form-control form-control-sm" name="type[]">
                                        <option value="debit">Debit</option>
                                        <option value="credit">Credit</option>
                                    </select>
                                </td>
                                
                                <td><button type="button" class="btn btn-sm btn-danger removeEntry">X</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success btn-sm" id="addEntry">+ Add More</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const transactionBody = document.getElementById("transactionBody");

    // Add new entry row
    document.getElementById("addEntry").addEventListener("click", function() {
        let newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td><input type="date" class="form-control form-control-sm" name="date[]" required></td>
            <td><input type="text" class="form-control form-control-sm" name="details[]" required></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="amount[]" required></td>
            <td>
                <select class="form-control form-control-sm" name="type[]">
                    <option value="debit">Debit</option>
                    <option value="credit">Credit</option>
                </select>
            </td>
            
            <td><button type="button" class="btn btn-sm btn-danger removeEntry">X</button></td>
        `;
        transactionBody.appendChild(newRow);
    });

    // Remove an entry row
    transactionBody.addEventListener("click", function(event) {
        if (event.target.classList.contains("removeEntry")) {
            event.target.closest("tr").remove();
        }
    });

    // AJAX form submission
    document.getElementById("transactionForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent default form submission

        let formData = new FormData(this);
        fetch("term_loan_entry_add_transaction.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            alert("Entries added successfully!");
            transactionBody.innerHTML = ""; // Clear table after submission
            document.getElementById("termLoanTransactionModal").querySelector(".btn-close").click(); // Close modal
        })
        .catch(error => console.error("Error:", error));
    });
});
</script>


 

<div class="modal fade" id="termLoanEntryModal" tabindex="-1" aria-labelledby="modalLabelTerm" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelTerm">Add Term Loan Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="term_loan_entry.php" method="post">
                    <input type="hidden" name="direct_from_term_loan" value="1">

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <select class="form-select form-select-sm" name="company_id" required>
                                <option value="">-- Select --</option>
                                <?php 
                                    $companies = $conn->query("SELECT * FROM companies");
                                    while ($row = $companies->fetch_assoc()) { 
                                ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['company_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Sanction No</label>
                            <input type="text" class="form-control form-control-sm" name="sanction_no">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Sanction Date</label>
                            <input type="date" class="form-control form-control-sm" name="sanction_date" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Reschedule Date</label>
                            <input type="date" class="form-control form-control-sm" name="reschedule_date">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Reschedule Amount</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="reschedule_amount" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Installment Frequency</label>
                            <div class="d-flex">
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="radio" name="installment_frequency" value="1" required>
                                    <label class="form-check-label">Monthly</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="installment_frequency" value="3" required>
                                    <label class="form-check-label">Quarterly</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Installment Amount</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="installment_amount" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">First Installment Date</label>
                            <input type="date" class="form-control form-control-sm" name="first_installment_date" required>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Grace Period (Months)</label>
                            <input type="number" class="form-control form-control-sm" name="grace_period" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Last Installment Date</label>
                            <input type="date" class="form-control form-control-sm" name="last_installment_date">
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="form-label">Special Conditions</label>
                        <input type="text" class="form-control form-control-sm" name="special_condition">
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Present Outstanding</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="present_outstanding">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Total Recovery</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="total_recovery">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Grace Period Details</label>
                            <input type="text" class="form-control form-control-sm" name="grace_period_details">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Sub Total</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="sub_total">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Passing Authority</label>
                            <input type="text" class="form-control form-control-sm" name="passing_authority">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Branch Code</label>
                            <input type="number" value="4006" class="form-control form-control-sm" name="branch_code">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Interest Rate</label>
                            <input type="text" class="form-control form-control-sm" name="interest_rate">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Remarks</label>
                            <input type="number" class="form-control form-control-sm" name="remarks">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="form-label">Latest State</label>
                            <input type="text" class="form-control form-control-sm" name="latest_state">
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="form-label">Classification</label>
                        <select class="form-select form-select-sm" name="classification">
                            <option value="">-- Select --</option>
                            <option value="BL">BL (Bad/Loss Loan)</option>
                            <option value="DF">DF (Doubtful Loan)</option>
                            <option value="SS">SS (Substandard Loan)</option>
                            <option value="SMA">SMA (Special Mention Account)</option>
                            <option value="STD">STD (Standard Loan)</option>
                            <option value="WR">WR (Write-off)</option>
                        </select>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Register Index No</label>
                            <input type="text" class="form-control form-control-sm" name="register_index_no" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Reschedule No</label>
                            <input type="text" class="form-control form-control-sm" name="reschedule_no" required>
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="form-label">LC Type</label>
                        <select class="form-select form-select-sm" name="lc_type" required>
                            <option value="CASH DEFERRED">CASH DEFERRED</option>
                            <option value="CASH SIGHT">CASH SIGHT</option>
                            <option value="BACK TO BACK">BACK TO BACK (BTB)</option>
                            <option value="EDF">Export Development Fund (EDF)</option>
                            <option value="UPAS LC">Usance Payable at Sight Letter of Credit (UPAS)</option>
                            <option value="Other (cc)">Other (cc)</option>
                        </select>
                    </div>

                    <div class="mt-2">
                        <label class="form-label">Upload Loan Documents (PDF)</label>
                        <input type="file" class="form-control form-control-sm" id="loan_documents_term" name="loan_documents[]" multiple>
                    </div>

                    

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Edit Term Loan Modal -->
 <!--
<div class="modal fade" id="editTermLoanModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Term Loan Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               
            <form action="update_term_loan.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="direct_from_term_loan" value="1">
                <input type="hidden" name="term_loan_id" id="edit_id">
                <input type="hidden" name="action" value="update">

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <select class="form-select form-select-sm" name="company_id" id="edit_company_id" required>
                                <option value="">-- Select --</option>
                                <?php 
                                    $companies = $conn->query("SELECT * FROM companies");
                                    while ($row = $companies->fetch_assoc()) { 
                                ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['company_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Sanction No</label>
                            <input type="text" class="form-control form-control-sm" name="sanction_no" id="edit_sanction_no">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Sanction Date</label>
                            <input type="date" class="form-control form-control-sm" name="sanction_date" id="edit_sanction_date">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Reschedule Date</label>
                            <input type="date" class="form-control form-control-sm" name="reschedule_date" id="edit_reschedule_date">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Reschedule Amount</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="reschedule_amount" id="edit_reschedule_amount" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Installment Frequency</label>
                            <div class="d-flex">
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="radio" name="installment_frequency" value="1" >
                                    <label class="form-check-label">Monthly</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="installment_frequency" value="3" >
                                    <label class="form-check-label">Quarterly</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Installment Amount</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="installment_amount" id="edit_installment_amount">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">First Installment Date</label>
                            <input type="date" class="form-control form-control-sm" name="first_installment_date" id="edit_first_installment_date">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Grace Period (Months)</label>
                            <input type="number" class="form-control form-control-sm" name="grace_period" id="edit_grace_period">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Last Installment Date</label>
                            <input type="date" class="form-control form-control-sm" name="last_installment_date" id="edit_last_installment_date">
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="form-label">Special Conditions</label>
                        <input type="text" class="form-control form-control-sm" name="special_condition" id="edit_special_condition">
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Present Outstanding</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="present_outstanding" id="edit_present_outstanding">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Total Recovery</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="total_recovery" id="edit_total_recovery">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Grace Period Details</label>
                            <input type="text" class="form-control form-control-sm" name="grace_period_details"  id="edit_grace_period_details">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Sub Total</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="sub_total" id="edit_sub_total">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Passing Authority</label>
                            <input type="text" class="form-control form-control-sm" name="passing_authority" id="edit_passing_authority">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Branch Code</label>
                            <input type="number" value="4006" class="form-control form-control-sm" name="branch_code" id="edit_branch_code">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Interest Rate</label>
                            <input type="text" class="form-control form-control-sm" name="interest_rate" id="edit_interest_rate">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Remarks</label>
                            <input type="number" class="form-control form-control-sm" name="remarks" id="edit_remarks">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="form-label">Latest State</label>
                            <input type="text" class="form-control form-control-sm" name="latest_state" id="edit_latest_state">
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="form-label">Classification</label>
                        <select class="form-select form-select-sm" name="classification" id="edit_classification">
                            <option value="">-- Select --</option>
                            <option value="BL">BL (Bad/Loss Loan)</option>
                            <option value="DF">DF (Doubtful Loan)</option>
                            <option value="SS">SS (Substandard Loan)</option>
                            <option value="SMA">SMA (Special Mention Account)</option>
                            <option value="STD">STD (Standard Loan)</option>
                            <option value="WR">WR (Write-off)</option>
                        </select>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Register Index No</label>
                            <input type="text" class="form-control form-control-sm" name="register_index_no" id="edit_register_index_no">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Reschedule No</label>
                            <input type="text" class="form-control form-control-sm" name="reschedule_no" id="edit_reschedule_no">
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="form-label">LC Type</label>
                        <select class="form-select form-select-sm" name="lc_type" id="edit_lc_type">
                            <option value="#">-- Select Option --</option>
                            <option value="CASH DEFERRED">CASH DEFERRED</option>
                            <option value="CASH SIGHT">CASH SIGHT</option>
                            <option value="BACK TO BACK">BACK TO BACK (BTB)</option>
                            <option value="EDF">Export Development Fund (EDF)</option>
                            <option value="UPAS LC">Usance Payable at Sight Letter of Credit (UPAS)</option>
                            <option value="Other (cc)">Other (cc)</option>
                        </select>
                    </div>

                    <span id="current_documents_term"></span>

                    <div class="mt-2">
                        <label class="form-label">Upload Loan Documents (PDF)</label>
                        <input type="file" class="form-control form-control-sm" name="loan_documents[]" multiple>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                    </div>

                    </form>



            </div>
        </div>
    </div>
</div>

-->

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteTermLoanModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this term loan?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>


<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModalforTermLoan" tabindex="-1" aria-labelledby="detailsTermModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsTermModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Details</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Transaction Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="transactionDetailsBodyTerm">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal for Demand Loan Details -->
<div class="modal fade" id="termloanModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Term Loan Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">

                <table class="table table-striped table-bordered">
                        <tr><td><h4 id="companyName"></h4></td> </tr>
                        <tr><td><p><strong>Company Type:</strong> <span id="companyType"></span></p></td> </tr>
                        <tr><td><p><strong>Address:</strong> <span id="companyAddress"></span></p></td> </tr>
                        <tr><td><p><strong>Contact:</strong> <span id="companyContact"></span></p></td> </tr>
                </table>
        
                <h5 class="mt-3">Term Loan Details</h5>
                <style>
                        th{ padding: 1px;  font-size: 15px;}
                        td{ padding: 1px;  font-size: 15px;}
                        tbody{font-size: 14px;}
                    </style>
                    <style>
                        table.dataTable {
                        width: 100% !important;
                        }
                        table.dataTable td {
                        font-size: .8em !important;
                        }
                        table.dataTable tr.dtrg-level-0 td {
                        font-size: 1.5em !important;
                        }
                        table.dataTable td {
                            padding: 2px !important;
                        }
                        .dataTable thead .sorting_asc,
                        .dataTable thead .sorting_desc,
                        .dataTable thead .sorting {
                            padding-left: 1.2rem !important;
                            padding-right: 0.9rem !important;
                        }
                        .dataTable > thead > tr > th.no_sort[class*="sort"]:before,
                        .dataTable > thead > tr > th.no_sort[class*="sort"]:after {
                            content: "" !important;
                        }
                        </style>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Demand Loan No</th>
                            <th>Sanction Date</th>
                            <th>Reschedule Amount</th>
                            <th>Installment Amount</th>
                            <th>Installment Frequency</th>
                            <th>First Installment Date</th>
                            <th>Grace Period</th>
                            <th>Last Installment Date</th>
                            <!-- <th>Special Condition</th> -->
                           
                            
                          
                        </tr>
                    </thead>
                    <tbody id="termLoanDetails"></tbody>
                </table>

                <h5 class="mt-3">Transaction Details</h5>
                <table class="table table-striped table-bordered" id="dlTransactionTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Particular</th>
                            <th>Amount <br> Debit</th>
                            <th>Amount <br> Credit</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody id="transactionDetails"></tbody>
                </table>

                <h5 class="mt-3">Final Balance: <span id="finalBalance" class="fw-bold"></span></h5>
                <button class="btn btn-danger btn-sm" id="printTermPdf">Print as PDF</button>
                <button class="btn btn-success btn-sm" id="downloadTermExcel">Print as Excel</button>
            </div>
                
        </div>
    </div>
</div>


 