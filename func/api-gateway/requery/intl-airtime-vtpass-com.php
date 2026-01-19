<?php
    $curl_url = "https://vtpass.com/api/requery";
    $curl_request = curl_init($curl_url);
    curl_setopt($curl_request, CURLOPT_POST, true);
    curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);

    $curl_http_headers = array(
        "Authorization: Basic ".base64_encode($api_detail["api_key"]),
        "Content-Type: application/json",
    );
    curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);

    $curl_postfields_data = json_encode(array(
        "request_id" => $get_api_reference_id
    ), true);

    curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
    $curl_result = curl_exec($curl_request);
    $curl_json_result = json_decode($curl_result, true);

    if(curl_errno($curl_request)){
        $api_response = "pending";
        $api_response_status = 2;
    } else {
        if(in_array($curl_json_result["code"], array("000", "044"))){
            $api_response = "successful";
            $api_response_status = 1;
            $api_response_description = "Transaction Successful | " . ($curl_json_result["content"]["transactions"]["product_name"] ?? "");
        } else if(in_array($curl_json_result["code"], array("001", "099"))){
            $api_response = "pending";
            $api_response_status = 2;
            $api_response_description = "Transaction Pending";
        } else {
            $api_response = "failed";
            $api_response_status = 3;
            $api_response_description = "Transaction Failed: " . ($curl_json_result["response_description"] ?? "");
        }
    }
    curl_close($curl_request);
?>