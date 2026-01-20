<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
function handle_product_actions($connection_server, $get_logged_admin_details) {
    if (isset($_POST["enable-product"]) || isset($_POST["disable-product"]) || isset($_POST["delete-product"])) {
        $product_name = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["product-name"]))));
        $product_type = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["product-type"]))));

        $response_message = "";

        if (empty($product_name)) {
            $response_message = "Product Name Field Empty";
        }

        if (empty($response_message)) {
            $status_table_name = "sas_" . str_replace('-', '_', $product_type) . "_status";

            if (isset($_POST["enable-product"]) || isset($_POST["disable-product"])) {
                $item_status = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["item-status"])));
                if (is_numeric($item_status) && in_array($item_status, array("0", "1"))) {
                    mysqli_query($connection_server, "UPDATE $status_table_name SET status='$item_status' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                    $response_message = "Product Updated Successfully";
                } else {
                    $response_message = "Invalid Product Status";
                }
            }

            if (isset($_POST["delete-product"])) {
                $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");
                $select_product_details = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                if (mysqli_num_rows($select_product_details) == 1) {
                    $get_product_details = mysqli_fetch_array($select_product_details);
                    $product_id = $get_product_details["id"];
                    mysqli_query($connection_server, "DELETE FROM $status_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                    foreach ($account_level_table_name_arrays as $account_level_table_name) {
                        mysqli_query($connection_server, "DELETE FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_id='$product_id'");
                    }
                    $response_message = "Product Deleted Successfully";
                } else {
                    $response_message = "Product Details Not Found";
                }
            }
        }

        $_SESSION["product_purchase_response"] = $response_message;
        header("Location: " . $_SERVER["REQUEST_URI"]);
        exit();
    }
}

function render_action_buttons($product_name, $product_type, $status) {
    if ($status == 1) {
        $action_btn = '
            <form method="post" action="">
                <input name="product-name" type="text" value="' . $product_name . '" hidden readonly required/>
                <input name="product-type" type="text" value="' . $product_type . '" hidden readonly required/>
                <input name="item-status" type="text" value="0" hidden readonly required/>
                <button name="disable-product" type="submit" class="btn btn-warning btn-sm">DISABLE</button>
            </form>
        ';
    } else {
        $action_btn = '
            <form method="post" action="">
                <input name="product-name" type="text" value="' . $product_name . '" hidden readonly required/>
                <input name="product-type" type="text" value="' . $product_type . '" hidden readonly required/>
                <input name="item-status" type="text" value="1" hidden readonly required/>
                <button name="enable-product" type="submit" class="btn btn-success btn-sm">ENABLE</button>
            </form>
        ';
    }

    return '
        <div class="btn-group">
            ' . $action_btn . '
            <form method="post" action="">
                <input name="product-name" type="text" value="' . $product_name . '" hidden readonly required/>
                <input name="product-type" type="text" value="' . $product_type . '" hidden readonly required/>
                <button name="delete-product" type="submit" class="btn btn-danger btn-sm">DELETE</button>
            </form>
        </div>
    ';
}

function install_product($connection_server, $get_logged_admin_details, $product_type, $status_table_name, $products_array, $product_varieties = array(), $has_extra_tables = false, $extra_tables_1 = array(), $extra_tables_2 = array()) {
    $api_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-id"])));
    $item_status = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["item-status"])));
    $product_name = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["product-name"]))));
    $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");

    if (!empty($api_id) && is_numeric($api_id)) {
        if (!empty($product_name) && in_array($product_name, $products_array)) {
            if (is_numeric($item_status) && in_array($item_status, array("0", "1"))) {
                $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id' && api_type='$product_type'");
                if (mysqli_num_rows($select_api_lists) == 1) {
                    $select_status_lists = mysqli_query($connection_server, "SELECT * FROM $status_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                    if (mysqli_num_rows($select_status_lists) == 0) {
                        mysqli_query($connection_server, "INSERT INTO $status_table_name (vendor_id, api_id, product_name, status) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '$product_name', '$item_status')");
                    } else {
                        mysqli_query($connection_server, "UPDATE $status_table_name SET api_id='$api_id', status='$item_status' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                    }

                    $all_tables = array_merge(array($account_level_table_name_arrays), ($has_extra_tables ? array($extra_tables_1, $extra_tables_2) : array()));

                    foreach ($all_tables as $tables) {
                        foreach ($tables as $account_level_table_name) {
                            $select_product_details = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                            if (mysqli_num_rows($select_product_details) == 1) {
                                $get_product_details = mysqli_fetch_array($select_product_details);
                                $product_id = $get_product_details["id"];
                                $product_variety_list = isset($product_varieties[$product_name]) ? $product_varieties[$product_name] : array("1");
                                foreach ($product_variety_list as $product_val_1) {
                                    $product_val_1 = trim($product_val_1);
                                    $product_pricing_table = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='$api_id' && product_id='$product_id' && val_1='$product_val_1'");
                                    if (mysqli_num_rows($product_pricing_table) == 0) {
                                        mysqli_query($connection_server, "INSERT INTO $account_level_table_name (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '$product_id', '$product_val_1', '0', '0')");
                                    }
                                }
                            }
                        }
                    }
                    $response_message = "Product Updated Successfully";
                } else {
                    $response_message = "API Doesnt Exists";
                }
            } else {
                $response_message = "Invalid Product Status";
            }
        } else {
            $response_message = "Invalid Product Name";
        }
    } else {
        $response_message = "Invalid API ID";
    }

    $_SESSION["product_purchase_response"] = $response_message;
    header("Location: " . $_SERVER["REQUEST_URI"]);
    exit();
}
?>