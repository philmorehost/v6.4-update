<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
    if ($connection_server && isset($get_logged_user_details)) {
        $get_requery = isset($_GET["requery"]) ? trim(strip_tags($_GET["requery"])) : '';
        if ($get_requery) {
            $select_user_requeried_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$get_logged_user_details["username"]."' && reference='".$get_requery."'");
            if($select_user_requeried_transaction_details && mysqli_num_rows($select_user_requeried_transaction_details) == 1){
                $purchase_method = "web";
                include_once($_SERVER["DOCUMENT_ROOT"]."/web/func/requery-transaction.php");
                $json_response_decode = json_decode($json_response_encode ?? '{}',true);
                $_SESSION["product_purchase_response"] = $json_response_decode["desc"] ?? '';
            }
        }

        $get_user_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$get_logged_user_details["username"]."' ORDER BY date DESC LIMIT 5");
    } else {
        $get_user_transaction_details = false;
    }
?>
<div class="card info-card px-5 py-5 transaction-history-table">
    <div class="col-12">
        <form method="get" action="Transactions.php">
            <input style="user-select: auto;" name="searchq" type="text" value="<?php echo trim(strip_tags($_GET["searchq"])); ?>" placeholder="Reference No, Meter No, Phone Number e.t.c" class="form-control" required/>
            <button style="user-select: auto;" type="submit" class="btn btn-success d-inline col-12 col-lg-auto my-2" >
                <i class="bi bi-search"></i> Search
            </button>
        </form>
    </div>
    <div style="user-select: auto; cursor: grab;" class="overflow-auto">
        <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
            <thead class="thead-dark">
              <tr>
                  <th>S/N</th><th>Reference</th><th>Type</th><th style="">Description</th><th>Amount</th><th>Amount Paid</th><th>Mode</th><th>Action</th><th>Status</th><th>Date</th>
              </tr>
            </thead>
            <tbody>
            <?php
                if($get_user_transaction_details && mysqli_num_rows($get_user_transaction_details) >= 1){
                    while($user_transaction = mysqli_fetch_assoc($get_user_transaction_details)){
                        if(!empty($user_transaction["api_id"]) && !empty($user_transaction["product_id"])){
                            $get_user_product_details_res = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$user_transaction["product_id"]."' LIMIT 1");
                            $get_user_product_details = ($get_user_product_details_res) ? mysqli_fetch_array($get_user_product_details_res) : false;

                            $get_user_api_details_res = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"] . "' && id='".$user_transaction["api_id"]."' LIMIT 1");
                            $get_user_api_details = ($get_user_api_details_res) ? mysqli_fetch_array($get_user_api_details_res) : false;

                            $transaction_type = ($get_user_product_details && $get_user_api_details) ? ucwords($get_user_product_details["product_name"]." ".str_replace(["-","_"], " ", $get_user_api_details["api_type"])) : ucwords($user_transaction["type_alternative"]);
                        }else{
                            $transaction_type = ucwords($user_transaction["type_alternative"]);
                        }
                        $countTransaction += 1;
                        echo 
                        '<tr>
                            <td>'.$countTransaction.'</td><td style="user-select: auto;">'.$user_transaction["reference"].'</td><td>'.$transaction_type.'</td><td>'.$user_transaction["description"].'</td><td>'.toDecimal($user_transaction["amount"], 2).'</td><td>'.toDecimal($user_transaction["discounted_amount"], 2).'</td><td>'.$user_transaction["mode"].'</td><td>'.transactionActionButton($user_transaction["api_id"], $user_transaction["product_id"], $user_transaction["reference"], $user_transaction["status"], $transaction_type).'</td><td>'.tranStatus($user_transaction["status"]).'</td><td>'.formDate($user_transaction["date"]).'</td>
                        </tr>';
                    }
                }
            ?>
            </tbody>
        </table>
    </div>
</div>