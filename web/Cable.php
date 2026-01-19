<?php session_start();
    include("../func/bc-config.php");
        
    if(isset($_POST["buy-cable"])){
        $purchase_method = "web";
        $action_function = 1;
		include_once("func/cable.php");
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        unset($_SESSION["iuc_number"]);
        unset($_SESSION["cable_provider"]);
        unset($_SESSION["cable_package"]);
        unset($_SESSION["cable_name"]);
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }

    if(isset($_POST["verify-cable"])){
        $purchase_method = "web";
        $action_function = 3;
		include_once("func/cable.php");
        $json_response_decode = json_decode($json_response_encode,true);
        if($json_response_decode["status"] == "success"){
            $_SESSION["iuc_number"] = $iuc_no;
            $_SESSION["cable_provider"] = $isp;
            $_SESSION["cable_package"] = $quantity;
            $_SESSION["cable_name"] = $json_response_decode["desc"];
        }
        
        if($json_response_decode["status"] == "failed"){
            $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        }
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }

    if(isset($_POST["reset-cable"])){
        unset($_SESSION["iuc_number"]);
        unset($_SESSION["cable_provider"]);
        unset($_SESSION["cable_package"]);
        unset($_SESSION["cable_name"]);
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
    
?>
<!DOCTYPE html>
<head>
    <title>Cable | <?php echo $get_all_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="<?php echo $get_all_site_details["primary_color"] ?? "#198754"; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">
            
    <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all'">
  <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" media="print" onload="this.media='all'">
  <link href="../assets-2/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets-2/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets-2/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets-2/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets-2/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="../assets-2/css/style.css" rel="stylesheet">

</head>
<body>
	<?php include("../func/bc-header.php"); ?>	
	<div class="pagetitle d-none d-md-block">
      <h1>BUY CABLE</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Buy Cable</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
		<div class="card info-card sales-card">
			<div class="card-body">
				<h5 class="card-title">Wallet Balance <span>| <?php echo "N".number_format($get_logged_user_details["balance"], 2); ?></span></h5>
			</div>
		</div>

    
    <div class="card info-card px-5 py-5">
	
            <form method="post" action="">
                <?php if(!isset($_SESSION["cable_name"])){ ?>
                <div style="text-align: center; user-select: auto;" style="container">
                    <img alt="Startimes" id="startimes-lg" product-status="enabled" src="/asset/startimes.jpg" onclick="tickCableCarrier('startimes');" class="col-2 rounded-5 border m-1 "/>
                    <img alt="DSTV" id="dstv-lg" product-status="enabled" src="/asset/dstv.jpg" onclick="tickCableCarrier('dstv');" class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1"/>
                    <img alt="GOTV" id="gotv-lg" product-status="enabled" src="/asset/gotv.jpg" onclick="tickCableCarrier('gotv');" class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1"/>
                    <img alt="ShowMax" id="showmax-lg" product-status="enabled" src="/asset/showmax.jpg" onclick="tickCableCarrier('showmax');" class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1"/>
                </div><br/>
                <input id="isprovider" name="isp" type="text" placeholder="Isp" hidden readonly required/>
                <input style="text-align: center;" id="iuc-number" name="iuc-number" onkeyup="tickCableCarrier(); resetCableQuantity();" type="text" value="" placeholder="Decoder IUC No." pattern="[0-9]*" inputmode="numeric" title="Charater must be atleast 10 digit long" class="form-control mb-1" required/><br/>
                <select style="text-align: center;" id="product-amount" name="quantity" onchange="tickCableCarrier();" class="form-control mb-1" required/>
                	<option product-category="" value="" default hidden selected>Cable Quantity</option>
                    <?php
                        $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                        if($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true){
                            $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                            $product_name_array = array("startimes", "dstv", "gotv", "showmax");
							$cable_type_table_name_arrays = array("startimes"=>"sas_cable_status", "dstv"=>"sas_cable_status", "gotv"=>"sas_cable_status", "showmax"=>"sas_cable_status");
							
							//Startimes
                            $get_startimes_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$cable_type_table_name_arrays[$product_name_array[0]]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."'"));
                            $get_api_enabled_startimes_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_startimes_status_details["api_id"]."' && api_type='cable' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_startimes_lists) == 1){
                                $get_api_enabled_startimes_lists = mysqli_fetch_array($get_api_enabled_startimes_lists);
                                $product_table_startimes_cable = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."' LIMIT 1"));
                                if($product_table_startimes_cable["status"] == 1){
                                	$product_discount_table_startimes_cable = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_startimes_lists["id"]."' && product_id='".$product_table_startimes_cable["id"]."'");
                                	if(mysqli_num_rows($product_discount_table_startimes_cable) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_startimes_cable)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="startimes-cable" value="'.$product_details["val_1"].'" hidden>Startimes '.ucwords(trim(str_replace(["-", "_"], " ", $product_details["val_1"]))).' N'.$product_details["val_2"].'</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //Dstv
                            $get_dstv_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$cable_type_table_name_arrays[$product_name_array[1]]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."'"));
                            $get_api_enabled_dstv_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_dstv_status_details["api_id"]."' && api_type='cable' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_dstv_lists) == 1){
                                $get_api_enabled_dstv_lists = mysqli_fetch_array($get_api_enabled_dstv_lists);
                                $product_table_dstv_cable = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."' LIMIT 1"));
                                if($product_table_dstv_cable["status"] == 1){
                                	$product_discount_table_dstv_cable = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_dstv_lists["id"]."' && product_id='".$product_table_dstv_cable["id"]."'");
                                	if(mysqli_num_rows($product_discount_table_dstv_cable) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_dstv_cable)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="dstv-cable" value="'.$product_details["val_1"].'" hidden>Dstv '.ucwords(trim(str_replace(["-", "_"], " ", $product_details["val_1"]))).' N'.$product_details["val_2"].'</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //Gotv
                            $get_gotv_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$cable_type_table_name_arrays[$product_name_array[2]]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."'"));
                            $get_api_enabled_gotv_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_gotv_status_details["api_id"]."' && api_type='cable' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_gotv_lists) == 1){
                                $get_api_enabled_gotv_lists = mysqli_fetch_array($get_api_enabled_gotv_lists);
                                $product_table_gotv_cable = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."' LIMIT 1"));
                                if($product_table_gotv_cable["status"] == 1){
                                	$product_discount_table_gotv_cable = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_gotv_lists["id"]."' && product_id='".$product_table_gotv_cable["id"]."'");
                                	if(mysqli_num_rows($product_discount_table_gotv_cable) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_gotv_cable)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="gotv-cable" value="'.$product_details["val_1"].'" hidden>Gotv '.ucwords(trim(str_replace(["-", "_"], " ", $product_details["val_1"]))).' N'.$product_details["val_2"].'</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //Showmax
                            $get_showmax_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$cable_type_table_name_arrays[$product_name_array[3]]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[3]."'"));
                            $get_api_enabled_showmax_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_showmax_status_details["api_id"]."' && api_type='cable' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_showmax_lists) == 1){
                                $get_api_enabled_showmax_lists = mysqli_fetch_array($get_api_enabled_showmax_lists);
                                $product_table_showmax_cable = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[3]."' LIMIT 1"));
                                if($product_table_showmax_cable["status"] == 1){
                                	$product_discount_table_showmax_cable = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_showmax_lists["id"]."' && product_id='".$product_table_showmax_cable["id"]."'");
                                	if(mysqli_num_rows($product_discount_table_showmax_cable) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_showmax_cable)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="showmax-cable" value="'.$product_details["val_1"].'" hidden>Showmax '.ucwords(trim(str_replace(["-", "_"], " ", $product_details["val_1"]))).' N'.$product_details["val_2"].'</option>';
                                        }
                                    	}
                                	}
                                }
                            }
                        }
                    ?>
                </select><br/>
                <?php }else{ ?>
                <div style="text-align: center; user-select: auto;" style="container">
                  <img alt="<?php echo $_SESSION['cable_provider']; ?>" id="<?php echo $_SESSION['cable_provider']; ?>-lg" src="/asset/<?php echo $_SESSION['cable_provider']; ?>.jpg" class="col-8 col-lg-5 "/><br/>
                  <div style="text-align: left;" class="container mb-1">
                      <span class="h5" style="user-select: auto;">Full-Name: <span class="h4 fw-bold"><?php echo strtoupper($_SESSION['cable_name']); ?></span></span><br/>
                      <span class="h5" style="user-select: none">IUC Number: <span class="h4 fw-bold"><?php echo $_SESSION['iuc_number']; ?></span></span><br/>
                      <span class="h5" style="user-select: auto;">Package: <span class="h4 fw-bold"><?php echo ucwords(trim(str_replace(["-", "_"], " ", strtoupper($_SESSION['cable_package'])))); ?></span></span><br/>
                  </div>
                </div><br/>
                <?php } ?>
                <?php if(!isset($_SESSION["cable_name"])){ ?>
                <button id="proceedBtn" name="verify-cable" type="button" style="pointer-events: none; user-select: auto;" class="btn btn-success mb-1 col-12" >
                    VERIFY CABLE
                </button><br>
                <?php }else{ ?>
                <button id="" name="buy-cable" type="submit" style="user-select: auto;" class="btn btn-success mb-1 col-12" >
                    BUY CABLE
                </button><br>
                <button id="" name="reset-cable" type="submit" style="user-select: auto;" class="btn btn-warning mb-1 col-12" >
                    RESET CABLE DETAILS
                </button><br>
                <?php } ?>
                <div style="text-align: center;" class="col-8">
                    <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                </div>
            </form>
        </div>
      </div>
    </section>

		<div class="d-none d-md-block">
		<?php include("../func/short-trans.php"); ?>
		</div>
	<?php include("../func/bc-footer.php"); ?>
	
</body>
</html>