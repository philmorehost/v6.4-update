<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-admin-config.php");
?>
<!DOCTYPE html>
<head>
    <title>Users Batch Transaction | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
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
      <h1>BATCH TRANSACTIONS</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Batch Transactions</li>
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
            
            if(!isset($_GET["searchq"]) && isset($_GET["page"]) && !empty(trim(strip_tags($_GET["page"]))) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) >= 1)){
                $page_num = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["page"])));
                $offset_statement = " OFFSET ".((20 * $page_num) - 20);
            }else{
                $offset_statement = "";
            }
            
            if(isset($_GET["searchq"]) && !empty(trim(strip_tags($_GET["searchq"])))){
                $search_statement = " && (product_name LIKE '%" . trim(strip_tags($_GET["searchq"])) . "%' OR batch_number LIKE '%" . trim(strip_tags($_GET["searchq"])) . "%' OR username LIKE '%".trim(strip_tags($_GET["searchq"]))."%')";
                $search_parameter = "searchq=".trim(strip_tags($_GET["searchq"]))."&&";
            }else{
                $search_statement = "";
                $search_parameter = "";
            }
            $get_user_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_bulk_product_purchase WHERE vendor_id='".$get_logged_admin_details["id"]."' $search_statement ORDER BY date DESC LIMIT 20 $offset_statement");
            
        ?>
        <div class="card info-card px-5 py-5">
            <div class="row">
                <form method="get" action="BatchTransactions.php" class="">
                    <input style="user-select: auto;" name="searchq" type="text" value="<?php echo trim(strip_tags($_GET["searchq"])); ?>" placeholder="Username, Batch number" class="form-control mt-3" />
                    <button style="user-select: auto;" type="submit" class="btn btn-success d-inline col-12 col-lg-auto my-2" >
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>
            </div>
            
        <div style="user-select: auto; cursor: grab;" class="overflow-auto">
          <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
              <thead class="thead-dark">
                <tr>
                    <th>S/N</th>
                    <th>Username ID</th>
                    <th>Product Name</th>
                    <th>Batch number</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (mysqli_num_rows($get_user_transaction_details) >= 1) {
                    while ($user_transaction = mysqli_fetch_assoc($get_user_transaction_details)) {

                        $get_successful_batch_transaction = mysqli_num_rows(mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && status='1' && batch_number='" . $user_transaction["batch_number"] . "'"));
                        $get_pending_batch_transaction = mysqli_num_rows(mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && status='2' && batch_number='" . $user_transaction["batch_number"] . "'"));
                        $get_failed_batch_transaction = mysqli_num_rows(mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && status='3' && batch_number='" . $user_transaction["batch_number"] . "'"));

                        $batch_transations_status = "Success: " . $get_successful_batch_transaction . "; Pending: " . $get_pending_batch_transaction . "; Failed: " . $get_failed_batch_transaction;

                        $countTransaction += 1;
                        echo
                            '<tr>
                                    <td>' . $countTransaction . '</td><td>' . $user_transaction["username"] . '</td><td>' . $user_transaction["product_name"] . '</td><td style="user-select: auto;"><a style="color: inherit; text-decoration: underline;" target="_blank" href="/bc-admin/Transactions.php?searchq=' . $user_transaction["batch_number"] . '">' . $user_transaction["batch_number"] . '</a></td><td>'.$batch_transations_status.'</td><td>' . formDate($user_transaction["date"]) . '</td>
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