<?php

$crypto_service_provider_alter_code = array("ngn" => "ngn", "usd" => "usd", "gbp" => "gbp", "cad" => "cad", "eur" => "eur", "btc" => "btc", "eth" => "eth", "doge" => "doge", "usdt" => "usdt", "usdc" => "usdc", "sol" => "sol", "ada" => "ada", "trx" => "trx");
if (in_array($source_currency, array_keys($crypto_service_provider_alter_code))) {

	$curl_url = "https://api.spendjuice.com/wallets/transactions";
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
	$curl_postfields_data = json_encode(array("amount" => $discounted_amount, "description" => "Customer payin", "type" => "credit", "wallet_id" => $get_wallet_currency_details["api_wallet_id"]), true);
	curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
	$curl_result = curl_exec($curl_request);
	$curl_json_result = json_decode($curl_result, true);

	fwrite(fopen("ajuicycreditwallet.txt", "a++"), $curl_postfields_data . "\n" . $curl_result . "\n\n");
	curl_close($curl_request);

}
?>