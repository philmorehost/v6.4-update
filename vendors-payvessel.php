<?php session_start();
	include(__DIR__."/func/bc-connect.php");
	include(__DIR__."/func/bc-func.php");
	
	$catch_incoming_request = json_decode('{"transaction": {"date": "2024-05-16T11:26:41", "reference": "000013240516112628000071775648", "sessionid": "000013240516112628000071775648"}, "order": {"currency": "NGN", "amount": 550, "settlement_amount": 500.27, "fee": 1.3, "description": "Inbound Transfer From OMOTERE  EBENEZER LANRE/GTBank Plc to DATAGIFTING/5223660934-9Payment Service Bank"}, "customer": {"email": "v5datagiftingcomng-1-beebayads@gmail.com", "phone": "08035173259"}, "virtualAccount": {"virtualAccountNumber": "5223652096", "virtualBank": "120001"}, "sender": {"senderAccountNumber": "0106768610", "SenderBankCode": "058", "senderBankName": "GTBank Plc", "senderName": "OMOTERE  EBENEZER LANRE"}, "message": "Success", "code": "00"}', true);
	//$catch_incoming_request = json_decode(file_get_contents("php://input"),true);
	$payvessel_keys = mysqli_fetch_assoc(mysqli_query($connection_server,"SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='payvessel'"));
	
	//$payvessel_verify_transaction = json_decode(confirmPaymentDeposited("GET","https://api.payvessel.co/transaction/verify/".$catch_incoming_request["data"]["reference"],["Authorization: Bearer ".$payvessel_keys["secret_key"]],""),true);
	
	//Payvessel Line
	$payload = file_get_contents('php://input');
	$payvessel_signature = $_SERVER['HTTP_PAYVESSEL_HTTP_SIGNATURE'];
	//this line maybe be differ depends on your server
	//$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR']; 
	$ip_address = $_SERVER['REMOTE_ADDR']; 
	$secret = $payvessel_keys["secret_key"];
	$hashkey = hash_hmac('sha512', $payload, $secret);
	//Payvessel End
	
	$webhook = "\n PAYLOAD:".$payload."\n IP: ".$ip_address."\n HASH:".$hashkey."\n SIGNATURE:".$payvessel_signature;
	fwrite(fopen("upayvessel-webhook.txt", "a++"), $webhook);
	//echo $webhook;
	
	
	$customer_name = "";
	$customer_phone_number = $catch_incoming_request["customer"]["phone"];
	$customer_email = $catch_incoming_request["customer"]["email"];
	$amount_paid = floatval($catch_incoming_request['order']['amount']);
	$amount_deposited = floatval($catch_incoming_request['order']['settlement_amount']);
	$transaction_id = $catch_incoming_request["transaction"]["reference"];
	$virtual_accountno = $catch_incoming_request["virtualAccount"]["virtualAccountNumber"];
	$virtual_bankcode = $catch_incoming_request["virtualAccount"]["virtualBank"];
	$payment_method = "BANK TRANSFER";
	$exp_customer_detail = array_filter(explode("-", trim($customer_email)));
	$customer_id = $exp_customer_detail[1];
	$customer_mail = $exp_customer_detail[2];
	
	$check_if_banks_exists = mysqli_query($connection_server, "SELECT * FROM sas_vendor_banks WHERE vendor_id='$customer_id' && account_number='$virtual_accountno' && bank_code='$virtual_bankcode'");
	
	if(mysqli_num_rows($check_if_banks_exists) == 1){
		$get_payment_details = mysqli_fetch_array($check_if_banks_exists);
		$vendor_id = trim($get_payment_details["vendor_id"]);
		$reference = substr(str_shuffle("12345678901234567890"), 0, 15);
		$check_vendor_user_exists = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='$vendor_id'");
		if(mysqli_num_rows($check_vendor_user_exists) == 1){
			$get_logged_admin_details = mysqli_fetch_array($check_vendor_user_exists);
			$_SESSION["admin_session"] = $get_logged_admin_details["email"];
			
			$select_transaction_history = mysqli_query($connection_server,"SELECT * FROM sas_vendor_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' && reference='$transaction_id'");
			
			if(($payvessel_signature == $hashkey) && ($ip_address == "162.246.254.36") && ($catch_incoming_request["message"] == "Success")) {
				if(mysqli_num_rows($select_transaction_history) == 0){
					chargeVendor("credit", $_SESSION["admin_session"], "Wallet Credit", $transaction_id, $amount_paid, $amount_deposited, "Payvessel Wallet Credit - ".str_replace("_"," ",$payment_method), $_SERVER["HTTP_HOST"], "1");
					unset($_SESSION["admin_session"]);
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