<?php
$purchase_method = strtoupper($purchase_method);
$purchase_method_array = array("API", "WEB", "APP");
$allowed_country_array = array("nigeria");
$allowed_kyc_array = array("nigerian_nin", "nigerian_pvc", "nigerian_drivers_license", "nigerian_international_passport");
if (in_array($purchase_method, $purchase_method_array)) {
    if ($purchase_method === "WEB") {
        $isp = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["isp"]))));
        $qty_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_POST["qty-number"]))));
        $type = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["type"]))));
        $quantity = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["quantity"]))));
        $firstname = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["firstname"])));
        $lastname = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["lastname"])));
        $dob = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["dob"])));
        $email = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["email"])));
        $phone = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["phone"])));
        $address = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["address"])));
        $postal_code = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["postal-code"])));
        $city = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["city"])));
        $state = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["state"])));
        $country = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["country"])));
        $kyc_mode = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["kyc-mode"])));
        $kyc_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["kyc-id"])));
        $id_selfie_url = "";
        $callback_url = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["callback-url"]))));

        $card_holder_ref = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["card-holder-ref"]))));

        $card_ref = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["card-ref"]))));

        $card_status = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["card-status"]))));

        $amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_POST["amount"]))));

    }

    if (in_array($purchase_method, array("API", "APP"))) {
        $isp = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["provider"]))));
        $qty_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($get_api_post_info["qty_number"]))));
        $type = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["type"]))));
        $quantity = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["quantity"]))));
        $firstname = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["firstname"])));
        $lastname = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["lastname"])));
        $dob = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["dob"])));
        $email = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["email"])));
        $phone = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["phone"])));
        $address = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["address"])));
        $postal_code = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["postal_code"])));
        $city = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["city"])));
        $state = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["state"])));
        $country = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["country"])));
        $kyc_mode = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["kyc_mode"])));
        $kyc_id = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["kyc_id"])));
        $id_selfie_url = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["id_selfie_url"])));
        $callback_url = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["callback_url"]))));

        $card_holder_ref = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["card_holder_ref"]))));
        $card_ref = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["card_ref"]))));

        $card_status = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["card_status"]))));
        $amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($get_api_post_info["amount"]))));

    }
    
    //$discounted_amount = $amount;
    $type_alternative = ucwords($isp . " card");
    $reference = substr(str_shuffle("12345678901234567890"), 0, 15);
    $description = ucwords($type . " charges");
    $status = 3;

    $delete_selfie_image_if_failed = false;
    $data_type_array = array("nairacard", "dollarcard");

    //Create Card Holder Service
    if ($action_function == 0) {
        if (!empty($firstname) && !empty($lastname) && !empty($dob) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($phone) && !empty($address) && !empty($postal_code) && is_numeric($postal_code) && !empty($city) && !empty($state) && !empty($country) && in_array($country, $allowed_country_array) && !empty($kyc_mode) && in_array($kyc_mode, $allowed_kyc_array) && !empty($kyc_id)) {
            $holder_reference = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz1234567890"), 0, 20);

            $select_card_holder = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_holders WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "'");

            if (mysqli_num_rows($select_card_holder) == 0 || $purchase_method == "api") {

                if (isset($_FILES['id-selfie-image']['name']) && !empty($_FILES['id-selfie-image']['name'])) {
                    $virtual_selfie_image_file_type = strtolower(pathinfo($_FILES['id-selfie-image']['name'], PATHINFO_EXTENSION));
                    // Allowed file types
                    $virtual_selfie_image_allowed_types = ['jpg', 'jpeg', 'png'];

                    if (in_array($virtual_selfie_image_file_type, $virtual_selfie_image_allowed_types)) {
                        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/virtual-selfie/";
                        $selfie_image_name = substr(str_shuffle("12345678901234567890"), 0, 15) . "." . $virtual_selfie_image_file_type;
                        $virtual_selfie_image_path = $uploadDir . $selfie_image_name;
                        if (move_uploaded_file($_FILES['id-selfie-image']['tmp_name'], $virtual_selfie_image_path)) {
                            $id_selfie_url = $web_http_host . "/virtual-selfie/" . $selfie_image_name;
                        }
                    }
                }

                $create_card_holder = mysqli_query($connection_server, "INSERT INTO sas_virtualcard_holders (vendor_id, holder_id, username, firstname, lastname, dob, email, phone_number, `address`, `city`, `state`, `country`, zipcode, kyc_mode, kyc_id, selfie_id_url, api_website, card_holder_status) VALUES ('" . $get_logged_user_details["vendor_id"] . "', '$holder_reference', '" . $get_logged_user_details["username"] . "', '$firstname', '$lastname', '$dob', '$email', '$phone', '$address', '$city', '$state', '$country', '$postal_code', '$kyc_mode', '$kyc_id', '$id_selfie_url', '" . $api_detail["api_base_url"] . "', 'active')");

                if ($create_card_holder) {
                    // Card Holder Created Successfully
                    $json_response_array = array("status" => "success", "desc" => "Card Holder Created Successfully");
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    //Error: Unable to create card holder
                    $json_response_array = array("status" => "failed", "desc" => "Error: Unable to create card holder");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Error: You can only create one card holder
                $json_response_array = array("status" => "failed", "desc" => "Error: You can only create one card holder");
                $json_response_encode = json_encode($json_response_array, true);
            }
        } else {
            //Incomplete Parameters
            $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters Or Invalid Selfie Image");
            $json_response_encode = json_encode($json_response_array, true);
        }
    } elseif ($action_function !== 0 && in_array($type, $data_type_array)) {
        //Create Card Service
        if ($action_function == 1) {
            if (!empty(userBalance(1)) && is_numeric(userBalance(1)) && (userBalance(1) > 0)) {

                if (!empty($isp) && !empty($qty_number) && is_numeric($qty_number) && ($qty_number >= 1) && !empty($type) && !empty($quantity) && !empty($card_holder_ref)) {


                    $select_card_holder = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_holders WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "' && holder_id = '$card_holder_ref'");
                    if (mysqli_num_rows($select_card_holder) == 1) {
                        $get_card_holder_detail = mysqli_fetch_array($select_card_holder);

                        $data_type_table_name_arrays = array("nairacard" => "sas_nairacard_status", "dollarcard" => "sas_dollarcard_status");
                        $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM " . $data_type_table_name_arrays[$type] . " WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='$isp'"));
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='" . $type . "'");
                        $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='" . $type . "' && status='1'");

                        if (mysqli_num_rows($get_api_lists) > 0) {
                            if (mysqli_num_rows($get_api_enabled_lists) == 1) {
                                while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                    if (!empty($api_detail["api_key"])) {
                                        if ($api_detail["status"] == 1) {
                                            $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                                            $data_type_table_name_arrays = array("nairacard" => "sas_nairacard_status", "dollarcard" => "sas_dollarcard_status");
                                            if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                                                $dollar_exchange_rate_table = mysqli_query($connection_server, "SELECT * FROM sas_dollar_exchange_rates WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_type='$type' && currency='ngn'");
                                                if (mysqli_num_rows($dollar_exchange_rate_table) == 1) {
                                                    $exchange_rate = mysqli_fetch_array($dollar_exchange_rate_table);

                                                    $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                                                    $data_type_table_name = $data_type_table_name_arrays[$type];
                                                    $product_name = strtolower($isp);
                                                    $product_status_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $data_type_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                                    $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                                    $product_discount_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_id='" . $api_detail["id"] . "' && product_id='" . $product_table["id"] . "' && val_1='" . $quantity . "' LIMIT 1"));
                                                    $amount = (($exchange_rate["debit_amount"] * $product_discount_table["val_2"]) * $qty_number);
                                                    $discounted_amount = $amount;
                                                }
                                            }
                                            if (!empty(trim($product_discount_table["val_1"])) && !empty(trim($product_discount_table["val_2"])) && is_numeric($product_discount_table["val_2"])) {
                                                if ((userBalance(1) >= $amount) && !empty($amount) && is_numeric($amount)) {
                                                    if (($product_table["status"] == 1) && ($product_status_table["status"] == 1)) {

                                                        $debit_user = chargeUser("debit", $qty_number, $type_alternative, $reference, "", $amount, $discounted_amount, $description, $purchase_method, $_SERVER["HTTP_HOST"], $status);
                                                        if ($debit_user === "success") {
                                                            $api_gateway_name_file_exists = $type . "-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                                                $api_gateway_name = $type . "-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                            } else {
                                                                $api_gateway_name = $type . "-localserver.php";
                                                            }

                                                            // Reset variables at the start of each transaction
                                                            $api_response = null;
                                                            $api_response_description = null;
                                                            $api_response_reference = null;
                                                            $api_response_text = null;
                                                            $api_response_status = null;

                                                            include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                                            $api_response_text = strtolower($api_response_text);
                                                            $users_card_purchased = trim($users_card_purchased);
                                                            if (in_array($api_response, array("successful"))) {
                                                                alterTransaction($reference, "status", $api_response_status);
                                                                alterTransaction($reference, "api_id", $api_detail["id"]);
                                                                alterTransaction($reference, "product_id", $product_table["id"]);
                                                                alterTransaction($reference, "api_reference", $api_response_reference);
                                                                alterTransaction($reference, "description", $api_response_description);
                                                                alterTransaction($reference, "api_website", $api_detail["api_base_url"]);
                                                                $json_response_array = array("ref" => $reference, "status" => "success", "desc" => "Transaction Successful", "response_desc" => $api_response_description, "cards" => $users_card_purchased);
                                                                $json_response_encode = json_encode($json_response_array, true);
                                                            }

                                                            if (in_array($api_response, array("pending"))) {
                                                                alterTransaction($reference, "status", $api_response_status);
                                                                alterTransaction($reference, "api_id", $api_detail["id"]);
                                                                alterTransaction($reference, "product_id", $product_table["id"]);
                                                                alterTransaction($reference, "api_reference", $api_response_reference);
                                                                alterTransaction($reference, "description", $api_response_description);
                                                                alterTransaction($reference, "api_website", $api_detail["api_base_url"]);
                                                                $json_response_array = array("ref" => $reference, "status" => "pending", "desc" => "Transaction Pending", "response_desc" => $api_response_description, "cards" => $users_card_purchased);
                                                                $json_response_encode = json_encode($json_response_array, true);
                                                            }

                                                            if ($api_response == "failed") {
                                                                $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                                                alterTransaction($reference, "api_id", $api_detail["id"]);
                                                                alterTransaction($reference, "product_id", $product_table["id"]);
                                                                alterTransaction($reference, "api_reference", $api_response_reference);
                                                                alterTransaction($reference, "description", $api_response_description);
                                                                chargeUser("credit", $qty_number, "Refund", $reference_2, "", $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $purchase_method, $_SERVER["HTTP_HOST"], "1");
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
                                                                $json_response_array = array("status" => "failed", "desc" => "Transaction Failed");
                                                                $json_response_encode = json_encode($json_response_array, true);
                                                            }
                                                        } else {
                                                            //Unable to proceed with charges
                                                            $json_response_array = array("status" => "failed", "desc" => "Unable to proceed with charges");
                                                            $json_response_encode = json_encode($json_response_array, true);
                                                        }
                                                    } else {
                                                        //Product Locked
                                                        $json_response_array = array("status" => "failed", "desc" => "Product Locked");
                                                        $json_response_encode = json_encode($json_response_array, true);
                                                    }
                                                } else {
                                                    //Insufficient Wallet Balance
                                                    $json_response_array = array("status" => "failed", "desc" => "Insufficient Wallet Balance");
                                                    $json_response_encode = json_encode($json_response_array, true);
                                                }
                                            } else {
                                                //Card size not available
                                                $json_response_array = array("status" => "failed", "desc" => "Card size not available");
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
                                }
                            } else {
                                if (mysqli_num_rows($get_api_enabled_lists) > 1) {
                                    //More than 1 gateway enabled (System is unavailable, try again later)
                                    $json_response_array = array("status" => "failed", "desc" => "System is unavailable, try again later");
                                    $json_response_encode = json_encode($json_response_array, true);
                                } else {
                                    if (mysqli_num_rows($get_api_enabled_lists) < 1) {
                                        //Gateway Disabled (Product Not Available)
                                        $json_response_array = array("status" => "failed", "desc" => "Product Not Available");
                                        $json_response_encode = json_encode($json_response_array, true);
                                    }
                                }
                            }
                        } else {
                            //No API Installed
                            $json_response_array = array("status" => "failed", "desc" => "Gateway Error");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    } else {
                        //Error: Card Holder Needs to be created first
                        $json_response_array = array("status" => "failed", "desc" => "Error: Card Holder Needs to be created first");
                        $json_response_encode = json_encode($json_response_array, true);
                    }

                } else {
                    //Incomplete Parameters
                    $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters Or Invalid Selfie Image");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Balance is LOW
                $json_response_array = array("status" => "failed", "desc" => "Balance is LOW");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

        //Change Card PIN Service
        if ($action_function == 2) {

            if (!empty($card_ref)) {

                $select_virtual_card = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $_SESSION["user_session"] . "' && reference='$card_ref'");
                if (mysqli_num_rows($select_virtual_card) == 1) {
                    while ($virtual_card_detail = mysqli_fetch_array($select_virtual_card)) {

                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_base_url='" . $virtual_card_detail["api_website"] . "' && api_type='" . $type . "'");
                        $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_base_url='" . $virtual_card_detail["api_website"] . "' && api_type='" . $type . "' && status='1'");

                        if (mysqli_num_rows($get_api_lists) > 0) {
                            if (mysqli_num_rows($get_api_enabled_lists) == 1) {
                                while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                    if (!empty($api_detail["api_key"])) {
                                        if ($api_detail["status"] == 1) {

                                            $api_gateway_name_file_exists = $type . "-pin-update-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                                $api_gateway_name = $type . "-pin-update-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                            } else {
                                                $api_gateway_name = $type . "-pin-update-localserver.php";
                                            }

                                            // Reset variables at the start of each transaction
                                            $api_response = null;
                                            $api_response_description = null;
                                            $api_response_reference = null;
                                            $api_response_text = null;
                                            $api_response_status = null;

                                            include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                            $api_response_text = strtolower($api_response_text);
                                            $users_card_purchased = trim($users_card_purchased);
                                            if (in_array($api_response, array("successful"))) {

                                                $json_response_array = array("status" => "success", "desc" => "Pin Updated Successfully");
                                                $json_response_encode = json_encode($json_response_array, true);
                                            }

                                            if ($api_response == "failed") {

                                                $json_response_array = array("status" => "failed", "desc" => "Pin Update Failed");
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
                                }
                            } else {
                                if (mysqli_num_rows($get_api_enabled_lists) > 1) {
                                    //More than 1 gateway enabled (System is unavailable, try again later)
                                    $json_response_array = array("status" => "failed", "desc" => "System is unavailable, try again later");
                                    $json_response_encode = json_encode($json_response_array, true);
                                } else {
                                    if (mysqli_num_rows($get_api_enabled_lists) < 1) {
                                        //Gateway Disabled (Product Not Available)
                                        $json_response_array = array("status" => "failed", "desc" => "Product Not Available");
                                        $json_response_encode = json_encode($json_response_array, true);
                                    }
                                }
                            }
                        } else {
                            //No API Installed
                            $json_response_array = array("status" => "failed", "desc" => "Gateway Error");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    }
                } else {
                    //Virtual Card not Exists
                    $json_response_array = array("status" => "failed", "desc" => "Virtual Card not Exists");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters Or Reference/Pin");
                $json_response_encode = json_encode($json_response_array, true);
            }

        }

        //Change Card Status Service
        if ($action_function == 3) {

            if (!empty($card_ref) && !empty($card_status) && in_array($card_status, array("active", "blocked"))) {

                $select_virtual_card = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $_SESSION["user_session"] . "' && reference='$card_ref'");
                if (mysqli_num_rows($select_virtual_card) == 1) {
                    while ($virtual_card_detail = mysqli_fetch_array($select_virtual_card)) {

                        if ($virtual_card_detail["card_status"] !== $card_status) {
                            $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_base_url='" . $virtual_card_detail["api_website"] . "' && api_type='" . $type . "'");
                            $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_base_url='" . $virtual_card_detail["api_website"] . "' && api_type='" . $type . "' && status='1'");

                            if (mysqli_num_rows($get_api_lists) > 0) {
                                if (mysqli_num_rows($get_api_enabled_lists) == 1) {
                                    while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                        if (!empty($api_detail["api_key"])) {
                                            if ($api_detail["status"] == 1) {

                                                $api_gateway_name_file_exists = $type . "-pin-update-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                                    $api_gateway_name = $type . "-status-update-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                } else {
                                                    $api_gateway_name = $type . "-status-update-localserver.php";
                                                }

                                                // Reset variables at the start of each transaction
                                                $api_response = null;
                                                $api_response_description = null;
                                                $api_response_reference = null;
                                                $api_response_text = null;
                                                $api_response_status = null;

                                                include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                                $api_response_text = strtolower($api_response_text);
                                                $users_card_purchased = trim($users_card_purchased);
                                                if (in_array($api_response, array("successful"))) {

                                                    $json_response_array = array("status" => "success", "desc" => "Status Updated Successfully");
                                                    $json_response_encode = json_encode($json_response_array, true);
                                                }

                                                if ($api_response == "failed") {

                                                    $json_response_array = array("status" => "failed", "desc" => "Status Update Failed");
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
                                    }
                                } else {
                                    if (mysqli_num_rows($get_api_enabled_lists) > 1) {
                                        //More than 1 gateway enabled (System is unavailable, try again later)
                                        $json_response_array = array("status" => "failed", "desc" => "System is unavailable, try again later");
                                        $json_response_encode = json_encode($json_response_array, true);
                                    } else {
                                        if (mysqli_num_rows($get_api_enabled_lists) < 1) {
                                            //Gateway Disabled (Product Not Available)
                                            $json_response_array = array("status" => "failed", "desc" => "Product Not Available");
                                            $json_response_encode = json_encode($json_response_array, true);
                                        }
                                    }
                                }
                            } else {
                                //No API Installed
                                $json_response_array = array("status" => "failed", "desc" => "Gateway Error");
                                $json_response_encode = json_encode($json_response_array, true);
                            }
                        } else {
                            //Already Active/Blocked
                            $json_response_array = array("status" => "failed", "desc" => "Card is " . ucwords($card_status) . " Already");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    }
                } else {
                    //Virtual Card not Exists
                    $json_response_array = array("status" => "failed", "desc" => "Virtual Card not Exists");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters Or Reference/Status");
                $json_response_encode = json_encode($json_response_array, true);
            }

        }

        //Card Funding Service
        if ($action_function == 4) {

            if (!empty($card_ref) && !empty($isp) && !empty($type) && !empty($amount) && is_numeric($amount)) {

                $select_virtual_card = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $_SESSION["user_session"] . "' && reference='$card_ref'");
                if (mysqli_num_rows($select_virtual_card) == 1) {
                    while ($virtual_card_detail = mysqli_fetch_array($select_virtual_card)) {

                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_base_url='" . $virtual_card_detail["api_website"] . "' && api_type='" . $type . "'");
                        $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_base_url='" . $virtual_card_detail["api_website"] . "' && api_type='" . $type . "' && status='1'");

                        if (mysqli_num_rows($get_api_lists) > 0) {
                            if (mysqli_num_rows($get_api_enabled_lists) == 1) {
                                while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                    if (!empty($api_detail["api_key"])) {
                                        if ($api_detail["status"] == 1) {

                                            $account_level_table_name_arrays = array(1 => "sas_smart_card_funding_parameter_values", 2 => "sas_agent_card_funding_parameter_values", 3 => "sas_api_card_funding_parameter_values");
                                            $data_type_table_name_arrays = array("nairacard" => "sas_nairacard_status", "dollarcard" => "sas_dollarcard_status");
                                            if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                                                $dollar_exchange_rate_table = mysqli_query($connection_server, "SELECT * FROM sas_dollar_exchange_rates WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_type='$type' && currency='ngn'");
                                                if (mysqli_num_rows($dollar_exchange_rate_table) == 1) {
                                                    $exchange_rate = mysqli_fetch_array($dollar_exchange_rate_table);

                                                    $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                                                    $data_type_table_name = $data_type_table_name_arrays[$type];
                                                    $product_name = strtolower($isp);
                                                    $product_status_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $data_type_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                                    $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                                    $product_discount_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_id='" . $api_detail["id"] . "' && product_id='" . $product_table["id"] . "' && val_1='1' LIMIT 1"));
                                                    $amount = ($product_discount_table["val_2"] + ($exchange_rate["credit_amount"] * $product_discount_table["val_2"]));
                                                    $discounted_amount = $amount;
                                                }
                                            }

                                            if (!empty(trim($product_discount_table["val_1"])) && !empty(trim($product_discount_table["val_2"])) && is_numeric($product_discount_table["val_2"])) {
                                                if ((userBalance(1) >= $amount) && !empty($amount) && is_numeric($amount)) {
                                                    if (($product_table["status"] == 1) && ($product_status_table["status"] == 1)) {

                                                        $debit_user = chargeUser("debit", $card_ref, $type_alternative, $reference, "", $amount, $discounted_amount, $description, $purchase_method, $_SERVER["HTTP_HOST"], $status);
                                                        if ($debit_user === "success") {
                                                            $api_gateway_name_file_exists = $type . "-funding-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                                                $api_gateway_name = $type . "-funding-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                            } else {
                                                                $api_gateway_name = $type . "-funding-localserver.php";
                                                            }

                                                            // Reset variables at the start of each transaction
                                                            $api_response = null;
                                                            $api_response_description = null;
                                                            $api_response_reference = null;
                                                            $api_response_text = null;
                                                            $api_response_status = null;

                                                            include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                                            $api_response_text = strtolower($api_response_text);
                                                            $users_card_purchased = trim($users_card_purchased);
                                                            if (in_array($api_response, array("successful"))) {
                                                                alterTransaction($reference, "status", $api_response_status);
                                                                // alterTransaction($reference, "api_id", $api_detail["id"]);
                                                                // alterTransaction($reference, "product_id", $product_table["id"]);
                                                                alterTransaction($reference, "api_reference", $api_response_reference);
                                                                alterTransaction($reference, "description", $api_response_description);
                                                                alterTransaction($reference, "api_website", $api_detail["api_base_url"]);
                                                                $json_response_array = array("ref" => $reference, "status" => "success", "desc" => "Card Funded Successful");
                                                                $json_response_encode = json_encode($json_response_array, true);
                                                            }

                                                            if ($api_response == "failed") {
                                                                $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                                                // alterTransaction($reference, "api_id", $api_detail["id"]);
                                                                // alterTransaction($reference, "product_id", $product_table["id"]);
                                                                alterTransaction($reference, "api_reference", $api_response_reference);
                                                                alterTransaction($reference, "description", $api_response_description);
                                                                chargeUser("credit", $qty_number, "Refund", $reference_2, "", $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $purchase_method, $_SERVER["HTTP_HOST"], "1");
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
                                                                $json_response_array = array("status" => "failed", "desc" => "Card Funding Failed");
                                                                $json_response_encode = json_encode($json_response_array, true);
                                                            }
                                                        } else {
                                                            //Unable to proceed with charges
                                                            $json_response_array = array("status" => "failed", "desc" => "Unable to proceed with charges");
                                                            $json_response_encode = json_encode($json_response_array, true);
                                                        }
                                                    } else {
                                                        //Product Locked
                                                        $json_response_array = array("status" => "failed", "desc" => "Product Locked");
                                                        $json_response_encode = json_encode($json_response_array, true);
                                                    }
                                                } else {
                                                    //Insufficient Wallet Balance
                                                    $json_response_array = array("status" => "failed", "desc" => "Insufficient Wallet Balance");
                                                    $json_response_encode = json_encode($json_response_array, true);
                                                }
                                            } else {
                                                //Card size not available
                                                $json_response_array = array("status" => "failed", "desc" => "Card size not available");
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
                                }
                            } else {
                                if (mysqli_num_rows($get_api_enabled_lists) > 1) {
                                    //More than 1 gateway enabled (System is unavailable, try again later)
                                    $json_response_array = array("status" => "failed", "desc" => "System is unavailable, try again later");
                                    $json_response_encode = json_encode($json_response_array, true);
                                } else {
                                    if (mysqli_num_rows($get_api_enabled_lists) < 1) {
                                        //Gateway Disabled (Product Not Available)
                                        $json_response_array = array("status" => "failed", "desc" => "Product Not Available");
                                        $json_response_encode = json_encode($json_response_array, true);
                                    }
                                }
                            }
                        } else {
                            //No API Installed
                            $json_response_array = array("status" => "failed", "desc" => "Gateway Error");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    }
                } else {
                    //Virtual Card not Exists
                    $json_response_array = array("status" => "failed", "desc" => "Virtual Card not Exists");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters Or Reference/Type/Amount");
                $json_response_encode = json_encode($json_response_array, true);
            }

        }

        //Card Withdrawal Service
        if ($action_function == 5) {

            if (!empty($card_ref) && !empty($isp) && !empty($type) && !empty($amount) && is_numeric($amount)) {

                $select_virtual_card = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $_SESSION["user_session"] . "' && reference='$card_ref'");
                if (mysqli_num_rows($select_virtual_card) == 1) {
                    while ($virtual_card_detail = mysqli_fetch_array($select_virtual_card)) {

                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_base_url='" . $virtual_card_detail["api_website"] . "' && api_type='" . $type . "'");
                        $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_base_url='" . $virtual_card_detail["api_website"] . "' && api_type='" . $type . "' && status='1'");

                        if (mysqli_num_rows($get_api_lists) > 0) {
                            if (mysqli_num_rows($get_api_enabled_lists) == 1) {
                                while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                    if (!empty($api_detail["api_key"])) {
                                        if ($api_detail["status"] == 1) {

                                            $account_level_table_name_arrays = array(1 => "sas_smart_card_funding_parameter_values", 2 => "sas_agent_card_funding_parameter_values", 3 => "sas_api_card_funding_parameter_values");
                                            $data_type_table_name_arrays = array("nairacard" => "sas_nairacard_status", "dollarcard" => "sas_dollarcard_status");
                                            if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                                                $dollar_exchange_rate_table = mysqli_query($connection_server, "SELECT * FROM sas_dollar_exchange_rates WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_type='$type' && currency='ngn'");
                                                if (mysqli_num_rows($dollar_exchange_rate_table) == 1) {
                                                    $exchange_rate = mysqli_fetch_array($dollar_exchange_rate_table);

                                                    $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                                                    $data_type_table_name = $data_type_table_name_arrays[$type];
                                                    $product_name = strtolower($isp);
                                                    $product_status_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $data_type_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                                    $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                                    $product_discount_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_id='" . $api_detail["id"] . "' && product_id='" . $product_table["id"] . "' && val_1='1' LIMIT 1"));
                                                    $amount = ($product_discount_table["val_2"] + ($exchange_rate["debit_amount"] * $product_discount_table["val_2"]));
                                                    $discounted_amount = $amount;
                                                }
                                            }

                                            if (!empty(trim($product_discount_table["val_1"])) && !empty(trim($product_discount_table["val_2"])) && is_numeric($product_discount_table["val_2"])) {
                                                if ((userBalance(1) >= $amount) && !empty($amount) && is_numeric($amount)) {
                                                    if (($product_table["status"] == 1) && ($product_status_table["status"] == 1)) {

                                                        $api_gateway_name_file_exists = $type . "-withdrawal-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                                            $api_gateway_name = $type . "-withdrawal-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                        } else {
                                                            $api_gateway_name = $type . "-withdrawal-localserver.php";
                                                        }

                                                        // Reset variables at the start of each transaction
                                                        $api_response = null;
                                                        $api_response_description = null;
                                                        $api_response_reference = null;
                                                        $api_response_text = null;
                                                        $api_response_status = null;

                                                        include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                                        $api_response_text = strtolower($api_response_text);
                                                        $users_card_purchased = trim($users_card_purchased);
                                                        if (in_array($api_response, array("successful"))) {

                                                            $credit_user = chargeUser("credit", $card_ref, $type_alternative, $reference, "", $amount, $discounted_amount, $description, $purchase_method, $_SERVER["HTTP_HOST"], $status);
                                                            if ($credit_user === "success") {
                                                                alterTransaction($reference, "status", $api_response_status);
                                                                // alterTransaction($reference, "api_id", $api_detail["id"]);
                                                                // alterTransaction($reference, "product_id", $product_table["id"]);
                                                                alterTransaction($reference, "api_reference", $api_response_reference);
                                                                alterTransaction($reference, "description", $api_response_description);
                                                                alterTransaction($reference, "api_website", $api_detail["api_base_url"]);

                                                                $json_response_array = array("ref" => $reference, "status" => "success", "desc" => "Card Withdrawal Successful");
                                                                $json_response_encode = json_encode($json_response_array, true);
                                                            } else {
                                                                //Unable to proceed with charges
                                                                $json_response_array = array("status" => "failed", "desc" => "Card Withdrawal Successful, But System Unable to credit account, contact admin for assistance");
                                                                $json_response_encode = json_encode($json_response_array, true);
                                                                $delete_selfie_image_if_failed = true;
                                                            }

                                                        }

                                                        if ($api_response == "failed") {
                                                            $json_response_array = array("status" => "failed", "desc" => "Card Withdrawal Failed");
                                                            $json_response_encode = json_encode($json_response_array, true);
                                                        }
                                                    } else {
                                                        //Product Locked
                                                        $json_response_array = array("status" => "failed", "desc" => "Product Locked");
                                                        $json_response_encode = json_encode($json_response_array, true);
                                                    }
                                                } else {
                                                    //Insufficient Wallet Balance
                                                    $json_response_array = array("status" => "failed", "desc" => "Insufficient Wallet Balance");
                                                    $json_response_encode = json_encode($json_response_array, true);
                                                }
                                            } else {
                                                //Card size not available
                                                $json_response_array = array("status" => "failed", "desc" => "Card size not available");
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
                                }
                            } else {
                                if (mysqli_num_rows($get_api_enabled_lists) > 1) {
                                    //More than 1 gateway enabled (System is unavailable, try again later)
                                    $json_response_array = array("status" => "failed", "desc" => "System is unavailable, try again later");
                                    $json_response_encode = json_encode($json_response_array, true);
                                } else {
                                    if (mysqli_num_rows($get_api_enabled_lists) < 1) {
                                        //Gateway Disabled (Product Not Available)
                                        $json_response_array = array("status" => "failed", "desc" => "Product Not Available");
                                        $json_response_encode = json_encode($json_response_array, true);
                                    }
                                }
                            }
                        } else {
                            //No API Installed
                            $json_response_array = array("status" => "failed", "desc" => "Gateway Error");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    }
                } else {
                    //Virtual Card not Exists
                    $json_response_array = array("status" => "failed", "desc" => "Virtual Card not Exists");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters Or Reference/Type/Amount");
                $json_response_encode = json_encode($json_response_array, true);
            }

        }

        //Card Callback Url Service
        if ($action_function == 6) {

            if (!empty($card_ref) && !empty($isp) && !empty($callback_url)) {
                if (filter_var($callback_url, FILTER_VALIDATE_URL)) {
                    $select_virtual_card = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $_SESSION["user_session"] . "' && reference='$card_ref'");
                    if (mysqli_num_rows($select_virtual_card) == 1) {
                        $virtual_card_detail = mysqli_fetch_array($select_virtual_card);
                        mysqli_query($connection_server, "UPDATE sas_virtualcard_purchaseds SET callback_url = '$callback_url' WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $_SESSION["user_session"] . "' && reference='$card_ref'");
                        //Callback Url Updated
                        $json_response_array = array("status" => "failed", "desc" => "Callback Url Updated");
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        //Virtual Card not Exists
                        $json_response_array = array("status" => "failed", "desc" => "Virtual Card not Exists");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    //Invalid Callback URL
                    $json_response_array = array("status" => "failed", "desc" => "Invalid Callback URL");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters Or Reference");
                $json_response_encode = json_encode($json_response_array, true);
            }

        }


    } elseif ($action_function == 7) {
        //View Card Service
        if ($action_function == 7) {

            if (!empty($card_ref)) {
                $select_virtual_card = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $_SESSION["user_session"] . "' && reference='$card_ref'");
            } else {
                $select_virtual_card = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $_SESSION["user_session"] . "'");
            }
            if (mysqli_num_rows($select_virtual_card) >= 1) {
                $cards_array = array();
                while ($virtual_card_detail = mysqli_fetch_assoc($select_virtual_card)) {
                    $card_array = array();
                    $card_array["reference"] = $virtual_card_detail["reference"];
                    $card_array["type"] = $virtual_card_detail["card_type"];
                    $card_array["brand"] = $virtual_card_detail["card_brand"];
                    $card_array["name"] = $virtual_card_detail["fullname"];
                    $card_array["balance"] = toDecimal($virtual_card_detail["card_balance"], 2);
                    $card_array["currency"] = $virtual_card_detail["card_currency"];
                    $card_array["card_number"] = wordwrap($virtual_card_detail["card_number"], 4, ' ', true);
                    $card_array["validity"] = $virtual_card_detail["card_validity"];
                    $card_array["cvv"] = $virtual_card_detail["card_cvv"];
                    $card_array["pin"] = $virtual_card_detail["card_pin"];
                    $card_array["address"] = $virtual_card_detail["card_address"];
                    $card_array["state"] = $virtual_card_detail["card_state"];
                    $card_array["country"] = $virtual_card_detail["card_country"];
                    $card_array["zipcode"] = $virtual_card_detail["card_zipcode"];
                    $card_array["callback_url"] = $virtual_card_detail["card_callback_url"];
                    $card_array["status"] = $virtual_card_detail["card_status"];

                    //Push to the cards array
                    array_push($cards_array, $card_array);
                }
                //Cards Retrieved Successfully
                $json_response_array = array("status" => "success", "desc" => "Cards Retrieved Successfully", "cards" => $cards_array);
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                //Virtual Card not Exists
                $json_response_array = array("status" => "failed", "desc" => "Virtual Card not Exists");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

    } else {
        //Invalid Card type
        $json_response_array = array("status" => "failed", "desc" => "Invalid Card type");
        $json_response_encode = json_encode($json_response_array, true);
        $delete_selfie_image_if_failed = true;
    }
} else {
    //Purchase Method Not specified
    $json_response_array = array("status" => "failed", "desc" => "Purchase Method Not specified");
    $json_response_encode = json_encode($json_response_array, true);
    $delete_selfie_image_if_failed = true;
}
?>