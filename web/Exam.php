<?php session_start();
    include("../func/bc-config.php");
        
    if(isset($_POST["buy-exam"])){
        $purchase_method = "web";
		include_once("func/exam.php");
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
    
?>
<!DOCTYPE html>
<head>
    <title>Exam PIN | <?php echo $get_all_site_details["site_title"]; ?></title>
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
      <h1>BUY EXAM PIN</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Exam PIN</li>
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
                    <img alt="Waec" id="waec-lg" product-status="enabled" src="/asset/waec.jpg" onclick="tickExamCarrier('waec'); resetExamQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="Neco" id="neco-lg" product-status="enabled" src="/asset/neco.jpg" onclick="tickExamCarrier('neco'); resetExamQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="Nabteb" id="nabteb-lg" product-status="enabled" src="/asset/nabteb.jpg" onclick="tickExamCarrier('nabteb'); resetExamQuantity();" class="col-2 rounded-5 border m-1 "/>
                	<img alt="Jamb" id="jamb-lg" product-status="enabled" src="/asset/jamb.jpg" onclick="tickExamCarrier('jamb'); resetExamQuantity();" class="col-2 rounded-5 border m-1 "/>
                </div><br/>
                <input id="examname" name="epp" type="text" placeholder="Exam Name" hidden readonly required/>
                <select style="text-align: center;" id="product-amount" name="quantity" onchange="pickExamQty();" class="form-control mb-1" required/>
                	<option product-category="" value="" default hidden selected>Exam Quantity</option>
                    <?php
                        $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                        if($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true){
                            $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                            $product_name_array = array("waec", "neco", "nabteb", "jamb");
							$exam_type_table_name_arrays = array("waec"=>"sas_exam_status", "neco"=>"sas_exam_status", "nabteb"=>"sas_exam_status", "jamb"=>"sas_exam_status");
							
							//Waec
                            $get_waec_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$exam_type_table_name_arrays[$product_name_array[0]]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."'"));
                            $get_api_enabled_waec_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_waec_status_details["api_id"]."' && api_type='exam' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_waec_lists) == 1){
                            	$get_api_enabled_waec_lists = mysqli_fetch_array($get_api_enabled_waec_lists);
                                $product_table_waec_exam = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."' LIMIT 1"));
                                if($product_table_waec_exam["status"] == 1){
                                	$product_discount_table_waec_exam = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_waec_lists["id"]."' && product_id='".$product_table_waec_exam["id"]."'");
                                	if(mysqli_num_rows($product_discount_table_waec_exam) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_waec_exam)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="waec-exam" value="'.$product_details["val_1"].'" hidden>WAEC '.ucwords(trim(str_replace(["-", "_"], " ", $product_details["val_1"]))).' N'.$product_details["val_2"].'</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //Neco
                            $get_neco_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$exam_type_table_name_arrays[$product_name_array[1]]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."'"));
                            $get_api_enabled_neco_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_neco_status_details["api_id"]."' && api_type='exam' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_neco_lists) == 1){
                                $get_api_enabled_neco_lists = mysqli_fetch_array($get_api_enabled_neco_lists);
                                $product_table_neco_exam = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."' LIMIT 1"));
                                if($product_table_neco_exam["status"] == 1){
                                	$product_discount_table_neco_exam = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_neco_lists["id"]."' && product_id='".$product_table_neco_exam["id"]."'");
                                	if(mysqli_num_rows($product_discount_table_neco_exam) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_neco_exam)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="neco-exam" value="'.$product_details["val_1"].'" hidden>NECO '.ucwords(trim(str_replace(["-", "_"], " ", $product_details["val_1"]))).' N'.$product_details["val_2"].'</option>';
                                        }
                                    	}
                                	}
                                }
                            }

                            //Nabteb
                            $get_nabteb_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$exam_type_table_name_arrays[$product_name_array[2]]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."'"));
                            $get_api_enabled_nabteb_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_nabteb_status_details["api_id"]."' && api_type='exam' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_nabteb_lists) == 1){
                                $get_api_enabled_nabteb_lists = mysqli_fetch_array($get_api_enabled_nabteb_lists);
                                $product_table_nabteb_exam = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."' LIMIT 1"));
                                if($product_table_nabteb_exam["status"] == 1){
                                	$product_discount_table_nabteb_exam = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_nabteb_lists["id"]."' && product_id='".$product_table_nabteb_exam["id"]."'");
                                	if(mysqli_num_rows($product_discount_table_nabteb_exam) > 0){
                                    	while($product_details = mysqli_fetch_assoc($product_discount_table_nabteb_exam)){
                                        if($product_details["val_2"] > 0){
                                        	echo '<option product-category="nabteb-exam" value="'.$product_details["val_1"].'" hidden>NABTEB '.ucwords(trim(str_replace(["-", "_"], " ", $product_details["val_1"]))).' N'.$product_details["val_2"].'</option>';
                                        }
                                    	}
                                	}
                                }
                            }
							
							//Jamb
							$get_jamb_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$exam_type_table_name_arrays[$product_name_array[3]]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[3]."'"));
							$get_api_enabled_jamb_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_jamb_status_details["api_id"]."' && api_type='exam' && status='1' LIMIT 1");
							if(mysqli_num_rows($get_api_enabled_jamb_lists) == 1){
								$get_api_enabled_jamb_lists = mysqli_fetch_array($get_api_enabled_jamb_lists);
								$product_table_jamb_exam = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[3]."' LIMIT 1"));
								if($product_table_jamb_exam["status"] == 1){
									$product_discount_table_jamb_exam = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_jamb_lists["id"]."' && product_id='".$product_table_jamb_exam["id"]."'");
									if(mysqli_num_rows($product_discount_table_jamb_exam) > 0){
										while($product_details = mysqli_fetch_assoc($product_discount_table_jamb_exam)){
                      if($product_details["val_2"] > 0){
											  echo '<option product-category="jamb-exam" value="'.$product_details["val_1"].'" hidden>JAMB '.ucwords(trim(str_replace(["-", "_"], " ", $product_details["val_1"]))).' N'.$product_details["val_2"].'</option>';
                      }
										}
									}
								}
							}
							
                        }
                    ?>
                </select><br/>
                <button id="proceedBtn" name="buy-exam" type="button" style="pointer-events: none; user-select: auto;" class="btn btn-success mb-1 col-12" >
                    BUY PIN
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