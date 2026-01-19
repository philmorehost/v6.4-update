<?php

$crypto_service_provider_alter_code = array("ngn" => "ngn", "usd" => "usd", "gbp" => "gbp", "cad" => "cad", "eur" => "eur", "btc" => "btc", "eth" => "eth", "doge" => "doge", "usdt" => "usdt", "usdc" => "usdc", "sol" => "sol", "ada" => "ada", "trx" => "trx");
if (in_array($source_currency, array_keys($crypto_service_provider_alter_code))) {

	$curl_url = "https://api.spendjuice.com/exchange/quote?source_currency=".strtoupper($source_currency)."&target_currency=".strtoupper($target_currency);
	$curl_request = curl_init($curl_url);
	curl_setopt($curl_request, CURLOPT_HTTPGET, true);
	curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
	$curl_http_headers = array(
		"Authorization: " . $api_detail["api_key"],
		"Content-Type: application/json",
	);
	curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
	$curl_result = curl_exec($curl_request);
	$curl_json_result = json_decode($curl_result, true);

	fwrite(fopen("ajuicyexchange.txt", "a++"), $curl_postfields_data . "\n" . $curl_result . "\n\n");

	if (curl_errno($curl_request)) {
		$api_response = "failed";
		$api_response_text = 1;
		$api_response_description = "Server not reachable";
		$api_response_status = 3;
	}

	if (isset($curl_json_result["data"]["id"]) && !empty($curl_json_result["data"]["id"])) {
		$api_swap_id = $curl_json_result["data"]["id"];
		$api_response = "successful";
		$api_response_text = 1;
		$conversion_type = $curl_json_result["data"]["type"];
		$raw_api_exchange_rate = $curl_json_result["data"]["rate"];
		$refined_api_exchange_rate = substr($raw_api_exchange_rate, 0, (strlen($raw_api_exchange_rate) - 2)) .".".substr($raw_api_exchange_rate, (strlen($raw_api_exchange_rate) -2));
		$api_response_rate = $refined_api_exchange_rate;
		$api_response_description = "Exchange rate retrieved successfully";
		$api_response_status = 1;
	} else {
		$api_response = "failed";
		$api_response_text = 3;
		$api_response_description = "Exchange rate Failed";
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