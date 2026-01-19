<?php session_start();
include("../func/bc-admin-config.php");

if (isset($_POST["update-key"])) {
    $api_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-id"])));
    $apikey = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-key"])));
    $apistatus = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-status"])));

    if (!empty($api_id) && is_numeric($api_id)) {
        if (!empty($apikey)) {
            if (is_numeric($apistatus) && in_array($apistatus, array("0", "1"))) {
                $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id' && api_type='dollarcard'");
                if (mysqli_num_rows($select_api_lists) == 1) {
                    mysqli_query($connection_server, "UPDATE sas_apis SET api_key='$apikey', status='$apistatus' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id' && api_type='dollarcard'");
                    //APIkey Updated Successfully
                    $json_response_array = array("desc" => "APIkey Updated Successfully");
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    //API Doesnt Exists
                    $json_response_array = array("desc" => "API Doesnt Exists");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Invalid API Status
                $json_response_array = array("desc" => "Invalid API Status");
                $json_response_encode = json_encode($json_response_array, true);
            }
        } else {
            //Apikey Field Empty
            $json_response_array = array("desc" => "Apikey Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        }
    } else {
        //Invalid Apikey Website
        $json_response_array = array("desc" => "Invalid Apikey Website");
        $json_response_encode = json_encode($json_response_array, true);
    }
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

$array_visa_product_variety = array("1");
$array_mastercard_product_variety = array("1");
$array_verve_product_variety = array("1");

if (isset($_POST["install-product"])) {
    $api_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-id"])));
    $item_status = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["item-status"])));
    $product_name = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["product-name"]))));
    $products_array = array("visa", "mastercard", "verve");
    $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");
    $card_funding_account_level_table_name_arrays = array("sas_smart_card_funding_parameter_values", "sas_agent_card_funding_parameter_values", "sas_api_card_funding_parameter_values");
    $card_transaction_account_level_table_name_arrays = array("sas_smart_card_transaction_parameter_values", "sas_agent_card_transaction_parameter_values", "sas_api_card_transaction_parameter_values");

    if (!empty($api_id) && is_numeric($api_id)) {
        if (!empty($product_name)) {
            if (in_array($product_name, $products_array)) {
                if (is_numeric($item_status) && in_array($item_status, array("0", "1"))) {
                    $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id' && api_type='dollarcard'");
                    $select_dollarcard_status_lists = mysqli_query($connection_server, "SELECT * FROM sas_dollarcard_status WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                    if (mysqli_num_rows($select_api_lists) == 1) {
                        if (mysqli_num_rows($select_dollarcard_status_lists) == 0) {
                            mysqli_query($connection_server, "INSERT INTO sas_dollarcard_status (vendor_id, api_id, product_name, status) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '$product_name', '$item_status')");
                        } else {
                            mysqli_query($connection_server, "UPDATE sas_dollarcard_status SET api_id='$api_id', status='$item_status' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                        }

                        foreach ($account_level_table_name_arrays as $account_level_table_name) {
                            $select_product_details = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                            if (mysqli_num_rows($select_product_details) == 1) {
                                $get_product_details = mysqli_fetch_array($select_product_details);
                                $get_selected_api_list = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id'"));
                                $select_api_list_with_api_type = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id!='$api_id' && api_type='" . $get_selected_api_list["api_type"] . "' LIMIT 1");
                                if (mysqli_num_rows($select_api_list_with_api_type) == 1) {
                                    $get_api_list_with_api_type = mysqli_fetch_array($select_api_list_with_api_type);
                                    $select_api_list_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_api_list_with_api_type["id"] . "' && product_id='" . $get_product_details["id"] . "'");
                                    if (mysqli_num_rows($select_api_list_product_pricing_table) == 1) {
                                        $get_api_list_product_pricing_table = mysqli_fetch_array($select_api_list_product_pricing_table);
                                        $pro_val_1 = $get_api_list_product_pricing_table["val_1"];
                                        $pro_val_2 = $get_api_list_product_pricing_table["val_2"];
                                        $pro_val_3 = $get_api_list_product_pricing_table["val_3"];
                                        $pro_val_4 = $get_api_list_product_pricing_table["val_4"];
                                        $pro_val_5 = $get_api_list_product_pricing_table["val_5"];
                                        $pro_val_6 = $get_api_list_product_pricing_table["val_6"];
                                        $pro_val_7 = $get_api_list_product_pricing_table["val_7"];
                                        $pro_val_8 = $get_api_list_product_pricing_table["val_8"];
                                        $pro_val_9 = $get_api_list_product_pricing_table["val_9"];
                                        $pro_val_10 = $get_api_list_product_pricing_table["val_10"];
                                    } else {
                                        $pro_val_1 = "0";
                                        $pro_val_2 = "0";
                                        $pro_val_3 = "0";
                                        $pro_val_4 = "0";
                                        $pro_val_5 = "0";
                                        $pro_val_6 = "0";
                                        $pro_val_7 = "0";
                                        $pro_val_8 = "0";
                                        $pro_val_9 = "0";
                                        $pro_val_10 = "0";
                                    }
                                } else {
                                    $pro_val_1 = "0";
                                    $pro_val_2 = "0";
                                    $pro_val_3 = "0";
                                    $pro_val_4 = "0";
                                    $pro_val_5 = "0";
                                    $pro_val_6 = "0";
                                    $pro_val_7 = "0";
                                    $pro_val_8 = "0";
                                    $pro_val_9 = "0";
                                    $pro_val_10 = "0";
                                }
                                $select_all_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='dollarcard'");
                                $product_array_string_name = "array_" . $product_name . "_product_variety";
                                $product_variety = $$product_array_string_name;
                                $count_product_variety = count($product_variety);
                                if ($count_product_variety >= 1) {
                                    foreach ($product_variety as $product_val_1) {
                                        $product_val_1 = trim($product_val_1);
                                        $product_pricing_table = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='$api_id' && product_id='" . $get_product_details["id"] . "' && val_1='$product_val_1'");
                                        if (mysqli_num_rows($product_pricing_table) == 0) {
                                            mysqli_query($connection_server, "INSERT INTO $account_level_table_name (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '" . $get_product_details["id"] . "', '$product_val_1', '$pro_val_2', '$pro_val_3')");
                                        } else {
                                            if (mysqli_num_rows($select_all_api_lists) >= 1) {
                                                while ($api_details = mysqli_fetch_assoc($select_all_api_lists)) {
                                                    if ($api_details["id"] !== $api_id) {
                                                        mysqli_query($connection_server, "DELETE FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $api_details["id"] . "' && product_id='" . $get_product_details["id"] . "' && val_1='$product_val_1'");
                                                    } else {
                                                        $check_product_pricing_row_exists = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='$api_id' && product_id='" . $get_product_details["id"] . "' && val_1='$product_val_1'");
                                                        if (mysqli_num_rows($check_product_pricing_row_exists) == 0) {
                                                            mysqli_query($connection_server, "INSERT INTO $account_level_table_name (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '" . $get_product_details["id"] . "', '$product_val_1', '$pro_val_2', '$pro_val_3')");
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        foreach ($card_funding_account_level_table_name_arrays as $account_level_table_name) {
                            $select_product_details = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                            if (mysqli_num_rows($select_product_details) == 1) {
                                $get_product_details = mysqli_fetch_array($select_product_details);
                                $get_selected_api_list = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id'"));
                                $select_api_list_with_api_type = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id!='$api_id' && api_type='" . $get_selected_api_list["api_type"] . "' LIMIT 1");
                                if (mysqli_num_rows($select_api_list_with_api_type) == 1) {
                                    $get_api_list_with_api_type = mysqli_fetch_array($select_api_list_with_api_type);
                                    $select_api_list_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_api_list_with_api_type["id"] . "' && product_id='" . $get_product_details["id"] . "'");
                                    if (mysqli_num_rows($select_api_list_product_pricing_table) == 1) {
                                        $get_api_list_product_pricing_table = mysqli_fetch_array($select_api_list_product_pricing_table);
                                        $pro_val_1 = $get_api_list_product_pricing_table["val_1"];
                                        $pro_val_2 = $get_api_list_product_pricing_table["val_2"];
                                        $pro_val_3 = $get_api_list_product_pricing_table["val_3"];
                                        $pro_val_4 = $get_api_list_product_pricing_table["val_4"];
                                        $pro_val_5 = $get_api_list_product_pricing_table["val_5"];
                                        $pro_val_6 = $get_api_list_product_pricing_table["val_6"];
                                        $pro_val_7 = $get_api_list_product_pricing_table["val_7"];
                                        $pro_val_8 = $get_api_list_product_pricing_table["val_8"];
                                        $pro_val_9 = $get_api_list_product_pricing_table["val_9"];
                                        $pro_val_10 = $get_api_list_product_pricing_table["val_10"];
                                    } else {
                                        $pro_val_1 = "0";
                                        $pro_val_2 = "0";
                                        $pro_val_3 = "0";
                                        $pro_val_4 = "0";
                                        $pro_val_5 = "0";
                                        $pro_val_6 = "0";
                                        $pro_val_7 = "0";
                                        $pro_val_8 = "0";
                                        $pro_val_9 = "0";
                                        $pro_val_10 = "0";
                                    }
                                } else {
                                    $pro_val_1 = "0";
                                    $pro_val_2 = "0";
                                    $pro_val_3 = "0";
                                    $pro_val_4 = "0";
                                    $pro_val_5 = "0";
                                    $pro_val_6 = "0";
                                    $pro_val_7 = "0";
                                    $pro_val_8 = "0";
                                    $pro_val_9 = "0";
                                    $pro_val_10 = "0";
                                }
                                $select_all_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='dollarcard'");
                                $product_array_string_name = "array_" . $product_name . "_product_variety";
                                $product_variety = $$product_array_string_name;
                                $count_product_variety = count($product_variety);
                                if ($count_product_variety >= 1) {
                                    foreach ($product_variety as $product_val_1) {
                                        $product_val_1 = trim($product_val_1);
                                        $product_pricing_table = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='$api_id' && product_id='" . $get_product_details["id"] . "' && val_1='$product_val_1'");
                                        if (mysqli_num_rows($product_pricing_table) == 0) {
                                            mysqli_query($connection_server, "INSERT INTO $account_level_table_name (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '" . $get_product_details["id"] . "', '$product_val_1', '$pro_val_2', '$pro_val_3')");
                                        } else {
                                            if (mysqli_num_rows($select_all_api_lists) >= 1) {
                                                while ($api_details = mysqli_fetch_assoc($select_all_api_lists)) {
                                                    if ($api_details["id"] !== $api_id) {
                                                        mysqli_query($connection_server, "DELETE FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $api_details["id"] . "' && product_id='" . $get_product_details["id"] . "' && val_1='$product_val_1'");
                                                    } else {
                                                        $check_product_pricing_row_exists = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='$api_id' && product_id='" . $get_product_details["id"] . "' && val_1='$product_val_1'");
                                                        if (mysqli_num_rows($check_product_pricing_row_exists) == 0) {
                                                            mysqli_query($connection_server, "INSERT INTO $account_level_table_name (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '" . $get_product_details["id"] . "', '$product_val_1', '$pro_val_2', '$pro_val_3')");
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        foreach ($card_transaction_account_level_table_name_arrays as $account_level_table_name) {
                            $select_product_details = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                            if (mysqli_num_rows($select_product_details) == 1) {
                                $get_product_details = mysqli_fetch_array($select_product_details);
                                $get_selected_api_list = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id'"));
                                $select_api_list_with_api_type = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id!='$api_id' && api_type='" . $get_selected_api_list["api_type"] . "' LIMIT 1");
                                if (mysqli_num_rows($select_api_list_with_api_type) == 1) {
                                    $get_api_list_with_api_type = mysqli_fetch_array($select_api_list_with_api_type);
                                    $select_api_list_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_api_list_with_api_type["id"] . "' && product_id='" . $get_product_details["id"] . "'");
                                    if (mysqli_num_rows($select_api_list_product_pricing_table) == 1) {
                                        $get_api_list_product_pricing_table = mysqli_fetch_array($select_api_list_product_pricing_table);
                                        $pro_val_1 = $get_api_list_product_pricing_table["val_1"];
                                        $pro_val_2 = $get_api_list_product_pricing_table["val_2"];
                                        $pro_val_3 = $get_api_list_product_pricing_table["val_3"];
                                        $pro_val_4 = $get_api_list_product_pricing_table["val_4"];
                                        $pro_val_5 = $get_api_list_product_pricing_table["val_5"];
                                        $pro_val_6 = $get_api_list_product_pricing_table["val_6"];
                                        $pro_val_7 = $get_api_list_product_pricing_table["val_7"];
                                        $pro_val_8 = $get_api_list_product_pricing_table["val_8"];
                                        $pro_val_9 = $get_api_list_product_pricing_table["val_9"];
                                        $pro_val_10 = $get_api_list_product_pricing_table["val_10"];
                                    } else {
                                        $pro_val_1 = "0";
                                        $pro_val_2 = "0";
                                        $pro_val_3 = "0";
                                        $pro_val_4 = "0";
                                        $pro_val_5 = "0";
                                        $pro_val_6 = "0";
                                        $pro_val_7 = "0";
                                        $pro_val_8 = "0";
                                        $pro_val_9 = "0";
                                        $pro_val_10 = "0";
                                    }
                                } else {
                                    $pro_val_1 = "0";
                                    $pro_val_2 = "0";
                                    $pro_val_3 = "0";
                                    $pro_val_4 = "0";
                                    $pro_val_5 = "0";
                                    $pro_val_6 = "0";
                                    $pro_val_7 = "0";
                                    $pro_val_8 = "0";
                                    $pro_val_9 = "0";
                                    $pro_val_10 = "0";
                                }
                                $select_all_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='dollarcard'");
                                $product_array_string_name = "array_" . $product_name . "_product_variety";
                                $product_variety = $$product_array_string_name;
                                $count_product_variety = count($product_variety);
                                if ($count_product_variety >= 1) {
                                    foreach ($product_variety as $product_val_1) {
                                        $product_val_1 = trim($product_val_1);
                                        $product_pricing_table = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='$api_id' && product_id='" . $get_product_details["id"] . "' && val_1='$product_val_1'");
                                        if (mysqli_num_rows($product_pricing_table) == 0) {
                                            mysqli_query($connection_server, "INSERT INTO $account_level_table_name (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '" . $get_product_details["id"] . "', '$product_val_1', '$pro_val_2', '$pro_val_3')");
                                        } else {
                                            if (mysqli_num_rows($select_all_api_lists) >= 1) {
                                                while ($api_details = mysqli_fetch_assoc($select_all_api_lists)) {
                                                    if ($api_details["id"] !== $api_id) {
                                                        mysqli_query($connection_server, "DELETE FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $api_details["id"] . "' && product_id='" . $get_product_details["id"] . "' && val_1='$product_val_1'");
                                                    } else {
                                                        $check_product_pricing_row_exists = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='$api_id' && product_id='" . $get_product_details["id"] . "' && val_1='$product_val_1'");
                                                        if (mysqli_num_rows($check_product_pricing_row_exists) == 0) {
                                                            mysqli_query($connection_server, "INSERT INTO $account_level_table_name (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '" . $get_product_details["id"] . "', '$product_val_1', '$pro_val_2', '$pro_val_3')");
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //Product Updated Successfully
                        $json_response_array = array("desc" => "Product Updated Successfully");
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        //API Doesnt Exists
                        $json_response_array = array("desc" => "API Doesnt Exists");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    //Invalid DOLLARCARD Status
                    $json_response_array = array("desc" => "Invalid dollarcard Status");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Invalid Product Name
                $json_response_array = array("desc" => "Invalid Product Name");
                $json_response_encode = json_encode($json_response_array, true);
            }
        } else {
            //Product Name Field Empty
            $json_response_array = array("desc" => "Product Name Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        }
    } else {
        //Invalid Apikey Website
        $json_response_array = array("desc" => "Invalid Apikey Website");
        $json_response_encode = json_encode($json_response_array, true);
    }
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-exchange-rate"])) {
    $currency_array = $_POST["currency"];
    $credit_amount_array = $_POST["credit-amount"];
    $debit_amount_array = $_POST["debit-amount"];
    if (count($currency_array) == count($credit_amount_array) && count($credit_amount_array) == count($debit_amount_array)) {
        foreach ($currency_array as $index => $api_id) {
            $currency = $currency_array[$index];
            $credit_amount = $credit_amount_array[$index];
            $debit_amount = $debit_amount_array[$index];

            $dollar_exchange_rate_table = mysqli_query($connection_server, "SELECT * FROM sas_dollar_exchange_rates WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_type='dollarcard' && currency='" . $currency . "'");
            if (mysqli_num_rows($dollar_exchange_rate_table) == 1) {
                while ($dollar_exchange_rate_details = mysqli_fetch_assoc($dollar_exchange_rate_table)) {
                    mysqli_query($connection_server, "UPDATE sas_dollar_exchange_rates SET credit_amount='" . $credit_amount . "', debit_amount='" . $debit_amount . "' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_type='dollarcard' && currency='" . $dollar_exchange_rate_details["currency"] . "'");
                }
            }
        }
        //Price Updated Successfully
        $json_response_array = array("desc" => "Price Updated Successfully");
        $json_response_encode = json_encode($json_response_array, true);
    } else {
        //Product Connection Error
        $json_response_array = array("desc" => "Product Connection Error");
        $json_response_encode = json_encode($json_response_array, true);
    }
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-price"])) {
    $api_id_array = $_POST["api-id"];
    $product_id_array = $_POST["product-id"];
    $product_code_1_array = $_POST["product-code-1"];
    $product_desc_array = $_POST["product-desc"];
    $smart_price_array = $_POST["smart-price"];
    $agent_price_array = $_POST["agent-price"];
    $api_price_array = $_POST["api-price"];
    $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");
    if (count($api_id_array) == count($product_id_array)) {
        foreach ($api_id_array as $index => $api_id) {
            $api_id = $api_id_array[$index];
            $product_id = $product_id_array[$index];
            $product_code_1 = $product_code_1_array[$index];
            $product_desc = $product_desc_array[$index];
            $smart_price = $smart_price_array[$index];
            $agent_price = $agent_price_array[$index];
            $api_price = $api_price_array[$index];
            $get_selected_api_list = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id'"));
            $select_api_list_with_api_type = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='" . $get_selected_api_list["api_type"] . "'");
            if (mysqli_num_rows($select_api_list_with_api_type) > 0) {
                while ($refined_api_id = mysqli_fetch_assoc($select_api_list_with_api_type)) {
                    $smart_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[0] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    if (mysqli_num_rows($smart_product_pricing_table) == 0) {
                        mysqli_query($connection_server, "INSERT INTO " . $account_level_table_name_arrays[0] . " (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('" . $get_logged_admin_details["id"] . "', '" . $refined_api_id["id"] . "', '$product_id', '$product_code_1', '$smart_price', '$product_desc')");
                    } else {
                        mysqli_query($connection_server, "UPDATE " . $account_level_table_name_arrays[0] . " SET vendor_id='" . $get_logged_admin_details["id"] . "', api_id='" . $refined_api_id["id"] . "', product_id='$product_id', val_1='$product_code_1', val_2='$smart_price', val_3='$product_desc' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    }

                    $agent_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[1] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    if (mysqli_num_rows($agent_product_pricing_table) == 0) {
                        mysqli_query($connection_server, "INSERT INTO " . $account_level_table_name_arrays[1] . " (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('" . $get_logged_admin_details["id"] . "', '" . $refined_api_id["id"] . "', '$product_id', '$product_code_1', '$agent_price', '$product_desc')");
                    } else {
                        mysqli_query($connection_server, "UPDATE " . $account_level_table_name_arrays[1] . " SET vendor_id='" . $get_logged_admin_details["id"] . "', api_id='" . $refined_api_id["id"] . "', product_id='$product_id', val_1='$product_code_1', val_2='$agent_price', val_3='$product_desc' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    }

                    $api_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[2] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    if (mysqli_num_rows($api_product_pricing_table) == 0) {
                        mysqli_query($connection_server, "INSERT INTO " . $account_level_table_name_arrays[2] . " (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('" . $get_logged_admin_details["id"] . "', '" . $refined_api_id["id"] . "', '$product_id', '$product_code_1', '$api_price', '$product_desc')");
                    } else {
                        mysqli_query($connection_server, "UPDATE " . $account_level_table_name_arrays[2] . " SET vendor_id='" . $get_logged_admin_details["id"] . "', api_id='" . $refined_api_id["id"] . "', product_id='$product_id', val_1='$product_code_1', val_2='$api_price', val_3='$product_desc' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    }
                }
            }
        }
        //Price Updated Successfully
        $json_response_array = array("desc" => "Price Updated Successfully");
        $json_response_encode = json_encode($json_response_array, true);
    } else {
        //Product Connection Error
        $json_response_array = array("desc" => "Product Connection Error");
        $json_response_encode = json_encode($json_response_array, true);
    }
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-funding-fee"])) {
    $api_id_array = $_POST["api-id"];
    $product_id_array = $_POST["product-id"];
    $product_code_1_array = $_POST["product-code-1"];
    $smart_price_array = $_POST["smart-price"];
    $agent_price_array = $_POST["agent-price"];
    $api_price_array = $_POST["api-price"];
    $account_level_table_name_arrays = array("sas_smart_card_funding_parameter_values", "sas_agent_card_funding_parameter_values", "sas_api_card_funding_parameter_values");
    if (count($api_id_array) == count($product_id_array)) {
        foreach ($api_id_array as $index => $api_id) {
            $api_id = $api_id_array[$index];
            $product_id = $product_id_array[$index];
            $product_code_1 = $product_code_1_array[$index];
            $smart_price = $smart_price_array[$index];
            $agent_price = $agent_price_array[$index];
            $api_price = $api_price_array[$index];
            $get_selected_api_list = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id'"));
            $select_api_list_with_api_type = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='" . $get_selected_api_list["api_type"] . "'");
            if (mysqli_num_rows($select_api_list_with_api_type) > 0) {
                while ($refined_api_id = mysqli_fetch_assoc($select_api_list_with_api_type)) {
                    $smart_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[0] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    if (mysqli_num_rows($smart_product_pricing_table) == 0) {
                        mysqli_query($connection_server, "INSERT INTO " . $account_level_table_name_arrays[0] . " (vendor_id, api_id, product_id, val_1, val_2) VALUES ('" . $get_logged_admin_details["id"] . "', '" . $refined_api_id["id"] . "', '$product_id', '$product_code_1', '$smart_price')");
                    } else {
                        mysqli_query($connection_server, "UPDATE " . $account_level_table_name_arrays[0] . " SET vendor_id='" . $get_logged_admin_details["id"] . "', api_id='" . $refined_api_id["id"] . "', product_id='$product_id', val_1='$product_code_1', val_2='$smart_price' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    }

                    $agent_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[1] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    if (mysqli_num_rows($agent_product_pricing_table) == 0) {
                        mysqli_query($connection_server, "INSERT INTO " . $account_level_table_name_arrays[1] . " (vendor_id, api_id, product_id, val_1, val_2) VALUES ('" . $get_logged_admin_details["id"] . "', '" . $refined_api_id["id"] . "', '$product_id', '$product_code_1', '$agent_price')");
                    } else {
                        mysqli_query($connection_server, "UPDATE " . $account_level_table_name_arrays[1] . " SET vendor_id='" . $get_logged_admin_details["id"] . "', api_id='" . $refined_api_id["id"] . "', product_id='$product_id', val_1='$product_code_1', val_2='$agent_price' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    }

                    $api_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[2] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    if (mysqli_num_rows($api_product_pricing_table) == 0) {
                        mysqli_query($connection_server, "INSERT INTO " . $account_level_table_name_arrays[2] . " (vendor_id, api_id, product_id, val_1, val_2) VALUES ('" . $get_logged_admin_details["id"] . "', '" . $refined_api_id["id"] . "', '$product_id', '$product_code_1', '$api_price')");
                    } else {
                        mysqli_query($connection_server, "UPDATE " . $account_level_table_name_arrays[2] . " SET vendor_id='" . $get_logged_admin_details["id"] . "', api_id='" . $refined_api_id["id"] . "', product_id='$product_id', val_1='$product_code_1', val_2='$api_price' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    }
                }
            }
        }
        //Funding Fee Updated Successfully
        $json_response_array = array("desc" => "Funding Fee Updated Successfully");
        $json_response_encode = json_encode($json_response_array, true);
    } else {
        //Product Connection Error
        $json_response_array = array("desc" => "Product Connection Error");
        $json_response_encode = json_encode($json_response_array, true);
    }
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-transaction-fee"])) {
    $api_id_array = $_POST["api-id"];
    $product_id_array = $_POST["product-id"];
    $product_code_1_array = $_POST["product-code-1"];
    $smart_price_array = $_POST["smart-price"];
    $agent_price_array = $_POST["agent-price"];
    $api_price_array = $_POST["api-price"];
    $account_level_table_name_arrays = array("sas_smart_card_transaction_parameter_values", "sas_agent_card_transaction_parameter_values", "sas_api_card_transaction_parameter_values");
    if (count($api_id_array) == count($product_id_array)) {
        foreach ($api_id_array as $index => $api_id) {
            $api_id = $api_id_array[$index];
            $product_id = $product_id_array[$index];
            $product_code_1 = $product_code_1_array[$index];
            $smart_price = $smart_price_array[$index];
            $agent_price = $agent_price_array[$index];
            $api_price = $api_price_array[$index];
            $get_selected_api_list = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id'"));
            $select_api_list_with_api_type = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='" . $get_selected_api_list["api_type"] . "'");
            if (mysqli_num_rows($select_api_list_with_api_type) > 0) {
                while ($refined_api_id = mysqli_fetch_assoc($select_api_list_with_api_type)) {
                    $smart_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[0] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    if (mysqli_num_rows($smart_product_pricing_table) == 0) {
                        mysqli_query($connection_server, "INSERT INTO " . $account_level_table_name_arrays[0] . " (vendor_id, api_id, product_id, val_1, val_2) VALUES ('" . $get_logged_admin_details["id"] . "', '" . $refined_api_id["id"] . "', '$product_id', '$product_code_1', '$smart_price')");
                    } else {
                        mysqli_query($connection_server, "UPDATE " . $account_level_table_name_arrays[0] . " SET vendor_id='" . $get_logged_admin_details["id"] . "', api_id='" . $refined_api_id["id"] . "', product_id='$product_id', val_1='$product_code_1', val_2='$smart_price' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    }

                    $agent_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[1] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    if (mysqli_num_rows($agent_product_pricing_table) == 0) {
                        mysqli_query($connection_server, "INSERT INTO " . $account_level_table_name_arrays[1] . " (vendor_id, api_id, product_id, val_1, val_2) VALUES ('" . $get_logged_admin_details["id"] . "', '" . $refined_api_id["id"] . "', '$product_id', '$product_code_1', '$agent_price')");
                    } else {
                        mysqli_query($connection_server, "UPDATE " . $account_level_table_name_arrays[1] . " SET vendor_id='" . $get_logged_admin_details["id"] . "', api_id='" . $refined_api_id["id"] . "', product_id='$product_id', val_1='$product_code_1', val_2='$agent_price' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    }

                    $api_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[2] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    if (mysqli_num_rows($api_product_pricing_table) == 0) {
                        mysqli_query($connection_server, "INSERT INTO " . $account_level_table_name_arrays[2] . " (vendor_id, api_id, product_id, val_1, val_2) VALUES ('" . $get_logged_admin_details["id"] . "', '" . $refined_api_id["id"] . "', '$product_id', '$product_code_1', '$api_price')");
                    } else {
                        mysqli_query($connection_server, "UPDATE " . $account_level_table_name_arrays[2] . " SET vendor_id='" . $get_logged_admin_details["id"] . "', api_id='" . $refined_api_id["id"] . "', product_id='$product_id', val_1='$product_code_1', val_2='$api_price' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id' && val_1='$product_code_1'");
                    }
                }
            }
        }
        //Transaction Fee Updated Successfully
        $json_response_array = array("desc" => "Transaction Fee Updated Successfully");
        $json_response_encode = json_encode($json_response_array, true);
    } else {
        //Product Connection Error
        $json_response_array = array("desc" => "Product Connection Error");
        $json_response_encode = json_encode($json_response_array, true);
    }
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

$csv_price_level_array = [];
$csv_price_level_array[] = "product_name,smart_level,agent_level,api_level,desc";

?>
<!DOCTYPE html>

<head>
    <title>Dollarcard API | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_super_admin_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="<?php echo $get_all_super_admin_site_details["primary_color"] ?? "#198754"; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">

    <!-- Vendor CSS Files -->
    <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets-2/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets-2/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets-2/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets-2/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="../assets-2/css/style.css" rel="stylesheet">

</head>

<body>
    <?php include("../func/bc-admin-header.php"); ?>
    <div class="pagetitle">
        <h1>DOLLARCARD API</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dollar Card</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="col-12">
            <div class="card info-card px-5 py-5">
                <div class="row mb-3">

                    <span style="user-select: auto;" class="h4 fw-bold">API SETTING</span><br>
                    <form method="post" action="">
                        <select style="text-align: center;" id="" name="api-id" onchange="getWebApikey(this);"
                            class="form-control mb-1" required />
                        <?php
                        //All DOLLARCARD API
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='dollarcard'");
                        if (mysqli_num_rows($get_api_lists) >= 1) {
                            echo '<option value="" default hidden selected>Choose API</option>';
                            while ($api_details = mysqli_fetch_assoc($get_api_lists)) {
                                if (empty(trim($api_details["api_key"]))) {
                                    $apikey_status = "( Empty Key )";
                                } else {
                                    $apikey_status = "";
                                }

                                echo '<option value="' . $api_details["id"] . '" api-key="' . $api_details["api_key"] . '" api-status="' . $api_details["status"] . '">' . strtoupper($api_details["api_base_url"]) . ' ' . $apikey_status . '</option>';
                            }
                        } else {
                            echo '<option value="" default hidden selected>No API</option>';
                        }
                        ?>
                        </select><br />
                        <select style="text-align: center;" id="web-apikey-status" name="api-status" onchange=""
                            class="form-control mb-1" required />
                        <option value="" default hidden selected>Choose API Status</option>
                        <option value="1">Enabled</option>
                        <option value="0">Disabled</option>
                        </select><br />
                        <input style="text-align: center;" id="web-apikey-input" name="api-key" onkeyup="" type="text"
                            value="" placeholder="Api Key" class="form-control mb-1" required /><br />
                        <button name="update-key" type="submit" style="user-select: auto;"
                            class="btn btn-primary col-12 mb-1">
                            UPDATE KEY
                        </button><br>
                        <div style="text-align: center;" class="container">
                            <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                        </div><br />
                    </form>
                </div>
            </div>

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <span style="user-select: auto;" class="h4 fw-bold">PRODUCT INSTALLATION</span><br>
                    <div style="text-align: center; user-select: auto;" class="container">
                        <img alt="Mastercard" id="mastercard-lg" product-name-array="visa,mastercard,verve"
                            src="/asset/mastercard.png"
                            onclick="tickProduct(this, 'mastercard', 'api-product-name', 'install-product', 'png');"
                            class="col-2 rounded-5 border m-1  " />
                        <img alt="Visa" id="visa-lg" product-name-array="visa,mastercard,verve" src="/asset/visa.png"
                            onclick="tickProduct(this, 'visa', 'api-product-name', 'install-product', 'png');"
                            class="col-2 rounded-5 border m-1 " />
                        <img alt="Verve" id="verve-lg" product-name-array="visa,mastercard,verve" src="/asset/verve.png"
                            onclick="tickProduct(this, 'verve', 'api-product-name', 'install-product', 'png');"
                            class="col-2 rounded-5 border m-1 " />
                    </div><br />
                    <form method="post" action="">
                        <input id="api-product-name" name="product-name" type="text" placeholder="Product Name" hidden
                            readonly required />
                        <select style="text-align: center;" id="" name="api-id" onchange="" class="form-control mb-1"
                            required />
                        <?php
                        //All DOLLARCARD API
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='dollarcard'");
                        if (mysqli_num_rows($get_api_lists) >= 1) {
                            echo '<option value="" default hidden selected>Choose API</option>';
                            while ($api_details = mysqli_fetch_assoc($get_api_lists)) {
                                if (empty(trim($api_details["api_key"]))) {
                                    $apikey_status = "( Empty Key )";
                                } else {
                                    $apikey_status = "";
                                }

                                echo '<option value="' . $api_details["id"] . '">' . strtoupper($api_details["api_base_url"]) . ' ' . $apikey_status . '</option>';
                            }
                        } else {
                            echo '<option value="" default hidden selected>No API</option>';
                        }
                        ?>
                        </select><br />
                        <div style="text-align: center;" class="container">
                            <span id="user-status-span" class="h5" style="user-select: auto;">DOLLARCARD STATUS</span>
                        </div><br />
                        <select style="text-align: center;" id="" name="item-status" onchange=""
                            class="form-control mb-1" required />
                        <option value="" default hidden selected>Choose DOLLARCARD Status</option>
                        <option value="1">Enabled</option>
                        <option value="0">Disabled</option>
                        </select><br />
                        <button id="install-product" name="install-product" type="submit"
                            style="pointer-events: none; user-select: auto;" class="btn btn-primary col-12 mb-1">
                            INSTALL PRODUCT
                        </button><br>
                    </form>
                </div>
            </div>

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <span style="user-select: auto;" class="h4 fw-bold">INSTALLED DOLLARCARD STATUS</span><br>
                    <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                        <table style="" class="table table-responsive table-striped table-bordered"
                            title="Horizontal Scroll: Shift + Mouse Scroll Button">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Product Name</th>
                                    <th>API Route</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $item_name_array = array("visa", "mastercard", "verve");
                                foreach ($item_name_array as $products) {
                                    $items_statement .= "product_name='$products' OR ";
                                }
                                $items_statement = "(" . trim(rtrim($items_statement, " OR ")) . ")";
                                $select_item_lists = mysqli_query($connection_server, "SELECT * FROM sas_dollarcard_status WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && $items_statement");
                                if (mysqli_num_rows($select_item_lists) >= 1) {
                                    while ($list_details = mysqli_fetch_assoc($select_item_lists)) {
                                        $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='" . $list_details["api_id"] . "' && api_type='dollarcard'");
                                        if (mysqli_num_rows($select_api_lists) == 1) {
                                            $api_details = mysqli_fetch_array($select_api_lists);
                                            $api_route_web = strtoupper($api_details["api_base_url"]);
                                        } else {
                                            if (mysqli_num_rows($select_api_lists) == 0) {
                                                $api_route_web = "Invalid API Website";
                                            } else {
                                                $api_route_web = "Duplicated API Website";
                                            }
                                        }
                                        if (strtolower(itemStatus($list_details["status"])) == "enabled") {
                                            $item_status = '<span style="color: green;">' . itemStatus($list_details["status"]) . '</span>';
                                        } else {
                                            $item_status = '<span style="color: grey;">' . itemStatus($list_details["status"]) . '</span>';
                                        }

                                        echo
                                            '<tr>
                                    <td>' . strtoupper(str_replace(["-", "_"], " ", $list_details["product_name"])) . '</td><td>' . $api_route_web . '</td><td>' . $item_status . '</td>
                                </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><br />

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <span style="user-select: auto;" class="h4 fw-bold">CURRENT DOLLAR EXCHANGE</span><br>
                    <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                        <form method="post" action="">
                            <table style="" class="table table-responsive table-striped table-bordered"
                                title="Horizontal Scroll: Shift + Mouse Scroll Button">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Qty</th>
                                        <th>Currency</th>
                                        <th>Credit Amount</th>
                                        <th>Debit Amount</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $each_country_exchange_rate_array = array("ngn");

                                    foreach ($each_country_exchange_rate_array as $currency) {
                                        $dollar_exchange_rate_table = mysqli_query($connection_server, "SELECT * FROM sas_dollar_exchange_rates WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_type='dollarcard' && currency='" . $currency . "'");

                                        if ((mysqli_num_rows($dollar_exchange_rate_table) == 0)) {
                                            mysqli_query($connection_server, "INSERT INTO sas_dollar_exchange_rates (vendor_id, product_type, currency, credit_amount, debit_amount) VALUES ('" . $get_logged_admin_details["id"] . "', 'dollarcard', '" . $currency . "', '0', '0')");
                                        }
                                    }

                                    foreach ($each_country_exchange_rate_array as $currency) {
                                        $dollar_exchange_rate_table = mysqli_query($connection_server, "SELECT * FROM sas_dollar_exchange_rates WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_type='dollarcard' && currency='" . $currency . "'");

                                        if ((mysqli_num_rows($dollar_exchange_rate_table) == 1)) {
                                            while ($dollar_exchange_rate_details = mysqli_fetch_assoc($dollar_exchange_rate_table)) {
                                                echo
                                                    '<tr style="background-color: transparent !important;">
                                                <td style="">
                                                    1 USD
                                                </td>
                                                <td style="">
                                                    ' . strtoupper($currency) . '
                                                    <input style="text-align: center;" name="currency[]" type="text" value="' . $dollar_exchange_rate_details["currency"] . '" hidden readonly required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" name="credit-amount[]" type="text" value="' . $dollar_exchange_rate_details["credit_amount"] . '" placeholder="Amount" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" name="debit-amount[]" type="text" value="' . $dollar_exchange_rate_details["debit_amount"] . '" placeholder="Amount" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="form-control mb-1" required/>
                                                </td>
                                            </tr>';
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <button id="" name="update-exchange-rate" type="submit" style="user-select: auto;"
                                class="btn btn-primary col-12 mb-1">
                                UPDATE EXCHANGE RATE
                            </button><br>
                        </form>
                    </div>
                </div>
            </div><br />

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <span style="user-select: auto;" class="h4 fw-bold">DOLLARCARD ISSUANCE FEE</span><br>
                    <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                        <table style="" class="table table-responsive table-striped table-bordered"
                            title="Horizontal Scroll: Shift + Mouse Scroll Button">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Digit</th>
                                    <th>Mode</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input style="text-align: center;" id="price-upgrade-input" name="" onkeyup=""
                                            type="text" value="" placeholder="Amount/Percent" class="form-control mb-1"
                                            required />
                                    </td>
                                    <td>
                                        <select style="text-align: center;" id="price-upgrade-type" name="" onchange=""
                                            class="form-control mb-1" required />
                                        <option value="" default hidden selected>Choose Update Type</option>
                                        <option value="amount+">Amount Increase</option>
                                        <option value="amount-">Amount Decrease</option>
                                        <option value="percent+">Percentage Increase</option>
                                        <option value="percent-">Percentage Decrease</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button onclick="upgradeePriceDiscount();" type="button"
                                            style="user-select: auto;" class="btn btn-primary col-12 mb-1">
                                            SAVE
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <form method="post" action="">
                            <table style="" class="table table-responsive table-striped table-bordered"
                                title="Horizontal Scroll: Shift + Mouse Scroll Button">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Smart Earner ($)</th>
                                        <th>Agent Vendor ($)</th>
                                        <th>API Vendor ($)</th>
                                        <th>Desc</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $item_name_array_2 = array("mastercard", "visa", "verve");
                                    foreach ($item_name_array_2 as $products) {
                                        $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_dollarcard_status WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$products'"));
                                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='dollarcard'");
                                        $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                                        $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$products' LIMIT 1"));
                                        $product_smart_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[1] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");
                                        $product_agent_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[2] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");
                                        $product_api_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[3] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");

                                        if ((mysqli_num_rows($get_api_lists) == 1) && (mysqli_num_rows($product_smart_table) > 0) && (mysqli_num_rows($product_agent_table) > 0) && (mysqli_num_rows($product_api_table) > 0)) {
                                            while (($product_smart_details = mysqli_fetch_assoc($product_smart_table)) && ($product_agent_details = mysqli_fetch_assoc($product_agent_table)) && ($product_api_details = mysqli_fetch_assoc($product_api_table))) {
                                                echo
                                                    '<tr style="background-color: transparent !important;">
                                                <td style="">
                                                    ' . strtoupper($products . " DOLLARCARD " . str_replace(["_", "-"], " ", $product_smart_details["val_1"])) . '
                                                    <input style="text-align: center;" name="api-id[]" type="text" value="' . $product_smart_details["api_id"] . '" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-id[]" type="text" value="' . $product_smart_details["product_id"] . '" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-code-1[]" type="text" value="' . $product_smart_details["val_1"] . '" hidden readonly required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_dollarcard_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_smart_level" name="smart-price[]" type="text" value="' . $product_smart_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_dollarcard_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_agent_level" name="agent-price[]" type="text" value="' . $product_agent_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_dollarcard_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_api_level" name="api-price[]" type="text" value="' . $product_api_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_dollarcard_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_desc" name="product-desc[]" type="text" value="' . $product_api_details["val_3"] . '" placeholder="Desc (Optional)" pattern="[0-9a-zA-Z.]{1,}" title="Description" class="form-control mb-1" />
                                                </td>
                                            </tr>';
                                                $csv_price_level_array[] = strtolower(trim($products)) . '_dollarcard_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . "," . $product_smart_details["val_2"] . "," . $product_agent_details["val_2"] . "," . $product_api_details["val_2"] . "," . $product_api_details["val_3"];
                                            }
                                        } else {

                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <button id="" name="update-price" type="submit" style="user-select: auto;"
                                class="btn btn-primary col-12 mb-1">
                                UPDATE FEE
                            </button><br>
                        </form>
                    </div>
                </div>
            </div><br />

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <span style="user-select: auto;" class="h4 fw-bold">DOLLARCARD FUNDING FEE</span><br>
                    <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                        <form method="post" action="">
                            <table style="" class="table table-responsive table-striped table-bordered"
                                title="Horizontal Scroll: Shift + Mouse Scroll Button">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Smart Earner ($)</th>
                                        <th>Agent Vendor ($)</th>
                                        <th>API Vendor ($)</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $item_name_array_3 = array("mastercard", "visa", "verve");
                                    foreach ($item_name_array_3 as $products) {
                                        $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_dollarcard_status WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$products'"));
                                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='dollarcard'");
                                        $account_level_table_name_arrays = array(1 => "sas_smart_card_funding_parameter_values", 2 => "sas_agent_card_funding_parameter_values", 3 => "sas_api_card_funding_parameter_values");
                                        $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$products' LIMIT 1"));
                                        $product_smart_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[1] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");
                                        $product_agent_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[2] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");
                                        $product_api_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[3] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");

                                        if ((mysqli_num_rows($get_api_lists) == 1) && (mysqli_num_rows($product_smart_table) > 0) && (mysqli_num_rows($product_agent_table) > 0) && (mysqli_num_rows($product_api_table) > 0)) {
                                            while (($product_smart_details = mysqli_fetch_assoc($product_smart_table)) && ($product_agent_details = mysqli_fetch_assoc($product_agent_table)) && ($product_api_details = mysqli_fetch_assoc($product_api_table))) {
                                                echo
                                                    '<tr style="background-color: transparent !important;">
                                                <td style="">
                                                    ' . strtoupper($products . " DOLLARCARD " . str_replace(["_", "-"], " ", $product_smart_details["val_1"])) . '
                                                    <input style="text-align: center;" name="api-id[]" type="text" value="' . $product_smart_details["api_id"] . '" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-id[]" type="text" value="' . $product_smart_details["product_id"] . '" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-code-1[]" type="text" value="' . $product_smart_details["val_1"] . '" hidden readonly required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_dollarcard_funding_fee_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_smart_level" name="smart-price[]" type="text" value="' . $product_smart_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_dollarcard_funding_fee_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_agent_level" name="agent-price[]" type="text" value="' . $product_agent_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_dollarcard_funding_fee_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_api_level" name="api-price[]" type="text" value="' . $product_api_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                            </tr>';
                                                $csv_price_level_array[] = strtolower(trim($products)) . '_dollarcard_funding_fee_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . "," . $product_smart_details["val_2"] . "," . $product_agent_details["val_2"] . "," . $product_api_details["val_2"] . "," . $product_api_details["val_3"];
                                            }
                                        } else {

                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <button id="" name="update-funding-fee" type="submit" style="user-select: auto;"
                                class="btn btn-primary col-12 mb-1">
                                UPDATE FEE
                            </button><br>
                        </form>
                    </div>
                </div>
            </div><br />

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <span style="user-select: auto;" class="h4 fw-bold">DOLLARCARD TRANSACTION FEE</span><br>
                    <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                        <form method="post" action="">
                            <table style="" class="table table-responsive table-striped table-bordered"
                                title="Horizontal Scroll: Shift + Mouse Scroll Button">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Smart Earner ($)</th>
                                        <th>Agent Vendor ($)</th>
                                        <th>API Vendor ($)</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $item_name_array_3 = array("mastercard", "visa", "verve");
                                    foreach ($item_name_array_3 as $products) {
                                        $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_dollarcard_status WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$products'"));
                                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='dollarcard'");
                                        $account_level_table_name_arrays = array(1 => "sas_smart_card_transaction_parameter_values", 2 => "sas_agent_card_transaction_parameter_values", 3 => "sas_api_card_transaction_parameter_values");
                                        $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$products' LIMIT 1"));
                                        $product_smart_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[1] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");
                                        $product_agent_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[2] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");
                                        $product_api_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[3] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");

                                        if ((mysqli_num_rows($get_api_lists) == 1) && (mysqli_num_rows($product_smart_table) > 0) && (mysqli_num_rows($product_agent_table) > 0) && (mysqli_num_rows($product_api_table) > 0)) {
                                            while (($product_smart_details = mysqli_fetch_assoc($product_smart_table)) && ($product_agent_details = mysqli_fetch_assoc($product_agent_table)) && ($product_api_details = mysqli_fetch_assoc($product_api_table))) {
                                                echo
                                                    '<tr style="background-color: transparent !important;">
                                                <td style="">
                                                    ' . strtoupper($products . " DOLLARCARD " . str_replace(["_", "-"], " ", $product_smart_details["val_1"])) . '
                                                    <input style="text-align: center;" name="api-id[]" type="text" value="' . $product_smart_details["api_id"] . '" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-id[]" type="text" value="' . $product_smart_details["product_id"] . '" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-code-1[]" type="text" value="' . $product_smart_details["val_1"] . '" hidden readonly required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_dollarcard_transaction_fee_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_smart_level" name="smart-price[]" type="text" value="' . $product_smart_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_dollarcard_transaction_fee_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_agent_level" name="agent-price[]" type="text" value="' . $product_agent_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_dollarcard_transaction_fee_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_api_level" name="api-price[]" type="text" value="' . $product_api_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                            </tr>';
                                                $csv_price_level_array[] = strtolower(trim($products)) . '_dollarcard_transaction_fee_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . "," . $product_smart_details["val_2"] . "," . $product_agent_details["val_2"] . "," . $product_api_details["val_2"] . "," . $product_api_details["val_3"];
                                            }
                                        } else {

                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <button id="" name="update-transaction-fee" type="submit" style="user-select: auto;"
                                class="btn btn-primary col-12 mb-1">
                                UPDATE FEE
                            </button><br>
                        </form>
                    </div>
                </div>
            </div><br />

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <span style="user-select: auto;" class="h4 fw-bold">FILL PRICE TABLE USING CSV</span><br>
                    <div style="user-select: auto; cursor: grab;"
                        class="container col-12 border rounded-2 px-5 py-3 lh-lg py-5">
                        <form method="post" enctype="multipart/form-data" action="">
                            <input style="text-align: center;" id="csv-chooser" type="file" accept=""
                                class="form-control mb-1" required /><br />
                            <button onclick="getCSVDetails('5');" type="button" style="user-select: auto;"
                                class="btn btn-primary col-12 mb-1">
                                PROCESS
                            </button>
                        </form>

                        <a onclick='downloadFile(`<?php echo implode("\n", $csv_price_level_array); ?>`, "dollarcard.csv");'
                            style="text-decoration: underline; user-select: auto;" class="h5 text-danger mt-3">Download
                            Price CSV</a><br />

                    </div>
                </div>
            </div><br />

        </div>
        </div>
    </section>
    <?php include("../func/bc-admin-footer.php"); ?>

</body>

</html>