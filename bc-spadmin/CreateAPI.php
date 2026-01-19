<?php session_start();
    include("../func/bc-spadmin-config.php");
    
    $product_type_array = array("crypto", "airtime", "intl-airtime", "shared-data", "sme-data", "cg-data", "dd-data", "betting", "datacard", "rechargecard", "nairacard", "dollarcard", "electric", "cable", "exam", "bulk-sms");
    $api_status_array = array(1 => "Public", 2 => "Private");

    if(isset($_POST["create-api"])){
        $type = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["type"]))));
        $status = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["status"])));
        $desc = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["desc"])));
        $price = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/","",trim(strip_tags(strtolower($_POST["price"])))));
        $unrefined_website_url = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["website-url"]))));
        $refined_website_url = trim(str_replace(["https","http",":/","/","www."," "],"",$unrefined_website_url));
        $website_url = $refined_website_url;
        if(in_array($type, $product_type_array) && in_array($status, array_keys($api_status_array)) && !empty($type) && in_array($type, $product_type_array) && !empty($status) && in_array($status, array_keys($api_status_array)) && !empty($price) && is_numeric($price) && !empty($website_url)){
            $check_api_details = mysqli_query($connection_server, "SELECT * FROM sas_api_marketplace_listings WHERE api_website='$website_url' && api_type='$type'");

            if(mysqli_num_rows($check_api_details) == 0){
                mysqli_query($connection_server, "INSERT INTO `sas_api_marketplace_listings` (api_website, api_type, price, description, status) VALUES ('$website_url', '$type', '$price', '$desc','$status')");
		        //API Created Successfully
                $json_response_array = array("desc" => "API Created Successfully");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(mysqli_num_rows($check_api_details) == 1){
                    //API Already Exists
                    $json_response_array = array("desc" => "API Already Exists");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(mysqli_num_rows($check_api_details) > 1){
                        //Duplicated Details, Contact Admin
                        $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }
            }
        }else{
            if(!in_array($type, $product_type_array)){
                //Acceptable API Type are (...)
                $json_response_array = array("desc" => "Acceptable API Type are (".implode(", ", $product_type_array).")");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(!in_array($status, array_keys($api_status_array))){
                    //Invalid API Status Code
                    $json_response_array = array("desc" => "Invalid API Status Code");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(empty($type)){
                        //API Type Field Empty
                        $json_response_array = array("desc" => "API Type Field Empty");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                        if(!in_array($type, $product_type_array)){
                            //Invalid API Type
                            $json_response_array = array("desc" => "Invalid API Type");
                            $json_response_encode = json_encode($json_response_array,true);
                        }else{
                            if(empty($status)){
                                //API Status Field Empty
                                $json_response_array = array("desc" => "API Status Field Empty");
                                $json_response_encode = json_encode($json_response_array,true);
                            }else{
                                if(!in_array($status, array_keys($api_status_array))){
                                    //Invalid API Status
                                    $json_response_array = array("desc" => "Invalid API Status");
                                    $json_response_encode = json_encode($json_response_array,true);
                                }else{
                                    if(empty($price)){
                                        //Price Field Empty
                                        $json_response_array = array("desc" => "Price Field Empty");
                                        $json_response_encode = json_encode($json_response_array,true);
                                    }else{
                                        if(!is_numeric($price)){
                                            //Non-numeric Price
                                            $json_response_array = array("desc" => "Non-numeric Price");
                                            $json_response_encode = json_encode($json_response_array,true);
                                        }else{
                                            if(empty($website_url)){
                                                //Website Url Field Empty
                                                $json_response_array = array("desc" => "Website Url Field Empty");
                                                $json_response_encode = json_encode($json_response_array,true);
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

        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
    
?>
<!DOCTYPE html>
<head>
    <title></title>
    <meta charset="UTF-8" />
    <meta name="description" content="" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="<?php echo $get_all_super_admin_site_details["primary_color"] ?? "#198754"; ?>" />
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
    <?php include("../func/bc-spadmin-header.php"); ?>    
    
    <div class="pagetitle">
      <h1>CREATE API</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Create  API</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
        
    <div class="card info-card px-5 py-5">
        <form method="post" enctype="multipart/form-data" action="">
            <div style="text-align: center;" class="container">
                <img src="<?php echo $web_http_host; ?>/asset/developer-icon.png" class="col-2" style="pointer-events: none; user-select: auto; filter: invert(1);"/>
            </div><br/>
            <div style="text-align: center;" class="container">
                <span id="api-status-span" class="h5" style="user-select: auto;">API INFORMATION</span>
            </div><br/>
            <select style="text-align: center;" id="" name="type" onchange="" class="form-control mb-1" required/>
                <option value="" default hidden selected>Choose API Type</option>
                <?php
                    foreach($product_type_array as $type){
                        echo '<option value="'.strtolower(trim($type)).'" >'.str_replace(["_","-"]," ",strtoupper($type)).'</option>';
                    }
                ?>
            </select><br/>
            <select style="text-align: center;" id="" name="status" onchange="" class="form-control mb-1" required/>
                <option value="" default hidden selected>Choose API Status</option>
                <?php
                    foreach($api_status_array as $status_code => $status_text){
                        echo '<option value="'.strtolower(trim($status_code)).'" >'.strtoupper($status_text).'</option>';
                    }
                ?>
            </select><br/>
            <div style="text-align: center;" class="container">
                <span id="api-status-span" class="h5" style="user-select: auto;">API PRICE</span>
            </div><br/>
            <input style="text-align: center;" name="price" type="number" value="" placeholder="Price" step="0.0001" min="1" class="form-control mb-1" required/><br/>
            <div style="text-align: center;" class="container">
                <span id="api-status-span" class="h5" style="user-select: auto;">WEBSITE URL</span>
            </div><br/>
            <input style="text-align: center;" name="website-url" type="url" value="https://" placeholder="Website Url" class="form-control mb-1" required/><br/>
            <div style="text-align: center;" class="container">
                <span id="api-status-span" class="h5" style="user-select: auto;">DESCRIPTION</span>
            </div><br/>
            <textarea style="text-align: center; resize: none;" id="" name="desc" onkeyup="" placeholder="Description (Optional)" class="form-control mb-1" rows="10"></textarea><br/>
            
            <button name="create-api" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                CREATE API
            </button><br>
        </form>
    </div>
  </div>
</section>
    <?php include("../func/bc-spadmin-footer.php"); ?>
    
</body>
</html>