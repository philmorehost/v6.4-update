<?php session_start();
include_once("../../func/bc-connect.php");

//Select Vendor Table
$select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {
	$api_json_response_encode = "";
	if (isset($api_post_info_from_app) && is_array($api_post_info_from_app)) {
		$purchase_method = "app";
		$get_api_post_info = $api_post_info_from_app;
	} else {
		$purchase_method = "api";
		$get_api_post_info = json_decode(file_get_contents('php://input'), true);
	}

	$get_vendor_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
	$api_key_sanitized = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["api_key"] ?? '')));
	$get_user_detail_via_apikey = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_vendor_details["id"] . "' && api_key='" . $api_key_sanitized . "' LIMIT 1");
	$get_logged_user_details = mysqli_fetch_array($get_user_detail_via_apikey);
	if (mysqli_num_rows($get_user_detail_via_apikey) == 1) {
		if ($get_logged_user_details["api_status"] == 1) {
			$_SESSION["user_session"] = $get_logged_user_details["username"];

            // Map API inputs to expected POST globals for the processing script
            $_POST["phone-number"] = $get_api_post_info["phone_number"] ?? '';
            $_POST["amount"] = $get_api_post_info["amount"] ?? '';
            $_POST["country_code"] = $get_api_post_info["country_code"] ?? '';
            $_POST["product_type_id"] = $get_api_post_info["product_type_id"] ?? '';
            $_POST["operator_id"] = $get_api_post_info["operator_id"] ?? '';
            $_POST["variation_code"] = $get_api_post_info["variation_code"] ?? '';

			include_once($_SERVER["DOCUMENT_ROOT"] . "/func/bc-func.php");
			include_once($_SERVER["DOCUMENT_ROOT"] . "/web/func/intl-airtime.php");

			$api_json_response_encode = $json_response_encode;
			alterUser($get_logged_user_details["username"], "last_login", date('Y-m-d H:i:s.u'));
			unset($_SESSION["user_session"]);
		} else {
			//API approval needed, Contact Admin
			$json_response_array = array("status" => "failed", "desc" => "API approval needed, Contact Admin");
			$api_json_response_encode = json_encode($json_response_array, true);
		}
	} else {
		//User not exists
		$json_response_array = array("status" => "failed", "desc" => "User not exists");
		$api_json_response_encode = json_encode($json_response_array, true);
	}
} else {
	//Website not registered
	$json_response_array = array("status" => "failed", "desc" => "Website not registered");
	$api_json_response_encode = json_encode($json_response_array, true);
}

if (!isset($api_post_info_from_app) || !is_array($api_post_info_from_app)) {
	echo $api_json_response_encode;
}

mysqli_close($connection_server);
?>
