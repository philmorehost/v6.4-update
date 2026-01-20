<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-admin-config.php");
    
    $user_id_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($_GET["userID"]))));
    $select_user = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='$user_id_number'");
    if(mysqli_num_rows($select_user) > 0){
        $get_user_details = mysqli_fetch_array($select_user);
    }

    if(isset($_POST["update-profile"])){
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
    	
        if(!empty($first) && !empty($last) && !empty($quest) && is_numeric($quest) && !empty($answer) && !empty($address) && !empty($email) && !empty($phone)){
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $check_user_details = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='".$user_id_number."'");
                if(mysqli_num_rows($check_user_details) == 1){
                    if((strlen($answer) >= 3) && (strlen($answer) <= 20)){
                        if(is_numeric($phone) && (strlen($phone) == 11)){
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
            	            mysqli_query($connection_server, "UPDATE sas_users SET security_quest='$quest', security_answer='$answer', firstname='$first', lastname='$last', othername='$other', home_address='$address', email='$email', phone_number='$phone' WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='".$user_id_number."'");
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

        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
    
    if(isset($_POST["change-password"])){
        $new_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["new-pass"])));
        $con_new_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["con-new-pass"])));
        
        if(!empty($new_pass) && !empty($con_new_pass)){
            $check_user_details = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='".$user_id_number."'");
            if(mysqli_num_rows($check_user_details) == 1){
                $md5_new_pass = md5($new_pass);
                $md5_con_new_pass = md5($con_new_pass);
                
                if($md5_new_pass !== $get_logged_admin_details["password"]){
                    if($md5_new_pass == $md5_con_new_pass){
                        mysqli_query($connection_server, "UPDATE sas_users SET password='$md5_new_pass' WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='".$user_id_number."'");
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
    <title>Edit User | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
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
      <h1>EDIT USER</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">User Edit</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">    
    <?php if(!empty($get_user_details['id'])){ ?>
    	<div class="card info-card px-5 py-5">
    		<span style="user-select: auto;" class="h4">USER INFO</span><br>
    		<form method="post" enctype="multipart/form-data" action="">
                <div style="text-align: center;" class="container">
                	<img src="<?php echo $web_http_host; ?>/asset/user-icon.png" class="col-2" style="pointer-events: none; user-select: auto; filter: invert(1);"/>
                </div><br/>
                <div style="text-align: center;" class="container">
            		<span id="user-status-span" class="h5" style="user-select: auto;">PERSONAL INFORMATION</span>
            	</div><br/>
                <input style="text-align: center;" name="first" type="text" value="<?php echo $get_user_details['firstname']; ?>" placeholder="Firstname" pattern="[a-zA-Z ]{3,}" title="Firstname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="last" type="text" value="<?php echo $get_user_details['lastname']; ?>" placeholder="Lastname" pattern="[a-zA-Z ]{3,}" title="Lastname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="other" type="text" value="<?php echo $get_user_details['othername']; ?>" placeholder="Othername" pattern="[a-zA-Z ]{3,}" title="Othername must be atleast 3 letters long" class="form-control mb-1" /><br/>
                <input style="text-align: center;" name="address" type="text" value="<?php echo $get_user_details['home_address']; ?>" placeholder="Home Address" class="form-control mb-1" required/><br/>
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
                				if($security_details["id"] == $get_user_details['security_quest']){
                					echo '<option value="'.$security_details["id"].'" selected>'.$security_details["quest"].'</option>';
                				}else{
                					echo '<option value="'.$security_details["id"].'">'.$security_details["quest"].'</option>';
                				}
                			}
                		}
                	?>
                </select><br/>
                <input style="text-align: center;" id="" name="answer" onkeyup="" type="text" value="<?php echo $get_user_details['security_answer']; ?>" placeholder="Security Answer e.g Dog" pattern="[0-9a-zA-Z ]{3,20}" title="Security Answer Must Be Between 3-20 Charaters Without Special Charaters" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
            		<span id="user-status-span" class="h5" style="user-select: auto;">CONTACT INFORMATION</span>
            	</div><br/>
                <input style="text-align: center;" name="email" type="email" value="<?php echo $get_user_details['email']; ?>" placeholder="Email" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="phone" type="text" value="<?php echo $get_user_details['phone_number']; ?>" placeholder="Phone Number" pattern="[0-9]{11}" title="Phone number must be 11 digit long" class="form-control mb-1" required/><br/>
				
                <button name="update-profile" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                    UPDATE PROFILE
                </button><br>
    		</form>
    	</div><br/>
    	
        <div class="card info-card px-5 py-5">
            <span style="user-select: auto;" class="h4">CHANGE PASSWORD</span><br>
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
      <div class="card info-card px-5 py-5">
    		<span style="user-select: auto;" class="h4">USER INFO</span><br>
    		<img src="<?php echo $web_http_host; ?>/asset/ooops.gif" class="h5 m-width-60 s-width-50" style="user-select: auto;"/><br/>
            <div style="text-align: center;" class="container">
                <span id="user-status-span" class="h5" style="user-select: auto;">Ooops</span><br/>
                <span id="user-status-span" class="h6" style="user-select: auto;">User Account Not Exists</span>
            </div><br/>
        </div>
    <?php } ?>
    </div>
  </section>
    <?php include("../func/bc-admin-footer.php"); ?>
    
</body>
</html>