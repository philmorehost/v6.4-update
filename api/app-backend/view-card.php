<?php error_reporting(0);
include_once("app-config.php");

header("Content-Type: application/json");
$incoming_post_request = file_get_contents("php://input");
fwrite(fopen("view-card.txt", "a"), $incoming_post_request . "\n\n");
$decode_post_request = json_decode($incoming_post_request, true);
if (json_last_error() === JSON_ERROR_NONE) {

    //Select Vendor Table
    $select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
    if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {
        $username = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["username"])));
        $password = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["encoded-passkey"])));
        $card_ref = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["trans_ref"])));

        $status_update = "failed";
        $status_msg = "Unknown Error";
        $cards = array();


        $checkuser = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND  (username = '" . $username . "' OR email='" . $username . "')");
        if (mysqli_num_rows($checkuser) == 1) {
            $checkuser_pass = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND (username = '" . $username . "' OR email='" . $username . "') AND password = '" . $password . "'");
            if (mysqli_num_rows($checkuser_pass) == 1) {
                $get_user_info = mysqli_fetch_array($checkuser_pass);
                $api_file_url = $_SERVER["DOCUMENT_ROOT"] . "/web/api/view-card.php";
                if (file_exists($api_file_url)) {
                    $api_post_info_from_app = array("api_key" => $get_user_info["api_key"], "card_ref" => $card_ref);
                    include_once($api_file_url);
                    if (in_array($json_response_array["status"], array("success", "pending"))) {
                        $status_update = "success";
                        $status_msg = $json_response_array["desc"];
                        $cards = $json_response_array["cards"];
                    } else {
                        $status_msg = $json_response_array["desc"];
                    }
                } else {
                    $status_msg = "Server Unavailable";
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
fwrite(fopen("view-card.json", "a"), $app_json . "\n\n");

echo $app_json;
?>