<?php session_start();
include(__DIR__ . "/func/bc-connect.php");
include(__DIR__ . "/func/bc-func.php");

$catch_incoming_request = json_decode(file_get_contents("php://input"), true);

//Select Vendor Table
$select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {
	$fincra_keys = mysqli_fetch_assoc(mysqli_query($connection_server, "SELECT * FROM sas_payment_gateways WHERE vendor_id='" . $select_vendor_table["id"] . "' && gateway_name='fincra'"));

	$customer_name = "";
	$customer_phone_number = "";
	$customer_email = "";
	$amount_paid = floatval($catch_incoming_request['data']['sourceAmount']);
	$amount_deposited = floatval($catch_incoming_request['data']['amountReceived']);
	$transaction_id = $catch_incoming_request["data"]["reference"];
	$virtual_accountid = $catch_incoming_request["data"]["virtualAccount"];
	$transaction_event = $catch_incoming_request["event"];
	$transaction_status = $catch_incoming_request["data"]["status"];
	$payment_method = "BANK TRANSFER";

	$payload = file_get_contents('php://input');
	$fincra_signature = $_SERVER['HTTP_SIGNATURE'];
	$hashkey = hash_hmac('sha512', $payload, $fincra_keys["secret_key"]);


	$vendor_id = trim($select_vendor_table["id"]);

	if ($transaction_event == "collection.successful" && $transaction_status == "successful") {
		$check_if_banks_exists = mysqli_query($connection_server, "SELECT * FROM sas_user_banks WHERE vendor_id='$vendor_id' && reference='$virtual_accountid'");

		if (mysqli_num_rows($check_if_banks_exists) == 1) {

			$get_payment_details = mysqli_fetch_array($check_if_banks_exists);

			$customer_username = $get_payment_details["username"];
			$virtual_accountno = $get_payment_details["account_number"];
			$virtual_bankcode = $get_payment_details["bank_code"];

			$user_id = $get_payment_details["username"];
			$reference = substr(str_shuffle("12345678901234567890"), 0, 15);
			$check_vendor_user_exists = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='$vendor_id' && username='$user_id'");
			if (mysqli_num_rows($check_vendor_user_exists) == 1) {
				$get_logged_user_details = mysqli_fetch_array($check_vendor_user_exists);
				$_SESSION["user_session"] = $get_logged_user_details["username"];

				$select_transaction_history = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE (reference='$transaction_id' OR api_reference='$transaction_id')");

				if ($fincra_signature == $hashkey) {
					if (mysqli_num_rows($select_transaction_history) == 0) {
						chargeUser("credit", $_SESSION["user_session"], "Wallet Credit", $reference, $transaction_id, $amount_paid, $amount_deposited, "Fincra Wallet Credit - " . str_replace("_", " ", $payment_method), strtoupper("WEB"), $_SERVER["HTTP_HOST"], "1");
						unset($_SESSION["user_session"]);
					}
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