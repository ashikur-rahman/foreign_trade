<?php
$conn = new mysqli('localhost', 'root', '', 'foreign_trade');
$id = 59;

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$company = [];
$child_companies = [];

// Fetch Parent Company Details
$query = "SELECT * FROM companies WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $company = [
        "id" => $row['id'],
        "company_name" => $row['company_name'],
        "company_type" => $row['company_type'],
        "contact_number" => $row['contact_number'],
        "address" => $row['address'],
        "parent_id" => $row['parent_id']
    ];
}
$stmt->close();

if ($company["parent_id"] == 0) {
    // Fetch Child Companies
    $query = "SELECT * FROM companies WHERE parent_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $company["id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($child = $result->fetch_assoc()) {
        $child_id = $child['id'];
        
        $child_company = [
            "id" => $child['id'],
            "company_name" => $child['company_name'],
            "company_type" => $child['company_type'],
            "contact_number" => $child['contact_number'],
            "address" => $child['address'],
            "demand_loans" => [],
            "term_loans" => []
        ];
        
        // Fetch Demand Loans
        $query_dl = "SELECT * FROM demand_loan_entry WHERE company_id = ?";
        $stmt_dl = $conn->prepare($query_dl);
        $stmt_dl->bind_param("i", $child_id);
        $stmt_dl->execute();
        $result_dl = $stmt_dl->get_result();
        
        while ($loan = $result_dl->fetch_assoc()) {
            $loan_id = $loan['id'];
            $demand_loan = [
                "id" => $loan['id'],
                "dl_no" => $loan['dl_no'],
                "disburse_date" => $loan['disburse_date'],
                "expiry_date" => $loan['expiry_date'],
                "loan_creation_amount" => $loan['loan_creation_amount'],
                "transactions" => []
            ];
            
            // Fetch Demand Loan Transactions
            $query_dlt = "SELECT * FROM demand_loan_entry_transactions WHERE demand_loan_id = ?";
            $stmt_dlt = $conn->prepare($query_dlt);
            $stmt_dlt->bind_param("i", $loan_id);
            $stmt_dlt->execute();
            $result_dlt = $stmt_dlt->get_result();
            
            while ($txn = $result_dlt->fetch_assoc()) {
                $demand_loan["transactions"][] = [
                    "transaction_date" => $txn['transaction_date'],
                    "details" => $txn['details'],
                    "type" => $txn['type'],
                    "amount" => $txn['amount']
                ];
            }
            $stmt_dlt->close();
            
            $child_company["demand_loans"][] = $demand_loan;
        }
        $stmt_dl->close();
        
        // Fetch Term Loans
        $query_tl = "SELECT * FROM term_loan WHERE company_id = ?";
        $stmt_tl = $conn->prepare($query_tl);
        $stmt_tl->bind_param("i", $child_id);
        $stmt_tl->execute();
        $result_tl = $stmt_tl->get_result();
        
        while ($loan = $result_tl->fetch_assoc()) {
            $loan_id = $loan['id'];
            $term_loan = [
                "id" => $loan['id'],
                "sanction_no" => $loan['sanction_no'],
                "sanction_date" => $loan['sanction_date'],
                "reschedule_amount" => $loan['reschedule_amount'],
                "installment_amount" => $loan['installment_amount'],
                "last_installment_date" => $loan['last_installment_date'],
                "transactions" => []
            ];
            
            // Fetch Term Loan Transactions
            $query_tlt = "SELECT * FROM term_loan_entry_transactions WHERE term_loan_id = ?";
            $stmt_tlt = $conn->prepare($query_tlt);
            $stmt_tlt->bind_param("i", $loan_id);
            $stmt_tlt->execute();
            $result_tlt = $stmt_tlt->get_result();
            
            while ($txn = $result_tlt->fetch_assoc()) {
                $term_loan["transactions"][] = [
                    "transaction_date" => $txn['transaction_date'],
                    "details" => $txn['details'],
                    "type" => $txn['type'],
                    "amount" => $txn['amount']
                ];
            }
            $stmt_tlt->close();
            
            $child_company["term_loans"][] = $term_loan;
        }
        $stmt_tl->close();
        
        $child_companies[] = $child_company;
    }
    $stmt->close();
}

$response = [
    "company" => $company,
    "child_companies" => $child_companies
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>


$children = [];
$have_child = false;
$child_company = [];
$child_loans = [];
$child_transactions = [];
$child_term_loans = [];
$child_term_transactions = [];

// var_dump($id);
// Fetch parent companies
// var_dump($query);


$query = "SELECT c.*
FROM companies c
WHERE c.id =  $id ";
//var_dump($query_2);
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
if (empty($company)) {
    $company = [
        "id"  => $row['id'],
        "company_name" => $row['company_name'],
        "company_type" => $row['company_type'],
        "contact_number" => $row['contact_number'],
        "address" => $row['address'],
        "parent_id" => $row['parent_id']
    ];
}
}

if($company["parent_id"] == 0 ){

    $find_child_id =  $company["id"];
    $child_companies = $conn->query("SELECT * FROM companies WHERE parent_id =$find_child_id");
    $children = [];
    while ($child = $child_companies->fetch_assoc()) {
        $child_id= $child['parent_id'];
        $children[$child['parent_id']][] = $child;
    }
    if(isset($children) && !empty($children)){

        // var_dump($children);
        // var_dump(count($children[$child_id]));
        $child_company=[];
        for($i=0; $i<count($children[$child_id]); $i++){
            
                        $ids = $children[$child_id];

                        $child_loans = [];
                       // $have_child = TRUE;
                       for($j=0; $j<count($ids); $j++){

                        $id=$ids[$j]['id'];

                       
                        
                            $query_2 = "SELECT c.*
                            FROM companies c
                            WHERE c.id =  $id ";
                            $result_1 = $conn->query($query_2);
                            //$child_loans = [];
                            while ($row = $result_1->fetch_assoc()) {
                                if (empty($child_company)) {
                                    $child_company = [
                                        "id"  => $row['id'],
                                        "company_name" => $row['company_name'],
                                        "company_type" => $row['company_type'],
                                        "contact_number" => $row['contact_number'],
                                        "address" => $row['address']
                                    ];
                                }
                            }

                            $query_1 = "SELECT dl.*
                            FROM demand_loan_entry dl 
                            WHERE dl.company_id =  $id";
    
                           // var_dump($query_1);exit();
                            $result = $conn->query($query_1);
    
    
    
                            while ($row = $result->fetch_assoc()) {
                                if (!empty($row['dl_no'])) {
                                    $child_loans[] = [
                                        "id" => $row['id'],
                                        "company_id" => $row['company_id'],
                                        "dl_no" => $row['dl_no'],
                                        "disburse_date" => $row['disburse_date'],
                                        "expiry_date" => $row['expiry_date'],
                                        "loan_creation_amount" => $row['loan_creation_amount']
                                    ];
                                }
                            }
    
                             

                             //var_dump( $loans); exit();
                        if (!empty($child_loans)) {
                            for($i=0;$i<count($child_loans); $i++){ 

                            $dl_id = $child_loans[$i]['id'];
                            //var_dump( $dl_id); exit();
                                    $query_3 = "SELECT t.* 
                                    FROM demand_loan_entry_transactions t
                                    WHERE t.demand_loan_id = $dl_id";
                                   
                                    $result_2 = $conn->query($query_3);

                                    while ($row = $result_2->fetch_assoc()) {
                                    if (!empty($row['id'])) {
                                        $child_transactions[] = [
                                            "demand_lone_number" => $child_loans[$i]['dl_no'],
                                            "transaction_date" => $row['transaction_date'],
                                            "details" => $row['details'],
                                            "type" => $row['type'],
                                            "amount" => $row['amount']
                                        ];
                                    }
                                    }


                            }
                        }

                     
                        $query_4 = "SELECT tl.* FROM term_loan tl WHERE tl.company_id = $id";
                        $result_4 = $conn->query($query_4);
                       
                       
                        while ($row = $result_4->fetch_assoc()) {
                            $child_term_loans[] = [
                                "term_loan_id" => $row['id'],
                                "sanction_no" => $row['sanction_no'],
                                "sanction_date" => $row['sanction_date'],
                                "reschedule_amount" => $row['reschedule_amount'],
                                "installment_amount" => $row['installment_amount'],
                                "last_installment_date" => $row['last_installment_date']
                            ];
                        }
                      

                        foreach ($child_term_loans as $loan) {
                            $tl_id = $loan['term_loan_id'];
                            $query_5 = "SELECT * FROM term_loan_entry_transactions WHERE term_loan_id = $tl_id";
                            $result_5 = $conn->query($query_5);

                            while ($row = $result_5->fetch_assoc()) {
                                $child_term_transactions[] = [
                                    "term_lone_number" => $loan['term_loan_id'],
                                    "transaction_date" => $row['transaction_date'],
                                    "details" => $row['details'],
                                    "type" => $row['type'],
                                    "amount" => $row['amount']
                                ];
                            }
                        }
                        
                       
                        


                      //  echo json_encode(["company" => $company, "loans" => $loans, "transactions" => $transactions, "term_loans" => $term_loans, "term_transactions" => $term_transactions]);



                       
                       
                       
                       
                       
                       
                       
                       
                        }

                        var_dump($company); 
                        var_dump($child_company); 
                        var_dump($child_loans); 
                        var_dump($child_transactions); 
                        var_dump($child_term_loans); 
                        var_dump($child_term_transactions);
                        // exit();
                        // var_dump($id);
                        exit();


                  

                       

                        
                        
/*

                        //echo json_encode(["company" => $company, "loans" => $loans, "transactions" => $transactions]);


                        

                        
                        


*/




        }

       // var_dump($company);
                        


    }
   // var_dump($children);
} else {



}


//                         var_dump($company);
//                         var_dump($child_transactions);
//                         var_dump($child_term_loans);
//                         var_dump($child_term_transactions);

                        exit('cccccc');


//var_dump($children);
// else {
// }
//exit('aa'); 


