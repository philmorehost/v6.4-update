<?php session_start();
    include("../func/bc-admin-config.php");
        
    if(isset($_POST["share-fund"])){
        $purchase_method = "web";
        $purchase_method = strtoupper($purchase_method);
    	$purchase_method_array = array("WEB");
    	if(in_array($purchase_method, $purchase_method_array)){
        if($purchase_method === "WEB"){
            $user = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["user"]))));
            $type = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["type"]))));
            $amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/","",trim(strip_tags($_POST["amount"]))));
        }

        $discounted_amount = $amount;
        $type_alternative = ucwords("wallet ".$type);
        $reference = substr(str_shuffle("12345678901234567890"), 0, 15);
        $description = ucwords("account ".$type."ed by admin");
        if(in_array($type, array("debit"))){
        	$transType = "debit";
        }
        
        if(in_array($type, array("credit","refund"))){
        	$transType = "credit";
        }
        $get_logged_user_query = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_logged_admin_details["id"]."' && username='$user' LIMIT 1");
		if(in_array($type, array("debit","credit","refund"))){
			if(mysqli_num_rows($get_logged_user_query) == 1){
				$credit_other_user = chargeOtherUser($user, $transType, $user, ucwords("wallet ".$type), $reference, "", $amount, $discounted_amount, $description, $purchase_method, $_SERVER["HTTP_HOST"], "1");
				if(in_array($credit_other_user, array("success"))){
					$json_response_array = array("reference" => $reference, "status" => "success", "desc" => ucwords($user." ".$type."ed with N".$amount." successfully"));
                	$json_response_encode = json_encode($json_response_array,true);
				}
                                                    
            	if($credit_other_user == "failed"){
					$json_response_array = array("desc" => "Transaction Failed");
                	$json_response_encode = json_encode($json_response_array,true);
             	}		
			}else{
				//User not exists
				$json_response_array = array("desc" => "User not exists");
				$json_response_encode = json_encode($json_response_array,true);
			}
		}else{
			//Invalid Transaction Type
			$json_response_array = array("desc" => "Invalid Transaction Type");
			$json_response_encode = json_encode($json_response_array,true);
		}
    }else{
        //Purchase Method Not specified
        $json_response_array = array("desc" => "Purchase Method Not specified");
        $json_response_encode = json_encode($json_response_array,true);
    }
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
?>
<!DOCTYPE html>
<head>
    <title>Share Fund | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
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
      <h1>SHARE FUND</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Share Fund</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
  
        
        <div style="text-align: center;" class="card info-card px-5 py-5">
            <span style="user-select: auto;" class="text-dark h3">FUND TRANSFER REQUEST (A2C)</span><br>
            <form method="post" action="">
                <input style="text-align: center;" id="share-fund-user" name="user" onkeyup="adminConfirmUser();" type="text" value="" placeholder="Username" class="form-control col-12 mt-3" required/><br/>
                <div style="text-align: center;" class="text-dark h5">
                    <span id="user-status-span" class="" style="user-select: auto;">Enter User ID</span>
                </div><br/>
                <select style="text-align: center;" id="" name="type" onchange="getWebApikey(this);" class="form-control col-12 mt-3" required/>
                	<option value="" selected hidden default>Choose Transaction Type</option>
                	<option value="debit">Debit</option>
                	<option value="credit">Credit</option>
                	<option value="refund">Refund</option>
                </select><br/>
                <input style="text-align: center;" id="share-fund-amount" name="amount" onkeyup="adminConfirmUser();" pattern="[0-9]{2, 7}" title="Digit must be around 10 to 1999999 naira" value="" placeholder="Amount" class="form-control col-12 mt-3" required/><br/>
                <button id="proceedBtn" name="share-fund" type="button" style="pointer-events: none; user-select: auto;" class="btn btn-success col-12 mt-3" >
                    SHARE FUND
                </button><br>
                <div style="text-align: center;" class="text-dark h5">
                    <span id="product-status-span" class="" style="user-select: auto;"></span>
                </div>
            </form>
        </div>

	<?php include("../func/bc-admin-footer.php"); ?>
	
</body>
</html>