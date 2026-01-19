<?php
$purchase_method = strtoupper($purchase_method);
$purchase_method_array = array("API", "WEB", "CRON_JOB", "APP");
if (in_array($purchase_method, $purchase_method_array)) {
    if ($purchase_method === "WEB") {
        $action_function = 2;
        $requery_reference = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_GET["requery"]))));
    }

    if (in_array($purchase_method, array("API", "APP"))) {
        $requery_reference = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($get_api_post_info["reference"]))));
    }

    if ($purchase_method === "CRON_JOB") {
        $requery_reference = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($cron_job_requery_reference))));
    }

    //Requery Transaction
    if ($action_function == 2) {
        if (!empty($requery_reference)) {
            if (getTransaction($requery_reference, "status") !== false) {
                if (getTransaction($requery_reference, "status") !== "3") {
                    $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . getTransaction($requery_reference, "api_id") . "'");
                    $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . getTransaction($requery_reference, "api_id") . "' && status='1'");

                    if (mysqli_num_rows($get_api_lists) > 0) {
                        while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                            if (!empty($api_detail["api_key"])) {
                                $api_gateway_name_file_exists = $api_detail["api_type"] . "-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/requery/" . $api_gateway_name_file_exists)) {
                                    $api_gateway_name = $api_detail["api_type"] . "-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                } else {
                                    $api_gateway_name = $api_detail["api_type"] . "-localserver.php";
                                }
                                $get_api_reference_id = getTransaction($requery_reference, "api_reference");

                                // Reset variables at the start of each transaction
                                $api_response = null;
                                $api_response_description = null;
                                $api_response_reference = null;
                                $api_response_text = null;
                                $api_response_status = null;

                                include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/requery/" . $api_gateway_name);
                                $api_response_text = strtolower($api_response_text);
                                if ($api_response == "successful") {
                                    alterTransaction($requery_reference, "status", $api_response_status);
                                    alterTransaction($requery_reference, "description", $api_response_description);
                                    $json_response_array = array("ref" => $requery_reference, "status" => "success", "desc" => "Transaction Successful", "response_desc" => $api_response_description);
                                    $json_response_encode = json_encode($json_response_array, true);
                                }

                                if ($api_response == "pending") {
                                    alterTransaction($requery_reference, "status", $api_response_status);
                                    alterTransaction($requery_reference, "description", $api_response_description);
                                    $json_response_array = array("ref" => $requery_reference, "status" => "pending", "desc" => "Transaction Pending", "response_desc" => $api_response_description);
                                    $json_response_encode = json_encode($json_response_array, true);
                                }

                                if ($api_response == "failed") {
                                    removeProductPurchaseList($requery_reference);
                                    $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                    $phone_no = getTransaction($requery_reference, "product_unique_id");
                                    $amount = getTransaction($requery_reference, "amount");
                                    $discounted_amount = getTransaction($requery_reference, "discounted_amount");
                                    $previous_purchase_method = getTransaction($requery_reference, "mode");

                                    chargeUser("credit", $phone_no, "Refund", $reference_2, "", $amount, $discounted_amount, "Refund for Ref:<i>'$requery_reference'</i>", $previous_purchase_method, $_SERVER["HTTP_HOST"], "1");
                                    // Email Beginning
                                    $log_template_encoded_text_array = array("{firstname}" => $get_logged_user_details["firstname"], "{lastname}" => $get_logged_user_details["lastname"], "{amount}" => "N" . $discounted_amount, "{description}" => "Refund for Ref No: $reference");
                                    $raw_log_template_subject = getUserEmailTemplate('user-refund', 'subject');
                                    $raw_log_template_body = getUserEmailTemplate('user-refund', 'body');
                                    foreach ($log_template_encoded_text_array as $array_key => $array_val) {
                                        $raw_log_template_subject = str_replace($array_key, $array_val, $raw_log_template_subject);
                                        $raw_log_template_body = str_replace($array_key, $array_val, $raw_log_template_body);
                                    }
                                    sendVendorEmail($get_logged_user_details["email"], $raw_log_template_subject, $raw_log_template_body);
                                    // Email End
                                    alterTransaction($requery_reference, "status", $api_response_status);
                                    alterTransaction($requery_reference, "description", $api_response_description);
                                    $json_response_array = array("status" => "failed", "desc" => "Transaction Failed");
                                    $json_response_encode = json_encode($json_response_array, true);
                                }
                            } else {
                                //Api Key Empty (Empty Gateway Key)
                                $json_response_array = array("status" => "failed", "desc" => "Empty Gateway Key");
                                $json_response_encode = json_encode($json_response_array, true);
                            }
                        }
                    } else {
                        //No API Installed
                        $json_response_array = array("status" => "failed", "desc" => "Gateway Error");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    //Account Refunded Already
                    $json_response_array = array("status" => "success", "desc" => "Account Refunded Already");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Transaction doesnt Exists
                $json_response_array = array("status" => "failed", "desc" => "Transaction doesnt Exists");
                $json_response_encode = json_encode($json_response_array, true);
            }
        } else {
            //Incomplete Parameters
            $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters");
            $json_response_encode = json_encode($json_response_array, true);
        }
    }
} else {
    //Purchase Method Not specified
    $json_response_array = array("status" => "failed", "desc" => "Purchase Method Not specified");
    $json_response_encode = json_encode($json_response_array, true);
}
?>