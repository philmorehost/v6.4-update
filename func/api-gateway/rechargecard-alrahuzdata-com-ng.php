<?php
$recharge_service_provider_alter_code = array("mtn" => "mtn", "airtel" => "airtel", "glo" => "glo", "9mobile" => "9mobile");
if (in_array($product_name, array_keys($recharge_service_provider_alter_code))) {
	if ($product_name == "mtn") {
		$net_id = "1";
		$web_recharge_size_array = array("100" => "13", "200" => "2", "500" => "3");
	} else {
		if ($product_name == "airtel") {
			$net_id = "4";
			$web_recharge_size_array = array("100" => "10", "200" => "11", "500" => "12");
		} else {
			if ($product_name == "glo") {
				$net_id = "2";
				$web_recharge_size_array = array("100" => "4", "200" => "5", "500" => "6");
			} else {
				if ($product_name == "9mobile") {
					$net_id = "3";
					$web_recharge_size_array = array("100" => "7", "200" => "8");
				}
			}
		}
	}

	if (in_array($quantity, array_keys($web_recharge_size_array))) {
		$card_name = $isp . "_" . $quantity;
		$curl_url = "https://" . $api_detail["api_base_url"] . "/api/rechargepin/";
		$curl_request = curl_init($curl_url);
		curl_setopt($curl_request, CURLOPT_POST, true);
		curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
		$curl_http_headers = array(
			"Authorization: Token  " . $api_detail["api_key"],
			"Content-Type: application/json",
		);
		curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
		$bilalsadasub_reference = substr(str_shuffle("12345678901234567890"), 0, 15);
		$curl_postfields_data = json_encode(array("network" => $net_id, "quantity" => $qty_number, "network_amount" => $web_data_size_array[$quantity], "name_on_card" => $_SERVER["HTTP_HOST"]), true);
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
		$curl_result = curl_exec($curl_request);
		$curl_json_result = json_decode($curl_result, true);

		if (curl_errno($curl_request)) {
			$api_response = "failed";
			$api_response_text = 1;
			$api_response_description = "";
			$api_response_status = 3;
		}

		if (in_array($curl_json_result["Status"], array("successful", "pending"))) {

			$alrahuzdata_recharge_card_str = "";
			$alrahuzdata_recharge_card_arr_json = $rechargecardJSONObj["data_pin"];

			foreach ($alrahuzdata_recharge_card_arr_json as $each_card_json) {
				$decode_each_card = $each_card_json;
				$alrahuzdata_recharge_card_str .= trim($decode_each_card["fields"]["pin"]) . ",";
			}

			$purchased_pin_in_line_break = rtrim(",", $alrahuzdata_recharge_card_str);

			mysqli_query($connection_server, "INSERT INTO sas_card_purchaseds (vendor_id, reference, business_name, card_type, username, card_name, cards) VALUES ('" . $get_logged_user_details["vendor_id"] . "', '$reference', '$business_name', '$type', '" . $get_logged_user_details["username"] . "', '$card_name', '" . str_replace(",", ",", $curl_json_result["pin"]) . "')");
			$users_card_purchased = str_replace(",", ",", $curl_json_result["pin"]);
			$api_response = "successful";
			$api_response_reference = $curl_json_result["request-id"];
			$api_response_text = $curl_json_result["Status"];
			$api_response_description = "Transaction Successful";
			$api_response_status = 1;
		}

		if (!in_array($curl_json_result["Status"], array("success"))) {
			$api_response = "failed";
			$api_response_text = $curl_json_result["Status"];
			$api_response_description = "Transaction Failed";
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
curl_close($curl_request);
?>