<?php

$crypto_service_provider_alter_code = array("ngn" => "ngn", "usd" => "usd", "gbp" => "gbp", "cad" => "cad", "eur" => "eur", "btc" => "btc", "eth" => "eth", "doge" => "doge", "usdt" => "usdt", "usdc" => "usdc", "sol" => "sol", "ada" => "ada", "trx" => "trx");
if (in_array($source_currency, array_keys($crypto_service_provider_alter_code))) {

	$curl_url = "https://api.spendjuice.com/exchange/swap";
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
	$curl_postfields_data = json_encode(array("amount" => $amount_to_convert, "source_currency" => strtoupper($source_currency), "target_currency" => strtoupper($target_currency), "quote_id" => $get_swap_info["api_swap_id"]), true);
	curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
	$curl_result = curl_exec($curl_request);
	$curl_json_result = json_decode($curl_result, true);

	fwrite(fopen("ajuicyswapcurrency.txt", "a++"), $curl_postfields_data . "\n" . $curl_result . "\n\n");

	if (curl_errno($curl_request)) {
		$api_response = "failed";
		$api_response_text = 1;
		$api_response_description = "Server not reachable";
		$api_response_status = 3;
	}

	if (isset($curl_json_result["data"]["id"]) && !empty($curl_json_result["data"]["id"]) && (!isset($curl_json_result["message"]))) {
		$api_response = "successful";
		$api_response_text = 1;
		$api_response_description = "Swap successfully";
		$api_response_reference = $curl_json_result["data"]["id"];
		$api_response_status = 1;
	} else {
		$api_response = "failed";
		$api_response_text = $curl_json_result["status"];
		$api_response_description = "Swap Failed";
		$api_response_status = 3;
	}
} else {
	//Service not available
	$api_response = "failed";
	$api_response_text = "Service not available";
	$api_response_description = "Service not available";
	$api_response_status = 3;
}
curl_close($curl_request);
?>