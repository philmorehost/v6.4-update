<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-admin-config.php");
    
    if(isset($_POST["send-code"])){
    	$email = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["email"]))));
    	if(!empty($email)){
		$get_vendor_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_vendors WHERE website_url='".$_SERVER["HTTP_HOST"]."'");
    		$get_user_details = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='".$get_vendor_details["id"]."' && email='$email'");
    		if(mysqli_num_rows($get_user_details) == 1){
    			$get_user_personal_details = mysqli_fetch_array($get_user_details);
				$_SESSION["admin-recovery-username"] = $get_user_personal_details["email"];
				$_SESSION["admin-recovery-code"] = substr(str_shuffle("12345678901234567890"), 0, 6);
				// Email Beginning
				$log_template_encoded_text_array = array("{firstname}" => $get_user_personal_details["firstname"], "{lastname}" => $get_user_personal_details["lastname"], "{recovery_code}" => $_SESSION["admin-recovery-code"]);
				$raw_log_template_subject = getSuperAdminEmailTemplate('vendor-account-recovery','subject');
				$raw_log_template_body = getSuperAdminEmailTemplate('vendor-account-recovery','body');
				foreach($log_template_encoded_text_array as $array_key => $array_val){
					$raw_log_template_subject = str_replace($array_key, $array_val, $raw_log_template_subject);
					$raw_log_template_body = str_replace($array_key, $array_val, $raw_log_template_body);
				}
				sendVendorEmail($get_user_personal_details["email"], $raw_log_template_subject, $raw_log_template_body);
				// Email End
				//Recovery Code Emailed Successfully
				$json_response_array = array("desc" => "Recovery Code Emailed Successfully");
				$json_response_encode = json_encode($json_response_array,true);
    		}else{
    			if(mysqli_num_rows($get_user_details) > 1){
    				//Duplicated Details, Contact Admin
    				$json_response_array = array("desc" => "Duplicated Details, Contact Admin");
    				$json_response_encode = json_encode($json_response_array,true);
    			}else{
    				//User Not Exists
    				$json_response_array = array("desc" => "User Not Exists");
    				$json_response_encode = json_encode($json_response_array,true);
    			}
    		}
    	}else{
    		if(empty($email)){
    			//Email Field Empty
    			$json_response_array = array("desc" => "Email Field Empty");
    			$json_response_encode = json_encode($json_response_array,true);
    		}
    	}
		
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }

	if(isset($_POST["verify-code"])){
    	$pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["pass"])));
    	$confirm_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["confirm-pass"])));
		$recovery_code = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["code"]))));
    	if(!empty($pass) && !empty($confirm_pass) && !empty($recovery_code) && is_numeric($recovery_code) && (strlen($recovery_code) == "6")){
		$get_vendor_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_vendors WHERE website_url='".$_SERVER["HTTP_HOST"]."'");
    		$get_user_details = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='".$get_vendor_details["id"]."' && email='".$_SESSION["admin-recovery-username"]."'");
    		if(mysqli_num_rows($get_user_details) == 1){
				if($_SESSION["admin-recovery-code"] == $recovery_code){
					if($pass == $confirm_pass){
						$md5_pass = md5($pass);
						$get_user_personal_details = mysqli_fetch_array($get_user_details);
						$get_user_details = mysqli_query($connection_server, "UPDATE sas_vendors SET password='$md5_pass' WHERE id='".$get_user_personal_details["id"]."' && email='".$get_user_personal_details["email"]."'");
						// Email Beginning
						$log_template_encoded_text_array = array("{firstname}" => $get_user_personal_details["firstname"], "{lastname}" => $get_user_personal_details["lastname"]);
						$raw_log_template_subject = getSuperAdminEmailTemplate('vendor-pass-update','subject');
						$raw_log_template_body = getSuperAdminEmailTemplate('vendor-pass-update','body');
						foreach($log_template_encoded_text_array as $array_key => $array_val){
							$raw_log_template_subject = str_replace($array_key, $array_val, $raw_log_template_subject);
							$raw_log_template_body = str_replace($array_key, $array_val, $raw_log_template_body);
						}
						sendVendorEmail($get_user_personal_details["email"], $raw_log_template_subject, $raw_log_template_body);
						// Email End
						unset($_SESSION["admin-recovery-username"]);
						unset($_SESSION["admin-recovery-code"]);

						//Password Changed Successfully
						$json_response_array = array("desc" => "Password Changed Successfully");
						$json_response_encode = json_encode($json_response_array,true);
					}else{
						//Password Not Match
						$json_response_array = array("desc" => "Password Not Match");
						$json_response_encode = json_encode($json_response_array,true);
					}
				}else{
					//Invalid Recovery Code
					$json_response_array = array("desc" => "Invalid Recovery Code");
					$json_response_encode = json_encode($json_response_array,true);
				}
    		}else{
    			if(mysqli_num_rows($get_user_details) > 1){
    				//Duplicated Details, Contact Admin
    				$json_response_array = array("desc" => "Duplicated Details, Contact Admin");
    				$json_response_encode = json_encode($json_response_array,true);
    			}else{
    				//User Not Exists
    				$json_response_array = array("desc" => "User Not Exists");
    				$json_response_encode = json_encode($json_response_array,true);
    			}
    		}
    	}else{
			if(empty($pass)){
    			//Password Field Empty
    			$json_response_array = array("desc" => "Password Field Empty");
    			$json_response_encode = json_encode($json_response_array,true);
    		}else{
				if(empty($confirm_pass)){
					//Confirm Password Field Empty
					$json_response_array = array("desc" => "Confirm Password Field Empty");
					$json_response_encode = json_encode($json_response_array,true);
				}else{
					if(empty($recovery_code)){
						//Recovery Code Field Empty
						$json_response_array = array("desc" => "Recovery Code Field Empty");
						$json_response_encode = json_encode($json_response_array,true);
					}else{
						if(!is_numeric($recovery_code)){
							//Non-numeric String
							$json_response_array = array("desc" => "Non-numeric String");
							$json_response_encode = json_encode($json_response_array,true);
						}else{
							if(strlen($recovery_code) !== "6"){
								//Recovery Code Must Be 6 Digits
								$json_response_array = array("desc" => "Recovery Code Must Be 6 Digits");
								$json_response_encode = json_encode($json_response_array,true);
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

	if(isset($_POST["reset-recovery"])){
		unset($_SESSION["admin-recovery-username"]);
		unset($_SESSION["admin-recovery-code"]);
		header("Location: ".$_SERVER["REQUEST_URI"]);
	}
?>
<!DOCTYPE html>
<head>
	<title>Password Recovery | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
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
        
        <div class="col-10 col-lg-6 justify-content-center align-self-center d-inline-block">
            <span style="user-select: auto;" class="text-bg-1 color-4 m-inline-block-dp s-inline-block-dp text-bold-500 m-font-size-20 s-font-size-25 m-margin-bm-2 s-margin-bm-2">PASSWORD RECOVERY</span><br>
        <form method="post" action="">
            <?php if(!isset($_SESSION["admin-recovery-username"]) && empty($_SESSION["admin-recovery-username"]) && !isset($_SESSION["admin-recovery-code"]) && empty($_SESSION["admin-recovery-code"]) && !is_numeric($_SESSION["admin-recovery-code"]) && (strlen($_SESSION["admin-recovery-code"]) !== "6")){ ?>
			<input style="text-align: center;" name="email" type="email" value="" placeholder="Email" class="form-control mb-1" required/><br/>
            <button id="" name="send-code" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-2" >
                SEND CODE
            </button><br/>
			<?php } ?>

			<?php if(isset($_SESSION["admin-recovery-username"]) && !empty($_SESSION["admin-recovery-username"]) && isset($_SESSION["admin-recovery-code"]) && !empty($_SESSION["admin-recovery-code"]) && is_numeric($_SESSION["admin-recovery-code"]) && (strlen($_SESSION["admin-recovery-code"]) == "6")){ ?>
			<div style="text-align: center;" class="color-4 bg-3 m-inline-block-dp s-inline-block-dp m-font-size-14 s-font-size-16 m-width-60 s-width-45">
                <span id="user-status-span" class="a-cursor" style="user-select: auto;">RECOVERY CODE</span>
            </div><br/>
			<input style="text-align: center; text-transform: lowercase;" name="code" type="text" value="" placeholder="6 digit recovery code" pattern="[0-9]{6}" title="Recovery code must be 6 digit long (No Space)" class="form-control mb-1" autocomplete="on" required/><br/>
            <div style="text-align: center;" class="color-4 bg-3 m-inline-block-dp s-inline-block-dp m-font-size-14 s-font-size-16 m-width-60 s-width-45">
                <span id="user-status-span" class="a-cursor" style="user-select: auto;">PASSWORD</span>
            </div><br/>
            <input style="text-align: center;" name="pass" type="password" value="" placeholder="New Password" class="form-control mb-1" required/><br/>
            <input style="text-align: center;" name="confirm-pass" type="password" value="" placeholder="Confirm Password" class="form-control mb-1" required/><br/>
            
			<button id="" name="verify-code" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-2" >
                VERIFY CODE
            </button><br/>
			<?php } ?>
            
        </form>

		<form method="post" action="">
			<?php if(isset($_SESSION["admin-recovery-username"]) && !empty($_SESSION["admin-recovery-username"]) && isset($_SESSION["admin-recovery-code"]) && !empty($_SESSION["admin-recovery-code"]) && is_numeric($_SESSION["admin-recovery-code"]) && (strlen($_SESSION["admin-recovery-code"]) == "6")){ ?>
			<button id="" name="reset-recovery" type="submit" style="user-select: auto;" class="btn btn-warning col-12 mb-2" >
                RESET RECOVERY
            </button><br/>
			<?php } ?>
        </form>

		<span style="user-select: auto;" class="h5">
			<a href="<?php echo $web_http_host; ?>/bc-admin/Login.php">
				<span style="user-select: auto;" class="fw-bold">
					Login Account
				</span>
			</a>
		</span><br>
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
</script> -->

<?php } ?>
<script src="/jsfile/bc-custom-all.js"></script>
</body>
</html>