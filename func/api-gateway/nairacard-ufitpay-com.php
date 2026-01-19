<?php
$data_service_provider_alter_code = array("mastercard" => "mastercard", "visa" => "visa", "verve" => "verve");
if (in_array($product_name, array_keys($data_service_provider_alter_code))) {
	if ($product_name == "mastercard") {
		$net_id = "1";
		$web_virtual_card_size_array = array("1" => "1");
	} else {
		if ($product_name == "visa") {
			$net_id = "2";
			$web_virtual_card_size_array = array("1" => "1");
		} else {
			if ($product_name == "verve") {
				$net_id = "3";
				$web_virtual_card_size_array = array("1" => "1");
			}
		}
	}

	if (in_array($quantity, array_keys($web_virtual_card_size_array))) {
		$card_name = $isp . "_" . $quantity;
		$explode_ufitpay_apikey = array_filter(explode(":", trim($api_detail["api_key"])));
		$curl_ufitpay_create_account_holder_url = "https://api.ufitpay.com/v1/create_card_holder";
		$curl_ufitpay_create_account_holder_request = curl_init($curl_ufitpay_create_account_holder_url);
		curl_setopt($curl_ufitpay_create_account_holder_request, CURLOPT_POST, true);
		curl_setopt($curl_ufitpay_create_account_holder_request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_ufitpay_create_account_holder_request, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl_ufitpay_create_account_holder_request, CURLOPT_SSL_VERIFYPEER, false);
		$curl_ufitpay_create_account_holder_http_headers = array(
			"Api-Key: " . $explode_ufitpay_apikey[0],
			"API-Token: " . $explode_ufitpay_apikey[1],
			"Content-Type: application/json",
		);
		curl_setopt($curl_ufitpay_create_account_holder_request, CURLOPT_HTTPHEADER, $curl_ufitpay_create_account_holder_http_headers);
		$curl_ufitpay_create_account_holder_postfields_data = json_encode(array("first_name" => $get_card_holder_detail["firstname"], "last_name" => $get_card_holder_detail["lastname"], "email" => $get_card_holder_detail["email"], "phone" => $get_card_holder_detail["phone_number"], "address" => $get_card_holder_detail["address"], "state" => $get_card_holder_detail["state"], "country" => $get_card_holder_detail["country"], "postal_code" => $get_card_holder_detail["zipcode"], "kyc_method" => strtoupper($get_card_holder_detail["kyc_mode"]), "bvn" => $get_card_holder_detail["kyc_id"], "selfie_image" => $get_card_holder_detail["selfie_id_url"], "id_image" => $get_card_holder_detail["selfie_id_url"], "id_number" => $get_card_holder_detail["kyc_id"]), true);
		curl_setopt($curl_ufitpay_create_account_holder_request, CURLOPT_POSTFIELDS, $curl_ufitpay_create_account_holder_postfields_data);
		$curl_ufitpay_create_account_holder_result = curl_exec($curl_ufitpay_create_account_holder_request);
		$curl_ufitpay_create_account_holder_json_result = json_decode($curl_ufitpay_create_account_holder_result, true);
		curl_close($curl_ufitpay_create_account_holder_request);
		if (curl_errno($curl_ufitpay_create_account_holder_request)) {
			$api_response = "failed";
			$api_response_text = 1;
			$api_response_description = "";
			$api_response_status = 3;
		}
		// $curl_ufitpay_create_account_holder_json_result = '{ "resource": "create_card_holder", "status":"success", "data": { "card_holder_id":"GupJFog3iyv32fsu22uc282", "first_name": "John", "last_name": "Doe", "status": "active" } }';
		// $curl_ufitpay_create_account_holder_json_result = json_decode($curl_ufitpay_create_account_holder_json_result, true);
		fwrite(fopen("nairacard.txt", "a++"), $curl_ufitpay_create_account_holder_result . "\n\n\n");


		if ($curl_ufitpay_create_account_holder_json_result["status"] == "success" && $curl_ufitpay_create_account_holder_json_result["data"]["status"] == "active") {
			$curl_url = "https://api.ufitpay.com/v1/create_virtual_card";
			$curl_request = curl_init($curl_url);
			curl_setopt($curl_request, CURLOPT_POST, true);
			curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
			$curl_http_headers = array(
				"Api-Key: " . $explode_ufitpay_apikey[0],
				"API-Token: " . $explode_ufitpay_apikey[1],
				"Content-Type: application/json",
			);
			curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
			$legitdataway_reference = substr(str_shuffle("12345678901234567890"), 0, 15);
			$curl_postfields_data = json_encode(array("card_currency" => "USD", "card_holder_id" => $curl_ufitpay_create_account_holder_json_result["data"]["card_holder_id"], "card_brand" => $data_service_provider_alter_code[$product_name], "funding_currency" => "USD", "callback_url" => $web_http_host . "/webhook/ufitpay-card-event.php"), true);
			curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
			$curl_result = curl_exec($curl_request);
			$curl_json_result = json_decode($curl_result, true);
			curl_close($curl_request);

			// $curl_json_result = '{ "resource": "create_virtual_card", "status":"success", "data": { "id":"GupJFog3iyv32fsu22uc282", "card_holder_id":"73cdudc9wfdoavcyqisb", "currency": "NGN", "brand": "VISA", "type": "3D Secured Virtual Card", "pan": "4177-****-****-7075", "name_on_card": "STEVEN JOBS", "card_number": "4177009811117075", "expiry_month": "12", "expiry_year": "27", "cvv": "876", "pin": "1234", "address_street": "123 Johnson Street", "address_city": "Wuse", "address_country": "Nigeria", "postal_code": "910002", "status": "active" } }';
			// $curl_json_result = json_decode($curl_json_result, true);

			if(filter_var($callback_url, FILTER_VALIDATE_URL)){
				$callback_url = trim($callback_url);
			}else{
				$callback_url = "";
			}

			if (in_array($curl_json_result["status"], array("success"))) {
				mysqli_query($connection_server, "INSERT INTO sas_virtualcard_purchaseds (vendor_id, reference, card_id, card_holder_id, card_type, username, fullname, card_name, card_cvv, card_validity, card_address, card_state, card_country, card_zipcode, card_number, card_pin, card_currency, card_brand, card_callback_url, api_website, card_status) VALUES ('" . $get_logged_user_details["vendor_id"] . "', '$reference', '" . $curl_json_result["data"]["id"] . "', '" . $curl_json_result["data"]["card_holder_id"] . "', '$type', '" . $get_logged_user_details["username"] . "', '" . $get_card_holder_detail["firstname"] . " " . $get_card_holder_detail["lastname"] . "', '$card_name', '" . $curl_json_result["data"]["cvv"] . "', '" . $curl_json_result["data"]["expiry_month"] . "/" . $curl_json_result["data"]["expiry_month"] . "', '" . $get_card_holder_detail["address"] . "', '" . $get_card_holder_detail["state"] . "', '" . $get_card_holder_detail["country"] . "', '" . $get_card_holder_detail["zipcode"] . "', '" . $curl_json_result["data"]["card_number"] . "', '" . $curl_json_result["data"]["pin"] . "', '" . $curl_json_result["data"]["currency"] . "', '$isp', '$callback_url', '" . $api_detail["api_base_url"] . "', 'active')");

				$api_response = "successful";
				$api_response_reference = $curl_json_result["data"]["id"];
				$api_response_text = $curl_json_result["status"];
				$api_response_description = "Card Created Successful";
				$api_response_status = 1;
			}

			if (!in_array($curl_json_result["status"], array("success"))) {
				$api_response = "failed";
				$api_response_text = $curl_json_result["status"];
				$api_response_description = "Card Creation Failed";
				$api_response_status = 3;
			}
		} else {
			//Err: Could not connect
			$api_response = "failed";
			$api_response_text = "";
			$api_response_description = "Err: Could not connect";
			$api_response_status = 3;
		}
	} else {
		//Data size not available
		$api_response = "failed";
		$api_response_text = "";
		$api_response_description = "";
		$api_response_status = 3;
	}
} else {
	//Service not available
	$api_response = "failed";
	$api_response_text = "";
	$api_response_description = "Service not available";
	$api_response_status = 3;
}
?>