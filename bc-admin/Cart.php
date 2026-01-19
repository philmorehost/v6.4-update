<?php session_start();
    include("../func/bc-admin-config.php");
    
	if(isset($_SESSION["spadmin_vendor_auth"]) && ($_SESSION["spadmin_vendor_auth"] == true)){
		$status_statement = "(status='1' OR status='2')";
	}else{
		$status_statement = "status='1'";
	}
	
	$get_host_name = array_filter(explode(":",trim($_SERVER["HTTP_HOST"])));
	$get_host_name = $get_host_name[0];
    if(isset($_POST["checkout-cart"])){
		$get_cart_items = mysqli_real_escape_string($connection_server, $_COOKIE[str_replace([":","."],"_",$get_host_name)."_".$get_logged_admin_details["id"]."_cart_items"]);
		
        if(isset($get_cart_items) && (array_filter(explode(" ",trim($get_cart_items))) >= 1)){
			$exp_cart_items = array_filter(explode(" ",trim($get_cart_items)));
			if(count($exp_cart_items) >= 1){
				$count_old_cart_items = 0;
				$count_new_cart_items = 0;
				$count_old_cart_items_amount = 0;
				$count_new_cart_items_amount = 0;
				$api_type_cart_name = "";
				$api_type_cart_name_price = "";
				$api_type_cart_name_website = "";
				foreach($exp_cart_items as $item_id){
					if(is_numeric($item_id) && ($item_id > 0)){
						$get_active_cart_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_api_marketplace_listings WHERE id='".$item_id."' && $status_statement"));
						
						if(isset($get_active_cart_details["api_type"])){
							$api_website = str_replace(["//www.","/","http:","https:"],"",$get_active_cart_details["api_website"]);
							$api_type = strtoupper(str_replace(["_","-"]," ",$get_active_cart_details["api_type"]));
							$product_description = $get_active_cart_details["description"];
							$product_api_price = toDecimal($get_active_cart_details["price"], 2);
							$count_new_cart_items += 1;
							$count_new_cart_items_amount += $get_active_cart_details["price"];
							$api_type_cart_name .= strtolower(str_replace(" ","-",trim($api_type))). " ";
							$api_type_cart_name_price .= $get_active_cart_details["price"]. " ";
							$api_type_cart_name_website .= strtolower($api_website). " ";

						}else{
							//Unknown Item_id
							// $count_old_cart_items += 1;
							// $count_old_cart_items_amount += $get_active_cart_details["price"];
						}
					}
				}


				if(is_numeric($count_new_cart_items_amount) && ($count_new_cart_items_amount > 0)){
					if(is_numeric(vendorBalance(2))){
						if((vendorBalance(2) > 0) && (vendorBalance(2) >= $count_new_cart_items_amount)){
							$product_unique_id = trim($api_type_cart_name);
							$reference = substr(str_shuffle("12345678901234567890"), 0, 15);
							$type_alternative = ucwords(str_replace(" ", ", ", $product_unique_id));
							$amount = $count_new_cart_items_amount;
							$discounted_amount = $amount;
							$description = "API Checkout Charge: ".strtoupper($type_alternative);
							$status = 3;
							$debit_vendor = chargeVendor("debit", $get_logged_admin_details["email"]." ".$product_unique_id, $type_alternative, $reference, $amount, $discounted_amount, $description, $_SERVER["HTTP_HOST"], $status);

							if($debit_vendor == "success"){
								alterVendorTransaction($reference, "status", "1");
								$cart_api_name_array = array_filter(explode(" ", trim($api_type_cart_name)));
								$cart_api_name_price_array = array_filter(explode(" ", trim($api_type_cart_name_price)));
								$cart_api_name_website_array = array_filter(explode(" ", trim($api_type_cart_name_website)));
								
								if(count($cart_api_name_array) > 0){
									if((count($cart_api_name_array) == count($cart_api_name_price_array)) && (count($cart_api_name_price_array) == count($cart_api_name_website_array))){
										$installation_message = "";
										foreach($cart_api_name_array as $index => $api_name){
											$reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
											$select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_base_url='".$cart_api_name_website_array[$index]."' && api_type='".strtolower($cart_api_name_array[$index])."'");
											if(mysqli_num_rows($select_api_lists) > 0){
												if(mysqli_num_rows($select_api_lists) == 1){
													$api_status = "Installed";
													$refund_amount = $cart_api_name_price_array[$index];
													$refund_discounted_amount = $refund_amount;
													$refund_description = "Refund for Ref:<i>'$reference'</i> Item ".ucwords(str_replace("-", " ",$cart_api_name_array[$index]))." API already Installed"."<br/>";
													$refund_status = 1;
													chargeVendor("credit", $get_logged_admin_details["email"]." ".$cart_api_name_array[$index], ucwords(str_replace("-", " ",$cart_api_name_array[$index])), $reference_2, $refund_amount, $refund_discounted_amount, $refund_description, $_SERVER["HTTP_HOST"], $refund_status);
													$installation_message .= "* [Failed: Refunded] ".ucwords(str_replace("-", " ",$cart_api_name_array[$index]))." (".$cart_api_name_website_array[$index].") already installed {N".$cart_api_name_price_array[$index]." Refunded}"."<br/>";
												}else{
													if(mysqli_num_rows($select_api_lists) > 1){
														//Installed (Duplicated API)
														$api_status = "Installed";
														$refund_amount = $cart_api_name_price_array[$index];
														$refund_discounted_amount = $refund_amount;
														$refund_description = "Refund for Ref:<i>'$reference'</i> Item ".ucwords(str_replace("-", " ",$cart_api_name_array[$index]))." API already Installed"."<br/>";
														$refund_status = 1;
														chargeVendor("credit", $get_logged_admin_details["email"]." ".$cart_api_name_array[$index], ucwords(str_replace("-", " ",$cart_api_name_array[$index])), $reference_2, $refund_amount, $refund_discounted_amount, $refund_description, $_SERVER["HTTP_HOST"], $refund_status);
														
														$installation_message .= "* "."[Failed: Duplicated API] ".ucwords(str_replace("-", " ",$cart_api_name_array[$index]))." (".$cart_api_name_website_array[$index].") already installed multiple times, Contact Admin for assistance {N".$cart_api_name_price_array[$index]." Refunded}"."<br/>";
													}
												}
											}else{
												//install code
												mysqli_query($connection_server, "INSERT INTO sas_apis (vendor_id, api_base_url, api_type, api_key, status) VALUES ('".$get_logged_admin_details["id"]."', '".$cart_api_name_website_array[$index]."', '".strtolower($cart_api_name_array[$index])."', '', '1')");
												$installation_message .= "* "."[Success: API Installed] ".ucwords(str_replace("-", " ",$cart_api_name_array[$index]))." (".$cart_api_name_website_array[$index].") installed Successfully {N".$cart_api_name_price_array[$index]." Paid}"."<br/>";
											}
										}
										//Clear Cart Items Cookies
										setcookie(str_replace([":","."],"_",$get_host_name)."_".$get_logged_admin_details["id"]."_cart_items", "", (time() - 100));
										$json_response_array = array("desc" => $installation_message);
										$json_response_encode = json_encode($json_response_array,true);
										$marketplace_redirect = true;
									}else{
										$json_response_array = array("desc" => ucwords("Error: Incomplete Information"));
										$json_response_encode = json_encode($json_response_array,true);
									}
								}else{
									$json_response_array = array("desc" => ucwords("API List Is Empty, Contact Admin for Further Assistance"));
									$json_response_encode = json_encode($json_response_array,true);
								}
							}else{
								chargeVendor("debit", $product_unique_id, $type_alternative, $reference, $amount, $discounted_amount, $description, $_SERVER["HTTP_HOST"], $status);
								$json_response_array = array("desc" => ucwords("Error: Failed To Initiate Transaction"));
								$json_response_encode = json_encode($json_response_array,true);
							}
						}else{
							//Insufficient Fund
							$json_response_array = array("desc" => "Insufficient Fund");
							$json_response_encode = json_encode($json_response_array,true);
						}
					}else{
						//Non-numeric Balance
						$json_response_array = array("desc" => "Non-numeric Balance");
						$json_response_encode = json_encode($json_response_array,true);
					}
				}else{
					//Non-numeric Amount
					$json_response_array = array("desc" => "Non-numeric Amount");
					$json_response_encode = json_encode($json_response_array,true);
				}
			}else{
				//No Item In Cart
				$json_response_array = array("desc" => "No Item In Cart");
				$json_response_encode = json_encode($json_response_array,true);
			}
        }else{
            //Cart Is Empty
            $json_response_array = array("desc" => "Cart Is Empty");
            $json_response_encode = json_encode($json_response_array,true);
        }
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
		if($marketplace_redirect == false){
        	header("Location: ".$_SERVER["REQUEST_URI"]);
		}else{
			header("Location: /bc-admin/MarketPlace.php");
		}
    }
?>
<!DOCTYPE html>
<head>
    <title>Checkout Cart | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
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
      <h1>CART CHECKOUT</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Cart</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
      <div class="card info-card px-5 py-5">
            <div class="row mb-3">
			<?php
				$get_cart_items = $_COOKIE[str_replace([":","."],"_",$get_host_name)."_".$get_logged_admin_details["id"]."_cart_items"];
				if(isset($get_cart_items) && !empty($get_cart_items)){
					$exp_cart_items = array_filter(explode(" ",trim($get_cart_items)));
					
					$count_old_cart_items = 0;
					$count_new_cart_items = 0;
					$count_old_cart_items_amount = 0;
					$count_new_cart_items_amount = 0;
					foreach($exp_cart_items as $item_id){
						if(is_numeric($item_id) && ($item_id > 0)){
							$get_active_cart_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_api_marketplace_listings WHERE id='".$item_id."' && $status_statement"));
							
							if(isset($get_active_cart_details["api_type"])){
								$api_website = str_replace(["//www.","/","http:","https:"],"",$get_active_cart_details["api_website"]);
								$api_type = strtoupper(str_replace(["_","-"]," ",$get_active_cart_details["api_type"]));
								$product_description = checkTextEmpty($get_active_cart_details["description"]);
								$product_api_price = toDecimal($get_active_cart_details["price"], 2);
								$api_status_array = array(1 => "Public", 2 => "Private");
								$select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_base_url='".$api_website."' && api_type='".$get_active_cart_details["api_type"]."'");
								if(mysqli_num_rows($select_api_lists) > 0){
									if(mysqli_num_rows($select_api_lists) == 1){
										$api_status = "Installed";
										$count_old_cart_items += 1;
										$count_old_cart_items_amount += $get_active_cart_details["price"];
									}else{
										if(mysqli_num_rows($select_api_lists) > 1){
											$api_status = "Installed (Duplicated API)";
										}
									}
								}else{
									$api_status = '<span style="color: red;">new</span>';
									$count_new_cart_items += 1;
									$count_new_cart_items_amount += $get_active_cart_details["price"];
								}
								$api_status = $api_status." (".$api_status_array[$get_active_cart_details["status"]].")";
								echo 
									'<div class="container col-12 border rounded-2 px-5 py-3 lh-lg py-5">
  									<div class="d-flex flex-row justify-content-between mb-4">
  										<span style="user-select: auto;" id="" class="h4 fw-bold">'.strtoupper($api_type).' API</span>
  										<span style="user-select: auto;" id="" class="h4 fw-bold ms-auto">Price: N'.$product_api_price.'</span><br/>
										</div>
										<div style="user-select: auto;" id="" class="h6 fw-bold"><span style="user-select: auto; text-decoration: underline;">Description:</span> <span class="h6">'.$product_description.'</span></div><br/>
										<div style="user-select: auto;" id="" class="h6 fw-bold"><span style="user-select: auto; text-decoration: underline;">Status:</span> <span class="h6">'.$api_status.'</span></div><br/>
										<div style="user-select: auto;" id="" class="h6 fw-bold">API Website: <a target="_blank" href="https://'.$api_website.'" style="user-select: auto; text-decoration: underline;" class="h6 text-primary">https://'.$api_website.'</a></div>
										<div style="user-select: auto; text-decoration: underline; color: red;"  onclick="removeAPIFromCart(`'.$item_id.'`, `'.$get_logged_admin_details["id"].'`);" id="" class="h6 fw-bold text-danger float-end">Remove</div><br/>
									</div>';
							}else{
								//Unknown Item_id
							}
						}
					}
				}else{
					//No Item In Cart
					echo 
						'<div class="container d-flex flex-column align-items-center justify-items-center justify-content-center">
				  		<img alt="Logo" src="'.$web_http_host.'/asset/ooops.gif" style="user-select: auto; pointer-events: none; object-fit: contain; object-position: center;" class="col-5"/><br/>
							<span style="user-select: auto;" id="" class="tet-warning h3">Oooops!!! Cart Empty</span>
						</div>';
				}
           ?>
           <?php if($count_new_cart_items > 0){
           		if(($count_old_cart_items + $count_new_cart_items) == 1){
           			$item_singular_plural = "item";
           		}else{
           			if(($count_old_cart_items + $count_new_cart_items) > 1){
           				$item_singular_plural = "items";
           			}else{
           				$item_singular_plural = "item";
           			}
           		}
           ?>
           		<div style="user-select: auto; text-align: right;" class="container lh-lg mt-3">
            		<span style="user-select: auto;" class="h6"><?php echo $count_new_cart_items; ?> <span style="" class="fw-bold text-success">new</span>, <?php echo $count_old_cart_items; ?> <span style="" class="fw-bold text-danger">installed</span> <?php echo $item_singular_plural; ?></span><br>
            		<span style="user-select: auto;" class="text-success h6">Sub Total: N<?php echo toDecimal(($count_old_cart_items_amount + $count_new_cart_items_amount), 2); ?></span><br>
            		<span style="user-select: auto;" class="text-danger h6">Total Amount: N<?php echo toDecimal($count_new_cart_items_amount, 2); ?></span><br>
            		<form method="post" action="">
            			<button onclick="askPermissionSubBtn(this,'Are you sure you want to Checkout Cart?');" name="checkout-cart" type="button" style="user-select: auto;" class="btn btn-danger col-auto mt-2" >
            				CHECKOUT
            			</button><br/>
            		</form>
            	</div>
           <?php } ?>
          </div>
        </div>
      </section>
		
        
    <?php include("../func/bc-admin-footer.php"); ?>
    
</body>
</html>