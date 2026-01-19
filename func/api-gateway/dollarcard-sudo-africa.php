<?php
$card_service_provider_alter_code = array("mastercard" => "MasterCard", "visa" => "Visa", "verve" => "Verve");
if (in_array($product_name, array_keys($card_service_provider_alter_code))) {
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
		$explode_sudo_apikey = array_filter(explode(":", trim($api_detail["api_key"])));
		$curl_sudo_create_account_holder_url = "https://api.sandbox.sudo.cards/customers";
		$curl_sudo_create_account_holder_request = curl_init($curl_sudo_create_account_holder_url);
		curl_setopt($curl_sudo_create_account_holder_request, CURLOPT_POST, true);
		curl_setopt($curl_sudo_create_account_holder_request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_sudo_create_account_holder_request, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl_sudo_create_account_holder_request, CURLOPT_SSL_VERIFYPEER, false);
		$curl_sudo_create_account_holder_http_headers = array(
			"Authorization: Bearer " . $api_detail["api_key"],
			"Content-Type: application/json",
		);
		curl_setopt($curl_sudo_create_account_holder_request, CURLOPT_HTTPHEADER, $curl_sudo_create_account_holder_http_headers);
		$country_code_array = array("nigeria" => "NG");

		$accepted_kyc_array = array("nigerian_nin" => "NIN", "nigerian_bvn" => "BVN");
		$customer_identity = array("type" => $accepted_kyc_array[strtolower($get_card_holder_detail["kyc_mode"])], "number" => $get_card_holder_detail["kyc_id"]);
		// $customer_kyc = array("idFrontUrl" => $get_card_holder_detail["selfie_id_url"], "idBackUrl" => $get_card_holder_detail["selfie_id_url"]);
		$customer_kyc = array("idFrontUrl" => $get_card_holder_detail["selfie_id_url"], "idBackUrl" => $get_card_holder_detail["selfie_id_url"]);

		$curl_sudo_create_account_holder_postfields_data = json_encode(array("type" => "individual", "name" => $get_card_holder_detail["firstname"] . " " . $get_card_holder_detail["lastname"], "emailAddress" => $get_card_holder_detail["email"], "phoneNumber" => $get_card_holder_detail["phone_number"], "status" => "active", "individual" => array("firstName" => $get_card_holder_detail["firstname"], "lastName" => $get_card_holder_detail["lastname"], "otherNames" => "", "dob" => $get_card_holder_detail["dob"], "email" => $get_card_holder_detail["email"], "phoneNumber" => $get_card_holder_detail["phone_number"], "identity" => $customer_identity, "documents" => $customer_kyc), "billingAddress" => array("line1" => $get_card_holder_detail["address"], "line2" => $get_card_holder_detail["address"], "city" => ucwords(strtolower($get_card_holder_detail["city"])), "state" => ucwords(strtolower($get_card_holder_detail["state"])), "country" => $country_code_array[$get_card_holder_detail["country"]], "postalCode" => $get_card_holder_detail["zipcode"])), true);
		curl_setopt($curl_sudo_create_account_holder_request, CURLOPT_POSTFIELDS, $curl_sudo_create_account_holder_postfields_data);
		$curl_sudo_create_account_holder_result = curl_exec($curl_sudo_create_account_holder_request);
		$curl_sudo_create_account_holder_json_result = json_decode($curl_sudo_create_account_holder_result, true);
		curl_close($curl_sudo_create_account_holder_request);


		if (curl_errno($curl_sudo_create_account_holder_request)) {
			$api_response = "failed";
			$api_response_text = 1;
			$api_response_description = "";
			$api_response_status = 3;
		}
		// $curl_sudo_create_account_holder_json_result = '{ "resource": "create_card_holder", "status":"success", "data": { "card_holder_id":"GupJFog3iyv32fsu22uc282", "first_name": "John", "last_name": "Doe", "status": "active" } }';
		// $curl_sudo_create_account_holder_json_result = json_decode($curl_sudo_create_account_holder_json_result, true);
		fwrite(fopen("sudo-dollarcard.txt", "a++"), $curl_sudo_create_account_holder_postfields_data . "\n" . $curl_sudo_create_account_holder_result . "\n\n\n");


		if ($curl_sudo_create_account_holder_json_result["statusCode"] == 200 && $curl_sudo_create_account_holder_json_result["data"]["status"] == "active") {

			$curl_url = "https://api.sandbox.sudo.cards/cards";
			$curl_request = curl_init($curl_url);
			curl_setopt($curl_request, CURLOPT_POST, true);
			curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
			$curl_http_headers = array(
				"Authorization: Bearer " . $api_detail["api_key"],
				"Content-Type: application/json",
			);
			curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
			$legitdataway_reference = substr(str_shuffle("12345678901234567890"), 0, 15);
			$funding_id = array("mastercard" => "690eeec21fbdc03dc00945d7", "verve" => "", "visa" => "690eeec21fbdc03dc00945d7");
			$debit_id = array("mastercard" => "690eefac1fbdc03dc00946d1", "verve" => "", "visa" => "690eefac1fbdc03dc00946d1");
			$curl_postfields_data = json_encode(array("customerId" => $curl_sudo_create_account_holder_json_result["data"]["_id"], "fundingSourceId" => $funding_id[$product_name], "debitAccountId" => $debit_id[$product_name], "type" => "virtual", "brand" => $card_service_provider_alter_code[$product_name], "currency" => "USD", "issuerCountry" => "USA", "status" => "active", "amount" => 3, "spendingControls" => array("spendingLimits" => array(array("interval" => "monthly", "amount" => 100000)), "channels" => array("atm" => true, "pos" => true, "web" => true, "mobile" => true), "allowedCategories" => [], "blockedCategories" => [])), true);
			curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
			$curl_result = curl_exec($curl_request);
			$curl_json_result = json_decode($curl_result, true);
			curl_close($curl_request);

			// $curl_json_result = '{ "resource": "create_virtual_card", "status":"success", "data": { "id":"GupJFog3iyv32fsu22uc282", "card_holder_id":"73cdudc9wfdoavcyqisb", "currency": "NGN", "brand": "VISA", "type": "3D Secured Virtual Card", "pan": "4177-****-****-7075", "name_on_card": "STEVEN JOBS", "card_number": "4177009811117075", "expiry_month": "12", "expiry_year": "27", "cvv": "876", "pin": "1234", "address_street": "123 Johnson Street", "address_city": "Wuse", "address_country": "Nigeria", "postal_code": "910002", "status": "active" } }';
			// $curl_json_result = json_decode($curl_json_result, true);

			fwrite(fopen("sudo-dollarcard-card.txt", "a++"), $curl_postfields_data . "\n" . $curl_result . "\n\n\n");
			if ($curl_json_result["statusCode"] == 200 && $curl_json_result["data"]["status"] == "active") {

				$curl_fetch_card_url = "https://api.sandbox.sudo.cards/cards/" . $curl_json_result["data"]["_id"] . "?reveal=true";
				$curl_fetch_card_request = curl_init($curl_fetch_card_url);
				curl_setopt($curl_fetch_card_request, CURLOPT_HTTPGET, true);
				curl_setopt($curl_fetch_card_request, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl_fetch_card_request, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl_fetch_card_request, CURLOPT_SSL_VERIFYPEER, false);
				$curl_fetch_card_http_headers = array(
					"Authorization: Bearer " . $api_detail["api_key"],
					"Content-Type: application/json",
				);
				curl_setopt($curl_fetch_card_request, CURLOPT_HTTPHEADER, $curl_fetch_card_http_headers);
				$curl_fetch_card_result = curl_exec($curl_fetch_card_request);
				$curl_fetch_card_json_result = json_decode($curl_fetch_card_result, true);
				curl_close($curl_fetch_card_request);

				if (filter_var($callback_url, FILTER_VALIDATE_URL)) {
					$callback_url = trim($callback_url);
				} else {
					$callback_url = "";
				}

				if ($curl_fetch_card_json_result["statusCode"] == 200 && $curl_fetch_card_json_result["data"]["status"] == "active") {
					mysqli_query($connection_server, "INSERT INTO sas_virtualcard_purchaseds (vendor_id, reference, card_id, card_holder_id, card_type, username, fullname, card_name, card_cvv, card_validity, card_address, card_state, card_country, card_zipcode, card_number, card_pin, card_currency, card_brand, card_callback_url, api_website, card_status) VALUES ('" . $get_logged_user_details["vendor_id"] . "', '$reference', '" . $curl_fetch_card_json_result["data"]["_id"] . "', '" . $curl_json_result["data"]["program"] . "', '$type', '" . $get_logged_user_details["username"] . "', '" . $get_card_holder_detail["firstname"] . " " . $get_card_holder_detail["lastname"] . "', '$card_name', '" . $curl_fetch_card_json_result["data"]["cvv"] . "', '" . $curl_fetch_card_json_result["data"]["expiryMonth"] . "/" . $curl_fetch_card_json_result["data"]["expiryYear"] . "', '" . $get_card_holder_detail["address"] . "', '" . $get_card_holder_detail["state"] . "', '" . $get_card_holder_detail["country"] . "', '" . $get_card_holder_detail["zipcode"] . "', '" . $curl_fetch_card_json_result["data"]["number"] . "', '" . $curl_fetch_card_json_result["data"]["defaultPin"] . "', '" . $curl_json_result["data"]["currency"] . "', '$isp', '$callback_url', '" . $api_detail["api_base_url"] . "', 'active')");

					$api_response = "successful";
					$api_response_reference = $curl_fetch_card_json_result["data"]["_id"];
					$api_response_text = $curl_fetch_card_json_result["data"]["status"];
					$api_response_description = "Card Created Successful";
					$api_response_status = 1;
				} else {
					$api_response = "failed";
					$api_response_text = $curl_fetch_card_json_result["data"]["status"];
					$api_response_description = "Card Creation Failed";
					$api_response_status = 3;
				}
			} else {
				//Err: Could not connect
				$api_response = "failed";
				$api_response_text = "";
				$api_response_description = "Err: Could create card";
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