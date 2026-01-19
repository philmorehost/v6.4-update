<?php

$curl_url = "https://api.sandbox.sudo.cards/cards/".$virtual_card_detail["card_id"]."/pin";
$curl_request = curl_init($curl_url);
curl_setopt($curl_request, CURLOPT_PUT, true);
curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
$curl_http_headers = array(
	"Authorization: Bearer " . $api_detail["api_key"],
	"Content-Type: application/json",
);
curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
$legitdataway_reference = substr(str_shuffle("12345678901234567890"), 0, 15);
$new_card_pin = substr(str_shuffle("1234567890"),0, 4);
$curl_postfields_data = json_encode(array("oldPin" => $virtual_card_detail["card_pin"], "newPin" => $new_card_pin), true);
curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
$curl_result = curl_exec($curl_request);
$curl_json_result = json_decode($curl_result, true);
curl_close($curl_request);

// $curl_json_result = '{ "resource": "change_card_pin", "status":"success", "data": { "id":"GupJFog3iyv32fsu22uc282", "new_pin": "12345678" } }';
// $curl_json_result = json_decode($curl_json_result, true);
fwrite(fopen("nairacard-pin.txt", "a++"), $curl_url."\n".$curl_postfields_data . "\n" . $curl_result . "\n\n\n");


if (in_array($curl_json_result["statusCode"], array(200))) {
	mysqli_query($connection_server, "UPDATE sas_virtualcard_purchaseds SET card_pin = '" . $new_card_pin . "' WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && reference= '$card_ref' && username='" . $get_logged_user_details["username"] . "'");

	$api_response = "successful";
	$api_response_reference = $curl_json_result["data"]["id"];
	$api_response_text = $curl_json_result["status"];
	$api_response_new_pin = $curl_json_result["data"]["new_pin"];
	$api_response_description = "Card PIN Updated Successful";
	$api_response_status = 1;
}

if (!in_array($curl_json_result["status"], array("success"))) {
	$api_response = "failed";
	$api_response_text = $curl_json_result["status"];
	$api_response_description = "Card PIN Update Failed";
	$api_response_status = 3;
}

?>