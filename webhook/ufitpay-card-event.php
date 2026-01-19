<?php session_start();
include($_SERVER["DOCUMENT_ROOT"] . "/func/bc-connect.php");
include($_SERVER["DOCUMENT_ROOT"] . "/func/bc-func.php");

$catch_incoming_request = json_decode(file_get_contents("php://input"), true);

//Select Vendor Table
$select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {

	$vendor_id = trim($select_vendor_table["id"]);

	$trans_event = $catch_incoming_request["event"];
	$card_id = $catch_incoming_request["card_id"];
	$card_pan = $catch_incoming_request["card_pan"];

	if ($trans_event == "card_otp") {
		$card_request_source = $catch_incoming_request["request_source"];
		$card_otp = $catch_incoming_request["otp"];
		$card_reference = $catch_incoming_request["datetime"];

		$check_if_card_exists = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='$vendor_id' && card_id='$card_id'");

		if (mysqli_num_rows($check_if_card_exists) == 1) {
			$get_card_details = mysqli_fetch_array($check_if_card_exists);

			$user_id = $get_card_details["username"];
			$reference = substr(str_shuffle("12345678901234567890"), 0, 15);
			$api_reference = $card_reference;

			$check_vendor_user_exists = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='$vendor_id' && username='$user_id'");
			if (mysqli_num_rows($check_vendor_user_exists) == 1) {
				$get_logged_user_details = mysqli_fetch_array($check_vendor_user_exists);
				$_SESSION["user_session"] = $get_logged_user_details["username"];
				$check_otp_exists = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_transaction_otp WHERE vendor_id='$vendor_id' && username='$user_id' && api_reference='$api_reference'");
				if (mysqli_num_rows($check_otp_exists) == 0) {
					mysqli_query($connection_server, "INSERT INTO sas_virtualcard_transaction_otp (vendor_id, username, card_id, card_pan, otp, request_source, reference, api_reference, api_website) VALUES ('$vendor_id', '$user_id', '" . $get_card_details["card_id"] . "', '$card_pan', '$card_otp', '$card_request_source', '$reference', '$api_reference', 'ufitpay.com')");
				}
			}
		}
	}

	if ($trans_event == "card_transaction") {
		$card_amount = $catch_incoming_request["amount"];
		$card_fees = $catch_incoming_request["fees"];
		$card_type = $catch_incoming_request["type"];
		$card_narration = $catch_incoming_request["narration"];
		$card_balance = $catch_incoming_request["balance"];
		$card_reference = $catch_incoming_request["reference"];

		$check_if_card_exists = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='$vendor_id' && card_id='$card_id'");

		if (mysqli_num_rows($check_if_card_exists) == 1) {

			$get_card_details = mysqli_fetch_array($check_if_card_exists);

			$user_id = $get_card_details["username"];
			$reference = substr(str_shuffle("12345678901234567890"), 0, 15);
			$api_reference = $card_reference;

			$check_vendor_user_exists = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='$vendor_id' && username='$user_id'");
			if (mysqli_num_rows($check_vendor_user_exists) == 1) {
				$get_logged_user_details = mysqli_fetch_array($check_vendor_user_exists);
				$_SESSION["user_session"] = $get_logged_user_details["username"];
				$check_trans_exists = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='$vendor_id' && username='$user_id' && api_reference='$api_reference'");
				if (mysqli_num_rows($check_trans_exists) == 0) {
					mysqli_query($connection_server, "UPDATE sas_virtualcard_purchaseds SET card_balance = '$card_balance' WHERE vendor_id='$vendor_id' && username='$user_id' && card_id='$card_id'");
					mysqli_query($connection_server, "INSERT INTO sas_transactions (vendor_id, product_unique_id, type_alternative, reference, api_reference, username, amount, discounted_amount, balance_before, balance_after, `description`, mode, api_website, `status`) VALUES ('$vendor_id', '$card_pan', 'Virtual Card " . ucwords($card_type) . "', '$reference', '$api_reference', '$user_id', '$card_amount', '$card_amount', '" . $get_card_details["card_balance"] . "', '$card_balance', '$card_narration', 'API', 'ufitpay.com', '1')");
				}
			}
		}
	}

}

function confirmPaymentDeposited($method, $url, $header, $json)
{
	$apiwalletBalance = curl_init($url);
	$apiwalletBalanceUrl = $url;
	curl_setopt($apiwalletBalance, CURLOPT_URL, $apiwalletBalanceUrl);
	curl_setopt($apiwalletBalance, CURLOPT_RETURNTRANSFER, true);
	if ($method == "POST") {
		curl_setopt($apiwalletBalance, CURLOPT_POST, true);
	}

	if ($method == "GET") {
		curl_setopt($apiwalletBalance, CURLOPT_HTTPGET, true);
	}

	if ($header == true) {
		curl_setopt($apiwalletBalance, CURLOPT_HTTPHEADER, $header);
	}
	if ($json == true) {
		curl_setopt($apiwalletBalance, CURLOPT_POSTFIELDS, $json);
	}
	curl_setopt($apiwalletBalance, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($apiwalletBalance, CURLOPT_SSL_VERIFYPEER, false);

	$GetAPIBalanceJSON = curl_exec($apiwalletBalance);
	return $GetAPIBalanceJSON;
}
?>