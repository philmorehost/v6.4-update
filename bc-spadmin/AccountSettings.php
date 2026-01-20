<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-spadmin-config.php");
    
    
    if(isset($_POST["change-logo"])){
        $logo_name = $_FILES["logo"]["name"];
        $logo_tmp_name = $_FILES["logo"]["tmp_name"];
        $logo_size = $_FILES["logo"]["size"];
        $logo_ext = strtolower(pathinfo($logo_name)["extension"]);
        $acceptable_ext_array = array("png","jpg");
        $website_edited_name = str_replace([".",":"],"-",$_SERVER["HTTP_HOST"]);
        
        if(!empty($logo_name) && ($logo_size <= "2097152") && in_array($logo_ext, $acceptable_ext_array)){
        	if(file_exists("../uploaded-image/sp-logo.png") == true){
				unlink("../uploaded-image/sp-logo.png");
				move_uploaded_file($logo_tmp_name, "../uploaded-image/sp-logo.png");
				//Website Logo Updated Successfully
				$json_response_array = array("desc" => "Website Logo Updated Successfully");
				$json_response_encode = json_encode($json_response_array,true);
			}else{
				move_uploaded_file($logo_tmp_name, "../uploaded-image/sp-logo.png");
				//Website Logo Created Successfully
				$json_response_array = array("desc" => "Website Logo Created Successfully");
				$json_response_encode = json_encode($json_response_array,true);
			}
        }else{
            if(empty($logo_name)){
                //File Field Empty
                $json_response_array = array("desc" => "File Field Empty");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(($logo_size > "2097152")){
                    //File Too Larger Than 2MB
                    $json_response_array = array("desc" => "File Too Larger Than 2MB");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(!in_array($logo_ext, $acceptable_ext_array)){
                        //Error: Image Extension must be ()
                        $json_response_array = array("desc" => "Error: Image Extension must be (".implode(", ", $acceptable_ext_array).")");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }
            }
        }
    
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }

    $css_style_template_array = array("1" => "bc-style-template-1", "2" => "bc-style-template-2", "3" => "bc-style-template-3", "4" => "bc-style-template-4", "5" => "bc-style-template-5");
    if (isset($_POST["update-template"])) {
        $template_name = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["template-name"])));
        $template_filename = pathinfo($template_name, PATHINFO_FILENAME);
        $css_style_template_name_array = array_values($css_style_template_array);

        if (!empty($template_name) && in_array($template_filename, $css_style_template_name_array)) {
            $select_spadmin_style_templates_details = mysqli_query($connection_server, "SELECT * FROM sas_spadmin_style_templates");
            if (mysqli_num_rows($select_spadmin_style_templates_details) == 0) {
                mysqli_query($connection_server, "INSERT INTO sas_spadmin_style_templates (template_name) VALUES ('$template_name')");
                //Template Created & Updated Successfully
                $json_response_array = array("desc" => "Template Created & Updated Successfully");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($select_spadmin_style_templates_details) == 1) {
                    mysqli_query($connection_server, "UPDATE sas_spadmin_style_templates SET template_name='$template_name'");
                    //Template Updated Successfully
                    $json_response_array = array("desc" => "Template Updated Successfully");
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    if (mysqli_num_rows($select_spadmin_style_templates_details) > 1) {
                        //Duplicated Details, Contact Admin
                        $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                }
            }
        } else {
            if (empty($template_name)) {
                //Template Field Empty
                $json_response_array = array("desc" => "Template Field Empty");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (!in_array($template_filename, $css_style_template_name_array)) {
                    //Invalid Template Type
                    $json_response_array = array("desc" => "Invalid Template Type");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }

        $json_response_decode = json_decode($json_response_encode, true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: " . $_SERVER["REQUEST_URI"]);
    }
    
    if(isset($_POST["update-profile"])){
        $first = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["first"]))));
        $last = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["last"]))));
        $address = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["address"]))));
        $email = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["email"]))));
        $phone = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["phone"]))));
    	$pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["pass"])));
        
        if(!empty($first) && !empty($last) && !empty($address) && !empty($email) && !empty($phone) && !empty($pass)){
            $check_admin_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin WHERE id='".$get_logged_spadmin_details["id"]."'");
            if(mysqli_num_rows($check_admin_details) == 1){
                $md5_pass = md5($pass);
                $check_admin_with_email = mysqli_query($connection_server, "SELECT * FROM sas_super_admin WHERE email='$email'");
                $check_admin_with_phone = mysqli_query($connection_server, "SELECT * FROM sas_super_admin WHERE phone_number='$phone'");
                $check_admin_with_pass = mysqli_query($connection_server, "SELECT * FROM sas_super_admin WHERE id='".$get_logged_spadmin_details["id"]."' && password='$md5_pass'");
                
                if(mysqli_num_rows($check_admin_with_pass) == 1){
                    $proceed_account_phone_verification = false;
                    if(mysqli_num_rows($check_admin_with_email) == 1){
                        $admin_email_fetch = mysqli_fetch_array($check_admin_with_email);
                        if($admin_email_fetch["id"] == $get_logged_spadmin_details["id"]){
                            $proceed_account_phone_verification = true;
                        }else{
                            //Email Taken By Another Admin
                            $json_response_array = array("desc" => "Email Taken By Another Admin");
                            $json_response_encode = json_encode($json_response_array,true);
                        }
                    }else{
                        if(mysqli_num_rows($check_admin_with_email) == 0){
                            $proceed_account_phone_verification = true;
                        }else{
                            //Duplicated Email, Contact Admin
                            $json_response_array = array("desc" => "Duplicated Email, Contact Admin");
                            $json_response_encode = json_encode($json_response_array,true);
                        }
                    }

                    $proceed_account_update = false;
                    if($proceed_account_phone_verification == true){
                        if(mysqli_num_rows($check_admin_with_phone) == 1){
                            $admin_phone_fetch = mysqli_fetch_array($check_admin_with_phone);
                            if($admin_phone_fetch["id"] == $get_logged_spadmin_details["id"]){
                                $proceed_account_update = true;
                            }else{
                                //Phone Number Taken By Another Admin
                                $json_response_array = array("desc" => "Phone Number Taken By Another Admin");
                                $json_response_encode = json_encode($json_response_array,true);
                            }
                        }else{
                            if(mysqli_num_rows($check_admin_with_phone) == 0){
                                $proceed_account_update = true;
                            }else{
                                //Duplicated Phone Number, Contact Admin
                                $json_response_array = array("desc" => "Duplicated Phone Number, Contact Admin");
                                $json_response_encode = json_encode($json_response_array,true);
                            }
                        }
                    }

                    if($proceed_account_update == true){
                        mysqli_query($connection_server, "UPDATE sas_super_admin SET firstname='$first', lastname='$last', home_address='$address', email='$email', phone_number='$phone' WHERE id='".$get_logged_spadmin_details["id"]."'");
                        //Profile Information Updated Successfully
                        $json_response_array = array("desc" => "Profile Information Updated Successfully");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }else{
                    //Incorrect Password
                    $json_response_array = array("desc" => "Incorrect Password");
                    $json_response_encode = json_encode($json_response_array,true);
                }
            }else{
                if(mysqli_num_rows($check_admin_details) == 0){
                    //Admin Not Exists
                    $json_response_array = array("desc" => "Admin Not Exists");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(mysqli_num_rows($check_admin_details) > 1){
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
                                if(empty($pass)){
                                    //Password Field Empty
                                    $json_response_array = array("desc" => "Password Field Empty");
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
        $old_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["old-pass"])));
        $new_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["new-pass"])));
        $con_new_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["con-new-pass"])));
        
        if(!empty($old_pass) && !empty($new_pass) && !empty($con_new_pass)){
            $check_admin_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin WHERE id='".$get_logged_spadmin_details["id"]."'");
            if(mysqli_num_rows($check_admin_details) == 1){
                $md5_old_pass = md5($old_pass);
                $md5_new_pass = md5($new_pass);
                $md5_con_new_pass = md5($con_new_pass);
                
                if($md5_old_pass == $get_logged_spadmin_details["password"]){
                    if($md5_new_pass !== $get_logged_spadmin_details["password"]){
                        if($md5_new_pass == $md5_con_new_pass){
                            mysqli_query($connection_server, "UPDATE sas_super_admin SET password='$md5_new_pass' WHERE id='".$get_logged_spadmin_details["id"]."'");
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
                if(mysqli_num_rows($check_admin_details) == 0){
                    //Admin Not Exists
                    $json_response_array = array("desc" => "Admin Not Exists");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(mysqli_num_rows($check_admin_details) > 1){
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

    if(isset($_POST["update-bank-details"])){
        $fullname = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["name"])));
        $bank_name = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["bank"])));
        $account_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/","",trim(strip_tags($_POST["number"]))));
        $phone_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/","",strip_tags($_POST["phone"])));
        $amount_charged = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/","",trim(strip_tags($_POST["charges"]))));

        if(!empty($fullname) && !empty($bank_name) && !empty($account_number) && is_numeric($account_number) && !empty($phone_number) && is_numeric($phone_number) && !empty($amount_charged) && is_numeric($amount_charged) && ($amount_charged > 0)){
            $get_admin_payment_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_payments LIMIT 1");
	
            if(mysqli_num_rows($get_admin_payment_details) == 1){
                mysqli_query($connection_server, "UPDATE sas_super_admin_payments SET bank_name='$bank_name', account_name='$fullname', account_number='$account_number', phone_number='$phone_number', amount_charged='$amount_charged'");
                //Bank Information Updated Successfully
                $json_response_array = array("desc" => "Bank Information Updated Successfully");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(mysqli_num_rows($get_admin_payment_details) == 0){
		            mysqli_query($connection_server, "INSERT INTO sas_super_admin_payments (bank_name, account_name, account_number, phone_number, amount_charged) VALUES ('$bank_name', '$fullname', '$account_number', '$phone_number', '$amount_charged')");
                    //Admin Bank Info Exists
                    $json_response_array = array("desc" => "Bank Information Created Successfully");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(mysqli_num_rows($get_admin_payment_details) > 1){
                        //Duplicated Details, Contact Admin
                        $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }
            }
        }else{
            if(empty($fullname)){
                //Fullname Field Empty
                $json_response_array = array("desc" => "Fullname Field Empty");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(empty($bank_name)){
                    //Bank Name Field Empty
                    $json_response_array = array("desc" => "Bank Name Field Empty");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(empty($account_number)){
                        //Account Number Field Empty
                        $json_response_array = array("desc" => "Account Number Field Empty");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                        if(!is_numeric($account_number)){
                            //Non-numeric Account Number
                            $json_response_array = array("desc" => "Non-numeric Account Number");
                            $json_response_encode = json_encode($json_response_array,true);
                        }else{
                            if(empty($phone_number)){
                                //Phone Number Field Empty
                                $json_response_array = array("desc" => "Phone Number Field Empty");
                                $json_response_encode = json_encode($json_response_array,true);
                            }else{
                                if(!is_numeric($phone_number)){
                                    //Non-numeric Phone Number
                                    $json_response_array = array("desc" => "Non-numeric Account Number");
                                    $json_response_encode = json_encode($json_response_array,true);
                                }else{
                                    if(empty($amount_charged)){
                                        //Amount Field Empty
                                        $json_response_array = array("desc" => "Amount Number Field Empty");
                                        $json_response_encode = json_encode($json_response_array,true);
                                    }else{
                                        if(!is_numeric($amount_charged)){
                                            //Non-numeric Amount
                                            $json_response_array = array("desc" => "Non-numeric Account");
                                            $json_response_encode = json_encode($json_response_array,true);
                                        }else{
                                            if($amount_charged > 0){
                                                //Amount Must Be Greater Than 0
                                                $json_response_array = array("desc" => "Amount Must Be Greater Than 0");
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


    if(isset($_POST["update-payment-order-details"])){
        $min_amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/","",trim(strip_tags($_POST["min"]))));
        $max_amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/","",trim(strip_tags($_POST["max"]))));

        if(!empty($min_amount) && is_numeric($min_amount) && ($min_amount > 0) && !empty($max_amount) && is_numeric($max_amount) && ($max_amount > 0) && ($max_amount > $min_amount)){
            $get_admin_payment_order_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_payment_orders LIMIT 1");
	
            if(mysqli_num_rows($get_admin_payment_order_details) == 1){
                mysqli_query($connection_server, "UPDATE sas_super_admin_payment_orders SET min_amount='$min_amount', max_amount='$max_amount'");
                //Payment Order Limits Information Updated Successfully
                $json_response_array = array("desc" => "Payment Order Limits Information Updated Successfully");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(mysqli_num_rows($get_admin_payment_order_details) == 0){
		            mysqli_query($connection_server, "INSERT INTO sas_super_admin_payment_orders (min_amount, max_amount) VALUES ('$min_amount', '$max_amount')");
                    //Payment Order Limits Information Created Successfully
                    $json_response_array = array("desc" => "Payment Order Limits Information Created Successfully");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(mysqli_num_rows($get_admin_payment_order_details) > 1){
                        //Duplicated Details, Contact Admin
                        $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }
            }
        }else{
            if(empty($min_amount)){
                //Minimum Amount Field Empty
                $json_response_array = array("desc" => "Minimum Amount Field Empty");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(!is_numeric($min_amount)){
                    //Non-numeric Minimum Amount
                    $json_response_array = array("desc" => "Non-numeric Minimum Amount");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(($min_amount < 0)){
                        //Minimum Amount MUst Be Greater Than Zero (0)
                        $json_response_array = array("desc" => "Minimum Amount MUst Be Greater Than Zero (0)");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                        if(empty($max_amount)){
                            //Maximum Amount Field Empty
                            $json_response_array = array("desc" => "Maximum Amount Field Empty");
                            $json_response_encode = json_encode($json_response_array,true);
                        }else{
                            if(!is_numeric($max_amount)){
                                //Non-numeric Maximum Amount
                                $json_response_array = array("desc" => "Non-numeric Maximum Amount");
                                $json_response_encode = json_encode($json_response_array,true);
                            }else{
                                if(($max_amount < 0)){
                                    //Maximum Amount MUst Be Greater Than Zero (0)
                                    $json_response_array = array("desc" => "Maximum Amount MUst Be Greater Than Zero (0)");
                                    $json_response_encode = json_encode($json_response_array,true);
                                }else{
                                    if(($min_amount > $max_amount)){
                                        //Minimum Amount Must Not Be Greater Than Maximum Amount
                                        $json_response_array = array("desc" => "Minimum Amount Must Not Be Greater Than Maximum Amount");
                                        $json_response_encode = json_encode($json_response_array,true);
                                    }else{
                                        if(($min_amount == $max_amount)){
                                            //Minimum Amount Must Not Equal Maximum Amount
                                            $json_response_array = array("desc" => "Minimum Amount Must Not Equal Maximum Amount");
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

	
    if(isset($_POST["update-site-details"])){
		$site_title = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["site-title"])));
		$site_desc = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["site-desc"])));
	
		if(!empty($site_title) && !empty($site_desc)){
			$get_site_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_site_details");
	
			if(mysqli_num_rows($get_site_details) == 1){
				mysqli_query($connection_server, "UPDATE sas_super_admin_site_details SET site_title='$site_title', site_desc='$site_desc'");
				//Site Information Updated Successfully
				$json_response_array = array("desc" => "Site Information Updated Successfully");
				$json_response_encode = json_encode($json_response_array,true);
			}else{
				if(mysqli_num_rows($get_site_details) == 0){
					mysqli_query($connection_server, "INSERT INTO sas_super_admin_site_details (site_title, site_desc) VALUES ('$site_title', '$site_desc')");
					//Site Information Created Successfully
					$json_response_array = array("desc" => "Site Information Created Successfully");
					$json_response_encode = json_encode($json_response_array,true);
				}else{
					if(mysqli_num_rows($get_site_details) > 1){
						//Duplicated Details, Contact Admin
						$json_response_array = array("desc" => "Duplicated Details, Contact Admin");
						$json_response_encode = json_encode($json_response_array,true);
					}
				}
			}
		}else{
			if(empty($site_title)){
				//Site Title Field Empty
				$json_response_array = array("desc" => "Site Title Field Empty");
				$json_response_encode = json_encode($json_response_array,true);
			}else{
				if(empty($site_desc)){
					//Site Desc Field Empty
					$json_response_array = array("desc" => "Site Description Field Empty");
					$json_response_encode = json_encode($json_response_array,true);
				}
			}
		}
	
		$json_response_decode = json_decode($json_response_encode,true);
		$_SESSION["product_purchase_response"] = $json_response_decode["desc"];
		header("Location: ".$_SERVER["REQUEST_URI"]);
	}

	$get_admin_payment_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_super_admin_payments LIMIT 1");
	$get_admin_payment_order_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_super_admin_payment_orders LIMIT 1");
	$get_site_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_super_admin_site_details LIMIT 1");
    

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
      <h1>ADMIN ACCOUNT SETTINGS</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Account Settings</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
  
        <div style="text-align: center;"
        class="card info-card px-5 py-5">
    		<form method="post" enctype="multipart/form-data" action="">
    			<?php
    				if(file_exists("../uploaded-image/sp-logo.png") == true){
    					$logo_image = '<img src="'.$web_http_host.'/uploaded-image/sp-logo.png" class="col-2" /><br/>';
    				}else{
    					$logo_image = '<span class="fw-bld h5">No Logo Image</span><br/>';
    				}
    				echo $logo_image;
    			?>
    			<div style="text-align: center;" class="container">
    				<span id="admin-status-span" class="h5" style="user-select: auto;">LOGO IMAGE</span>
    			</div><br/>
    			<input style="text-align: center;" name="logo" type="file" accept=".png,.jpg" placeholder="Choose Image" class="form-control mb-1" required/><br/>
    			
    			<button name="change-logo" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
              CHANGE LOGO
          </button><br>
    		</form>
    	</div><br/>
    	
      <!-- <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">STYLE
            TEMPLATE</span><br>
            <form method="post" enctype="multipart/form-data" action="">
                <div style="text-align: center;"
                class="text-dark h5">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">CHOOSE TEMPLATE</span>
                </div><br />
                <div style="text-align: center;"
                    class="color-4 bg-3 m-inline-block-dp s-inline-block-dp m-font-size-14 s-font-size-16 m-width-60 s-width-47">
                    <div class="color-2 bg-3 m-flex-row-dp s-flex-row-dp">
                        <?php
                        foreach ($css_style_template_array as $template_id => $template_name) {
                            $template_name = $template_name . ".css";
                            $template_id_name = "Temp " . $template_id;
                            echo
                                '<div class="m-width-20 s-width-20 m-height-auto s-height-auto">
                                    <span id="admin-status-span" class="h5" style="user-select: auto;">'.$template_id_name.'</span><br/>
                                    <input type="radio" name="template-name" value="'.$template_name.'" required/>
                                    <img alt="Template '.$template_id.'" src="../asset/template/temp-'.$template_id.'.png" class="col-10">
                                </div>';
                        }
                        ?>
                    </div>

                </div><br />

                <button name="update-template" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                    UPDATE TEMPLATE
                </button><br>
            </form>
        </div><br /> -->

        <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">UPDATE
            PROFILE</span><br>
            <form method="post" action="">
                <div style="text-align: center;"
                class="text-dark h5">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">PERSONAL INFORMATION</span>
                </div><br />
                <input style="text-align: center;" name="first" type="text" value="<?php echo $get_logged_spadmin_details['firstname']; ?>" placeholder="Firstname" pattern="[a-zA-Z ]{3,}" title="Firstname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="last" type="text" value="<?php echo $get_logged_spadmin_details['lastname']; ?>" placeholder="Lastname" pattern="[a-zA-Z ]{3,}" title="Lastname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="email" type="email" value="<?php echo $get_logged_spadmin_details['email']; ?>" placeholder="Email" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="phone" type="text" value="<?php echo $get_logged_spadmin_details['phone_number']; ?>" placeholder="Phone Number" pattern="[0-9]{11}" title="Phone number must be 11 digit long" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="address" type="text" value="<?php echo $get_logged_spadmin_details['home_address']; ?>" placeholder="Home Address" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">PASSWORD</span>
                </div><br/>
                <input style="text-align: center;" name="pass" type="password" value="" placeholder="Old Password" class="form-control mb-1" required/><br/>
                
                <button name="update-profile" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                    UPDATE PROFILE
                </button><br>
            </form>
        </div><br/>

        <div style="text-align: center;"
        class="card info-card px-5 py-5">
            <span style="user-select: auto;" class="text-dark h3">CHANGE PASSWORD</span><br>
            <form method="post" action="">
                <div style="text-align: center;" class="container">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">OLD PASSWORD</span>
                </div><br/>
                <input style="text-align: center;" name="old-pass" type="password" value="" placeholder="Old Password" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">NEW PASSWORD</span>
                </div><br/>
                <input style="text-align: center;" name="new-pass" type="password" value="" placeholder="New Password" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" name="con-new-pass" type="password" value="" placeholder="Confirm New Password" class="form-control mb-1" required/><br/>
                <button name="change-password" type="submit" style="user-select: auto;" class="btn btn-success col-12 mb-1" >
                    CHANGE PASSWORD
                </button><br>
            </form>
        </div><br/>

        <div style="text-align: center;"
        class="card info-card px-5 py-5">
            <span style="user-select: auto;" class="text-dark h3">BANK INFORMATION</span><br>
            <form method="post" action="">
                <div style="text-align: center;" class="container">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">ACCOUNT NAME</span>
                </div><br/>
                <input style="text-align: center;" name="name" type="text" value="<?php echo $get_admin_payment_details['account_name']; ?>" placeholder="Fullname" pattern="[a-zA-Z ]{3,}" title="Fullname must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">BANK NAME</span>
                </div><br/>
                <input style="text-align: center;" name="bank" type="text" value="<?php echo $get_admin_payment_details['bank_name']; ?>" placeholder="Bank Name" pattern="[a-zA-Z ]{3,}" title="Bank Name must be atleast 3 letters long" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">ACCOUNT NUMBER</span>
                </div><br/>
                <input style="text-align: center;" name="number" type="text" value="<?php echo $get_admin_payment_details['account_number']; ?>" placeholder="Account Number" pattern="[0-9]{10}" title="Account number must be 10 digit long" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">PHONE NUMBER</span>
                </div><br/>
                <input style="text-align: center;" name="phone" type="text" value="<?php echo $get_admin_payment_details['phone_number']; ?>" placeholder="Phone Number" pattern="[0-9]{11}" title="Phone number must be 11 digit long" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">BANK CHARGES</span>
                </div><br/>
                <input style="text-align: center;" name="charges" type="text" value="<?php echo $get_admin_payment_details['amount_charged']; ?>" placeholder="Bank Charges" pattern="[0-9]{1,}" title="Bank Charges must atleast 1 digit long" class="form-control mb-1" required/><br/>
            
                <button name="update-bank-details" type="submit" style="user-select: auto;" class="btn btn-success col-12 mb-1" >
                    UPDATE BANK
                </button><br>
            </form>
        </div>

        <div style="text-align: center;"
        class="card info-card px-5 py-5">
            <span style="user-select: auto;" class="text-dark h3">PAYMENT ORDER</span><br>
            <form method="post" action="">
                <div style="text-align: center;" class="container">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">MINIMUM AMOUNT</span>
                </div><br/>
                <input style="text-align: center;" name="min" type="text" value="<?php echo $get_admin_payment_order_details['min_amount']; ?>" placeholder="Minimum Amount" pattern="[0-9]{2,}" title="Minimum Amount must be atleast 2 digit long" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">MAXIMUM AMOUNT</span>
                </div><br/>
                <input style="text-align: center;" name="max" type="text" value="<?php echo $get_admin_payment_order_details['max_amount']; ?>" placeholder="Maximum Amount" pattern="[0-9]{2,}" title="Maximum Amount must be atleast 2 digit long" class="form-control mb-1" required/><br/>
                
                <button name="update-payment-order-details" type="submit" style="user-select: auto;" class="btn btn-success col-12 mb-1" >
                    UPDATE PAYMENT ORDER
                </button><br>
            </form>
        </div>

        
        <div style="text-align: center;"
        class="card info-card px-5 py-5">
            <span style="user-select: auto;" class="text-dark h3">SITE DETAILS</span><br>
            <form method="post" action="">
                <div style="text-align: center;" class="container">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">SITE TITLE</span>
                </div><br/>
                <input style="text-align: center;" name="site-title" type="text" value="<?php echo $get_site_details['site_title']; ?>" placeholder="Site Title" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="admin-status-span" class="h5" style="user-select: auto;">SITE DESCRIPTION</span>
                </div><br/>
                <input style="text-align: center;" name="site-desc" type="text" value="<?php echo $get_site_details['site_desc']; ?>" placeholder="Site Description" class="form-control mb-1" required/><br/>
                
                <button name="update-site-details" type="submit" style="user-select: auto;" class="btn btn-success col-12 mb-1" >
                    UPDATE SITE DETAILS
                </button><br>
            </form>
        </div>
      </div>
    </section>
        
    <?php include("../func/bc-spadmin-footer.php"); ?>
    
</body>
</html>