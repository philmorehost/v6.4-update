<?php
$exam_service_provider_alter_code = array("waec" => "waec", "neco" => "neco", "nabteb" => "nabteb", "jamb" => "jamb");
if (in_array($product_name, array_keys($exam_service_provider_alter_code))) {
	if ($product_name == "waec") {
		$web_exam_size_array = array("result_checker" => "1");
	} else {
		if ($product_name == "neco") {
			$web_exam_size_array = array("result_checker" => "2");
		} else {
			if ($product_name == "nabteb") {
				$web_exam_size_array = array("result_checker" => "3");
			} else {
				if ($product_name == "jamb") {
					$web_exam_size_array = array();
				}
			}
		}
	}

	if (in_array($quantity, array_keys($web_exam_size_array))) {
		$curl_url = "https://" . $api_detail["api_base_url"] . "/api/v1/exam-card/buy";
		$curl_request = curl_init($curl_url);
		curl_setopt($curl_request, CURLOPT_POST, true);
		curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
		$curl_http_headers = array("Authorization: Bearer " . $api_detail["api_key"], "Content-Type: application/json");
		curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
		$naijaresultpins_reference = substr(str_shuffle("12345678901234567890"), 0, 15);
		$curl_postfields_data = json_encode(array("card_type_id" => $web_exam_size_array[$quantity], "quantity" => 1), true);
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
		$curl_result = curl_exec($curl_request);
		$curl_json_result = json_decode($curl_result, true);

		fwrite(fopen("naijaexampins.txt", "a++"), $curl_result);
	
		if (curl_errno($curl_request)) {
			$api_response = "failed";
			$api_response_text = 1;
			$api_response_description = "";
			$api_response_status = 3;
		}

		if ($curl_json_result["status"] === true && in_array($curl_json_result["code"], array("000"))) {
			$api_response = "successful";
			$api_response_reference = $curl_json_result["reference"];
			$api_response_text = $curl_json_result["status"];
			$api_response_description = "Transaction Successful | PIN: " . $curl_json_result["cards"][0]["pin"].", Serial No: ".$curl_json_result["cards"][0]["serial_no"];
			$api_response_status = 1;
		}

		if (!in_array($curl_json_result["code"], array("000"))) {
			$api_response = "failed";
			$api_response_text = $curl_json_result["status"];
			$api_response_description = "Transaction Failed";
			$api_response_status = 3;
		}
	} else {
		//Exam size not available
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