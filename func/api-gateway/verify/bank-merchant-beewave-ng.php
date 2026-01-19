<?php
    $web_bank_code_name_array = $bank_code_name_array;
    if(in_array($bank_code, array_keys($web_bank_code_name_array))){
        // $curl_url = "https://".$api_detail["api_base_url"]."/web/api/verify-electric.php";
        $curl_url = "https://".$transfer_gateway_detail["gateway_name"]."/api/v1/bank-transfer/local-bank-verification.php";
        $curl_request = curl_init($curl_url);
        curl_setopt($curl_request, CURLOPT_POST, true);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
        $curl_http_headers = array(
        	"Content-Type: application/json",
        );
        curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
        $curl_postfields_data = json_encode(array("access_key"=> $transfer_gateway_detail["public_key"], "secret_key"=> $transfer_gateway_detail["secret_key"],"account_number"=> $account_number,"bank_code"=> $bank_code), true);
        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
        $curl_result = curl_exec($curl_request);
        $curl_json_result = json_decode($curl_result, true);
        fwrite(fopen("merchant.txt", "a++"), $curl_url."\n".$curl_result);
        
        if($curl_json_result["status"] === true){
        	$api_response = "successful";
        	$api_response_text = $curl_json_result["status"];
        	$api_response_description = "Bank Verified Successfully";
        	$api_response_account_name = $curl_json_result["data"]["account_name"];
        	$api_response_account_number = $curl_json_result["data"]["account_number"];
        	$api_response_bank_name = $curl_json_result["data"]["bank_name"];
        	$api_response_bank_code = $curl_json_result["data"]["bank_code"];
        	$api_response_enquiry_id = $curl_json_result["data"]["enquiry_id"];
        	$api_response_status = 1;
        }
        
        if($curl_json_result["status"] === false){
        	$api_response = "failed";
        	$api_response_text = $curl_json_result["status"];
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