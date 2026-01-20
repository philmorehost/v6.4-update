<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-admin-config.php");
    
    $user_id_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($_GET["userID"]))));
    $select_user = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='$user_id_number'");
    if(mysqli_num_rows($select_user) > 0){
        $get_user_details = mysqli_fetch_array($select_user);
    }

    if(isset($_POST["create-profile"])){
        $user = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["user"]))));
    	$first = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["first"]))));
    	$last = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["last"]))));
        $other = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["other"]))));
    	$quest = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["quest"])));
    	$answer = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["answer"]))));
    	$address = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["address"]))));
        $email = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["email"]))));
        $phone = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["phone"]))));
    	//$bank_code = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["bank-code"])));
        //$account_number = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["account-number"])));
        //$bvn = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["bvn"]))));
    	//$nin = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["nin"]))));
    	
        if(!empty($user) && !empty($first) && !empty($last) && !empty($quest) && is_numeric($quest) && !empty($answer) && !empty($address) && !empty($email) && !empty($phone)){
            $check_user_details = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_logged_admin_details["id"]."' && username='$user'");
            
            if(!empty($referral)){
                $check_user_referral_details = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_logged_admin_details["id"]."' && username='$referral'");
                if(mysqli_num_rows($check_user_referral_details) == 1){
                    $get_referral_details = mysqli_fetch_array($check_user_referral_details);
                    $referral_edited = $get_referral_details["id"];
                }else{
                    $referral_edited = "";
                }
            }else{
                $referral_edited = "";
            }
            if(mysqli_num_rows($check_user_details) == 0){
                if(!filter_var($user, FILTER_VALIDATE_EMAIL)){
                    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                        if(is_numeric($phone) && (strlen($phone) == 11)){
                            $md5_pass = md5($pass);
                            /*if(!empty($bank_code) && is_numeric($bank_code) && (strlen($bank_code) >= 1)){
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
                            }*/
                            $api_key = substr(str_shuffle("abdcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ12345678901234567890"), 0, 50);
                            $last_login = date('Y-m-d H:i:s.u');
                            mysqli_query($connection_server, "INSERT INTO sas_users (vendor_id, email, username, password, phone_number, balance, firstname, lastname, othername, security_quest, security_answer, home_address, referral_id, account_level, api_key, last_login, api_status, status) VALUES ('".$get_logged_admin_details["id"]."', '$email', '$user', '$md5_pass', '$phone', '0', '$first', '$last', '$other', '$quest', '$answer', '$address', '$referral_edited', '1', '$api_key', '$last_login', '2', '1')");
                            // Email Beginning
                            $reg_template_encoded_text_array = array("{firstname}" => $first, "{lastname}" => $last, "{username}" => $user, "{address}" => $address, "{email}" => $email, "{phone}" => $phone);
                            $raw_reg_template_subject = getUserEmailTemplate('user-reg','subject');
                            $raw_reg_template_body = getUserEmailTemplate('user-reg','body');
                            foreach($reg_template_encoded_text_array as $array_key => $array_val){
                            	$raw_reg_template_subject = str_replace($array_key, $array_val, $raw_reg_template_subject);
                            	$raw_reg_template_body = str_replace($array_key, $array_val, $raw_reg_template_body);
                            }
                            sendVendorEmail($email, $raw_reg_template_subject, $raw_reg_template_body);
                            // Email End
                            //Congratulations, Account Has Been Created Successfully. You can now proceed to login
                            $json_response_array = array("desc" => "Congratulations, User account Has Been Created Successfully. User can now proceed to login");
                            $json_response_encode = json_encode($json_response_array,true);
                        }else{
                            //Phone number should be 11 digit long
                            $json_response_array = array("desc" => "Phone number should be 11 digit long");
                            $json_response_encode = json_encode($json_response_array,true);
                        }
                    }else{
                        //Invalid Email
                        $json_response_array = array("desc" => "Invalid Email");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }else{
                    //Username Cannot Be Email
                    $json_response_array = array("desc" => "Username Cannot Be Email");
                    $json_response_encode = json_encode($json_response_array,true);
                }
            }else{
                if(mysqli_num_rows($check_user_details) == 1){
                    //User Already Exists
                    $json_response_array = array("desc" => "User Already Exists");
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
            if(empty($user)){
                //Username Field Empty
                $json_response_array = array("desc" => "Username Field Empty");
                $json_response_encode = json_encode($json_response_array,true);
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
        
?>
<!DOCTYPE html>
<head>
    <title>Create User | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
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
      <h1>CREATE USER</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Create User</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
        <div class="card info-card px-5 py-5">
    		<form method="post" enctype="multipart/form-data" action="">
                <div style="text-align: center;" class="container">
                	<img src="<?php echo $web_http_host; ?>/asset/user-icon.png" class="col-2" style="pointer-events: none; user-select: auto; filter: invert(1);"/>
                </div><br/>
                <div style="text-align: center;" class="container">
            		<span id="user-status-span" class="fw-bold h5" style="user-select: auto;">PERSONAL INFORMATION</span>
            	</div><br/>
                <input style="text-align: center;" name="user" type="text" value="" placeholder="Username" pattern="[a-zA-Z]{6,}" title="Username must be atleast 6 lowercase letters long (No Space)" class="form-control mb-1" autocomplete="off" required/><br/>
                <input style="text-align: center;" name="first" type="text" value="" placeholder="Firstname" pattern="[a-zA-Z ]{3,}" title="Firstname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="last" type="text" value="" placeholder="Lastname" pattern="[a-zA-Z ]{3,}" title="Lastname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="other" type="text" value="" placeholder="Othername" pattern="[a-zA-Z ]{3,}" title="Othername must be atleast 3 letters long" class="form-control mb-1" /><br/>
                <input style="text-align: center;" name="address" type="text" value="" placeholder="Home Address" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                	<span id="user-status-span" class="fw-bold h5" style="user-select: auto;">SECURITY QUESTION & ANSWER</span>
                </div><br/>
                <select style="text-align: center;" id="" name="quest" onchange="" class="form-control mb-1" required/>
                	<option value="" default hidden selected>Choose Security Question</option>
                	<?php
                		//Security Quests
                		$get_security_quest_details = mysqli_query($connection_server, "SELECT * FROM sas_security_quests");
                		if(mysqli_num_rows($get_security_quest_details) >= 1){
                			while($security_details = mysqli_fetch_assoc($get_security_quest_details)){
                				echo '<option value="'.$security_details["id"].'">'.$security_details["quest"].'</option>';
                			}
                		}
                	?>
                </select><br/>
                <input style="text-align: center;" id="" name="answer" onkeyup="" type="text" value="" placeholder="Security Answer e.g Dog" pattern="[0-9a-zA-Z ]{3,20}" title="Security Answer Must Be Between 3-20 Charaters Without Special Charaters" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
            		<span id="user-status-span" class="fw-bold h5" style="user-select: auto;">CONTACT INFORMATION</span>
            	</div><br/>
                <input style="text-align: center;" name="email" type="email" value="" placeholder="Email" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="phone" type="text" value="" placeholder="Phone Number" pattern="[0-9]{11}" title="Phone number must be 11 digit long" class="form-control mb-1" required/><br/>
                
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="fw-bold h5" style="user-select: auto;">NEW PASSWORD</span>
                </div><br/>
                <input style="text-align: center;" name="new-pass" type="password" value="" placeholder="New Password" class="form-control mb-1" required/><br/>
                
                <button name="create-profile" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                    CREATE PROFILE
                </button><br>
    		</form>
    	</div><br/>

      </div>
    </section>
    <?php include("../func/bc-admin-footer.php"); ?>
    
</body>
</html>