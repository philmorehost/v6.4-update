<?php error_reporting(0);
include_once("app-config.php");

header("Content-Type: application/json");
$incoming_post_request = file_get_contents("php://input");
fwrite(fopen("create-card.txt", "a"), $incoming_post_request . "\n\n");
$decode_post_request = json_decode($incoming_post_request, true);
if (json_last_error() === JSON_ERROR_NONE) {

    //Select Vendor Table
    $select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
    if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {
        $username = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["username"])));
        $password = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["encoded-passkey"])));
        $network = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["network"])));
        $card_type = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["type"])));

        $status_update = "failed";
        $status_msg = "Unknown Error";


        $checkuser = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND  (username = '" . $username . "' OR email='" . $username . "')");
        if (mysqli_num_rows($checkuser) == 1) {
            $checkuser_pass = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND (username = '" . $username . "' OR email='" . $username . "') AND password = '" . $password . "'");
            if (mysqli_num_rows($checkuser_pass) == 1) {
                $get_user_info = mysqli_fetch_array($checkuser_pass);

                $select_card_holder = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_holders WHERE vendor_id = '" . $select_vendor_table["id"] . "' && username = '" . $get_user_info["username"] . "' LIMIT 1");
                if (mysqli_num_rows($select_card_holder) == 1) {
                    $get_card_holder_detail = mysqli_fetch_array($select_card_holder);

                    $api_file_url = $_SERVER["DOCUMENT_ROOT"] . "/web/api/create-card.php";
                    if (file_exists($api_file_url)) {
                        $api_post_info_from_app = array("api_key" => $get_user_info["api_key"], "provider" => $network, "qty_number" => 1, "type" => $card_type, "quantity" => 1, "card_holder_ref" => $get_card_holder_detail["holder_id"]);
                        include_once($api_file_url);
                        if (in_array($json_response_array["status"], array("success", "pending"))) {
                            $status_update = "success";
                            $status_msg = $json_response_array["desc"];
                        } else {
                            $status_msg = "Card Creation Failed";
                        }
                    } else {
                        $status_msg = "Server Unavailable";
                    }
                } else {
                    $status_msg = "Error: Login to " . $web_http_host . " and create a Card Holder before generating an account";
                }
            } else {
                $status_msg = "Invalid Password";
            }
        } else {
            $status_msg = "Invalid Username or email";
        }

        $app_json = json_encode(array("json-status" => "success", "status" => $status_update, "status-msg" => $status_msg, "cards" => $cards), true);
    } else {
        //Website not registered
        $app_json = json_encode(array("json-status" => "failed", "status" => "failed", "status-msg" => "Website not registered"), true);
    }
} else {
    $app_json = json_encode(array("json-status" => "success", "status" => "failed", "status-msg" => "Bad Request"), true);
}
fwrite(fopen("create-card.json", "a"), $app_json . "\n\n" . json_encode($json_response_encode) . "\n\n");

echo $app_json;
?>