<?php

$crypto_service_provider_alter_code = array("ngn" => "ngn", "usd" => "usd", "gbp" => "gbp", "cad" => "cad", "eur" => "eur", "btc" => "btc", "eth" => "eth", "doge" => "doge", "usdt" => "usdt", "usdc" => "usdc", "sol" => "sol", "ada" => "ada", "trx" => "trx");
if (in_array($currency, array_keys($crypto_service_provider_alter_code))) {
	$crypto_ledger_query = mysqli_query($connection_server, "SELECT * FROM sas_user_crypto_ledger_balance WHERE vendor_id = '" . $get_logged_user_details['vendor_id'] . "' AND username = '" . $get_logged_user_details['username'] . "' AND currency = '" . $currency . "'");
	if (empty($get_customer_holder_detail["api_customer_id"])) {
		$curl_url = "https://api.spendjuice.com/customers";
		$curl_request = curl_init($curl_url);
		curl_setopt($curl_request, CURLOPT_POST, true);
		curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
		$curl_http_headers = array(
			"Authorization: " . $api_detail["api_key"],
			"Content-Type: application/json",
		);
		curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
		$curl_postfields_data = json_encode(array("first_name" => $firstname, "last_name" => $lastname, "phone_number" => "+234" . substr($phone, 1, 11), "email" => $email, "billing_address" => array("line1" => "76 Edun Street", "city" => "ilorin", "state" => "kwara", "zip_code" => "250241", "country" => "NG"), "type" => "individual"), true);
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
		$curl_result = curl_exec($curl_request);
		$curl_json_result = json_decode($curl_result, true);

		if (curl_errno($curl_request)) {
			$api_response = "failed";
			$api_response_text = 1;
			$api_response_description = "Server not reachable";
			$api_response_status = 3;
		}

	} else {
		$curl_json_result = array("data" => ["id" => $get_customer_holder_detail["api_customer_id"]]);
	}
	//$curl_json_result = '{"data":{"id":"ed6435a3-6dd6-43d4-bfe1-639094f30a08","status":"active","type":"individual","email":"beebayads@gmail.com","account_id":"1adb89b0-1f4f-4711-b57a-bc78f709af80","phone_number":"+2348124232128","last_name":"Abdulrahaman","first_name":"Habeebullahi","billing_address":{"state":"kwara","country":"NG","line1":"76 Edun Street","city":"ilorin","zip_code":"250241"}}}';
	//$curl_json_result = json_decode($curl_json_result, true);

	fwrite(fopen("ajuicycus.txt", "a++"), $curl_postfields_data . "\n" . $curl_result . "\n\n");


	if (isset($curl_json_result["data"]["id"]) && !empty($curl_json_result["data"]["id"])) {
		$api_customer_id = $curl_json_result["data"]["id"];
		if (mysqli_num_rows($crypto_ledger_query) == 1) {
			mysqli_query($connection_server, "UPDATE sas_user_crypto_ledger_balance SET api_customer_id = '$api_customer_id' WHERE vendor_id = '" . $get_logged_user_details['vendor_id'] . "' AND username = '" . $get_logged_user_details['username'] . "' AND currency = '" . $currency . "'");

			mysqli_query($connection_server, "UPDATE sas_crypto_customer_holders SET api_customer_id = '$api_customer_id', api_website = '" . $api_detail["api_base_url"] . "' WHERE vendor_id = '" . $get_logged_user_details['vendor_id'] . "' AND username = '" . $get_logged_user_details['username'] . "' AND customer_id = '$customer_ref'");

		}

		if (mysqli_num_rows($crypto_ledger_query) == 1) {
			$wallet_info = mysqli_fetch_array($crypto_ledger_query);
		}

		if (!isset($wallet_info["api_wallet_id"]) || empty($wallet_info["api_wallet_id"])) {
			$curl_wallet_url = "https://api.spendjuice.com/wallets";
			$curl_wallet_request = curl_init($curl_wallet_url);
			curl_setopt($curl_wallet_request, CURLOPT_POST, true);
			curl_setopt($curl_wallet_request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_wallet_request, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_wallet_request, CURLOPT_SSL_VERIFYPEER, false);
			$curl_wallet_http_headers = array(
				"Authorization: " . $api_detail["api_key"],
				"Content-Type: application/json",
			);
			curl_setopt($curl_wallet_request, CURLOPT_HTTPHEADER, $curl_wallet_http_headers);
			$curl_wallet_postfields_data = json_encode(array("currency" => strtoupper($currency), "customer_id" => $api_customer_id), true);
			curl_setopt($curl_wallet_request, CURLOPT_POSTFIELDS, $curl_wallet_postfields_data);
			$curl_wallet_result = curl_exec($curl_wallet_request);
			$curl_wallet_json_result = json_decode($curl_wallet_result, true);

			if (curl_errno($curl_wallet_request)) {
				$api_response = "failed";
				$api_response_text = 1;
				$api_response_description = "Server not reachable";
				$api_response_status = 3;
			}
		} else {
			$curl_wallet_json_result = array("data" => ["id" => $wallet_info["api_wallet_id"]]);
		}
		//$curl_wallet_json_result = '{"data":{"id":"1a461eb0-c364-4b86-98d5-6287c4982636","status":"active","balance":0,"currency":"USDT","account_id":"1adb89b0-1f4f-4711-b57a-bc78f709af80","customer_id":"ed6435a3-6dd6-43d4-bfe1-639094f30a08","payment_methods":[]}}';
		//$curl_wallet_json_result = json_decode($curl_wallet_json_result, true);

		fwrite(fopen("ajuicywallet.txt", "a++"), $curl_wallet_result . "\n\n");

		if (isset($curl_wallet_json_result["data"]["id"]) && !empty($curl_wallet_json_result["data"]["id"])) {
			$api_wallet_id = $curl_wallet_json_result["data"]["id"];
			if (mysqli_num_rows($crypto_ledger_query) == 1) {
				mysqli_query($connection_server, "UPDATE sas_user_crypto_ledger_balance SET api_wallet_id = '$api_wallet_id' WHERE vendor_id = '" . $get_logged_user_details['vendor_id'] . "' AND username = '" . $get_logged_user_details['username'] . "' AND currency = '" . $currency . "'");
			}
			$curl_payment_method_url = "https://api.spendjuice.com/wallets/" . $api_wallet_id . "/payment-method";
			$curl_payment_method_request = curl_init($curl_payment_method_url);
			curl_setopt($curl_payment_method_request, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($curl_payment_method_request, CURLOPT_POST, true);
			curl_setopt($curl_payment_method_request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_payment_method_request, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_payment_method_request, CURLOPT_SSL_VERIFYPEER, false);
			$curl_payment_method_http_headers = array(
				"Authorization: " . $api_detail["api_key"],
				"Content-Type: application/json",
			);
			curl_setopt($curl_payment_method_request, CURLOPT_HTTPHEADER, $curl_payment_method_http_headers);
			$curl_payment_method_postfields_data = json_encode(array("id" => $api_wallet_id, "type" => "crypto_address"), true);
			curl_setopt($curl_payment_method_request, CURLOPT_POSTFIELDS, $curl_payment_method_postfields_data);
			$curl_payment_method_result = curl_exec($curl_payment_method_request);
			$curl_payment_method_json_result = json_decode($curl_payment_method_result, true);
			fwrite(fopen("ajuicypayment_method.txt", "a++"), $curl_payment_method_url . "\n" . $curl_payment_method_result . "\n\n");

			$curl_retrieve_wallet_url = "https://api.spendjuice.com/wallets/" . $api_wallet_id;
			$curl_retrieve_wallet_request = curl_init($curl_retrieve_wallet_url);
			curl_setopt($curl_retrieve_wallet_request, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($curl_retrieve_wallet_request, CURLOPT_HTTPGET, true);
			curl_setopt($curl_retrieve_wallet_request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_retrieve_wallet_request, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_retrieve_wallet_request, CURLOPT_SSL_VERIFYPEER, false);
			$curl_retrieve_wallet_http_headers = array(
				"Authorization: " . $api_detail["api_key"],
				"Content-Type: application/json",
			);
			curl_setopt($curl_retrieve_wallet_request, CURLOPT_HTTPHEADER, $curl_retrieve_wallet_http_headers);
			$curl_retrieve_wallet_result = curl_exec($curl_retrieve_wallet_request);
			$curl_retrieve_wallet_json_result = json_decode($curl_retrieve_wallet_result, true);
			fwrite(fopen("ajuicyretrieve_wallet.txt", "a++"), $curl_retrieve_wallet_url . "\n" . $curl_retrieve_wallet_result . "\n\n");

			if (isset($curl_retrieve_wallet_json_result["data"]["id"]) && !empty($curl_retrieve_wallet_json_result["data"]["id"]) && !empty($curl_retrieve_wallet_json_result["data"]["payment_methods"])) {
				foreach ($curl_retrieve_wallet_json_result["data"]["payment_methods"] as $payment_method) {
					if ($payment_method["currency"] === strtoupper($currency)) {
						$crypto_address = $payment_method["address"];
						$crypto_chain = $payment_method["chain"];

						if (mysqli_num_rows($crypto_ledger_query) == 1) {
							$crypto_wallet_id = str_shuffle(substr("abcdefghijklmnopqrstuvwxyz1234567890", 0, 15));
							mysqli_query($connection_server, "UPDATE sas_user_crypto_ledger_balance SET wallet_id = '$crypto_wallet_id', crypto_address = '$crypto_address', crypto_chain = '$crypto_chain', `status` = '1' WHERE vendor_id = '" . $get_logged_user_details['vendor_id'] . "' AND username = '" . $get_logged_user_details['username'] . "' AND currency = '" . $currency . "'");
						}

						$api_response = "successful";
						break; // Exit the loop once the correct currency is found
					}
				}
				$api_response_text = 1;
				$api_response_description = "Wallet created successfully";
				$api_response_status = 1;
			} else {
				$api_response = "failed";
				$api_response_text = $curl_json_result["status"];
				$api_response_description = "Wallet Payment method update failed";
				$api_response_status = 3;
			}
		} else {
			$api_response = "failed";
			$api_response_text = $curl_json_result["status"];
			$api_response_description = "Wallet creation Failed";
			$api_response_status = 3;
		}

	} else {
		$api_response = "failed";
		$api_response_text = $curl_json_result["status"];
		$api_response_description = "Account creation Failed";
		$api_response_status = 3;
	}
} else {
	//Service not available
	$api_response = "failed";
	$api_response_text = "Service not available";
	$api_response_description = "Service not available";
	$api_response_status = 3;
}

?>