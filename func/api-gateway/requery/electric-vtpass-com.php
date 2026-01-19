<?php
    if(!empty($requery_reference)){
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
        $curl_postfields_data = json_encode(array("request_id"=> $get_api_reference_id), true);
        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
        $curl_result = curl_exec($curl_request);
        $curl_json_result = json_decode($curl_result, true);
        

        if(in_array($curl_json_result["code"],array("000","044"))){
            $api_response = "successful";
            $api_response_reference = $curl_json_result["requestId"];
            $api_response_text = $curl_json_result["response_description"];
            $api_response_description = "Transaction Successful | Meter No: ".$curl_json_result["content"]["transactions"]["unique_element"]." | Meter Token: ".$curl_json_result["token"];
            $api_response_status = 1;
        }
        
        if(in_array($curl_json_result["code"],array("001","099"))){
            $api_response = "pending";
            $api_response_reference = $curl_json_result["requestId"];
            $api_response_text = $curl_json_result["response_description"];
            $api_response_description = "Transaction Pending | Meter No: ".$curl_json_result["content"]["transactions"]["unique_element"]." | Meter Token: ".$curl_json_result["token"];
            $api_response_status = 2;
        }
        
        if(!in_array($curl_json_result["code"],array("000","044","001","099"))){
            $api_response = "failed";
            $api_response_text = $curl_json_result["response_description"];
            $api_response_description = "Transaction Failed | Meter No: ".getTransaction($requery_reference, "product_unique_id")." recharge failed";
            $api_response_status = 3;
        }
    }
curl_close($curl_request);
?>