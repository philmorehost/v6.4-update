<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-spadmin-config.php");
    
    if(isset($_GET["deleteBillingID"])){
        $billing_id_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($_GET["deleteBillingID"]))));
        if(is_numeric($billing_id_number)){
            $select_billing_with_id = mysqli_query($connection_server, "SELECT * FROM sas_vendor_billings WHERE id='$billing_id_number'");
            if(mysqli_num_rows($select_billing_with_id) == 1){
                mysqli_query($connection_server, "DELETE FROM sas_vendor_billings WHERE id='$billing_id_number'");
                $json_response_array = array("desc" => ucwords("Billing Deleted Successfully"));
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(mysqli_num_rows($select_billing_with_id) > 1){
                    $json_response_array = array("desc" => ucwords("Duplicated Details, Contact Admin"));
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(mysqli_num_rows($select_billing_with_id) < 1){
                        $json_response_array = array("desc" => ucwords("Billing Details Not Exists Or May Have Been Deleted"));
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }
            }
        }else{
            //Non-numeric string
            $json_response_array = array("desc" => "Non-numeric string");
            $json_response_encode = json_encode($json_response_array,true);
        }
        
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: /bc-spadmin/Billings.php");
    }

    
?>
<!DOCTYPE html>
<head>
    <title></title>
    <meta charset="UTF-8" />
    <meta name="description" content="" />
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

    <?php
    	//Redirect To Vendor Page
        $getVendorUrl = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["vendorUrl"])));
    	$getVendorLogAuth = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["vendorLogAuth"])));
        $getRedirectUrl = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["redirect"])));
    	
    	if(isset($_GET["vendorUrl"]) && !empty($getVendorUrl) && isset($_GET["vendorLogAuth"]) && !empty($getVendorLogAuth)){
            if(isset($_GET["redirect"]) && !empty($getRedirectUrl)){
                echo '<script>	window.onload = function(){	window.open("http://'.$getVendorUrl.'/bc-admin/Dashboard.php?logVendorAdmin='.$getVendorLogAuth.'&&redirectAdminTo='.$getRedirectUrl.'","_blank"); window.open("/bc-spadmin/Vendors.php","_self");	}	</script>';
            }else{
                echo '<script>	window.onload = function(){	window.open("http://'.$getVendorUrl.'/bc-admin/Dashboard.php?logVendorAdmin='.$getVendorLogAuth.'","_blank"); window.open("/bc-spadmin/Vendors.php","_self");	}	</script>';
            }
    	}
    ?>
</head>
<body>
    <?php include("../func/bc-spadmin-header.php"); ?>
    <div class="pagetitle">
      <h1>VIEW  BILLINGS</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Billings</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
        <?php
            
            if(!isset($_GET["searchq"]) && isset($_GET["page"]) && !empty(trim(strip_tags($_GET["page"]))) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) >= 1)){
                $page_num = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["page"])));
                $offset_statement = " OFFSET ".((10 * $page_num) - 10);
            }else{
                $offset_statement = "";
            }
            
            if(isset($_GET["searchq"]) && !empty(trim(strip_tags($_GET["searchq"])))){
                $search_statement = " && (bill_type LIKE '%".trim(strip_tags($_GET["searchq"]))."%' OR amount LIKE '%".trim(strip_tags($_GET["searchq"]))."%' OR description LIKE '%".trim(strip_tags($_GET["searchq"]))."%' OR starting_date LIKE '%".trim(strip_tags($_GET["searchq"]))."%' OR ending_date LIKE '%".trim(strip_tags($_GET["searchq"]))."%')";
                $search_parameter = "searchq=".trim(strip_tags($_GET["searchq"]))."&&";
            }else{
                $search_statement = "";
                $search_parameter = "";
            }
            $get_active_billing_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_billings WHERE starting_date != '' $search_statement ORDER BY date DESC LIMIT 10 $offset_statement");
            
        ?>
            <div class="card info-card px-5 py-5">
                <form method="get" action="Billings.php" class="">
                    <input style="user-select: auto;" name="searchq" type="text" value="<?php echo trim(strip_tags($_GET["searchq"])); ?>" placeholder="Billing Type, Description, Year, Month, Day e.t.c" class="form-control mb-1" />
                    <button style="user-select: auto;" type="submit" class="btn btn-success d-inline col-12 col-lg-auto my-2" >
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>
            </div>

          <span style="user-select: auto;" class="fw-bold h4 mb-1 mt-5">BILLING LIST (<?php echo mysqli_num_rows($get_active_billing_details); ?>)</span><br>
            <div style="user-select: auto; cursor: grab;" class="overflow-auto">
              <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
                  <thead class="thead-dark">
                    <tr>
                        <th>S/N</th><th>Type</th><th>Description</th><th>Amount</th><th>Starting Date</th><th>Ending Date</th><th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if(mysqli_num_rows($get_active_billing_details) >= 1){
                        while($billing_details = mysqli_fetch_assoc($get_active_billing_details)){
                            $transaction_type = ucwords($user_details["type_alternative"]);
                            $countTransaction += 1;
                            $delete_billing_detail = '<span onclick="customJsRedirect(`/bc-spadmin/Billings.php?deleteBillingID='.$billing_details["id"].'`, `Are you sure you want to delete billing with serial number ['.$countTransaction.']`);" style="text-decoration: underline; color: green;" class=""><i title="Delete Billing" style="" class="bi bi-trash-fill" ></i></span>';
                            
                            $billing_type_with_link = $billing_details["bill_type"].' <span onclick="customJsRedirect(`/bc-spadmin/BillingEdit.php?billingID='.$billing_details["id"].'`, `Are you sure you want to edit billing with serial number ['.$countTransaction.']`);" style="text-decoration: underline; color: green;" class="a-cursor"><i title="Edit Billing" style="" class="bi bi-pencil-square" ></i></span>';
                            
                            echo 
                            '<tr>
                                <td>'.$countTransaction.'</td><td>'.$billing_type_with_link.'</td><td>'.checkTextEmpty($billing_details["description"]).'</td><td>'.toDecimal($billing_details["amount"], 2).'</td><td>'.formDateWithoutTime($billing_details["starting_date"]).'</td><td>'.formDateWithoutTime($billing_details["ending_date"]).'</td><td class="">'.$delete_billing_detail.'</td>
                            </tr>';
                        }
                    }
                    ?>
                  </tbody>
                </table>
            </div><br/>
            
            <div class="mt-2 justify-content-between justify-items-center">
                <?php if(isset($_GET["page"]) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) > 1)){ ?>
                <a href="Billings.php?<?php echo $search_parameter; ?>page=<?php echo (trim(strip_tags($_GET["page"])) - 1); ?>">
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
                <a href="Billings.php?<?php echo $search_parameter; ?>page=<?php echo $trans_next; ?>">
                    <button style="user-select: auto;" class="btn btn-success col-auto">Next</button>
                </a>
            </div>
      </div>
    </section>

    <?php include("../func/bc-spadmin-footer.php"); ?>
    
</body>
</html>