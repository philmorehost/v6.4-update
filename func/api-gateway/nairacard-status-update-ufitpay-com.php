<?php
$card_status_array = array("active" => "active", "blocked" => "inactive");
$curl_url = "https://api.ufitpay.com/v1/update_card_status";
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
$curl_postfields_data = json_encode(array("id" => $virtual_card_detail["card_id"], "status" => $card_status_array[$card_status]), true);
curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
$curl_result = curl_exec($curl_request);
$curl_json_result = json_decode($curl_result, true);
curl_close($curl_request);

// $curl_json_result = '{ "resource": "update_card_status", "status":"success", "data": { "id":"GupJFog3iyv32fsu22uc282", "new_status": "inactive" } }';
// $curl_json_result = json_decode($curl_json_result, true);
// fwrite(fopen("nairacard.txt", "a++"), $curl_postfields_data . "\n" . $curl_result . "\n\n\n");


if (in_array($curl_json_result["status"], array("success"))) {
	mysqli_query($connection_server, "UPDATE sas_virtualcard_purchaseds SET card_status = '" . $card_status . "' WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && reference= '$card_ref' && username='" . $get_logged_user_details["username"] . "'");

	$api_response = "successful";
	$api_response_reference = $curl_json_result["data"]["id"];
	$api_response_text = $curl_json_result["status"];
	$api_response_new_card_status = $curl_json_result["data"]["new_status"];
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