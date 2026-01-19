<?php
$purchase_method = strtoupper($purchase_method);
$purchase_method_array = array("API", "WEB", "APP");
if (in_array($purchase_method, $purchase_method_array)) {
    if ($purchase_method === "WEB") {
        if (isset($_SESSION["transfer_enquiry_id"])) {
            $transfer_enquiry_id = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_SESSION["transfer_enquiry_id"]))));
            $bank_code = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_SESSION["bank_code"]))));
            $amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_SESSION["amount"]))));
            $account_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_SESSION["account_number"]))));
            $narration = mysqli_real_escape_string($connection_server, trim(strip_tags($_SESSION["narration"])));
        } else {
            $bank_code = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["bank-code"]))));
            $amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_POST["amount"]))));
            $account_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_POST["account-number"]))));
            $narration = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["narration"])));
        }

    }

    if (in_array($purchase_method, array("API", "APP"))) {
        $transfer_enquiry_id = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["enquiry_id"]))));
        $bank_code = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["bank_code"]))));
        $amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($get_api_post_info["amount"]))));
        $account_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($get_api_post_info["account_number"]))));
        $narration = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["narration"]))));
        //$requery_reference = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/","",trim(strip_tags($get_api_post_info["reference"]))));

    }
    //$discounted_amount = $amount;
    $bank_code_alternative = ucwords("Bank Transfer");
    $reference = substr(str_shuffle("12345678901234567890"), 0, 15);
    $description = "Bank Charges";
    $status = 3;


    $bank_code_array = array();
    $bank_code_name_array = array();
    $retrieve_bank_list = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/func/banks.json");
    $retrieve_bank_list = json_decode($retrieve_bank_list, true);
    if (is_array($retrieve_bank_list)) {
        foreach ($retrieve_bank_list as $each_bank) {
            $each_bank_json = $each_bank;
            array_push($bank_code_array, $each_bank_json["bankCode"]);
            $bank_code_name_array[$each_bank_json["bankCode"]] = $each_bank_json["bankName"];
        }
    }


    if (in_array($bank_code, $bank_code_array)) {
        //Transfer Service
        if ($action_function == 1) {
            if (!empty(userBalance(1)) && is_numeric(userBalance(1)) && (userBalance(1) > 0)) {
                if (userBalance(1) >= $amount && !empty($amount) && is_numeric($amount)) {
                    if (!empty($transfer_enquiry_id) && !empty($narration) && !empty($bank_code)) {
                        if (!empty($account_number) && is_numeric($account_number) && (strlen($account_number) == 10) && !empty($bank_code)) {
                            $get_transfer_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_bank_transfer_gateways WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && status='1'");

                            if (mysqli_num_rows($get_transfer_gateway_details) == 1) {
                                $transfer_gateway_detail = mysqli_fetch_array($get_transfer_gateway_details);
                                if (!empty($transfer_gateway_detail["public_key"]) && !empty($transfer_gateway_detail["secret_key"])) {
                                    if ($transfer_gateway_detail["status"] == 1) {


                                        $transfer_fee = $transfer_gateway_detail["transfer_fee"];
                                        $amount = floatval($amount);
                                        $discounted_amount = (floatval($amount) + floatval($transfer_fee));

                                        if ((userBalance(1) >= $amount) && !empty($amount) && is_numeric($amount)) {
                                            $debit_user = chargeUser("debit", $account_number, $bank_code_alternative, $reference, "", $amount, $discounted_amount, $description, $purchase_method, $_SERVER["HTTP_HOST"], $status);
                                            if ($debit_user === "success") {

                                                $api_gateway_name_file_exists = "bank-" . str_replace(".", "-", $transfer_gateway_detail["gateway_name"]) . ".php";
                                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                                    $api_gateway_name = "bank-" . str_replace(".", "-", $transfer_gateway_detail["gateway_name"]) . ".php";

                                                    // Reset variables at the start of each transaction
                                                    $api_response = null;
                                                    $api_response_text = null;
                                                    $api_response_reference = null;
                                                    $api_response_description = null;
                                                    $api_response_account_name = null;
                                                    $api_response_account_number = null;
                                                    $api_response_bank_name = null;
                                                    $api_response_bank_code = null;
                                                    $api_response_session_id = null;
                                                    $api_response_enquiry_id = null;
                                                    $api_response_narration = null;
                                                    $api_response_status = 1;

                                                    include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                                    $api_response_text = strtolower($api_response_text);
                                                    if (in_array($api_response, array("successful", "pending"))) {
                                                        alterTransaction($reference, "status", $api_response_status);
                                                        alterTransaction($reference, "api_id", "");
                                                        alterTransaction($reference, "product_id", $account_number);
                                                        alterTransaction($reference, "api_reference", $api_response_reference);
                                                        $description = "Bank Transfer to Account Name: " . $api_response_account_name . " (" . $api_response_account_number . ") (" . $api_response_bank_name . ")";
                                                        alterTransaction($reference, "description", $description);
                                                        alterTransaction($reference, "api_website", $transfer_gateway_detail["gateway_name"]);

                                                        $json_response_array = array("ref" => $reference, "status" => "success", "desc" => $api_response_description, "session_id" => $api_response_session_id, "enquiry_id" => $api_response_enquiry_id, "customer_name" => $api_response_account_name, "bank_name" => $api_response_bank_name, "bank_code" => $api_response_bank_code, "account_name" => $api_response_account_name, "account_number" => $api_response_account_number, "narration" => $api_response_narration);
                                                        $json_response_encode = json_encode($json_response_array, true);
                                                    }
                                                    if ($api_response == "failed") {
                                                        $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                                        alterTransaction($reference, "api_id", "");
                                                        alterTransaction($reference, "product_id", $account_number);
                                                        alterTransaction($reference, "description", $api_response_description);
                                                        chargeUser("credit", $account_number, "Refund", $reference_2, "", $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $purchase_method, $_SERVER["HTTP_HOST"], "1");
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
                                                        $json_response_array = array("status" => "failed", "desc" => "Transfer Failed");
                                                        $json_response_encode = json_encode($json_response_array, true);
                                                    }
                                                } else {
                                                    //Server unavailable at the moment
                                                    $json_response_array = array("status" => "failed", "desc" => "Server unavailable at the moment");
                                                    $json_response_encode = json_encode($json_response_array, true);
                                                }
                                            } else {
                                                //Unable to proceed with charges
                                                $json_response_array = array("status" => "failed", "desc" => "Unable to proceed with charges");
                                                $json_response_encode = json_encode($json_response_array, true);
                                            }
                                        } else {
                                            //Insufficient Wallet Balance
                                            $json_response_array = array("status" => "failed", "desc" => "Insufficient Wallet Balance");
                                            $json_response_encode = json_encode($json_response_array, true);
                                        }
                                    } else {
                                        //System Is Busy
                                        $json_response_array = array("status" => "failed", "desc" => "System Is Busy");
                                        $json_response_encode = json_encode($json_response_array, true);
                                    }
                                } else {
                                    //Api Key Empty (Empty Gateway Key)
                                    $json_response_array = array("status" => "failed", "desc" => "Empty Gateway Key");
                                    $json_response_encode = json_encode($json_response_array, true);
                                }
                            } else {
                                if (mysqli_num_rows($get_transfer_gateway_details) > 1) {
                                    //More than 1 gateway enabled (System is unavailable, try again later)
                                    $json_response_array = array("status" => "failed", "desc" => "System is unavailable, try again later");
                                    $json_response_encode = json_encode($json_response_array, true);
                                } else {
                                    if (mysqli_num_rows($get_transfer_gateway_details) < 1) {
                                        //Gateway Disabled (Product Not Available)
                                        $json_response_array = array("status" => "failed", "desc" => "Service Not Available");
                                        $json_response_encode = json_encode($json_response_array, true);
                                    }
                                }
                            }
                        } else {
                            //Incomplete Account Details
                            $json_response_array = array("status" => "failed", "desc" => "Incomplete Account Details");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    } else {
                        //Incomplete Parameters
                        $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters (Narration/Enquiry/Bank)");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    //Insufficient Funds
                    $json_response_array = array("status" => "failed", "desc" => "Insufficient Funds");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Balance is LOW
                $json_response_array = array("status" => "failed", "desc" => "Balance is LOW");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

        //Verify Service
        if ($action_function == 3) {
            if (!empty($account_number) && is_numeric($account_number) && (strlen($account_number) == 10) && !empty($bank_code)) {
                $get_transfer_gateway_details = mysqli_query($connection_server, "SELECT * FROM sas_bank_transfer_gateways WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && status='1'");

                if (mysqli_num_rows($get_transfer_gateway_details) == 1) {
                    $transfer_gateway_detail = mysqli_fetch_array($get_transfer_gateway_details);
                    if (!empty($transfer_gateway_detail["public_key"]) && !empty($transfer_gateway_detail["secret_key"])) {
                        if ($transfer_gateway_detail["status"] == 1) {
                            $transfer_fee = $transfer_gateway_detail["transfer_fee"];
                            $api_gateway_name_file_exists = "bank-" . str_replace(".", "-", $transfer_gateway_detail["gateway_name"]) . ".php";
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/verify/" . $api_gateway_name_file_exists)) {
                                $api_gateway_name = "bank-" . str_replace(".", "-", $transfer_gateway_detail["gateway_name"]) . ".php";

                                // Reset variables at the start of each transaction
                                $api_response = null;
                                $api_response_text = null;
                                $api_response_description = null;
                                $api_response_account_name = null;
                                $api_response_account_number = null;
                                $api_response_bank_name = null;
                                $api_response_bank_code = null;
                                $api_response_enquiry_id = null;
                                $api_response_status = 1;

                                include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/verify/" . $api_gateway_name);
                                $api_response_text = strtolower($api_response_text);
                                if (in_array($api_response, array("successful", "pending"))) {
                                $narration = isset($narration) && !empty($narration) ? $narration : "";
                                    $json_response_array = array("status" => "success", "desc" => $api_response_description, "enquiry_id" => $api_response_enquiry_id, "customer_name" => $api_response_account_name, "bank_name" => $api_response_bank_name, "bank_code" => $api_response_bank_code, "account_name" => $api_response_account_name, "account_number" => $api_response_account_number, "narration" => $narration);
                                    $json_response_encode = json_encode($json_response_array, true);
                                }
                                if ($api_response == "failed") {
                                    $json_response_array = array("status" => "failed", "desc" => "Error: Unable to verify bank account");
                                    $json_response_encode = json_encode($json_response_array, true);
                                }
                            } else {
                                //Server unavailable at the moment
                                $json_response_array = array("status" => "failed", "desc" => "Server unavailable at the moment");
                                $json_response_encode = json_encode($json_response_array, true);
                            }
                        } else {
                            //System Is Busy
                            $json_response_array = array("status" => "failed", "desc" => "System Is Busy");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    } else {
                        //Api Key Empty (Empty Gateway Key)
                        $json_response_array = array("status" => "failed", "desc" => "Empty Gateway Key");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    if (mysqli_num_rows($get_transfer_gateway_details) > 1) {
                        //More than 1 gateway enabled (System is unavailable, try again later)
                        $json_response_array = array("status" => "failed", "desc" => "System is unavailable, try again later");
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        if (mysqli_num_rows($get_transfer_gateway_details) < 1) {
                            //Gateway Disabled (Product Not Available)
                            $json_response_array = array("status" => "failed", "desc" => "Service Not Available");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    }
                }
            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

    } else {
        //Invalid bank type
        $json_response_array = array("status" => "failed", "desc" => "Invalid bank type");
        $json_response_encode = json_encode($json_response_array, true);
    }
} else {
    //Purchase Method Not specified
    $json_response_array = array("status" => "failed", "desc" => "Purchase Method Not specified");
    $json_response_encode = json_encode($json_response_array, true);
}
?>