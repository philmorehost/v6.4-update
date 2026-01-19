<?php
    $data_service_provider_alter_code = array("mtn" => "mtn", "airtel" => "airtel", "glo" => "glo", "9mobile" => "9mobile");
    if(in_array($product_name, array_keys($data_service_provider_alter_code))){
        if($product_name == "mtn"){
            $web_data_size_array = array("1gb_weekly_plan_plus_free_1gb_for_youtube_plus_100mb_for_youtube_music_plus_5mins" => "1gb_weekly_plan_plus_free_1gb_for_youtube_plus_100mb_for_youtube_music_plus_5mins", "1.5gb_plus_1.4gb_youtube_night_plus_1hr_100mb_youtube_daily_plus_10mins" => "1.5gb_plus_1.4gb_youtube_night_plus_1hr_100mb_youtube_daily_plus_10mins", "10gb_plus_2gb_youtube_night_300mb_youtube_music_plus_20mins" => "10gb_plus_2gb_youtube_night_300mb_youtube_music_plus_20mins", "20gb_plus_2gb_youtube_night_300mb_youtube_music_plus_25mins" => "20gb_plus_2gb_youtube_night_300mb_youtube_music_plus_25mins", "40gb_30days_plus_40mins" => "40gb_30days_plus_40mins", "75gb_30days_plus_40mins" => "75gb_30days_plus_40mins", "120gb_30days_plus_80mins" => "120gb_30days_plus_80mins", "1tb_1year" => "1tb_1year");
        }else{
            if($product_name == "airtel"){
                $web_data_size_array = array("1gb_1day" => "1gb_1day", "2gb_1day" => "2gb_1day", "1.5gb_plus_2gb_youtube_night_plus_200mb_youtube_music_spotify_30days" => "1.5gb_plus_2gb_youtube_night_plus_200mb_youtube_music_spotify_30days", "4.5gb_plus_4gb_youtube_night_plus_450mb_spotify_plus_510mb_tiktok_510mb_airtel_tv_streaming_30days" => "4.5gb_plus_4gb_youtube_night_plus_450mb_spotify_plus_510mb_tiktok_510mb_airtel_tv_streaming_30days", "6gb_plus_4gb_youtube_night_450mb_spotify_510mb_tiktok_plus_510mb_airtel_tv_streaming_30days" => "6gb_plus_4gb_youtube_night_450mb_spotify_510mb_tiktok_plus_510mb_airtel_tv_streaming_30days", "10gb_plus_2gb_youtube_night_plus_200mb_spotify_30days" => "10gb_plus_2gb_youtube_night_plus_200mb_spotify_30days", "40gb_30days" => "40gb_30days", "75gb_30days" => "75gb_30days", "110gb_30days" => "110gb_30days");
            }else{
                if($product_name == "glo"){
                    $web_data_size_array = array("150mb_1day_115mb_plus_35mb" => "150mb_1day_115mb_plus_35mb", "350mb_2days_240mb_plus_110mb" => "350mb_2days_240mb_plus_110mb", "3.9gb_30days_1.9gb_plus_2gb" => "3.9gb_30days_1.9gb_plus_2gb", "7.5gb_30days_3.5gb_plus_4gb" => "7.5gb_30days_3.5gb_plus_4gb", "9.2gb_30days_5.2gb_plus_4gb" => "9.2gb_30days_5.2gb_plus_4gb", "10.8gb_30days_6.8gb_plus_4gb" => "10.8gb_30days_6.8gb_plus_4gb", "14gb_30days_10gb_plus_4gb" => "14gb_30days_10gb_plus_4gb", "18gb_30days_14gb_plus_4gb" => "18gb_30days_14gb_plus_4gb", "24gb_30days_20gb_plus_4gb" => "24gb_30days_20gb_plus_4gb", "29.5gb_30days_27.5gb_plus_2gb" => "29.5gb_30days_27.5gb_plus_2gb", "50gb_30days_46gb_plus_4gb" => "50gb_30days_46gb_plus_4gb", "93gb_30days_86gb_plus_7gb" => "93gb_30days_86gb_plus_7gb", "119gb_30days_109gb_plus_10gb" => "119gb_30days_109gb_plus_10gb", "138gb_30days_126gb_plus_12gb" => "138gb_30days_126gb_plus_12gb");
                }else{
                    if($product_name == "9mobile"){
                        $web_data_size_array = array("1gb_daily_plus_100mb_social" => "1gb_daily_plus_100mb_social", "2gb_3days_plus_100mb_social" => "2gb_3days_plus_100mb_social", "7gb_plus_100mb_social_weekly" => "7gb_plus_100mb_social_weekly", "4.2gb_2gb_all_time_plus_2.2gb_night_30days" => "4.2gb_2gb_all_time_plus_2.2gb_night_30days", "6.5gb_2.5gb_all_time_plus_4gb_night_30days" => "6.5gb_2.5gb_all_time_plus_4gb_night_30days", "9.5gb_5.5gb_all_time_plus_4gb_night_30days" => "9.5gb_5.5gb_all_time_plus_4gb_night_30days", "11gb_7gb_all_time_plus_4gb_night_30days" => "11gb_7gb_all_time_plus_4gb_night_30days", "12gb_30days" => "12gb_30days", "18.5gb_15gb_all_time_plus_3.5gb_night_30days" => "18.5gb_15gb_all_time_plus_3.5gb_night_30days", "24gb_30days" => "24gb_30days", "35gb_30days" => "35gb_30days", "50gb_30days" => "50gb_30days", "80gb_30days" => "80gb_30days", "125gb_30days" => "125gb_30days");
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
            $curl_postfields_data = json_encode(array("api_key"=> $api_detail["api_key"],"network"=> $product_name,"phone_number"=> $phone_no,"type"=> "dd-data", "quantity"=>$web_data_size_array[$quantity]), true);
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