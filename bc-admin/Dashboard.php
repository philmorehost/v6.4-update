<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start([
    'cookie_lifetime' => 286400,
	'gc_maxlifetime' => 286400,
]);
    include("../func/bc-admin-config.php");

    $select_vendor_super_admin_status_message = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_status_messages");
    if(mysqli_num_rows($select_vendor_super_admin_status_message) == 1){
	$get_vendor_super_admin_status_message = mysqli_fetch_array($select_vendor_super_admin_status_message);
	if(!isset($_SESSION["product_purchase_response"]) && isset($_SESSION["admin_session"])){
		$vendor_super_admin_status_message_template_encoded_text_array = array("{firstname}" => $get_logged_admin_details["firstname"]);
		foreach($vendor_super_admin_status_message_template_encoded_text_array as $array_key => $array_val){
			$vendor_super_admin_status_message_template_text = str_replace($array_key, $array_val, $get_vendor_super_admin_status_message["message"]);
		}
		$_SESSION["product_purchase_response"] = str_replace("\n","<br/>",$vendor_super_admin_status_message_template_text);
	}
    }

    if(isset($_POST["pay-bill"])){
        $purchase_method = "web";
        $purchase_method = strtoupper($purchase_method);
        $purchase_method_array = array("WEB");

        if(in_array($purchase_method, $purchase_method_array)){
            if($purchase_method === "WEB"){
                $bill_id = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["bill-id"]))));
            }

            if(!empty($bill_id)){
                if(is_numeric($bill_id)){
                    $get_bill_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_billings WHERE id='".$bill_id."'");
                    if(mysqli_num_rows($get_bill_details) == 1){
			$check_if_bill_is_paid = mysqli_query($connection_server, "SELECT * FROM sas_vendor_paid_bills WHERE vendor_id='".$get_logged_admin_details["id"]."' && bill_id='".$bill_id."'");
			if(mysqli_num_rows($check_if_bill_is_paid) == 0){
				$bill_amount = mysqli_fetch_array($get_bill_details);
				if(!empty($bill_amount["amount"]) && is_numeric($bill_amount["amount"]) && ($bill_amount["amount"] > 0)){
				if(!empty(vendorBalance(1)) && is_numeric(vendorBalance(1)) && (vendorBalance(1) > 0)){
					$amount = $bill_amount["amount"];
					$discounted_amount = $amount;
					$type_alternative = ucwords($bill_amount["bill_type"]);
					$reference = substr(str_shuffle("12345678901234567890"), 0, 15);
					$description = ucwords(checkTextEmpty($bill_amount["description"])." - Bill charges");
					$status = 1;

					$debit_vendor = chargeVendor("debit", $bill_amount["bill_type"], $type_alternative, $reference, $amount, $discounted_amount, $description, $_SERVER["HTTP_HOST"], $status);
					if($debit_vendor === "success"){
					$add_vendor_paid_bill_details = mysqli_query($connection_server, "INSERT INTO sas_vendor_paid_bills (vendor_id, bill_id, bill_type, description, amount, starting_date, ending_date) VALUES ('".$get_logged_admin_details["id"]."', '".$bill_amount["id"]."', '".$bill_amount["bill_type"]."', '".$bill_amount["description"]."', '$amount', '".$bill_amount["starting_date"]."','".$bill_amount["ending_date"]."')");
					if($add_vendor_paid_bill_details == true){
						//Account ... Bill Successfully
						$json_response_array = array("desc" => "Account ".ucwords($bill_amount["bill_type"])." Bill Successfully");
						$json_response_encode = json_encode($json_response_array,true);
					}else{
						$reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
						chargeVendor("credit", $bill_amount["bill_type"], "Refund", $reference_2, $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $_SERVER["HTTP_HOST"], "1");
						//Bill Failed, Contact Admin
						$json_response_array = array("desc" => "Bill Failed, Contact Admin");
						$json_response_encode = json_encode($json_response_array,true);
					}
					}else{
					//Insufficient Fund
					$json_response_array = array("desc" => "Insufficient Fund");
					$json_response_encode = json_encode($json_response_array,true);
					}
				}else{
					//Balance is LOW
					$json_response_array = array("desc" => "Balance is LOW");
					$json_response_encode = json_encode($json_response_array,true);
				}
				}else{
				//Pricing Error, Contact Admin
				$json_response_array = array("desc" => "Pricing Error, Contact Admin");
				$json_response_encode = json_encode($json_response_array,true);
				}
                        }else{
				//Bill Has Already Been Paid
				$json_response_array = array("desc" => "Bill Has Already Been Paid");
				$json_response_encode = json_encode($json_response_array,true);
                        }
                    }else{
                        //Error: Billing Details Not Exists, Contact Admin
                        $json_response_array = array("desc" => "Error: Billing Details Not Exists, Contact Admin");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }else{
                    //Non-numeric Bill ID
                    $json_response_array = array("desc" => "Non-numeric Bill ID");
                    $json_response_encode = json_encode($json_response_array,true);
                }
            }else{
                //Bill Field Empty
                $json_response_array = array("desc" => "Bill Field Empty");
                $json_response_encode = json_encode($json_response_array,true);
            }
        }
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }


	if((!empty($get_logged_admin_details["bank_code"]) && is_numeric($get_logged_admin_details["bank_code"]) && !empty($get_logged_admin_details["bvn"]) && is_numeric($get_logged_admin_details["bvn"]) && strlen($get_logged_admin_details["bvn"]) == 11) || (!empty($get_logged_admin_details["bank_code"]) && is_numeric($get_logged_admin_details["bank_code"]) && !empty($get_logged_admin_details["nin"]) && is_numeric($get_logged_admin_details["nin"]) && strlen($get_logged_admin_details["nin"]) == 11)){
		$virtual_account_vaccount_err = "";
		if((!empty($get_logged_admin_details["bvn"]) && is_numeric($get_logged_admin_details["bvn"]) && strlen($get_logged_admin_details["bvn"]) == 11) && (!empty($get_logged_admin_details["nin"]) && is_numeric($get_logged_admin_details["nin"]) && strlen($get_logged_admin_details["nin"]) == 11)){
			$verification_type = 1;
			$bvn_nin_monnify_account_creation = '"bvn" => $get_logged_admin_details["bvn"], "nin" => $get_logged_admin_details["nin"]';
			$bvn_nin_payvessel_account_creation = '"bvn" => $get_logged_admin_details["bvn"]';
		}else{
			if((!empty($get_logged_admin_details["bvn"]) && is_numeric($get_logged_admin_details["bvn"]) && strlen($get_logged_admin_details["bvn"]) == 11)){
				$verification_type = 1;
				$bvn_nin_monnify_account_creation = '"bvn" => $get_logged_admin_details["bvn"]';
				$bvn_nin_payvessel_account_creation = '"bvn" => $get_logged_admin_details["bvn"]';
			}else{
				if((!empty($get_logged_admin_details["nin"]) && is_numeric($get_logged_admin_details["nin"]) && strlen($get_logged_admin_details["nin"]) == 11)){
					$verification_type = 2;
					$bvn_nin_monnify_account_creation = '"nin" => $get_logged_admin_details["nin"]';
				}
			}
		}

		$registered_virtual_bank_arr = array();
		$virtual_bank_code_arr = array("232", "035", "50515", "120001");
		if(is_array(getVendorVirtualBank()) == true){
			foreach(getVendorVirtualBank() as $bank_json){
				$bank_json = json_decode($bank_json, true);
				array_push($registered_virtual_bank_arr, $bank_json["bank_code"]);
			}
		}
		if((getVendorVirtualBank() == false) || ((is_array(getVendorVirtualBank()) == true) && (!empty(array_diff($virtual_bank_code_arr, $registered_virtual_bank_arr))))){
		//Monnify
		$get_monnify_access_token = json_decode(getVendorMonnifyAccessToken(), true);
		if($get_monnify_access_token["status"] == "success"){

			//Check If Monnify Virtual Account Exists
			$admin_monnify_account_reference = md5($_SERVER["HTTP_HOST"]."-".$get_logged_admin_details["id"]."-".$get_logged_admin_details["email"]);
			$get_monnify_reserve_account = json_decode(makeMonnifyRequest("get", $get_monnify_access_token["token"], "api/v2/bank-transfer/reserved-accounts/".$admin_monnify_account_reference, ""), true);
			if($get_monnify_reserve_account["status"] == "success"){
				$monnify_reserve_account_response = json_decode($get_monnify_reserve_account["json_result"], true);
				foreach($monnify_reserve_account_response["responseBody"]["accounts"] as $monnify_accounts_json){
					if(in_array($monnify_accounts_json["bankCode"], array("232", "035", "50515"))){

                        addVendorVirtualBank($admin_monnify_account_reference, $monnify_accounts_json["bankCode"], $monnify_accounts_json["bankName"], $monnify_accounts_json["accountNumber"], $monnify_reserve_account_response["responseBody"]["accountName"]);
					}
				}
			}else{
				$select_monnify_gateway_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='monnify' LIMIT 1");
				$monnify_create_reserve_account_array = array("accountReference" => $admin_monnify_account_reference, "accountName" => $get_logged_admin_details["firstname"]." ".$get_logged_admin_details["lastname"]." ".$get_logged_admin_details["othername"], "currencyCode" => "NGN", "contractCode" => $select_monnify_gateway_details["encrypt_key"], "customerEmail" => $get_logged_admin_details["email"], $bvn_nin_monnify_account_creation, "getAllAvailableBanks" => false, "preferredBanks" => ["232", "035", "50515", "058"]);
				makeMonnifyRequest("post", $get_monnify_access_token["token"], "api/v2/bank-transfer/reserved-accounts", $monnify_create_reserve_account_array);
				//$virtual_account_vaccount_err .= '<span class="color-4">Virtual Account Created Successfully</span>';
			}
		}else{
			if($get_monnify_access_token["status"] == "failed"){
				//$virtual_account_vaccount_err .= '<span class="color-4">'.$get_monnify_access_token["message"].'</span>';
			}
		}

		//Payvessel
		if((!empty($get_logged_admin_details["bvn"]) && is_numeric($get_logged_admin_details["bvn"]) && strlen($get_logged_admin_details["bvn"]) == 11)){
		$get_payvessel_access_token = json_decode(getVendorPayvesselAccessToken(), true);
		if($get_payvessel_access_token["status"] == "success"){
			$select_payvessel_gateway_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='payvessel' LIMIT 1");
			$admin_payvessel_account_reference = str_replace([".","-",":"], "", $_SERVER["HTTP_HOST"])."-".$get_logged_admin_details["id"]."-".$get_logged_admin_details["email"];
			$payvessel_create_reserve_account_array = array("email" => $admin_payvessel_account_reference, "name" => $get_logged_admin_details["firstname"]." ".$get_logged_admin_details["lastname"], "phoneNumber" => $get_logged_admin_details["phone_number"], $bvn_nin_payvessel_account_creation, "businessid" => $select_payvessel_gateway_details["encrypt_key"], "bankcode" => ["101", "120001"], "account_type" => "STATIC");
			$get_payvessel_reserve_account = json_decode(makePayvesselRequest("post", $get_payvessel_access_token["token"], "api/external/request/customerReservedAccount/", $payvessel_create_reserve_account_array), true);

			if($get_payvessel_reserve_account["status"] == "success"){
				$payvessel_reserve_account_response = json_decode($get_payvessel_reserve_account["json_result"], true);

				foreach($payvessel_reserve_account_response["banks"] as $payvessel_accounts_json){

					addVendorVirtualBank($payvessel_accounts_json["trackingReference"], $payvessel_accounts_json["bankCode"], $payvessel_accounts_json["bankName"], $payvessel_accounts_json["accountNumber"], $payvessel_accounts_json["accountName"]);
				}
				//$virtual_account_vaccount_err .= '<span class="color-4">Virtual Account Created Successfully</span>';
			}

			if($payvessel_reserve_account_response["status"] == "failed"){
				//$virtual_account_vaccount_err .= '<span class="color-4">'.$get_payvessel_access_token["message"].'</span>';
			}
		}else{
			if($get_payvessel_access_token["status"] == "failed"){
				//$virtual_account_vaccount_err .= '<span class="color-4">'.$get_payvessel_access_token["message"].'</span>';
			}
		}
		}
		}else{
			foreach(getVendorVirtualBank() as $monnify_accounts_json){
				$monnify_accounts_json = json_decode($monnify_accounts_json, true);
				if(in_array($monnify_accounts_json["bank_code"], array("232", "035", "50515", "058", "101", "120001"))){

				}
			}
		}
	}else{
		if(empty($get_logged_admin_details["bank_code"])){
			//$virtual_account_vaccount_err .= '<span class="color-4">Incomplete Bank Details, Update Your Bank Details In Account Settings</span><br/>';
		}else{
			if(!is_numeric($get_logged_admin_details["bank_code"])){
				//$virtual_account_vaccount_err .= '<span class="color-4">Non-numeric Bank Code</span><br/>';
			}else{
				if(empty($get_logged_admin_details["bvn"])){
					//$virtual_account_vaccount_err .= '<span class="color-4">Update BVN if neccessary</span><br/>';
				}else{
					if(!is_numeric($get_logged_admin_details["bvn"])){
						//$virtual_account_vaccount_err .= '<span class="color-4">Non-numeric BVN</span><br/>';
					}else{
						if(strlen($get_logged_admin_details["bvn"]) !== 11){
							//$virtual_account_vaccount_err .= '<span class="color-4">BVN must be 11 digit long</span><br/>';
						}else{
							if(empty($get_logged_admin_details["nin"])){
								//$virtual_account_vaccount_err .= '<span class="color-4">Update NIN if neccessary</span><br/>';
							}else{
								if(!is_numeric($get_logged_admin_details["nin"])){
									//$virtual_account_vaccount_err .= '<span class="color-4">Non-numeric NIN</span><br/>';
								}else{
									if(strlen($get_logged_admin_details["nin"]) !== 11){
										//$virtual_account_vaccount_err .= '<span class="color-4">NIN must be 11 digit long</span>';
									}
								}
							}
						}
					}
				}
			}
		}

	}
?>
<!DOCTYPE html>
<head>
    <title>Admin Dashboard | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
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
    .info-card.card-blue { background-color: #eef7ff; }
    .info-card.card-red { background-color: #fceeed; }
    .info-card.card-green { background-color: #eefcef; }
    .info-card.card-yellow { background-color: #fff9e6; }
    .info-card {
        border-radius: 15px;
    }
    .shadow {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    }
  </style>

</head>
<body>
    <?php include("../func/bc-admin-header.php"); ?>

	<div class="pagetitle">
      <h1>DASHBOARD</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
    <div class="row">

        <!-- Total Users Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card info-card card-blue shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Users <span>| All Time</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?php
                                $get_total_users = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_logged_admin_details["id"]."'");
                                echo mysqli_num_rows($get_total_users);
                            ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- End Total Users Card -->

        <!-- Balance Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card info-card card-red shadow">
                <div class="card-body">
                    <h5 class="card-title">Balance <span>| Current</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="ps-3">
                            <h6>₦<?php echo toDecimal($get_logged_admin_details["balance"], "2"); ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- End Balance Card -->

        <!-- Total Deposit Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card info-card card-green shadow">
                <div class="card-body">
                    <h5 class="card-title">Deposit <span>| Total</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div class="ps-3">
                            <h6>₦<?php
                                $all_admin_credit_transaction = 0;
                                $get_all_admin_credit_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' && (type_alternative LIKE '%credit%' OR type_alternative LIKE '%received%' OR type_alternative LIKE '%commission%')");
                                if(mysqli_num_rows($get_all_admin_credit_transaction_details) >= 1){
                                    while($transaction_record = mysqli_fetch_assoc($get_all_admin_credit_transaction_details)){
                                        $all_admin_credit_transaction += $transaction_record["discounted_amount"];
                                    }
                                }
                                echo toDecimal($all_admin_credit_transaction, 2);
                            ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- End Total Deposit Card -->

        <!-- Total Spent Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card info-card card-yellow shadow">
                <div class="card-body">
                    <h5 class="card-title">Spent <span>| Total</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-cart-x"></i>
                        </div>
                        <div class="ps-3">
                            <h6>₦<?php
                                $all_admin_debit_transaction = 0;
                                $get_all_admin_debit_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' && (type_alternative NOT LIKE '%credit%' && type_alternative NOT LIKE '%refund%' && type_alternative NOT LIKE '%received%' && type_alternative NOT LIKE '%commission%' && status NOT LIKE '%3%')");
                                if(mysqli_num_rows($get_all_admin_debit_transaction_details) >= 1){
                                    while($transaction_record = mysqli_fetch_assoc($get_all_admin_debit_transaction_details)){
                                        $all_admin_debit_transaction += $transaction_record["discounted_amount"];
                                    }
                                }
                                echo toDecimal($all_admin_debit_transaction, 2);
                            ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- End Total Spent Card -->

    </div>
</section>

        <!-- Row -->
        <div class="row">
          <!-- Subscription Card -->
          <div class="col-12 col-lg-6">
            <div class="card info-card sales-card p-3">
              <div class="card-body">
                <h5 class="card-title text-center">SUBSCRIPTION</h5>
                <?php
                  $expiry_date = $get_logged_admin_details['expiry_date'];
                  $status_color = 'text-secondary';
                  $status_text = 'No Subscription';
                  $expiry_text = 'N/A';

                  if ($expiry_date) {
                      $today = new DateTime();
                      $expiry = new DateTime($expiry_date);
                      if ($expiry < $today) {
                          $status_color = 'text-danger';
                          $status_text = 'Expired';
                      } else {
                          $status_color = 'text-success';
                          $status_text = 'Active';
                      }
                      $expiry_text = date('F j, Y', strtotime($expiry_date));
                  }
                ?>
                <div class="text-center mb-3">
                    <p class="mb-1">Status: <span class="fw-bold <?php echo $status_color; ?>"><?php echo $status_text; ?></span></p>
                    <p class="mb-0">Expires on: <span class="fw-bold"><?php echo $expiry_text; ?></span></p>
                </div>
                <a href="RenewSubscription.php" class="btn btn-primary w-100">
                  <?php echo ($status_text === 'Active' || $status_text === 'Expired') ? 'Renew / Upgrade' : 'Subscribe Now'; ?>
                </a>
              </div>
            </div>
          </div>

          <!-- Bill Payment Card -->
          <div class="col-12 col-lg-6">
            <div class="card info-card sales-card p-3">
              <div class="card-body">
                <h5 class="card-title text-center">BILL PAYMENT</h5>
                <form method="post" action="">
                  <select style="text-align: center;" name="bill-id" class="form-select mb-3" required>
                    <option value="" default hidden selected>Choose Bill</option>
                    <?php
                      $get_active_billing_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_billings WHERE date >= '".$get_logged_admin_details["reg_date"]."' ORDER BY date DESC");
                      if(mysqli_num_rows($get_active_billing_details) >= 1){
                        while($active_billing = mysqli_fetch_assoc($get_active_billing_details)){
                          $get_paid_bill_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_paid_bills WHERE vendor_id='".$get_logged_admin_details["id"]."' && bill_id='".$active_billing["id"]."'");
                          if(mysqli_num_rows($get_paid_bill_details) == 0){
                            echo '<option value="'.$active_billing["id"].'">'.$active_billing["bill_type"].' @ N'.toDecimal($active_billing["amount"], 2).' (Starts: '.formDateWithoutTime($active_billing["starting_date"]).', Ends: '.formDateWithoutTime($active_billing["ending_date"]).')</option>';
                          }
                        }
                      }
                    ?>
                  </select>
                  <button name="pay-bill" type="submit" class="btn btn-success w-100">Pay Bill</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Row End -->

    <!-- Row -->
    <div class="col-12">
      <!-- Col -->
      <div class="col-12 col-lg-12">
        <div class="card info-card sales-card p-3">
            <div class="card-body">
                <h5 class="card-title">Auto Funding</h5>
                <div class="d-flex overflow-auto">
                    <?php
                      foreach (getVendorVirtualBank() as $bank_accounts_json) {
                          $bank_accounts_json = json_decode($bank_accounts_json, true);
                          if (in_array($bank_accounts_json["bank_code"], array("110072", "232", "035", "50515", "120001", "100039"))) {
                              echo '
                                  <div class="card me-3 shadow-sm" style="min-width: 300px; border-radius: 12px;">
                                      <div class="card-header bg-success text-white">
                                          <h5 class="card-title text-white mb-0">' . strtoupper($bank_accounts_json["account_name"]) . '</h5>
                                      </div>
                                      <div class="card-body bg-light">
                                          <div class="d-flex justify-content-between align-items-center mt-3">
                                              <div>
                                                  <p class="mb-0 text-success fw-bold">' . strtoupper($bank_accounts_json["bank_name"]) . '</p>
                                                  <h4 class="mb-0 text-success">' . $bank_accounts_json["account_number"] . '
                                                      <span onclick="copyAccount(\'' . $bank_accounts_json["account_number"] . '\');" class="p-1 card-icon rounded-circle" style="cursor: pointer;">
                                                          <i title="Copy Account Number" class="bi bi-copy h5 text-success"></i>
                                                      </span>
                                                  </h4>
                                              </div>
                                              <div class="text-end">
                                                  <p class="mb-0 text-success">Charges</p>
                                                  <h4 class="text-success">₦50</h4>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="card-footer bg-white border-0 text-center">
                                          <a href="#" class="text-success fw-bold text-decoration-none">Automated Funding</a>
                                      </div>
                                  </div>';
                          }
                      }
                    ?>
                </div>
            </div>
        </div>
      </div>
      <!-- Col End -->

    </div>
      <!-- Row End -->
      </div>
    </section>
        <?php include("../func/admin-short-trans.php"); ?>
    <?php include("../func/bc-admin-footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function copyAccount(accountNumber) {
        navigator.clipboard.writeText(accountNumber).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Account number copied: ' + accountNumber,
                timer: 2000,
                showConfirmButton: false
            });
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }
    </script>
</body>
</html>
