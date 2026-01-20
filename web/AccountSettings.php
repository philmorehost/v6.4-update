<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-config.php");
        
    if(isset($_POST["update-profile"])){
        $first = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["first"]))));
    	$last = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["last"]))));
        $other = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["other"]))));
    	$quest = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["quest"])));
    	$answer = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["answer"]))));
    	$address = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["address"]))));
        $phone = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["phone"]))));
        
        if(!empty($first) && !empty($last) && !empty($quest) && is_numeric($quest) && !empty($answer) && !empty($address) && !empty($phone)){
            $check_user_details = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$get_logged_user_details["username"]."'");
            if(mysqli_num_rows($check_user_details) == 1){
                if((strlen($answer) >= 3) && (strlen($answer) <= 20)){
                    if(is_numeric($phone) && (strlen($phone) == 11)){
                    	mysqli_query($connection_server, "UPDATE sas_users SET security_quest='$quest', security_answer='$answer', firstname='$first', lastname='$last', othername='$other', home_address='$address', phone_number='$phone' WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$get_logged_user_details["username"]."'");
                		// Email Beginning
               			$log_template_encoded_text_array = array("{firstname}" => $first, "{lastname}" => $last, "{email}" => $get_logged_user_details["email"], "{phone}" => $get_logged_user_details["phone_number"], "{address}" => $address, "{security_answer}" => $answer);
               			$raw_log_template_subject = getUserEmailTemplate('user-account-update','subject');
               			$raw_log_template_body = getUserEmailTemplate('user-account-update','body');
               			foreach($log_template_encoded_text_array as $array_key => $array_val){
              				$raw_log_template_subject = str_replace($array_key, $array_val, $raw_log_template_subject);
               				$raw_log_template_body = str_replace($array_key, $array_val, $raw_log_template_body);
               			}
               			sendVendorEmail($get_logged_user_details["email"], $raw_log_template_subject, $raw_log_template_body);
               			// Email End
               			
               			//Profile Information Updated Successfully
               			$json_response_array = array("desc" => "Profile Information Updated Successfully");
               			$json_response_encode = json_encode($json_response_array,true);
                	}else{
                		//Phone number should be 11 digit long
                		$json_response_array = array("desc" => "Phone number should be 11 digit long");
                		$json_response_encode = json_encode($json_response_array,true);
                	}
                }else{
                    //Security Answer Must Be Between 3-20 Charaters Without Special Charaters
                    $json_response_array = array("desc" => "Security Answer Must Be Between 3-20 Charaters Without Special Charaters");
                    $json_response_encode = json_encode($json_response_array,true);
                }
            }else{
                if(mysqli_num_rows($check_user_details) == 0){
                    //User Not Exists
                    $json_response_array = array("desc" => "User Not Exists");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(mysqli_num_rows($check_user_details) > 1){
                        //Duplicated Details, Contact Admin
                        $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }
            }
        }else{
            if(empty($first)){
                //Firstname Field Empty
                $json_response_array = array("desc" => "Firstname Field Empty");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(empty($last)){
                    //Lastname Field Empty
                    $json_response_array = array("desc" => "Lastname Field Empty");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(empty($quest)){
                        //Security Question Field Empty
                        $json_response_array = array("desc" => "Security Question Field Empty");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                        if(!is_numeric($quest)){
                            //Security Question Cannot Be String
                            $json_response_array = array("desc" => "Security Question Cannot Be String");
                            $json_response_encode = json_encode($json_response_array,true);
                        }else{
                            if(empty($answer)){
                                //Security Answer Field Empty
                                $json_response_array = array("desc" => "Security Answer Field Empty");
                                $json_response_encode = json_encode($json_response_array,true);
                            }else{
                                if(empty($address)){
                                    //Home Address Field Empty
                                    $json_response_array = array("desc" => "Home Address Field Empty");
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
    
    if(isset($_POST["update-verification"])){
        $bank_code = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["bank-code"])));
        $account_number = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["account-number"])));
        $bvn_nin = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["bvnnin"])));
        $verification_type = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["verification-type"])));
        $verification_type_array = array(1 => "bvn", 2 => "nin");
        if(!empty($bank_code) && is_numeric($bank_code) && (strlen($bank_code) >= 1) && !empty($account_number) && is_numeric($account_number) && (strlen($account_number) == 10) && !empty($bvn_nin) && is_numeric($bvn_nin) && (strlen($bvn_nin) == 11) && in_array($verification_type, array_keys($verification_type_array))){
            $check_user_details = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$get_logged_user_details["username"]."'");
            if(mysqli_num_rows($check_user_details) == 1){
                if($verification_type == 1){
                	$amount = 30;
                }else{
                	if($verification_type == 2){
                		$amount = 100;
                	}
                }
                $discounted_amount = $amount;
                $type_alternative = ucwords("bvn/nin verification");
                $reference = substr(str_shuffle("12345678901234567890"), 0, 15);
                $description = "BVN/NIN Verification charges";
                $status = 3;
                $purchase_method = "WEB";
                if(!empty(userBalance(1)) && is_numeric(userBalance(1)) && (userBalance(1) > 0) && (userBalance(1) >= $amount)){
                    $debit_user = chargeUser("debit", "BVN/NIN Verification", $type_alternative, $reference, "", $amount, $discounted_amount, $description, $purchase_method, $_SERVER["HTTP_HOST"], $status);
                    if($debit_user === "success"){                         
                        $get_monnify_access_token = json_decode(getUserMonnifyAccessToken(), true);
                        if($get_monnify_access_token["status"] == "success"){
                            $user_detail_verified = false;
                            $get_monnify_nuban_verification = json_decode(makeMonnifyRequest("get", $get_monnify_access_token["token"], "api/v1/disbursements/account/validate?accountNumber=".$account_number."&bankCode=".$bank_code, ""), true);
                            if($get_monnify_nuban_verification["status"] == "success"){
                                if($verification_type == 1){
                                    $bvn_nin_account_array = array("bankCode" => $bank_code, "accountNumber" => $account_number, "bvn" => $bvn_nin);
                                    $get_monnify_bvn_account_verification = json_decode(makeMonnifyRequest("post", $get_monnify_access_token["token"], "api/v1/vas/bvn-account-match", $bvn_nin_account_array), true);
                                    if($get_monnify_bvn_account_verification["status"] == "success"){
                                        mysqli_query($connection_server, "UPDATE sas_users SET bank_code='$bank_code', account_number='$account_number', bvn='$bvn_nin' WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$get_logged_user_details["username"]."'");
                                        $user_detail_verified = true;
                                        alterTransaction($reference, "status", "1");
                                        //User BVN Verification Information Updated Successfully
                                        $json_response_array = array("desc" => "User BVN Verification Information Updated Successfully");
                                        $json_response_encode = json_encode($json_response_array,true);
                                    }else{
                                        $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                        chargeUser("credit", "BVN/NIN Verification", "Refund", $reference_2, "", $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $purchase_method, $_SERVER["HTTP_HOST"], "1");
                                        //BVN And Account Number Not Linked
                                        $json_response_array = array("desc" => "BVN And Account Number Not Linked");
                                        $json_response_encode = json_encode($json_response_array,true);
                                    }
                                }

                                if($verification_type == 2){
                                    $bvn_nin_account_array = array("nin" => $bvn_nin);
                                    $get_monnify_nin_account_verification = json_decode(makeMonnifyRequest("post", $get_monnify_access_token["token"], "api/v1/vas/nin-details", $bvn_nin_account_array), true);
                                    if($get_monnify_nin_account_verification["status"] == "success"){
                                        $monnify_nin_response = json_decode($get_monnify_nin_account_verification["json_result"], true);
                                        $retrieved_monnify_phone_number = $monnify_nin_response["responseBody"]["mobileNumber"];
                                        $strlen_retrieved_monnify_phone_number = strlen($retrieved_monnify_phone_number);
                                        $refined_retrieved_monnify_phone_number = "0".substr($retrieved_monnify_phone_number, ($strlen_retrieved_monnify_phone_number - 10), $strlen_retrieved_monnify_phone_number);
                                        mysqli_query($connection_server, "UPDATE sas_users SET phone_number='$refined_retrieved_monnify_phone_number', bank_code='$bank_code', account_number='$account_number', nin='$bvn_nin' WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$get_logged_user_details["username"]."'");
                                        $user_detail_verified = true;
                                        alterTransaction($reference, "status", "1");
                                        //User NIN Verification Information Updated Successfully
                                        $json_response_array = array("desc" => "User NIN Verification Information Updated Successfully");
                                        $json_response_encode = json_encode($json_response_array,true);
                                    }else{
                                        $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                        chargeUser("credit", "BVN/NIN Verification", "Refund", $reference_2, "", $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $purchase_method, $_SERVER["HTTP_HOST"], "1");
                                        //NIN Cannot Be Verified
                                        $json_response_array = array("desc" => "NIN Cannot Be Verified");
                                        $json_response_encode = json_encode($json_response_array,true);
                                    }
                                }

                                /*if($user_detail_verified == true){
                                    //Check If Monnify Virtual Account Exists
                                    $user_monnify_account_reference = md5($_SERVER["HTTP_HOST"]."-".$get_logged_user_details["vendor_id"]."-".$get_logged_user_details["username"]);
                                    $get_monnify_reserve_account = json_decode(makeMonnifyRequest("get", $get_monnify_access_token["token"], "api/v2/bank-transfer/reserved-accounts/".$user_monnify_account_reference, ""), true);
                                    if($get_monnify_reserve_account["status"] == "failed"){
                                        $select_monnify_gateway_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_payment_gateways WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && gateway_name='monnify' LIMIT 1");
                                        $monnify_create_reserve_account_array = array("accountReference" => $user_monnify_account_reference, "accountName" => $get_logged_user_details["firstname"]." ".$get_logged_user_details["lastname"]." ".$get_logged_user_details["othername"], "currencyCode" => "NGN", "contractCode" => $select_monnify_gateway_details["encrypt_key"], "customerEmail" => $get_logged_user_details["email"], $bvn_nin_monnify_account_creation, "getAllAvailableBanks" => false, "preferredBanks" => ["232", "035", "50515", "058"]);
                                        makeMonnifyRequest("post", $get_monnify_access_token["token"], "api/v2/bank-transfer/reserved-accounts", $monnify_create_reserve_account_array);
                                        
                                    }
                                }*/
                            }else{
                                $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                chargeUser("credit", "BVN/NIN Verification", "Refund", $reference_2, "", $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $purchase_method, $_SERVER["HTTP_HOST"], "1");
                                //Invalid Account Number
                                $json_response_array = array("desc" => "Invalid Account Number");
                                $json_response_encode = json_encode($json_response_array,true);
                            }
                        }else{
                            if($get_monnify_access_token["status"] == "failed"){
                                //Unable To Generate Token , Try Again Later
                                $json_response_array = array("desc" => $get_monnify_access_token["message"]);
                                $json_response_encode = json_encode($json_response_array,true);
                            }
                        }
                    }else{
                        $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                        chargeUser("credit", "BVN/NIN Verification", "Refund", $reference_2, "", $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $purchase_method, $_SERVER["HTTP_HOST"], "1");
                        //Unable to proceed with charges
                        $json_response_array = array("status" => "failed", "desc" => "Unable to proceed with charges");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }else{
                    //Error: Insufficient Funds
                    $json_response_array = array("desc" => "Error: Insufficient Funds");
                    $json_response_encode = json_encode($json_response_array,true);
                }   
            }else{
                if(mysqli_num_rows($check_user_details) == 0){
                    //User Not Exists
                    $json_response_array = array("desc" => "User Not Exists");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(mysqli_num_rows($check_user_details) > 1){
                        //Duplicated Details, Contact Admin
                        $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }
            }
        }else{
            if(empty($bvn_nin)){
                //BVN/NIN Field Empty
                $json_response_array = array("desc" => strtoupper($verification_type_array[$verification_type])." Field Empty");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(!is_numeric($bvn_nin)){
                    //Non-numeric BVN/NIN String
                    $json_response_array = array("desc" => "Non-numeric ".strtoupper($verification_type_array[$verification_type])." String");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(strlen($bvn_nin) == 11){
                        //BVN/NIN must be 11 digit long
                        $json_response_array = array("desc" => strtoupper($verification_type_array[$verification_type])." must be 11 digit long");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                    	if(empty($bank_code)){
                    		//Bank Code Field Empty
                    		$json_response_array = array("desc" => "Bank Code Field Empty");
                    		$json_response_encode = json_encode($json_response_array,true);
                    	}else{
                    		if(!is_numeric($bank_code)){
                    			//Non-numeric Bank Code String
                    			$json_response_array = array("desc" => "Non-numeric Bank Code String");
                    			$json_response_encode = json_encode($json_response_array,true);
                    		}else{
                    			if(strlen($bank_code) < 1){
                    				//Bank Code must be atleast 1 digit long
                    				$json_response_array = array("desc" => "Bank Code must be atleast 1 digit long");
                    				$json_response_encode = json_encode($json_response_array,true);
                    			}else{
                    				if(empty($account_number)){
                    					//Account Number Field Empty
                    					$json_response_array = array("desc" => "Account Number Field Empty");
                    					$json_response_ennumber = json_encode($json_response_array,true);
                    				}else{
                    					if(!is_numeric($account_number)){
                    						//Non-numeric Account Number String
                    						$json_response_array = array("desc" => "Non-numeric Account Number String");
                    						$json_response_ennumber = json_encode($json_response_array,true);
                    					}else{
                    						if(strlen($account_number) < 1){
                    							//Account Number must be atleast 1 digit long
                    							$json_response_array = array("desc" => "Account Number must be atleast 1 digit long");
                    							$json_response_ennumber = json_encode($json_response_array,true);
                    						}else{
                                                if(!in_array($verification_type, array_keys($verification_type_array))){
                                                    //Unknown Verification Type
                                                    $json_response_array = array("desc" => "Unknown Verification Type");
                                                    $json_response_ennumber = json_encode($json_response_array,true);
                                                }
                    						}
                    					}
                    				}
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
    
    if(isset($_POST["change-password"])){
        $old_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["old-pass"])));
    	$new_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["new-pass"])));
        $con_new_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["con-new-pass"])));
        
        if(!empty($old_pass) && !empty($new_pass) && !empty($con_new_pass)){
            $check_user_details = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$get_logged_user_details["username"]."'");
            if(mysqli_num_rows($check_user_details) == 1){
                $md5_old_pass = md5($old_pass);
                $md5_new_pass = md5($new_pass);
                $md5_con_new_pass = md5($con_new_pass);
                
                if($md5_old_pass == $get_logged_user_details["password"]){
                    if($md5_new_pass !== $get_logged_admin_details["password"]){
                        if($md5_new_pass == $md5_con_new_pass){
                            mysqli_query($connection_server, "UPDATE sas_users SET password='$md5_new_pass' WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$get_logged_user_details["username"]."'");
                            // Email Beginning
                            $log_template_encoded_text_array = array("{firstname}" => $get_logged_user_details["firstname"], "{lastname}" => $get_logged_user_details["lastname"]);
                            $raw_log_template_subject = getUserEmailTemplate('user-pass-update','subject');
                            $raw_log_template_body = getUserEmailTemplate('user-pass-update','body');
                            foreach($log_template_encoded_text_array as $array_key => $array_val){
                            	$raw_log_template_subject = str_replace($array_key, $array_val, $raw_log_template_subject);
                            	$raw_log_template_body = str_replace($array_key, $array_val, $raw_log_template_body);
                            }
                            sendVendorEmail($get_logged_user_details["email"], $raw_log_template_subject, $raw_log_template_body);
                            // Email End
                            
                            //Account Password Updated Successfully
                            $json_response_array = array("desc" => "Account Password Updated Successfully");
                            $json_response_encode = json_encode($json_response_array,true);
                        }else{
                            //New & Confirm Password Not Match
                            $json_response_array = array("desc" => "New & Confirm Password Not Match");
                            $json_response_encode = json_encode($json_response_array,true);
                        }
                    }else{
                        //New & Old Password Must Be Different
                        $json_response_array = array("desc" => "New & Old Password Must Be Different");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }else{
                    //Incorrect Old Password
                    $json_response_array = array("desc" => "Incorrect Old Password");
                    $json_response_encode = json_encode($json_response_array,true);
                }
            }else{
                if(mysqli_num_rows($check_user_details) == 0){
                    //User Not Exists
                    $json_response_array = array("desc" => "User Not Exists");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(mysqli_num_rows($check_user_details) > 1){
                    //Duplicated Details, Contact Admin
                        $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }
            }
        }else{
            if(empty($old_pass)){
                //Old Password Field Empty
                $json_response_array = array("desc" => "Old Password Field Empty");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(empty($new_pass)){
                    //New Password Field Empty
                    $json_response_array = array("desc" => "New Password Field Empty");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(empty($con_new_pass)){
                        //Confirm New Password Field Empty
                        $json_response_array = array("desc" => "Confirm New Password Field Empty");
                        $json_response_encode = json_encode($json_response_array,true);
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
    <title>Account Settings | <?php echo $get_all_site_details["site_title"]; ?></title>
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
	
		<div class="pagetitle">
      <h1>USER ACCOUNT SETTINGS</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Account Settings</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">

    
    <div class="card info-card px-5 py-5">
            <form method="post" action="">
                <div style="text-align: center;" class="container">
                	<img src="<?php echo $web_http_host; ?>/asset/user-icon.png" class="col-2" style="pointer-events: none; user-select: auto; filter: invert(1);"/>
                </div><br/>
            	<div style="text-align: center;" class="container">
            		<span id="user-status-span" class="fw-bold h4" style="user-select: auto;">PERSONAL INFORMATION</span>
            	</div><br/>
                <input style="text-align: center;" name="first" type="text" value="<?php echo $get_logged_user_details['firstname']; ?>" placeholder="Firstname" pattern="[a-zA-Z ]{3,}" title="Firstname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="last" type="text" value="<?php echo $get_logged_user_details['lastname']; ?>" placeholder="Lastname" pattern="[a-zA-Z ]{3,}" title="Lastname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="other" type="text" value="<?php echo $get_logged_user_details['othername']; ?>" placeholder="Othername" pattern="[a-zA-Z ]{3,}" title="Othername must be atleast 3 letters long" class="form-control mb-1" /><br/>
                <input style="text-align: center;" name="address" type="text" value="<?php echo $get_logged_user_details['home_address']; ?>" placeholder="Home Address" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                	<span id="user-status-span" class="h5" style="user-select: auto;">SECURITY QUESTION & ANSWER</span>
                </div><br/>
                <select style="text-align: center;" id="" name="quest" onchange="" class="form-control mb-1" required/>
                	<option value="" default hidden selected>Choose Security Question</option>
                	<?php
                		//Security Quests
                		$get_security_quest_details = mysqli_query($connection_server, "SELECT * FROM sas_security_quests");
                		if(mysqli_num_rows($get_security_quest_details) >= 1){
                			while($security_details = mysqli_fetch_assoc($get_security_quest_details)){
                				if($security_details["id"] == $get_logged_user_details['security_quest']){
                					echo '<option value="'.$security_details["id"].'" selected>'.$security_details["quest"].'</option>';
                				}else{
                					echo '<option value="'.$security_details["id"].'">'.$security_details["quest"].'</option>';
                				}
                			}
                		}
                	?>
                </select><br/>
                <input style="text-align: center;" id="" name="answer" onkeyup="" type="text" value="<?php echo $get_logged_user_details['security_answer']; ?>" placeholder="Security Answer e.g Dog" pattern="[0-9a-zA-Z ]{3,20}" title="Security Answer Must Be Between 3-20 Charaters Without Special Charaters" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                	<span id="user-status-span" class="h5" style="user-select: auto;">CONTACT INFORMATION</span>
                </div><br/>
                <input style="text-align: center;" name="phone" type="text" inputmode="numeric" pattern="[0-9]*" value="<?php echo $get_logged_user_details['phone_number']; ?>" placeholder="Phone Number" title="Phone number must be 11 digit long" class="form-control mb-1" required/><br/>
                <button name="update-profile" type="submit" style="user-select: auto;" class="btn btn-success mb-1 col-12" >
                    UPDATE PROFILE
                </button><br>
                <div style="text-align: center;" class="col-12">
                	<span id="user-status-span" class="h5" style="user-select: auto;">NB: Contact Admin For Further Assistance!!!</span>
                </div><br/>
            </form>
        </div>
        
        <div style="text-align: center;" class="card info-card px-5 py-5">
            <span style="user-select: auto;" class="fw-bold h4">CUSTOMER KYC</span><br>
            <form method="post" action="">
                
                <div style="text-align: center;" class="container">
                	<span id="user-status-span" class="h5" style="user-select: auto;">BANK INFORMATION</span>
                </div><br/>
                
                <select style="text-align: center;" id="" name="bank-code" onchange="" class="form-control mb-1" required/>
                	<option value="" default hidden selected>Choose Bank</option>
                	<?php

                        //Bank Lists
                        $get_monnify_access_token_2 = json_decode(getUserMonnifyAccessToken(), true);
                            
                        if($get_monnify_access_token_2["status"] == "success"){
                            $get_monnify_bank_lists = json_decode(getMonnifyBanks($get_monnify_access_token_2["token"]), true);
                            
                            if($get_monnify_bank_lists["status"] == "success"){
                                foreach($get_monnify_bank_lists["banks"] as $bank_json){
                                    $decode_bank_json = $bank_json;
                                    if($decode_bank_json["code"] == $get_logged_user_details["bank_code"]){
                                        echo '<option value="'.$decode_bank_json["code"].'" selected>'.$decode_bank_json["name"].'</option>';
                                    }else{
                                        echo '<option value="'.$decode_bank_json["code"].'">'.$decode_bank_json["name"].'</option>';
                                    }
                                }
                            }
                        }
                		
                	?>
                </select><br/>
				<input style="text-align: center;" name="account-number" type="text" inputmode="numeric" pattern="[0-9]*" value="<?php echo $get_logged_user_details['account_number']; ?>" placeholder="Account Number *" title="Account number must be 10 digit long" class="form-control mb-1" required/><br/>
				
                <div style="text-align: center;" class="container">
                	<span id="user-status-span" class="fw-bold h4" style="user-select: auto;">BVN/NIN INFORMATION</span>
                </div><br/>
                <select style="text-align: center;" id="" name="verification-type" onchange="" class="form-control mb-1" required/>
                	<option value="" default hidden selected>Choose Verification Type</option>
                	<option value="1">BVN</option>
                	<option value="2">NIN</option>	
                </select><br/>
                <input style="text-align: center;" name="bvnnin" type="text" inputmode="numeric" pattern="[0-9]*" value="" placeholder="BVN or NIN *" title="BVN must be 11 digit long" class="form-control mb-1" required/><br/>
                
                <button disabled name="update-verification" type="submit" style="user-select: auto;" class="btn btn-success mb-1 col-12" >
                    UPDATE DETAILS
                </button><br>
                <div style="text-align: center;" class="container">
         					<span id="user-status-span" class="h5" style="user-select: auto;">NB: Updated BVN & NIN will not be shown for your privacy security and note that certain charge applied for every successful BVN (₦30) and NIN (₦100) verification respectively</span>
         				</div><br/>
            </form>
        </div><br/>
        
        <div style="text-align: center;" class="card info-card px-5 py-5">
            <span style="user-select: auto;" class="h4">CHANGE PASSWORD</span><br>
            <form method="post" action="">
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">OLD PASSWORD</span>
                </div><br/>
                <input style="text-align: center;" name="old-pass" type="password" value="" placeholder="Old Password" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">NEW PASSWORD</span>
                </div><br/>
                <input style="text-align: center;" name="new-pass" type="password" value="" placeholder="New Password" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="con-new-pass" type="password" value="" placeholder="Confirm New Password" class="form-control mb-1" required/><br/>
                <button name="change-password" type="submit" style="user-select: auto;" class="btn btn-success mb-1 col-12" >
                    CHANGE PASSWORD
                </button><br>
                
            </form>
        </div>
      </div>
    </section>
	<?php include("../func/bc-footer.php"); ?>
	
</body>
</html>