<?php error_reporting(0);
include_once("app-config.php");

header("Content-Type: application/json");
$incoming_post_request = file_get_contents("php://input");
fwrite(fopen("update_user.txt", "a"), $incoming_post_request . "\n\n");
$decode_post_request = json_decode($incoming_post_request, true);
if (json_last_error() === JSON_ERROR_NONE) {

    //Select Vendor Table
    $select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
    if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {

        $username = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["username"])));
        $password = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["encoded-passkey"])));
        $fullname_exp = array_filter(explode(" ", trim($decode_post_request['fullname'])));
        $firstname = mysqli_real_escape_string($connection_server, trim(strip_tags($fullname_exp[0])));
        $lastname = mysqli_real_escape_string($connection_server, trim(strip_tags($fullname_exp[1] ?? "")));
        $othername = mysqli_real_escape_string($connection_server, trim(strip_tags($fullname_exp[2] ?? "")));
        $phone = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["phone"])));
        $address = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["address"])));

        $status_update = "failed";
        $status_msg = "Unknown Error";

        if (
            !empty($username) &&
            !empty($password) &&
            !empty($firstname) &&
            !empty($lastname) &&
            !empty($phone) &&
            is_numeric($phone) &&
            (strlen($phone) == 11) &&
            !empty($address)
        ) {
            $checkuser = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND (username = '" . $username . "' OR email='" . $username . "')");
            if (mysqli_num_rows($checkuser) == 1) {
                $checkuser_pass = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND (username = '" . $username . "' OR email='" . $username . "') AND password = '" . $password . "'");
                if (mysqli_num_rows($checkuser_pass) == 1) {
                    $get_user_detail = mysqli_fetch_array($checkuser_pass);
                    $fullname_exp = array_filter(explode(" ", trim($fullname)));

                    $check_existing_user = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND phone_number = '$phone' AND username != '" . $username . "'");
                    if (mysqli_num_rows($check_existing_user) == 0) {
                        $update_user = mysqli_query($connection_server, "UPDATE sas_users SET firstname = '" . $firstname . "', lastname = '" . $lastname . "', othername = '" . $othername . "', phone_number = '$phone', `home_address` = '$address' WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND username = '" . $username . "'");
                        if ($update_user == true) {
                            // Email Beginning
                            $account_update_template_encoded_text_array = array("{firstname}" => $fullname_exp[0], "{lastname}" => $lastname, "{email}" => $get_user_detail["email"], "{phone}" => $phone, "{address}" => $address);
                            $raw_account_update_template_subject = getUserEmailTemplate('user-account-update', 'subject');
                            $raw_account_update_template_body = getUserEmailTemplate('user-account-update', 'body');
                            foreach ($account_update_template_encoded_text_array as $array_key => $array_val) {
                                $raw_account_update_template_subject = str_replace($array_key, $array_val, $raw_account_update_template_subject);
                                $raw_account_update_template_body = str_replace($array_key, $array_val, $raw_account_update_template_body);
                            }

                            beeMailer($get_user_detail["email"], $raw_account_update_template_subject, $raw_account_update_template_body);
                            // Email End
                            $status_update = "success";
                            $status_msg = "Information updated successfully";
                        } else {
                            $status_msg = "Error: Server Down, Details failed to update";
                        }
                    } else {
                        $status_msg = "Error: Phone is taken by another user";
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
            } elseif (empty($firstname)) {
                $status_msg = "Empty Firstname";
            } elseif (empty($lastname)) {
                $status_msg = "Empty Lastname";
            } elseif (empty($phone)) {
                $status_msg = "Empty Phone number";
            } elseif (!is_numeric($phone)) {
                $status_msg = "Phone number must be a number";
            } elseif (strlen($phone) < 11 || strlen($phone) > 11) {
                $status_msg = "Phone number must be 11 digit";
            } elseif (empty($address)) {
                $status_msg = "Empty Address";
            }
        }
        // fwrite(fopen("update_user.txt", "a"), $status_msg . "\n\n");

        $app_json = json_encode(array("json-status" => "success", "status" => $status_update, "status-msg" => $status_msg), true);
        // fwrite(fopen("update_user.txt", "a"), $app_json . "\n\n");
    } else {
        //Website not registered
        $app_json = json_encode(array("json-status" => "failed", "status" => "failed", "status-msg" => "Website not registered"), true);
    }
} else {
    $app_json = json_encode(array("json-status" => "success", "status" => "failed", "status-msg" => "Bad Request"), true);
}
fwrite(fopen("update_user.json", "a"), $app_json . "\n\n");


echo $app_json;
?>