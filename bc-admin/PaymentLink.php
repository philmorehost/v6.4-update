<?php session_start();
    include("../func/bc-admin-config.php");
	
	$kyc_verification_array = array("bvn", "nin");
    $payment_link_gateway_array = array("monnify", "flutterwave", "paystack", "payvessel", "beewave", "fincra", "vpay");
	$payment_link_gateway_webhook_array = array("monnify" => $web_http_host . "/users-monnify.php", "flutterwave" => $web_http_host . "/users-flutterwave.php", "paystack" => $web_http_host . "/users-paystack.php", "payvessel" => $web_http_host . "/users-payvessel.php", "beewave" => $web_http_host . "/users-beewave.php", "fincra" => $web_http_host . "/users-fincra.php", "vpay" => $web_http_host . "/users-vpay.php");
	
    if(isset($_POST["update-gateway-details"])){
        $gateway_name = $_POST["gateway-name"];
        $public_key = $_POST["public-key"];
        $secret_key = $_POST["secret-key"];
        $encrypt_key = $_POST["encrypt-key"];
        $payment_percent = $_POST["payment-percent"];
        $gateway_array_list = $payment_link_gateway_array;

        if((count($gateway_name) > 0) && (count($public_key) > 0) && (count($secret_key) > 0) && (count($public_key) == count($secret_key))){
            foreach($gateway_name as $index => $name){
                $each_gateway_name = mysqli_real_escape_string($connection_server, trim(strip_tags($gateway_name[$index])));
                $each_public_key = mysqli_real_escape_string($connection_server, trim(strip_tags($public_key[$index])));
                $each_secret_key = mysqli_real_escape_string($connection_server, trim(strip_tags($secret_key[$index])));
                $each_encrypt_key = mysqli_real_escape_string($connection_server, trim(strip_tags($encrypt_key[$index])));
                $each_payment_percent = mysqli_real_escape_string($connection_server, trim(strip_tags($payment_percent[$index])));
                
                //$each_gateway_status_unrefined = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/","",trim(strip_tags( $_POST["gateway-status-".$each_gateway_name] ))));
                if(isset($_POST["gateway-status-".$each_gateway_name])){
                    $each_gateway_status = "1";
                }else{
                	$each_gateway_status = "2";
                }

                if(in_array($each_gateway_name, $gateway_array_list)){
                    $get_payment_link_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_payment_link_gateways WHERE vendor_id='".$get_logged_admin_details["id"]."' && gateway_name='$each_gateway_name'");
                    if(mysqli_num_rows($get_payment_link_gateway_details) == 1){
                        mysqli_query($connection_server, "UPDATE sas_payment_link_gateways SET public_key='$each_public_key', secret_key='$each_secret_key', encrypt_key='$each_encrypt_key', percentage='$each_payment_percent', status='$each_gateway_status' WHERE vendor_id='".$get_logged_admin_details["id"]."' && gateway_name='$each_gateway_name'");
                        //Payment Gateway Information Updated Successfully
                        $json_response_array = array("desc" => "Payment Gateway Information Updated Successfully");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                        if(mysqli_num_rows($get_payment_link_gateway_details) == 0){
                            mysqli_query($connection_server, "INSERT INTO sas_payment_link_gateways (vendor_id, gateway_name, public_key, secret_key, encrypt_key, percentage, status) VALUES ('".$get_logged_admin_details["id"]."', '$each_gateway_name', '$each_public_key', '$each_secret_key', '$each_encrypt_key', '$each_payment_percent', '$each_gateway_status')");
                            //Payment Gateway Information Created Successfully
                            $json_response_array = array("desc" => "Payment Gateway Information Created Successfully");
                            $json_response_encode = json_encode($json_response_array,true);
                        }else{
                            if(mysqli_num_rows($get_payment_link_gateway_details) > 1){
                                //Duplicated Details, Contact Admin
                                $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                                $json_response_encode = json_encode($json_response_array,true);
                            }
                        }
                    }
                }else{
                    //cannot show the error once
                }
            }
        }else{
            if((count($gateway_name) < 1)){
                //Gateway Field Not Available
                $json_response_array = array("desc" => "Gateway Field Not Available");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if((count($public_key) < 1)){
                    //Public Key Field Not Available
                    $json_response_array = array("desc" => "Public Key Field Not Available");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if((count($secret_key) < 1)){
                        //Secret Key Field Not Available
                        $json_response_array = array("desc" => "Secret Key Field Not Available");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                    	if((count($encrypt_key) < 1)){
                    		//Encrypt Key Field Not Available
                    		$json_response_array = array("desc" => "Encrypt Key Field Not Available");
                    		$json_response_encode = json_encode($json_response_array,true);
                    	}else{
                    		if((count($payment_percent) < 1)){
                    			//Payment Percentage Field Not Available
                    			$json_response_array = array("desc" => "Payment Percentage Field Not Available");
                	    		$json_response_encode = json_encode($json_response_array,true);
                    		}else{
                    			if((count($public_key) !== count($secret_key)) || (count($secret_key) !== count($encrypt_key)) || (count($encrypt_key) !== count($payment_percent))){
                        			//Incomplete Field
                            		$json_response_array = array("desc" => "Incomplete Field");
                           		 	$json_response_encode = json_encode($json_response_array,true);
                    			}
                    		}
                    	}
                    }
                }
            }
        }

        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }

?>
<!DOCTYPE html>
<head>
    <title>Payment Gateway | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
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
      <h1>PAYMENT LINK GATEWAYS</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Payment Link Gateways</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">

        <div style="text-align: center;" class="card info-card px-5 py-5">
            <span style="user-select: auto;" class="text-dark h4">PAYMENT LINK GATEWAYS</span><br>
            <form method="post" action="">
                <?php
                    foreach($payment_link_gateway_array as $gateway_name){
                        $get_gateway_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_payment_link_gateways WHERE vendor_id='".$get_logged_admin_details["id"]."' && gateway_name='$gateway_name'"));
                        if(in_array($get_gateway_details["status"], array(1, 2))){
                            if($get_gateway_details["status"] == 1){
                                $gateway_checkbox_value = "checked";
                                $gateway_status_value = "1";
                                $gateway_status = '<span style="color: ;" class="color-4 text-bold-600">Enabled</span>';
                            }else{
                                $gateway_checkbox_value = "";
                                $gateway_status_value = "2";
                                $gateway_status = '<span style="color: ;" class="color-4 text-bold-600">Disabled</span>';
                            }
                        }else{
                            $gateway_checkbox_value = "";
                            $gateway_status_value = "2";
                            $gateway_status = '<span style="color: ;" class="color-4 text-bold-600">Invalid Status Code</span>';
                        }
                        echo    '<span style="user-select: auto;" class="fw-bold h4 text-primary">'.strtoupper($gateway_name).'</span><br>
                                <input style="text-align: center;" name="gateway-name[]" value="'.$gateway_name.'" hidden required/>
                                <div style="text-align: center;" class="container h6 mt-5 mb-0">
                                    <span id="admin-status-span" class="h6" style="user-select: auto;">'.strtoupper("Public Key").'</span>
                                </div><br/>
                                <input style="text-align: center;" name="public-key[]" type="text" placeholder="'.ucwords($gateway_name).' Public Key" value="'.$get_gateway_details["public_key"].'" class="form-control mb-1" /><br/>
                                <div style="text-align: center;" class="container h6 mt-3">
                                    <span id="admin-status-span" class="h6" style="user-select: auto;">'.strtoupper("Secret Key").'</span>
                                </div><br/>
                                <input style="text-align: center;" name="secret-key[]" type="text" placeholder="'.ucwords($gateway_name).' Secret Key" value="'.$get_gateway_details["secret_key"].'" class="form-control mb-1" /><br/>
                                <div style="text-align: center;" class="container h6 mt-3">
                                	<span id="admin-status-span" class="h6" style="user-select: auto;">'.strtoupper("Encrypt Key").'</span>
                                </div><br/>
                                <input style="text-align: center;" name="encrypt-key[]" type="text" placeholder="'.ucwords($gateway_name).' Encrypt Key" value="'.$get_gateway_details["encrypt_key"].'" class="form-control mb-1" /><br/>
                                <div style="text-align: center;" class="container h6 mt-3">
                                	<span id="admin-status-span" class="h6" style="user-select: auto;">'.strtoupper("Payment Discount (%)").'</span>
                                </div><br/>
                                <input style="text-align: center;" name="payment-percent[]" type="number" step="0.001" min="1" max="100" placeholder="'.ucwords($gateway_name).' Percentage Discount" value="'.$get_gateway_details["percentage"].'" class="form-control mb-1" /><br/>
                                <div style="text-align: center;" class="container h6 mt-3">
                                	<span id="admin-status-span" class="h6" style="user-select: auto;">WEBHOOK</span>
                                </div><br/>
                                <input style="text-align: center;" name="" type="text" placeholder="'.ucwords($gateway_name).' Webhook" value="'.$payment_link_gateway_webhook_array[$gateway_name].'" class="form-control mb-1" readonly/><br/>
                                <div style="text-align: right;" class="color-2 bg-3 m-inline-block-dp s-inline-block-dp m-width-60 s-width-45 m-margin-bm-2 s-margin-bm-2">
                                    <input '.$gateway_checkbox_value.' id="gateway-switch-'.$gateway_name.'" name="gateway-status-'.$gateway_name.'" value="'.$gateway_status_value.'" type="checkbox" class="form-check-input mb-1" />
                                    <label for="gateway-switch-'.$gateway_name.'" style="user-select: auto;" class="h6">'.$gateway_status.'</label>
                                </div><br/>';
                    }
                ?>
                    
                <button name="update-gateway-details" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                    UPDATE GATEWAY DETAILS
                </button><br>
            </form>
        </div>
      </div>
    </section>
        
    <?php include("../func/bc-admin-footer.php"); ?>
    
</body>
</html>