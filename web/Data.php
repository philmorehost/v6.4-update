<?php session_start();
    include("../func/bc-config.php");
    //alterTransaction("6176424889","status","2");
    if(isset($_POST["buy-data"])){
        $purchase_method = "web";
		include_once("func/data.php");
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        //echo '<script>alert("'.$json_response_decode["status"].': '.$json_response_decode["desc"].'");</script>';
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
    
?>
<!DOCTYPE html>
<head>
    <title>Shared Data, SME Data, Direct Data, Corporate Data | <?php echo $get_all_site_details["site_title"]; ?></title>
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
      <h1>BUY DATA</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Buy Data</li>
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
                <div style="text-align: center; user-select: auto;" class="container">
                    <img alt="Airtel" id="airtel-lg" product-status="enabled" src="/asset/airtel.png" onclick="tickDataCarrier('airtel');" class="col-2 rounded-5 border m-1 "/>
                    <img alt="MTN" id="mtn-lg" product-status="enabled" src="/asset/mtn.png" onclick="tickDataCarrier('mtn');" class="col-2 rounded-5 border m-1"/>
                    <img alt="Glo" id="glo-lg" product-status="enabled" src="/asset/glo.png" onclick="tickDataCarrier('glo');" class="col-2 rounded-5 border m-1"/>
                    <img alt="9mobile" id="9mobile-lg" product-status="enabled" src="/asset/9mobile.png" onclick="tickDataCarrier('9mobile');" class="col-2 rounded-5 border m-1"/>
                </div><br/>
                <input id="isprovider" name="isp" type="text" placeholder="Isp" hidden readonly required/>
                <input style="text-align: center;" id="phone-number" name="phone-number" onkeyup="tickDataCarrier(); resetDataQuantity();" type="text" inputmode="numeric" pattern="[0-9]*" value="" placeholder="Phone number e.g 08124232128" title="Charater must be an 11 digit" class="form-control mb-1" required/><br/>
                <select style="text-align: center;" id="internet-data-type" name="type" onchange="tickDataCarrier(); resetDataQuantity();" class="form-control mb-1" required/>
                    <option value="" default hidden selected>Data Type</option>
                	<option value="shared-data">Shared Data</option>
                	<option value="sme-data">SME Data</option>
                	<option value="cg-data">Corporate Gifting Data</option>
                	<option value="dd-data">Direct Data</option>
                </select><br/>
                <select style="text-align: center;" id="product-amount" name="quantity" onchange="tickDataCarrier();" class="form-control mb-1" required/>
                	<option product-category="" value="" default hidden selected>Data Quantity</option>
                    <?php
                        $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                        if($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true){
                            $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                            $product_name_array = array("mtn", "airtel", "glo", "9mobile");
							$data_type_table_name_arrays = array("shared-data"=>"sas_shared_data_status", "sme-data"=>"sas_sme_data_status", "cg-data"=>"sas_cg_data_status", "dd-data"=>"sas_dd_data_status");
							
                            //MTN SHARED
                            $get_mtn_shared_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["shared-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."'"));
                            $get_api_enabled_shared_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_mtn_shared_status_details["api_id"]."' && api_type='shared-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_shared_data_lists) == 1){
                                $get_api_enabled_shared_data_lists = mysqli_fetch_array($get_api_enabled_shared_data_lists);
                                $product_table_mtn_shared_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."' LIMIT 1"));
                                if($product_table_mtn_shared_data["status"] == 1){
                                    $product_discount_table_mtn_shared_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_shared_data_lists["id"]."' && product_id='".$product_table_mtn_shared_data["id"]."' AND status = 1");
                                    if(mysqli_num_rows($product_discount_table_mtn_shared_data) > 0){
                                        while($product_details = mysqli_fetch_assoc($product_discount_table_mtn_shared_data)){
                                          if($product_details["val_2"] > 0){
                                            echo '<option product-category="mtn-shared-data" value="'.$product_details["val_1"].'" hidden>MTN SHARED '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                          }
                                        }
                                    }
                                }
                            }

                            //MTN SME
                            $get_mtn_sme_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["sme-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."'"));
                            $get_api_enabled_sme_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_mtn_sme_status_details["api_id"]."' && api_type='sme-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_sme_data_lists) == 1){
                                $get_api_enabled_sme_data_lists = mysqli_fetch_array($get_api_enabled_sme_data_lists);
                                $product_table_mtn_sme_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."' LIMIT 1"));
                                if($product_table_mtn_sme_data["status"] == 1){
					$product_discount_table_mtn_sme_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_sme_data_lists["id"]."' && product_id='".$product_table_mtn_sme_data["id"]."' AND status = 1");
                                	if(mysqli_num_rows($product_discount_table_mtn_sme_data) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_mtn_sme_data)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="mtn-sme-data" value="'.$product_details["val_1"].'" hidden>MTN SME '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //MTN CG
                            $get_mtn_cg_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["cg-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."'"));
                            $get_api_enabled_cg_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_mtn_cg_status_details["api_id"]."' && api_type='cg-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_cg_data_lists) == 1){
                                $get_api_enabled_cg_data_lists = mysqli_fetch_array($get_api_enabled_cg_data_lists);
                                $product_table_mtn_cg_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."' LIMIT 1"));
                                if($product_table_mtn_cg_data["status"] == 1){
					$product_discount_table_mtn_cg_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_cg_data_lists["id"]."' && product_id='".$product_table_mtn_cg_data["id"]."' AND status = 1");
                                	if(mysqli_num_rows($product_discount_table_mtn_cg_data) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_mtn_cg_data)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="mtn-cg-data" value="'.$product_details["val_1"].'" hidden>MTN CG '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //MTN DD
                            $get_mtn_dd_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["dd-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."'"));
                            $get_api_enabled_dd_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_mtn_dd_status_details["api_id"]."' && api_type='dd-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_dd_data_lists) == 1){
                                $get_api_enabled_dd_data_lists = mysqli_fetch_array($get_api_enabled_dd_data_lists);
                                $product_table_mtn_dd_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."' LIMIT 1"));
                                if($product_table_mtn_dd_data["status"] == 1){
					$product_discount_table_mtn_dd_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_dd_data_lists["id"]."' && product_id='".$product_table_mtn_dd_data["id"]."' AND status = 1");
                                	if(mysqli_num_rows($product_discount_table_mtn_dd_data) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_mtn_dd_data)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="mtn-dd-data" value="'.$product_details["val_1"].'" hidden>MTN DIRECT '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //AIRTEL SHARED
                            $get_airtel_shared_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["shared-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."'"));
                            $get_api_enabled_shared_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_airtel_shared_status_details["api_id"]."' && api_type='shared-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_shared_data_lists) == 1){
                                $get_api_enabled_shared_data_lists = mysqli_fetch_array($get_api_enabled_shared_data_lists);
                                $product_table_airtel_shared_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."' LIMIT 1"));
                                if($product_table_airtel_shared_data["status"] == 1){
                                    $product_discount_table_airtel_shared_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_shared_data_lists["id"]."' && product_id='".$product_table_airtel_shared_data["id"]."' AND status = 1");
                                    if(mysqli_num_rows($product_discount_table_airtel_shared_data) > 0){
                                        while($product_details = mysqli_fetch_assoc($product_discount_table_airtel_shared_data)){
                                          if($product_details["val_2"] > 0){
                                            echo '<option product-category="airtel-shared-data" value="'.$product_details["val_1"].'" hidden>AIRTEL SHARED '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                          }
                                        }
                                    }
                                }
                            }

                            //AIRTEL SME
                            $get_airtel_sme_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["sme-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."'"));
                            $get_api_enabled_sme_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_airtel_sme_status_details["api_id"]."' && api_type='sme-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_sme_data_lists) == 1){
                                $get_api_enabled_sme_data_lists = mysqli_fetch_array($get_api_enabled_sme_data_lists);
                                $product_table_airtel_sme_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."' LIMIT 1"));
                                if($product_table_airtel_sme_data["status"] == 1){
					$product_discount_table_airtel_sme_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_sme_data_lists["id"]."' && product_id='".$product_table_airtel_sme_data["id"]."' AND status = 1");
                                	if(mysqli_num_rows($product_discount_table_airtel_sme_data) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_airtel_sme_data)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="airtel-sme-data" value="'.$product_details["val_1"].'" hidden>AIRTEL SME '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //AIRTEL CG
                            $get_airtel_cg_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["cg-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."'"));
                            $get_api_enabled_cg_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_airtel_cg_status_details["api_id"]."' && api_type='cg-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_cg_data_lists) == 1){
                                $get_api_enabled_cg_data_lists = mysqli_fetch_array($get_api_enabled_cg_data_lists);
                                $product_table_airtel_cg_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."' LIMIT 1"));
                                if($product_table_airtel_cg_data["status"] == 1){
					$product_discount_table_airtel_cg_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_cg_data_lists["id"]."' && product_id='".$product_table_airtel_cg_data["id"]."' AND status = 1");
                                	if(mysqli_num_rows($product_discount_table_airtel_cg_data) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_airtel_cg_data)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="airtel-cg-data" value="'.$product_details["val_1"].'" hidden>AIRTEL CG '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //AIRTEL DD
                            $get_airtel_dd_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["dd-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."'"));
                            $get_api_enabled_dd_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_airtel_dd_status_details["api_id"]."' && api_type='dd-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_dd_data_lists) == 1){
                                $get_api_enabled_dd_data_lists = mysqli_fetch_array($get_api_enabled_dd_data_lists);
                                $product_table_airtel_dd_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."' LIMIT 1"));
                                if($product_table_airtel_dd_data["status"] == 1){
					$product_discount_table_airtel_dd_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_dd_data_lists["id"]."' && product_id='".$product_table_airtel_dd_data["id"]."' AND status = 1");
                                	if(mysqli_num_rows($product_discount_table_airtel_dd_data) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_airtel_dd_data)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="airtel-dd-data" value="'.$product_details["val_1"].'" hidden>AIRTEL DIRECT '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //GLO SHARED
                            $get_glo_shared_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["shared-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."'"));
                            $get_api_enabled_shared_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_glo_shared_status_details["api_id"]."' && api_type='shared-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_shared_data_lists) == 1){
                                $get_api_enabled_shared_data_lists = mysqli_fetch_array($get_api_enabled_shared_data_lists);
                                $product_table_glo_shared_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."' LIMIT 1"));
                                if($product_table_glo_shared_data["status"] == 1){
                                    $product_discount_table_glo_shared_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_shared_data_lists["id"]."' && product_id='".$product_table_glo_shared_data["id"]."' AND status = 1");
                                    if(mysqli_num_rows($product_discount_table_glo_shared_data) > 0){
                                        while($product_details = mysqli_fetch_assoc($product_discount_table_glo_shared_data)){
                                          if($product_details["val_2"] > 0){
                                            echo '<option product-category="glo-shared-data" value="'.$product_details["val_1"].'" hidden>GLO SHARED '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                          }
                                        }
                                    }
                                }
                            }

                            //GLO SME
                            $get_glo_sme_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["sme-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."'"));
                            $get_api_enabled_sme_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_glo_sme_status_details["api_id"]."' && api_type='sme-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_sme_data_lists) == 1){
                                $get_api_enabled_sme_data_lists = mysqli_fetch_array($get_api_enabled_sme_data_lists);
                                $product_table_glo_sme_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."' LIMIT 1"));
                                if($product_table_glo_sme_data["status"] == 1){
					$product_discount_table_glo_sme_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_sme_data_lists["id"]."' && product_id='".$product_table_glo_sme_data["id"]."' AND status = 1");
                                	if(mysqli_num_rows($product_discount_table_glo_sme_data) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_glo_sme_data)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="glo-sme-data" value="'.$product_details["val_1"].'" hidden>GLO SME '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //GLO CG
                            $get_glo_cg_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["cg-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."'"));
                            $get_api_enabled_cg_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_glo_cg_status_details["api_id"]."' && api_type='cg-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_cg_data_lists) == 1){
                                $get_api_enabled_cg_data_lists = mysqli_fetch_array($get_api_enabled_cg_data_lists);
                                $product_table_glo_cg_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."' LIMIT 1"));
                                if($product_table_glo_cg_data["status"] == 1){
					$product_discount_table_glo_cg_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_cg_data_lists["id"]."' && product_id='".$product_table_glo_cg_data["id"]."' AND status = 1");
                                	if(mysqli_num_rows($product_discount_table_glo_cg_data) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_glo_cg_data)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="glo-cg-data" value="'.$product_details["val_1"].'" hidden>GLO CG '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //GLO DD
                            $get_glo_dd_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["dd-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."'"));
                            $get_api_enabled_dd_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_glo_dd_status_details["api_id"]."' && api_type='dd-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_dd_data_lists) == 1){
                                $get_api_enabled_dd_data_lists = mysqli_fetch_array($get_api_enabled_dd_data_lists);
                                $product_table_glo_dd_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."' LIMIT 1"));
                                if($product_table_glo_dd_data["status"] == 1){
					$product_discount_table_glo_dd_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_dd_data_lists["id"]."' && product_id='".$product_table_glo_dd_data["id"]."' AND status = 1");
                                	if(mysqli_num_rows($product_discount_table_glo_dd_data) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_glo_dd_data)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="glo-dd-data" value="'.$product_details["val_1"].'" hidden>GLO DIRECT '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //9MOBILE SHARED
                            $get_9mobile_shared_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["shared-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[3]."'"));
                            $get_api_enabled_shared_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_9mobile_shared_status_details["api_id"]."' && api_type='shared-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_shared_data_lists) == 1){
                                $get_api_enabled_shared_data_lists = mysqli_fetch_array($get_api_enabled_shared_data_lists);
                                $product_table_9mobile_shared_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[3]."' LIMIT 1"));
                                if($product_table_9mobile_shared_data["status"] == 1){
                                    $product_discount_table_9mobile_shared_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_shared_data_lists["id"]."' && product_id='".$product_table_9mobile_shared_data["id"]."' AND status = 1");
                                    if(mysqli_num_rows($product_discount_table_9mobile_shared_data) > 0){
                                        while($product_details = mysqli_fetch_assoc($product_discount_table_9mobile_shared_data)){
                                          if($product_details["val_2"] > 0){
                                            echo '<option product-category="9mobile-shared-data" value="'.$product_details["val_1"].'" hidden>9MOBILE SHARED '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                          }
                                        }
                                    }
                                }
                            }

                            //9MOBILE SME
                            $get_9mobile_sme_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["sme-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[3]."'"));
                            $get_api_enabled_sme_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_9mobile_sme_status_details["api_id"]."' && api_type='sme-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_sme_data_lists) == 1){
                                $get_api_enabled_sme_data_lists = mysqli_fetch_array($get_api_enabled_sme_data_lists);
                                $product_table_9mobile_sme_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[3]."' LIMIT 1"));
                                if($product_table_9mobile_sme_data["status"] == 1){
					$product_discount_table_9mobile_sme_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_sme_data_lists["id"]."' && product_id='".$product_table_9mobile_sme_data["id"]."' AND status = 1");
                                	if(mysqli_num_rows($product_discount_table_9mobile_sme_data) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_9mobile_sme_data)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="9mobile-sme-data" value="'.$product_details["val_1"].'" hidden>9MOBILE SME '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //9MOBILE CG
                            $get_9mobile_cg_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["cg-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[3]."'"));
                            $get_api_enabled_cg_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_9mobile_cg_status_details["api_id"]."' && api_type='cg-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_cg_data_lists) == 1){
                                $get_api_enabled_cg_data_lists = mysqli_fetch_array($get_api_enabled_cg_data_lists);
                                $product_table_9mobile_cg_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[3]."' LIMIT 1"));
                                if($product_table_9mobile_cg_data["status"] == 1){
					$product_discount_table_9mobile_cg_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_cg_data_lists["id"]."' && product_id='".$product_table_9mobile_cg_data["id"]."' AND status = 1");
                                	if(mysqli_num_rows($product_discount_table_9mobile_cg_data) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_9mobile_cg_data)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="9mobile-cg-data" value="'.$product_details["val_1"].'" hidden>9MOBILE CG '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //9MOBILE DD
                            $get_9mobile_dd_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["dd-data"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[3]."'"));
                            $get_api_enabled_dd_data_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_9mobile_dd_status_details["api_id"]."' && api_type='dd-data' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_dd_data_lists) == 1){
                                $get_api_enabled_dd_data_lists = mysqli_fetch_array($get_api_enabled_dd_data_lists);
                                $product_table_9mobile_dd_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[3]."' LIMIT 1"));
                                if($product_table_9mobile_dd_data["status"] == 1){
					$product_discount_table_9mobile_dd_data = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_dd_data_lists["id"]."' && product_id='".$product_table_9mobile_dd_data["id"]."' AND status = 1");
                                	if(mysqli_num_rows($product_discount_table_9mobile_dd_data) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_9mobile_dd_data)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="9mobile-dd-data" value="'.$product_details["val_1"].'" hidden>9MOBILE DIRECT '.str_replace("_"," ",$product_details["val_1"]).' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                        }
                    ?>
                </select><br/>
                <div style="text-align: left;" id="phone-bypass-div" class="container mb-1">
                    <input id="phone-bypass" onclick="tickDataCarrier('airtel');" type="checkbox" class="form-check-input mb-1" />
                    <div class="col-12">
                        <label for="phone-bypass" class="h5" style="user-select: auto;">
                            Bypass Phone Verification
                        </label>
                    </div>
                </div><br>
                <button id="proceedBtn" name="buy-data" type="button" style="pointer-events: none; user-select: auto;" class="btn btn-success mb-1 col-12" >
                    BUY DATA
                </button><br>
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