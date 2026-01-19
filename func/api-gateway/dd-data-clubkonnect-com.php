<?php
    $data_service_provider_alter_code = array("mtn" => "mtn", "airtel" => "airtel", "glo" => "glo", "9mobile" => "9mobile");
    if(in_array($product_name, array_keys($data_service_provider_alter_code))){
        if($product_name == "mtn"){
            $clubkonnect_isp_code = "01";
            $web_data_size_array = array("1gb_weekly_plan_plus_free_1gb_for_youtube_plus_100mb_for_youtube_music_plus_5mins" => "500.01","1gb_plus_1gb_youtube_night_plus_1hr_100mb_youtube_daily_plus_10mins" => "800.01","1.5gb_plus_1.4gb_youtube_night_plus_1hr_100mb_youtube_daily_plus_10mins" => "2000.01","1.8gb_plus_5mins" => "1500.01","4.25gb_plus_10mins" => "3000.01","5gb_weekly" => "1500.02","7gb_weekly" => "2000.02","5.5gb_monthly" => "3500.01","8gb_plus_2gb_youtube_night_300mb_youtube_music_plus_20mins" => "4500.01","11gb_plus_25mins" => "5000.01","15gb_plus_25mins" => "6500.01","20gb_monthly" => "7500.01","25gb_monthly" => "9000.01","32gb_30days_plus_40mins" => "11000.01","75gb_30days_plus_40mins" => "20000.01","120gb_30days_plus_80mins" => "35000.01","150gb_30days_plus_80mins" => "40000.01","400gb_1year" => "120000.01");
		}else{
            if($product_name == "airtel"){
                $clubkonnect_isp_code = "04";
                $web_data_size_array = array("75mb_1day" => "74.91","100mb_1day" => "99.91","200mb_1day" => "199.91","300mb_1day" => "299.91","500mb_7days" => "499.92","1gb_7days" => "799.91","1.5gb_7days" => "999.92","3.5gb_7days" => "1499.92","6gb_7days" => "2499.91","10gb_7days" => "2999.91","18gb_7days" => "4999.91","2gb_30days" => "1499.93","3gb_30days" => "1999.91","4gb_30days" => "2499.92","8gb_30days" => "2999.92","10gb_30days" => "3999.91","13gb_30days" => "4999.92","18gb_30days" => "5999.91","25gb_30days" => "7999.91","35gb_30days" => "9999.91","60gb_30days" => "14999.91","100gb_30days" => "19999.91","160gb_30days" => "29999.91","210gb_30days" => "39999.91","300gb_30days" => "49999.91","350gb_30days" => "49999.91","650gb_30days" => "99999.91");
            }else{
                if($product_name == "glo"){
                    $clubkonnect_isp_code = "02";
                    $web_data_size_array = array("150mb_1day_115mb_plus_35mb" => "105.01","350mb_2days_240mb_plus_110mb" => "350.01","3.9gb_30days_1.9gb_plus_2gb" => "2500.01","7.5gb_30days_3.5gb_plus_4gb" => "","9.2gb_30days_5.2gb_plus_4gb" => "5800.01","10.8gb_30days_6.8gb_plus_4gb" => "7700.01","14gb_30days_10gb_plus_4gb" => "10000.01","18gb_30days_14gb_plus_4gb" => "13250.01","24gb_30days_20gb_plus_4gb" => "18250.01","29.5gb_30days_27.5gb_plus_2gb" => "29500.01","50gb_30days_46gb_plus_4gb" => "50000.01","93gb_30days_86gb_plus_7gb" => "93000.01","119gb_30days_109gb_plus_10gb" => "119000.01","138gb_30days_126gb_plus_12gb" => "138000.01");
                }else{
                    if($product_name == "9mobile"){
                        $clubkonnect_isp_code = "03";
                        $web_data_size_array = array("1gb_daily_plus_100mb_social" => "300.01","2gb_3days_plus_100mb_social" => "500.01","7gb_plus_100mb_social_weekly" => "1500.01","4.2gb_2gb_all_time_plus_2.2gb_night_30days" => "1000.01","6.5gb_2.5gb_all_time_plus_4gb_night_30days" => "1200.01","9.5gb_5.5gb_all_time_plus_4gb_night_30days" => "2000.01","11gb_7gb_all_time_plus_4gb_night_30days" => "2500.01","12gb_30days" => "3000.01","18.5gb_15gb_all_time_plus_3.5gb_night_30days" => "4000.01","24gb_30days" => "5000.01","35gb_30days" => "7000.01","50gb_30days" => "10000.01","80gb_30days" => "15000.01","125gb_30days" => "20000.01");
                    }
                }
            }
        }
        if(in_array($quantity, array_keys($web_data_size_array))){
        	$explode_clubkonnect_apikey = array_filter(explode(":",trim($api_detail["api_key"])));
            $curl_url = "https://www.nellobytesystems.com/APIDatabundleV1.asp?UserID=".$explode_clubkonnect_apikey[0]."&APIKey=".$explode_clubkonnect_apikey[1]."&MobileNetwork=".$clubkonnect_isp_code."&DataPlan=".$web_data_size_array[$quantity]."&MobileNumber=".$phone_no;
            $curl_request = curl_init($curl_url);
            curl_setopt($curl_request, CURLOPT_HTTPGET, true);
            curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
            $curl_result = curl_exec($curl_request);
            $curl_json_result = json_decode($curl_result, true);
            

            if(curl_errno($curl_request)){
                $api_response = "failed";
                $api_response_text = 1;
                $api_response_description = "";
                $api_response_status = 3;
            }
            
            if(in_array($curl_json_result["statuscode"],array(200, 201, 299))){
            	$api_response = "successful";
            	$api_response_reference = $curl_json_result["orderid"];
            	$api_response_text = $curl_json_result["status"];
            	$api_response_description = "Transaction Successful | You have successfully shared ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data to 234".substr($phone_no, "1", "11");
            	$api_response_status = 1;
            }
            
            if(in_array($curl_json_result["statuscode"],array(100, 300))){
            	$api_response = "pending";
            	$api_response_reference = $curl_json_result["orderid"];
            	$api_response_text = $curl_json_result["status"];
            	$api_response_description = "Transaction Pending | You have successfully shared ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data to 234".substr($phone_no, "1", "11");
            	$api_response_status = 2;
            }
            
            if(!in_array($curl_json_result["statuscode"],array(100, 300, 200, 201, 299))){
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