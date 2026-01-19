<?php
    // Service ID for Foreign Airtime
    $serviceID = "foreign-airtime";

    $curl_url = "https://vtpass.com/api/pay";
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

    $request_id = "INTL" . date('YmdHi') . substr(str_shuffle("1234567890"), 0, 8);

    $curl_postfields_data = json_encode(array(
        "request_id" => $request_id,
        "serviceID" => $serviceID,
        "billersCode" => $phone_no,
        "variation_code" => $variation_code,
        "amount" => $amount,
        "phone" => $phone_no,
        "operator_id" => $operator_id,
        "country_code" => $country_code,
        "product_type_id" => $product_type_id,
        "email" => $get_logged_user_details["email"]
    ));

    curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
    $curl_result = curl_exec($curl_request);
    $curl_json_result = json_decode($curl_result, true);

    if(curl_errno($curl_request)){
        $api_response = "failed";
        $api_response_text = 1;
        $api_response_description = "Server connection error";
        $api_response_status = 3;
    } else {
        if(in_array($curl_json_result["code"], array("000", "044"))){
            $api_response = "successful";
            $api_response_reference = $curl_json_result["requestId"];
            $api_response_text = $curl_json_result["response_description"];
            $api_response_description = "Transaction Successful";
            $api_response_status = 1;
        } else if(in_array($curl_json_result["code"], array("001", "099"))){
            $api_response = "pending";
            $api_response_reference = $curl_json_result["requestId"] ?? $request_id;
            $api_response_text = $curl_json_result["response_description"];
            $api_response_description = "Transaction Pending";
            $api_response_status = 2;
        } else {
            $api_response = "failed";
            $api_response_text = $curl_json_result["response_description"] ?? "Unknown error";
            $api_response_description = "Transaction Failed: " . ($curl_json_result["response_description"] ?? "");
            $api_response_status = 3;
        }
    }
    curl_close($curl_request);
?>