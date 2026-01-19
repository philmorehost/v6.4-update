<?php session_start();
include("../func/bc-admin-config.php");

// Ensure intl-airtime exists in sas_products
$check_product = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_admin_details["id"]."' AND product_name='intl-airtime'");
if (mysqli_num_rows($check_product) == 0) {
    mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('".$get_logged_admin_details["id"]."', 'intl-airtime', '1')");
}

// Create tables if not exists
mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_intl_airtime_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT,
    api_id INT,
    product_name VARCHAR(50),
    status INT DEFAULT 1
)");

mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_intl_airtime_operators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT,
    country_code VARCHAR(10),
    country_name VARCHAR(100),
    product_type_id INT,
    product_type_name VARCHAR(100),
    operator_id VARCHAR(50),
    operator_name VARCHAR(100),
    status INT DEFAULT 1,
    smart_discount VARCHAR(10) DEFAULT '1',
    agent_discount VARCHAR(10) DEFAULT '1',
    api_discount VARCHAR(10) DEFAULT '1'
)");

// Check if columns exist and add them if not
$check_product_type_id = mysqli_query($connection_server, "SHOW COLUMNS FROM sas_intl_airtime_operators LIKE 'product_type_id'");
if (mysqli_num_rows($check_product_type_id) == 0) {
    mysqli_query($connection_server, "ALTER TABLE sas_intl_airtime_operators ADD COLUMN product_type_id INT AFTER country_name");
}
$check_product_type_name = mysqli_query($connection_server, "SHOW COLUMNS FROM sas_intl_airtime_operators LIKE 'product_type_name'");
if (mysqli_num_rows($check_product_type_name) == 0) {
    mysqli_query($connection_server, "ALTER TABLE sas_intl_airtime_operators ADD COLUMN product_type_name VARCHAR(100) AFTER product_type_id");
}

if(isset($_POST["update-key"])){
    $api_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-id"])));
    $apikey = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-key"])));
    $apistatus = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-status"])));

    if(!empty($api_id) && is_numeric($api_id)){
        if(!empty($apikey)){
            if(is_numeric($apistatus) && in_array($apistatus, array("0", "1"))){
                $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='$api_id' && api_type='intl-airtime'");
                if(mysqli_num_rows($select_api_lists) == 1){
                    mysqli_query($connection_server, "UPDATE sas_apis SET api_key='$apikey', status='$apistatus' WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='$api_id' && api_type='intl-airtime'");
                    //APIkey Updated Successfully
                    $json_response_array = array("desc" => "APIkey Updated Successfully");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    //API Doesnt Exists
                    $json_response_array = array("desc" => "API Doesnt Exists");
                    $json_response_encode = json_encode($json_response_array,true);
                }
            }else{
                //Invalid API Status
                $json_response_array = array("desc" => "Invalid API Status");
                $json_response_encode = json_encode($json_response_array,true);
            }
        }else{
            //Apikey Field Empty
            $json_response_array = array("desc" => "Apikey Field Empty");
            $json_response_encode = json_encode($json_response_array,true);
        }
    }else{
        //Invalid Apikey Website
        $json_response_array = array("desc" => "Invalid Apikey Website");
        $json_response_encode = json_encode($json_response_array,true);
    }
    $json_response_decode = json_decode($json_response_encode,true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: ".$_SERVER["REQUEST_URI"]);
    exit();
}

if (isset($_POST["sync-vtpass"])) {
    @set_time_limit(0);
    $api_id = mysqli_real_escape_string($connection_server, $_POST["api-id"]);
    $get_api_details = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE id='$api_id' LIMIT 1");
    if (mysqli_num_rows($get_api_details) == 1) {
        $api_detail = mysqli_fetch_array($get_api_details);

        $curl_http_headers = array(
            "Authorization: Basic ".base64_encode($api_detail["api_key"]),
        );

        // Fetch Countries
        $curl_url = "https://vtpass.com/api/get-international-airtime-countries";
        $curl_request = curl_init($curl_url);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
        $curl_result = curl_exec($curl_request);
        $curl_json_result = json_decode($curl_result, true);
        curl_close($curl_request);

        if (isset($curl_json_result["content"]["countries"])) {
            foreach ($curl_json_result["content"]["countries"] as $country) {
                $country_code = $country["code"];
                $country_name = mysqli_real_escape_string($connection_server, $country["name"]);

                // Fetch Product Types for each country
                $curl_pt_url = "https://vtpass.com/api/get-international-airtime-product-types?code=$country_code";
                $curl_pt_request = curl_init($curl_pt_url);
                curl_setopt($curl_pt_request, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_pt_request, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl_pt_request, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl_pt_request, CURLOPT_HTTPHEADER, $curl_http_headers);
                $curl_pt_result = curl_exec($curl_pt_request);
                $curl_pt_json_result = json_decode($curl_pt_result, true);
                curl_close($curl_pt_request);

                if (isset($curl_pt_json_result["content"])) {
                    foreach ($curl_pt_json_result["content"] as $product_type) {
                        $product_type_id = $product_type["product_type_id"];
                        $product_type_name = mysqli_real_escape_string($connection_server, $product_type["name"]);

                        // Fetch Operators for each country/product type
                        $curl_op_url = "https://vtpass.com/api/get-international-airtime-operators?code=$country_code&product_type_id=$product_type_id";
                        $curl_op_request = curl_init($curl_op_url);
                        curl_setopt($curl_op_request, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl_op_request, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($curl_op_request, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl_op_request, CURLOPT_HTTPHEADER, $curl_http_headers);
                        $curl_op_result = curl_exec($curl_op_request);
                        $curl_op_json_result = json_decode($curl_op_result, true);
                        curl_close($curl_op_request);

                        if (isset($curl_op_json_result["content"])) {
                            foreach ($curl_op_json_result["content"] as $operator) {
                                $operator_id = $operator["operator_id"];
                                $operator_name = mysqli_real_escape_string($connection_server, $operator["name"]);

                                $check = mysqli_query($connection_server, "SELECT * FROM sas_intl_airtime_operators WHERE vendor_id='".$get_logged_admin_details["id"]."' AND country_code='$country_code' AND product_type_id='$product_type_id' AND operator_id='$operator_id'");
                                if (mysqli_num_rows($check) == 0) {
                                    mysqli_query($connection_server, "INSERT INTO sas_intl_airtime_operators (vendor_id, country_code, country_name, product_type_id, product_type_name, operator_id, operator_name, smart_discount, agent_discount, api_discount) VALUES ('".$get_logged_admin_details["id"]."', '$country_code', '$country_name', '$product_type_id', '$product_type_name', '$operator_id', '$operator_name', '1', '1', '1')");
                                } else {
                                    mysqli_query($connection_server, "UPDATE sas_intl_airtime_operators SET country_name='$country_name', product_type_name='$product_type_name', operator_name='$operator_name' WHERE vendor_id='".$get_logged_admin_details["id"]."' AND country_code='$country_code' AND product_type_id='$product_type_id' AND operator_id='$operator_id'");
                                }
                            }
                        }
                    }
                }
            }
            $_SESSION["product_purchase_response"] = "Countries and Networks synced successfully";
        } else {
            $_SESSION["product_purchase_response"] = "Failed to fetch from VTPASS: " . ($curl_json_result["response_description"] ?? "Unknown Error");
        }
    }
    header("Location: " . $_SERVER["REQUEST_URI"]);
    exit();
}

if (isset($_POST["bulk-update-discount"])) {
    $discount_type = mysqli_real_escape_string($connection_server, $_POST["discount-type"]);
    $discount_value = mysqli_real_escape_string($connection_server, $_POST["discount-value"]);

    if (is_numeric($discount_value)) {
        if ($discount_type == "smart") {
            mysqli_query($connection_server, "UPDATE sas_intl_airtime_operators SET smart_discount='$discount_value' WHERE vendor_id='".$get_logged_admin_details["id"]."'");
        } else if ($discount_type == "agent") {
            mysqli_query($connection_server, "UPDATE sas_intl_airtime_operators SET agent_discount='$discount_value' WHERE vendor_id='".$get_logged_admin_details["id"]."'");
        } else if ($discount_type == "api") {
            mysqli_query($connection_server, "UPDATE sas_intl_airtime_operators SET api_discount='$discount_value' WHERE vendor_id='".$get_logged_admin_details["id"]."'");
        }
        $_SESSION["product_purchase_response"] = "Bulk update successful for " . ucfirst($discount_type) . " level";
    } else {
        $_SESSION["product_purchase_response"] = "Invalid discount value";
    }
    header("Location: " . $_SERVER["REQUEST_URI"]);
    exit();
}

if (isset($_POST["update-operators"])) {
    $ids = $_POST["id"];
    $statuses = $_POST["status"];
    $smarts = $_POST["smart_discount"];
    $agents = $_POST["agent_discount"];
    $apis = $_POST["api_discount"];

    foreach ($ids as $index => $id) {
        $id = mysqli_real_escape_string($connection_server, $id);
        $status = mysqli_real_escape_string($connection_server, $statuses[$index]);
        $smart = mysqli_real_escape_string($connection_server, $smarts[$index]);
        $agent = mysqli_real_escape_string($connection_server, $agents[$index]);
        $api = mysqli_real_escape_string($connection_server, $apis[$index]);

        mysqli_query($connection_server, "UPDATE sas_intl_airtime_operators SET status='$status', smart_discount='$smart', agent_discount='$agent', api_discount='$api' WHERE id='$id' AND vendor_id='".$get_logged_admin_details["id"]."'");
    }
    $_SESSION["product_purchase_response"] = "Updated successfully";
    header("Location: " . $_SERVER["REQUEST_URI"]);
    exit();
}

include_once("../func/bc-product-actions.php");
handle_product_actions($connection_server, $get_logged_admin_details);

if(isset($_POST["install-product"])){
    $products_array = array("intl-airtime");
    install_product($connection_server, $get_logged_admin_details, "intl-airtime", "sas_intl_airtime_status", $products_array);
}

?>
<!DOCTYPE html>
<head>
    <title>Int'l Airtime Management | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets-2/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include("../func/bc-admin-header.php"); ?>
    <div class="pagetitle">
      <h1>INTERNATIONAL AIRTIME MANAGEMENT</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">International Airtime</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
            <span style="user-select: auto;" class="h4 fw-bold">API SETTING</span><br>
            <form method="post" action="">
                <select style="text-align: center;" id="web-api-id" name="api-id" onchange="getWebApikey(this);" class="form-control mb-1" required/>
                    <?php
                        //All Airtime API
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_type='intl-airtime'");
                        if(mysqli_num_rows($get_api_lists) >= 1){
                            echo '<option value="" default hidden selected>Choose API</option>';
                            while($api_details = mysqli_fetch_assoc($get_api_lists)){
				if(empty(trim($api_details["api_key"]))){
					$apikey_status = "( Empty Key )";
				}else{
					$apikey_status = "";
				}

                                $selected = (strpos(strtolower($api_details["api_base_url"]), 'vtpass.com') !== false) ? 'selected' : '';
                                echo '<option value="'.$api_details["id"].'" api-key="'.$api_details["api_key"].'" api-status="'.$api_details["status"].'" '.$selected.'>'.strtoupper($api_details["api_base_url"]).' '.$apikey_status.'</option>';
                            }
                        }else{
                            echo '<option value="" default hidden selected>No API</option>';
                        }
                    ?>
                </select><br/>
                <select style="text-align: center;" id="web-apikey-status" name="api-status" onchange="" class="form-control mb-1" required/>
                    <option value="" default hidden selected>Choose API Status</option>
                    <option value="1" >Enabled</option>
                    <option value="0" >Disabled</option>
                </select><br/>
                <input style="text-align: center;" id="web-apikey-input" name="api-key" onkeyup="" type="text" value="" placeholder="Api Key" class="form-control mb-1" required/><br/>
                <button name="update-key" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                    UPDATE KEY
                </button><br>
                <div style="text-align: center;" class="container">
                    <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                </div><br/>
            </form>
          </div>
        </div>

        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
            <span style="user-select: auto;" class="h4 fw-bold">PRODUCT INSTALLATION</span><br>
            <div style="text-align: center; user-select: auto;" class="container">
                <img alt="Int'l Airtime" id="intl-airtime-lg" product-name-array="intl-airtime" src="/asset/intl-airtime.png" onclick="tickProduct(this, 'intl-airtime', 'api-product-name', 'install-product', 'png');" class="col-2 rounded-5 border m-1  "/>
            </div><br/>
            <form method="post" action="">
                <input id="api-product-name" name="product-name" type="text" placeholder="Product Name" hidden readonly required/>
                <select style="text-align: center;" id="product-api-id" name="api-id" onchange="" class="form-control mb-1" required/>
                    <?php
                        //All Airtime API
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_type='intl-airtime'");
                        if(mysqli_num_rows($get_api_lists) >= 1){
                            echo '<option value="" default hidden selected>Choose API</option>';
                            while($api_details = mysqli_fetch_assoc($get_api_lists)){
				if(empty(trim($api_details["api_key"]))){
					$apikey_status = "( Empty Key )";
				}else{
					$apikey_status = "";
				}

                                $selected = (strpos(strtolower($api_details["api_base_url"]), 'vtpass.com') !== false) ? 'selected' : '';
                                echo '<option value="'.$api_details["id"].'" '.$selected.'>'.strtoupper($api_details["api_base_url"]).' '.$apikey_status.'</option>';
                            }
                        }else{
                            echo '<option value="" default hidden selected>No API</option>';
                        }
                    ?>
                </select><br/>
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">INTERNATIONAL AIRTIME STATUS</span>
                </div><br/>
                <select style="text-align: center;" id="" name="item-status" onchange="" class="form-control mb-1" required/>
                    <option value="" default hidden selected>Choose Status</option>
                    <option value="1" >Enabled</option>
                    <option value="0" >Disabled</option>
                </select><br/>
                <button id="install-product" name="install-product" type="submit" style="pointer-events: none; user-select: auto;" class="btn btn-primary col-12 mb-1" >
                    INSTALL PRODUCT
                </button><br>
            </form>
          </div>
        </div>
        <script>
            window.addEventListener('DOMContentLoaded', (event) => {
                var apiSelect = document.getElementById('web-api-id');
                if (apiSelect && apiSelect.value !== "") {
                    getWebApikey(apiSelect);
                }
            });
        </script>

        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
            <span style="user-select: auto;" class="h4 fw-bold">INSTALLED CATEGORY STATUS</span><br>
            <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
              <table style="" class="table table-responsive table-striped table-bordered">
                  <thead class="thead-dark">
                    <tr>
                        <th>Product Name</th><th>API Route</th><th>Status</th><th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                        $select_item_lists = mysqli_query($connection_server, "SELECT * FROM sas_intl_airtime_status WHERE vendor_id='".$get_logged_admin_details["id"]."' AND product_name='intl-airtime'");
                        if(mysqli_num_rows($select_item_lists) >= 1){
                            while($list_details = mysqli_fetch_assoc($select_item_lists)){
                                $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='".$list_details["api_id"]."' && api_type='intl-airtime'");
                                if(mysqli_num_rows($select_api_lists) == 1){
                                    $api_details = mysqli_fetch_array($select_api_lists);
                                    $api_route_web = strtoupper($api_details["api_base_url"]);
                                }else{
                                    $api_route_web = "Invalid API";
                                }
                                if($list_details["status"] == 1){
                                    $item_status = '<span style="color: green;">Enabled</span>';
                                }else{
                                    $item_status = '<span style="color: grey;">Disabled</span>';
                                }

                                echo
                                '<tr>
                                    <td>'.strtoupper(str_replace(["-","_"], " ", $list_details["product_name"])).'</td><td>'.$api_route_web.'</td><td>'.$item_status.'</td>
                                    <td>'.render_action_buttons($list_details["product_name"], "intl-airtime", $list_details["status"]).'</td>
                                </tr>';
                            }
                        }
                    ?>
                  </tbody>
                </table>
            </div>
          </div>
        </div>

        <div class="card info-card px-5 py-5">
            <h5 class="card-title">Sync from VTPASS</h5>
            <form method="post" action="">
                <div class="mb-3">
                    <label class="form-label">Select VTPASS API Route</label>
                    <select name="api-id" class="form-select" required>
                        <?php
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_type='intl-airtime'");
                        while($api = mysqli_fetch_assoc($get_api_lists)){
                            echo '<option value="'.$api["id"].'">'.strtoupper($api["api_base_url"]).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <button name="sync-vtpass" type="submit" class="btn btn-primary w-100">SYNC COUNTRIES & OPERATORS</button>
            </form>
        </div>

        <div class="card info-card px-5 py-5">
            <h5 class="card-title">Bulk Discount Update</h5>
            <div class="row">
                <div class="col-md-4 mb-3 border-end">
                    <h6>Smart User</h6>
                    <form method="post" action="">
                        <input type="hidden" name="discount-type" value="smart">
                        <div class="input-group">
                            <input type="number" step="0.01" name="discount-value" class="form-control" placeholder="Discount (%)" required>
                            <button name="bulk-update-discount" type="submit" class="btn btn-primary">Update All</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 mb-3 border-end">
                    <h6>Agent Vendor</h6>
                    <form method="post" action="">
                        <input type="hidden" name="discount-type" value="agent">
                        <div class="input-group">
                            <input type="number" step="0.01" name="discount-value" class="form-control" placeholder="Discount (%)" required>
                            <button name="bulk-update-discount" type="submit" class="btn btn-warning">Update All</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>API Vendor</h6>
                    <form method="post" action="">
                        <input type="hidden" name="discount-type" value="api">
                        <div class="input-group">
                            <input type="number" step="0.01" name="discount-value" class="form-control" placeholder="Discount (%)" required>
                            <button name="bulk-update-discount" type="submit" class="btn btn-success">Update All</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card info-card px-5 py-5">
            <h5 class="card-title">Manage Countries & Operators</h5>
            <form method="post" action="">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Product Type</th>
                                <th>Operator</th>
                                <th>Status</th>
                                <th>Smart Disc (%)</th>
                                <th>Agent Disc (%)</th>
                                <th>API Disc (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ops = mysqli_query($connection_server, "SELECT * FROM sas_intl_airtime_operators WHERE vendor_id='".$get_logged_admin_details["id"]."' ORDER BY country_name ASC, product_type_name ASC");
                            while($row = mysqli_fetch_assoc($ops)){
                                ?>
                                <tr>
                                    <td><?php echo $row["country_name"]; ?> (<?php echo $row["country_code"]; ?>)</td>
                                    <td><?php echo $row["product_type_name"]; ?></td>
                                    <td><?php echo $row["operator_name"]; ?>
                                        <input type="hidden" name="id[]" value="<?php echo $row["id"]; ?>">
                                    </td>
                                    <td>
                                        <select name="status[]" class="form-select form-select-sm">
                                            <option value="1" <?php if($row["status"]==1) echo "selected"; ?>>Enabled</option>
                                            <option value="0" <?php if($row["status"]==0) echo "selected"; ?>>Disabled</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="smart_discount[]" value="<?php echo $row["smart_discount"]; ?>" class="form-control form-control-sm"></td>
                                    <td><input type="text" name="agent_discount[]" value="<?php echo $row["agent_discount"]; ?>" class="form-control form-control-sm"></td>
                                    <td><input type="text" name="api_discount[]" value="<?php echo $row["api_discount"]; ?>" class="form-control form-control-sm"></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <button name="update-operators" type="submit" class="btn btn-success w-100 mt-3">SAVE CHANGES</button>
            </form>
        </div>
    </section>
    <?php include("../func/bc-admin-footer.php"); ?>
</body>
</html>