<?php session_start();
include_once("../../func/bc-config.php");

if (isset($_GET["operator_id"]) && isset($_GET["product_type_id"])) {
    $operator_id = mysqli_real_escape_string($connection_server, $_GET["operator_id"]);
    $product_type_id = mysqli_real_escape_string($connection_server, $_GET["product_type_id"]);

    // Get API details
    $get_status = mysqli_query($connection_server, "SELECT * FROM sas_intl_airtime_status WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' AND product_name='intl-airtime' AND status='1' LIMIT 1");

    if (mysqli_num_rows($get_status) == 1) {
        $status_row = mysqli_fetch_array($get_status);
        $get_api_details = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE id='" . $status_row["api_id"] . "' && status='1' LIMIT 1");

        if (mysqli_num_rows($get_api_details) == 1) {
            $api_detail = mysqli_fetch_array($get_api_details);

            $curl_url = "https://vtpass.com/api/service-variations?serviceID=foreign-airtime&operator_id=$operator_id&product_type_id=$product_type_id";
            $curl_request = curl_init($curl_url);
            curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
            $curl_http_headers = array(
                "Authorization: Basic ".base64_encode($api_detail["api_key"]),
            );
            curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
            $curl_result = curl_exec($curl_request);
            curl_close($curl_request);

            header('Content-Type: application/json');
            echo $curl_result;
        } else {
            echo json_encode(["status" => "failed", "desc" => "API not found"]);
        }
    } else {
        echo json_encode(["status" => "failed", "desc" => "Service not active"]);
    }
}
?>
