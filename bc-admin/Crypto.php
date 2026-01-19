<?php session_start();
include("../func/bc-admin-config.php");

if (isset($_POST["update-key"])) {
    $api_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-id"])));
    $apikey = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-key"])));
    $apistatus = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-status"])));

    if (!empty($api_id) && is_numeric($api_id)) {
        if (!empty($apikey)) {
            if (is_numeric($apistatus) && in_array($apistatus, array("0", "1"))) {
                $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id' && api_type='crypto'");
                if (mysqli_num_rows($select_api_lists) == 1) {
                    mysqli_query($connection_server, "UPDATE sas_apis SET api_key='$apikey', status='$apistatus' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id' && api_type='crypto'");
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

include_once("../func/bc-product-actions.php");
handle_product_actions($connection_server, $get_logged_admin_details);

if (isset($_POST["install-product"])) {

    $api_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-id"])));
    $item_status = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["item-status"])));
    $product_name = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["product-name"]))));
    $products_array = array("ngn", "usd", "gbp", "cad", "eur", "btc", "eth", "doge", "usdt", "usdc", "sol", "ada", "trx");
    $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");
    $exchange_fee_account_level_table_name_arrays = array("sas_smart_exchange_fee_parameter_values", "sas_agent_exchange_fee_parameter_values", "sas_api_exchange_fee_parameter_values");

    $product_varieties = array(
        "ngn" => array("interac"),
        "cad" => array("interac"),
        "usdt" => array("eth", "trx", "bsc"),
        "usdc" => array("eth", "matic", "avaxc"),
        "btc" => array("btc"),
        "eth" => array("eth"),
        "doge" => array("doge"),
        "sol" => array("sol"),
        "ada" => array("ada"),
        "trx" => array("trx")
    );

    $product_exchange_fee_varieties = array(
        "ngn" => array("1"),
        "usd" => array("1"),
        "gbp" => array("1"),
        "cad" => array("1"),
        "eur" => array("1"),
        "btc" => array("1"),
        "eth" => array("1"),
        "doge" => array("1"),
        "usdt" => array("1"),
        "usdc" => array("1"),
        "sol" => array("1"),
        "ada" => array("1"),
        "trx" => array("1")
    );

    if (!empty($api_id) && is_numeric($api_id)) {
        if (!empty($product_name)) {
            if (in_array($product_name, $products_array)) {
                if (is_numeric($item_status) && in_array($item_status, array("0", "1"))) {
                    $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id' && api_type='crypto'");
                    $select_crypto_status_lists = mysqli_query($connection_server, "SELECT * FROM sas_crypto_status WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                    if (mysqli_num_rows($select_api_lists) == 1) {
                        if (mysqli_num_rows($select_crypto_status_lists) == 0) {
                            mysqli_query($connection_server, "INSERT INTO sas_crypto_status (vendor_id, api_id, product_name, status) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '$product_name', '$item_status')");
                        } else {
                            mysqli_query($connection_server, "UPDATE sas_crypto_status SET api_id='$api_id', status='$item_status' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
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
                                $select_all_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='crypto'");
                                $product_array_string_name = $product_name;
                                $current_varieties = $product_varieties[$product_array_string_name];
                                if (is_array($current_varieties)) {
                                    $count_product_varieties = count($current_varieties);
                                    if ($count_product_varieties >= 1) {
                                        foreach ($current_varieties as $product_val_1) {
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
                        }

                        foreach ($exchange_fee_account_level_table_name_arrays as $account_level_table_name) {
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
                                $select_all_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='crypto'");
                                $product_array_string_name = $product_name;
                                $current_varieties = $product_exchange_fee_varieties[$product_array_string_name];
                                if (is_array($current_varieties)) {
                                    $count_product_varieties = count($current_varieties);
                                    if ($count_product_varieties >= 1) {
                                        foreach ($current_varieties as $product_val_1) {
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
                    //Invalid Crypto Status
                    $json_response_array = array("desc" => "Invalid Crypto Status");
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

if (isset($_POST["update-exchange"])) {
    $api_id_array = $_POST["api-id"];
    $product_id_array = $_POST["product-id"];
    $product_code_1_array = $_POST["product-code-1"];
    $smart_price_array = $_POST["smart-price"];
    $agent_price_array = $_POST["agent-price"];
    $api_price_array = $_POST["api-price"];
    $account_level_table_name_arrays = array("sas_smart_exchange_fee_parameter_values", "sas_agent_exchange_fee_parameter_values", "sas_api_exchange_fee_parameter_values");
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
        //Exchange Fee Updated Successfully
        $json_response_array = array("desc" => "Exchange Fee Updated Successfully");
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
    $smart_price_array = $_POST["smart-price"];
    $agent_price_array = $_POST["agent-price"];
    $api_price_array = $_POST["api-price"];
    $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");
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

$csv_price_level_array = [];
$csv_price_level_array[] = "product_name,smart_level,agent_level,api_level";

?>
<!DOCTYPE html>

<head>
    <title>Crypto API | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
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
        <h1>CRYPTO API</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Crypto</li>
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
                        //All Crypto API
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='crypto'");
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
                        <img alt="Ngn" id="ngn-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/ngn.jpg"
                            onclick="tickProduct(this, 'ngn', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1  " />
                        <img alt="Usd" id="usd-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/usd.jpg"
                            onclick="tickProduct(this, 'usd', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1 " />
                        <img alt="Gbp" id="gbp-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/gbp.jpg"
                            onclick="tickProduct(this, 'gbp', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1 " />
                        <img alt="Cad" id="cad-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/cad.jpg"
                            onclick="tickProduct(this, 'cad', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1 " />
                        <img alt="Eur" id="eur-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/eur.jpg"
                            onclick="tickProduct(this, 'eur', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1 " />
                        <img alt="Btc" id="btc-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/btc.jpg"
                            onclick="tickProduct(this, 'btc', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1 " />
                        <img alt="Eth" id="eth-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/eth.jpg"
                            onclick="tickProduct(this, 'eth', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1 " />
                        <img alt="Doge" id="doge-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/doge.jpg"
                            onclick="tickProduct(this, 'doge', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1 " />
                        <img alt="Usdt" id="usdt-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/usdt.jpg"
                            onclick="tickProduct(this, 'usdt', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1 " />
                        <img alt="Usdc" id="usdc-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/usdc.jpg"
                            onclick="tickProduct(this, 'usdc', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1 " />
                        <img alt="Sol" id="sol-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/sol.jpg"
                            onclick="tickProduct(this, 'sol', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1 " />
                        <img alt="Ada" id="ada-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/ada.jpg"
                            onclick="tickProduct(this, 'ada', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1 " />
                        <img alt="Trx" id="trx-lg"
                            product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                            src="/asset/trx.jpg"
                            onclick="tickProduct(this, 'trx', 'api-product-name', 'install-product', 'jpg');"
                            class="col-2 rounded-5 border m-1 " />
                    </div><br />
                    <form method="post" action="">
                        <input id="api-product-name" name="product-name" type="text" placeholder="Product Name" hidden
                            readonly required />
                        <select style="text-align: center;" id="" name="api-id" onchange="" class="form-control mb-1"
                            required />
                        <?php
                        //All Crypto API
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='crypto'");
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
                            <span id="user-status-span" class="h5" style="user-select: auto;">CRYPTO STATUS</span>
                        </div><br />
                        <select style="text-align: center;" id="" name="item-status" onchange=""
                            class="form-control mb-1" required />
                        <option value="" default hidden selected>Choose Crypto Status</option>
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
                    <span style="user-select: auto;" class="h4 fw-bold">INSTALLED CRYPTO STATUS</span><br>
                    <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                        <table style="" class="table table-responsive table-striped table-bordered"
                            title="Horizontal Scroll: Shift + Mouse Scroll Button">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Product Name</th>
                                    <th>API Route</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $item_name_array = array("ngn", "usd", "gbp", "cad", "eur", "btc", "eth", "doge", "usdt", "usdc", "sol", "ada", "trx");
                                foreach ($item_name_array as $products) {
                                    $items_statement .= "product_name='$products' OR ";
                                }
                                $items_statement = "(" . trim(rtrim($items_statement, " OR ")) . ")";
                                $select_item_lists = mysqli_query($connection_server, "SELECT * FROM sas_crypto_status WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && $items_statement");
                                if (mysqli_num_rows($select_item_lists) >= 1) {
                                    while ($list_details = mysqli_fetch_assoc($select_item_lists)) {
                                        $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='" . $list_details["api_id"] . "' && api_type='crypto'");
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
                                    <td>' . render_action_buttons($list_details["product_name"], "crypto", $list_details["status"]) . '</td>
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
                    <span style="user-select: auto;" class="h4 fw-bold">CRYPTO EXCHANGE FEE</span><br>
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
                                        <th>Smart Earner</th>
                                        <th>Agent Vendor</th>
                                        <th>API Vendor</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $item_name_array_2 = array("ngn", "usd", "gbp", "cad", "eur", "btc", "eth", "doge", "usdt", "usdc", "sol", "ada", "trx");
                                    foreach ($item_name_array_2 as $products) {
                                        $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_crypto_status WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$products'"));
                                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='crypto'");
                                        $account_level_table_name_arrays = array(1 => "sas_smart_exchange_fee_parameter_values", 2 => "sas_agent_exchange_fee_parameter_values", 3 => "sas_api_exchange_fee_parameter_values");
                                        $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$products' LIMIT 1"));
                                        $product_smart_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[1] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");
                                        $product_agent_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[2] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");
                                        $product_api_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[3] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");

                                        if ((mysqli_num_rows($get_api_lists) == 1) && (mysqli_num_rows($product_smart_table) > 0) && (mysqli_num_rows($product_agent_table) > 0) && (mysqli_num_rows($product_api_table) > 0)) {
                                            while (($product_smart_details = mysqli_fetch_assoc($product_smart_table)) && ($product_agent_details = mysqli_fetch_assoc($product_agent_table)) && ($product_api_details = mysqli_fetch_assoc($product_api_table))) {
                                                echo
                                                    '<tr style="background-color: transparent !important;">
                                                <td style="">
                                                    ' . strtoupper($products . " " . str_replace(["_", "-"], " ", $product_smart_details["val_1"])) . '
                                                    <input style="text-align: center;" name="api-id[]" type="text" value="' . $product_smart_details["api_id"] . '" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-id[]" type="text" value="' . $product_smart_details["product_id"] . '" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-code-1[]" type="text" value="' . $product_smart_details["val_1"] . '" hidden readonly required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_smart_level" name="smart-price[]" type="text" value="' . $product_smart_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_agent_level" name="agent-price[]" type="text" value="' . $product_agent_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_api_level" name="api-price[]" type="text" value="' . $product_api_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                            </tr>';
                                                $csv_price_level_array[] = strtolower(trim($products)) . '_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . "," . $product_smart_details["val_2"] . "," . $product_agent_details["val_2"] . "," . $product_api_details["val_2"];
                                            }
                                        } else {

                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <button id="" name="update-exchange" type="submit" style="user-select: auto;"
                                class="button-box h5 outline-none color-2 bg-7 m-inline-block-dp s-inline-block-dp outline-none onhover-bg-color-5 br-radius-5px br-width-4 br-color-4 m-width-80 s-width-62 m-float-rt m-clr-float-both s-float-rt s-clr-float-both m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-margin-rt-2 s-margin-rt-2 m-margin-bm-1 s-margin-bm-1">
                                UPDATE FEE
                            </button><br>
                        </form>
                    </div>
                </div>
            </div><br />

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <span style="user-select: auto;" class="h4 fw-bold">CRYPTO TRANSFER FEE</span><br>
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
                                        <th>Smart Earner</th>
                                        <th>Agent Vendor</th>
                                        <th>API Vendor</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $item_name_array_3 = array("ngn", "usd", "gbp", "cad", "eur", "btc", "eth", "doge", "usdt", "usdc", "sol", "ada", "trx");
                                    foreach ($item_name_array_3 as $products) {
                                        $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_crypto_status WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$products'"));
                                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='crypto'");
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
                                                    ' . strtoupper($products . " " . str_replace(["_", "-"], " ", $product_smart_details["val_1"])) . '
                                                    <input style="text-align: center;" name="api-id[]" type="text" value="' . $product_smart_details["api_id"] . '" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-id[]" type="text" value="' . $product_smart_details["product_id"] . '" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-code-1[]" type="text" value="' . $product_smart_details["val_1"] . '" hidden readonly required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_smart_level" name="smart-price[]" type="text" value="' . $product_smart_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_agent_level" name="agent-price[]" type="text" value="' . $product_agent_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . '_api_level" name="api-price[]" type="text" value="' . $product_api_details["val_2"] . '" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                            </tr>';
                                                $csv_price_level_array[] = strtolower(trim($products)) . '_' . str_replace(["_", "-"], "_", $product_smart_details["val_1"]) . "," . $product_smart_details["val_2"] . "," . $product_agent_details["val_2"] . "," . $product_api_details["val_2"];
                                            }
                                        } else {

                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <button id="" name="update-price" type="submit" style="user-select: auto;"
                                class="button-box h5 outline-none color-2 bg-7 m-inline-block-dp s-inline-block-dp outline-none onhover-bg-color-5 br-radius-5px br-width-4 br-color-4 m-width-80 s-width-62 m-float-rt m-clr-float-both s-float-rt s-clr-float-both m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-margin-rt-2 s-margin-rt-2 m-margin-bm-1 s-margin-bm-1">
                                UPDATE PRICE
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
                            <button onclick="getCSVDetails('4');" type="button" style="user-select: auto;"
                                class="btn btn-primary col-12 mb-1">
                                PROCESS
                            </button>
                        </form>
                    </div><br />

                    <a onclick='downloadFile(`<?php echo implode("\n", $csv_price_level_array); ?>`, "crypto.csv");'
                        style="text-decoration: underline; user-select: auto;" class="h5 text-danger mt-3">Download
                        Price CSV</a>
                </div>
            </div>
        </div>
        </div>
    </section>
    <?php include("../func/bc-admin-footer.php"); ?>

</body>

</html>