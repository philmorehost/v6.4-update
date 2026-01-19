<?php
    $data_service_provider_alter_code = array("mtn" => "mtn", "airtel" => "airtel", "glo" => "glo", "9mobile" => "9mobile");
    if(in_array($product_name, array_keys($data_service_provider_alter_code))){
        if($product_name == "mtn"){
            $web_data_size_array = array("250mb" => "250mb", "500mb" => "500mb", "1gb" => "1gb", "2gb" => "2gb", "3gb" => "3gb", "5gb" => "5gb", "10gb" => "10gb");
        }else{
            if($product_name == "airtel"){
                $web_data_size_array = array("300mb" => "300mb", "500mb" => "500mb", "1gb" => "1gb", "2gb" => "2gb", "5gb" => "5gb", "10gb" => "10gb");
            }else{
                if($product_name == "glo"){
                    $web_data_size_array = array("200mb" => "200mb", "500mb" => "500mb", "1gb" => "1gb", "2gb" => "2gb", "3gb" => "3gb", "5gb" => "5gb", "10gb" => "10gb");
                }else{
                    if($product_name == "9mobile"){
                        $web_data_size_array = array("500mb" => "500mb", "1gb" => "1gb", "2gb" => "2gb", "3gb" => "3gb", "5gb" => "5gb", "10gb" => "10gb");
                    }
                }
            }
        }
        if(in_array($quantity, array_keys($web_data_size_array))){
            $curl_url = "https://".$api_detail["api_base_url"]."/web/api/data.php";
            $curl_request = curl_init($curl_url);
            curl_setopt($curl_request, CURLOPT_POST, true);
            curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
            $curl_http_headers = array(
                "Content-Type: application/json",
            );
            curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
            $curl_postfields_data = json_encode(array("api_key"=> $api_detail["api_key"],"network"=> $product_name,"phone_number"=> $phone_no,"type"=> "cg-data", "quantity"=>$web_data_size_array[$quantity]), true);
            curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
            $curl_result = curl_exec($curl_request);
            $curl_json_result = json_decode($curl_result, true);
            

            if(curl_errno($curl_request)){
                $api_response = "failed";
                $api_response_text = 1;
                $api_response_description = "";
                $api_response_status = 3;
            }
            
            if(in_array($curl_json_result["status"],array("success"))){
                $api_response = "successful";
                $api_response_reference = $curl_json_result["ref"];
                $api_response_text = $curl_json_result["status"];
                $api_response_description = "Transaction Successful | You have successfully shared ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data to 234".substr($phone_no, "1", "11");
                $api_response_status = 1;
            }
            
            if(in_array($curl_json_result["status"],array("pending"))){
                $api_response = "pending";
                $api_response_reference = $curl_json_result["ref"];
                $api_response_text = $curl_json_result["status"];
                $api_response_description = "Transaction Pending | You have successfully shared ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data to 234".substr($phone_no, "1", "11");
                $api_response_status = 2;
            }
            
            if(in_array($curl_json_result["status"],array("failed"))){
                $api_response = "failed";
                $api_response_text = $curl_json_result["status"];
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