<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start([
    'cookie_lifetime' => 286400,
	'gc_maxlifetime' => 286400,
]);
    include("../func/bc-admin-config.php");
?>
<!DOCTYPE html>
<head>
    <title>Users Transaction | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_super_admin_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="<?php echo $get_all_super_admin_site_details["primary_color"] ?? "#198754"; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">
    
      <!-- Vendor CSS Files -->
  <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets-2/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets-2/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets-2/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets-2/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets-2/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="../assets-2/css/style.css" rel="stylesheet">

</head>
<body>
    <?php include("../func/bc-admin-header.php"); ?>
    <div class="pagetitle">
      <h1>TRANSACTIONS</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Transactions</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">

        <?php
            $select_user_requeried_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' && reference='".trim(strip_tags($_GET["requery"]))."'");
            if(mysqli_num_rows($select_user_requeried_transaction_details) == 1){
            	$get_selected_user_requeried_transaction_details = mysqli_fetch_array($select_user_requeried_transaction_details);
            	$get_logged_user_query = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_logged_admin_details["id"]."' && username='".$get_selected_user_requeried_transaction_details["username"]."' LIMIT 1");
            	if(mysqli_num_rows($get_logged_user_query) == 1){
            		$get_logged_user_details = mysqli_fetch_array($get_logged_user_query);
            	}
            	$purchase_method = "web";
            	include_once($_SERVER["DOCUMENT_ROOT"]."/web/func/requery-transaction.php");
            	$json_response_decode = json_decode($json_response_encode,true);
            	$_SESSION["product_purchase_response"] = $json_response_decode["desc"];
            }
            
            if(isset($_GET["page"]) && !empty(trim(strip_tags($_GET["page"]))) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) >= 1)){
                $page_num = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["page"])));
                $offset_statement = " OFFSET ".((20 * $page_num) - 20);
            }
            
             if(!isset($_GET["searchq"]) && isset($_GET["page"]) && !empty(trim(strip_tags($_GET["page"]))) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) >= 1)){
                $page_num = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["page"])));
                $offset_statement = " OFFSET ".((20 * $page_num) - 20);
            }else{
                $offset_statement = "";
            }
            
            if(isset($_GET["searchq"]) && !empty(trim(strip_tags($_GET["searchq"])))){
                $search_statement = " && (product_unique_id LIKE '%".trim(strip_tags($_GET["searchq"]))."%' OR reference LIKE '%".trim(strip_tags($_GET["searchq"]))."%' OR type_alternative LIKE '%".trim(strip_tags($_GET["searchq"]))."%' OR description LIKE '%".trim(strip_tags($_GET["searchq"]))."%' OR username LIKE '%".trim(strip_tags($_GET["searchq"]))."%')";
                $search_parameter = "searchq=".trim(strip_tags($_GET["searchq"]))."&&";
            }else{
                $search_statement = "";
                $search_parameter = "";
            }
            $get_user_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' $search_statement ORDER BY date DESC LIMIT 200 $offset_statement");
            
        ?>
        <div class="card info-card px-5 py-5">
            <div class="row">
                <form method="get" action="Transactions.php" class="">
                    <input style="user-select: auto;" name="searchq" type="text" value="<?php echo trim(strip_tags($_GET["searchq"])); ?>" placeholder="Email, Reference No e.t.c" class="form-control mt-3" />
                    <input hidden style="user-select: auto;" name="page" type="number" value="1" placeholder="" class="" />
                    <button style="user-select: auto;" type="submit" class="btn btn-success d-inline col-12 col-lg-auto my-2" >
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>
            </div>
          
          <div style="user-select: auto; cursor: grab;" class="overflow-auto">
            <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
                <thead class="thead-dark">
                  <tr>
                      <th>S/N</th><th>Reference</th><th>Username ID</th><th>Type</th><th>Balance Before</th><th>Amount</th><th>Amount Paid</th><th>Balance After</th><th style="">Description</th><th>Mode</th><th>Action</th><th>Status</th><th>API Website</th><th>Date</th>
                  </tr>
                </thead>
                <tbody>
                    <?php
                        if(mysqli_num_rows($get_user_transaction_details) >= 1){
                            while($user_transaction = mysqli_fetch_assoc($get_user_transaction_details)){
                                if(!empty($user_transaction["api_id"]) && !empty($user_transaction["product_id"])){
                                    $get_admin_product_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='".$user_transaction["product_id"]."' LIMIT 1");
                                    $get_admin_api_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='".$user_transaction["api_id"]."' LIMIT 1");
                                    $transaction_type = ucwords($get_admin_product_details["product_name"]." ".str_replace(["-","_"], " ", $get_admin_api_details["api_type"]));
                                }else{
                                    $transaction_type = ucwords($user_transaction["type_alternative"]);
                                }
                                $countTransaction += 1;
                                echo 
                                '<tr>
                                    <td>'.$countTransaction.'</td><td style="user-select: auto;">'.$user_transaction["reference"].'</td><td>'.ucwords($user_transaction["username"]).'</td><td>'.$transaction_type.'</td><td>'.toDecimal($user_transaction["balance_before"], 2).'</td><td>'.toDecimal($user_transaction["amount"], 2).'</td><td>'.toDecimal($user_transaction["discounted_amount"], 2).'</td><td>'.toDecimal($user_transaction["balance_after"], 2).'</td><td>'.$user_transaction["description"].'</td><td>'.$user_transaction["mode"].'</td><td>'.adminTransactionActionButton($user_transaction["api_id"], $user_transaction["product_id"], $user_transaction["reference"], $user_transaction["status"], $transaction_type).'</td><td>'.tranStatus($user_transaction["status"]).'</td><td>'.$user_transaction["api_website"].'</td><td>'.formDate($user_transaction["date"]).'</td>
                                </tr>';
                            }
                        }
                    ?>
                  </tbody>
                </table>
            </div>
            <div class="mt-2 justify-content-between justify-items-center">
                <?php if(isset($_GET["page"]) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) > 1)){ ?>
                <a href="Transactions.php?<?php echo $search_parameter; ?>page=<?php echo (trim(strip_tags($_GET["page"])) - 1); ?>">
                    <button style="user-select: auto;" class="btn btn-success col-auto">Prev</button>
                </a>
                <?php } ?>
                <?php
                	if(isset($_GET["page"]) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) >= 1)){
                		$trans_next = (trim(strip_tags($_GET["page"])) +1);
                	}else{
                		$trans_next = 2;
                	}
                ?>
                <a href="Transactions.php?<?php echo $search_parameter; ?>page=<?php echo $trans_next; ?>">
                    <button style="user-select: auto;" class="btn btn-success col-auto">Next</button>
                </a>
            </div>
        </div>
      </div>
    </section>
    <?php include("../func/bc-admin-footer.php"); ?>
    
</body>
</html>