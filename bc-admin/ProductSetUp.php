<?php session_start();
include("../func/bc-admin-config.php");

$crypto_array = array("ngn", "usd", "gbp", "cad", "eur", "btc", "eth", "doge", "usdt", "usdc", "sol", "ada", "trx");
$telecoms_array = array("mtn", "airtel", "glo", "9mobile", "intl-airtime");
$cable_array = array("startimes", "dstv", "gotv", "showmax");
$card_array = array("mastercard", "visa", "verve");
$exam_array = array("waec", "neco", "nabteb", "jamb");
$electric_array = array("ekedc", "eedc", "ikedc", "jedc", "kedco", "ibedc", "phed", "aedc", "yedc", "bedc", "kaedco", "aba");
$betting_array = array("msport", "naijabet", "nairabet", "bet9ja-agent", "betland", "betlion", "supabet", "bet9ja", "bangbet", "betking", "1xbet", "betway", "merrybet", "mlotto", "western-lotto", "hallabet", "green-lotto");
$products_array = array_merge($crypto_array, $telecoms_array, $cable_array, $card_array, $exam_array, $electric_array, $betting_array);

if (isset($_POST["install-all-product"])) {
    $all_product_status = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["all-product-status"])));
    foreach ($products_array as $product_name) {
        if (is_numeric($all_product_status) && in_array($all_product_status, array("0", "1"))) {
            $product_status = $all_product_status;
        } else {
            $product_status = 1;
        }

        $select_product_lists = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
        if (mysqli_num_rows($select_product_lists) == 0) {
            mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('" . $get_logged_admin_details["id"] . "', '$product_name', '$product_status')");
        } else {
            mysqli_query($connection_server, "UPDATE sas_products SET status='$product_status' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
        }
    }
    //All Product Installed Successfully
    $json_response_array = array("desc" => "All Product Installed Successfully");
    $_SESSION["product_purchase_response"] = $json_response_array["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}


if (isset($_POST["update-product"])) {
    $product_status = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["product-status"])));
    $product_name = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["product-name"]))));
    $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");
    $product_variety = array();
    if (!empty($product_name)) {
        if (in_array($product_name, $products_array)) {
            if (is_numeric($product_status) && in_array($product_status, array("0", "1"))) {
                $select_product_lists = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                if (mysqli_num_rows($select_product_lists) == 0) {
                    mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('" . $get_logged_admin_details["id"] . "', '$product_name', '$product_status')");
                } else {
                    mysqli_query($connection_server, "UPDATE sas_products SET status='$product_status' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                }
                //Product Status Updated Successfully
                $json_response_array = array("desc" => "Product Status Updated Successfully");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                //Invalid Product Status
                $json_response_array = array("desc" => "Invalid Product Status");
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

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}
?>
<!DOCTYPE html>

<head>
    <title>Product Function | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
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
        <h1>PRODUCT SETTINGS</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Product Settings</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="col-12">


            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <div style="text-align: center;" class="container">
                        <img src="<?php echo $web_http_host; ?>/asset/installation-icon.png" class="col-2 mb-5"
                            style="pointer-events: none; user-select: auto; filter: invert(1);" /><br />
                        <span style="user-select: auto;" class="h4 fw-bold">PRE
                            INSTALL ALL PRODUCTS</span><br>
                        <form method="post" action="">
                            <select style="text-align: center;" id="" name="all-product-status" onchange=""
                                class="form-control mb-1 mt-3" required />
                            <option value="" default hidden selected>Choose All Product Status</option>
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                            </select><br />
                            <button id="install-all-product" name="install-all-product"
                                onclick="javascript: if(confirm('Want to Pre-Install all product?')){this.type='submit';}"
                                type="button" style="user-select: auto;" class="btn btn-primary col-12 mb-1">
                                SAVE CHANGES
                            </button><br>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <div style="text-align: center;" class="container">

                        <span style="user-select: auto;" class="h4 fw-bold">CRYPTO
                            PRODUCT STATUS</span><br>
                        <div style="text-align: center; user-select: auto;" class="container">
                            <img alt="Ngn" id="ngn-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/ngn.jpg"
                                onclick="tickProduct(this, 'ngn', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Usd" id="usd-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/usd.jpg"
                                onclick="tickProduct(this, 'usd', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Gbp" id="gbp-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/gbp.jpg"
                                onclick="tickProduct(this, 'gbp', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Cad" id="cad-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/cad.jpg"
                                onclick="tickProduct(this, 'cad', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Eur" id="eur-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/eur.jpg"
                                onclick="tickProduct(this, 'eur', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Btc" id="btc-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/btc.jpg"
                                onclick="tickProduct(this, 'btc', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Eth" id="eth-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/eth.jpg"
                                onclick="tickProduct(this, 'eth', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Doge" id="doge-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/doge.jpg"
                                onclick="tickProduct(this, 'doge', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Usdt" id="usdt-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/usdt.jpg"
                                onclick="tickProduct(this, 'usdt', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Usdc" id="usdc-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/usdc.jpg"
                                onclick="tickProduct(this, 'usdc', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Sol" id="sol-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/sol.jpg"
                                onclick="tickProduct(this, 'sol', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Ada" id="ada-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/ada.jpg"
                                onclick="tickProduct(this, 'ada', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Trx" id="trx-lg"
                                product-name-array="ngn, usd, gbp, cad, eur, btc, eth, doge, usdt, usdc, sol, ada, trx"
                                src="/asset/trx.jpg"
                                onclick="tickProduct(this, 'trx', 'api-crypto-name', 'install-crypto', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                        </div><br />
                        <form method="post" action="">
                            <input id="api-crypto-name" name="product-name" type="text" placeholder="Product Name"
                                hidden readonly required />
                            <div style="text-align: center;" class="h5 fw-bold">
                                <span id="user-status-span" class="a-cursor" style="user-select: auto;">ALL PRODUCT
                                    STATUS</span>
                            </div><br />
                            <select style="text-align: center;" id="" name="product-status" onchange=""
                                class="form-control mb-1" required />
                            <option value="" default hidden selected>Choose Product Status</option>
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                            </select><br />
                            <button id="install-crypto" name="update-product" type="submit"
                                style="pointer-events: none; user-select: auto;" class="btn btn-primary col-12 mb-5">
                                UPDATE STATUS
                            </button><br>
                        </form>

                        <span style="user-select: auto;" class="h4 fw-bold">INSTALLED
                            CRYPTO STATUS</span><br>
                        <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                            <table style="" class="table table-responsive table-striped table-bordered"
                                title="Horizontal Scroll: Shift + Mouse Scroll Button">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    function cryptoFunc()
                                    {
                                        global $connection_server;
                                        global $get_logged_admin_details;
                                        $product_name_array = array("ngn", "usd", "gbp", "cad", "eur", "btc", "eth", "doge", "usdt", "usdc", "sol", "ada", "trx");
                                        foreach ($product_name_array as $products) {
                                            $products_statement .= "product_name='$products' ";
                                        }
                                        $products_statement = trim($products_statement);
                                        $products_statement = str_replace(" ", " OR ", $products_statement);
                                        $select_product_lists = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && ($products_statement)");
                                        if (mysqli_num_rows($select_product_lists) >= 1) {
                                            while ($list_details = mysqli_fetch_assoc($select_product_lists)) {
                                                if (strtolower(itemStatus($list_details["status"])) == "enabled") {
                                                    $item_status = '<span style="color: green;">' . itemStatus($list_details["status"]) . '</span>';
                                                } else {
                                                    $item_status = '<span style="color: grey;">' . itemStatus($list_details["status"]) . '</span>';
                                                }

                                                $product_tr_return .=
                                                    '<tr>
                                            <td>' . strtoupper(str_replace(["-", "_"], " ", $list_details["product_name"])) . '</td><td>' . $item_status . '</td>
                                        </tr>';
                                            }
                                        }
                                        return $product_tr_return;
                                    }

                                    echo cryptoFunc();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><br />

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <div style="text-align: center;" class="container">

                        <span style="user-select: auto;" class="h4 fw-bold">TELECOMS
                            PRODUCT STATUS</span><br>
                        <div style="text-align: center; user-select: auto;" class="container">
                            <img alt="Airtel" id="airtel-lg" product-name-array="mtn,airtel,glo,9mobile"
                                src="/asset/airtel.png"
                                onclick="tickProduct(this, 'airtel', 'api-telecoms-name', 'install-telecoms', 'png');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="MTN" id="mtn-lg" product-name-array="mtn,airtel,glo,9mobile" src="/asset/mtn.png"
                                onclick="tickProduct(this, 'mtn', 'api-telecoms-name', 'install-telecoms', 'png');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Glo" id="glo-lg" product-name-array="mtn,airtel,glo,9mobile" src="/asset/glo.png"
                                onclick="tickProduct(this, 'glo', 'api-telecoms-name', 'install-telecoms', 'png');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="9mobile" id="9mobile-lg" product-name-array="mtn,airtel,glo,9mobile,intl-airtime"
                                src="/asset/9mobile.png"
                                onclick="tickProduct(this, '9mobile', 'api-telecoms-name', 'install-telecoms', 'png');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Int'l Airtime" id="intl-airtime-lg" product-name-array="mtn,airtel,glo,9mobile,intl-airtime"
                                src="/asset/intl-airtime.png"
                                onclick="tickProduct(this, 'intl-airtime', 'api-telecoms-name', 'install-telecoms', 'png');"
                                class="col-2 rounded-5 border m-1 " />
                        </div><br />
                        <form method="post" action="">
                            <input id="api-telecoms-name" name="product-name" type="text" placeholder="Product Name"
                                hidden readonly required />
                            <div style="text-align: center;" class="container mt-4">
                                <span id="user-status-span" class="h5 fw-bold" style="user-select: auto;">ALL PRODUCT
                                    STATUS</span>
                            </div><br />
                            <select style="text-align: center;" id="" name="product-status" onchange=""
                                class="form-control mb-1" required />
                            <option value="" default hidden selected>Choose Product Status</option>
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                            </select><br />
                            <button id="install-telecoms" name="update-product" type="submit"
                                style="pointer-events: none; user-select: auto;" class="btn btn-primary col-12 mb-5">
                                UPDATE STATUS
                            </button><br>
                        </form>

                        <span style="user-select: auto;" class="h4 fw-bold">INSTALLED
                            TELECOMS STATUS</span><br>
                        <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                            <table style="" class="table table-responsive table-striped table-bordered"
                                title="Horizontal Scroll: Shift + Mouse Scroll Button">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    function telecomFunc()
                                    {
                                        global $connection_server;
                                        global $get_logged_admin_details;
                                        $product_name_array = array("mtn", "airtel", "glo", "9mobile", "intl-airtime");
                                        foreach ($product_name_array as $products) {
                                            $products_statement .= "product_name='$products' ";
                                        }
                                        $products_statement = trim($products_statement);
                                        $products_statement = str_replace(" ", " OR ", $products_statement);
                                        $select_product_lists = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && ($products_statement)");
                                        if (mysqli_num_rows($select_product_lists) >= 1) {
                                            while ($list_details = mysqli_fetch_assoc($select_product_lists)) {
                                                if (strtolower(itemStatus($list_details["status"])) == "enabled") {
                                                    $item_status = '<span style="color: green;">' . itemStatus($list_details["status"]) . '</span>';
                                                } else {
                                                    $item_status = '<span style="color: grey;">' . itemStatus($list_details["status"]) . '</span>';
                                                }

                                                $product_tr_return .=
                                                    '<tr>
                                          <td>' . strtoupper(str_replace(["-", "_"], " ", $list_details["product_name"])) . '</td><td>' . $item_status . '</td>
                                      </tr>';
                                            }
                                        }
                                        return $product_tr_return;
                                    }

                                    echo telecomFunc();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><br />

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <div style="text-align: center;" class="container">

                        <span style="user-select: auto;" class="h4 fw-bold">CABLE
                            PRODUCT STATUS</span><br>
                        <div style="text-align: center; user-select: auto;" class="container">
                            <img alt="Startimes" id="startimes-lg" product-name-array="startimes,dstv,gotv,showmax"
                                src="/asset/startimes.jpg"
                                onclick="tickProduct(this, 'startimes', 'api-cable-name', 'install-cable', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Dstv" id="dstv-lg" product-name-array="startimes,dstv,gotv,showmax"
                                src="/asset/dstv.jpg"
                                onclick="tickProduct(this, 'dstv', 'api-cable-name', 'install-cable', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Gotv" id="gotv-lg" product-name-array="startimes,dstv,gotv,showmax"
                                src="/asset/gotv.jpg"
                                onclick="tickProduct(this, 'gotv', 'api-cable-name', 'install-cable', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="ShowMax" id="showmax-lg" product-name-array="startimes,dstv,gotv,showmax"
                                src="/asset/showmax.jpg"
                                onclick="tickProduct(this, 'showmax', 'api-cable-name', 'install-cable', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                        </div><br />
                        <form method="post" action="">
                            <input id="api-cable-name" name="product-name" type="text" placeholder="Product Name" hidden
                                readonly required />
                            <div style="text-align: center;" class="h5 fw-bold">
                                <span id="user-status-span" class="a-cursor" style="user-select: auto;">ALL PRODUCT
                                    STATUS</span>
                            </div><br />
                            <select style="text-align: center;" id="" name="product-status" onchange=""
                                class="form-control mb-1" required />
                            <option value="" default hidden selected>Choose Product Status</option>
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                            </select><br />
                            <button id="install-cable" name="update-product" type="submit"
                                style="pointer-events: none; user-select: auto;" class="btn btn-primary col-12 mb-5">
                                UPDATE STATUS
                            </button><br>
                        </form>

                        <span style="user-select: auto;" class="h4 fw-bold">INSTALLED
                            CABLE STATUS</span><br>
                        <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                            <table style="" class="table table-responsive table-striped table-bordered"
                                title="Horizontal Scroll: Shift + Mouse Scroll Button">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    function cableFunc()
                                    {
                                        global $connection_server;
                                        global $get_logged_admin_details;
                                        $product_name_array = array("startimes", "dstv", "gotv", "showmax");
                                        foreach ($product_name_array as $products) {
                                            $products_statement .= "product_name='$products' ";
                                        }
                                        $products_statement = trim($products_statement);
                                        $products_statement = str_replace(" ", " OR ", $products_statement);
                                        $select_product_lists = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && ($products_statement)");
                                        if (mysqli_num_rows($select_product_lists) >= 1) {
                                            while ($list_details = mysqli_fetch_assoc($select_product_lists)) {
                                                if (strtolower(itemStatus($list_details["status"])) == "enabled") {
                                                    $item_status = '<span style="color: green;">' . itemStatus($list_details["status"]) . '</span>';
                                                } else {
                                                    $item_status = '<span style="color: grey;">' . itemStatus($list_details["status"]) . '</span>';
                                                }

                                                $product_tr_return .=
                                                    '<tr>
                                            <td>' . strtoupper(str_replace(["-", "_"], " ", $list_details["product_name"])) . '</td><td>' . $item_status . '</td>
                                        </tr>';
                                            }
                                        }
                                        return $product_tr_return;
                                    }

                                    echo cableFunc();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><br />

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <div style="text-align: center;" class="container">

                        <span style="user-select: auto;" class="h4 fw-bold">VIRTUAL CARD
                            PRODUCT STATUS</span><br>
                        <div style="text-align: center; user-select: auto;" class="container">
                            <img alt="Mastercard" id="mastercard-lg" product-name-array="mastercard,visa,verve"
                                src="/asset/mastercard.png"
                                onclick="tickProduct(this, 'mastercard', 'api-card-name', 'install-card', 'png');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Visa" id="visa-lg" product-name-array="mastercard,visa,verve"
                                src="/asset/visa.png"
                                onclick="tickProduct(this, 'visa', 'api-card-name', 'install-card', 'png');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Verve" id="verve-lg" product-name-array="mastercard,visa,verve"
                                src="/asset/verve.png"
                                onclick="tickProduct(this, 'verve', 'api-card-name', 'install-card', 'png');"
                                class="col-2 rounded-5 border m-1 " />
                        </div><br />
                        <form method="post" action="">
                            <input id="api-card-name" name="product-name" type="text" placeholder="Product Name" hidden
                                readonly required />
                            <div style="text-align: center;" class="h5 fw-bold">
                                <span id="user-status-span" class="a-cursor" style="user-select: auto;">ALL PRODUCT
                                    STATUS</span>
                            </div><br />
                            <select style="text-align: center;" id="" name="product-status" onchange=""
                                class="form-control mb-1" required />
                            <option value="" default hidden selected>Choose Product Status</option>
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                            </select><br />
                            <button id="install-card" name="update-product" type="submit"
                                style="pointer-events: none; user-select: auto;" class="btn btn-primary col-12 mb-5">
                                UPDATE STATUS
                            </button><br>
                        </form>

                        <span style="user-select: auto;" class="h4 fw-bold">INSTALLED
                            VIRTUAL CARD STATUS</span><br>
                        <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                            <table style="" class="table table-responsive table-striped table-bordered"
                                title="Horizontal Scroll: Shift + Mouse Scroll Button">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    function cardFunc()
                                    {
                                        global $connection_server;
                                        global $get_logged_admin_details;
                                        $product_name_array = array("mastercard", "visa", "verve");
                                        foreach ($product_name_array as $products) {
                                            $products_statement .= "product_name='$products' ";
                                        }
                                        $products_statement = trim($products_statement);
                                        $products_statement = str_replace(" ", " OR ", $products_statement);
                                        $select_product_lists = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && ($products_statement)");
                                        if (mysqli_num_rows($select_product_lists) >= 1) {
                                            while ($list_details = mysqli_fetch_assoc($select_product_lists)) {
                                                if (strtolower(itemStatus($list_details["status"])) == "enabled") {
                                                    $item_status = '<span style="color: green;">' . itemStatus($list_details["status"]) . '</span>';
                                                } else {
                                                    $item_status = '<span style="color: grey;">' . itemStatus($list_details["status"]) . '</span>';
                                                }

                                                $product_tr_return .=
                                                    '<tr>
                                            <td>' . strtoupper(str_replace(["-", "_"], " ", $list_details["product_name"])) . '</td><td>' . $item_status . '</td>
                                        </tr>';
                                            }
                                        }
                                        return $product_tr_return;
                                    }

                                    echo cardFunc();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><br />

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <div style="text-align: center;" class="container">

                        <span style="user-select: auto;" class="h4 fw-bold">EXAM
                            PRODUCT STATUS</span><br>
                        <div style="text-align: center; user-select: auto;" class="container">
                            <img alt="Waec" id="waec-lg" product-name-array="waec,neco,nabteb,jamb"
                                src="/asset/waec.jpg"
                                onclick="tickProduct(this, 'waec', 'api-exam-name', 'install-exam', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Neco" id="neco-lg" product-name-array="waec,neco,nabteb,jamb"
                                src="/asset/neco.jpg"
                                onclick="tickProduct(this, 'neco', 'api-exam-name', 'install-exam', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Nabteb" id="nabteb-lg" product-name-array="waec,neco,nabteb,jamb"
                                src="/asset/nabteb.jpg"
                                onclick="tickProduct(this, 'nabteb', 'api-exam-name', 'install-exam', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="Jamb" id="jamb-lg" product-name-array="waec,neco,nabteb,jamb"
                                src="/asset/jamb.jpg"
                                onclick="tickProduct(this, 'jamb', 'api-exam-name', 'install-exam', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                        </div><br />
                        <form method="post" action="">
                            <input id="api-exam-name" name="product-name" type="text" placeholder="Product Name" hidden
                                readonly required />
                            <div style="text-align: center;" class="h5 fw-bold">
                                <span id="user-status-span" class="a-cursor" style="user-select: auto;">ALL PRODUCT
                                    STATUS</span>
                            </div><br />
                            <select style="text-align: center;" id="" name="product-status" onchange=""
                                class="form-control mb-1" required />
                            <option value="" default hidden selected>Choose Product Status</option>
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                            </select><br />
                            <button id="install-exam" name="update-product" type="submit"
                                style="pointer-events: none; user-select: auto;" class="btn btn-primary col-12 mb-5">
                                UPDATE STATUS
                            </button><br>
                        </form>

                        <span style="user-select: auto;" class="h4 fw-bold">INSTALLED
                            EXAM STATUS</span><br>
                        <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                            <table style="" class="table table-responsive table-striped table-bordered"
                                title="Horizontal Scroll: Shift + Mouse Scroll Button">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    function examFunc()
                                    {
                                        global $connection_server;
                                        global $get_logged_admin_details;
                                        $product_name_array = array("waec", "neco", "nabteb", "jamb");
                                        foreach ($product_name_array as $products) {
                                            $products_statement .= "product_name='$products' ";
                                        }
                                        $products_statement = trim($products_statement);
                                        $products_statement = str_replace(" ", " OR ", $products_statement);
                                        $select_product_lists = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && ($products_statement)");
                                        if (mysqli_num_rows($select_product_lists) >= 1) {
                                            while ($list_details = mysqli_fetch_assoc($select_product_lists)) {
                                                if (strtolower(itemStatus($list_details["status"])) == "enabled") {
                                                    $item_status = '<span style="color: green;">' . itemStatus($list_details["status"]) . '</span>';
                                                } else {
                                                    $item_status = '<span style="color: grey;">' . itemStatus($list_details["status"]) . '</span>';
                                                }

                                                $product_tr_return .=
                                                    '<tr>
                                            <td>' . strtoupper(str_replace(["-", "_"], " ", $list_details["product_name"])) . '</td><td>' . $item_status . '</td>
                                        </tr>';
                                            }
                                        }
                                        return $product_tr_return;
                                    }

                                    echo examFunc();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><br />

            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <div style="text-align: center;" class="container">
                        <span style="user-select: auto;" class="h4 fw-bold">ELECTRIC
                            PRODUCT STATUS</span><br>
                        <div style="text-align: center; user-select: auto;" class="container">
                            <img alt="ekedc" id="ekedc-lg"
                                product-name-array="ekedc,eedc,ikedc,jedc,kedco,ibedc,phed,aedc,yedc,bedc,kaedco,aba"
                                src="/asset/ekedc.jpg"
                                onclick="tickProduct(this, 'ekedc', 'api-electric-name', 'install-electric', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="eedc" id="eedc-lg"
                                product-name-array="ekedc,eedc,ikedc,jedc,kedco,ibedc,phed,aedc,yedc,bedc,kaedco,aba"
                                src="/asset/eedc.jpg"
                                onclick="tickProduct(this, 'eedc', 'api-electric-name', 'install-electric', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="ikedc" id="ikedc-lg"
                                product-name-array="ekedc,eedc,ikedc,jedc,kedco,ibedc,phed,aedc,yedc,bedc,kaedco,aba"
                                src="/asset/ikedc.jpg"
                                onclick="tickProduct(this, 'ikedc', 'api-electric-name', 'install-electric', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="jedc" id="jedc-lg"
                                product-name-array="ekedc,eedc,ikedc,jedc,kedco,ibedc,phed,aedc,yedc,bedc,kaedco,aba"
                                src="/asset/jedc.jpg"
                                onclick="tickProduct(this, 'jedc', 'api-electric-name', 'install-electric', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="kedco" id="kedco-lg"
                                product-name-array="ekedc,eedc,ikedc,jedc,kedco,ibedc,phed,aedc,yedc,bedc,kaedco,aba"
                                src="/asset/kedco.jpg"
                                onclick="tickProduct(this, 'kedco', 'api-electric-name', 'install-electric', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="ibedc" id="ibedc-lg"
                                product-name-array="ekedc,eedc,ikedc,jedc,kedco,ibedc,phed,aedc,yedc,bedc,kaedco,aba"
                                src="/asset/ibedc.jpg"
                                onclick="tickProduct(this, 'ibedc', 'api-electric-name', 'install-electric', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="phed" id="phed-lg"
                                product-name-array="ekedc,eedc,ikedc,jedc,kedco,ibedc,phed,aedc,yedc,bedc,kaedco,aba"
                                src="/asset/phed.jpg"
                                onclick="tickProduct(this, 'phed', 'api-electric-name', 'install-electric', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="aedc" id="aedc-lg"
                                product-name-array="ekedc,eedc,ikedc,jedc,kedco,ibedc,phed,aedc,yedc,bedc,kaedco,aba"
                                src="/asset/aedc.jpg"
                                onclick="tickProduct(this, 'aedc', 'api-electric-name', 'install-electric', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="yedc" id="yedc-lg"
                                product-name-array="ekedc,eedc,ikedc,jedc,kedco,ibedc,phed,aedc,yedc,bedc,kaedco,aba"
                                src="/asset/yedc.jpg"
                                onclick="tickProduct(this, 'yedc', 'api-electric-name', 'install-electric', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="bedc" id="bedc-lg"
                                product-name-array="ekedc,eedc,ikedc,jedc,kedco,ibedc,phed,aedc,yedc,bedc,kaedco,aba"
                                src="/asset/bedc.jpg"
                                onclick="tickProduct(this, 'bedc', 'api-electric-name', 'install-electric', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="kaedco" id="kaedco-lg"
                                product-name-array="ekedc,eedc,ikedc,jedc,kedco,ibedc,phed,aedc,yedc,bedc,kaedco,aba"
                                src="/asset/kaedco.jpg"
                                onclick="tickProduct(this, 'kaedco', 'api-electric-name', 'install-electric', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="aba" id="aba-lg"
                                product-name-array="ekedc,eedc,ikedc,jedc,kedco,ibedc,phed,aedc,yedc,bedc,kaedco,aba"
                                src="/asset/aba.jpg"
                                onclick="tickProduct(this, 'aba', 'api-electric-name', 'install-electric', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                        </div><br />
                        <form method="post" action="">
                            <input id="api-electric-name" name="product-name" type="text" placeholder="Product Name"
                                hidden readonly required />
                            <div style="text-align: center;" class="h5 fw-bold">
                                <span id="user-status-span" class="a-cursor" style="user-select: auto;">ALL PRODUCT
                                    STATUS</span>
                            </div><br />
                            <select style="text-align: center;" id="" name="product-status" onchange=""
                                class="form-control mb-1" required />
                            <option value="" default hidden selected>Choose Product Status</option>
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                            </select><br />
                            <button id="install-electric" name="update-product" type="submit"
                                style="pointer-events: none; user-select: auto;" class="btn btn-primary col-12 mb-5">
                                UPDATE STATUS
                            </button><br>
                        </form>

                        <span style="user-select: auto;" class="h4 fw-bold">INSTALLED
                            ELECTRIC STATUS</span><br>
                        <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                            <table style="" class="table table-responsive table-striped table-bordered"
                                title="Horizontal Scroll: Shift + Mouse Scroll Button">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    function electricFunc()
                                    {
                                        global $connection_server;
                                        global $get_logged_admin_details;
                                        $product_name_array = array("ekedc", "eedc", "ikedc", "jedc", "kedco", "ibedc", "phed", "aedc", "yedc", "bedc", "kaedco", "aba");
                                        foreach ($product_name_array as $products) {
                                            $products_statement .= "product_name='$products' ";
                                        }
                                        $products_statement = trim($products_statement);
                                        $products_statement = str_replace(" ", " OR ", $products_statement);
                                        $select_product_lists = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && ($products_statement)");
                                        if (mysqli_num_rows($select_product_lists) >= 1) {
                                            while ($list_details = mysqli_fetch_assoc($select_product_lists)) {
                                                if (strtolower(itemStatus($list_details["status"])) == "enabled") {
                                                    $item_status = '<span style="color: green;">' . itemStatus($list_details["status"]) . '</span>';
                                                } else {
                                                    $item_status = '<span style="color: grey;">' . itemStatus($list_details["status"]) . '</span>';
                                                }

                                                $product_tr_return .=
                                                    '<tr>
                                            <td>' . strtoupper(str_replace(["-", "_"], " ", $list_details["product_name"])) . '</td><td>' . $item_status . '</td>
                                        </tr>';
                                            }
                                        }
                                        return $product_tr_return;
                                    }

                                    echo electricFunc();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><br />


            <div class="card info-card px-5 py-5">
                <div class="row mb-3">
                    <div style="text-align: center;" class="container">

                        <span style="user-select: auto;" class="h4 fw-bold">BETTING
                            PRODUCT STATUS</span><br>
                        <div style="text-align: center; user-select: auto;" class="container">
                            <img alt="msport" id="msport-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/msport.jpg"
                                onclick="tickProduct(this, 'msport', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="naijabet" id="naijabet-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/naijabet.jpg"
                                onclick="tickProduct(this, 'naijabet', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="nairabet" id="nairabet-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/nairabet.jpg"
                                onclick="tickProduct(this, 'nairabet', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="bet9ja-agent" id="bet9ja-agent-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/bet9ja-agent.jpg"
                                onclick="tickProduct(this, 'bet9ja-agent', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="betland" id="betland-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/betland.jpg"
                                onclick="tickProduct(this, 'betland', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="betlion" id="betlion-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/betlion.jpg"
                                onclick="tickProduct(this, 'betlion', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="supabet" id="supabet-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/supabet.jpg"
                                onclick="tickProduct(this, 'supabet', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="bet9ja" id="bet9ja-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/bet9ja.jpg"
                                onclick="tickProduct(this, 'bet9ja', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="bangbet" id="bangbet-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/bangbet.jpg"
                                onclick="tickProduct(this, 'bangbet', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="betking" id="betking-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/betking.jpg"
                                onclick="tickProduct(this, 'betking', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="1xbet" id="1xbet-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/1xbet.jpg"
                                onclick="tickProduct(this, '1xbet', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="betway" id="betway-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/betway.jpg"
                                onclick="tickProduct(this, 'betway', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="merrybet" id="merrybet-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/merrybet.jpg"
                                onclick="tickProduct(this, 'merrybet', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="mlotto" id="mlotto-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/mlotto.jpg"
                                onclick="tickProduct(this, 'mlotto', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="western-lotto" id="western-lotto-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/western-lotto.jpg"
                                onclick="tickProduct(this, 'western-lotto', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="hallabet" id="hallabet-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/hallabet.jpg"
                                onclick="tickProduct(this, 'hallabet', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />

                            <img alt="green-lotto" id="green-lotto-lg"
                                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                                src="/asset/green-lotto.jpg"
                                onclick="tickProduct(this, 'green-lotto', 'api-betting-name', 'install-betting', 'jpg');"
                                class="col-2 rounded-5 border m-1 " />


                        </div><br />
                        <form method="post" action="">
                            <input id="api-betting-name" name="product-name" type="text" placeholder="Product Name"
                                hidden readonly required />
                            <div style="text-align: center;" class="h5 fw-bold">
                                <span id="user-status-span" class="a-cursor" style="user-select: auto;">ALL PRODUCT
                                    STATUS</span>
                            </div><br />
                            <select style="text-align: center;" id="" name="product-status" onchange=""
                                class="form-control mb-1" required />
                            <option value="" default hidden selected>Choose Product Status</option>
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                            </select><br />
                            <button id="install-betting" name="update-product" type="submit"
                                style="pointer-events: none; user-select: auto;" class="btn btn-primary col-12 mb-5">
                                UPDATE STATUS
                            </button><br>
                        </form>

                        <span style="user-select: auto;" class="h4 fw-bold">INSTALLED
                            BETTING STATUS</span><br>
                        <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
                            <table style="" class="table table-responsive table-striped table-bordered"
                                title="Horizontal Scroll: Shift + Mouse Scroll Button">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    function bettingFunc()
                                    {
                                        global $connection_server;
                                        global $get_logged_admin_details;
                                        $product_name_array = array("msport", "naijabet", "nairabet", "bet9ja-agent", "betland", "betlion", "supabet", "bet9ja", "bangbet", "betking", "1xbet", "betway", "merrybet", "mlotto", "western-lotto", "hallabet", "green-lotto");
                                        foreach ($product_name_array as $products) {
                                            $products_statement .= "product_name='$products' ";
                                        }
                                        $products_statement = trim($products_statement);
                                        $products_statement = str_replace(" ", " OR ", $products_statement);
                                        $select_product_lists = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && ($products_statement)");
                                        if (mysqli_num_rows($select_product_lists) >= 1) {
                                            while ($list_details = mysqli_fetch_assoc($select_product_lists)) {
                                                if (strtolower(itemStatus($list_details["status"])) == "enabled") {
                                                    $item_status = '<span style="color: green;">' . itemStatus($list_details["status"]) . '</span>';
                                                } else {
                                                    $item_status = '<span style="color: grey;">' . itemStatus($list_details["status"]) . '</span>';
                                                }

                                                $product_tr_return .=
                                                    '<tr>
                                            <td>' . strtoupper(str_replace(["-", "_"], " ", $list_details["product_name"])) . '</td><td>' . $item_status . '</td>
                                        </tr>';
                                            }
                                        }
                                        return $product_tr_return;
                                    }

                                    echo bettingFunc();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><br />



        </div>
        </div>
    </section>
    <?php include("../func/bc-admin-footer.php"); ?>

</body>

</html>