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
        $firstname = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["firstname"])));
        $lastname = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["lastname"])));
        $email = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["email"])));
        $phone = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["phone"])));
        $address = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["address"])));
        $zipcode = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["zipcode"])));
        $state = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["state"])));
        $country = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["country"])));
        $kyc_mode = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["kyc_mode"])));
        $kyc_id = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["kyc_id"])));
        $id_selfie_url = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["id_selfie_url"])));

        $status_update = "failed";
        $status_msg = "Unknown Error";


        $checkuser = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND  (username = '" . $username . "' OR email='" . $username . "')");
        if (mysqli_num_rows($checkuser) == 1) {
            $checkuser_pass = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND (username = '" . $username . "' OR email='" . $username . "') AND password = '" . $password . "'");
            if (mysqli_num_rows($checkuser_pass) == 1) {
                $get_user_info = mysqli_fetch_array($checkuser_pass);
                $api_file_url = $_SERVER["DOCUMENT_ROOT"] . "/web/api/create-card-holder.php";
                if (file_exists($api_file_url)) {
                    $api_post_info_from_app = array("api_key" => $get_user_info["api_key"], "firstname" => $firstname,  "lastname" => $lastname,  "email" => $email,  "phone" => $phone,  "address" => $address,  "postal_code" => $zipcode,  "state" => $state,  "country" => $country,  "kyc_mode" => $kyc_mode,  "kyc_id" => $kyc_id,  "id_selfie_url" => $id_selfie_url);
                    include_once($api_file_url);
                    if (in_array($json_response_array["status"], array("success", "pending"))) {
                        $status_update = "success";
                        $status_msg = $json_response_array["desc"];
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