<?php error_reporting(0);
include_once("app-config.php");

header("Content-Type: application/json");
$incoming_post_request = file_get_contents("php://input");
fwrite(fopen("upgrade_user.txt", "a"), $incoming_post_request . "\n\n");
$decode_post_request = json_decode($incoming_post_request, true);
if (json_last_error() === JSON_ERROR_NONE) {

    //Select Vendor Table
    $select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
    if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {

        $username = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["username"])));
        $password = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["encoded-passkey"])));
        $level = mysqli_real_escape_string($connection_server, trim(strtolower(strip_tags($decode_post_request["level"]))));

        $status_update = "failed";
        $status_msg = "Unknown Error";

        $account_level_upgrade_array = array("smart" => 1, "agent" => 2, "api" => 3);
        $purchase_method = "app";
        $purchase_method = strtoupper($purchase_method);
        $purchase_method_array = array("APP");


        if (
            !empty($username) &&
            !empty($password) &&
            !empty($level) &&
            in_array($level, array_keys($account_level_upgrade_array))
        ) {
            $checkuser = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND (username = '" . $username . "' OR email='" . $username . "')");
            if (mysqli_num_rows($checkuser) == 1) {
                $checkuser_pass = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE  vendor_id = '" . $select_vendor_table["id"] . "' AND (username = '" . $username . "' OR email='" . $username . "') AND password = '" . $password . "'");
                if (mysqli_num_rows($checkuser_pass) == 1) {
                    $get_user_detail = mysqli_fetch_array($checkuser_pass);
                    if ($get_user_detail["status"] == "1") {
                        if ($account_level_upgrade_array[$level] > $get_user_detail["account_level"]) {
                            $get_upgrade_price = mysqli_query($connection_server, "SELECT * FROM sas_user_upgrade_price WHERE vendor_id='" . $select_vendor_table["id"] . "' && account_type='" . $account_level_upgrade_array[$level] . "'");
                            if (mysqli_num_rows($get_upgrade_price) == 1) {
                                $upgrade_price = mysqli_fetch_array($get_upgrade_price);
                                if (!empty($upgrade_price["price"]) && is_numeric($upgrade_price["price"]) && ($upgrade_price["price"] > 0)) {

                                    $user_logged_query = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE  vendor_id = '" . $select_vendor_table["id"] . "' AND (username = '" . $username . "' OR email='" . $username . "') AND password = '" . $password . "'");
                                    $get_logged_user_details = mysqli_fetch_array($user_logged_query);

                                    if (!empty(userBalance(1)) && is_numeric(userBalance(1)) && (userBalance(1) > 0)) {
                                        $amount = $upgrade_price["price"];
                                        $discounted_amount = $amount;
                                        $type_alternative = ucwords("Account Upgrade");
                                        $reference = substr(str_shuffle("12345678901234567890"), 0, 15);
                                        $description = ucwords(accountLevel($account_level_upgrade_array[$level]) . " Upgrade charges");
                                        $status = 1;

                                        $debit_user = chargeOtherUser($get_logged_user_details["username"], "debit", accountLevel($account_level_upgrade_array[$level]), $type_alternative, $reference, "", $amount, $discounted_amount, $description, $purchase_method, $_SERVER["HTTP_HOST"], $status);
                                        if ($debit_user === "success") {
                                            $user_logged_name = $get_logged_user_details["username"];
                                            $account_upgrade_id = $account_level_upgrade_array[$level];
                                            $alter_user_details = alterUser($user_logged_name, "account_level", $account_upgrade_id);
                                            if ($alter_user_details == "success") {

                                                // Email Beginning
                                                $upgrade_user_template_encoded_text_array = array("{firstname}" => $get_user_detail["firstname"], "{lastname}" => $get_user_detail["lastname"], "{account_level}" => $level . " level");
                                                $raw_upgrade_user_template_subject = getUserEmailTemplate('user-upgrade', 'subject');
                                                $raw_upgrade_user_template_body = getUserEmailTemplate('user-upgrade', 'body');
                                                foreach ($upgrade_user_template_encoded_text_array as $array_key => $array_val) {
                                                    $raw_upgrade_user_template_subject = str_replace($array_key, $array_val, $raw_upgrade_user_template_subject);
                                                    $raw_upgrade_user_template_body = str_replace($array_key, $array_val, $raw_upgrade_user_template_body);
                                                }

                                                beeMailer($get_user_detail["email"], $raw_upgrade_user_template_subject, $raw_upgrade_user_template_body);
                                                // Email End

                                                $status_update = "success";
                                                $status_msg = "Account Upgraded to " . ucwords($level) . " Successful";

                                            } else {
                                                $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                                chargeOtherUser($get_logged_user_details["username"], "credit", accountLevel($account_level_upgrade_array[$level]), "Refund", $reference_2, "", $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $purchase_method, $_SERVER["HTTP_HOST"], "1");
                                                $status_msg = "Upgrade Failed, Contact Admin";
                                            }
                                        } else {
                                            $status_msg = "Insufficient Fund" . $upgrade_price["price"];
                                        }

                                    } else {
                                        $status_msg = "Balance is LOW";
                                    }
                                } else {
                                    $status_msg = "Pricing Error, Contact Admin";
                                }
                            } else {
                                $status_msg = "Error: Pricing Not Available, Contact Admin";
                            }
                        } else {
                            $status_msg = "Error: Account Cannot Be Downgraded, Contact Admin";
                        }
                    } else {
                        $status_msg = "Account Locked, Contact the admin";
                    }
                } else {
                    $status_msg = "Invalid Password";
                }
            } else {
                $status_msg = "Invalid Username or email";
            }
        } else {
            if (empty($username)) {
                $status_msg = "Empty Username";
            } elseif (empty($password)) {
                $status_msg = "Empty Password";
            } elseif (empty($level)) {
                $status_msg = "Empty Level";
            } elseif (!in_array($level, array_keys($account_level_upgrade_array))) {
                $status_msg = "Invalid Account Level";
            }
        }
        // fwrite(fopen("upgrade_user.txt", "a"), $status_msg . "\n\n");

        $app_json = json_encode(array("json-status" => "success", "status" => $status_update, "status-msg" => $status_msg), true);
        // fwrite(fopen("upgrade_user.txt", "a"), $app_json . "\n\n");
    } else {
        //Website not registered
        $app_json = json_encode(array("json-status" => "failed", "status" => "failed", "status-msg" => "Website not registered"), true);
    }
} else {
    $app_json = json_encode(array("json-status" => "success", "status" => "failed", "status-msg" => "Bad Request"), true);
}
fwrite(fopen("upgrade_user.json", "a"), $app_json . "\n\n");


echo $app_json;
?>