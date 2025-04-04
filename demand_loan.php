<div class="row">
                <div class="col-md-12">
                <div class="row">
                    <!-- <div class="col-md-5">



                    
      
                    </div> -->
                    <div class="col-md-12">

                    <form method="post" action="">
                        <button type="submit" class="btn btn-info btn-sm" name="update_all_entries"> <i class='bi bi-clipboard-plus'></i> Update demand Loan Data</button>
                    </form>

<?php

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
            dle.sub_total = loan_summary.final_balance
    ";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Records updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating records: " . $conn->error . "');</script>";
    }
}

?>

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

                        .unselectable{
                            background: grey !important;
                            cursor: not-allowed;
                            pointer-events: none;
                           
                        }
                        
                        </style>
                          <button style="margin-bottom: 10px;" data-target='#demandLoanEntryModal' data-target=".bd-example-modal-xl" data-toggle='modal' class='btn btn-success btn-sm open-demand-loan-modal'><i class='bi bi-clipboard-plus'></i> Add Demand Loan Entries</button>
                            <table class="table table-hover" id="demandLoanTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Party Name</th>
                                    <th>Type</th>
                                    <th>DL No.</th>
                                    <th>Disbursement Date</th>
                                    <th>Expiry Date</th>
                                    <th>Loan Amount</th>
                                    
                                    <th>Total Recovery</th>
                                    <th>Present Outstanding</th>
                                    <th>Loan Doc</th>
                                    
                                    <th style="width:150px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query("SELECT c.company_name, dl.* 
                                                        FROM demand_loan_entry as dl 
                                                        INNER JOIN companies as c ON dl.`company_id`=c.id;");
                while ($row = $result->fetch_assoc()) {
                        if($row['is_demand_loan_active']==1){ 
                            $modalId = "updateDemandLoanModal_".$row['id'];
                            echo "<tr>
                            <td>{$row['id']}</td>
                            <td><a class='view-loan-details' href='#' data-target='#loanModal' data-toggle='modal' class='open-modal' data-id='".$row['id']."' data-name='".$row['company_name']."'>{$row['company_name']}</a></td>
                            <td>{$row['type']}</td>
                            <td>{$row['dl_no']}</td>
                            <td>{$row['disburse_date']}</td>
                            <td>{$row['expiry_date']}</td>
                            <td>{$row['loan_creation_amount']}</td>
                            <td>{$row['total_recovery']}</td>
                            <td>{$row['sub_total']}</td>";

                            $filePaths = explode(",", $row['loan_documents']); // Split file paths

                            echo "<td>";
                            if (!empty($row['loan_documents'])) {
                                foreach ($filePaths as $index => $filePath) {
                                    $fileName = basename($filePath);
                                    $shortName = (strlen($fileName) > 15) ? substr($fileName, 14, 20) . "..." : $fileName;
                                    echo "<a href='$filePath' target='_blank' title='$fileName'>$shortName</a><br>"; // Display multiple links
                                }
                            } else {
                                echo "No Document";
                            }
                            echo "</td>";
                           
                            echo "
                            <td>

                            <a href='#' data-target='#transactionModal' data-toggle='modal' class='btn btn-warning btn-sm open-modal' data-id='".$row['id']."' data-name='".$row['company_name']."'><i class='bi bi-clipboard-plus'></i></a>
                            <a href='#' class='btn btn-success btn-sm view-details' data-id='".$row['id']."' data-name='".$row['company_name']."'><i class='bi bi-eye'></i></a>
                            <a class='btn btn-info btn-sm' data-bs-toggle='modal' data-bs-target='#$modalId'><i class='bi bi-database-down'></i></a>";






                           

                           if($_SESSION['role'] == 'Super'){
                             echo "<a class='btn btn-danger btn-sm' onclick='confirmDelete({$row['id']})'><i class='bi bi-trash'></i></a>";
                           }
                            
                          echo "
                            <a  href='#' data-target='#termLoanEntryModal' data-toggle='modal' class='btn btn-primary btn-sm open-termloan-modal' data-id='".$row['company_id']."' data-name='".$row['company_name']."'><i class='bi bi-send-check'></i></a>
                            
                            </td>
                        
                        </tr>";

                    } else {
                 
                            echo "<tr >
                            <td>{$row['id']}</td>
                            <td><a class='view-loan-details' href='#' data-target='#loanModal' data-toggle='modal' class='open-modal' data-id='".$row['id']."' data-name='".$row['company_name']."'>{$row['company_name']}</a></td>
                            <td>{$row['type']}</td>
                            <td>{$row['dl_no']}</td>
                            <td>{$row['disburse_date']}</td>
                            <td>{$row['expiry_date']}</td>
                            <td>{$row['loan_creation_amount']}</td>
                            <td>{$row['sub_total']}</td>";

                            $filePaths = explode(",", $row['loan_documents']); // Split file paths

                            echo "<td>";
                            if (!empty($row['loan_documents'])) {
                                foreach ($filePaths as $index => $filePath) {
                                    $fileName = basename($filePath);
                                    $shortName = (strlen($fileName) > 15) ? substr($fileName, 0, 12) . "..." : $fileName;
                                    echo "<a href='$filePath' target='_blank' title='$fileName'>$shortName</a><br>"; // Display multiple links
                                }
                            } else {
                                echo "No Document";
                            }
                            echo "</td>";
                           
                            echo "
                            <td> <p style='background-color: red; color: white; font-weight: bold; padding: 3px; font-size: 10px;'>This loan is converted to term loan </p> </td>

                        </tr>";
                    }

                    // UPDATE MODAL

// Update Demand Loan Modal
echo "
<div class='modal fade' id='$modalId' tabindex='-1' aria-labelledby='updateModalLabel' aria-hidden='true'>
    <div class='modal-dialog modal-xl'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title'>Update Demand Loan for " . htmlspecialchars($row['company_name']) . "</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <form method='POST' action='demand_loan_process.php' enctype='multipart/form-data'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>

                    <div class='row'>
                        <div class='col-md-6'>
                            <label for='update_company_id_" . $row['id'] . "' class='form-label'>Select Party</label>
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
                            <label for='update_type_" . $row['id'] . "' class='form-label'>LC Type</label>
                            <select class='form-control' id='update_type_" . $row['id'] . "' name='type'>
                                <option value='CASH DEFERRED' " . ($row['type'] == 'CASH DEFERRED' ? 'selected' : '') . ">CASH DEFERRED</option>
                                <option value='CASH SIGHT' " . ($row['type'] == 'CASH SIGHT' ? 'selected' : '') . ">CASH SIGHT</option>
                                <option value='BACK TO BACK' " . ($row['type'] == 'BACK TO BACK' ? 'selected' : '') . ">BACK TO BACK (BTB)</option>
                                <option value='Export Development Fund (EDF)' " . ($row['type'] == 'Export Development Fund (EDF)' ? 'selected' : '') . ">Export Development Fund (EDF)</option>
                                <option value='UPAS LC' " . ($row['type'] == 'UPAS LC' ? 'selected' : '') . ">UPAS LC</option>
                                <option value='Other (cc)' " . ($row['type'] == 'Other (cc)' ? 'selected' : '') . ">Other (cc)</option>
                            </select>
                        </div>
                    </div>

                    <div class='row mt-2'>
                        <div class='col-md-6'>
                            <label for='update_dl_no_" . $row['id'] . "' class='form-label'>DL No</label>
                            <input type='text' class='form-control' id='update_dl_no_" . $row['id'] . "' name='dl_no' value='" . htmlspecialchars($row['dl_no']) . "' required>
                        </div>

                        <div class='col-md-6'>
                            <label for='update_disburse_date_" . $row['id'] . "' class='form-label'>Disburse Date</label>
                            <input type='date' class='form-control' id='update_disburse_date_" . $row['id'] . "' name='disburse_date' value='" . $row['disburse_date'] . "' required>
                        </div>
                    </div>

                    <div class='row mt-2'>
                            <div class='col-md-6'>
                                <label for='update_expiry_date_{$row['id']}' class='form-label'>Expiry Date</label>
                                <input type='date' class='form-control' id='update_expiry_date_{$row['id']}' name='expiry_date' value='{$row['expiry_date']}' required>
                            </div>
                            <div class='col-md-6'>
                                <label for='update_loan_creation_amount_{$row['id']}' class='form-label'>Loan Creation Amount</label>
                                <input type='text' class='form-control' id='update_loan_creation_amount_{$row['id']}' name='loan_creation_amount' value='{$row['loan_creation_amount']}'>
                            </div>
                    </div>
                    <div class='row mt-2'>
                            <div class='col-md-6'>
                                <label for='update_present_outstanding_{$row['id']}' class='form-label'>Total Present Outstanding</label>
                                <input type='text' class='form-control' id='update_present_outstanding_{$row['id']}' name='present_outstanding' value='{$row['present_outstanding']}'>
                            </div>
                            <div class='col-md-6'>
                                <label for='update_sub_total_{$row['id']}' class='form-label'>Sub Total</label>
                                <input type='text' class='form-control' id='update_sub_total_{$row['id']}' name='sub_total' value='{$row['sub_total']}'>
                            </div>
                    </div>

                    <div class='row mt-2'>
                            <div class='col-md-6'>
                                <label for='update_exchange_rate_of_dl_{$row['id']}' class='form-label'>Exchange Rate</label>
                                <input type='text' class='form-control' id='update_exchange_rate_of_dl_{$row['id']}' name='exchange_rate_of_dl' value='{$row['exchange_rate_of_dl']}'>
                            </div>
                            <div class='col-md-6'>
                                <label for='update_reason_for_dl_{$row['id']}' class='form-label'>Reason for Demand Loan</label>
                                <input type='text' class='form-control' id='update_reason_for_dl_{$row['id']}' name='reason_for_dl' value='{$row['reason_for_dl']}'>
                            </div>
                        </div>




  <div class='row mt-2'>
                    <div class='col-md-6'>
                        <label for='update_moad_{$row['id']}' class='form-label'>MOAD Nos</label>
                        <input type='text' class='form-control' id='update_moad_{$row['id']}' name='moad' value='{$row['moad']}'>
                    </div>

                    <div class='col-md-6'>

                            <label for='classification_{$row['id']}' class='form-label'>Classification Type</label>
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
                        </div>

                        <div class='row mt-2'>
                        <div class='col-md-6'>
                            <label for='lc_nos_{$row['id']}' class='form-label'>L/C Numbers</label>
                            <input type='text' class='form-control' id='update_lc_nos_{$row['id']}' name='lc_nos' value='{$row['lc_nos']}'>
                        </div>
                        <div class='col-md-6'>
                            <label for='lc_amount_usd_{$row['id']}' class='form-label'>LC USD Amount</label>
                            <input type='text' class='form-control' id='update_lc_amount_usd_{$row['id']}' name='lc_amount_usd' value='{$row['lc_amount_usd']}'>
                        </div>
                    </div>

                    <div class='row mt-2'>
                        <div class='col-md-6'>
                            <label for='update_rf_{$row['id']}' class='form-label'>R F</label>
                            <input type='text' class='form-control' id='update_rf_{$row['id']}' name='rf' value='{$row['rf']}'>
                        </div>
                        <div class='col-md-6'>
                            <label for='latest_state_{$row['id']}' class='form-label'>Latest State as of Date</label>
                            <input type='text' class='form-control' id='update_latest_state_{$row['id']}' name='latest_state' value='{$row['latest_state']}'>
                        </div>
                    </div>
                    <div class='mt-2'>
                        <label for='update_loan_documents_{$row['id']}' class='form-label'>Upload New Loan Documents (PDF)</label>
                        <input type='file' class='form-control' id='update_loan_documents_{$row['id']}' name='loan_documents[]' multiple>        
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
                    
                   echo " <div class='mt-3'>
                        <button type='submit' name='update_demand_loan' class='btn btn-success'>Update Demand Loan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
";

//EOF UPDATE MODAL
                                    
                                  
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" style="text-align:right">Total:</th>
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
                // Function to update the file list in the modal
function updateFileList(files) {
    let fileListContainer = document.getElementById('fileList');
    fileListContainer.innerHTML = ''; // Clear previous content

    if (files.length > 0) {
        files.forEach((file, index) => {
            let listItem = document.createElement('div');
            listItem.innerHTML = `
                <span>${file}</span>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeFile(${index})">Remove</button>
            `;
            fileListContainer.appendChild(listItem);
        });
    } else {
        fileListContainer.innerHTML = '<p>No files uploaded yet.</p>';
    }
}

// Remove file handler (to handle deletions)
function removeFile(index) {
    uploadedFiles.splice(index, 1);
    updateFileList(uploadedFiles);
}

// Example of file upload handler
$("#uploadForm").on('submit', function (e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    $.ajax({
        url: "your_upload_script.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            let files = JSON.parse(response);
            uploadedFiles = files; // Update the global file array
            updateFileList(files); // Update the display
        }
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
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Add Debit/Credit Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transactionForm">
                    <input type="hidden" name="company_id" id="company_id">
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="transactionContainer">
                            <tr class="transaction-entry">
                                <td><input type="date" class="form-control" name="date[]" required></td>
                                <td><input type="text" class="form-control" name="details[]" required></td>
                                <td><input type="number" step="0.01" class="form-control" name="amount[]" required></td>
                                <td>
                                    <select class="form-control" name="type[]">
                                        <option value="debit">Debit</option>
                                        <option value="credit">Credit</option>
                                    </select>
                                </td>
                               
                                <td><button type="button" class="btn btn-danger btn-sm removeEntry">X</button></td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="button" id="addTransaction" class="btn btn-secondary btn-sm">+ Add More</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let maxEntries = 25;
    let transactionContainer = document.getElementById("transactionContainer");
    let addTransactionBtn = document.getElementById("addTransaction");

    addTransactionBtn.addEventListener("click", function () {
        if (transactionContainer.children.length < maxEntries) {
            let entry = document.createElement("tr");
            entry.classList.add("transaction-entry");
            entry.innerHTML = `
                <td><input type="date" class="form-control" name="date[]" required></td>
                <td><input type="text" class="form-control" name="details[]" required></td>
                <td><input type="number" step="0.01" class="form-control" name="amount[]" required></td>
                <td>
                    <select class="form-control" name="type[]">
                        <option value="debit">Debit</option>
                        <option value="credit">Credit</option>
                    </select>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm removeEntry">X</button></td>
            `;
            transactionContainer.appendChild(entry);

            entry.querySelector(".removeEntry").addEventListener("click", function () {
                entry.remove();
            });
        }
    });

    document.getElementById("transactionForm").addEventListener("submit", function (event) {
        event.preventDefault();
        let formData = new FormData(this);

        fetch("demand_loan_entry_add_transaction.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                document.getElementById("transactionForm").reset();
                transactionContainer.innerHTML = `
                    <tr class="transaction-entry">
                        <td><input type="date" class="form-control" name="date[]" required></td>
                        <td><input type="text" class="form-control" name="details[]" required></td>
                        <td><input type="number" step="0.01" class="form-control" name="amount[]" required></td>
                        <td>
                            <select class="form-control" name="type[]">
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                            </select>
                        </td>
                        <td><button type="button" class="btn btn-danger btn-sm removeEntry">X</button></td>
                    </tr>
                `;
            }
        })
        .catch(error => console.error("Error:", error));
    });
});
</script>


    <!-- Term Modal -->
    <div class="modal fade" id="termLoanEntryModal" tabindex="-1" aria-labelledby="modalLabelTerm" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelTerm">Add Term Loan Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="term_loan_entry.php" method="post">
                        <!-- <input type="hidden" name="company_id" id="company_id"> -->
                        <input type="hidden" name="company_id" id="company_id_term">
                        <div class="mb-3">
                            <label for="sanction_no" class="form-label">Sanction No</label>
                            <textarea class="form-control address"  name="sanction_no" ></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="sanction_date" class="form-label">Sanction Date</label>
                            <input type="date" class="form-control" name="sanction_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="reschedule_amount" class="form-label">Reschedule Amount</label>
                            <input type="number" step="0.01" class="form-control" name="reschedule_amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="installment_frequency" class="form-label">Reschedule Frequency</label>
                            <select class="form-control" name="installment_frequency">
                                <option value="1">1</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="installment_amount" class="form-label">Installment Amount</label>
                            <input type="number" step="0.01" class="form-control" name="installment_amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="first_installment_date" class="form-label">First Installment Date</label>
                            <input type="date" class="form-control" name="first_installment_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="grace_period" class="form-label">Grace Period</label>
                            <textarea class="form-control address"  name="grace_period" ></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="last_installment_date" class="form-label">Last Installment Date</label>
                            <input type="date" class="form-control" name="last_installment_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_installment_date" class="form-label">Special Conditions</label>
                            <textarea class="form-control address" name="special_condition" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel"></h5>
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
                    <tbody id="transactionDetailsBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal for Demand Loan Details -->
<div class="modal fade" id="loanModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Demand Loan Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <style>
                        table {
                        width: 100% !important;
                        }
                        table td {
                        font-size: 1.1em !important;
                        }
                        table tr.dtrg-level-0 td {
                        font-size: 1.5em !important;
                        }
                        table td {
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
            <div class="modal-body">
                <table class="table table-striped table-borderless">
                        <tr><td><h4 id="companyName"></h4></td> </tr>
                        <tr><td><p><strong>Company Type:</strong> <span id="companyType"></span></p></td> </tr>
                        <tr><td><p><strong>Address:</strong> <span id="companyAddress"></span></p></td> </tr>
                        <tr><td><p><strong>Contact:</strong> <span id="companyContact"></span></p></td> </tr>
                </table>
         

                <h5 class="mt-3">Demand Loan Details</h5>
                
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Loan No</th>
                            <th>Disbursement Date</th>
                            <th>Expiry Date</th>
                          
                        </tr>
                    </thead>
                    <tbody id="loanDetails"></tbody>
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
                <button class="btn btn-danger" id="printPdf">Print as PDF</button>
                <button class="btn btn-success" id="downloadExcel">Print as Excel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="demandLoanEntryModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Add Demand Loan Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="demand_loan_process.php" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="company_id" class="form-label">Select Party</label>
                            <select class="form-control select2" id="company_id" name="company_id">
                                <option value="">-- Select Option --</option>
                                <?php 
                                    $companies = $conn->query("SELECT * FROM companies WHERE parent_id=0");
                                    while ($row = $companies->fetch_assoc()) { 
                                ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['company_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label">LC Type</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="CASH DEFERRED">CASH DEFERRED</option>
                                <option value="CASH SIGHT">CASH SIGHT</option>
                                <option value="BACK TO BACK">BACK TO BACK (BTB)</option>
                                <option value="Export Development Fund (EDF)">Export Development Fund (EDF)</option>
                                <option value="UPAS LC">Usance Payable at Sight Letter of Credit (UPAS)</option>
                                <option value="Other (cc)">Other (cc)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="dl_no" class="form-label">DL No</label>
                            <input type="text" class="form-control" id="dl_no" name="dl_no" required placeholder="DL:01/24, DL:02/24">
                        </div>
                        <div class="col-md-6">
                            <label for="disburse_date" class="form-label">Disburse Date</label>
                            <input type="date" class="form-control" id="disburse_date" name="disburse_date" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="loan_creation_amount" class="form-label">Loan Creation Amount</label>
                            <input type="text" class="form-control" id="loan_creation_amount" name="loan_creation_amount">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="present_outstanding" class="form-label">Total Present Outstanding</label>
                            <input type="text" class="form-control" id="present_outstanding" name="present_outstanding">
                        </div>
                        <div class="col-md-6">
                            <label for="sub_total" class="form-label">Sub Total</label>
                            <input type="text" class="form-control" id="sub_total" name="sub_total">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="lc_nos" class="form-label">L/C Numbers</label>
                            <input type="text" class="form-control" id="lc_nos" name="lc_nos" placeholder="00118331,00023112">
                        </div>
                        <div class="col-md-6">
                            <label for="lc_amount_usd" class="form-label">LC USD Amount</label>
                            <input type="text" class="form-control" id="lc_amount_usd" name="lc_amount_usd">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="exchange_rate_of_dl" class="form-label">Exchange Rate</label>
                            <input type="text" class="form-control" id="exchange_rate_of_dl" name="exchange_rate_of_dl">
                        </div>
                        <div class="col-md-6">
                            <label for="moad" class="form-label">MOAD Nos</label>
                            <input type="text" class="form-control" id="moad" name="moad" placeholder="Add MODA numbers">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="classification" class="form-label">Classification Type</label>
                            <select class="form-control" id="classification" name="classification">
                                <option value="">--Select--</option>
                                <option value="BL">BL (Bad/Loss Loan)</option>
                                <option value="DF">DF (Doubtful Loan)</option>
                                <option value="SS">SS (Substandard Loan)</option>
                                <option value="SMA">SMA (Special Mention Account)</option>
                                <option value="STD">STD (Standard Loan)</option>
                                <option value="WR">WR (Write-off)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="rf" class="form-label">Physical Register No</label>
                            <input type="text" class="form-control" id="rf" name="rf" placeholder="R: 05/165">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="reason_for_dl" class="form-label">Reason for Demand Loan</label>
                        <input type="text" class="form-control" id="reason_for_dl" name="reason_for_dl">
                    </div>
                    <div class="mt-2">
                        <label for="latest_state" class="form-label">Latest State as of Date</label>
                        <input type="text" class="form-control" id="latest_state" name="latest_state">
                    </div>
                    <div class="mt-2">
                        <label for="loan_documents" class="form-label">Upload Loan Documents (PDF)</label>
                        <input type="file" class="form-control" id="loan_documents" name="loan_documents[]" multiple>
                    </div>
                    <div class="mt-3 text-center">
                        <button type="submit" name="add_demand_loan" class="btn btn-primary">Add Demand Loan Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>






    <!-- Update Demand Loan Modal -->
<div class="modal fade" id="updateDemandLoanModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Demand Loan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="demand_loan_process.php" enctype="multipart/form-data">
                    <input type="hidden" id="update_id" name="id">
                    <div class="row">
                        <div class="col-md-6">
                    
                    <div class="mb-3">
                        <label for="update_company_id" class="form-label">Select Party</label>
                        <select class="form-control" id="update_company_id" name="company_id">
                            <option value="">-- Select Option --</option>
                            <?php
                            $companies = $conn->query("SELECT * FROM companies");
                            while ($row = $companies->fetch_assoc()) { 
                            ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['company_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                        </div>
                        <div class="col-md-6">
                    <div class="mb-3">
                    
                            <label for="update_type" class="form-label">LC Type</label>
                            <select class="form-control" id="update_type" name="type">
                                <option value="CASH DEFERRED">CASH DEFERRED</option>
                                <option value="CASH SIGHT">CASH SIGHT</option>
                                <option value="BACK TO BACK">BACK TO BACK (BTB)</option>
                                <option value="Export Development Fund (EDF)">Export Development Fund (EDF)</option>
                                <option value="UPAS LC">UPAS LC</option>
                                <option value="Other (cc)">Other (cc)</option>
                            </select>
                        
                    </div>
                    </div>
                    </div>

                    <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="update_dl_no" class="form-label">DL No</label>
                        <input type="text" class="form-control" id="update_dl_no" name="dl_no" required>
                    </div>

                    <div class="col-md-6">
                        <label for="update_disburse_date" class="form-label">Disburse Date</label>
                        <input type="date" class="form-control" id="update_disburse_date" name="disburse_date" required>
                    </div>
                    </div>


                    <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="update_expiry_date" class="form-label">Expiry Date</label>
                        <input type="date" class="form-control" id="update_expiry_date" name="expiry_date" required>
                    </div>

                    <div class="col-md-6">
                        <label for="update_loan_creation_amount" class="form-label">Loan Creation Amount</label>
                        <input type="text" class="form-control" id="update_loan_creation_amount" name="loan_creation_amount">
                    </div>
                   
                    </div>

                    <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="update_present_outstanding" class="form-label">Total Present Outstanding</label>
                        <input type="text" class="form-control" id="update_present_outstanding" name="present_outstanding">
                    </div>

                    <div class="col-md-6">
                        <label for="update_sub_total" class="form-label">Sub Total</label>
                        <input type="text" class="form-control" id="update_sub_total" name="sub_total">
                    </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="exchange_rate_of_dl" class="form-label">Exchange Rate</label>
                            <input type="text" class="form-control" id="update_exchange_rate_of_dl" name="exchange_rate_of_dl">
                        </div>
                        <div class="col-md-6">
                                <label for="reason_for_dl" class="form-label">Reason for Demand Loan</label>
                                <input type="text" class="form-control" id="update_reason_for_dl" name="reason_for_dl">
                        </div>
                    </div>

                    <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="update_moad" class="form-label">MOAD Nos</label>
                        <input type="text" class="form-control" id="update_moad" name="moad">
                    </div>

                    <div class="col-md-6">

                            <label for="classification" class="form-label">Classification Type</label>
                            <select class="form-control" id="update_classification" name="classification">
                                <option value="">--Select--</option>
                                <option value="BL">BL (Bad/Loss Loan)</option>
                                <option value="DF">DF (Doubtful Loan)</option>
                                <option value="SS">SS (Substandard Loan)</option>
                                <option value="SMA">SMA (Special Mention Account)</option>
                                <option value="STD">STD (Standard Loan)</option>
                                <option value="WR">WR (Write-off)</option>
                            </select>
                        </div>
                        </div>

                        <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="lc_nos" class="form-label">L/C Numbers</label>
                            <input type="text" class="form-control" id="update_lc_nos" name="lc_nos" placeholder="00118331,00023112">
                        </div>
                        <div class="col-md-6">
                            <label for="lc_amount_usd" class="form-label">LC USD Amount</label>
                            <input type="text" class="form-control" id="update_lc_amount_usd" name="lc_amount_usd">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="update_rf" class="form-label">R F</label>
                            <input type="text" class="form-control" id="update_rf" name="rf">
                        </div>
                        <div class="col-md-6">
                            <label for="latest_state" class="form-label">Latest State as of Date</label>
                            <input type="text" class="form-control" id="update_latest_state" name="latest_state">
                        </div>
                    </div>

                    <div class="mt-2">
                        <label for="update_loan_documents" class="form-label">Upload New Loan Documents (PDF)</label>
                        <input type="file" class="form-control" id="update_loan_documents" name="loan_documents[]" multiple>        
                    </div>

                    <button type="submit" name="update_demand_loan" class="btn btn-success">Update Demand Loan</button>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteDemandLoanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this demand loan?</p>
                <form method="POST" action="demand_loan_process.php">
                    <input type="hidden" id="delete_id" name="id">
                    <button type="submit" name="delete_demand_loan" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
//     if (data.loan_documents) {
//     let pdfLinks = JSON.parse(data.loan_documents).map(file => `
//         <li><a href="uploads/${file}" target="_blank">${file}</a></li>
//     `).join('');

//     $("#loanDocuments").html(`<ul>${pdfLinks}</ul>`);
// }
</script>