<?php
// Implementation of International Airtime processing using VTPASS
$phone_no = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["phone-number"])));
$amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_POST["amount"]))));
$country_code = mysqli_real_escape_string($connection_server, $_POST["country_code"]);
$product_type_id = mysqli_real_escape_string($connection_server, $_POST["product_type_id"]);
$operator_id = mysqli_real_escape_string($connection_server, $_POST["operator_id"]);
$variation_code = mysqli_real_escape_string($connection_server, $_POST["variation_code"]);

// Fetch discount and variation info
$select_op = mysqli_query($connection_server, "SELECT * FROM sas_intl_airtime_operators WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' AND country_code='$country_code' AND product_type_id='$product_type_id' AND operator_id='$operator_id' AND status='1' LIMIT 1");

if (mysqli_num_rows($select_op) == 1) {
    $op_details = mysqli_fetch_array($select_op);

    // Determine discount based on account level
    $discount_percent = 1;
    if ($get_logged_user_details["account_level"] == 1) $discount_percent = $op_details["smart_discount"];
    else if ($get_logged_user_details["account_level"] == 2) $discount_percent = $op_details["agent_discount"];
    else if ($get_logged_user_details["account_level"] == 3) $discount_percent = $op_details["api_discount"];

    $discounted_amount = $amount - ($amount * ($discount_percent / 100));
    $type_alternative = "International Airtime (".$op_details['country_name'].")";
    $reference = "INTL-AIR-" . substr(str_shuffle("1234567890"), 0, 10);
    $description = "International Airtime (".$op_details['operator_name'].") to $phone_no";
    $status = 3;

    if ($get_logged_user_details["balance"] >= $discounted_amount) {
        // Find VTPASS API via status table
        $get_status = mysqli_query($connection_server, "SELECT * FROM sas_intl_airtime_status WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' AND product_name='intl-airtime' AND status='1' LIMIT 1");

        if (mysqli_num_rows($get_status) == 1) {
            $status_row = mysqli_fetch_array($get_status);
            $get_api_details = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE id='" . $status_row["api_id"] . "' && status='1' LIMIT 1");

            if (mysqli_num_rows($get_api_details) == 1) {
                $api_detail = mysqli_fetch_array($get_api_details);

                $check_limit = productIDPurchaseChecker($phone_no, "intl-airtime");
                if ($check_limit == "success") {
                    $debit_user = chargeUser("debit", $phone_no, $type_alternative, $reference, "", $amount, $discounted_amount, $description, "WEB", $_SERVER["HTTP_HOST"], $status);
            if ($debit_user === "success") {
                include($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/intl-airtime-vtpass-com.php");

                if ($api_response == "successful") {
                    alterTransaction($reference, "status", "1");
                    alterTransaction($reference, "api_id", $api_detail["id"]);
                    alterTransaction($reference, "api_reference", $api_response_reference);
                    $json_response_array = array("status" => "success", "desc" => "International Airtime Successful");
                } else if ($api_response == "pending") {
                    alterTransaction($reference, "status", "2");
                    alterTransaction($reference, "api_id", $api_detail["id"]);
                    alterTransaction($reference, "api_reference", $api_response_reference);
                    $json_response_array = array("status" => "pending", "desc" => "International Airtime Pending");
                } else {
                    $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                    chargeUser("credit", $phone_no, "Refund", $reference_2, "", $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", "WEB", $_SERVER["HTTP_HOST"], "1");
                    $json_response_array = array("status" => "failed", "desc" => $api_response_description);
                }
                if ($api_response == "successful") {
                    updateProductPurchaseList($reference, $phone_no, "intl-airtime");
                }
                if ($api_response == "pending") {
                    updateProductPurchaseList($reference, $phone_no, "intl-airtime");
                }

            } else {
                $json_response_array = array("status" => "failed", "desc" => "Unable to proceed with charges");
            }
                } else {
                    if($check_limit == "limit_exceeded"){
                        $json_response_array = array("status" => "failed", "desc" => "[SECURITY_ALERT] Daily Limit Exceeded For This Phone Number: " . $phone_no . ", Contact Admin for Support");
                    }else{
                        $json_response_array = array("status" => "failed", "desc" => "Error: Security check failed. Try again later.");
                    }
                }
                } else {
                    $json_response_array = array("status" => "failed", "desc" => "International Airtime API not configured");
                }
        } else {
                $json_response_array = array("status" => "failed", "desc" => "International Airtime API route not found");
        }
    } else {
            $json_response_array = array("status" => "failed", "desc" => "Insufficient Fund");
    }
} else {
    $json_response_array = array("status" => "failed", "desc" => "Invalid Country or Operator selected");
}

$json_response_encode = json_encode($json_response_array, true);
?>
