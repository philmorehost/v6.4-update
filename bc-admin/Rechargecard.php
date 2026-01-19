<?php session_start();
    include("../func/bc-admin-config.php");

    if(isset($_GET["action"]) && isset($_GET["product_id"]) && isset($_GET["val_1"]) && isset($_GET["api_id"])){
        $action = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["action"])));
        $product_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["product_id"])));
        $val_1 = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["val_1"])));
        $api_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["api_id"])));
        $pricing_tables = ['sas_smart_parameter_values', 'sas_agent_parameter_values', 'sas_api_parameter_values'];
    
        if($action == "enable" || $action == "disable"){
            $new_status = ($action == "enable") ? 1 : 0;
            foreach($pricing_tables as $table){
                mysqli_query($connection_server, "UPDATE $table SET status='$new_status' WHERE vendor_id='".$get_logged_admin_details["id"]."' && product_id='$product_id' && val_1='$val_1' && api_id='$api_id'");
            }
            $_SESSION["product_purchase_response"] = "Package status updated successfully.";
    
        } elseif($action == "delete"){
            foreach($pricing_tables as $table){
                mysqli_query($connection_server, "DELETE FROM $table WHERE vendor_id='".$get_logged_admin_details["id"]."' && product_id='$product_id' && val_1='$val_1' && api_id='$api_id'");
            }
            $_SESSION["product_purchase_response"] = "Package deleted successfully.";
    
        } else {
            $_SESSION["product_purchase_response"] = "Invalid action.";
        }
        header("Location: Rechargecard.php");
        exit();
    }
        
    if(isset($_POST["update-key"])){
        $api_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-id"])));
        $apikey = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-key"])));
        $apistatus = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-status"])));
        
        if(!empty($api_id) && is_numeric($api_id)){
            if(!empty($apikey)){
                if(is_numeric($apistatus) && in_array($apistatus, array("0", "1"))){
                    $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='$api_id' && api_type='rechargecard'");
                    if(mysqli_num_rows($select_api_lists) == 1){
                        mysqli_query($connection_server, "UPDATE sas_apis SET api_key='$apikey', status='$apistatus' WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='$api_id' && api_type='rechargecard'");
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
    }

    include_once("../func/bc-product-actions.php");
    handle_product_actions($connection_server, $get_logged_admin_details);

    if (isset($_POST["install-product"])) {
        $products_array = array("mtn", "airtel", "glo", "9mobile");
        $product_varieties = array(
            "mtn" => array("100", "200", "500"),
            "airtel" => array("100", "200", "500"),
            "glo" => array("100", "200", "500"),
            "9mobile" => array("100", "200", "500")
        );
        $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");
        install_product($connection_server, $get_logged_admin_details, 'rechargecard', 'sas_rechargecard_status', $products_array, $product_varieties, $account_level_table_name_arrays);
    }

    if(isset($_POST["update-price"])){
        $api_id_array = $_POST["api-id"];
        $product_id_array = $_POST["product-id"];
        $product_code_1_array = $_POST["product-code-1"];
        $smart_price_array = $_POST["smart-price"];
        $agent_price_array = $_POST["agent-price"];
        $api_price_array = $_POST["api-price"];
        $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");
        if(count($api_id_array) == count($product_id_array)){
            foreach($api_id_array as $index => $api_id){
                $api_id = $api_id_array[$index];
                $product_id = $product_id_array[$index];
                $product_code_1 = $product_code_1_array[$index];
                $smart_price = $smart_price_array[$index];
                $agent_price = $agent_price_array[$index];
                $api_price = $api_price_array[$index];
                $get_selected_api_list = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='$api_id'"));
                $select_api_list_with_api_type = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_type='".$get_selected_api_list["api_type"]."'");
                if(mysqli_num_rows($select_api_list_with_api_type) > 0){
                    while($refined_api_id = mysqli_fetch_assoc($select_api_list_with_api_type)){
                        $smart_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM ".$account_level_table_name_arrays[0]." WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$refined_api_id["id"]."' && product_id='$product_id' && val_1='$product_code_1'");                          
                        if(mysqli_num_rows($smart_product_pricing_table) == 0){
                            mysqli_query($connection_server, "INSERT INTO ".$account_level_table_name_arrays[0]." (vendor_id, api_id, product_id, val_1, val_2) VALUES ('".$get_logged_admin_details["id"]."', '".$refined_api_id["id"]."', '$product_id', '$product_code_1', '$smart_price')");
                        }else{
                            mysqli_query($connection_server, "UPDATE ".$account_level_table_name_arrays[0]." SET vendor_id='".$get_logged_admin_details["id"]."', api_id='".$refined_api_id["id"]."', product_id='$product_id', val_1='$product_code_1', val_2='$smart_price' WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$refined_api_id["id"]."' && product_id='$product_id' && val_1='$product_code_1'");
                        }
                        
                        $agent_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM ".$account_level_table_name_arrays[1]." WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$refined_api_id["id"]."' && product_id='$product_id' && val_1='$product_code_1'");                          
                        if(mysqli_num_rows($agent_product_pricing_table) == 0){
                            mysqli_query($connection_server, "INSERT INTO ".$account_level_table_name_arrays[1]." (vendor_id, api_id, product_id, val_1, val_2) VALUES ('".$get_logged_admin_details["id"]."', '".$refined_api_id["id"]."', '$product_id', '$product_code_1', '$agent_price')");
                        }else{
                            mysqli_query($connection_server, "UPDATE ".$account_level_table_name_arrays[1]." SET vendor_id='".$get_logged_admin_details["id"]."', api_id='".$refined_api_id["id"]."', product_id='$product_id', val_1='$product_code_1', val_2='$agent_price' WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$refined_api_id["id"]."' && product_id='$product_id' && val_1='$product_code_1'");
                        }
                        
                        $api_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM ".$account_level_table_name_arrays[2]." WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$refined_api_id["id"]."' && product_id='$product_id' && val_1='$product_code_1'");                            
                        if(mysqli_num_rows($api_product_pricing_table) == 0){
                            mysqli_query($connection_server, "INSERT INTO ".$account_level_table_name_arrays[2]." (vendor_id, api_id, product_id, val_1, val_2) VALUES ('".$get_logged_admin_details["id"]."', '".$refined_api_id["id"]."', '$product_id', '$product_code_1', '$api_price')");
                        }else{
                            mysqli_query($connection_server, "UPDATE ".$account_level_table_name_arrays[2]." SET vendor_id='".$get_logged_admin_details["id"]."', api_id='".$refined_api_id["id"]."', product_id='$product_id', val_1='$product_code_1', val_2='$api_price' WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$refined_api_id["id"]."' && product_id='$product_id' && val_1='$product_code_1'");
                        }
                    }
                }
            }
            //Price Updated Successfully
            $json_response_array = array("desc" => "Price Updated Successfully");
            $json_response_encode = json_encode($json_response_array,true);
        }else{
            //Product Connection Error
            $json_response_array = array("desc" => "Product Connection Error");
            $json_response_encode = json_encode($json_response_array,true);
        }
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }

    if(isset($_POST["upload-product"])){
        $product_name = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["isp"]))));
        $product_qty = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["qty"]))));
        $dial_code = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["dial-code"]))));
        $card_list = $_POST["cards"];
        $card_list_replace_space = str_replace(" ","",trim($card_list));
        $card_list_replace = str_replace("\r\n",",",trim($card_list_replace_space));
        $card_list_array = array_filter(explode(",",trim($card_list_replace)));
        foreach($card_list_array as $each_card){
            $card_list_alter .= $each_card."\n";
        }
        $new_card_lists = mysqli_real_escape_string($connection_server, trim(strip_tags(str_replace("\n",",",trim($card_list_alter)))));
        $products_array = array("mtn", "airtel", "glo", "9mobile");
        if(!empty($product_name)){
            if(in_array($product_name, $products_array)){
                if(!empty($product_qty)){
                    $card_name = $product_name."_".$product_qty;
                    $select_rechargecard_products = mysqli_query($connection_server, "SELECT * FROM sas_cards WHERE vendor_id='".$get_logged_admin_details["id"]."' && card_name='$card_name'");
                    if(mysqli_num_rows($select_rechargecard_products) == 0){
                        mysqli_query($connection_server, "INSERT INTO sas_cards (vendor_id, card_name, cards, dial_code) VALUES ('".$get_logged_admin_details["id"]."', '$card_name', '$new_card_lists', '$dial_code')");
                    }else{
                        mysqli_query($connection_server, "UPDATE sas_cards SET cards='$new_card_lists', dial_code='$dial_code' WHERE vendor_id='".$get_logged_admin_details["id"]."' && card_name='$card_name'");
                    }
                    //Card Uploaded Successfully
                    $json_response_array = array("desc" => ucwords($product_name)." Card Uploaded Successfully");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    //Product Quantity Field Empty
                    $json_response_array = array("desc" => "Product Quantity Field Empty");
                    $json_response_encode = json_encode($json_response_array,true);
                }
            }else{
                //Invalid Product Name
                $json_response_array = array("desc" => "Invalid Product Name");
                $json_response_encode = json_encode($json_response_array,true);
            }
        }else{
            //Product Name Field Empty
            $json_response_array = array("desc" => "Product Name Field Empty");
            $json_response_encode = json_encode($json_response_array,true);
        }
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
    
    $csv_price_level_array = [];
    $csv_price_level_array[] = "product_name,smart_level,agent_level,api_level";
    
?>
<!DOCTYPE html>
<head>
    <title>Rechargecard API | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_super_admin_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="black" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
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
      <h1>RECHARGECARD API</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Rechargecard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
          
            <span style="user-select: auto;" class="h4 fw-bold">API SETTING</span><br>
            <form method="post" action="">
                <select style="text-align: center;" id="" name="api-id" onchange="getWebApikey(this);" class="form-control mb-1" required/>
                    <?php
                        //All Rechargecard API
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_type='rechargecard'");
                        if(mysqli_num_rows($get_api_lists) >= 1){
                            echo '<option value="" default hidden selected>Choose API</option>';
                            while($api_details = mysqli_fetch_assoc($get_api_lists)){
                                if(empty(trim($api_details["api_key"]))){
                                    $apikey_status = "( Empty Key )";
                                }else{
                                    $apikey_status = "";
                                }
                                
                                echo '<option value="'.$api_details["id"].'" api-key="'.$api_details["api_key"].'" api-status="'.$api_details["status"].'">'.strtoupper($api_details["api_base_url"]).' '.$apikey_status.'</option>';
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
                <button type="button" class="btn btn-info col-12 mb-2" product-name-array="mtn,airtel,glo,9mobile" onclick="tickProduct(this, 'all', 'api-product-name', 'install-product', 'png');">SELECT ALL</button><br/>
                <img alt="Airtel" id="airtel-lg" product-name-array="mtn,airtel,glo,9mobile" src="/asset/airtel.png" onclick="tickProduct(this, 'airtel', 'api-product-name', 'install-product', 'png');" class="col-2 rounded-5 border m-1  "/>
                <img alt="MTN" id="mtn-lg" product-name-array="mtn,airtel,glo,9mobile" src="/asset/mtn.png" onclick="tickProduct(this, 'mtn', 'api-product-name', 'install-product', 'png');" class="col-2 rounded-5 border m-1 "/>
                <img alt="Glo" id="glo-lg" product-name-array="mtn,airtel,glo,9mobile" src="/asset/glo.png" onclick="tickProduct(this, 'glo', 'api-product-name', 'install-product', 'png');" class="col-2 rounded-5 border m-1 "/>
                <img alt="9mobile" id="9mobile-lg" product-name-array="mtn,airtel,glo,9mobile" src="/asset/9mobile.png" onclick="tickProduct(this, '9mobile', 'api-product-name', 'install-product', 'png');" class="col-2 rounded-5 border m-1 "/>
            </div><br/>
            <form method="post" action="">
                <input id="api-product-name" name="product-name" type="text" placeholder="Product Name" hidden readonly required/>
                <select style="text-align: center;" id="" name="api-id" onchange="" class="form-control mb-1" required/>
                    <?php
                        //All Rechargecard API
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_type='rechargecard'");
                        if(mysqli_num_rows($get_api_lists) >= 1){
                            echo '<option value="" default hidden selected>Choose API</option>';
                            while($api_details = mysqli_fetch_assoc($get_api_lists)){
                                if(empty(trim($api_details["api_key"]))){
                                    $apikey_status = "( Empty Key )";
                                }else{
                                    $apikey_status = "";
                                }
                                
                                echo '<option value="'.$api_details["id"].'">'.strtoupper($api_details["api_base_url"]).' '.$apikey_status.'</option>';
                            }
                        }else{
                            echo '<option value="" default hidden selected>No API</option>';
                        }
                    ?>
                </select><br/>
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">RECHARGECARD STATUS</span>
                </div><br/>
                <select style="text-align: center;" id="" name="item-status" onchange="" class="form-control mb-1" required/>
                    <option value="" default hidden selected>Choose Rechargecard Status</option>
                    <option value="1" >Enabled</option>
                    <option value="0" >Disabled</option>
                </select><br/>
                <button id="install-product" name="install-product" type="submit" style="pointer-events: none; user-select: auto;" class="btn btn-primary col-12 mb-1" >
                    INSTALL PRODUCT
                </button><br>
            </form>
          </div>
        </div>
        
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
            <span style="user-select: auto;" class="h4 fw-bold">INSTALLED RECHARGECARD STATUS</span><br>
            <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
              <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
                  <thead class="thead-dark">
                    <tr>
                        <th>Product Name</th><th>API Route</th><th>Status</th><th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                        $item_name_array = array("mtn", "airtel", "glo", "9mobile");
                        foreach($item_name_array as $products){
                            $items_statement .= "product_name='$products' OR ";
                        }
                        $items_statement = "(".trim(rtrim($items_statement," OR ")).")";
                        $select_item_lists = mysqli_query($connection_server, "SELECT * FROM sas_rechargecard_status WHERE vendor_id='".$get_logged_admin_details["id"]."' && $items_statement");
                        if(mysqli_num_rows($select_item_lists) >= 1){
                            while($list_details = mysqli_fetch_assoc($select_item_lists)){
                                $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='".$list_details["api_id"]."' && api_type='rechargecard'");
                                if(mysqli_num_rows($select_api_lists) == 1){
                                    $api_details = mysqli_fetch_array($select_api_lists);
                                    $api_route_web = strtoupper($api_details["api_base_url"]);
                                }else{
                                    if(mysqli_num_rows($select_api_lists) == 0){
                                        $api_route_web = "Invalid API Website";
                                    }else{
                                        $api_route_web = "Duplicated API Website";
                                    }
                                }
                                if(strtolower(itemStatus($list_details["status"])) == "enabled"){
                                    $item_status = '<span style="color: green;">'.itemStatus($list_details["status"]).'</span>';
                                }else{
                                    $item_status = '<span style="color: grey;">'.itemStatus($list_details["status"]).'</span>';
                                }
                                
                                echo 
                                '<tr>
                                    <td>'.strtoupper(str_replace(["-","_"], " ", $list_details["product_name"])).'</td><td>'.$api_route_web.'</td><td>'.$item_status.'</td>
                                    <td>'.render_action_buttons($list_details["product_name"], "rechargecard", $list_details["status"]).'</td>
                                </tr>';
                            }
                        }
                    ?>
                  </tbody>
                </table>
            </div>
          </div>
        </div><br/>
			   
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
            <span style="user-select: auto;" class="h4 fw-bold">RECHARGECARD DISCOUNT</span><br>
            <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
              <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
                <thead class="thead-dark">
                  <tr>
                      <th>Digit</th><th>Mode</th><th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                    <input style="text-align: center;" id="price-upgrade-input" name="" onkeyup="" type="text" value="" placeholder="Amount/Percent" class="form-control mb-1" required/>
                  </td>
                  <td>
                    <select style="text-align: center;" id="price-upgrade-type" name="" onchange="" class="form-control mb-1" required/>
                        <option value="" default hidden selected>Choose Update Type</option>
                        <option value="amount+" >Amount Increase</option>
                        <option value="amount-" >Amount Decrease</option>
                        <option value="percent+" >Percentage Increase</option>
                        <option value="percent-" >Percentage Decrease</option>
                    </select>
                  </td>
                  <td>
                    <button onclick="upgradeePriceDiscount();" type="button" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                      SAVE
                    </button>
                  </td>
                  </tr>
                </tbody>
              </table>
                <form method="post" action="">
                  <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
                    <thead class="thead-dark">
                      <tr>
                          <th>Product Name</th><th>Smart Earner</th><th>Agent Vendor</th><th>API Vendor</th><th>Status</th><th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        
                        <?php
                            $item_name_array_2 = array("mtn", "airtel", "glo", "9mobile");
                            foreach($item_name_array_2 as $products){
                                $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_rechargecard_status WHERE vendor_id='".$get_logged_admin_details["id"]."' && product_name='$products'"));
                                $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='".$get_item_status_details["api_id"]."' && api_type='rechargecard'");
                                $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                                $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_admin_details["id"]."' && product_name='$products' LIMIT 1"));
                                $product_smart_table = mysqli_query($connection_server, "SELECT * FROM ".$account_level_table_name_arrays[1]." WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$get_item_status_details["api_id"]."' && product_id='".$product_table["id"]."'");                         
                                $product_agent_table = mysqli_query($connection_server, "SELECT * FROM ".$account_level_table_name_arrays[2]." WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$get_item_status_details["api_id"]."' && product_id='".$product_table["id"]."'");                         
                                $product_api_table = mysqli_query($connection_server, "SELECT * FROM ".$account_level_table_name_arrays[3]." WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$get_item_status_details["api_id"]."' && product_id='".$product_table["id"]."'");                           
                                
                                if((mysqli_num_rows($get_api_lists) == 1) && (mysqli_num_rows($product_smart_table) > 0) && (mysqli_num_rows($product_agent_table) > 0) && (mysqli_num_rows($product_api_table) > 0)){
                                    while(($product_smart_details = mysqli_fetch_assoc($product_smart_table)) && ($product_agent_details = mysqli_fetch_assoc($product_agent_table)) && ($product_api_details = mysqli_fetch_assoc($product_api_table))){
                                        $status_text = ($product_smart_details['status'] == 1) ? '<span class="text-success">Enabled</span>' : '<span class="text-secondary">Disabled</span>';
                                        $actions = '';
                                        if ($product_smart_details['status'] == 1) {
                                            $actions .= '<a href="Rechargecard.php?action=disable&product_id='.$product_smart_details["product_id"].'&val_1='.$product_smart_details["val_1"].'&api_id='.$product_smart_details["api_id"].'" class="btn btn-warning btn-sm mb-1 d-block">Disable</a> ';
                                        } else {
                                            $actions .= '<a href="Rechargecard.php?action=enable&product_id='.$product_smart_details["product_id"].'&val_1='.$product_smart_details["val_1"].'&api_id='.$product_smart_details["api_id"].'" class="btn btn-success btn-sm mb-1 d-block">Enable</a> ';
                                        }
                                        $actions .= '<a href="Rechargecard.php?action=delete&product_id='.$product_smart_details["product_id"].'&val_1='.$product_smart_details["val_1"].'&api_id='.$product_smart_details["api_id"].'" class="btn btn-danger btn-sm d-block" onclick="return confirm(\'Are you sure you want to delete this package? This action cannot be undone.\');">Delete</a>';

                                        echo 
                                            '<tr style="background-color: transparent !important;">
                                                <td style="">
                                                    '.strtoupper($products." ".str_replace(["_","-"]," ",$product_smart_details["val_1"])).'
                                                    <input style="text-align: center;" name="api-id[]" type="text" value="'.$product_smart_details["api_id"].'" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-id[]" type="text" value="'.$product_smart_details["product_id"].'" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-code-1[]" type="text" value="'.$product_smart_details["val_1"].'" hidden readonly required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="'.strtolower(trim($products)).'_rechargecard_'.str_replace(["_","-"],"_",$product_smart_details["val_1"]).'_smart_level" name="smart-price[]" type="text" value="'.$product_smart_details["val_2"].'" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="'.strtolower(trim($products)).'_rechargecard_'.str_replace(["_","-"],"_",$product_smart_details["val_1"]).'_agent_level" name="agent-price[]" type="text" value="'.$product_agent_details["val_2"].'" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="'.strtolower(trim($products)).'_rechargecard_'.str_replace(["_","-"],"_",$product_smart_details["val_1"]).'_api_level" name="api-price[]" type="text" value="'.$product_api_details["val_2"].'" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>'.$status_text.'</td>
                                                <td style="min-width: 100px;">'.$actions.'</td>
                                            </tr>'; 
                                            $csv_price_level_array[] = strtolower(trim($products)).'_rechargecard_'.str_replace(["_","-"],"_",$product_smart_details["val_1"]).",".$product_smart_details["val_2"].",".$product_agent_details["val_2"].",".$product_api_details["val_2"];
                                    }
                                }else{
                                    
                                }
                            }
                        ?>
                      </tbody>
                    </table>
                    <button id="" name="update-price" type="submit" style="user-select: auto;" class="button-box h5 outline-none color-2 bg-7 m-inline-block-dp s-inline-block-dp outline-none onhover-bg-color-5 br-radius-5px br-width-4 br-color-4 m-width-80 s-width-80 m-float-rt m-clr-float-both s-float-rt s-clr-float-both m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-margin-rt-2 s-margin-rt-2 m-margin-bm-1 s-margin-bm-1" >
                        UPDATE PRICE
                    </button><br>
                </form>
            </div>
          </div>
        </div><br/>
			
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
			<span style="user-select: auto;" class="h4 fw-bold">FILL PRICE TABLE USING CSV</span><br>
			<div style="user-select: auto; cursor: grab;" class="container col-12 border rounded-2 px-5 py-3 lh-lg py-5">
				<form method="post" enctype="multipart/form-data" action="">
					<input style="text-align: center;" id="csv-chooser" type="file" accept="" class="form-control mb-1" required/><br/>
					<button onclick="getCSVDetails('4');" type="button" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
						PROCESS
					</button>
				</form>
				
				<a onclick='downloadFile(`<?php echo implode("\n",$csv_price_level_array); ?>`, "rechargecard.csv");' style="text-decoration: underline; user-select: auto;" class="h5 text-danger mt-3">Download Price CSV</a><br/>
			
			</div>
		</div>
	</div><br/>
			
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
            <span style="user-select: auto;" class="h4 fw-bold">RECHARGECARD UPLOAD</span><br>
            <form method="post" action="">
                <select style="text-align: center;" id="admin-cards-isp" name="isp" onchange="adminCardsSwitch(); adminCardsSwitchReset();" class="form-control mb-1" required/>
                    <?php
                        //All RECHARGECARD PRODUCT
                        $get_rechargecard_products = mysqli_query($connection_server, "SELECT * FROM sas_rechargecard_status WHERE vendor_id='".$get_logged_admin_details["id"]."'");
                        if(mysqli_num_rows($get_rechargecard_products) >= 1){
                            echo '<option value="" default hidden selected>Choose Product</option>';
                            while($product_details = mysqli_fetch_assoc($get_rechargecard_products)){
                                if(!empty($product_details["status"])){
                                    if(is_numeric($product_details["status"])){
                                        if(in_array($product_details["status"], array(0, 1))){
                                            if($product_details["status"] == 1){
                                                $product_status = "( Enabled )";
                                            }else{
                                                if($product_details["status"] == 0){
                                                    $product_status = "( Disabled )";
                                                }
                                            }
                                        }else{
                                            $product_status = "( Invalid Status Code )";
                                        }
                                    }else{
                                        $product_status = "( non-numeric string )";
                                    }
                                }else{
                                    $product_status = "( Status Not Available )";
                                }
                                echo '<option value="'.$product_details["product_name"].'">'.strtoupper($product_details["product_name"]).' '.$product_status.'</option>';
                            }
                        }else{
                            echo '<option value="" default hidden selected>No Product</option>';
                        }
                    ?>
                </select><br/>
                <select style="text-align: center;" id="admin-cards-qty" name="qty" onchange="adminCardsSwitch();" class="form-control mb-1" required/>
                    <option value="" default hidden selected>Choose Qty</option>
                    <?php
                        function rechargecardQty(){
                            global $connection_server, $get_logged_admin_details, $array_mtn_product_variety, $array_airtel_product_variety, $array_glo_product_variety, $array_9mobile_product_variety;
                            $item_name_array_3 = array("mtn", "airtel", "glo", "9mobile");
                            //All RECHARGECARD PRODUCT
                            $get_rechargecard_products = mysqli_query($connection_server, "SELECT * FROM sas_cards WHERE vendor_id='".$get_logged_admin_details["id"]."'");
                            foreach($item_name_array_3 as $products){
                                $product_array_string_name = "array_".$products."_product_variety";
                                $product_variety = $$product_array_string_name;
                                $count_product_variety = count($product_variety);
                                if($count_product_variety >= 1){
                                    foreach($product_variety as $product_qty){
                                        $return_product_option .= '<option value="'.$product_qty.'" product-isp="'.$products.'" hidden>'.$product_qty.'</option>';
                                    }
                                }
                            }
                            return $return_product_option;
                        }

                        echo rechargecardQty();
                    ?>
                </select><br/>
                <input style="text-align: center;" id="admin-cards-input" name="dial-code" onkeyup="" type="text" value="" placeholder="Recharge Code, Balance Code (seperated by commas)" class="form-control mb-1" required/><br/>
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">RECHARGECARD LISTS</span>
                </div><br/>
                <textarea style="text-align: center; resize: none;" id="admin-cards-textarea" name="cards" onkeyup="" placeholder="Rechargecards seperated by commas" class="form-control mb-1" rows="10"></textarea><br>
                <?php
                    function rechargecardTextarea(){
                        global $connection_server, $get_logged_admin_details, $array_mtn_product_variety, $array_airtel_product_variety, $array_glo_product_variety, $array_9mobile_product_variety;
                        $item_name_array_3 = array("mtn", "airtel", "glo", "9mobile");
                        //All RECHARGECARD PRODUCT
                        $get_rechargecard_products = mysqli_query($connection_server, "SELECT * FROM sas_cards WHERE vendor_id='".$get_logged_admin_details["id"]."'");
                        foreach($item_name_array_3 as $products){
                            $product_array_string_name = "array_".$products."_product_variety";
                            $product_variety = $$product_array_string_name;
                            $count_product_variety = count($product_variety);
                            if($count_product_variety >= 1){
                                foreach($product_variety as $product_qty){
                                    $textarea_id_name = $products."_".$product_qty;
                                    $input_id_name = $products."_".$product_qty."_dial_code";
                                    
                                    $get_rechargecard_product_cards = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_cards WHERE vendor_id='".$get_logged_admin_details["id"]."' && card_name='".$textarea_id_name."' LIMIT 1"));
                                    $return_product_textarea .= 
                                    '<textarea style="text-align: center; resize: none;" id="'.$textarea_id_name.'" name="" onkeyup="" placeholder="Rechargecards seperated by commas" class="m-none-dp s-none-dp" >'.$get_rechargecard_product_cards["cards"].'</textarea>
                                    <input style="text-align: center; resize: none;" id="'.$input_id_name.'" name="" onkeyup="" placeholder="Dial Code" class="m-none-dp s-none-dp" value="'.$get_rechargecard_product_cards["dial_code"].'" />';
                                }
                            }
                        }
                        return $return_product_textarea;
                    }

                    echo rechargecardTextarea();
                ?>
                <button id="upload-product" name="upload-product" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                    UPLOAD PRODUCT
                </button><br>
            </form>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php include("../func/bc-admin-footer.php"); ?>
    
</body>
</html>