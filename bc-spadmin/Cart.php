<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-spadmin-config.php");
    
    $get_host_name = array_filter(explode(":",trim($_SERVER["HTTP_HOST"])));
    $get_host_name = $get_host_name[0];
    if(isset($_POST["delete-cart"])){
        $get_cart_items = mysqli_real_escape_string($connection_server, $_COOKIE[str_replace([":","."],"_",$get_host_name)."_".$get_logged_spadmin_details["id"]."_cart_items"]);
        $marketplace_redirect = false;
        if(isset($get_cart_items)){
            $exp_cart_items = array_filter(explode(" ",trim($get_cart_items)));
            if(count($exp_cart_items) >= 1){
                foreach($exp_cart_items as $items){
                    $all_refined_cart_items .= "id='$items' ";
                }
                $exp_all_refined_cart_items = array_filter(explode(" ",trim($all_refined_cart_items)));
                $implode_cart_items = implode(" OR ", $exp_all_refined_cart_items);
                //Clear Cart Items Cookies
                setcookie(str_replace([":","."],"_",$get_host_name)."_".$get_logged_spadmin_details["id"]."_cart_items", "", (time() - 100));
                mysqli_query($connection_server, "DELETE FROM sas_api_marketplace_listings WHERE $implode_cart_items");
                //Cart Item Deleted Successfully
                $json_response_array = array("desc" => "Cart Item Deleted Successfully");
                $json_response_encode = json_encode($json_response_array,true);
                $marketplace_redirect = true;
            }else{
                //No Item In Cart
                $json_response_array = array("desc" => "No Item In Cart");
                $json_response_encode = json_encode($json_response_array,true);
            }
        }else{
            //Cart Is Empty
            $json_response_array = array("desc" => "Cart Is Empty");
            $json_response_encode = json_encode($json_response_array,true);
        }
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        if($marketplace_redirect == false){
            header("Location: ".$_SERVER["REQUEST_URI"]);
        }else{
            header("Location: /bc-spadmin/MarketPlace.php");
        }
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
      <h1>CART CHECKOUT</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Cart</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
      <div class="card info-card px-5 py-5">
            <div class="row mb-3">

            <?php
                $get_cart_items = $_COOKIE[str_replace([":","."],"_",$get_host_name)."_".$get_logged_spadmin_details["id"]."_cart_items"];
				
				if(isset($get_cart_items) && !empty($get_cart_items)){
                    $exp_cart_items = array_filter(explode(" ",trim($get_cart_items)));
                    
                    $count_old_cart_items = 0;
                    $count_new_cart_items = 0;
                    $count_old_cart_items_amount = 0;
                    $count_new_cart_items_amount = 0;
                    foreach($exp_cart_items as $item_id){
                        if(is_numeric($item_id) && ($item_id > 0)){
                            $get_active_cart_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_api_marketplace_listings WHERE id='".$item_id."'");
                            
                            if(isset($get_active_cart_details["api_type"])){
                                $api_website = str_replace(["//www.","/","http:","https:"],"",$get_active_cart_details["api_website"]);
                                $api_type = strtoupper(str_replace(["_","-"]," ",$get_active_cart_details["api_type"]));
                                $product_description = checkTextEmpty($get_active_cart_details["description"]);
                                $product_api_price = toDecimal($get_active_cart_details["price"], 2);
                                $api_status_array = array(1 => "Public", 2 => "Private");
                                $count_new_cart_items += 1;
                                $count_new_cart_items_amount += $get_active_cart_details["price"];
                                $api_status = "".$api_status_array[$get_active_cart_details["status"]]."";
                                echo 
                                    '<div class="container col-12 border rounded-2 px-5 py-3 lh-lg py-5">
                    									<div class="d-flex flex-row justify-content-between mb-4">
                    										<span style="user-select: auto;" id="" class="h4 fw-bold">'.strtoupper($api_type).' API</span>
                    										<span style="user-select: auto;" id="" class="h4 fw-bold ms-auto">Price: N'.$product_api_price.'</span><br/>
                  										</div>
                  										<div style="user-select: auto;" id="" class="h6 fw-bold"><span style="user-select: auto; text-decoration: underline;">Description:</span> <span class="h6">'.$product_description.'</span></div><br/>
                  										<div style="user-select: auto;" id="" class="h6 fw-bold"><span style="user-select: auto; text-decoration: underline;">Status:</span> <span class="h6">'.$api_status.'</span></div><br/>
                  										<div style="user-select: auto;" id="" class="h6 fw-bold">API Website: <a target="_blank" href="https://'.$api_website.'" style="user-select: auto; text-decoration: underline;" class="h6 text-primary">https://'.$api_website.'</a></div>
                  										<div style="user-select: auto; text-decoration: underline; color: red;"  onclick="removeAPIFromCart(`'.$item_id.'`, `'.$get_logged_spadmin_details["id"].'`);" id="" class="h6 fw-bold text-danger float-end">Remove</div><br/>
                  									</div>';
                            }else{
                                //Unknown Item_id
                            }
                        }
                    }
                }else{
                    //No Item In Cart
                    echo 
                        '<div class="container d-flex flex-column align-items-center justify-items-center justify-content-center">
            				  		<img alt="Logo" src="'.$web_http_host.'/asset/ooops.gif" style="user-select: auto; pointer-events: none; object-fit: contain; object-position: center;" class="col-5"/><br/>
            							<span style="user-select: auto;" id="" class="tet-warning h3">Oooops!!! Cart Empty</span>
            						</div>';
                }
           ?>
           <?php if($count_new_cart_items > 0){
                if(($count_new_cart_items) == 1){
                    $item_singular_plural = "item";
                }else{
                    if(($count_new_cart_items) > 1){
                        $item_singular_plural = "items";
                    }else{
                        $item_singular_plural = "item";
                    }
                }
           ?>
                <div style="user-select: auto; text-align: right;" class="container lh-lg mt-3">
            		<span style="user-select: auto;" class="h6"><?php echo $count_new_cart_items; ?> <span style="" class="fw-bold text-success">new</span>, <?php echo $count_old_cart_items; ?> <span style="" class="fw-bold text-danger">installed</span> <?php echo $item_singular_plural; ?></span><br>
            		<span style="user-select: auto;" class="text-success h6">Sub Total: N<?php echo toDecimal(($count_old_cart_items_amount + $count_new_cart_items_amount), 2); ?></span><br>
            		<span style="user-select: auto;" class="text-danger h6">Total Amount: N<?php echo toDecimal($count_new_cart_items_amount, 2); ?></span><br>
            		<form method="post" action="">
            			<button onclick="askPermissionSubBtn(this,'Are you sure you want to Checkout Cart?');" name="checkout-cart" type="button" style="user-select: auto;" class="btn btn-danger col-auto mt-2" >
            				CHECKOUT
            			</button><br/>
            		</form>
            	</div>
           <?php } ?>
         </div>
        </div>
      </section>
        
        
    <?php include("../func/bc-spadmin-footer.php"); ?>
    
</body>
</html>