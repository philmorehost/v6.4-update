<?php
    $explode_clubkonnect_apikey = array_filter(explode(":",trim($api_detail["api_key"])));
    $curl_url = "https://www.nellobytesystems.com/APIQueryV1.asp?UserID=".$explode_clubkonnect_apikey[0]."&APIKey=".$explode_clubkonnect_apikey[1]."&OrderID=".$get_api_reference_id;
    $curl_request = curl_init($curl_url);
    curl_setopt($curl_request, CURLOPT_HTTPGET, true);
    curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
    $curl_result = curl_exec($curl_request);
    $curl_json_result = json_decode($curl_result, true);
    
    
    if(in_array($curl_json_result["statuscode"],array(200, 201, 299))){
        $api_response = "successful";
        $api_response_reference = $curl_json_result["orderid"];
        $api_response_text = $curl_json_result["status"];
        $api_response_description = str_replace(["pending","failed"], "successful", str_replace(["Transaction Pending","Transaction Failed"], "Transaction Successful", getTransaction($requery_reference, "description")));
        $api_response_status = 1;
    }
    
    if(in_array($curl_json_result["statuscode"],array(100, 300))){
        $api_response = "pending";
        $api_response_reference = $curl_json_result["orderid"];
        $api_response_text = $curl_json_result["status"];
        $api_response_description = str_replace(["successful","failed"], "pending", str_replace(["Transaction Successful","Transaction Failed"], "Transaction Pending", getTransaction($requery_reference, "description")));
        $api_response_status = 2;
    }
    
    if(!in_array($curl_json_result["statuscode"],array(100, 300, 200, 201, 299))){
        $api_response = "failed";
        $api_response_text = $curl_json_result["status"];
        $api_response_description = "Transaction Failed | N".getTransaction($requery_reference, "amount")." Airtime to 234".substr(getTransaction($requery_reference, "product_unique_id"), "1", "11")." failed";
        $api_response_status = 3;
    }
curl_close($curl_request);
?>