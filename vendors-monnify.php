<?php session_start();
	include(__DIR__."/func/bc-connect.php");
	include(__DIR__."/func/bc-func.php");
	
	$catch_incoming_request = json_decode(file_get_contents("php://input"),true);
	
	$monnify_keys = mysqli_fetch_assoc(mysqli_query($connection_server,"SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='monnify'"));
	$monnifyApiUrl = "https://api.monnify.com/api/v1/auth/login";
	$monnifyAPILogin = curl_init($monnifyApiUrl);
	curl_setopt($monnifyAPILogin,CURLOPT_URL,$monnifyApiUrl);
	curl_setopt($monnifyAPILogin,CURLOPT_POST,true);
	curl_setopt($monnifyAPILogin,CURLOPT_RETURNTRANSFER,true);
	$monnifyLoginHeader = array("Authorization: Basic ".base64_encode($monnify_keys["public_key"].':'.$monnify_keys["secret_key"]),"Content-Type: application/json","Content-Length: 0");
	curl_setopt($monnifyAPILogin,CURLOPT_HTTPHEADER,$monnifyLoginHeader);
	curl_setopt($monnifyAPILogin, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($monnifyAPILogin, CURLOPT_SSL_VERIFYPEER, false);
	
	$GetMonnifyJSON = curl_exec($monnifyAPILogin);
	$monnifyJSONObj = json_decode($GetMonnifyJSON,true);
	
	$access_token = $monnifyJSONObj["responseBody"]["accessToken"];
	if($catch_incoming_request["eventData"] == true){
		$monnify_verify_transaction = json_decode(confirmPaymentDeposited("GET","https://api.monnify.com/api/v2/transactions/".urlencode($catch_incoming_request["eventData"]["transactionReference"]),["Authorization: Bearer ".$access_token],""),true);
	}else{
		$monnify_verify_transaction = json_decode(confirmPaymentDeposited("GET","https://api.monnify.com/api/v2/transactions/".urlencode($catch_incoming_request["transactionReference"]),["Authorization: Bearer ".$access_token],""),true);
	}
	
	if(($monnify_verify_transaction["responseBody"]["paymentStatus"] == "PAID") && (($catch_incoming_request["product"]["type"] == "RESERVED_ACCOUNT") OR ($catch_incoming_request["eventData"]["product"]["type"] == "RESERVED_ACCOUNT"))){
		if($catch_incoming_request["eventData"] == true){
			$customer_name = $catch_incoming_request["eventData"]["customer"]["name"];
			$customer_email = $catch_incoming_request["eventData"]["customer"]["email"];
			$amount_paid = $catch_incoming_request["eventData"]["totalPayable"];
			$amount_deposited = $catch_incoming_request["eventData"]["settlementAmount"];
			$transaction_id = $catch_incoming_request["eventData"]["transactionReference"];
			$vendor_customer_id = $catch_incoming_request["eventData"]["product"]["reference"];
			$payment_method = $catch_incoming_request["eventData"]["paymentMethod"];
			
		}else{
			$customer_name = $catch_incoming_request["customer"]["name"];
			$customer_email = $catch_incoming_request["customer"]["email"];
			$amount_paid = $catch_incoming_request["totalPayable"];
			$amount_deposited = $catch_incoming_request["settlementAmount"];
			$transaction_id = $catch_incoming_request["transactionReference"];
			$vendor_customer_id = $catch_incoming_request["product"]["reference"];
			$payment_method = $catch_incoming_request["paymentMethod"];
		}
		
		$check_if_banks_exists = mysqli_query($connection_server, "SELECT * FROM sas_vendor_banks WHERE reference='$vendor_customer_id' LIMIT 1");
		if(mysqli_num_rows($check_if_banks_exists) == 1){
			$get_payment_details = mysqli_fetch_array($check_if_banks_exists);
			$vendor_id = trim($get_payment_details["id"]);
			$reference = substr(str_shuffle("12345678901234567890"), 0, 15);
			$check_vendor_user_exists = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='$vendor_id'");
			if(mysqli_num_rows($check_vendor_user_exists) == 1){
				$get_logged_admin_details = mysqli_fetch_array($check_vendor_user_exists);
				$_SESSION["admin_session"] = $get_logged_admin_details["email"];

				$select_transaction_history = mysqli_query($connection_server,"SELECT * FROM sas_vendor_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' && reference='$transaction_id'");
				
				if(($catch_incoming_request["paymentStatus"] == "PAID") OR ($catch_incoming_request["eventData"]["paymentStatus"] == "PAID")){
					if(mysqli_num_rows($select_transaction_history) == 0){
						chargeVendor("credit", $_SESSION["admin_session"], "Wallet Credit", $transaction_id, $amount_paid, $amount_deposited, "Monnify Wallet Credit - ".str_replace("_"," ",$payment_method), $_SERVER["HTTP_HOST"], "1");
						unset($_SESSION["admin_session"]);
					}
				}
			}
		}
	}
	
	if(($monnify_verify_transaction["responseBody"]["paymentStatus"] == "PAID") && (($catch_incoming_request["product"]["type"] == "WEB_SDK") OR ($catch_incoming_request["eventData"]["product"]["type"] == "WEB_SDK"))){
		if($catch_incoming_request["eventData"] == true){
			$customer_name = $catch_incoming_request["eventData"]["customer"]["name"];
			$customer_email = $catch_incoming_request["eventData"]["customer"]["email"];
			$amount_paid = $catch_incoming_request["eventData"]["totalPayable"];
			$amount_deposited = $catch_incoming_request["eventData"]["settlementAmount"];
			$transaction_id = $catch_incoming_request["eventData"]["paymentReference"];
			$payment_method = $catch_incoming_request["eventData"]["paymentMethod"];
			
		}else{
			$customer_name = $catch_incoming_request["customer"]["name"];
			$customer_email = $catch_incoming_request["customer"]["email"];
			$amount_paid = $catch_incoming_request["totalPayable"];
			$amount_deposited = $catch_incoming_request["settlementAmount"];
			$transaction_id = $catch_incoming_request["paymentReference"];
			$payment_method = $catch_incoming_request["paymentMethod"];
			
		}
		
		$check_if_pre_payment_exists = mysqli_query($connection_server, "SELECT * FROM sas_vendor_payment_checkouts WHERE reference='$transaction_id'");
		if(mysqli_num_rows($check_if_pre_payment_exists) == 1){
			$get_payment_details = mysqli_fetch_array($check_if_pre_payment_exists);
			$vendor_id = trim($get_payment_details["vendor_id"]);
			$reference = substr(str_shuffle("12345678901234567890"), 0, 15);
			$check_vendor_user_exists = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='$vendor_id'");
			if(mysqli_num_rows($check_vendor_user_exists) == 1){
				$get_logged_admin_details = mysqli_fetch_array($check_vendor_user_exists);
				$_SESSION["admin_session"] = $get_logged_admin_details["email"];
				
				$select_transaction_history = mysqli_query($connection_server,"SELECT * FROM sas_vendor_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' && reference='$transaction_id'");
				
				if(($catch_incoming_request["paymentStatus"] == "PAID") OR ($catch_incoming_request["eventData"]["paymentStatus"] == "PAID")){
					if(mysqli_num_rows($select_transaction_history) == 0){
						chargeVendor("credit", $_SESSION["admin_session"], "Wallet Credit", $transaction_id, $amount_paid, $amount_deposited, "Monnify Wallet Credit - ".str_replace("_"," ",$payment_method), $_SERVER["HTTP_HOST"], "1");
						unset($_SESSION["admin_session"]);
					}
				}
			}
		}

	}

	function confirmPaymentDeposited($method,$url,$header,$json){
		$apiwalletBalance = curl_init($url);
		$apiwalletBalanceUrl = $url;
		curl_setopt($apiwalletBalance,CURLOPT_URL,$apiwalletBalanceUrl);
		curl_setopt($apiwalletBalance,CURLOPT_RETURNTRANSFER,true);
		if($method == "POST"){
			curl_setopt($apiwalletBalance,CURLOPT_POST,true);
		}
		
		if($method == "GET"){
		curl_setopt($apiwalletBalance,CURLOPT_HTTPGET,true);
		}
		
		if($header == true){
			curl_setopt($apiwalletBalance,CURLOPT_HTTPHEADER,$header);
		}
		if($json == true){
			curl_setopt($apiwalletBalance,CURLOPT_POSTFIELDS,$json);
		}
		curl_setopt($apiwalletBalance, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($apiwalletBalance, CURLOPT_SSL_VERIFYPEER, false);
		
		$GetAPIBalanceJSON = curl_exec($apiwalletBalance);
		return $GetAPIBalanceJSON;
	}
?>