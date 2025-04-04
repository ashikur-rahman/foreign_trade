
             <div class="row">
                <div class="col-md-12">
                <div class="row">
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
                    <button style="margin-bottom: 10px;" data-target='#companyEntryModal' data-target=".bd-example-modal-xl" data-toggle='modal' class='btn btn-success btn-sm open-company-entry-modal'><i class='bi bi-clipboard-plus'></i> Add Company</button>
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
                    
                    <table id="companyTable" class="display">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Company Name</th>
                                    <th>Type</th>
                                    <th>Address</th>
                                    <th>Contact Number</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query("SELECT * FROM companies");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['id']}</td>
                                            <td><a class='view-all-loan-details' href='#' data-target='#allLoanModal' data-toggle='modal' class='open-modal' data-id='".$row['id']."' data-name='".$row['company_name']."'>{$row['company_name']}</a></td>
                                            <td>{$row['company_type']}</td>
                                            <td>{$row['address']}</td>
                                            <td>{$row['contact_number']}</td>
                                            <td> ";
                                    echo ($row['company_type']=='Group'? "<button class='btn btn-warning btn-sm view-all-company-loan-details' href='#' data-target='#allCompanyLoanModal' data-toggle='modal' class='open-modal' data-id='".$row['id']."' data-name='".$row['company_name']."'><i class='bi bi-eye'></i></button> ":"" );
                                    echo   "<button class='btn btn-info btn-sm update-company' data-id='{$row['id']}' data-name='{$row['company_name']}' data-type='{$row['company_type']}' data-address='{$row['address']}' data-contact='{$row['contact_number']}' data-bs-toggle='modal' data-bs-target='#updateCompanyModal'><i class='bi bi-arrow-clockwise'></i></button> ";
                                        if($_SESSION['role'] == 'Super'){
                                            echo "  <button class='btn btn-danger btn-sm delete-company' data-id='{$row['id']}' data-bs-toggle='modal' data-bs-target='#deleteCompanyModal'><i class='bi bi-trash'></i></button>";
                                        }     
                                    echo "</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
             </div>





     <!-- Modal -->
 <div class="modal fade" id="companyEntryModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Add Company Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    

                 
                <form method="POST" action="company_process.php">
                        <div class="mb-3">
                            <label for="company_type" class="form-label">Party Type</label>
                            <select class="form-control" id="company_type" name="company_type" onchange="toggleParentCompany()" required>
                                    <option value=""> -- Select Option -- </option>
                                    <option value="Single">Single</option>
                                    <option value="Group">Group</option>
                            </select>
                        </div>
                        <div class="mb-3" id="parentCompany" style="display: none;">
                            <label for="parent_id" class="form-label">Main Company (If it is a Group)</label>
                            <select class="form-control" id="parent_id" name="parent_id">
                                <option value="">-- Select Option --</option>
                                <?php // Fetch companies
                                    $companies = $conn->query("SELECT * FROM companies WHERE parent_id=0");
                                    while ($row = $companies->fetch_assoc()) { 
                                ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['company_name']; ?></option>
                                <?php 
                                    } 
                                ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Party Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" required>
                        </div>
                        
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                        </div>
                        <button type="submit" name="add_company" class="btn btn-primary">Add Party</button>

                </form>




                </div>
            </div>
        </div>
    </div>


<!-- Update Company Modal -->
<div class="modal fade" id="updateCompanyModal" tabindex="-1" aria-labelledby="updateCompanyLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateCompanyLabel">Update Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form  method="POST" action="company_process.php">
                    <input type="hidden" id="updateCompanyId" name="id">
                    <div class="mb-3">
                        <label for="updateCompanyName" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="updateCompanyName" name="company_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateCompanyType" class="form-label">Company Type</label>
                        <input type="text" class="form-control" id="updateCompanyType" name="company_type" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="updateCompanyAddress" class="form-label">Address</label>
                        <input type="text" class="form-control" id="updateCompanyAddress" name="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateCompanyContact" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="updateCompanyContact" name="contact_number" required>
                    </div>
                    <button type="submit" name="update_company" class="btn btn-primary">Update Company</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Company Modal -->
<div class="modal fade" id="deleteCompanyModal" tabindex="-1" aria-labelledby="deleteCompanyLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCompanyLabel">Delete Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this company?</p>
                <input type="hidden" id="deleteCompanyId">
                <button type="button" class="btn btn-danger" id="confirmDelete">Yes, Delete</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>



 
                
 <!-- Modal for Loan Details -->
<div class="modal fade" id="allLoanModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Loan Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

            <table class="table table-striped table-borderless">
                        <tr><td><h4 id="companyName"></h4></td> </tr>
                        <tr><td><p><strong>Company Type:</strong> <span id="companyType"></span></p></td> </tr>
                        <tr><td><p><strong>Address:</strong> <span id="companyAddress"></span></p></td> </tr>
                        <tr><td><p><strong>Contact:</strong> <span id="companyContact"></span></p></td> </tr>
                </table>




                
                <ul class="nav nav-tabs" id="loanTabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="demandLoanTab" data-bs-toggle="tab" href="#demandLoan">Demand Loan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="termLoanTab" data-bs-toggle="tab" href="#termLoan">Term Loan</a>
                    </li>
                </ul>

                <div class="tab-content mt-3">
                    
                    <div class="tab-pane fade show active" id="demandLoan">
                        <h5>Demand Loan Details</h5>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Demand Loan No</th>
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
                                    <th>Demand Loan No.</th>
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
                    </div>

                    
                    <div class="tab-pane fade" id="termLoan">
                        <h5>Term Loan Details</h5>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Term Loan No</th>
                                    <th>Sanction Date</th>
                                    <th>Reschedule Amount</th>
                                    <th>Installment Amount</th>
                                    <th>Last installment date</th>
                                </tr>
                            </thead>
                            <tbody id="termLoanDetails"></tbody>
                        </table>

                        <h5 class="mt-3">Transaction Details</h5>
                        <table class="table table-striped table-bordered" id="tlTransactionTable">
                            <thead>
                                <tr>
                                    <th>Term Loan No.</th>
                                    <th>Date</th>
                                    <th>Particular</th>
                                    <th>Amount <br> Debit</th>
                                    <th>Amount <br> Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody id="termTransactionDetails"></tbody>
                        </table>
                        <h5 class="mt-3">Final Balance: <span id="termFinalBalance" class="fw-bold"></span></h5>
                    </div>
                </div>

                <button class="btn btn-danger" id="printPdf_company">Print as PDF</button>
                <button class="btn btn-success" id="downloadExcel_">Print as Excel</button>







            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="allCompanyLoanModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Loan Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                <div class="mb-3">
                    <h4 id="allCompanyName"></h4>
                    <p><strong>Company Type:</strong> <span id="allCompanyType"></span></p>
                    <p><strong>Company Address:</strong> <span id="allCompanyAddress"></span></p>
                    <p><strong>Contact:</strong> <span id="allCompanyContact"></span></p>
                </div>

                
                <div class="accordion" id="companyAccordion">
                   
                </div>

                <!-- <button class="btn btn-danger mt-3" id="printPdf_all_company">Print as PDF</button>
                <button class="btn btn-success mt-3" id="downloadExcel">Print as Excel</button> -->
            </div>
        </div>
    </div>
</div>

<script>
function populateLoanModal(data) {
    // Parent Company
    document.getElementById("companyName").textContent = data.company.company_name;
    document.getElementById("companyType").textContent = data.company.company_type;
    document.getElementById("companyAddress").textContent = data.company.address;
    document.getElementById("companyContact").textContent = data.company.contact_number;

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
                            
                            <div class="tab-pane fade show active" id="demandLoan${index}">
                                <h5>Demand Loan Details</h5>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Demand Loan No</th>
                                            <th>Disbursement Date</th>
                                            <th>Expiry Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${child.demand_loans.map(dl => `
                                            <tr>
                                                <td>${dl.dl_no}</td>
                                                <td>${dl.disburse_date}</td>
                                                <td>${dl.expiry_date}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>

                                <h5 class="mt-3">Transaction Details</h5>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Demand Loan No.</th>
                                            <th>Date</th>
                                            <th>Particular</th>
                                            <th>Amount <br> Debit</th>
                                            <th>Amount <br> Credit</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${child.demand_loans.flatMap(dl => dl.transactions.map(tr => `
                                            <tr>
                                                <td>${dl.dl_no}</td>
                                                <td>${tr.transaction_date}</td>
                                                <td>${tr.details}</td>
                                                <td>${tr.type === 'Debit' ? tr.amount : ''}</td>
                                                <td>${tr.type === 'Credit' ? tr.amount : ''}</td>
                                                <td>-</td>
                                            </tr>
                                        `)).join('')}
                                    </tbody>
                                </table>
                            </div>

                           
                            <div class="tab-pane fade" id="termLoan${index}">
                                <h5>Term Loan Details</h5>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Term Loan No</th>
                                            <th>Sanction Date</th>
                                            <th>Reschedule Amount</th>
                                            <th>Installment Amount</th>
                                            <th>Last Installment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${child.term_loans.map(tl => `
                                            <tr>
                                                <td>${tl.sanction_no}</td>
                                                <td>${tl.sanction_date}</td>
                                                <td>${tl.reschedule_amount}</td>
                                                <td>${tl.installment_amount}</td>
                                                <td>${tl.last_installment_date}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>

                                <h5 class="mt-3">Transaction Details</h5>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Term Loan No.</th>
                                            <th>Date</th>
                                            <th>Particular</th>
                                            <th>Amount <br> Debit</th>
                                            <th>Amount <br> Credit</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${child.term_loans.flatMap(tl => tl.transactions.map(tr => `
                                            <tr>
                                                <td>${tl.sanction_no}</td>
                                                <td>${tr.transaction_date}</td>
                                                <td>${tr.details}</td>
                                                <td>${tr.type === 'Debit' ? tr.amount : ''}</td>
                                                <td>${tr.type === 'Credit' ? tr.amount : ''}</td>
                                                <td>-</td>
                                            </tr>
                                        `)).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    document.getElementById("companyAccordion").innerHTML = accordionContent;
}

</script>

<script>
        function toggleParentCompany() {
            let companyType = document.getElementById("company_type").value;
            console.log(companyType);
            document.getElementById("parentCompany").style.display = (companyType === "Group") ? "block" : "none";
        }
    </script>



