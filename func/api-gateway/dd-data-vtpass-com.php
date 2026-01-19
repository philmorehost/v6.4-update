<?php
    $data_service_provider_alter_code = array("mtn" => "mtn", "airtel" => "airtel", "glo" => "glo", "9mobile" => "9mobile");
    if(in_array($product_name, array_keys($data_service_provider_alter_code))){
        if($product_name == "mtn"){
            $vtpass_isp_code = "mtn-data";
            $web_data_size_array = array("20gb_30days" => "mtn-20gb-5500", "40gb" => "mtn-40gb-11000", "75gb_30days" => "mtn-75gb-16000", "3gb_30days" => "mtn-3gb-1600", "120gb_60days" => "mtn-120gb-30000", "150gb_90days" => "mtn-150gb-50000", "1gb_24hrs" => "mtn-1gb-350", "200mb_2days" => "mtn-50mb-200", "75gb_60days" => "mtn-75gb-20000", "250gb_90days" => "mtn-250gb-75000", "400gb_365days" => "mtn-400gb-120000", "2000gb_365days" => "mtn-2000gb-450000", "10gb_30days" => "mtn-data-3500", "2.5gb_2days" => "mtn-2-5gb-600", "8gb_30days" => "mtn-8gb-3000");
		}else{
            if($product_name == "airtel"){
                $vtpass_isp_code = "airtel-data";
                $web_data_size_array = array("1.5gb" => "airt-1200", "3gb_30days" => "airt-1500", "4.5gb_30days" => "airt-2000", "750mb" => "airt-500", "200mb_3days" => "airt-200", "350mb_10_extra_7days" => "airt-350", "40gb_30days" => "airt-10000", "75gb_30days" => "airt-15000", "1gb_1day" => "airt-350x", "2gb_2days" => "airt-500x", "6gb_30days" => "airt-2500", "15gb" => "airt-4000", "18gb" => "airt-5000");
            }else{
                if($product_name == "glo"){
                    $vtpass_isp_code = "glo-data";
                    $web_data_size_array = array("350mb_2days" => "glo200", "1.35gb_14days" => "glo500", "5.8gb" => "glo2000", "7.7gb" => "glo2500", "10gb" => "glo3000", "18.25gb" => "glo5000", "29.5gb" => "glo8000", "50gb" => "glo10000", "93gb" => "glo15000", "119gb" => "glo18000", "50mb_1day" => "glo50x", "138gb" => "glo20000", "7gb_special_7days" => "glo1500x", "225gb_30days" => "glo30000", "300gb_30days" => "glo36000", "425gb_90days" => "glo50000", "525gb_90days" => "glo60000", "675gb_120days" => "glo75000");
                }else{
                    if($product_name == "9mobile"){
                        $vtpass_isp_code = "etisalat-data";
                        $web_data_size_array = array("15gb_30days" => "eti-4000", "7gb_7days" => "eti-1500", "650mb_24hrs" => "eti-200", "11gb_30days" => "eti-2500");
                    }
                }
            }
        }
        if(in_array($quantity, array_keys($web_data_size_array))){
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
            $vtpass_reference = substr(str_shuffle("12345678901234567890"), 0, 15);
            $curl_postfields_data = json_encode(array("request_id"=>$vtpass_reference,"serviceID"=>$vtpass_isp_code,"billersCode"=>$phone_no,"variation_code"=>$web_data_size_array[$quantity],"phone"=>$phone_no),true);
            curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
            $curl_result = curl_exec($curl_request);
            $curl_json_result = json_decode($curl_result, true);
            

            if(curl_errno($curl_request)){
                $api_response = "failed";
                $api_response_text = 1;
                $api_response_description = "";
                $api_response_status = 3;
            }
            
            if(in_array($curl_json_result["code"],array("000","044"))){
            	$api_response = "successful";
            	$api_response_reference = $curl_json_result["requestId"];
            	$api_response_text = $curl_json_result["response_description"];
            	$api_response_description = "Transaction Successful | You have successfully shared ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data to 234".substr($phone_no, "1", "11");
            	$api_response_status = 1;
            }
            
            if(in_array($curl_json_result["code"],array("001","099"))){
            	$api_response = "pending";
            	$api_response_reference = $curl_json_result["requestId"];
            	$api_response_text = $curl_json_result["response_description"];
            	$api_response_description = "Transaction Pending | You have successfully shared ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data to 234".substr($phone_no, "1", "11");
            	$api_response_status = 2;
            }
            
            if(!in_array($curl_json_result["code"],array("000","044","001","099"))){
            	$api_response = "failed";
            	$api_response_text = $curl_json_result["response_description"];
            	$api_response_description = "Transaction Failed | ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data shared to 234".substr($phone_no, "1", "11")." failed";
            	$api_response_status = 3;
            }
        }else{
            //Data size not available
            $api_response = "failed";
            $api_response_text = "";
            $api_response_description = "";
            $api_response_status = 3;
        }
    }else{
        //Service not available
        $api_response = "failed";
        $api_response_text = "";
        $api_response_description = "Service not available";
        $api_response_status = 3;
    }
curl_close($curl_request);
?>