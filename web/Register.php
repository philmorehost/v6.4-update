<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-config.php");
    if(isset($get_logged_user_details["username"]) && !empty($get_logged_user_details["username"]) && ($get_logged_user_details["status"] == 1)){
    	header("Location: /web/Dashboard.php");
    }
    
    $get_recaptcha_key = mysqli_query_and_fetch_array($connection_server,"SELECT * FROM sas_recaptcha_setting WHERE vendor_id='".$select_vendor_table["id"]."' LIMIT 1");
    
    if(isset($_POST["register"])){
    	$user = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["user"]))));
    	$pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["pass"])));
        $first = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["first"]))));
    	$last = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["last"]))));
        $other = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["other"]))));
    	$email = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["email"]))));
        $phone = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["phone"]))));
    	$address = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["address"]))));
        $referral = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_GET["referral"]))));
        
        if(isset($_POST['g-recaptcha-response'])){
            $captcha = $_POST['g-recaptcha-response'];
        
            if(!$captcha){
                //Please Check The Captcha Form!
                $json_response_array = array("desc" => "Please Check The Captcha Form!");
                $json_response_encode = json_encode($json_response_array,true);
                
            }
            
            $responseCaptcha = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$get_recaptcha_key["secret_key"]."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
            if($responseCaptcha['success'] == true){
                if(!empty($user) && !empty($pass) && !empty($first) && !empty($last) && !empty($email) && !empty($phone) && !empty($address)){
                    $get_vendor_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_vendors WHERE website_url='".$_SERVER["HTTP_HOST"]."' LIMIT 1");
                    $check_user_details = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_vendor_details["id"]."' && username='$user'");
                    
                    if(!empty($referral)){
                        $check_user_referral_details = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_vendor_details["id"]."' && username='$referral'");
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
                                    $api_key = substr(str_shuffle("abdcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ12345678901234567890"), 0, 50);
                                    $last_login = date('Y-m-d H:i:s.u');
                                    mysqli_query($connection_server, "INSERT INTO sas_users (vendor_id, email, username, password, phone_number, balance, firstname, lastname, othername, home_address, referral_id, account_level, api_key, last_login, api_status, status) VALUES ('".$get_vendor_details["id"]."', '$email', '$user', '$md5_pass', '$phone', '0', '$first', '$last', '$other', '$address', '$referral_edited', '1', '$api_key', '$last_login', '2', '1')");
                                    
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
                                    $json_response_array = array("desc" => "Congratulations, Account Has Been Created Successfully. You can now proceed to login");
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
                        if(empty($pass)){
                            //Password Field Empty
                            $json_response_array = array("desc" => "Password Field Empty");
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
                                    if(empty($email)){
                                        //Email Field Empty
                                        $json_response_array = array("desc" => "Email Field Empty");
                                        $json_response_encode = json_encode($json_response_array,true);
                                    }else{
                                    	if(empty($phone)){
                                    		//Phone Number Field Empty
                                    		$json_response_array = array("desc" => "Home Address Field Empty");
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
                }
            }else{
                //Invalid Verification, Try Again!
                $json_response_array = array("desc" => "Invalid Verification, Try Again!");
                $json_response_encode = json_encode($json_response_array,true);
            }
		}else{
			//Bots Registration Not Allowed!
			$json_response_array = array("desc" => "Bots Registration Not Allowed!");
			$json_response_encode = json_encode($json_response_array,true);
		}
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
    
    $get_referral = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_GET["referral"]))));
    $get_referral_vendor_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_vendors WHERE website_url='".$_SERVER["HTTP_HOST"]."' LIMIT 1");
    $check_user_referral_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_referral_vendor_details["id"]."' && username='$get_referral'");
    
?>
<!DOCTYPE html>
<head>
    <title>Register | <?php echo $get_all_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="<?php echo $get_all_site_details["primary_color"] ?? "#198754"; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        
          <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

    <script src="https://merchant.beewave.ng/checkout.min.js"></script> 
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
<style>
  body{
    background-image: url('../asset/web-bg-image.jpg');
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    background-attachment: fixed;
  }
</style>
</head>
<body>	
    <div style="text-align: center;" class="container h-100 d-flex flex-column flex-lg-row">
        
        <div class="col-10 col-lg-6 justify-content-center align-self-center d-inline-block mt-5 mt-lg-0">
          <img src="<?php echo $web_http_host; ?>/uploaded-image/<?php echo str_replace(['.',':'],'-',$_SERVER['HTTP_HOST']).'_'; ?>logo.png" style="user-select: auto; object-fit: contain; object-position: center;" class="col-6 rounded-circle"/><br/>
        </div>
        
        <div class="col-10 col-lg-6 h-100 justify-content-center align-self-center d-inline-block">
          
          <div class="container h-lg-100 d-block overflow-lg-auto mt-1 mt-lg-5">
            <span style="user-select: auto;" class="fw-bold h4">CREATE ACCOUNT</span><br>
            <form method="post" action="">
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">PERSONAL INFORMATION</span>
                </div><br/>
                <input style="text-align: center;" name="user" type="text" value="" placeholder="Username" pattern="[a-zA-Z]{6,}" title="Username must be atleast 6 lowercase letters long (No Space)" class="form-control mb-1" autocomplete="off" required/><br/>
                <input style="text-align: center;" name="first" type="text" value="" placeholder="Firstname" pattern="[a-zA-Z ]{3,}" title="Firstname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="last" type="text" value="" placeholder="Lastname" pattern="[a-zA-Z ]{3,}" title="Lastname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="other" type="text" value="" placeholder="Othername" pattern="[a-zA-Z ]{3,}" title="Othername must be atleast 3 letters long" class="form-control mb-1" /><br/>
                <input style="text-align: center;" name="address" type="text" value="" placeholder="Home Address" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">CONTACT INFORMATION</span>
                </div><br/>
                <input style="text-align: center;" name="email" type="email" value="" placeholder="Email" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="phone" type="text" value="" placeholder="Phone Number" pattern="[0-9]{11}" title="Phone number must be 11 digit long" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="color-4 bg-3 m-inline-block-dp s-inline-block-dp m-font-size-14 s-font-size-16 m-width-60 s-width-45">
                    <span id="user-status-span" class="a-cursor" style="user-select: auto;">PASSWORD</span>
                </div><br/>
                <input style="text-align: center;" name="pass" type="password" value="" placeholder="Password" class="form-control mb-1" required/><br/>
                
                <?php if(!empty($check_user_referral_details["id"])){ ?>
                <div style="text-align: center;" class="col-12">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">Referred by: <?php echo ucwords($check_user_referral_details["firstname"]." ".$check_user_referral_details["lastname"]).checkIfEmpty(ucwords($check_user_referral_details["othername"]), ", ", ""); ?></span>
                </div><br/>
                <?php } ?>
                <div class="col-12">	
                	<div class="g-recaptcha" style="transform: scale(0.82); -webkit-transform: scale(0.82); transform-origin: 0 0; -webkit-transform-origin: 0 0;" data-sitekey="<?php echo $get_recaptcha_key['site_key']; ?>"></div>
                </div><br/>
                <button id="" name="register" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-5" >
                    REGISTER
                </button><br/>
                <span style="user-select: auto;" class="h5">Already have an account? 
                	<a href="<?php echo $web_http_host; ?>/web/Login.php">
                		<span style="user-select: auto;" class="fw-bold">
                			Signin
                		</span>
                	</a>
                </span><br>
                <span style="user-select: auto;" class="h5">
                	<a href="<?php echo $web_http_host; ?>/web/PasswordRecovery.php">
                		<span style="user-select: auto;" class="fw-bold">
                			Password Recovery
                		</span>
                	</a>
                </span><br>
            </form>
          </div>
    </div>
  </div>

<?php if(isset($_SESSION["product_purchase_response"])){ ?>

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.16/dist/sweetalert2.all.min.js"></script>

<script>
  Swal.fire ('Done!', '<?php echo $_SESSION["product_purchase_response"]; ?>', 'success') ;
            //Swal.fire({ position:'top-end',type:'',title:'Oops', text: 'kindly fill all form', showConfirmButton:1,timer:2500 });
          setTimeout(() => {
                fetch('/func/unset-product-response.php')
                    .then(response => response.text());
            }, 1000); // 3 seconds

</script>


	<!-- <div style="text-align: center;" id="customAlertDiv" class="bg-2 box-shadow m-z-index-2 s-z-index-2 m-block-dp s-block-dp m-position-fix s-position-fix m-top-20 s-top-40 br-radius-5px m-width-60 s-width-26 m-height-auto s-height-auto m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-padding-tp-5 s-padding-tp-1 m-padding-bm-5 s-padding-bm-1 m-margin-lt-19 s-margin-lt-36 m-margin-bm-2 s-margin-bm-2">
	<span style="user-select: notne;" class="color-10 text-bold-500 m-font-size-20 s-font-size-25">
		<?php echo $_SESSION["product_purchase_response"]; ?>
	</span><br/>
	<button style="text-align: center; user-select: auto;" onclick="customDismissPop();" onkeypress="keyCustomDismissPop(event);" class="button-box onhover-bg-color-10 a-cursor color-2 bg-10 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-30 s-width-30 m-height-auto s-height-auto m-margin-tp-1 s-margin-tp-1 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-5 s-padding-lt-5 m-padding-rt-5 s-padding-rt-5">
		DISMISS
	</button>
</div>
<script>
	function customDismissPop(){
		var customAlertDiv = document.getElementById("customAlertDiv");
		setTimeout(function(){
			customAlertDiv.style.display = "none";
		}, 300);
	}
	
	document.addEventListener("keydown", function(event){
		if(event.keyCode === 13){
			//prevent enter key default function
			event.preventDefault();
			var customAlertDiv = document.getElementById("customAlertDiv");
			setTimeout(function(){
				customAlertDiv.style.display = "none";
			}, 300);
		}
	});
	
	clearProductResponse();
	function clearProductResponse(){
		var productHttp = new XMLHttpRequest();
        productHttp.open("GET", "../unset-product.php");
        productHttp.setRequestHeader("Content-Type", "application/json");
        // productHttp.onload = function(){
        //     alert(productHttp.status);
        // }
        productHttp.send();
	}
</script>-->
<?php } ?>
<script src="/jsfile/bc-custom-all.js"></script>
</body>
</html>