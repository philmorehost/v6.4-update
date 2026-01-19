<?php session_start();
include(__DIR__ . "/func/bc-connect.php");
include(__DIR__ . "/func/bc-func.php");

$catch_incoming_request = json_decode(file_get_contents("php://input"), true);
fwrite(fopen("ajuicydebitwallet.txt", "a++"), $catch_incoming_request . "\n\n");

//Select Vendor Table
$select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {
	$juicyway_keys = mysqli_fetch_assoc(mysqli_query($connection_server, "SELECT * FROM sas_payment_gateways WHERE vendor_id='" . $select_vendor_table["id"] . "' && gateway_name='juicyway'"));

	$transaction_event = $catch_incoming_request["data"]["event"];
	$transaction_type = $catch_incoming_request["data"]["type"];
	$customer_wallet_id = $catch_incoming_request["data"]["customer"]["account_id"];
	$currency = $catch_incoming_request["data"]["currency"];
	$amount_paid = ($catch_incoming_request["data"]["amount"] / 100);
	$amount_deposited = $amount_paid;
	$transaction_id = $catch_incoming_request["data"]["transaction_id"];
	$payment_method = "WEB";
	$vendor_id = trim($select_vendor_table["id"]);
	$status = 1;

	$select_wallet_by_account_id = mysqli_query($connection_server, "SELECT * FROM sas_user_crypto_ledger_balance WHERE api_wallet_id='$customer_wallet_id'");
	if (mysqli_num_rows($select_wallet_by_account_id) == 1) {
		$get_wallet_by_account_id_details = mysqli_fetch_assoc($select_wallet_by_account_id);
		$user_id = $get_wallet_by_account_id_details["username"];
		$reference = substr(str_shuffle("12345678901234567890"), 0, 15);
		$check_vendor_user_exists = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='$vendor_id' && username='$user_id'");
		if (mysqli_num_rows($check_vendor_user_exists) == 1) {
			$get_logged_user_details = mysqli_fetch_array($check_vendor_user_exists);
			$_SESSION["user_session"] = $get_logged_user_details["username"];

			$select_transaction_history = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE (reference='$transaction_id' OR api_reference='$transaction_id')");

			if (($transaction_event == "payment.session.succeeded") && ($catch_incoming_request["data"]["status"] == "success")) {
				if (mysqli_num_rows($select_transaction_history) == 0) {
					chargeUserCryptoWallet("credit", $currency, $currency, $reference, "", $amount, $discounted_amount, "Wallet Credit via " . strtoupper($currency), $purchase_method, $_SERVER["HTTP_HOST"], $status);
					unset($_SESSION["user_session"]);
				}
			}
		}
	}
}

?>