<?php
function accountLevel($levelNumber)
{
	if (is_numeric($levelNumber)) {
		$account_level_details = array(1 => "Smart User", 2 => "Agent Vendor", 3 => "API Vendor");
		if ($account_level_details[$levelNumber] == true) {
			return $account_level_details[$levelNumber];
		} else {
			return "invalid level id";
		}
	} else {
		return "non-numeric string";
	}
}

function accountStatus($statusNumber)
{
	if (is_numeric($statusNumber)) {
		$account_status_details = array(1 => "Active", 2 => "Deactivated", 3 => "Deleted");
		if ($account_status_details[$statusNumber] == true) {
			return $account_status_details[$statusNumber];
		} else {
			return "invalid status id";
		}
	} else {
		return "non-numeric string";
	}
}

function tranStatus($statusNumber)
{
	if (is_numeric($statusNumber)) {
		$trans_status_details = array(1 => "Success", 2 => "Pending", 3 => "Failed");
		if ($trans_status_details[$statusNumber] == true) {
			return $trans_status_details[$statusNumber];
		} else {
			return "invalid status code";
		}
	} else {
		return "non-numeric string";
	}
}

function itemStatus($statusNumber)
{
	if (is_numeric($statusNumber)) {
		$item_status_details = array(0 => "Disabled", 1 => "Enabled");
		if ($item_status_details[$statusNumber] == true) {
			return $item_status_details[$statusNumber];
		} else {
			return "invalid status code";
		}
	} else {
		return "non-numeric string";
	}
}

function checkIfEmpty($text, $addbef, $addafter)
{
	if (!empty(trim($text))) {
		return $addbef . $text . $addafter;
	} else {
		return "";
	}
}

function checkTextEmpty($text)
{
	if (!empty(trim($text))) {
		return $text;
	} else {
		return "No Comment";
	}
}

function userBalance($decimalIndex)
{
	global $get_logged_user_details;
	if (!empty($get_logged_user_details["balance"])) {
		$exp_number = explode(".", trim($get_logged_user_details["balance"]));
		$firstNumber = $exp_number[0];
		$decimalNumber = isset($exp_number[1]) ? $exp_number[1] : 0;

		if (is_numeric($get_logged_user_details["balance"] ?? 0) && is_numeric($decimalIndex)) {
			return ($firstNumber + 0) . "." . sprintf("%0" . $decimalIndex . "d", $decimalNumber);
		} else {
			if (trim($get_logged_user_details["balance"] ?? "") == "") {
				return "0." . sprintf("%0" . $decimalIndex . "d", 0);
			}
		}
	} else {
		return "0." . sprintf("%0" . $decimalIndex . "d", 0);
	}
}

function vendorBalance($decimalIndex)
{
	global $get_logged_admin_details;
	if (!empty($get_logged_admin_details["balance"])) {
		$exp_number = explode(".", trim($get_logged_admin_details["balance"]));
		$firstNumber = $exp_number[0];
		$decimalNumber = isset($exp_number[1]) ? $exp_number[1] : 0;

		if (is_numeric($get_logged_admin_details["balance"]) && is_numeric($decimalIndex)) {
			return ($firstNumber + 0) . "." . sprintf("%0" . $decimalIndex . "d", $decimalNumber);
		} else {
			if (trim($get_logged_admin_details["balance"]) == "") {
				return "0." . sprintf("%0" . $decimalIndex . "d", 0);
			}
		}
	} else {
		return "0." . sprintf("%0" . $decimalIndex . "d", 0);
	}
}

function chargeUser($type, $product_unique_id, $type_alternative, $reference, $api_reference, $amount, $discounted_amount, $description, $mode, $api_website, $status)
{
	global $connection_server;

	$type = mysqli_real_escape_string($connection_server, trim(strip_tags($type)));
	$product_unique_id = mysqli_real_escape_string($connection_server, trim(strip_tags($product_unique_id)));
	$type_alternative = mysqli_real_escape_string($connection_server, trim(strip_tags($type_alternative)));
	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($reference)));
	$api_reference = mysqli_real_escape_string($connection_server, trim(strip_tags($api_reference)));
	$amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($amount))));
	$discounted_amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($discounted_amount))));
	$description = mysqli_real_escape_string($connection_server, trim(strip_tags($description)));
	$mode = mysqli_real_escape_string($connection_server, trim(strip_tags($mode)));
	$api_website = mysqli_real_escape_string($connection_server, trim(strip_tags($api_website)));
	$status = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($status))));

	$transactionTypeArray = array("credit", "debit");
	$statusArray = array(1, 2, 3);

	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$get_logged_user_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_vendor_det["id"] . "' && username='" . $_SESSION["user_session"] . "' LIMIT 1"));


	if (!empty($get_logged_user_det["balance"]) && is_numeric($get_logged_user_det["balance"]) && !empty($amount) && is_numeric($amount) && !empty($discounted_amount) && is_numeric($discounted_amount) && ($discounted_amount > 0)) {
		if (in_array($type, $transactionTypeArray) && !empty($product_unique_id) && !empty($type_alternative) && !empty($reference) && !empty($description) && is_numeric($status) && in_array($status, $statusArray)) {
			if ($type === "debit") {
				if (($get_logged_user_det["balance"] > 0) && ($amount > 0) && ($get_logged_user_det["balance"] >= $amount) && ($get_logged_user_det["balance"] >= $discounted_amount)) {
					$user_balance_before_debit = $get_logged_user_det["balance"];
					$user_balance_after_debit = ($user_balance_before_debit - $discounted_amount);

					$insert_transaction = mysqli_query($connection_server, "INSERT INTO sas_transactions (vendor_id, product_unique_id, type_alternative, reference, api_reference, username, amount, discounted_amount, balance_before, balance_after, description, mode, api_website, status) VALUES ('" . $get_logged_user_det["vendor_id"] . "', '$product_unique_id', '$type_alternative', '$reference', '$api_reference', '" . $get_logged_user_det["username"] . "', '$amount', '$discounted_amount', '$user_balance_before_debit', '$user_balance_after_debit', '$description', '$mode', '$api_website', '$status')");
					$charge_user = mysqli_query($connection_server, "UPDATE sas_users SET balance='$user_balance_after_debit' WHERE vendor_id='" . $get_logged_user_det["vendor_id"] . "' && username='" . $get_logged_user_det["username"] . "'");
					if (($user_balance_before_debit == true) && ($charge_user == true)) {
						// Email Beginning
						$funding_template_encoded_text_array = array("{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_debit, 2), "{balance_after}" => toDecimal($user_balance_after_debit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
						$raw_funding_template_subject = getUserEmailTemplate('user-funding', 'subject');
						$raw_funding_template_body = getUserEmailTemplate('user-funding', 'body');
						foreach ($funding_template_encoded_text_array as $array_key => $array_val) {
							$raw_funding_template_subject = str_replace($array_key, $array_val, $raw_funding_template_subject);
							$raw_funding_template_body = str_replace($array_key, $array_val, $raw_funding_template_body);
						}

						$transaction_template_encoded_text_array = array("{admin_firstname}" => $get_vendor_det["firstname"], "{admin_lastname}" => $get_vendor_det["lastname"], "{username}" => $get_logged_user_det["username"], "{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_debit, 2), "{balance_after}" => toDecimal($user_balance_after_debit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
						$raw_transaction_template_subject = getUserEmailTemplate('user-transactions', 'subject');
						$raw_transaction_template_body = getUserEmailTemplate('user-transactions', 'body');
						foreach ($transaction_template_encoded_text_array as $array_key => $array_val) {
							$raw_transaction_template_subject = str_replace($array_key, $array_val, $raw_transaction_template_subject);
							$raw_transaction_template_body = str_replace($array_key, $array_val, $raw_transaction_template_body);
						}
						sendVendorEmail($get_logged_user_det["email"], $raw_funding_template_subject, $raw_funding_template_body);
						sendVendorEmail($get_vendor_det["email"], $raw_transaction_template_subject, $raw_transaction_template_body);
						// Email End
						return "success";
					} else {
						return "failed";
					}
				} else {
					return "failed";
				}
			}

			if ($type === "credit") {
				$user_balance_before_credit = $get_logged_user_det["balance"];
				$user_balance_after_credit = ($user_balance_before_credit + $discounted_amount);

				$insert_transaction = mysqli_query($connection_server, "INSERT INTO sas_transactions (vendor_id, product_unique_id, type_alternative, reference, api_reference, username, amount, discounted_amount, balance_before, balance_after, description, mode, api_website, status) VALUES ('" . $get_logged_user_det["vendor_id"] . "', '$product_unique_id', '$type_alternative', '$reference', '$api_reference', '" . $get_logged_user_det["username"] . "', '$amount', '$discounted_amount', '$user_balance_before_credit', '$user_balance_after_credit', '$description', '$mode', '$api_website', '$status')");
				$charge_user = mysqli_query($connection_server, "UPDATE sas_users SET balance='$user_balance_after_credit' WHERE vendor_id='" . $get_logged_user_det["vendor_id"] . "' && username='" . $get_logged_user_det["username"] . "' ");
				if (($user_balance_before_credit == true) && ($charge_user == true)) {
					// Email Beginning
					$funding_template_encoded_text_array = array("{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_credit, 2), "{balance_after}" => toDecimal($user_balance_after_credit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
					$raw_funding_template_subject = getUserEmailTemplate('user-funding', 'subject');
					$raw_funding_template_body = getUserEmailTemplate('user-funding', 'body');
					foreach ($funding_template_encoded_text_array as $array_key => $array_val) {
						$raw_funding_template_subject = str_replace($array_key, $array_val, $raw_funding_template_subject);
						$raw_funding_template_body = str_replace($array_key, $array_val, $raw_funding_template_body);
					}

					$transaction_template_encoded_text_array = array("{admin_firstname}" => $get_vendor_det["firstname"], "{admin_lastname}" => $get_vendor_det["lastname"], "{username}" => $get_logged_user_det["username"], "{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_credit, 2), "{balance_after}" => toDecimal($user_balance_after_credit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
					$raw_transaction_template_subject = getUserEmailTemplate('user-transactions', 'subject');
					$raw_transaction_template_body = getUserEmailTemplate('user-transactions', 'body');
					foreach ($transaction_template_encoded_text_array as $array_key => $array_val) {
						$raw_transaction_template_subject = str_replace($array_key, $array_val, $raw_transaction_template_subject);
						$raw_transaction_template_body = str_replace($array_key, $array_val, $raw_transaction_template_body);
					}
					sendVendorEmail($get_logged_user_det["email"], $raw_funding_template_subject, $raw_funding_template_body);
					sendVendorEmail($get_vendor_det["email"], $raw_transaction_template_subject, $raw_transaction_template_body);
					// Email End
					return "success";
				} else {
					return "failed";
				}
			}

			if (!in_array($type, $transactionTypeArray)) {
				return "failed";
			}
		} else {
			return "failed";
		}
	} else {
		return "failed";
	}
}

function chargeOtherUser($user_id, $type, $product_unique_id, $type_alternative, $reference, $api_reference, $amount, $discounted_amount, $description, $mode, $api_website, $status)
{
	global $connection_server;

	$user_id = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($user_id))));
	$type = mysqli_real_escape_string($connection_server, trim(strip_tags($type)));
	$product_unique_id = mysqli_real_escape_string($connection_server, trim(strip_tags($product_unique_id)));
	$type_alternative = mysqli_real_escape_string($connection_server, trim(strip_tags($type_alternative)));
	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($reference)));
	$api_reference = mysqli_real_escape_string($connection_server, trim(strip_tags($api_reference)));
	$amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($amount))));
	$discounted_amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($discounted_amount))));
	$description = mysqli_real_escape_string($connection_server, trim(strip_tags($description)));
	$mode = mysqli_real_escape_string($connection_server, trim(strip_tags($mode)));
	$api_website = mysqli_real_escape_string($connection_server, trim(strip_tags($api_website)));
	$status = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($status))));

	$transactionTypeArray = array("credit", "debit");
	$statusArray = array(1, 2, 3);

	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$get_logged_user_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_vendor_det["id"] . "' && username='" . $user_id . "' LIMIT 1"));


	if (!empty($get_logged_user_det["balance"]) && is_numeric($get_logged_user_det["balance"]) && !empty($amount) && is_numeric($amount) && !empty($discounted_amount) && is_numeric($discounted_amount) && ($discounted_amount > 0)) {
		if (in_array($type, $transactionTypeArray) && !empty($product_unique_id) && !empty($type_alternative) && !empty($reference) && !empty($description) && is_numeric($status) && in_array($status, $statusArray)) {
			if ($type === "debit") {
				if (($get_logged_user_det["balance"] > 0) && ($amount > 0) && ($get_logged_user_det["balance"] >= $amount) && ($get_logged_user_det["balance"] >= $discounted_amount)) {
					$user_balance_before_debit = $get_logged_user_det["balance"];
					$user_balance_after_debit = ($user_balance_before_debit - $discounted_amount);

					$insert_transaction = mysqli_query($connection_server, "INSERT INTO sas_transactions (vendor_id, product_unique_id, type_alternative, reference, api_reference, username, amount, discounted_amount, balance_before, balance_after, description, mode, api_website, status) VALUES ('" . $get_logged_user_det["vendor_id"] . "', '$product_unique_id', '$type_alternative', '$reference', '$api_reference', '" . $user_id . "', '$amount', '$discounted_amount', '$user_balance_before_debit', '$user_balance_after_debit', '$description', '$mode', '$api_website', '$status')");
					$charge_user = mysqli_query($connection_server, "UPDATE sas_users SET balance='$user_balance_after_debit' WHERE vendor_id='" . $get_logged_user_det["vendor_id"] . "' && username='" . $user_id . "'");
					if (($user_balance_before_debit == true) && ($charge_user == true)) {
						// Email Beginning
						$funding_template_encoded_text_array = array("{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_debit, 2), "{balance_after}" => toDecimal($user_balance_after_debit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
						$raw_funding_template_subject = getUserEmailTemplate('user-funding', 'subject');
						$raw_funding_template_body = getUserEmailTemplate('user-funding', 'body');
						foreach ($funding_template_encoded_text_array as $array_key => $array_val) {
							$raw_funding_template_subject = str_replace($array_key, $array_val, $raw_funding_template_subject);
							$raw_funding_template_body = str_replace($array_key, $array_val, $raw_funding_template_body);
						}

						$transaction_template_encoded_text_array = array("{admin_firstname}" => $get_vendor_det["firstname"], "{admin_lastname}" => $get_vendor_det["lastname"], "{username}" => $get_logged_user_det["username"], "{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_debit, 2), "{balance_after}" => toDecimal($user_balance_after_debit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
						$raw_transaction_template_subject = getUserEmailTemplate('user-transactions', 'subject');
						$raw_transaction_template_body = getUserEmailTemplate('user-transactions', 'body');
						foreach ($transaction_template_encoded_text_array as $array_key => $array_val) {
							$raw_transaction_template_subject = str_replace($array_key, $array_val, $raw_transaction_template_subject);
							$raw_transaction_template_body = str_replace($array_key, $array_val, $raw_transaction_template_body);
						}
						sendVendorEmail($get_logged_user_det["email"], $raw_funding_template_subject, $raw_funding_template_body);
						sendVendorEmail($get_vendor_det["email"], $raw_transaction_template_subject, $raw_transaction_template_body);
						// Email End
						return "success";
					} else {
						return "failed";
					}
				} else {
					return "failed";
				}
			}

			if ($type === "credit") {
				$user_balance_before_credit = $get_logged_user_det["balance"];
				$user_balance_after_credit = ($user_balance_before_credit + $discounted_amount);

				$insert_transaction = mysqli_query($connection_server, "INSERT INTO sas_transactions (vendor_id, product_unique_id, type_alternative, reference, api_reference, username, amount, discounted_amount, balance_before, balance_after, description, mode, api_website, status) VALUES ('" . $get_logged_user_det["vendor_id"] . "', '$product_unique_id', '$type_alternative', '$reference', '$api_reference', '" . $user_id . "', '$amount', '$discounted_amount', '$user_balance_before_credit', '$user_balance_after_credit', '$description', '$mode', '$api_website', '$status')");
				$charge_user = mysqli_query($connection_server, "UPDATE sas_users SET balance='$user_balance_after_credit' WHERE vendor_id='" . $get_logged_user_det["vendor_id"] . "' && username='" . $user_id . "' ");
				if (($user_balance_before_credit == true) && ($charge_user == true)) {
					// Email Beginning
					$funding_template_encoded_text_array = array("{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_credit, 2), "{balance_after}" => toDecimal($user_balance_after_credit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
					$raw_funding_template_subject = getUserEmailTemplate('user-funding', 'subject');
					$raw_funding_template_body = getUserEmailTemplate('user-funding', 'body');
					foreach ($funding_template_encoded_text_array as $array_key => $array_val) {
						$raw_funding_template_subject = str_replace($array_key, $array_val, $raw_funding_template_subject);
						$raw_funding_template_body = str_replace($array_key, $array_val, $raw_funding_template_body);
					}

					$transaction_template_encoded_text_array = array("{admin_firstname}" => $get_vendor_det["firstname"], "{admin_lastname}" => $get_vendor_det["lastname"], "{username}" => $get_logged_user_det["username"], "{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_credit, 2), "{balance_after}" => toDecimal($user_balance_after_credit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
					$raw_transaction_template_subject = getUserEmailTemplate('user-transactions', 'subject');
					$raw_transaction_template_body = getUserEmailTemplate('user-transactions', 'body');
					foreach ($transaction_template_encoded_text_array as $array_key => $array_val) {
						$raw_transaction_template_subject = str_replace($array_key, $array_val, $raw_transaction_template_subject);
						$raw_transaction_template_body = str_replace($array_key, $array_val, $raw_transaction_template_body);
					}
					sendVendorEmail($get_logged_user_det["email"], $raw_funding_template_subject, $raw_funding_template_body);
					sendVendorEmail($get_vendor_det["email"], $raw_transaction_template_subject, $raw_transaction_template_body);
					// Email End
					return "success";
				} else {
					return "failed";
				}
			}

			if (!in_array($type, $transactionTypeArray)) {
				return "failed";
			}
		} else {
			return "failed";
		}
	} else {
		return "failed";
	}
}

function chargeVendor($type, $product_unique_id, $type_alternative, $reference, $amount, $discounted_amount, $description, $api_website, $status)
{
	global $connection_server;

	$type = mysqli_real_escape_string($connection_server, trim(strip_tags($type)));
	$product_unique_id = mysqli_real_escape_string($connection_server, trim(strip_tags($product_unique_id)));
	$type_alternative = mysqli_real_escape_string($connection_server, trim(strip_tags($type_alternative)));
	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($reference)));
	$amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($amount))));
	$discounted_amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($discounted_amount))));
	$description = mysqli_real_escape_string($connection_server, trim(strip_tags($description)));
	$api_website = mysqli_real_escape_string($connection_server, trim(strip_tags($api_website)));
	$status = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($status))));

	$transactionTypeArray = array("credit", "debit");
	$statusArray = array(1, 2, 3);

	$get_spadmin_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_super_admin LIMIT 1"));
	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$get_logged_user_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='" . $get_vendor_det["id"] . "' LIMIT 1"));


	if (!empty($get_logged_user_det["balance"]) && is_numeric($get_logged_user_det["balance"]) && !empty($amount) && is_numeric($amount) && !empty($discounted_amount) && is_numeric($discounted_amount) && ($discounted_amount > 0)) {
		if (in_array($type, $transactionTypeArray) && !empty($product_unique_id) && !empty($type_alternative) && !empty($reference) && !empty($description) && is_numeric($status) && in_array($status, $statusArray)) {
			if ($type === "debit") {
				if (($get_logged_user_det["balance"] > 0) && ($amount > 0) && ($get_logged_user_det["balance"] >= $amount) && ($get_logged_user_det["balance"] >= $discounted_amount)) {
					$user_balance_before_debit = $get_logged_user_det["balance"];
					$user_balance_after_debit = ($user_balance_before_debit - $discounted_amount);

					$insert_transaction = mysqli_query($connection_server, "INSERT INTO sas_vendor_transactions (vendor_id, product_unique_id, type_alternative, reference, amount, discounted_amount, balance_before, balance_after, description, api_website, status) VALUES ('" . $get_logged_user_det["id"] . "', '$product_unique_id', '$type_alternative', '$reference', '$amount', '$discounted_amount', '$user_balance_before_debit', '$user_balance_after_debit', '$description', '$api_website', '$status')");
					$charge_user = mysqli_query($connection_server, "UPDATE sas_vendors SET balance='$user_balance_after_debit' WHERE id='" . $get_logged_user_det["id"] . "'");
					if (($user_balance_before_debit == true) && ($charge_user == true)) {
						// Email Beginning
						$funding_template_encoded_text_array = array("{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_debit, 2), "{balance_after}" => toDecimal($user_balance_after_debit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
						$raw_funding_template_subject = getSuperAdminEmailTemplate('vendor-funding', 'subject');
						$raw_funding_template_body = getSuperAdminEmailTemplate('vendor-funding', 'body');
						foreach ($funding_template_encoded_text_array as $array_key => $array_val) {
							$raw_funding_template_subject = str_replace($array_key, $array_val, $raw_funding_template_subject);
							$raw_funding_template_body = str_replace($array_key, $array_val, $raw_funding_template_body);
						}

						$transaction_template_encoded_text_array = array("{admin_firstname}" => $get_spadmin_det["firstname"], "{admin_lastname}" => $get_spadmin_det["lastname"], "{email}" => $get_logged_user_det["email"], "{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_debit, 2), "{balance_after}" => toDecimal($user_balance_after_debit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
						$raw_transaction_template_subject = getSuperAdminEmailTemplate('vendor-transactions', 'subject');
						$raw_transaction_template_body = getSuperAdminEmailTemplate('vendor-transactions', 'body');
						foreach ($transaction_template_encoded_text_array as $array_key => $array_val) {
							$raw_transaction_template_subject = str_replace($array_key, $array_val, $raw_transaction_template_subject);
							$raw_transaction_template_body = str_replace($array_key, $array_val, $raw_transaction_template_body);
						}
						sendVendorEmail($get_logged_user_det["email"], $raw_funding_template_subject, $raw_funding_template_body);
						sendVendorEmail($get_spadmin_det["email"], $raw_transaction_template_subject, $raw_transaction_template_body);
						// Email End
						return "success";
					} else {
						return "failed";
					}
				} else {
					return "failed";
				}
			}

			if ($type === "credit") {
				$user_balance_before_credit = $get_logged_user_det["balance"];
				$user_balance_after_credit = ($user_balance_before_credit + $discounted_amount);

				$insert_transaction = mysqli_query($connection_server, "INSERT INTO sas_vendor_transactions (vendor_id, product_unique_id, type_alternative, reference, amount, discounted_amount, balance_before, balance_after, description, api_website, status) VALUES ('" . $get_logged_user_det["id"] . "', '$product_unique_id', '$type_alternative', '$reference', '$amount', '$discounted_amount', '$user_balance_before_credit', '$user_balance_after_credit', '$description', '$api_website', '$status')");
				$charge_user = mysqli_query($connection_server, "UPDATE sas_vendors SET balance='$user_balance_after_credit' WHERE id='" . $get_logged_user_det["id"] . "'");
				if (($user_balance_before_credit == true) && ($charge_user == true)) {
					// Email Beginning
					$funding_template_encoded_text_array = array("{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_credit, 2), "{balance_after}" => toDecimal($user_balance_after_credit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
					$raw_funding_template_subject = getSuperAdminEmailTemplate('vendor-funding', 'subject');
					$raw_funding_template_body = getSuperAdminEmailTemplate('vendor-funding', 'body');
					foreach ($funding_template_encoded_text_array as $array_key => $array_val) {
						$raw_funding_template_subject = str_replace($array_key, $array_val, $raw_funding_template_subject);
						$raw_funding_template_body = str_replace($array_key, $array_val, $raw_funding_template_body);
					}

					$transaction_template_encoded_text_array = array("{admin_firstname}" => $get_spadmin_det["firstname"], "{admin_lastname}" => $get_spadmin_det["lastname"], "{email}" => $get_logged_user_det["email"], "{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_credit, 2), "{balance_after}" => toDecimal($user_balance_after_credit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
					$raw_transaction_template_subject = getSuperAdminEmailTemplate('vendor-transactions', 'subject');
					$raw_transaction_template_body = getSuperAdminEmailTemplate('vendor-transactions', 'body');
					foreach ($transaction_template_encoded_text_array as $array_key => $array_val) {
						$raw_transaction_template_subject = str_replace($array_key, $array_val, $raw_transaction_template_subject);
						$raw_transaction_template_body = str_replace($array_key, $array_val, $raw_transaction_template_body);
					}
					sendVendorEmail($get_logged_user_det["email"], $raw_funding_template_subject, $raw_funding_template_body);
					sendVendorEmail($get_spadmin_det["email"], $raw_transaction_template_subject, $raw_transaction_template_body);
					// Email End
					return "success";
				} else {
					return "failed";
				}
			}

			if (!in_array($type, $transactionTypeArray)) {
				return "failed";
			}
		} else {
			return "failed";
		}
	} else {
		return "failed";
	}
}

function chargeOtherVendor($vendor_id, $type, $product_unique_id, $type_alternative, $reference, $amount, $discounted_amount, $description, $api_website, $status)
{
	global $connection_server;

	$type = mysqli_real_escape_string($connection_server, trim(strip_tags($type)));
	$product_unique_id = mysqli_real_escape_string($connection_server, trim(strip_tags($product_unique_id)));
	$type_alternative = mysqli_real_escape_string($connection_server, trim(strip_tags($type_alternative)));
	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($reference)));
	$amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($amount))));
	$discounted_amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($discounted_amount))));
	$description = mysqli_real_escape_string($connection_server, trim(strip_tags($description)));
	$api_website = mysqli_real_escape_string($connection_server, trim(strip_tags($api_website)));
	$status = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($status))));

	$transactionTypeArray = array("credit", "debit");
	$statusArray = array(1, 2, 3);

	$get_spadmin_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_super_admin LIMIT 1"));
	$get_logged_user_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE email='" . $vendor_id . "' LIMIT 1"));


	if (!empty($get_logged_user_det["balance"]) && is_numeric($get_logged_user_det["balance"]) && !empty($amount) && is_numeric($amount) && !empty($discounted_amount) && is_numeric($discounted_amount) && ($discounted_amount > 0)) {
		if (in_array($type, $transactionTypeArray) && !empty($product_unique_id) && !empty($type_alternative) && !empty($reference) && !empty($description) && is_numeric($status) && in_array($status, $statusArray)) {
			if ($type === "debit") {
				if (($get_logged_user_det["balance"] > 0) && ($amount > 0) && ($get_logged_user_det["balance"] >= $amount) && ($get_logged_user_det["balance"] >= $discounted_amount)) {
					$user_balance_before_debit = $get_logged_user_det["balance"];
					$user_balance_after_debit = ($user_balance_before_debit - $discounted_amount);

					$insert_transaction = mysqli_query($connection_server, "INSERT INTO sas_vendor_transactions (vendor_id, product_unique_id, type_alternative, reference, amount, discounted_amount, balance_before, balance_after, description, api_website, status) VALUES ('" . $get_logged_user_det["id"] . "', '$product_unique_id', '$type_alternative', '$reference', '$amount', '$discounted_amount', '$user_balance_before_debit', '$user_balance_after_debit', '$description', '$api_website', '$status')");
					$charge_user = mysqli_query($connection_server, "UPDATE sas_vendors SET balance='$user_balance_after_debit' WHERE id='" . $get_logged_user_det["id"] . "'");
					if (($user_balance_before_debit == true) && ($charge_user == true)) {
						// Email Beginning
						$funding_template_encoded_text_array = array("{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_debit, 2), "{balance_after}" => toDecimal($user_balance_after_debit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
						$raw_funding_template_subject = getSuperAdminEmailTemplate('vendor-funding', 'subject');
						$raw_funding_template_body = getSuperAdminEmailTemplate('vendor-funding', 'body');
						foreach ($funding_template_encoded_text_array as $array_key => $array_val) {
							$raw_funding_template_subject = str_replace($array_key, $array_val, $raw_funding_template_subject);
							$raw_funding_template_body = str_replace($array_key, $array_val, $raw_funding_template_body);
						}

						$transaction_template_encoded_text_array = array("{admin_firstname}" => $get_spadmin_det["firstname"], "{admin_lastname}" => $get_spadmin_det["lastname"], "{email}" => $get_logged_user_det["email"], "{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_debit, 2), "{balance_after}" => toDecimal($user_balance_after_debit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
						$raw_transaction_template_subject = getSuperAdminEmailTemplate('vendor-transactions', 'subject');
						$raw_transaction_template_body = getSuperAdminEmailTemplate('vendor-transactions', 'body');
						foreach ($transaction_template_encoded_text_array as $array_key => $array_val) {
							$raw_transaction_template_subject = str_replace($array_key, $array_val, $raw_transaction_template_subject);
							$raw_transaction_template_body = str_replace($array_key, $array_val, $raw_transaction_template_body);
						}
						sendVendorEmail($get_logged_user_det["email"], $raw_funding_template_subject, $raw_funding_template_body);
						sendSuperAdminEmail($get_spadmin_det["email"], $raw_transaction_template_subject, $raw_transaction_template_body);
						// Email End
						return "success";
					} else {
						return "failed";
					}
				} else {
					return "failed";
				}
			}

			if ($type === "credit") {
				$user_balance_before_credit = $get_logged_user_det["wallet_balance"];
				$user_balance_after_credit = ($user_balance_before_credit + $discounted_amount);

				$insert_transaction = mysqli_query($connection_server, "INSERT INTO sas_vendor_transactions (vendor_id, product_unique_id, type_alternative, reference, amount, discounted_amount, balance_before, balance_after, description, api_website, status) VALUES ('" . $get_logged_user_det["id"] . "', '$product_unique_id', '$type_alternative', '$reference', '$amount', '$discounted_amount', '$user_balance_before_credit', '$user_balance_after_credit', '$description', '$api_website', '$status')");
				$charge_user = mysqli_query($connection_server, "UPDATE sas_vendors SET balance='$user_balance_after_credit' WHERE id='" . $get_logged_user_det["id"] . "'");
				if (($user_balance_before_credit == true) && ($charge_user == true)) {
					// Email Beginning
					$funding_template_encoded_text_array = array("{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_credit, 2), "{balance_after}" => toDecimal($user_balance_after_credit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
					$raw_funding_template_subject = getSuperAdminEmailTemplate('vendor-funding', 'subject');
					$raw_funding_template_body = getSuperAdminEmailTemplate('vendor-funding', 'body');
					foreach ($funding_template_encoded_text_array as $array_key => $array_val) {
						$raw_funding_template_subject = str_replace($array_key, $array_val, $raw_funding_template_subject);
						$raw_funding_template_body = str_replace($array_key, $array_val, $raw_funding_template_body);
					}

					$transaction_template_encoded_text_array = array("{admin_firstname}" => $get_spadmin_det["firstname"], "{admin_lastname}" => $get_spadmin_det["lastname"], "{email}" => $get_logged_user_det["email"], "{firstname}" => $get_logged_user_det["firstname"], "{lastname}" => $get_logged_user_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_credit, 2), "{balance_after}" => toDecimal($user_balance_after_credit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
					$raw_transaction_template_subject = getSuperAdminEmailTemplate('vendor-transactions', 'subject');
					$raw_transaction_template_body = getSuperAdminEmailTemplate('vendor-transactions', 'body');
					foreach ($transaction_template_encoded_text_array as $array_key => $array_val) {
						$raw_transaction_template_subject = str_replace($array_key, $array_val, $raw_transaction_template_subject);
						$raw_transaction_template_body = str_replace($array_key, $array_val, $raw_transaction_template_body);
					}
					sendVendorEmail($get_logged_user_det["email"], $raw_funding_template_subject, $raw_funding_template_body);
					sendSuperAdminEmail($get_spadmin_det["email"], $raw_transaction_template_subject, $raw_transaction_template_body);
					// Email End
					return "success";
				} else {
					return "failed";
				}
			}

			if (!in_array($type, $transactionTypeArray)) {
				return "failed";
			}
		} else {
			return "failed";
		}
	} else {
		return "failed";
	}
}

function addUserVirtualBank($reference, $bank_code, $bank_name, $account_number, $account_name)
{
	global $connection_server;

	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($reference)));
	$bank_code = mysqli_real_escape_string($connection_server, trim(strip_tags($bank_code)));
	$bank_name = mysqli_real_escape_string($connection_server, trim(strip_tags($bank_name)));
	$account_number = mysqli_real_escape_string($connection_server, trim(strip_tags($account_number)));
	$account_name = mysqli_real_escape_string($connection_server, trim(strip_tags($account_name)));

	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$get_logged_user_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_vendor_det["id"] . "' && username='" . $_SESSION["user_session"] . "' LIMIT 1"));

	if (!empty($reference) && !empty($bank_code) && !empty($bank_name) && !empty($account_number) && !empty($account_name)) {
		$select_banks = mysqli_query($connection_server, "SELECT * FROM sas_user_banks WHERE vendor_id='" . $get_logged_user_det["vendor_id"] . "' && username='" . $get_logged_user_det["username"] . "' && bank_code='" . $bank_code . "'");
		if (($select_banks == true) && (mysqli_num_rows($select_banks) == 0)) {
			mysqli_query($connection_server, "INSERT INTO sas_user_banks (vendor_id, username, reference, bank_code, bank_name, account_number, account_name) VALUES ('" . $get_logged_user_det["vendor_id"] . "', '" . $get_logged_user_det["username"] . "', '$reference', '$bank_code', '$bank_name', '$account_number', '$account_name')");
		}
	}
}

function getUserVirtualBank()
{
	global $connection_server;

	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$get_logged_user_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_vendor_det["id"] . "' && username='" . $_SESSION["user_session"] . "' LIMIT 1"));

	$select_banks = mysqli_query($connection_server, "SELECT * FROM sas_user_banks WHERE vendor_id='" . $get_logged_user_det["vendor_id"] . "' && username='" . $get_logged_user_det["username"] . "'");
	if (($select_banks == true) && (mysqli_num_rows($select_banks) >= 1)) {
		$banks_json_array_list = array();
		while ($get_bank_details = mysqli_fetch_array($select_banks)) {
			$banks_json_array = array("reference" => $get_bank_details["reference"], "bank_code" => $get_bank_details["bank_code"], "bank_name" => $get_bank_details["bank_name"], "account_name" => $get_bank_details["account_name"], "account_number" => $get_bank_details["account_number"]);
			$banks_json_array = json_encode($banks_json_array, true);
			array_push($banks_json_array_list, $banks_json_array);
		}
		return $banks_json_array_list;
	} else {
		return false;
	}
}

function addVendorVirtualBank($reference, $bank_code, $bank_name, $account_number, $account_name)
{
	global $connection_server;

	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($reference)));
	$bank_code = mysqli_real_escape_string($connection_server, trim(strip_tags($bank_code)));
	$bank_name = mysqli_real_escape_string($connection_server, trim(strip_tags($bank_name)));
	$account_number = mysqli_real_escape_string($connection_server, trim(strip_tags($account_number)));
	$account_name = mysqli_real_escape_string($connection_server, trim(strip_tags($account_name)));

	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$get_logged_user_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='" . $get_vendor_det["id"] . "' LIMIT 1"));

	if (!empty($reference) && !empty($bank_code) && !empty($bank_name) && !empty($account_number) && !empty($account_name)) {
		$select_banks = mysqli_query($connection_server, "SELECT * FROM sas_vendor_banks WHERE vendor_id='" . $get_logged_user_det["id"] . "' && bank_code='" . $bank_code . "'");
		if (($select_banks == true) && (mysqli_num_rows($select_banks) == 0)) {
			mysqli_query($connection_server, "INSERT INTO sas_vendor_banks (vendor_id, reference, bank_code, bank_name, account_number, account_name) VALUES ('" . $get_logged_user_det["id"] . "', '$reference', '$bank_code', '$bank_name', '$account_number', '$account_name')");
		}
	}
}

function getVendorVirtualBank()
{
	global $connection_server;

	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$get_logged_user_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='" . $get_vendor_det["id"] . "' LIMIT 1"));

	$select_banks = mysqli_query($connection_server, "SELECT * FROM sas_vendor_banks WHERE vendor_id='" . $get_logged_user_det["id"] . "'");
	if (($select_banks == true) && (mysqli_num_rows($select_banks) >= 1)) {
		$banks_json_array_list = array();
		while ($get_bank_details = mysqli_fetch_array($select_banks)) {
			$banks_json_array = array("reference" => $get_bank_details["reference"], "bank_code" => $get_bank_details["bank_code"], "bank_name" => $get_bank_details["bank_name"], "account_name" => $get_bank_details["account_name"], "account_number" => $get_bank_details["account_number"]);
			$banks_json_array = json_encode($banks_json_array, true);
			array_push($banks_json_array_list, $banks_json_array);
		}
		return $banks_json_array_list;
	} else {
		return false;
	}
}

function alterTransaction($reference, $column_name, $column_value)
{
	global $connection_server;

	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($reference)));
	$column_name = mysqli_real_escape_string($connection_server, trim(strip_tags($column_name)));
	$column_value = mysqli_real_escape_string($connection_server, trim(strip_tags($column_value)));

	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$get_logged_user_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_vendor_det["id"] . "' && username='" . $_SESSION["user_session"] . "' LIMIT 1"));

	if (!empty($reference) && !empty($column_name) && !empty($column_value)) {
		$select_transaction = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='" . $get_logged_user_det["vendor_id"] . "' && username='" . $get_logged_user_det["username"] . "' && reference='" . $reference . "'");
		if (($select_transaction == true) && (mysqli_num_rows($select_transaction) == 1)) {
			$update_transaction = mysqli_query($connection_server, "UPDATE sas_transactions SET " . $column_name . "='" . $column_value . "' WHERE vendor_id='" . $get_logged_user_det["vendor_id"] . "' && username='" . $get_logged_user_det["username"] . "' && reference='" . $reference . "'");
		}
	}
}

function alterVendorTransaction($reference, $column_name, $column_value)
{
	global $connection_server;

	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($reference)));
	$column_name = mysqli_real_escape_string($connection_server, trim(strip_tags($column_name)));
	$column_value = mysqli_real_escape_string($connection_server, trim(strip_tags($column_value)));

	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$get_logged_user_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='" . $get_vendor_det["id"] . "' LIMIT 1"));

	if (!empty($reference) && !empty($column_name) && !empty($column_value)) {
		$select_transaction = mysqli_query($connection_server, "SELECT * FROM sas_vendor_transactions WHERE vendor_id='" . $get_logged_user_det["id"] . "' && reference='" . $reference . "'");
		if (($select_transaction == true) && (mysqli_num_rows($select_transaction) == 1)) {
			$update_transaction = mysqli_query($connection_server, "UPDATE sas_vendor_transactions SET " . $column_name . "='" . $column_value . "' WHERE vendor_id='" . $get_logged_user_det["id"] . "' && reference='" . $reference . "'");
		}
	}
}

function getTransaction($reference, $column_name)
{
	global $connection_server, $get_logged_user_details;

	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($reference)));
	$column_name = mysqli_real_escape_string($connection_server, trim(strip_tags($column_name)));

	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$get_logged_user_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_vendor_det["id"] . "' && username='" . $get_logged_user_details["username"] . "' LIMIT 1"));

	if (!empty($reference) && !empty($column_name)) {

		$select_transaction = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='" . $get_logged_user_det["vendor_id"] . "' && username='" . $get_logged_user_det["username"] . "' && reference='" . $reference . "'");

		if (($select_transaction == true) && (mysqli_num_rows($select_transaction) == 1)) {
			$get_transaction_column = mysqli_fetch_array($select_transaction);
			return $get_transaction_column[$column_name];
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function transactionActionButton($api_id, $product_id, $transaction_ref, $transaction_status, $transaction_type)
{
	global $connection_server, $get_logged_user_details;
	if (!empty($api_id) && !empty($product_id)) {
		$get_user_product_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $product_id . "' LIMIT 1"));
		$get_user_api_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $api_id . "' LIMIT 1"));

		if (in_array($get_user_api_details["api_type"], array("electric")) && !strpos($transaction_type, "refund")) {
			$product_transaction_action_button = '<a href="/web/ViewElectric.php?ref=' . $transaction_ref . '" style="text-decoration: underline; color: green;" class="a-cursor">View Receipt</a>';
		}

		if (in_array($get_user_api_details["api_type"], array("datacard", "rechargecard")) && !strpos($transaction_type, "refund")) {
			$product_transaction_action_button = '<a href="/web/ViewCard.php?ref=' . $transaction_ref . '" style="text-decoration: underline; color: green;" class="a-cursor">View Card</a>';
		}

		if (in_array($get_user_api_details["api_type"], array("nairacard", "dollarcard")) && !strpos($transaction_type, "refund")) {
			$product_transaction_action_button = '<a href="/web/ViewVirtualCard.php?ref=' . $transaction_ref . '" style="text-decoration: underline; color: green;" class="a-cursor">View Virtual Card</a>';
		}

		if (!in_array($get_user_api_details["api_type"], array("electric", "datacard", "rechargecard", "nairacard", "dollarcard")) && !strpos($transaction_type, "refund")) {
			$product_transaction_action_button = 'Successful';
		}
		$product_transaction_requery_button = '<a href="/web/Transactions.php?requery=' . $transaction_ref . '" style="text-decoration: underline; color: red;" class="a-cursor">Requery</a>';

		//Successful
		if (in_array($transaction_status, array(1))) {
			$all_product_transaction_actions_button = $product_transaction_action_button;
		} else {
			//Pending
			if (in_array($transaction_status, array(2))) {
				$all_product_transaction_actions_button = $product_transaction_requery_button;
			} else {
				//Failed
				if (in_array($transaction_status, array(3))) {
					$all_product_transaction_actions_button = '-';
				}
			}
		}

		return $all_product_transaction_actions_button;
	} else {
		if (in_array(strtolower($transaction_type), array("bank transfer")) && !strpos($transaction_type, "refund")) {
			$product_transaction_action_button = '<a href="/web/ViewBankReceipt.php?ref=' . $transaction_ref . '" style="text-decoration: underline; color: green;" class="a-cursor">View Receipt</a>';
		} else {
			$product_transaction_action_button = "-";
		}

		return $product_transaction_action_button;
	}

}

function adminTransactionActionButton($api_id, $product_id, $transaction_ref, $transaction_status, $transaction_type)
{
	global $connection_server, $get_logged_admin_details;
	if (!empty($api_id) && !empty($product_id)) {
		$get_user_product_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='" . $product_id . "' LIMIT 1"));
		$get_user_api_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='" . $api_id . "' LIMIT 1"));

		if (in_array($get_user_api_details["api_type"], array("electric")) && !strpos($transaction_type, "refund")) {
			$product_transaction_action_button = '<a href="/web/ViewElectric.php?ref=' . $transaction_ref . '" style="text-decoration: underline; color: green;" class="a-cursor">View Receipt</a>';
		}

		if (in_array($get_user_api_details["api_type"], array("datacard", "rechargecard")) && !strpos($transaction_type, "refund")) {
			$product_transaction_action_button = '<a href="/web/ViewCard.php?ref=' . $transaction_ref . '" style="text-decoration: underline; color: green;" class="a-cursor">View Card</a>';
		}

		if (in_array($get_user_api_details["api_type"], array("nairacard", "dollarcard")) && !strpos($transaction_type, "refund")) {
			$product_transaction_action_button = '<a href="/web/ViewVirtualCard.php?ref=' . $transaction_ref . '" style="text-decoration: underline; color: green;" class="a-cursor">View Virtual Card</a>';
		}

		if (!in_array($get_user_api_details["api_type"], array("electric", "datacard", "rechargecard", "nairacard", "dollarcard")) && !strpos($transaction_type, "refund")) {
			$product_transaction_action_button = 'Successful';
		}

		$product_transaction_requery_button = '<a href="/bc-admin/Transactions.php?requery=' . $transaction_ref . '" style="text-decoration: underline; color: red;" class="a-cursor">Requery</a>';

		//Successful
		if (in_array($transaction_status, array(1))) {
			$all_product_transaction_actions_button = $product_transaction_action_button;
		} else {
			//Pending
			if (in_array($transaction_status, array(2))) {
				$all_product_transaction_actions_button = $product_transaction_requery_button;
			} else {
				//Failed
				if (in_array($transaction_status, array(3))) {
					$all_product_transaction_actions_button = '-';
				}
			}
		}

		return $all_product_transaction_actions_button;
	} else {
		if (in_array(strtolower($transaction_type), array("bank transfer")) && !strpos($transaction_type, "refund")) {
			$product_transaction_action_button = '<a href="/web/ViewBankReceipt.php?ref=' . $transaction_ref . '" style="text-decoration: underline; color: green;" class="a-cursor">View Receipt</a>';
		} else {
			$product_transaction_action_button = "-";
		}

		return $product_transaction_action_button;
	}

}

function alterUser($userID, $column_name, $column_value)
{
	global $connection_server;

	$userID = mysqli_real_escape_string($connection_server, trim(strip_tags($userID)));
	$column_name = mysqli_real_escape_string($connection_server, trim(strip_tags($column_name)));
	$column_value = mysqli_real_escape_string($connection_server, trim(strip_tags($column_value)));

	if (!empty($userID) && !empty($column_name) && !empty($column_value)) {
		$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
		$get_logged_user_det = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_vendor_det["id"] . "' && username='$userID'");
		if (mysqli_num_rows($get_logged_user_det) == 1) {
			while ($user_details = mysqli_fetch_assoc($get_logged_user_det)) {
				$vendor_id = $user_details["vendor_id"];
				$username_id = $user_details["username"];
				$update_user_details = mysqli_query($connection_server, "UPDATE sas_users SET $column_name='$column_value' WHERE vendor_id='$vendor_id' && username='$username_id'");
				if ($update_user_details == true) {
					return "success";
				} else {
					return "failed";
				}
			}
		} else {
			return "failed";
		}
	}
}

function alterVendor($userID, $column_name, $column_value)
{
	global $connection_server;

	$userID = mysqli_real_escape_string($connection_server, trim(strip_tags($userID)));
	$column_name = mysqli_real_escape_string($connection_server, trim(strip_tags($column_name)));
	$column_value = mysqli_real_escape_string($connection_server, trim(strip_tags($column_value)));

	if (!empty($userID) && !empty($column_name) && !empty($column_value)) {
		$get_logged_user_det = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='$userID'");
		if (mysqli_num_rows($get_logged_user_det) == 1) {
			while ($user_details = mysqli_fetch_assoc($get_logged_user_det)) {
				$vendor_id = $user_details["id"];
				$update_user_details = mysqli_query($connection_server, "UPDATE sas_vendors SET $column_name='$column_value' WHERE id='$vendor_id'");
				if ($update_user_details == true) {
					return "success";
				} else {
					return "failed";
				}
			}
		} else {
			return "failed";
		}
	}
}

function alterAPI($userID, $apiID, $column_name, $column_value)
{
	global $connection_server;

	$userID = mysqli_real_escape_string($connection_server, trim(strip_tags($userID)));
	$apiID = mysqli_real_escape_string($connection_server, trim(strip_tags($apiID)));
	$column_name = mysqli_real_escape_string($connection_server, trim(strip_tags($column_name)));
	$column_value = mysqli_real_escape_string($connection_server, trim($column_value));

	if (!empty($userID) && !empty($apiID) && !empty($column_name) && in_array($column_value, array(0, 1))) {
		$get_logged_user_det = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='$userID'");
		if (mysqli_num_rows($get_logged_user_det) == 1) {
			while ($user_details = mysqli_fetch_assoc($get_logged_user_det)) {
				$vendor_id = $user_details["id"];
				$update_user_details = mysqli_query($connection_server, "UPDATE sas_apis SET $column_name='$column_value' WHERE id='$apiID' && vendor_id='$vendor_id'");
				if ($update_user_details == true) {
					return "success";
				} else {
					return "failed";
				}
			}
		} else {
			return "failed";
		}
	}
}

function toDecimal($number, $decimalIndex)
{
	if (is_numeric($number) && is_numeric($decimalIndex)) {
		$exp_number = explode(".", trim($number));
		$firstNumber = $exp_number[0];
		$decimalNumber = isset($exp_number[1]) ? substr($exp_number[1], 0, $decimalIndex) : 0;
		return ($firstNumber + 0) . "." . sprintf("%0" . $decimalIndex . "d", $decimalNumber);
	} else {
		return "non-numeric string";
	}
}

function productIDBlockChecker($item_id)
{
	global $connection_server, $get_logged_user_details;

	$item_id = mysqli_real_escape_string($connection_server, trim(strip_tags($item_id)));
	if (!empty($item_id) && is_numeric($item_id)) {
		$select_item_query = mysqli_query($connection_server, "SELECT * FROM sas_id_blocking_system WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_id='$item_id'");
		if (mysqli_num_rows($select_item_query) == 0) {
			return "success";
		} else {
			if (mysqli_num_rows($select_item_query) > 0) {
				return "failed";
			}
		}
	} else {
		return "failed";
	}
}

function productIDPurchaseChecker($item_id, $product_type)
{
	global $connection_server, $get_logged_user_details;

	$item_id = mysqli_real_escape_string($connection_server, trim(strip_tags($item_id)));
	$product_type = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($product_type))));

	if (!empty($item_id) && is_numeric($item_id) && !empty($product_type)) {
		$get_user_daily_purchase_limit_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_daily_purchase_limit WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' LIMIT 1"));

		// If limit is not set or set to 0, no limit enforced
		if(empty($get_user_daily_purchase_limit_details["limit"]) || ($get_user_daily_purchase_limit_details["limit"] <= 0)){
			return "success";
		}

		$select_validated_item_query = mysqli_query($connection_server, "SELECT * FROM sas_validated_user_purchase_id_list WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_id='$item_id'");

		// Whitelisted IDs bypass the limit
		if (mysqli_num_rows($select_validated_item_query) > 0) {
			return "success";
		}

		$select_item_query = mysqli_query($connection_server, "SELECT * FROM sas_daily_purchase_tracker WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $get_logged_user_details["username"] . "' && product_id='$item_id' && product_type='$product_type' && date_purchased='" . date("Y-m-d") . "'");

		if (mysqli_num_rows($select_item_query) < $get_user_daily_purchase_limit_details["limit"]) {
			return "success";
		} else {
			return "limit_exceeded";
		}
	} else {
		return "failed";
	}
}

function updateProductPurchaseList($reference, $item_id, $product_type)
{
	global $connection_server, $get_logged_user_details;

	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($reference)));
	$item_id = mysqli_real_escape_string($connection_server, trim(strip_tags($item_id)));
	$product_type = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($product_type))));

	if (!empty($reference) && is_numeric($reference) && !empty($item_id) && is_numeric($item_id) && !empty($product_type)) {
		mysqli_query($connection_server, "INSERT INTO sas_daily_purchase_tracker (vendor_id, reference, product_type, product_id, username, date_purchased) VALUES ('" . $get_logged_user_details["vendor_id"] . "','$reference','$product_type','$item_id','" . $get_logged_user_details["username"] . "','" . date("Y-m-d") . "')");

		$get_user_daily_purchase_limit_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_daily_purchase_limit WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' LIMIT 1"));

		if(!empty($get_user_daily_purchase_limit_details["limit"]) && ($get_user_daily_purchase_limit_details["limit"] > 0)){
			$select_item_query = mysqli_query($connection_server, "SELECT * FROM sas_daily_purchase_tracker WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $get_logged_user_details["username"] . "' && product_id='$item_id' && product_type='$product_type' && date_purchased='" . date("Y-m-d") . "'");

			if (mysqli_num_rows($select_item_query) > $get_user_daily_purchase_limit_details["limit"]) {
				//Block Suspicious Accounts immediately
				alterUser($get_logged_user_details["username"], "status", "2");
				alterUser($get_logged_user_details["username"], "api_status", "2");

				// Email Beginning
				$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
				$transaction_template_encoded_text_array = array("{admin_firstname}" => $get_vendor_det["firstname"], "{admin_lastname}" => $get_vendor_det["lastname"], "{username}" => $get_logged_user_details["username"], "{firstname}" => $get_logged_user_details["firstname"], "{lastname}" => $get_logged_user_details["lastname"]);
				$raw_transaction_template_subject = "Urgent - Potential Suspicious Activity for User: [{username}] ([{firstname} {lastname}])";
				$raw_transaction_template_body = "Dear {admin_firstname} {admin_lastname}, " . "\n\n" . "Please be advised that user [{username}] ([{firstname} {lastname}]) has exceeded their daily transaction limit and has been automatically suspended." . "\n\n" . "As per our security protocols, I recommend you immediately:" . "\n\n" . "Review their recent activity for any suspicious patterns or unusually large transactions." . "\n" . "Contact the user to inquire about their activity and confirm its legitimacy." . "\n" . "If you suspect any illegal activity, please escalate the matter to the appropriate authorities immediately." . "\n\n" . "Thank you for your prompt attention to this matter.";
				foreach ($transaction_template_encoded_text_array as $array_key => $array_val) {
					$raw_transaction_template_subject = str_replace($array_key, $array_val, $raw_transaction_template_subject);
					$raw_transaction_template_body = str_replace($array_key, $array_val, $raw_transaction_template_body);
				}
				sendVendorEmail($get_vendor_det["email"], $raw_transaction_template_subject, $raw_transaction_template_body);
				// Email End
			}
		}
		return "success";
	} else {
		return "failed";
	}
}

function removeProductPurchaseList($reference)
{
	global $connection_server, $get_logged_user_details;

	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($reference)));

	if (!empty($reference) && is_numeric($reference)) {
		$select_item_query = mysqli_query($connection_server, "SELECT * FROM sas_daily_purchase_tracker WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $get_logged_user_details["username"] . "' && reference='$reference'");
		if (mysqli_num_rows($select_item_query) >= 1) {
			mysqli_query($connection_server, "DELETE FROM sas_daily_purchase_tracker WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $get_logged_user_details["username"] . "' && reference='$reference'");
			return "success";
		} else {
			return "failed";
		}
	} else {
		return "failed";
	}
}

function formDate($date_time)
{
	if (!empty($date_time) || ($date_time !== NULL)) {
		$exp_date_time = array_filter(explode(" ", trim($date_time)));
		$date = $exp_date_time[0];
		$time = $exp_date_time[1];

		$month = array("01" => "January", "02" => "Febuary", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");
		$exp_date = explode("-", trim($date));
		$post_date = $month[$exp_date[1]] . ", " . $exp_date[2] . " " . $exp_date[0];
		$exp_time = explode(":", trim($time));
		if ($exp_time[0] > 12) {
			$hour = ($exp_time[0] - 12);
			$period = "pm";
		} else {
			$hour = $exp_time[0];
			$period = "am";
		}
		$min = $exp_time[1];
		$sec = $exp_time[2];

		return $post_date . " " . $hour . ":" . $min . "." . $sec . $period;
	} else {
		return "DateTime Not Available";
	}
}

function formDateWithoutTime($date)
{
	if (!empty($date) || ($date !== NULL)) {
		$month = array("01" => "January", "02" => "Febuary", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");
		$exp_date = explode("-", trim($date));
		$post_date = $month[$exp_date[1]] . " " . $exp_date[2] . ", " . $exp_date[0];

		return $post_date;
	} else {
		return "Date Not Available";
	}
}

function timeFrame($time)
{
	if (!empty($time) || ($time !== NULL)) {
		$exp_time = array_filter(explode(":", trim($time)));
		$hr = $exp_time[0];
		$min = $exp_time[1];
		if (in_array($hr, range(0, 11))) {
			return $hr . ":" . $min . "am";
		}
		if (in_array($hr, range(12, 24))) {
			return ($hr - 12) . ":" . $min . "pm";
		}
	} else {
		return "Time Not Available";
	}
}

function getUserEmailTemplate($row_id, $column_name)
{
	global $connection_server;
	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$template_details = mysqli_query($connection_server, "SELECT * FROM sas_email_templates WHERE vendor_id='" . $get_vendor_det["id"] . "' && email_type='$row_id'");
	if (mysqli_num_rows($template_details) == 1) {
		$template_array = mysqli_fetch_array($template_details);
		if (isset($template_array[$column_name])) {
			return $template_array[$column_name];
		} else {
			//Column Mismatch
			return "";
		}
	} else {
		if (mysqli_num_rows($template_details) > 1) {
			//Duplicated Details
			return "";
		} else {
			if (mysqli_num_rows($template_details) == 0) {
				//Null
				return "";
			}
		}
	}
}

function getVendorEmailTemplate($row_id, $column_name)
{
	global $connection_server;
	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$template_details = mysqli_query($connection_server, "SELECT * FROM sas_email_templates WHERE vendor_id='" . $get_vendor_det["id"] . "' && email_type='$row_id'");
	if (mysqli_num_rows($template_details) == 1) {
		$template_array = mysqli_fetch_array($template_details);
		if (isset($template_array[$column_name])) {
			return $template_array[$column_name];
		} else {
			//Column Mismatch
			return "";
		}
	} else {
		if (mysqli_num_rows($template_details) > 1) {
			//Duplicated Details
			return "";
		} else {
			if (mysqli_num_rows($template_details) == 0) {
				//Null
				return "";
			}
		}
	}
}

function getSuperAdminEmailTemplate($row_id, $column_name)
{
	global $connection_server;
	$template_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_email_templates WHERE email_type='$row_id'");
	if (mysqli_num_rows($template_details) == 1) {
		$template_array = mysqli_fetch_array($template_details);
		if (isset($template_array[$column_name])) {
			return $template_array[$column_name];
		} else {
			//Column Mismatch
			return "";
		}
	} else {
		if (mysqli_num_rows($template_details) > 1) {
			//Duplicated Details
			return "";
		} else {
			if (mysqli_num_rows($template_details) == 0) {
				//Null
				return "";
			}
		}
	}
}

function get_admin_info($email, $column_name)
{
	global $connection_server;
	$checkadmin = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id = '" . $_SERVER["HTTP_HOST"] . "' AND email='" . $email . "'");
	if (mysqli_num_rows($checkadmin) == 1) {
		$get_admin_details = mysqli_fetch_array($checkadmin);
		$column_exists = false;
		$describe_table = mysqli_query($connection_server, "DESCRIBE sas_vendors");
		while ($row = mysqli_fetch_assoc($describe_table)) {
			if ($row["Field"] === strtolower($column_name)) {
				$column_exists = true;
			}
		}
		if ($column_exists) {
			return $get_admin_details[$column_name];
		} else {
			return "Error: Requested field not exists";
		}
	} else {
		return "Error: Vendor not exists";
	}
}

function get_user_info($username_or_email, $column_name)
{
	global $connection_server;
	$select_vendor_table = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1");
	if (mysqli_num_rows($select_vendor_table) == 1) {
		$get_vendor_detail = mysqli_fetch_array($select_vendor_table);
		$checkuser = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $get_vendor_detail["id"] . "' AND (username = '" . $username_or_email . "' OR email='" . $username_or_email . "')");
		if (mysqli_num_rows($checkuser) == 1) {
			$get_user_details = mysqli_fetch_array($checkuser);
			$column_exists = false;
			$describe_table = mysqli_query($connection_server, "DESCRIBE sas_users");
			while ($row = mysqli_fetch_assoc($describe_table)) {
				if ($row["Field"] === strtolower($column_name)) {
					$column_exists = true;
				}
			}
			if ($column_exists) {
				return $get_user_details[$column_name];
			} else {
				return "Error: Requested field not exists";
			}
		} else {
			return "Error: User not exists";
		}
	} else {
		return "Error: Vendor not exists";
	}
}


function beeMailer($recipient_email, $email_subject, $email_body)
{

	global $connection_server;
	// Always set content-type when sending HTML email
	$mail_headers = "MIME-Version: 1.0" . "\r\n";
	$mail_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$mail_headers .= 'From: ' . $email_subject . ' <no-reply@' . $_SERVER["HTTP_HOST"] . '>' . "\r\n";
	$mail_headers .= 'Cc: ' . get_admin_info(1, "email") . "\r\n";
	//$mail_headers .= 'Subject: '.$email_subject."\r\n";

	$website_admin_phone_number = "234" . substr(get_admin_info(1, "phone_number"), 1, 11);
	$details_array = array($website_admin_phone_number);
	$mail_html_body = mailDesignTemplate($email_subject, $email_body, $details_array);
	customBCMailSender('', $recipient_email, $email_subject, $mail_html_body, $mail_headers);
	fwrite(fopen("./email-msg.txt", "a++"), "\n" . $recipient_email . " || " . strtoupper($email_subject) . " || " . $email_body . "\n");
}

function sendVendorEmail($recipient_email, $email_subject, $email_body)
{
	global $connection_server, $get_logged_user_details, $get_logged_admin_details;
	if (isset($get_logged_user_details) && !empty($get_logged_user_details["username"])) {
		$logged_account_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='" . $get_logged_user_details["vendor_id"] . "'"));
	} else {
		if (isset($get_logged_admin_details) && !empty($get_logged_admin_details["email"])) {
			$logged_account_details = $get_logged_admin_details;
		} else {
			$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
			$logged_account_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='" . $get_vendor_det["id"] . "'"));
		}
	}
	// Always set content-type when sending HTML email
	$mail_headers = "MIME-Version: 1.0" . "\r\n";
	$mail_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$mail_headers .= 'From: ' . $email_subject . ' <no-reply@' . $_SERVER["HTTP_HOST"] . '>' . "\r\n";
	$mail_headers .= 'Cc: ' . $logged_account_details["email"] . "\r\n";
	//$mail_headers .= 'Subject: '.$email_subject."\r\n";

	$website_admin_phone_number = "234" . substr($logged_account_details["phone_number"], 1, 11);
	$details_array = array($website_admin_phone_number);
	$mail_html_body = mailDesignTemplate($email_subject, $email_body, $details_array);
	customBCMailSender('', $recipient_email, $email_subject, $mail_html_body, $mail_headers);
	//fwrite(fopen("./email-msg.txt", "a++"), "\n".$recipient_email." || ".strtoupper($email_subject)." || ".$email_body."\n");
}

function sendSuperAdminEmail($recipient_email, $email_subject, $email_body)
{
	global $connection_server, $get_logged_spadmin_details;
	if (isset($get_logged_spadmin_details) && !empty($get_logged_spadmin_details["email"])) {
		$logged_account_details = $get_logged_spadmin_details;
	} else {
		$logged_account_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_super_admin WHERE email='$recipient_email' LIMIT 1"));
	}

	// Always set content-type when sending HTML email
	$mail_headers = "MIME-Version: 1.0" . "\r\n";
	$mail_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$mail_headers .= 'From: ' . $email_subject . ' <no-reply@' . $_SERVER["HTTP_HOST"] . '>' . "\r\n";
	$mail_headers .= 'Cc: ' . $logged_account_details["email"] . "\r\n";
	//$mail_headers .= 'Subject: '.$email_subject."\r\n";

	$website_admin_phone_number = "234" . substr($logged_account_details["phone_number"], 1, 11);
	$details_array = array($website_admin_phone_number);
	$mail_html_body = mailDesignTemplate($email_subject, $email_body, $details_array);
	customBCMailSender('', $recipient_email, $email_subject, $mail_html_body, $mail_headers);
	//fwrite(fopen("./email-msg.txt", "a++"), "\n".$recipient_email." || ".strtoupper($email_subject)." || ".$email_body."\n");
}

function sendVendorEmailSpecific($mailto_type, $email_subject, $email_body)
{
	global $connection_server, $get_logged_admin_details;
	$mailto_array = array("all" => "(status='1' OR status='2' OR status='3')", "a" => "status='1'", "b" => "status='2'", "d" => "status='3'", "bd" => "(status='2' OR status='3')");
	if (in_array($mailto_type, array_keys($mailto_array))) {
		$select_users = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && " . $mailto_array[$mailto_type]);
		if (mysqli_num_rows($select_users) >= 1) {
			while ($each_user = mysqli_fetch_assoc($select_users)) {
				$reg_template_encoded_text_array = array("{firstname}" => $each_user["firstname"], "{lastname}" => $each_user["lastname"], "{username}" => $each_user["username"], "{address}" => $each_user["home_address"], "{email}" => $each_user["email"], "{phone}" => $each_user["phone_number"]);
				$raw_reg_template_subject = $email_subject;
				$raw_reg_template_body = $email_body;
				foreach ($reg_template_encoded_text_array as $array_key => $array_val) {
					$raw_reg_template_subject = str_replace($array_key, $array_val, $raw_reg_template_subject);
					$raw_reg_template_body = str_replace($array_key, $array_val, $raw_reg_template_body);
				}
				fwrite(fopen("email.txt", "a++"), "\n" . $raw_reg_template_body);
				sendVendorEmail($each_user["email"], $raw_reg_template_subject, $raw_reg_template_body);
			}
			return "success";
		} else {
			return "failed";
		}
	} else {
		return "error";
	}

}

function sendSuperAdminEmailSpecific($mailto_type, $email_subject, $email_body)
{
	global $connection_server;
	$mailto_array = array("all" => "(status='1' OR status='2' OR status='3')", "a" => "status='1'", "b" => "status='2'", "d" => "status='3'", "bd" => "(status='2' OR status='3')");
	if (in_array($mailto_type, array_keys($mailto_array))) {
		$select_vendors = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE " . $mailto_array[$mailto_type]);
		if (mysqli_num_rows($select_vendors) >= 1) {
			while ($each_vendor = mysqli_fetch_assoc($select_vendors)) {
				$reg_template_encoded_text_array = array("{firstname}" => $each_vendor["firstname"], "{lastname}" => $each_vendor["lastname"], "{address}" => $each_vendor["home_address"], "{email}" => $each_vendor["email"], "{phone}" => $each_vendor["phone_number"]);
				$raw_reg_template_subject = $email_subject;
				$raw_reg_template_body = $email_body;
				foreach ($reg_template_encoded_text_array as $array_key => $array_val) {
					$raw_reg_template_subject = str_replace($array_key, $array_val, $raw_reg_template_subject);
					$raw_reg_template_body = str_replace($array_key, $array_val, $raw_reg_template_body);
				}
				sendSuperAdminEmail($each_vendor["email"], $raw_reg_template_subject, $raw_reg_template_body);
			}
			return "success";
		} else {
			return "failed";
		}
	} else {
		return "error";
	}

}


function createVendorEmailTemplateIfNotExists($email_type, $subject, $body)
{
	global $connection_server;

	$email_type = mysqli_real_escape_string($connection_server, trim(strip_tags($email_type)));
	$subject = mysqli_real_escape_string($connection_server, trim(strip_tags($subject)));
	$body = mysqli_real_escape_string($connection_server, trim(strip_tags($body)));

	if (!empty($subject) && !empty($body) && !empty($email_type)) {
		$vendor_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "'"));
		$template_details = mysqli_query($connection_server, "SELECT * FROM sas_email_templates WHERE vendor_id='" . $vendor_details["id"] . "' && email_type='$email_type'");
		if (mysqli_num_rows($template_details) == 0) {
			mysqli_query($connection_server, "INSERT INTO sas_email_templates (vendor_id, email_type, subject, body) VALUES ('" . $vendor_details["id"] . "', '$email_type', '$subject', '$body')");
			return "success";
		} else {
			return "failed";
		}
	} else {
		return "failed";
	}
}

function createSuperAdminEmailTemplateIfNotExists($email_type, $subject, $body)
{
	global $connection_server;

	$email_type = mysqli_real_escape_string($connection_server, trim(strip_tags($email_type)));
	$subject = mysqli_real_escape_string($connection_server, trim(strip_tags($subject)));
	$body = mysqli_real_escape_string($connection_server, trim(strip_tags($body)));

	if (!empty($subject) && !empty($body) && !empty($email_type)) {
		$template_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_email_templates WHERE email_type='$email_type'");
		if (mysqli_num_rows($template_details) == 0) {
			mysqli_query($connection_server, "INSERT INTO sas_super_admin_email_templates (email_type, subject, body) VALUES ('$email_type', '$subject', '$body')");
			return "success";
		} else {
			return "failed";
		}
	} else {
		return "failed";
	}
}


//Payment Gateways
//User Token
function getUserMonnifyAccessToken()
{
	global $connection_server, $get_logged_user_details;
	$select_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_payment_gateways WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && gateway_name='monnify'");

	if ((mysqli_num_rows($select_gateway_details) == 1)) {
		$get_gateway_details = mysqli_fetch_array($select_gateway_details);
		if ($get_gateway_details["status"] == "1") {
			$monnify_public_key = trim($get_gateway_details["public_key"]);
			$monnify_secret_key = trim($get_gateway_details["secret_key"]);

			if (!empty($monnify_public_key) && !empty($monnify_secret_key)) {

				$curl_url = "https://api.monnify.com/api/v1/auth/login";
				$curl_request = curl_init($curl_url);
				curl_setopt($curl_request, CURLOPT_POST, true);

				// $post_field_array = array("username" => $username, "password" => $password);
				// curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
				curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

				$header_field_array = array("Authorization: Basic " . base64_encode($monnify_public_key . ":" . $monnify_secret_key), "Content-Type: application/json", "Content-Length: 0");
				curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
				curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

				curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
				$curl_result = curl_exec($curl_request);
				$curl_json_result = json_decode($curl_result, true);

				if ($curl_json_result !== null) {
					if (($curl_json_result["responseMessage"] == "success") && isset($curl_json_result["responseBody"]["accessToken"]) && !empty($curl_json_result["responseBody"]["accessToken"])) {
						$accessToken = $curl_json_result["responseBody"]["accessToken"];
						$monnify_json_response_array = array("status" => "success", "message" => "Token Generated", "token" => $accessToken);
						return json_encode($monnify_json_response_array, true);
					} else {
						$monnify_json_response_array = array("status" => "failed", "message" => "No Access Token");
						return json_encode($monnify_json_response_array, true);
					}
				}

				if ($curl_result === false) {
					$curl_error = curl_error($curl_request);
					$monnify_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
					return json_encode($monnify_json_response_array, true);
				}

				if ($curl_json_result === null) {
					$curl_error = curl_error($curl_request);
					$monnify_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
					return json_encode($monnify_json_response_array, true);
				}

				curl_close($curl_request);
			} else {
				$monnify_json_response_array = array("status" => "failed", "message" => "Required Keys Are Empty");
				return json_encode($monnify_json_response_array, true);
			}
		} else {
			$monnify_json_response_array = array("status" => "failed", "message" => "Monnify Is Down/Disabled");
			return json_encode($monnify_json_response_array, true);
		}
	} else {
		$monnify_json_response_array = array("status" => "failed", "message" => "Monnify Has Not Been Installed");
		return json_encode($monnify_json_response_array, true);
	}
}

//Vendor User Token
function getVendorUserMonnifyAccessToken()
{
	global $connection_server, $get_logged_admin_details;
	$select_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_payment_gateways WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && gateway_name='monnify'");

	if ((mysqli_num_rows($select_gateway_details) == 1)) {
		$get_gateway_details = mysqli_fetch_array($select_gateway_details);
		if ($get_gateway_details["status"] == "1") {
			$monnify_public_key = trim($get_gateway_details["public_key"]);
			$monnify_secret_key = trim($get_gateway_details["secret_key"]);

			if (!empty($monnify_public_key) && !empty($monnify_secret_key)) {

				$curl_url = "https://api.monnify.com/api/v1/auth/login";
				$curl_request = curl_init($curl_url);
				curl_setopt($curl_request, CURLOPT_POST, true);

				// $post_field_array = array("username" => $username, "password" => $password);
				// curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
				curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

				$header_field_array = array("Authorization: Basic " . base64_encode($monnify_public_key . ":" . $monnify_secret_key), "Content-Type: application/json", "Content-Length: 0");
				curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
				curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

				curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
				$curl_result = curl_exec($curl_request);
				$curl_json_result = json_decode($curl_result, true);

				if ($curl_json_result !== null) {
					if (($curl_json_result["responseMessage"] == "success") && isset($curl_json_result["responseBody"]["accessToken"]) && !empty($curl_json_result["responseBody"]["accessToken"])) {
						$accessToken = $curl_json_result["responseBody"]["accessToken"];
						$monnify_json_response_array = array("status" => "success", "message" => "Token Generated", "token" => $accessToken);
						return json_encode($monnify_json_response_array, true);
					} else {
						$monnify_json_response_array = array("status" => "failed", "message" => "No Access Token");
						return json_encode($monnify_json_response_array, true);
					}
				}

				if ($curl_result === false) {
					$curl_error = curl_error($curl_request);
					$monnify_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
					return json_encode($monnify_json_response_array, true);
				}

				if ($curl_json_result === null) {
					$curl_error = curl_error($curl_request);
					$monnify_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
					return json_encode($monnify_json_response_array, true);
				}

				curl_close($curl_request);
			} else {
				$monnify_json_response_array = array("status" => "failed", "message" => "Required Keys Are Empty");
				return json_encode($monnify_json_response_array, true);
			}
		} else {
			$monnify_json_response_array = array("status" => "failed", "message" => "Monnify Is Down/Disabled");
			return json_encode($monnify_json_response_array, true);
		}
	} else {
		$monnify_json_response_array = array("status" => "failed", "message" => "Monnify Has Not Been Installed");
		return json_encode($monnify_json_response_array, true);
	}
}

//Vendor Token
function getVendorMonnifyAccessToken()
{
	global $connection_server, $get_logged_admin_details;
	$select_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='monnify'");

	if ((mysqli_num_rows($select_gateway_details) == 1)) {
		$get_gateway_details = mysqli_fetch_array($select_gateway_details);
		if ($get_gateway_details["status"] == "1") {
			$monnify_public_key = trim($get_gateway_details["public_key"]);
			$monnify_secret_key = trim($get_gateway_details["secret_key"]);

			if (!empty($monnify_public_key) && !empty($monnify_secret_key)) {

				$curl_url = "https://api.monnify.com/api/v1/auth/login";
				$curl_request = curl_init($curl_url);
				curl_setopt($curl_request, CURLOPT_POST, true);

				// $post_field_array = array("username" => $username, "password" => $password);
				// curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
				curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

				$header_field_array = array("Authorization: Basic " . base64_encode($monnify_public_key . ":" . $monnify_secret_key), "Content-Type: application/json", "Content-Length: 0");
				curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
				curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

				curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
				$curl_result = curl_exec($curl_request);
				$curl_json_result = json_decode($curl_result, true);

				if ($curl_json_result !== null) {
					if (($curl_json_result["responseMessage"] == "success") && isset($curl_json_result["responseBody"]["accessToken"]) && !empty($curl_json_result["responseBody"]["accessToken"])) {
						$accessToken = $curl_json_result["responseBody"]["accessToken"];
						$monnify_json_response_array = array("status" => "success", "message" => "Token Generated", "token" => $accessToken);
						return json_encode($monnify_json_response_array, true);
					} else {
						$monnify_json_response_array = array("status" => "failed", "message" => "No Access Token");
						return json_encode($monnify_json_response_array, true);
					}
				}

				if ($curl_result === false) {
					$curl_error = curl_error($curl_request);
					$monnify_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
					return json_encode($monnify_json_response_array, true);
				}

				if ($curl_json_result === null) {
					$curl_error = curl_error($curl_request);
					$monnify_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
					return json_encode($monnify_json_response_array, true);
				}

				curl_close($curl_request);
			} else {
				$monnify_json_response_array = array("status" => "failed", "message" => "Required Keys Are Empty");
				return json_encode($monnify_json_response_array, true);
			}
		} else {
			$monnify_json_response_array = array("status" => "failed", "message" => "Monnify Is Down/Disabled");
			return json_encode($monnify_json_response_array, true);
		}
	} else {
		$monnify_json_response_array = array("status" => "failed", "message" => "Monnify Has Not Been Installed");
		return json_encode($monnify_json_response_array, true);
	}
}

//Super Admin Token
function getSuperAdminMonnifyAccessToken()
{
	global $connection_server;
	$select_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='monnify'");

	if ((mysqli_num_rows($select_gateway_details) == 1)) {
		$get_gateway_details = mysqli_fetch_array($select_gateway_details);

		$monnify_public_key = trim($get_gateway_details["public_key"]);
		$monnify_secret_key = trim($get_gateway_details["secret_key"]);

		if (!empty($monnify_public_key) && !empty($monnify_secret_key)) {

			$curl_url = "https://api.monnify.com/api/v1/auth/login";
			$curl_request = curl_init($curl_url);
			curl_setopt($curl_request, CURLOPT_POST, true);

			// $post_field_array = array("username" => $username, "password" => $password);
			// curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
			curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

			$header_field_array = array("Authorization: Basic " . base64_encode($monnify_public_key . ":" . $monnify_secret_key), "Content-Type: application/json", "Content-Length: 0");
			curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
			curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

			curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
			$curl_result = curl_exec($curl_request);
			$curl_json_result = json_decode($curl_result, true);

			if ($curl_json_result !== null) {
				if (($curl_json_result["responseMessage"] == "success") && isset($curl_json_result["responseBody"]["accessToken"]) && !empty($curl_json_result["responseBody"]["accessToken"])) {
					$accessToken = $curl_json_result["responseBody"]["accessToken"];
					$monnify_json_response_array = array("status" => "success", "message" => "Token Generated", "token" => $accessToken);
					return json_encode($monnify_json_response_array, true);
				} else {
					$monnify_json_response_array = array("status" => "failed", "message" => "No Access Token");
					return json_encode($monnify_json_response_array, true);
				}
			}

			if ($curl_result === false) {
				$curl_error = curl_error($curl_request);
				$monnify_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
				return json_encode($monnify_json_response_array, true);
			}

			if ($curl_json_result === null) {
				$curl_error = curl_error($curl_request);
				$monnify_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
				return json_encode($monnify_json_response_array, true);
			}

			curl_close($curl_request);
		} else {
			$monnify_json_response_array = array("status" => "failed", "message" => "Required Keys Are Empty");
			return json_encode($monnify_json_response_array, true);
		}
	} else {
		$monnify_json_response_array = array("status" => "failed", "message" => "Monnify Has Not Been Installed");
		return json_encode($monnify_json_response_array, true);
	}
}

function getMonnifyBanks($generatedAccessToken)
{
	global $connection_server;

	$curl_url = "https://api.monnify.com/api/v1/banks";
	$curl_request = curl_init($curl_url);
	curl_setopt($curl_request, CURLOPT_HTTPGET, true);

	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

	$header_field_array = array("Authorization: Bearer " . $generatedAccessToken, "Content-Type: application/json");
	curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
	curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

	curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
	$curl_result = curl_exec($curl_request);
	$curl_json_result = json_decode($curl_result, true);

	if ($curl_json_result !== null) {
		if (($curl_json_result["responseMessage"] == "success")) {
			$bankArrayList = $curl_json_result["responseBody"];
			$monnify_json_response_array = array("status" => "success", "message" => "Banks Generated", "banks" => $bankArrayList);
			return json_encode($monnify_json_response_array, true);
		} else {
			$monnify_json_response_array = array("status" => "failed", "message" => "No Banks");
			return json_encode($monnify_json_response_array, true);
		}
	}

	if ($curl_result === false) {
		$curl_error = curl_error($curl_request);
		$monnify_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
		return json_encode($monnify_json_response_array, true);
	}

	if ($curl_json_result === null) {
		$curl_error = curl_error($curl_request);
		$monnify_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
		return json_encode($monnify_json_response_array, true);
	}

	curl_close($curl_request);


}

function makeMonnifyRequest($req_method, $generatedAccessToken, $parameter_url, $req_body)
{
	global $connection_server;

	$curl_url = "https://api.monnify.com/" . $parameter_url;
	$curl_request = curl_init($curl_url);
	if ($req_method == "post") {
		curl_setopt($curl_request, CURLOPT_POST, true);
	} else {
		if ($req_method == "get") {
			curl_setopt($curl_request, CURLOPT_HTTPGET, true);
		}
	}

	if (is_array($req_body)) {
		$post_field_array = $req_body;
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
	}
	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

	$header_field_array = array("Authorization: Bearer " . $generatedAccessToken, "Content-Type: application/json");
	curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
	curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

	curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
	$curl_result = curl_exec($curl_request);
	$curl_json_result = json_decode($curl_result, true);

	if ($curl_json_result !== null) {
		if (($curl_json_result["responseMessage"] == "success")) {
			$encoded_json_result = json_encode($curl_json_result, true);
			$monnify_json_response_array = array("status" => "success", "message" => "Request Successful", "json_result" => $encoded_json_result);
			return json_encode($monnify_json_response_array, true);
		} else {
			$monnify_json_response_array = array("status" => "failed", "message" => "Request Failed");
			return json_encode($monnify_json_response_array, true);
		}
	}

	if ($curl_result === false) {
		$curl_error = curl_error($curl_request);
		$monnify_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
		return json_encode($monnify_json_response_array, true);
	}

	if ($curl_json_result === null) {
		$curl_error = curl_error($curl_request);
		$monnify_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
		return json_encode($monnify_json_response_array, true);
	}

	curl_close($curl_request);


}

//Payvessel
//User Token
function getUserPayvesselAccessToken()
{
	global $connection_server, $get_logged_user_details;
	$select_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_payment_gateways WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && gateway_name='payvessel'");

	if ((mysqli_num_rows($select_gateway_details) == 1)) {
		$get_gateway_details = mysqli_fetch_array($select_gateway_details);
		if ($get_gateway_details["status"] == "1") {
			$payvessel_public_key = trim($get_gateway_details["public_key"]);
			$payvessel_secret_key = trim($get_gateway_details["secret_key"]);
			$payvessel_encrypt_key = trim($get_gateway_details["encrypt_key"]);

			if (!empty($payvessel_public_key) && !empty($payvessel_secret_key)) {
				$accessToken = base64_encode($payvessel_public_key . ":" . $payvessel_secret_key);
				$payvessel_json_response_array = array("status" => "success", "message" => "Token Generated", "encrypt_key" => $payvessel_encrypt_key, "token" => $accessToken);
				return json_encode($payvessel_json_response_array, true);
			} else {
				$payvessel_json_response_array = array("status" => "failed", "message" => "Required Keys Are Empty");
				return json_encode($payvessel_json_response_array, true);
			}
		} else {
			$payvessel_json_response_array = array("status" => "failed", "message" => "Payvessel Is Down/Disabled");
			return json_encode($payvessel_json_response_array, true);
		}
	} else {
		$payvessel_json_response_array = array("status" => "failed", "message" => "Payvessel Has Not Been Installed");
		return json_encode($payvessel_json_response_array, true);
	}
}

//Vendor User Token
function getVendorUserPayvesselAccessToken()
{
	global $connection_server, $get_logged_admin_details;
	$select_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_payment_gateways WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && gateway_name='payvessel'");

	if ((mysqli_num_rows($select_gateway_details) == 1)) {
		$get_gateway_details = mysqli_fetch_array($select_gateway_details);
		if ($get_gateway_details["status"] == "1") {
			$payvessel_public_key = trim($get_gateway_details["public_key"]);
			$payvessel_secret_key = trim($get_gateway_details["secret_key"]);
			$payvessel_encrypt_key = trim($get_gateway_details["encrypt_key"]);

			if (!empty($payvessel_public_key) && !empty($payvessel_secret_key)) {
				$accessToken = base64_encode($payvessel_public_key . ":" . $payvessel_secret_key);
				$payvessel_json_response_array = array("status" => "success", "message" => "Token Generated", "encrypt_key" => $payvessel_encrypt_key, "token" => $accessToken);
				return json_encode($payvessel_json_response_array, true);
			} else {
				$payvessel_json_response_array = array("status" => "failed", "message" => "Required Keys Are Empty");
				return json_encode($payvessel_json_response_array, true);
			}
		} else {
			$payvessel_json_response_array = array("status" => "failed", "message" => "Payvessel Is Down/Disabled");
			return json_encode($payvessel_json_response_array, true);
		}
	} else {
		$payvessel_json_response_array = array("status" => "failed", "message" => "Payvessel Has Not Been Installed");
		return json_encode($payvessel_json_response_array, true);
	}
}

//Vendor Token
function getVendorPayvesselAccessToken()
{
	global $connection_server, $get_logged_admin_details;
	$select_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='payvessel'");

	if ((mysqli_num_rows($select_gateway_details) == 1)) {
		$get_gateway_details = mysqli_fetch_array($select_gateway_details);
		if ($get_gateway_details["status"] == "1") {
			$payvessel_public_key = trim($get_gateway_details["public_key"]);
			$payvessel_secret_key = trim($get_gateway_details["secret_key"]);
			$payvessel_encrypt_key = trim($get_gateway_details["encrypt_key"]);

			if (!empty($payvessel_public_key) && !empty($payvessel_secret_key)) {
				$accessToken = base64_encode($payvessel_public_key . ":" . $payvessel_secret_key);
				$payvessel_json_response_array = array("status" => "success", "message" => "Token Generated", "encrypt_key" => $payvessel_encrypt_key, "token" => $accessToken);
				return json_encode($payvessel_json_response_array, true);
			} else {
				$payvessel_json_response_array = array("status" => "failed", "message" => "Required Keys Are Empty");
				return json_encode($payvessel_json_response_array, true);
			}
		} else {
			$payvessel_json_response_array = array("status" => "failed", "message" => "Payvessel Is Down/Disabled");
			return json_encode($payvessel_json_response_array, true);
		}
	} else {
		$payvessel_json_response_array = array("status" => "failed", "message" => "Payvessel Has Not Been Installed");
		return json_encode($payvessel_json_response_array, true);
	}
}

//Super Admin Token
function getSuperAdminPayvesselAccessToken()
{
	global $connection_server;
	$select_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='payvessel'");

	if ((mysqli_num_rows($select_gateway_details) == 1)) {
		$get_gateway_details = mysqli_fetch_array($select_gateway_details);
		if ($get_gateway_details["status"] == "1") {
			$payvessel_public_key = trim($get_gateway_details["public_key"]);
			$payvessel_secret_key = trim($get_gateway_details["secret_key"]);
			$payvessel_encrypt_key = trim($get_gateway_details["encrypt_key"]);

			if (!empty($payvessel_public_key) && !empty($payvessel_secret_key)) {
				$accessToken = base64_encode($payvessel_public_key . ":" . $payvessel_secret_key);
				$payvessel_json_response_array = array("status" => "success", "message" => "Token Generated", "encrypt_key" => $payvessel_encrypt_key, "token" => $accessToken);
				return json_encode($payvessel_json_response_array, true);
			} else {
				$payvessel_json_response_array = array("status" => "failed", "message" => "Required Keys Are Empty");
				return json_encode($payvessel_json_response_array, true);
			}
		} else {
			$payvessel_json_response_array = array("status" => "failed", "message" => "Payvessel Is Down/Disabled");
			return json_encode($payvessel_json_response_array, true);
		}
	} else {
		$payvessel_json_response_array = array("status" => "failed", "message" => "Payvessel Has Not Been Installed");
		return json_encode($payvessel_json_response_array, true);
	}
}

function makePayvesselRequest($req_method, $generatedAccessToken, $parameter_url, $req_body)
{
	global $connection_server;
	$key_explode = array_filter(explode(":", trim(base64_decode($generatedAccessToken))));
	$curl_url = "https://api.payvessel.com/" . $parameter_url;
	$curl_request = curl_init($curl_url);
	if ($req_method == "post") {
		curl_setopt($curl_request, CURLOPT_POST, true);
	} else {
		if ($req_method == "get") {
			curl_setopt($curl_request, CURLOPT_HTTPGET, true);
		}
	}

	if (is_array($req_body)) {
		$post_field_array = $req_body;
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
	}
	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

	$header_field_array = array("api-key: " . $key_explode[0], "api-secret: Bearer " . $key_explode[1], "Content-Type: application/json");
	curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
	curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

	curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
	$curl_result = curl_exec($curl_request);
	$curl_json_result = json_decode($curl_result, true);

	if ($curl_json_result !== null) {
		if (($curl_json_result["status"] === true)) {
			$encoded_json_result = json_encode($curl_json_result, true);
			$payvessel_json_response_array = array("status" => "success", "message" => "Request Successful", "json_result" => $encoded_json_result);
			return json_encode($payvessel_json_response_array, true);
		} else {
			$payvessel_json_response_array = array("status" => "failed", "message" => "Request Failed");
			return json_encode($payvessel_json_response_array, true);
		}
	}

	if ($curl_result === false) {
		$curl_error = curl_error($curl_request);
		$payvessel_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
		return json_encode($payvessel_json_response_array, true);
	}

	if ($curl_json_result === null) {
		$curl_error = curl_error($curl_request);
		$payvessel_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
		return json_encode($payvessel_json_response_array, true);
	}

	curl_close($curl_request);


}

function makeBeewaveRequest($req_method, $generatedAccessToken, $parameter_url, $req_body)
{
	global $connection_server;
	// $key_explode = array_filter(explode(":", trim(base64_decode($generatedAccessToken))));
	$curl_url = "https://merchant.beewave.ng/" . $parameter_url;
	$curl_request = curl_init($curl_url);
	if ($req_method == "post") {
		curl_setopt($curl_request, CURLOPT_POST, true);
	} else {
		if ($req_method == "get") {
			curl_setopt($curl_request, CURLOPT_HTTPGET, true);
		}
	}

	if (is_array($req_body)) {
		$post_field_array = $req_body;
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
	}
	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

	$header_field_array = array("Accept: application/json", "Content-Type: application/json");
	curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
	curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

	curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
	$curl_result = curl_exec($curl_request);
	$curl_json_result = json_decode($curl_result, true);

	if ($curl_json_result !== null) {
		if (($curl_json_result["status"] === true)) {
			$encoded_json_result = json_encode($curl_json_result, true);
			$beewave_json_response_array = array("status" => "success", "message" => "Request Successful", "json_result" => $encoded_json_result);
			return json_encode($beewave_json_response_array, true);
		} else {
			$beewave_json_response_array = array("status" => "failed", "message" => "Request Failed");
			return json_encode($beewave_json_response_array, true);
		}
	}

	if ($curl_result === false) {
		$curl_error = curl_error($curl_request);
		$beewave_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
		return json_encode($beewave_json_response_array, true);
	}

	if ($curl_json_result === null) {
		$curl_error = curl_error($curl_request);
		$beewave_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
		return json_encode($beewave_json_response_array, true);
	}

	curl_close($curl_request);

}


function makeFincraRequest($req_method, $generatedAccessToken, $parameter_url, $req_body)
{
	global $connection_server;
	// $key_explode = array_filter(explode(":", trim(base64_decode($generatedAccessToken))));
	$curl_url = "https://api.fincra.com/" . $parameter_url;
	$curl_request = curl_init($curl_url);
	if ($req_method == "post") {
		curl_setopt($curl_request, CURLOPT_POST, true);
	} else {
		if ($req_method == "get") {
			curl_setopt($curl_request, CURLOPT_HTTPGET, true);
		}
	}

	if (is_array($req_body)) {
		$post_field_array = $req_body;
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
	}
	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

	$header_field_array = array("api-key: " . $generatedAccessToken, "Accept: application/json", "Content-Type: application/json");
	curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
	curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

	curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
	$curl_result = curl_exec($curl_request);
	$curl_json_result = json_decode($curl_result, true);
	fwrite(fopen("fincra-account.txt", "a++"), $curl_result);
	if ($curl_json_result !== null) {
		if (($curl_json_result["success"] === true)) {
			$encoded_json_result = json_encode($curl_json_result, true);
			$fincra_json_response_array = array("status" => "success", "message" => "Request Successful", "json_result" => $encoded_json_result);
			return json_encode($fincra_json_response_array, true);
		} else {
			$fincra_json_response_array = array("status" => "failed", "message" => "Request Failed");
			return json_encode($fincra_json_response_array, true);
		}
	}

	if ($curl_result === false) {
		$curl_error = curl_error($curl_request);
		$fincra_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
		return json_encode($fincra_json_response_array, true);
	}

	if ($curl_json_result === null) {
		$curl_error = curl_error($curl_request);
		$fincra_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
		return json_encode($fincra_json_response_array, true);
	}

	curl_close($curl_request);

}

//VPay
// array(7) { ["status"]=> bool(true) ["message"]=> string(17) "Here. Your token." ["token"]=> string(287) "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmaXJzdG5hbWUiOiJFYmVuZXplciIsImJ1c2luZXNzbmFtZSI6IlBoaWxtb3JlIEljdCIsImJ1c2luZXNzaWQiOiI4SVZMIiwidmVyc2lvbiI6MSwidXNlciI6IjY0NWViYjM3Mjk2ZjNhNzU1YTVhMGJmMyIsImlhdCI6MTcxNjQ1OTQxMCwiZXhwIjoxNzE2NDU5NzEwfQ.UjNUKJkA9Z9Vs6FVeX07gF3JEhuHF2wBTAGQ_2J5sZE" ["firstname"]=> string(8) "Ebenezer" ["lastname"]=> string(7) "Omotere" ["businessname"]=> string(12) "Philmore Ict" ["businessid"]=> string(4) "8IVL" }
//User Token
function getUserVpayAccessToken()
{
	global $connection_server, $get_logged_user_details;
	$select_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_payment_gateways WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && gateway_name='vpay'");

	if ((mysqli_num_rows($select_gateway_details) == 1)) {
		$get_gateway_details = mysqli_fetch_array($select_gateway_details);
		if ($get_gateway_details["status"] == "1") {
			$vpay_public_key = trim($get_gateway_details["public_key"]);
			$vpay_secret_key = trim($get_gateway_details["secret_key"]);
			$vpay_merchant_userpass = array_filter(explode(":", trim($get_gateway_details["encrypt_key"])));
			$vpay_merchant_username = $vpay_merchant_userpass[0];
			$vpay_merchant_password = $vpay_merchant_userpass[1];

			if (!empty($vpay_public_key) && !empty($vpay_merchant_username) && !empty($vpay_merchant_password)) {

				$curl_url = "https://services2.vpay.africa/api/service/v1/query/merchant/login";
				$curl_request = curl_init($curl_url);
				curl_setopt($curl_request, CURLOPT_POST, true);

				$post_field_array = array("username" => $vpay_merchant_username, "password" => $vpay_merchant_password);
				curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
				curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

				$header_field_array = array("publicKey: " . $vpay_public_key, "Content-Type: application/json");
				curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
				curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

				curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
				$curl_result = curl_exec($curl_request);
				$curl_json_result = json_decode($curl_result, true);
				//var_dump($curl_json_result);
				if ($curl_json_result !== null) {
					if (($curl_json_result["status"] == true) && isset($curl_json_result["token"]) && !empty($curl_json_result["token"])) {
						if ((isset($_COOKIE["vpay_user_token"]) || !isset($_COOKIE["vpay_user_token"])) && ($_COOKIE["vpay_user_token"] !== $curl_json_result["token"])) {
							setcookie("vpay_user_token", $curl_json_result["token"], time() + (6 * 60 * 60));
							$accessToken = $curl_json_result["token"];
							$vpay_json_response_array = array("status" => "success", "message" => "Token Generated", "token" => $accessToken);
							return json_encode($vpay_json_response_array, true);
						} else {
							$accessToken = $_COOKIE["vpay_user_token"];
							$vpay_json_response_array = array("status" => "success", "message" => "Token Generated", "token" => $accessToken);
							return json_encode($vpay_json_response_array, true);
						}
					} else {
						$vpay_json_response_array = array("status" => "failed", "message" => "No Access Token");
						return json_encode($vpay_json_response_array, true);
					}
				}

				if ($curl_result === false) {
					$curl_error = curl_error($curl_request);
					$vpay_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
					return json_encode($vpay_json_response_array, true);
				}

				if ($curl_json_result === null) {
					$curl_error = curl_error($curl_request);
					$vpay_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
					return json_encode($vpay_json_response_array, true);
				}

				curl_close($curl_request);
			} else {
				$vpay_json_response_array = array("status" => "failed", "message" => "Required Keys Are Empty");
				return json_encode($vpay_json_response_array, true);
			}
		} else {
			$vpay_json_response_array = array("status" => "failed", "message" => "Vpay Is Down/Disabled");
			return json_encode($vpay_json_response_array, true);
		}
	} else {
		$vpay_json_response_array = array("status" => "failed", "message" => "Vpay Has Not Been Installed");
		return json_encode($vpay_json_response_array, true);
	}
}

//Vendor User Token
function getVendorUserVpayAccessToken()
{
	global $connection_server, $get_logged_admin_details;
	$select_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_payment_gateways WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && gateway_name='vpay'");

	if ((mysqli_num_rows($select_gateway_details) == 1)) {
		$get_gateway_details = mysqli_fetch_array($select_gateway_details);
		if ($get_gateway_details["status"] == "1") {
			$vpay_public_key = trim($get_gateway_details["public_key"]);
			$vpay_secret_key = trim($get_gateway_details["secret_key"]);
			$vpay_merchant_userpass = array_filter(explode(":", trim($get_gateway_details["encrypt_key"])));
			$vpay_merchant_username = $vpay_merchant_userpass[0];
			$vpay_merchant_password = $vpay_merchant_userpass[1];

			if (!empty($vpay_public_key) && !empty($vpay_merchant_username) && !empty($vpay_merchant_password)) {

				$curl_url = "https://services2.vpay.africa/api/service/v1/query/merchant/login";
				$curl_request = curl_init($curl_url);
				curl_setopt($curl_request, CURLOPT_POST, true);

				$post_field_array = array("username" => $vpay_merchant_username, "password" => $vpay_merchant_password);
				curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
				curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

				$header_field_array = array("publicKey: " . $vpay_public_key, "Content-Type: application/json");
				curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
				curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

				curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
				$curl_result = curl_exec($curl_request);
				$curl_json_result = json_decode($curl_result, true);
				//var_dump($curl_json_result);
				if ($curl_json_result !== null) {
					if (($curl_json_result["status"] == true) && isset($curl_json_result["token"]) && !empty($curl_json_result["token"])) {
						if ((isset($_COOKIE["vpay_vendor_user_token"]) || !isset($_COOKIE["vpay_vendor_user_token"])) && ($_COOKIE["vpay_vendor_user_token"] !== $curl_json_result["token"])) {
							setcookie("vpay_vendor_user_token", $curl_json_result["token"], time() + (6 * 60 * 60));
							$accessToken = $curl_json_result["token"];
							$vpay_json_response_array = array("status" => "success", "message" => "Token Generated", "token" => $accessToken);
							return json_encode($vpay_json_response_array, true);
						} else {
							$accessToken = $_COOKIE["vpay_vendor_user_token"];
							$vpay_json_response_array = array("status" => "success", "message" => "Token Generated", "token" => $accessToken);
							return json_encode($vpay_json_response_array, true);
						}
					} else {
						$vpay_json_response_array = array("status" => "failed", "message" => "No Access Token");
						return json_encode($vpay_json_response_array, true);
					}
				}

				if ($curl_result === false) {
					$curl_error = curl_error($curl_request);
					$vpay_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
					return json_encode($vpay_json_response_array, true);
				}

				if ($curl_json_result === null) {
					$curl_error = curl_error($curl_request);
					$vpay_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
					return json_encode($vpay_json_response_array, true);
				}

				curl_close($curl_request);
			} else {
				$vpay_json_response_array = array("status" => "failed", "message" => "Required Keys Are Empty");
				return json_encode($vpay_json_response_array, true);
			}
		} else {
			$vpay_json_response_array = array("status" => "failed", "message" => "Vpay Is Down/Disabled");
			return json_encode($vpay_json_response_array, true);
		}
	} else {
		$vpay_json_response_array = array("status" => "failed", "message" => "Vpay Has Not Been Installed");
		return json_encode($vpay_json_response_array, true);
	}
}

//Vendor Token
function getVendorVpayAccessToken()
{
	global $connection_server, $get_logged_admin_details;
	$select_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='vpay'");

	if ((mysqli_num_rows($select_gateway_details) == 1)) {
		$get_gateway_details = mysqli_fetch_array($select_gateway_details);
		if ($get_gateway_details["status"] == "1") {
			$vpay_public_key = trim($get_gateway_details["public_key"]);
			$vpay_secret_key = trim($get_gateway_details["secret_key"]);
			$vpay_merchant_userpass = array_filter(explode(":", trim($get_gateway_details["encrypt_key"])));
			$vpay_merchant_username = $vpay_merchant_userpass[0];
			$vpay_merchant_password = $vpay_merchant_userpass[1];

			if (!empty($vpay_public_key) && !empty($vpay_merchant_username) && !empty($vpay_merchant_password)) {

				$curl_url = "https://services2.vpay.africa/api/service/v1/query/merchant/login";
				$curl_request = curl_init($curl_url);
				curl_setopt($curl_request, CURLOPT_POST, true);

				$post_field_array = array("username" => $vpay_merchant_username, "password" => $vpay_merchant_password);
				curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
				curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

				$header_field_array = array("publicKey: " . $vpay_public_key, "Content-Type: application/json");
				curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
				curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

				curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
				$curl_result = curl_exec($curl_request);
				$curl_json_result = json_decode($curl_result, true);
				//var_dump($curl_json_result);
				if ($curl_json_result !== null) {
					if (($curl_json_result["status"] == true) && isset($curl_json_result["token"]) && !empty($curl_json_result["token"])) {
						if ((isset($_COOKIE["vpay_vendor_token"]) || !isset($_COOKIE["vpay_vendor_token"])) && ($_COOKIE["vpay_vendor_token"] !== $curl_json_result["token"])) {
							setcookie("vpay_vendor_token", $curl_json_result["token"], time() + (6 * 60 * 60));
							$accessToken = $curl_json_result["token"];
							$vpay_json_response_array = array("status" => "success", "message" => "Token Generated", "token" => $accessToken);
							return json_encode($vpay_json_response_array, true);
						} else {
							$accessToken = $_COOKIE["vpay_vendor_token"];
							$vpay_json_response_array = array("status" => "success", "message" => "Token Generated", "token" => $accessToken);
							return json_encode($vpay_json_response_array, true);
						}
					} else {
						$vpay_json_response_array = array("status" => "failed", "message" => "No Access Token");
						return json_encode($vpay_json_response_array, true);
					}
				}

				if ($curl_result === false) {
					$curl_error = curl_error($curl_request);
					$vpay_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
					return json_encode($vpay_json_response_array, true);
				}

				if ($curl_json_result === null) {
					$curl_error = curl_error($curl_request);
					$vpay_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
					return json_encode($vpay_json_response_array, true);
				}

				curl_close($curl_request);
			} else {
				$vpay_json_response_array = array("status" => "failed", "message" => "Required Keys Are Empty");
				return json_encode($vpay_json_response_array, true);
			}
		} else {
			$vpay_json_response_array = array("status" => "failed", "message" => "Vpay Is Down/Disabled");
			return json_encode($vpay_json_response_array, true);
		}
	} else {
		$vpay_json_response_array = array("status" => "failed", "message" => "Vpay Has Not Been Installed");
		return json_encode($vpay_json_response_array, true);
	}
}

//Super Admin Token
function getSuperAdminVpayAccessToken()
{
	global $connection_server;
	$select_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='vpay'");

	if ((mysqli_num_rows($select_gateway_details) == 1)) {
		$get_gateway_details = mysqli_fetch_array($select_gateway_details);

		$vpay_public_key = trim($get_gateway_details["public_key"]);
		$vpay_secret_key = trim($get_gateway_details["secret_key"]);
		$vpay_merchant_userpass = array_filter(explode(":", trim($get_gateway_details["encrypt_key"])));
		$vpay_merchant_username = $vpay_merchant_userpass[0];
		$vpay_merchant_password = $vpay_merchant_userpass[1];

		if (!empty($vpay_public_key) && !empty($vpay_merchant_username) && !empty($vpay_merchant_password)) {

			$curl_url = "https://services2.vpay.africa/api/service/v1/query/merchant/login";
			$curl_request = curl_init($curl_url);
			curl_setopt($curl_request, CURLOPT_POST, true);

			$post_field_array = array("username" => $vpay_merchant_username, "password" => $vpay_merchant_password);
			curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
			curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

			$header_field_array = array("publicKey: " . $vpay_public_key, "Content-Type: application/json");
			curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
			curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

			curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
			$curl_result = curl_exec($curl_request);
			$curl_json_result = json_decode($curl_result, true);
			//var_dump($curl_json_result);
			if ($curl_json_result !== null) {
				if (($curl_json_result["status"] == true) && isset($curl_json_result["token"]) && !empty($curl_json_result["token"])) {
					if ((isset($_COOKIE["vpay_super_admin_token"]) || !isset($_COOKIE["vpay_super_admin_token"])) && ($_COOKIE["vpay_super_admin_token"] !== $curl_json_result["token"])) {
						setcookie("vpay_super_admin_token", $curl_json_result["token"], time() + (6 * 60 * 60));
						$accessToken = $curl_json_result["token"];
						$vpay_json_response_array = array("status" => "success", "message" => "Token Generated", "token" => $accessToken);
						return json_encode($vpay_json_response_array, true);
					} else {
						$accessToken = $_COOKIE["vpay_super_admin_token"];
						$vpay_json_response_array = array("status" => "success", "message" => "Token Generated", "token" => $accessToken);
						return json_encode($vpay_json_response_array, true);
					}
				} else {
					$vpay_json_response_array = array("status" => "failed", "message" => "No Access Token");
					return json_encode($vpay_json_response_array, true);
				}
			}

			if ($curl_result === false) {
				$curl_error = curl_error($curl_request);
				$vpay_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
				return json_encode($vpay_json_response_array, true);
			}

			if ($curl_json_result === null) {
				$curl_error = curl_error($curl_request);
				$vpay_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
				return json_encode($vpay_json_response_array, true);
			}

			curl_close($curl_request);
		} else {
			$vpay_json_response_array = array("status" => "failed", "message" => "Required Keys Are Empty");
			return json_encode($vpay_json_response_array, true);
		}
	} else {
		$vpay_json_response_array = array("status" => "failed", "message" => "Vpay Has Not Been Installed");
		return json_encode($vpay_json_response_array, true);
	}
}

function makeVpayRequest($req_method, $generatedAccessToken, $parameter_url, $req_body)
{
	global $connection_server;
	$key_explode = array_filter(explode(":", trim(base64_decode($generatedAccessToken))));
	$curl_url = "https://api.vpay.com/" . $parameter_url;
	$curl_request = curl_init($curl_url);
	if ($req_method == "post") {
		curl_setopt($curl_request, CURLOPT_POST, true);
	} else {
		if ($req_method == "get") {
			curl_setopt($curl_request, CURLOPT_HTTPGET, true);
		}
	}

	if (is_array($req_body)) {
		$post_field_array = $req_body;
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($post_field_array, true));
	}
	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);

	$header_field_array = array("api-key: " . $key_explode[0], "api-secret: Bearer " . $key_explode[1], "Content-Type: application/json");
	curl_setopt($curl_request, CURLOPT_HTTPHEADER, $header_field_array);
	curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);

	curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
	$curl_result = curl_exec($curl_request);
	$curl_json_result = json_decode($curl_result, true);

	if ($curl_json_result !== null) {
		if (($curl_json_result["status"] === true)) {
			$encoded_json_result = json_encode($curl_json_result, true);
			$vpay_json_response_array = array("status" => "success", "message" => "Request Successful", "json_result" => $encoded_json_result);
			return json_encode($vpay_json_response_array, true);
		} else {
			$vpay_json_response_array = array("status" => "failed", "message" => implode(":", $key_explode) . "Request Failed");
			return json_encode($vpay_json_response_array, true);
		}
	}

	if ($curl_result === false) {
		$curl_error = curl_error($curl_request);
		$vpay_json_response_array = array("status" => "failed", "message" => "Error: " . $curl_error);
		return json_encode($vpay_json_response_array, true);
	}

	if ($curl_json_result === null) {
		$curl_error = curl_error($curl_request);
		$vpay_json_response_array = array("status" => "failed", "message" => "Null Error: " . $curl_error);
		return json_encode($vpay_json_response_array, true);
	}

	curl_close($curl_request);


}

function identifyISP($phoneNumber)
{
	// Define the carrier prefixes
	$carrierMTN = ["803", "702", "703", "704", "903", "806", "706", "813", "810", "814", "816", "906", "916", "913", "903"];
	$carrierAirtel = ["701", "708", "802", "808", "812", "901", "902", "904", "907", "911", "912"];
	$carrierGlo = ["805", "705", "905", "807", "815", "811", "915"];
	$carrier9mobile = ["809", "817", "818", "908", "909"];

	// Extract the 2nd to 4th digits from the phone number
	$prefix = substr($phoneNumber, 1, 3);

	// Identify the ISP
	if (in_array($prefix, $carrierMTN)) {
		return "mtn";
	} elseif (in_array($prefix, $carrierAirtel)) {
		return "airtel";
	} elseif (in_array($prefix, $carrierGlo)) {
		return "glo";
	} elseif (in_array($prefix, $carrier9mobile)) {
		return "9mobile";
	} else {
		return "Unknown";
	}
}

function chargeUserCryptoWallet($type, $currency, $type_alternative, $reference, $api_reference, $amount, $discounted_amount, $description, $mode, $api_website, $status)
{
	global $connection_server;

	$type = mysqli_real_escape_string($connection_server, trim(strip_tags($type)));
	$currency = mysqli_real_escape_string($connection_server, trim(strip_tags($currency)));
	$type_alternative = mysqli_real_escape_string($connection_server, trim(strip_tags($type_alternative)));
	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($reference)));
	$api_reference = mysqli_real_escape_string($connection_server, trim(strip_tags($api_reference)));
	$amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($amount))));
	$discounted_amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($discounted_amount))));
	$description = mysqli_real_escape_string($connection_server, trim(strip_tags($description)));
	$mode = mysqli_real_escape_string($connection_server, trim(strip_tags($mode)));
	$api_website = mysqli_real_escape_string($connection_server, trim(strip_tags($api_website)));
	$status = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($status))));

	$transactionTypeArray = array("credit", "debit");
	$statusArray = array(1, 2, 3);

	$get_vendor_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$get_logged_user_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_user_crypto_ledger_balance WHERE vendor_id='" . $get_vendor_det["id"] . "' && username='" . $_SESSION["user_session"] . "' && currency='".$currency."' LIMIT 1"));
	$get_logged_user_main_det = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_vendor_det["id"] . "' && username='" . $_SESSION["user_session"] . "' LIMIT 1"));


	if (!empty($get_logged_user_det["wallet_balance"]) && is_numeric($get_logged_user_det["wallet_balance"]) && !empty($amount) && is_numeric($amount) && !empty($discounted_amount) && is_numeric($discounted_amount) && ($discounted_amount > 0)) {
		if (in_array($type, $transactionTypeArray) && !empty($type_alternative) && !empty($reference) && !empty($description) && is_numeric($status) && in_array($status, $statusArray)) {
			if ($type === "debit") {
				if (($get_logged_user_det["wallet_balance"] > 0) && ($amount > 0) && ($get_logged_user_det["wallet_balance"] >= $amount) && ($get_logged_user_det["wallet_balance"] >= $discounted_amount)) {
					$user_balance_before_debit = $get_logged_user_det["wallet_balance"];
					$user_balance_after_debit = ($user_balance_before_debit - $discounted_amount);

					$insert_transaction = mysqli_query($connection_server, "INSERT INTO sas_crypto_transactions (vendor_id, currency, type_alternative, reference, api_reference, username, amount, discounted_amount, balance_before, balance_after, description, mode, api_website, status) VALUES ('" . $get_logged_user_det["vendor_id"] . "', '$currency', '$type_alternative', '$reference', '$api_reference', '" . $get_logged_user_det["username"] . "', '$amount', '$discounted_amount', '$user_balance_before_debit', '$user_balance_after_debit', '$description', '$mode', '$api_website', '$status')");
					$charge_user = mysqli_query($connection_server, "UPDATE sas_user_crypto_ledger_balance SET wallet_balance='$user_balance_after_debit' WHERE vendor_id='" . $get_logged_user_det["vendor_id"] . "' && username='" . $get_logged_user_det["username"] . "' && currency='".$currency."'");
					if (($user_balance_before_debit == true) && ($charge_user == true)) {
						// Email Beginning
						$funding_template_encoded_text_array = array("{firstname}" => $get_logged_user_main_det["firstname"], "{lastname}" => $get_logged_user_main_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_debit, 2), "{balance_after}" => toDecimal($user_balance_after_debit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
						$raw_funding_template_subject = getUserEmailTemplate('user-crypto-funding', 'subject');
						$raw_funding_template_body = getUserEmailTemplate('user-crypto-funding', 'body');
						foreach ($funding_template_encoded_text_array as $array_key => $array_val) {
							$raw_funding_template_subject = str_replace($array_key, $array_val, $raw_funding_template_subject);
							$raw_funding_template_body = str_replace($array_key, $array_val, $raw_funding_template_body);
						}

						$transaction_template_encoded_text_array = array("{admin_firstname}" => $get_vendor_det["firstname"], "{admin_lastname}" => $get_vendor_det["lastname"], "{username}" => $get_logged_user_det["username"], "{firstname}" => $get_logged_user_main_det["firstname"], "{lastname}" => $get_logged_user_main_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_debit, 2), "{balance_after}" => toDecimal($user_balance_after_debit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
						$raw_transaction_template_subject = getUserEmailTemplate('user-crypto-transactions', 'subject');
						$raw_transaction_template_body = getUserEmailTemplate('user-crypto-transactions', 'body');
						foreach ($transaction_template_encoded_text_array as $array_key => $array_val) {
							$raw_transaction_template_subject = str_replace($array_key, $array_val, $raw_transaction_template_subject);
							$raw_transaction_template_body = str_replace($array_key, $array_val, $raw_transaction_template_body);
						}
						sendVendorEmail($get_logged_user_main_det["email"], $raw_funding_template_subject, $raw_funding_template_body);
						sendVendorEmail($get_logged_user_main_det["email"], $raw_transaction_template_subject, $raw_transaction_template_body);
						// Email End
						return "success";
					} else {
						return "failed";
					}
				} else {
					return "failed";
				}
			}

			if ($type === "credit") {
				$user_balance_before_credit = $get_logged_user_det["wallet_balance"];
				$user_balance_after_credit = ($user_balance_before_credit + $discounted_amount);

				$insert_transaction = mysqli_query($connection_server, "INSERT INTO sas_crypto_transactions (vendor_id, currency, type_alternative, reference, api_reference, username, amount, discounted_amount, balance_before, balance_after, description, mode, api_website, status) VALUES ('" . $get_logged_user_det["vendor_id"] . "', '$currency', '$type_alternative', '$reference', '$api_reference', '" . $get_logged_user_det["username"] . "', '$amount', '$discounted_amount', '$user_balance_before_credit', '$user_balance_after_credit', '$description', '$mode', '$api_website', '$status')");
				$charge_user = mysqli_query($connection_server, "UPDATE sas_user_crypto_ledger_balance SET wallet_balance='$user_balance_after_credit' WHERE vendor_id='" . $get_logged_user_det["vendor_id"] . "' && username='" . $get_logged_user_det["username"] . "' && currency='".$currency."'");
				if (($user_balance_before_credit == true) && ($charge_user == true)) {
					// Email Beginning
					$funding_template_encoded_text_array = array("{firstname}" => $get_logged_user_main_det["firstname"], "{lastname}" => $get_logged_user_main_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_credit, 2), "{balance_after}" => toDecimal($user_balance_after_credit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
					$raw_funding_template_subject = getUserEmailTemplate('user-crypto-funding', 'subject');
					$raw_funding_template_body = getUserEmailTemplate('user-crypto-funding', 'body');
					foreach ($funding_template_encoded_text_array as $array_key => $array_val) {
						$raw_funding_template_subject = str_replace($array_key, $array_val, $raw_funding_template_subject);
						$raw_funding_template_body = str_replace($array_key, $array_val, $raw_funding_template_body);
					}

					$transaction_template_encoded_text_array = array("{admin_firstname}" => $get_vendor_det["firstname"], "{admin_lastname}" => $get_vendor_det["lastname"], "{username}" => $get_logged_user_det["username"], "{firstname}" => $get_logged_user_main_det["firstname"], "{lastname}" => $get_logged_user_main_det["lastname"], "{balance_before}" => toDecimal($user_balance_before_credit, 2), "{balance_after}" => toDecimal($user_balance_after_credit, 2), "{amount}" => toDecimal($amount, 2) . " @ " . toDecimal($discounted_amount, 2), "{type}" => $type, "{description}" => $description);
					$raw_transaction_template_subject = getUserEmailTemplate('user-crypto-transactions', 'subject');
					$raw_transaction_template_body = getUserEmailTemplate('user-crypto-transactions', 'body');
					foreach ($transaction_template_encoded_text_array as $array_key => $array_val) {
						$raw_transaction_template_subject = str_replace($array_key, $array_val, $raw_transaction_template_subject);
						$raw_transaction_template_body = str_replace($array_key, $array_val, $raw_transaction_template_body);
					}
					sendVendorEmail($get_logged_user_det["email"], $raw_funding_template_subject, $raw_funding_template_body);
					sendVendorEmail($get_vendor_det["email"], $raw_transaction_template_subject, $raw_transaction_template_body);
					// Email End
					return "success";
				} else {
					return "failed";
				}
			}

			if (!in_array($type, $transactionTypeArray)) {
				return "failed";
			}
		} else {
			return "failed";
		}
	} else {
		return "failed";
	}
}
?>