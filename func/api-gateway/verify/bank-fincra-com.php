<?php
    $web_bank_code_name_array = $bank_code_name_array;
    if(in_array($bank_code, array_keys($web_bank_code_name_array))){
        
        $curl_url = "https://sandboxapi.fincra.com/core/accounts/resolve";
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
        curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);
        $curl_postfields_data = json_encode(array("accountNumber"=> $account_number,"bankCode"=> $bank_code, "currency" => "NGN", "type" => "nuban"), true);
        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
        $curl_result = curl_exec($curl_request);
        $curl_json_result = json_decode($curl_result, true);
        fwrite(fopen("fincra-merchant.txt", "a++"), $curl_url."\n".$curl_result);
        
        if($curl_json_result["success"] === true){
        	$api_response = "successful";
        	$api_response_text = $curl_json_result["success"];
        	$api_response_description = "Bank Verified Successfully";
        	$api_response_account_name = $curl_json_result["data"]["accountName"];
        	$api_response_account_number = $curl_json_result["data"]["accountNumber"];
        	$api_response_bank_name = "";
        	$api_response_bank_code = $curl_json_result["data"]["bankCode"];
        	$api_response_enquiry_id = substr(str_shuffle("12345678901234567890"), 0, 15)."_FC";
        	$api_response_status = 1;
        }
        
        if($curl_json_result["success"] === false){
        	$api_response = "failed";
        	$api_response_text = $curl_json_result["success"];
        	$api_response_description = "Err: Cannot Verify Bank Account";
        	$api_response_status = 3;
        }
    }else{
        //Electric size not available
        $api_response = "failed";
        $api_response_text = "";
        $api_response_description = "";
        $api_response_status = 3;
    }
curl_close($curl_request);
?>