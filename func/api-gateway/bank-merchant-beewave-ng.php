<?php
$web_bank_code_name_array = $bank_code_name_array;
if (in_array($bank_code, array_keys($web_bank_code_name_array))) {
    $curl_url = "https://" . $transfer_gateway_detail["gateway_name"] . "/api/v1/bank-transfer/local-transfer";
    $curl_request = curl_init($curl_url);
    curl_setopt($curl_request, CURLOPT_POST, true);
    curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
    $curl_http_headers = array(
        "Content-Type: application/json",
    );
    curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
    $curl_postfields_data = json_encode(array("access_key" => $transfer_gateway_detail["public_key"], "secret_key" => $transfer_gateway_detail["secret_key"], "encrypt_key" => $transfer_gateway_detail["encrypt_key"], "enquiry_id" => $transfer_enquiry_id, "account_number" => $account_number, "bank_code" => $bank_code, "amount" => $amount, "narration" => $narration), true);
    curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
    $curl_result = curl_exec($curl_request);
    $curl_json_result = json_decode($curl_result, true);
    // fwrite(fopen("merchant.txt", "a++"), $curl_result);
    // $curl_json_result = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/func/api-gateway/transfer.json"), true);

    if ($curl_json_result["status"] === true) {
        $api_response = "successful";
        $api_response_text = $curl_json_result["data"]["status"];
        $api_response_reference = $curl_json_result["data"]["transaction_ref"];
        $api_response_description = "Transfer Successful";
        $api_response_account_name = $curl_json_result["data"]["destination"]["account_name"];
        $api_response_account_number = $curl_json_result["data"]["destination"]["account_number"];
        $api_response_bank_name = $curl_json_result["data"]["destination"]["bank_name"];
        $api_response_bank_code = $curl_json_result["data"]["destination"]["bank_code"];
        $api_response_enquiry_id = $curl_json_result["data"]["destination"]["enquiry_id"];
        $api_response_session_id = $curl_json_result["data"]["destination"]["session_id"];
        $api_response_narration = $curl_json_result["data"]["destination"]["narration"];
        $api_response_status = 1;
        mysqli_query($connection_server, "INSERT INTO sas_bank_transfer_history (vendor_id, reference, username, amount, amount_charged, bank_code, bank_name, account_name, account_number, narration, session_id) VALUES ('" . $get_logged_user_details["vendor_id"] . "', '$reference', '" . $get_logged_user_details["username"] . "', '$amount', '$discounted_amount', '$api_response_bank_code', '$api_response_bank_name', '$api_response_account_name', '$api_response_account_number', '$api_response_narration', '$api_response_session_id')");
    }

    if ($curl_json_result["status"] === false) {
        $api_response = "failed";
        $api_response_text = $curl_json_result["data"]["status"];
        $api_response_description = "Err: Transfer Failed";
        $api_response_status = 3;
    }
} else {
    //Electric size not available
    $api_response = "failed";
    $api_response_text = "";
    $api_response_description = "";
    $api_response_status = 3;
}
// curl_close($curl_request);
?>