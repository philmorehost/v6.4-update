<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-spadmin-config.php");
    
    $vendor_id_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($_GET["vendorID"]))));
    $select_vendor = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='$vendor_id_number'");
    if(mysqli_num_rows($select_vendor) > 0){
        $get_vendor_details = mysqli_fetch_array($select_vendor);
    }

    if(isset($_POST["update-profile"])){
        $first = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["first"]))));
        $last = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["last"]))));
        $address = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["address"]))));
        $email = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["email"]))));
        $phone = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["phone"]))));
        $bank_code = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["bank-code"])));
        $account_number = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["account-number"])));
        $bvn = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["bvn"]))));
        $nin = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["nin"]))));
        $unrefined_website_url = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["website-url"]))));
        $refined_website_url = trim(str_replace(["https","http",":/","/","www."," "],"",$unrefined_website_url));
        $website_url = $refined_website_url;
        
        if(!empty($first) && !empty($last) && !empty($address) && !empty($email) && !empty($phone) && !empty($website_url)){
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $check_vendor_details = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='".$vendor_id_number."'");
                if(mysqli_num_rows($check_vendor_details) == 1){
                    $get_vendor_details = mysqli_fetch_array($check_vendor_details);
                    $check_vendor_new_email = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE email='$email'");
                    $check_vendor_new_website = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='$website_url'");
                    $proceed_to_email_check = false;

                    if((mysqli_num_rows($check_vendor_new_website) == 1) || (mysqli_num_rows($check_vendor_new_website) < 1)){
                        if(mysqli_num_rows($check_vendor_new_website) == 1){
                            $get_new_vendor_details = mysqli_fetch_array($check_vendor_new_website);
                            if($get_new_vendor_details["id"] == $get_vendor_details["id"]){
                                mysqli_query($connection_server, "UPDATE sas_vendors SET firstname='$first', lastname='$last', home_address='$address', email='$email', phone_number='$phone' WHERE id='".$vendor_id_number."'");
                                $proceed_to_email_check = true;
                            }else{
                                //Website Address Taken By Another Vendor
                                $json_response_array = array("desc" => "Website Address Taken By Another Vendor");
                                $json_response_encode = json_encode($json_response_array,true);
                            }
                        }else{
                            if(mysqli_num_rows($check_vendor_new_website) < 1){
                                $proceed_to_email_check = true;
                            }
                        }
                    }else{
                        if(mysqli_num_rows($check_vendor_new_website) > 1){
                            //Duplicated Vendor Website Address, Contact Developer
                            $json_response_array = array("desc" => "Duplicated Vendor Website Address, Contact Developer");
                            $json_response_encode = json_encode($json_response_array,true);
                        }
                    }
                    if($proceed_to_email_check == true){
                        $email_check_verified = false;
                        if((mysqli_num_rows($check_vendor_new_email) == 1) || (mysqli_num_rows($check_vendor_new_email) < 1)){
                            if(mysqli_num_rows($check_vendor_new_email) == 1){
                                $get_new_vendor_details = mysqli_fetch_array($check_vendor_new_email);
                                if($get_new_vendor_details["id"] == $get_vendor_details["id"]){
                                    $email_check_verified = true;
                                }else{
                                    //Email Taken By Another Vendor
                                    $json_response_array = array("desc" => "Email Taken By Another Vendor");
                                    $json_response_encode = json_encode($json_response_array,true);
                                }
                            }else{
                                if(mysqli_num_rows($check_vendor_new_email) < 1){
                                    $email_check_verified = true;
                                }
                            }
                        }else{
                            if(mysqli_num_rows($check_vendor_new_email) > 1){
                                //Duplicated Vendor Email, Contact Developer
                                $json_response_array = array("desc" => "Duplicated Vendor Email, Contact Developer");
                                $json_response_encode = json_encode($json_response_array,true);
                            }
                        }
                    }

                    if($email_check_verified == true){
                    	if(!empty($bank_code) && is_numeric($bank_code) && (strlen($bank_code) >= 1)){
                    		$refined_bank_code = $bank_code;
                    	}else{
                    		$refined_bank_code = "";
                    	}
                    	
                    	if(!empty($account_number) && is_numeric($account_number) && (strlen($account_number) == 10)){
                    		$refined_account_number = $account_number;
                    	}else{
                    		$refined_account_number = "";
                    	}

                        if(!empty($bvn) && is_numeric($bvn) && (strlen($bvn) == 11)){
                    		$refined_bvn = $bvn;
                    	}else{
                    		$refined_bvn = "";
                    	}

                        if(!empty($nin) && is_numeric($nin) && (strlen($nin) == 11)){
                    		$refined_nin = $nin;
                    	}else{
                    		$refined_nin = "";
                    	}

                        mysqli_query($connection_server, "UPDATE sas_vendors SET firstname='$first', lastname='$last', home_address='$address', bank_code='$refined_bank_code', account_number='$refined_account_number', bvn='$refined_bvn', nin='$refined_nin', email='$email', phone_number='$phone', website_url='$website_url' WHERE id='".$vendor_id_number."'");
                        // Email Beginning
                        $log_template_encoded_text_array = array("{firstname}" => $first, "{lastname}" => $last, "{email}" => $email, "{phone}" => $phone, "{address}" => $address, "{website}" => $website_url);
                        $raw_log_template_subject = getSuperAdminEmailTemplate('vendor-account-update','subject');
                        $raw_log_template_body = getSuperAdminEmailTemplate('vendor-account-update','body');
                        foreach($log_template_encoded_text_array as $array_key => $array_val){
                        	$raw_log_template_subject = str_replace($array_key, $array_val, $raw_log_template_subject);
                        	$raw_log_template_body = str_replace($array_key, $array_val, $raw_log_template_body);
                        }
                        sendSuperAdminEmail($get_logged_admin_details["email"], $raw_log_template_subject, $raw_log_template_body);
                        // Email End
                        //Profile Information Updated Successfully
                        $json_response_array = array("desc" => "Profile Information Updated Successfully");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }else{
                    if(mysqli_num_rows($check_vendor_details) == 0){
                        //Vendor Not Exists
                        $json_response_array = array("desc" => "Vendor Not Exists");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                        if(mysqli_num_rows($check_vendor_details) > 1){
                            //Duplicated Details, Contact Admin
                            $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                            $json_response_encode = json_encode($json_response_array,true);
                        }
                    }
                }
            }else{
                //Invalid Email
                $json_response_array = array("desc" => "Invalid Email");
                $json_response_encode = json_encode($json_response_array,true);
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
                    if(empty($address)){
                        //Home Address Field Empty
                        $json_response_array = array("desc" => "Home Address Field Empty");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                        if(empty($email)){
                            //Email Field Empty
                            $json_response_array = array("desc" => "Email Field Empty");
                            $json_response_encode = json_encode($json_response_array,true);
                        }else{
                            if(empty($phone)){
                                //Phone Number Field Empty
                                $json_response_array = array("desc" => "Phone Number Field Empty");
                                $json_response_encode = json_encode($json_response_array,true);
                            }else{
                                if(empty($website_url)){
                                    //Website Url Field Empty
                                    $json_response_array = array("desc" => "Website Url Field Empty");
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
    
    if(isset($_POST["change-password"])){
        $new_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["new-pass"])));
        $con_new_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["con-new-pass"])));
        
        if(!empty($new_pass) && !empty($con_new_pass)){
            $check_vendor_details = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='".$vendor_id_number."'");
            if(mysqli_num_rows($check_vendor_details) == 1){
                $md5_new_pass = md5($new_pass);
                $md5_con_new_pass = md5($con_new_pass);
                
                if($md5_new_pass !== $get_logged_spadmin_details["password"]){
                    if($md5_new_pass == $md5_con_new_pass){
                        mysqli_query($connection_server, "UPDATE sas_vendors SET password='$md5_new_pass' WHERE id='".$vendor_id_number."'");
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
                if(mysqli_num_rows($check_vendor_details) == 0){
                    //Vendor Not Exists
                    $json_response_array = array("desc" => "Vendor Not Exists");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(mysqli_num_rows($check_vendor_details) > 1){
                    //Duplicated Details, Contact Admin
                        $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }
            }
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
    
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
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

</head>
<body>
    <?php include("../func/bc-spadmin-header.php"); ?>    
    
    <div class="pagetitle">
      <h1>EDIT VENDOR</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Edit Vendor</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
    
    <?php if(!empty($get_vendor_details['id'])){ ?>
        <div class="card info-card px-5 py-5">
            <form method="post" enctype="multipart/form-data" action="">
                <div style="text-align: center;" class="container">
                    <img src="<?php echo $web_http_host; ?>/asset/user-icon.png" class="col-2" style="pointer-events: none; user-select: auto; filter: invert(1);"/>
                </div><br/>
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">PERSONAL INFORMATION</span>
                </div><br/>
                <input style="text-align: center;" name="first" type="text" value="<?php echo $get_vendor_details['firstname']; ?>" placeholder="Firstname" pattern="[a-zA-Z ]{3,}" title="Firstname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="last" type="text" value="<?php echo $get_vendor_details['lastname']; ?>" placeholder="Lastname" pattern="[a-zA-Z ]{3,}" title="Lastname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="address" type="text" value="<?php echo $get_vendor_details['home_address']; ?>" placeholder="Home Address" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">CONTACT INFORMATION</span>
                </div><br/>
                <input style="text-align: center;" name="email" type="email" value="<?php echo $get_vendor_details['email']; ?>" placeholder="Email" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="phone" type="text" value="<?php echo $get_vendor_details['phone_number']; ?>" placeholder="Phone Number" pattern="[0-9]{11}" title="Phone number must be 11 digit long" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">WEBSITE URL</span>
                </div><br/>
                <input style="text-align: center;" name="website-url" type="url" value="https://<?php echo $get_vendor_details['website_url']; ?>" placeholder="Website Url" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">VENDOR BANK INFORMATION</span>
                </div><br/>
                <select style="text-align: center;" id="" name="bank-code" onchange="" class="form-control mb-1" />
                    <option value="" default hidden selected>Choose Bank</option>
                    <?php

                        //Bank Lists
                        $get_monnify_access_token_2 = json_decode(getSuperAdminMonnifyAccessToken(), true);
                            
                        if($get_monnify_access_token_2["status"] == "success"){
                            $get_monnify_bank_lists = json_decode(getMonnifyBanks($get_monnify_access_token_2["token"]), true);
                            
                            if($get_monnify_bank_lists["status"] == "success"){
                                foreach($get_monnify_bank_lists["banks"] as $bank_json){
                                    $decode_bank_json = $bank_json;
                                    if($decode_bank_json["code"] == $get_vendor_details["bank_code"]){
                                        echo '<option value="'.$decode_bank_json["code"].'" selected>'.$decode_bank_json["name"].'</option>';
                                    }else{
                                        echo '<option value="'.$decode_bank_json["code"].'">'.$decode_bank_json["name"].'</option>';
                                    }
                                }
                            }
                        }
                        
                    ?>
                </select><br/>
                <input style="text-align: center;" name="account-number" type="text" value="<?php echo $get_vendor_details['account_number']; ?>" placeholder="Account Number (optional)" pattern="[0-9]{10}" title="Account number must be 10 digit long" class="form-control mb-1" /><br/>
                
                <div style="text-align: center;" class="container">
                	<span id="user-status-span" class="h5" style="user-select: auto;">VENDOR VERIFICATION</span>
                </div><br/>
                <input style="text-align: center;" name="bvn" type="text" value="<?php echo $get_vendor_details['bvn']; ?>" placeholder="BVN (optional)" pattern="[0-9]{11}" title="BVN must be 11 digit long" class="form-control mb-1" /><br/>
                <input style="text-align: center;" name="nin" type="text" value="<?php echo $get_vendor_details['nin']; ?>" placeholder="NIN (optional)" pattern="[0-9]{11}" title="NIN must be 11 digit long" class="form-control mb-1" /><br/>
                <button name="update-profile" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                    UPDATE PROFILE
                </button><br>
            </form>
        </div><br/>
        
        <div class="card info-card px-5 py-5">
            <span style="user-select: auto;" class="text-dark h4 fw-bold">CHANGE PASSWORD</span><br>
            <form method="post" action="">
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">NEW PASSWORD</span>
                </div><br/>
                <input style="text-align: center;" name="new-pass" type="password" value="" placeholder="New Password" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">CONFIRM NEW PASSWORD</span>
                </div><br/>
                <input style="text-align: center;" name="con-new-pass" type="password" value="" placeholder="Confirm New Password" class="form-control mb-1" required/><br/>
                <button name="change-password" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                    CHANGE PASSWORD
                </button><br>
                
            </form>
        </div>

    <?php }else{ ?>
        <div class="container d-flex flex-column align-items-center justify-items-center justify-content-center">
            <img src="<?php echo $web_http_host; ?>/asset/ooops.gif" class="col-4" style="user-select: auto;"/><br/>
            <div style="text-align: center;" class="container">
                <span id="user-status-span" class="h3" style="user-select: auto;">Ooops</span><br/>
                <span id="user-status-span" class="h5" style="user-select: auto;">Vendor Account Not Exists</span>
            </div><br/>
        </div>
    <?php } ?>
    </div>
  </section>
    <?php include("../func/bc-spadmin-footer.php"); ?>
    
</body>
</html>