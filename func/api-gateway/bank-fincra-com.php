<?php
$web_bank_code_name_array = $bank_code_name_array;
if (in_array($bank_code, array_keys($web_bank_code_name_array))) {
    $curl_url = "https://api.fincra.com/disbursements/payouts";
    $curl_request = curl_init($curl_url);
    curl_setopt($curl_request, CURLOPT_POST, true);
    curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
    $curl_http_headers = array(
        "api-key: " . $transfer_gateway_detail["secret_key"],
        "Accept: application/json",
        "Content-Type: application/json",
    );
    curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
    $beneficiary_array = array("firstName" => $get_logged_user_details["firstname"], "lastName" => $get_logged_user_details["lastname"], "email" => $get_logged_user_details["username"]."-".$get_logged_user_details["email"], "type" => "individual", "accountHolder" => "", "accountNumber" => $account_number, "bankCode" => $bank_code, "sortCode" => "9090", "country" => "NG", "registrationNumber" => "A909");
    $curl_postfields_data = json_encode(array("business" => $transfer_gateway_detail["encrypt_key"], "sourceCurrency" => "NGN", "destinationCurrency" => "NGN", "amount" => $amount, "description" => $narration, "customerReference" => $get_logged_user_details["username"], "beneficiary" => $beneficiary_array, "sender" => array("name" => $get_logged_user_details["firstname"]." ".$get_logged_user_details["lastname"], "email" => $get_logged_user_details["username"]."-".$get_logged_user_details["email"]), "paymentDestination" => "bank_account"), true);
    curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
    $curl_result = curl_exec($curl_request);
    $curl_json_result = json_decode($curl_result, true);
    fwrite(fopen("fincra-merchant.txt", "a++"), $curl_result."\n".$curl_postfields_data);
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