<?php

$purchase_method = strtoupper($purchase_method ?? "");
$purchase_method_array = array("API", "WEB", "APP");
$json_response_encode = "";
$allowed_country_array = array("nigeria", "usa", "gb");


if (in_array($purchase_method, $purchase_method_array)) {
    if ($purchase_method === "WEB") {
        //Customer/Wallet Creation

        $firstname = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["firstname"] ?? $_POST["firstname"])));
        $lastname = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["lastname"] ?? $_POST["lastname"])));
        $email = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["email"] ?? $_POST["email"])));
        $phone = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["phone_number"] ?? $_POST["phone"])));
        $address = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["address"] ?? $_POST["address"])));
        $postal_code = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["zipcode"] ?? $_POST["postal-code"])));
        $city = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["city"] ?? $_POST["city"])));
        $state = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["state"] ?? $_POST["state"])));
        $country = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["country"] ?? $_POST["country"])));

        //Customer ref
        $customer_ref = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["customer_id"])));

        $currency = mysqli_real_escape_string($connection_server, trim(strip_tags($currency ?? $_POST["currency"])));
        $chain = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["chain"]))));
        $label = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["label"]))));

        $amount = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["amount"]))));
        $beneficiary_id = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["beneficiary_id"]))));

        $source_currency = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["source-currency"])));
        $target_currency = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["target-currency"])));

        $swap_id = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["swap-id"]))));

        $desc = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["desc"]))));

    }

    if (in_array($purchase_method, array("API", "APP"))) {
        //Customer/Wallet Creation
        $firstname = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["firstname"])));
        $lastname = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["lastname"])));
        $email = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["email"])));
        $phone = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["phone"])));
        $address = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["address"])));
        $postal_code = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["postal_code"])));
        $city = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["city"])));
        $state = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["state"])));
        $country = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["country"])));

        //Customer ref
        $customer_ref = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["customer_ref"]))));

        $currency = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["currency"]))));
        $chain = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["chain"]))));
        $label = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["label"]))));

        $amount = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["amount"]))));
        $beneficiary_id = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["beneficiary_id"]))));

        $source_currency = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["source-currency"]))));
        $target_currency = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["target-currency"]))));

        $swap_id = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["swap-id"]))));

        $desc = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["desc"]))));

    }
    //$discounted_amount = $amount;
    $type_alternative = ucwords($currency . " crypto");
    $reference = substr(str_shuffle("12345678901234567890"), 0, 15);
    $description = "Crypto Charges";
    $status = 3;

    $crypto_type_array = array("ngn", "usd", "gbp", "cad", "eur", "btc", "eth", "doge", "usdt", "usdc", "sol", "ada", "trx");
    if ($action_function == 0 || $action_function == 5 || $action_function == 6 || $action_function == 7 || $action_function == 8 || in_array($currency, $crypto_type_array)) {

        //Create Card Holder Service
        if ($action_function == 0) {
            if (!empty($firstname) && !empty($lastname) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($phone) && !empty($address) && !empty($postal_code) && is_numeric($postal_code) && !empty($city) && !empty($state) && !empty($country) && in_array($country, $allowed_country_array)) {
                $holder_reference = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz1234567890"), 0, 20);

                $select_customer_holder = mysqli_query($connection_server, "SELECT * FROM sas_crypto_customer_holders WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "'");

                if (mysqli_num_rows($select_customer_holder) == 0 || $purchase_method == "api") {
                    $create_customer_holder = mysqli_query($connection_server, "INSERT INTO sas_crypto_customer_holders (vendor_id, customer_id, username, firstname, lastname, email, phone_number, `address`, `city`, `state`, `country`, zipcode, api_website, customer_status) VALUES ('" . $get_logged_user_details["vendor_id"] . "', '$holder_reference', '" . $get_logged_user_details["username"] . "', '$firstname', '$lastname', '$email', '$phone', '$address', '$city', '$state', '$country', '$postal_code', '" . $api_detail["api_base_url"] . "', 'active')");

                    if ($create_customer_holder) {
                        // Card Holder Created Successfully
                        $json_response_array = array("status" => "success", "desc" => "KYC Created Successfully");
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        //Error: Unable to create customer holder
                        $json_response_array = array("status" => "failed", "desc" => "Error: Unable to update kyc");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    //Error: You can only create one customer holder
                    $json_response_array = array("status" => "failed", "desc" => "Error: You can only create one customer kyc");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

        //Wallet Creation Service
        if ($action_function == 1) {
            if (!empty($firstname) && !empty($lastname) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($phone) && is_numeric($phone) && strlen($phone) == 11 && !empty($currency) && !empty($customer_ref)) {
                $select_customer_holder = mysqli_query($connection_server, "SELECT * FROM sas_crypto_customer_holders WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "' && customer_id = '$customer_ref'");
                if (mysqli_num_rows($select_customer_holder) == 1) {
                    $get_customer_holder_detail = mysqli_fetch_array($select_customer_holder);

                    $crypto_type_table_name_arrays = array("ngn" => "sas_crypto_status", "usd" => "sas_crypto_status", "gbp" => "sas_crypto_status", "cad" => "sas_crypto_status", "eur" => "sas_crypto_status", "btc" => "sas_crypto_status", "eth" => "sas_crypto_status", "doge" => "sas_crypto_status", "usdt" => "sas_crypto_status", "usdc" => "sas_crypto_status", "sol" => "sas_crypto_status", "ada" => "sas_crypto_status", "trx" => "sas_crypto_status");
                    $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM " . $crypto_type_table_name_arrays[$currency] . " WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='$currency'"));
                    $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='crypto'");
                    $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='crypto' && status='1'");

                    if (mysqli_num_rows($get_api_lists) > 0) {
                        if (mysqli_num_rows($get_api_enabled_lists) == 1) {
                            while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                if (!empty($api_detail["api_key"])) {
                                    if ($api_detail["status"] == 1) {
                                        $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                                        if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                                            $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                                            $crypto_type_table_name = $crypto_type_table_name_arrays[$currency];
                                            $product_name = strtolower($currency);
                                            $product_status_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $crypto_type_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                            $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                            $product_discount_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_id='" . $api_detail["id"] . "' && product_id='" . $product_table["id"] . "' LIMIT 1"));
                                        }
                                        if (($product_table["status"] == 1) && ($product_status_table["status"] == 1)) {
                                            $api_gateway_name_file_exists = "crypto-wallet-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                                $api_gateway_name = "crypto-wallet-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                            } else {
                                                $api_gateway_name = "crypto-wallet-localserver.php";
                                            }

                                            // Reset variables at the start of each transaction
                                            $api_response = null;
                                            $api_response_description = null;
                                            $api_response_reference = null;
                                            $api_response_text = null;
                                            $api_response_status = null;

                                            include($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                            $api_response_text = strtolower($api_response_text);

                                            if (in_array($api_response, array("successful"))) {
                                                $json_response_array = array("status" => "success", "desc" => "Wallet Created Successfully");
                                                $json_response_encode = json_encode($json_response_array, true);
                                            }

                                            if (in_array($api_response, array("pending"))) {
                                                $json_response_array = array("status" => "pending", "desc" => "Wallet Creation is Pending");
                                                $json_response_encode = json_encode($json_response_array, true);
                                            }

                                            if ($api_response == "failed") {
                                                $json_response_array = array("status" => "failed", "desc" => "Wallet Creation Failed");
                                                $json_response_encode = json_encode($json_response_array, true);
                                            }
                                        } else {
                                            //Product Locked
                                            $json_response_array = array("status" => "failed", "desc" => "Product Locked");
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
                $err_msg = [];
                !empty($firstname) ? "" : $err_msg[] = "First name required";
                !empty($lastname) ? "" : $err_msg[] = "Last name required";
                !empty($email) ? "" : $err_msg[] = "Email required";
                filter_var($email, FILTER_VALIDATE_EMAIL) ? "" : $err_msg[] = "Invalid Email";
                !empty($phone) ? "" : $err_msg[] = "Phone number required";
                is_numeric($phone) ? "" : $err_msg[] = "Invalid phone number";
                strlen($phone) == 11 ? "" : $err_msg[] = "Phone must be 11 digit";
                !empty($currency) ? "" : $err_msg[] = "Currency required";
                !empty($customer_ref) ? "" : $err_msg[] = "Customer required";
                $json_response_array = array("status" => "failed", "desc" => implode(", ", $err_msg));
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

        //Wallet Retriever Service
        if ($action_function == 2) {
            if (!empty($currency) && in_array($currency, $crypto_type_array)) {
                $crypto_ledger_query = mysqli_query($connection_server, "SELECT * FROM sas_user_crypto_ledger_balance WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "' && currency = '$currency'");

                if (mysqli_num_rows($crypto_ledger_query) == 1) {
                    $get_wallet_currency_details = mysqli_fetch_array($crypto_ledger_query);

                    if (empty($get_wallet_currency_details["crypto_address"])) {
                        //Fallback: Attempt to generate/fetch address if missing
                        $select_customer_holder = mysqli_query($connection_server, "SELECT * FROM sas_crypto_customer_holders WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "' LIMIT 1");
                        if (mysqli_num_rows($select_customer_holder) == 1) {
                            $get_customer_holder_detail = mysqli_fetch_array($select_customer_holder);
                            $customer_ref = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["customer_id"])));
                            $firstname = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["firstname"])));
                            $lastname = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["lastname"])));
                            $email = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["email"])));
                            $phone = mysqli_real_escape_string($connection_server, trim(strip_tags($get_customer_holder_detail["phone_number"])));

                            // Get API Details for fallback
                            $crypto_type_table_name_arrays = array("ngn" => "sas_crypto_status", "usd" => "sas_crypto_status", "gbp" => "sas_crypto_status", "cad" => "sas_crypto_status", "eur" => "sas_crypto_status", "btc" => "sas_crypto_status", "eth" => "sas_crypto_status", "doge" => "sas_crypto_status", "usdt" => "sas_crypto_status", "usdc" => "sas_crypto_status", "sol" => "sas_crypto_status", "ada" => "sas_crypto_status", "trx" => "sas_crypto_status");
                            $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM " . $crypto_type_table_name_arrays[$currency] . " WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='$currency'"));

                            // Re-initialize $api_detail to avoid pollution from previous iterations
                            $api_detail = null;
                            $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . ($get_item_status_details["api_id"] ?? 0) . "' && api_type='crypto'");
                            if ($get_api_lists) {
                                $api_detail = mysqli_fetch_array($get_api_lists);
                            }

                            if ($api_detail) {
                                $api_gateway_name_file_exists = "crypto-wallet-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                    $api_gateway_name = "crypto-wallet-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                } else {
                                    $api_gateway_name = "crypto-wallet-localserver.php";
                                }

                                include($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);

                                //Re-fetch after potential update
                                $crypto_ledger_query = mysqli_query($connection_server, "SELECT * FROM sas_user_crypto_ledger_balance WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "' && currency = '$currency'");
                                $get_wallet_currency_details = mysqli_fetch_array($crypto_ledger_query);
                            }
                        }
                    }

                    // Currency reetrieved successfully
                    $json_response_array = array("status" => "success", "desc" => "Currency reetrieved successfully", "data" => ["wallet_id" => $get_wallet_currency_details["wallet_id"], "wallet_balance" => $get_wallet_currency_details["wallet_balance"], "ledger_balance" => $get_wallet_currency_details["wallet_balance"], "currency" => strtoupper($get_wallet_currency_details["currency"]), "payment_methods" => ["currency" => strtoupper($get_wallet_currency_details["currency"]), "address" => $get_wallet_currency_details["crypto_address"], "chain" => strtoupper($get_wallet_currency_details["crypto_chain"])]]);
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    //Error: Wallet not exits
                    $json_response_array = array("status" => "failed", "desc" => "Error: Wallet not exits");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Incomplete Parameters
                !empty($currency) ? "" : $err_msg[] = "Currency required";
                in_array($currency, $crypto_type_array) ? "" : $err_msg[] = "Invalid currency";
                $json_response_array = array("status" => "failed", "desc" => implode(", ", $err_msg));
                $json_response_encode = json_encode($json_response_array, true);
            }
        }
        //Currency Transfer Chain/Network
        if ($action_function == 3) {
            if (!empty($currency)) {
                $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                    $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                    $product_name = strtolower($currency);
                    $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                    $product_discount_table = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_id='" . $product_table["id"] . "'");
                }

                $crypto_currency_chain_array = array();
                if (mysqli_num_rows($product_discount_table) >= 1) {
                    while ($get_product_discount_table = mysqli_fetch_array($product_discount_table)) {
                        $crypto_currency_chain_array[$get_product_discount_table["val_1"]] = $get_product_discount_table["val_2"];
                    }
                }
                $json_response_array = array("status" => "success", "desc" => "Currency Chain Retrieved Successfully", "data" => array("currency" => $currency, "chain" => $crypto_currency_chain_array));
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Currency is required");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

        //Crypto Transfer Beneficiary
        if ($action_function == 4) {
            if (!empty($label) && !empty($currency) && !empty($chain) && !empty($address)) {
                $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                    $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                    $product_name = strtolower($currency);
                    $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                    $product_discount_table = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_id='" . $product_table["id"] . "' && val_1 = '" . $chain . "'");
                }

                if (mysqli_num_rows($product_discount_table) == 1) {
                    $crypto_type_table_name_arrays = array("ngn" => "sas_crypto_status", "usd" => "sas_crypto_status", "gbp" => "sas_crypto_status", "cad" => "sas_crypto_status", "eur" => "sas_crypto_status", "btc" => "sas_crypto_status", "eth" => "sas_crypto_status", "doge" => "sas_crypto_status", "usdt" => "sas_crypto_status", "usdc" => "sas_crypto_status", "sol" => "sas_crypto_status", "ada" => "sas_crypto_status", "trx" => "sas_crypto_status");
                    $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM " . $crypto_type_table_name_arrays[$currency] . " WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='$currency'"));

                    $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='crypto'");
                    if (mysqli_num_rows($get_api_lists) > 0) {
                        while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                            if (!empty($api_detail["api_key"])) {
                                if ($api_detail["status"] == 1) {

                                    $api_gateway_name_file_exists = "crypto-wallet-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                        $api_gateway_name = "crypto-beneficiary-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                    } else {
                                        $api_gateway_name = "crypto-beneficiary-localserver.php";
                                    }

                                    // Reset variables at the start of each transaction
                                    $api_response = null;
                                    $api_response_description = null;
                                    $api_response_reference = null;
                                    $api_response_text = null;
                                    $api_response_status = null;

                                    include($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                    $api_response_text = strtolower($api_response_text);

                                    if (in_array($api_response, array("successful"))) {
                                        $beneficiary_api_reference = $api_beneficiary_id;
                                        $beneficiary_reference = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz1234567890"), 0, 20);
                                        $select_crypto_beneficiary = mysqli_query($connection_server, "SELECT * FROM sas_crypto_beneficiary WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "' && ((beneficiary_id = '$label') OR (currency = '$currency' && crypto_chain = '$chain' && crypto_address = '$address'))");
                                        if (mysqli_num_rows($select_crypto_beneficiary) == 0) {
                                            $create_crypto_beneficiary = mysqli_query($connection_server, "INSERT INTO sas_crypto_beneficiary (vendor_id, api_beneficiary_id, beneficiary_id, username, label, currency, crypto_chain, crypto_address, api_website) VALUES ('" . $get_logged_user_details["vendor_id"] . "', '$beneficiary_api_reference', '$beneficiary_reference', '" . $get_logged_user_details["username"] . "', '$label', '$currency', '$chain', '$address', '')");

                                            if ($create_crypto_beneficiary) {
                                                $json_response_array = array("status" => "success", "desc" => "Beneficiary saved Successfully", "data" => array("label" => $label, "currency" => $currency, "chain" => $chain, "address" => $address));
                                                $json_response_encode = json_encode($json_response_array, true);
                                            } else {
                                                //Error: Unable to create customer holder
                                                $json_response_array = array("status" => "failed", "desc" => "Error: Unable to update kyc");
                                                $json_response_encode = json_encode($json_response_array, true);
                                            }
                                        } else {
                                            //Error: Beneficiary with same Label/Crypto destination already exists
                                            $json_response_array = array("status" => "failed", "desc" => "Error: Beneficiary with same Label/Crypto destination already exists");
                                            $json_response_encode = json_encode($json_response_array, true);
                                        }

                                    }

                                    if ($api_response == "failed") {
                                        $json_response_array = array("status" => "failed", "desc" => "Beneficiary Creation Failed");
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
                        //No API Installed
                        $json_response_array = array("status" => "failed", "desc" => "Gateway Error");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    //Mismatched Currency/Chain
                    $json_response_array = array("status" => "failed", "desc" => "Mismatched Currency/Chain");
                    $json_response_encode = json_encode($json_response_array, true);
                }

            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

        //Crypto Transfer Beneficiaries
        if ($action_function == 5) {
            $beneficiary_reference = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz1234567890"), 0, 20);
            $select_crypto_beneficiary = mysqli_query($connection_server, "SELECT * FROM sas_crypto_beneficiary WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "'");
            if (mysqli_num_rows($select_crypto_beneficiary) >= 1) {
                $crypto_beneficiary_array = array();
                while ($get_beneficiary_info = mysqli_fetch_assoc($select_crypto_beneficiary)) {
                    $each_beneficiary_array = array("beneficiary_id" => $get_beneficiary_info["beneficiary_id"], "label" => $get_beneficiary_info["label"], "currency" => $get_beneficiary_info["currency"], "chain" => $get_beneficiary_info["crypto_chain"], "address" => $get_beneficiary_info["crypto_address"]);
                    array_push($crypto_beneficiary_array, $each_beneficiary_array);
                }

                $json_response_array = array("status" => "success", "desc" => "Beneficiaries Retrieved Successfully", "data" => $crypto_beneficiary_array);
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                //Error: Beneficiary not exists
                $json_response_array = array("status" => "failed", "desc" => "Error: Beneficiary not exists");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

        //Crypto Transfer to a Beneficiary
        if ($action_function == 6) {
            if (!empty($beneficiary_id) && !empty($amount) && is_numeric($amount)) {

                $beneficiary_reference = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz1234567890"), 0, 20);
                $select_crypto_beneficiary = mysqli_query($connection_server, "SELECT * FROM sas_crypto_beneficiary WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "' && beneficiary_id='" . $beneficiary_id . "'");


                if (mysqli_num_rows($select_crypto_beneficiary) == 1) {
                    $get_beneficiary_info = mysqli_fetch_array($select_crypto_beneficiary);
                    $select_user_crypto_ledger_balance = mysqli_query($connection_server, "SELECT * FROM sas_user_crypto_ledger_balance WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "' && currency = '" . $get_beneficiary_info["currency"] . "'");
                    if (mysqli_num_rows($select_user_crypto_ledger_balance) == 1) {
                        $currency = $get_beneficiary_info["currency"];
                        $get_wallet_currency_details = mysqli_fetch_array($select_user_crypto_ledger_balance);



                        $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                        if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                            $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                            $product_name = strtolower($currency);
                            $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                            $product_discount_table = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_id='" . $product_table["id"] . "' && val_1 = '" . $get_beneficiary_info["crypto_chain"] . "'");
                        }

                        if (mysqli_num_rows($product_discount_table) == 1) {
                            $get_product_discount_details = mysqli_fetch_array($product_discount_table);
                            $crypto_type_table_name_arrays = array("ngn" => "sas_crypto_status", "usd" => "sas_crypto_status", "gbp" => "sas_crypto_status", "cad" => "sas_crypto_status", "eur" => "sas_crypto_status", "btc" => "sas_crypto_status", "eth" => "sas_crypto_status", "doge" => "sas_crypto_status", "usdt" => "sas_crypto_status", "usdc" => "sas_crypto_status", "sol" => "sas_crypto_status", "ada" => "sas_crypto_status", "trx" => "sas_crypto_status");
                            $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM " . $crypto_type_table_name_arrays[$currency] . " WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='$currency'"));

                            $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='crypto'");
                            if (mysqli_num_rows($get_api_lists) > 0) {
                                while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                    if (!empty($api_detail["api_key"])) {
                                        if ($api_detail["status"] == 1) {

                                            $api_gateway_name_file_exists = "crypto-beneficiary-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                                $api_gateway_name = "crypto-beneficiary-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                            } else {
                                                $api_gateway_name = "crypto-beneficiary-localserver.php";
                                            }

                                            // Reset variables at the start of each transaction
                                            $api_response = null;
                                            $api_response_description = null;
                                            $api_response_reference = null;
                                            $api_response_text = null;
                                            $api_response_status = null;

                                            // include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                            // $api_response_text = strtolower($api_response_text);



                                            $crypto_beneficiary_array = array();
                                            $each_beneficiary_array = array("beneficiary_id" => $get_beneficiary_info["beneficiary_id"], "label" => $get_beneficiary_info["label"], "currency" => $get_beneficiary_info["currency"], "chain" => $get_beneficiary_info["crypto_chain"], "address" => $get_beneficiary_info["crypto_address"]);
                                            array_push($crypto_beneficiary_array, $each_beneficiary_array);

                                            $purchase_method = strtoupper($purchase_method);
                                            $amount = $amount;
                                            $discounted_amount = ($amount + ($amount * ($get_product_discount_details["val_2"] / 100)));
                                            $reference = substr(str_shuffle("12345678901234567890"), 0, 15);
                                            $api_reference = substr(str_shuffle("12345678901234567890"), 0, 15);
                                            $api_website = $api_detail["api_base_url"];
                                            $user_crypto_balance_before_debit = $get_wallet_currency_details["wallet_balance"];
                                            $user_crypto_balance_after_debit = ($user_crypto_balance_before_debit - $discounted_amount);

                                            $description = ucwords($currency . " transfer to " . $get_beneficiary_info["label"] . " { " . $get_wallet_currency_details["crypto_address"] . " }");
                                            $mode = $purchase_method;
                                            $status = 1;
                                            $debit_user = chargeUserCryptoWallet("debit", $get_beneficiary_info["currency"], $get_beneficiary_info["crypto_chain"], $reference, "", $amount, $discounted_amount, $description, $purchase_method, $_SERVER["HTTP_HOST"], $status);
                                            if ($debit_user === "success") {
                                                $insert_transaction = mysqli_query($connection_server, "INSERT INTO `sas_user_crypto_wallet_transactions` 
                                                  (vendor_id, username, from_currency, to_currency, from_address, beneficiary_id, amount, discounted_amount, balance_before, balance_after, description, mode, reference, api_reference, api_website, status) VALUES 
                                                  ('" . $get_logged_user_details["vendor_id"] . "', '" . $get_logged_user_details["username"] . "', '$currency', '" . $get_beneficiary_info["currency"] . "', '" . $get_wallet_currency_details["crypto_address"] . "', '" . $get_beneficiary_info["beneficiary_id"] . "', '$amount', '$discounted_amount', '$user_crypto_balance_before_debit', '$user_crypto_balance_after_debit', '$description', '$mode', '$reference', '$api_reference', '$api_website', '$status')");

                                                $json_response_array = array("status" => "success", "desc" => "Transfer To { " . $get_beneficiary_info["label"] . " } Successfully", "data" => $crypto_beneficiary_array);
                                                $json_response_encode = json_encode($json_response_array, true);
                                            } else {
                                                //Error: Transaction Unsuccessful
                                                $json_response_array = array("status" => "failed", "desc" => "Error: Transaction Unsuccessful");
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
                                //No API Installed
                                $json_response_array = array("status" => "failed", "desc" => "Gateway Error");
                                $json_response_encode = json_encode($json_response_array, true);
                            }
                        } else {
                            //Mismatched Currency/Chain
                            $json_response_array = array("status" => "failed", "desc" => "Mismatched Currency/Chain");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    } else {
                        //Wallet not exists
                        $json_response_array = array("status" => "failed", "desc" => "Wallet not exists");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    //Error: Beneficiary not exists
                    $json_response_array = array("status" => "failed", "desc" => "Error: Beneficiary not exists");
                    $json_response_encode = json_encode($json_response_array, true);
                }


            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

        //Crypto currency conversion
        if ($action_function == 7) {
            if (!empty($source_currency) && !empty($target_currency)) {
                if ($source_currency !== $target_currency) {
                    $account_level_table_name_arrays = array(1 => "sas_smart_exchange_fee_parameter_values", 2 => "sas_agent_exchange_fee_parameter_values", 3 => "sas_api_exchange_fee_parameter_values");
                    if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                        $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                        $product_name = strtolower($source_currency);
                        $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                        $product_discount_table = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_id='" . $product_table["id"] . "' && val_1 = '1'");
                    }

                    if (mysqli_num_rows($product_discount_table) == 1) {
                        $get_product_discount_details = mysqli_fetch_array($product_discount_table);
                        $crypto_type_table_name_arrays = array("ngn" => "sas_crypto_status", "usd" => "sas_crypto_status", "gbp" => "sas_crypto_status", "cad" => "sas_crypto_status", "eur" => "sas_crypto_status", "btc" => "sas_crypto_status", "eth" => "sas_crypto_status", "doge" => "sas_crypto_status", "usdt" => "sas_crypto_status", "usdc" => "sas_crypto_status", "sol" => "sas_crypto_status", "ada" => "sas_crypto_status", "trx" => "sas_crypto_status");
                        $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM " . $crypto_type_table_name_arrays[$source_currency] . " WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='$source_currency'"));

                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='crypto'");
                        if (mysqli_num_rows($get_api_lists) > 0) {
                            while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                if (!empty($api_detail["api_key"])) {
                                    if ($api_detail["status"] == 1) {

                                        $select_user_crypto_ledger_balance = mysqli_query($connection_server, "SELECT * FROM sas_user_crypto_ledger_balance WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "' && currency = '$source_currency'");

                                        if (mysqli_num_rows($select_user_crypto_ledger_balance) == 1) {
                                            $get_wallet_currency_details = mysqli_fetch_array($select_user_crypto_ledger_balance);

                                            if (is_numeric($get_wallet_currency_details["wallet_balance"]) && (floatval($get_wallet_currency_details["wallet_balance"]) >= 0)) {


                                                $api_gateway_name_file_exists = "crypto-convert-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                                    $api_gateway_name = "crypto-convert-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                } else {
                                                    $api_gateway_name = "crypto-convert-localserver.php";
                                                }

                                                // Reset variables at the start of each transaction
                                                $api_response = null;
                                                $api_response_description = null;
                                                $api_response_reference = null;
                                                $api_response_text = null;
                                                $api_response_status = null;

                                                include($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                                $api_response_text = strtolower($api_response_text);

                                                if (in_array($api_response, array("successful", "pending"))) {

                                                    $swap_reference = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz1234567890"), 0, 20);
                                                    $swap_api_reference = $api_swap_id;
                                                    if ($conversion_type == "buy") {
                                                        $api_response_conversion_type = "buy";
                                                        $api_response_rate = ($api_response_rate - (($get_product_discount_details["val_2"] / 100) * $api_response_rate));
                                                    } else {
                                                        $api_response_conversion_type = "sell";
                                                        $api_response_rate = ($api_response_rate + (($get_product_discount_details["val_2"] / 100) * $api_response_rate));
                                                    }
                                                    $create_crypto_swap_history = mysqli_query($connection_server, "INSERT INTO sas_user_crypto_swap (vendor_id, username, source_currency, target_currency, rate, `type`, swap_id, api_swap_id, api_website, `status`) VALUES ('" . $get_logged_user_details["vendor_id"] . "', '" . $get_logged_user_details["username"] . "', '$source_currency', '$target_currency', '$api_response_rate', '$conversion_type', '$swap_reference', '$swap_api_reference', '" . $api_detail["api_base_url"] . "', '2')");
                                                    if ($create_crypto_swap_history) {
                                                        //Convertion rate retrieval successful
                                                        $json_response_array = array("status" => "success", "desc" => "Convertion rate retrieval successful", "source-currency" => $source_currency, "target-currency" => $target_currency, "swap-id" => $swap_reference, "rate" => $api_response_rate, "type" => $api_response_conversion_type, "fee" => $get_product_discount_details["val_2"], "convert-time" => 30);
                                                        $json_response_encode = json_encode($json_response_array, true);
                                                    } else {
                                                        //Error: Unable to convert currency
                                                        $json_response_array = array("status" => "failed", "desc" => "Error: Unable to convert currency");
                                                        $json_response_encode = json_encode($json_response_array, true);
                                                    }
                                                }

                                                if ($api_response == "failed") {
                                                    $json_response_array = array("status" => "failed", "desc" => "Conversion Failed");
                                                    $json_response_encode = json_encode($json_response_array, true);
                                                }
                                            } else {
                                                //Error: Invalid wallet balance
                                                $json_response_array = array("status" => "failed", "desc" => "Error: Invalid wallet balance");
                                                $json_response_encode = json_encode($json_response_array, true);
                                            }
                                        } else {
                                            //Error: Wallet not exits
                                            $json_response_array = array("status" => "failed", "desc" => "Error: Wallet not exits");
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
                            //No API Installed
                            $json_response_array = array("status" => "failed", "desc" => "Gateway Error");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    } else {
                        //Mismatched Currency/Chain
                        $json_response_array = array("status" => "failed", "desc" => "Mismatched Currency/Chain");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    //Conversion for same wallet not allowed
                    $json_response_array = array("status" => "failed", "desc" => "Conversion for same wallet not allowed");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

        //Crypto currency swap
        if ($action_function == 8) {
            if (!empty($swap_id) && !empty($amount) && is_numeric($amount)) {
                $amount_to_convert = $amount;
                $swap_reference = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz1234567890"), 0, 20);
                $select_crypto_swap = mysqli_query($connection_server, "SELECT * FROM sas_user_crypto_swap WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "' && swap_id='" . $swap_id . "'");


                if (mysqli_num_rows($select_crypto_swap) == 1) {
                    $get_swap_info = mysqli_fetch_array($select_crypto_swap);
                    $select_user_crypto_ledger_balance = mysqli_query($connection_server, "SELECT * FROM sas_user_crypto_ledger_balance WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "' && currency = '" . $get_swap_info["source_currency"] . "'");
                    if (mysqli_num_rows($select_user_crypto_ledger_balance) == 1) {
                        $source_currency = $get_swap_info["source_currency"];
                        $target_currency = $get_swap_info["target_currency"];
                        $get_wallet_currency_details = mysqli_fetch_array($select_user_crypto_ledger_balance);



                        $account_level_table_name_arrays = array(1 => "sas_smart_exchange_fee_parameter_values", 2 => "sas_agent_exchange_fee_parameter_values", 3 => "sas_api_exchange_fee_parameter_values");
                        if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                            $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                            $product_name = strtolower($source_currency);
                            $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                            $product_discount_table = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_id='" . $product_table["id"] . "' && val_1 = '1'");
                        }

                        if (mysqli_num_rows($product_discount_table) == 1) {
                            $get_product_discount_details = mysqli_fetch_array($product_discount_table);
                            $crypto_permanent_wallet_arrays = array("usdt", "usdc");
                            $crypto_type_table_name_arrays = array("ngn" => "sas_crypto_status", "usd" => "sas_crypto_status", "gbp" => "sas_crypto_status", "cad" => "sas_crypto_status", "eur" => "sas_crypto_status", "btc" => "sas_crypto_status", "eth" => "sas_crypto_status", "doge" => "sas_crypto_status", "usdt" => "sas_crypto_status", "usdc" => "sas_crypto_status", "sol" => "sas_crypto_status", "ada" => "sas_crypto_status", "trx" => "sas_crypto_status");
                            $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM " . $crypto_type_table_name_arrays[$source_currency] . " WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='$source_currency'"));

                            $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='crypto'");
                            if (mysqli_num_rows($get_api_lists) > 0) {
                                while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                    if (!empty($api_detail["api_key"])) {
                                        if ($api_detail["status"] == 1) {

                                            $api_gateway_name_file_exists = "crypto-swap-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                                $api_gateway_name = "crypto-swap-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                            } else {
                                                $api_gateway_name = "crypto-swap-localserver.php";
                                            }

                                            // Reset variables at the start of each transaction
                                            $api_response = null;
                                            $api_response_description = null;
                                            $api_response_reference = null;
                                            $api_response_text = null;
                                            $api_response_status = null;

                                            include($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                            $api_response_text = strtolower($api_response_text);



                                            $crypto_swap_array = array();
                                            $each_swap_array = array("swap_id" => $get_swap_info["swap_id"], "rate" => $get_swap_info["rate"], "fee" => ($get_product_discount_details["val_2"] / 100), "source_currency" => $get_swap_info["source_currency"], "target_currency" => $get_swap_info["target_currency"]);
                                            array_push($crypto_swap_array, $each_swap_array);

                                            $purchase_method = strtoupper($purchase_method);

                                            if ($get_swap_info["type"] == "buy") {
                                                $amount = $get_swap_info["rate"] * $amount_to_convert;
                                                $discounted_amount = $amount;
                                            } else {
                                                $amount = $amount_to_convert / $get_swap_info["rate"];
                                                $discounted_amount = $amount;
                                            }

                                            $reference = substr(str_shuffle("12345678901234567890"), 0, 15);
                                            $api_reference = $api_response_reference;
                                            $api_website = $api_detail["api_base_url"];
                                            $user_crypto_balance_before_debit = $get_wallet_currency_details["wallet_balance"];
                                            $user_crypto_balance_after_debit = ($user_crypto_balance_before_debit - $discounted_amount);

                                            $description = $amount_to_convert . " " . strtoupper($source_currency) . " Swapped to " . $discounted_amount . " " . strtoupper($target_currency);
                                            $mode = $purchase_method;
                                            $status = 1;
                                            $debit_user = chargeUserCryptoWallet("debit", $get_swap_info["source_currency"], $get_swap_info["target_currency"], $reference, "", $amount_to_convert, $amount_to_convert, $description, $purchase_method, $_SERVER["HTTP_HOST"], $status);
                                            if ($debit_user === "success") {
                                                $insert_transaction = mysqli_query($connection_server, "INSERT INTO `sas_user_crypto_wallet_transactions` 
                                                  (vendor_id, username, from_currency, to_currency, from_address, beneficiary_id, amount, discounted_amount, balance_before, balance_after, description, mode, reference, api_reference, api_website, status) VALUES 
                                                  ('" . $get_logged_user_details["vendor_id"] . "', '" . $get_logged_user_details["username"] . "', '" . $get_swap_info["source_currency"] . "', '" . $get_swap_info["target_currency"] . "', '" . $get_swap_info["swap_id"] . "', '" . $get_swap_info["swap_id"] . "', '$amount_to_convert', '$amount_to_convert', '$user_crypto_balance_before_debit', '$user_crypto_balance_after_debit', '$description', '$mode', '$reference', '$api_reference', '$api_website', '$status')");

                                                $description_2 = $discounted_amount . " " . strtoupper($target_currency) . " Swapped from " . $amount_to_convert . " " . strtoupper($source_currency);
                                                $credit_user_wallet = chargeUserCryptoWallet("credit", $get_swap_info["target_currency"], $get_swap_info["source_currency"], $reference, "", $amount, $discounted_amount, $description_2, $purchase_method, $_SERVER["HTTP_HOST"], $status);
                                                if (in_array($source_currency, $crypto_permanent_wallet_arrays)) {
                                                    $api_wallet_debit_gateway_name_file_exists = "crypto-debit-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_wallet_debit_gateway_name_file_exists)) {
                                                        $api_wallet_debit_gateway_name = "crypto-debit-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                    } else {
                                                        $api_wallet_debit_gateway_name = "crypto-debit-localserver.php";
                                                    }

                                                    include($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_wallet_debit_gateway_name);

                                                }

                                                if (in_array($target_currency, $crypto_permanent_wallet_arrays)) {
                                                    $api_wallet_debit_gateway_name_file_exists = "crypto-credit-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_wallet_debit_gateway_name_file_exists)) {
                                                        $api_wallet_debit_gateway_name = "crypto-credit-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                    } else {
                                                        $api_wallet_debit_gateway_name = "crypto-credit-localserver.php";
                                                    }

                                                    include($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_wallet_debit_gateway_name);

                                                }
                                                $json_response_array = array("status" => "success", "desc" => "Currency Swapped Successfully", "data" => $crypto_swap_array);
                                                $json_response_encode = json_encode($json_response_array, true);
                                            } else {
                                                //Error: Transaction Unsuccessful
                                                $json_response_array = array("status" => "failed", "desc" => "Error: Insufficient Wallet Funds");
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
                                //No API Installed
                                $json_response_array = array("status" => "failed", "desc" => "Gateway Error");
                                $json_response_encode = json_encode($json_response_array, true);
                            }
                        } else {
                            //Mismatched Currency/Chain
                            $json_response_array = array("status" => "failed", "desc" => "Mismatched Currency/Chain");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    } else {
                        //Wallet not exists
                        $json_response_array = array("status" => "failed", "desc" => "Wallet not exists");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    //Error: Swap Id not exists
                    $json_response_array = array("status" => "failed", "desc" => "Error: Swap Id not exists");
                    $json_response_encode = json_encode($json_response_array, true);
                }

            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

        //Currency Transfer Chain/Network Fee
        if ($action_function == 9) {
            if (!empty($currency) && !empty($chain)) {
                $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                    $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                    $product_name = strtolower($currency);
                    $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                    $product_discount_table = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_id='" . $product_table["id"] . "' && val_1='" . $chain . "'");
                }

                $crypto_currency_chain_array = array();
                if (mysqli_num_rows($product_discount_table) == 1) {
                    $get_product_discount_table = mysqli_fetch_array($product_discount_table);
                    //Mismatched Currency/Chain
                    $json_response_array = array("status" => "success", "desc" => "Currency Chain Retrieved Successfully", "data" => array("currency" => $currency, "chain" => $chain, "fee" => $get_product_discount_table["val_2"]));
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    //Mismatched Currency/Chain
                    $json_response_array = array("status" => "failed", "desc" => "Mismatched Currency/Chain");
                    $json_response_encode = json_encode($json_response_array, true);
                }

            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Currency is required");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

        //Create Payment Link
        if ($action_function == 10) {
            if (!empty($currency) && !empty($amount) && ($amount > 0) && !empty($desc)) {
                $payment_link_currency_arrays = array("ngn", "cad");
                if (in_array($currency, $payment_link_currency_arrays)) {
                    $payment_reference = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz1234567890"), 0, 20);


                    $reference = $payment_reference;
                    $api_reference = "";
                    $api_website = "";
                    $desc = $desc;
                    $min_qty = 1;
                    $max_qty = 1;
                    $status = 2;
                    $amount = strip_tags($amount);
                    $currency = strip_tags($currency);

                    $create_user_payment_link = mysqli_query($connection_server, "INSERT INTO sas_payment_links (vendor_id, username, currency, reference, api_reference, amount, `description`, min_qty, max_qty, api_website, `status`) VALUES ('" . $get_logged_user_details["vendor_id"] . "', '" . $get_logged_user_details["username"] . "', '$currency', '$reference', '$api_reference', '$amount', '$desc', '$min_qty', '$max_qty', '$api_website', '$status')");

                    if ($create_user_payment_link) {
                        // Payment Link Created Successfully
                        $json_response_array = array("status" => "success", "desc" => "Payment Link Created Successfully", "url" => "https://" . $_SERVER["HTTP_HOST"] . "/payment-link/" . $reference);
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        //Error: Unable to create payment link
                        $json_response_array = array("status" => "failed", "desc" => "Error: Unable to create payment link");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    //Error: Currency not allowed for payment link
                    $json_response_array = array("status" => "failed", "desc" => "Error: Currency not allowed for payment link");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Incomplete Parameters
                $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }

    } else {
        //Invalid crypto type
        $json_response_array = array("status" => "failed", "desc" => "Invalid crypto type");
        $json_response_encode = json_encode($json_response_array, true);
    }
} else {
    //Purchase Method Not specified
    $json_response_array = array("status" => "failed", "desc" => "Purchase Method Not specified");
    $json_response_encode = json_encode($json_response_array, true);
}
?>