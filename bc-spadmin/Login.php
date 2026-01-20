<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
	
    include("../func/bc-spadmin-config.php");
    if(isset($get_logged_spadmin_details["email"]) && !empty($get_logged_spadmin_details["email"]) && ($get_logged_spadmin_details["status"] == 1)){
    	$redirecturl = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["redirecturl"])));
		if(!empty(trim($redirecturl)) && file_exists("..".$redirecturl)){
			header("Location: ".$redirecturl);
		}else{
			header("Location: /bc-spadmin/Dashboard.php");
		}
	}

    if(isset($_POST["login"])){
    	$email = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["email"]))));
    	$pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["pass"])));
    	if(!empty($email) && !empty($pass)){
    		$get_admin_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin WHERE email='$email'");
			if(mysqli_num_rows($get_admin_details) == 1){
				$md5_pass = md5($pass);
				$check_admin_password_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin WHERE email='$email' && password='$md5_pass'");
				if(mysqli_num_rows($check_admin_password_details) == 1){
					while($admin_detail = mysqli_fetch_assoc($check_admin_password_details)){
						if($admin_detail["status"] == 1){
							$_SESSION["spadmin_session"] = strtolower($admin_detail["email"]);
							//Welcome Back Message
							$json_response_array = array("desc" => "Welcome Back, ".ucwords($admin_detail["firstname"]));
							$json_response_encode = json_encode($json_response_array,true);
						}else{
							//Account Locked, Contact Admin
							$json_response_array = array("desc" => "Account Locked, Contact Admin");
							$json_response_encode = json_encode($json_response_array,true);
						}
					}
				}else{
					if(mysqli_num_rows($check_admin_password_details) < 1){
						//Incorrect Password
						$json_response_array = array("desc" => "Incorrect Password");
						$json_response_encode = json_encode($json_response_array,true);
					}
				}
			}else{
				if(mysqli_num_rows($get_admin_details) > 1){
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
    		}else{
    			if(empty($pass)){
    				//Password Field Empty
    				$json_response_array = array("desc" => "Password Field Empty");
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
    <div style="text-align: center;" class="container h-100 d-flex flex-column flex-lg-row">
        <div class="col-10 col-lg-6 justify-content-center align-self-center d-inline-block mt-5 mt-lg-0">
          <img src="<?php echo $web_http_host; ?>/uploaded-image/sp-logo.png" style="user-select: auto; object-fit: contain; object-position: center;" class="col-6 rounded-circle"/><br/>
        </div>
        
        <div class="col-10 col-lg-6 justify-content-center align-self-center d-inline-block">
          <span style="user-select: auto;" class="fw-bold h4">SUPER ADMIN LOGIN</span><br>
        <form method="post" action="">
            <input style="text-align: center; text-transform: lowercase;" name="email" type="email" value="" placeholder="Email" class="form-control mb-1" autocomplete="on" required/><br/>
            <input style="text-align: center;" name="pass" type="password" value="" placeholder="********" pattern="{8,}" title="Password must be atleast 8 character long" class="form-control mb-1" autocomplete="off" required/><br/>
            <button id="" name="login" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-2" >
                LOGIN
            </button><br/>
        </form>

		<span style="user-select: auto;" class="h5">
			<a href="<?php echo $web_http_host; ?>/bc-spadmin/PasswordRecovery.php">
				<span style="user-select: auto;" class="fw-boldfw-bold">
					Password Recovery
				</span>
			</a>
		</span><br>
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