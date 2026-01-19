<?php session_start();
    include("../func/bc-admin-config.php");
    
    if(isset($_SESSION["spadmin_vendor_auth"]) && ($_SESSION["spadmin_vendor_auth"] == true)){
		$status_statement = "(status='1' OR status='2')";
	}else{
		$status_statement = "status='1'";
	}
?>
<!DOCTYPE html>
<head>
    <title>API MarketPlace | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_super_admin_site_details["site_desc"], 0, 160); ?>" />
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
    <?php include("../func/bc-admin-header.php"); ?>
    <div class="pagetitle">
      <h1>MARKETPLACE</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">MarketPlace</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
        
        <?php
            
            if(!isset($_GET["searchq"]) && isset($_GET["page"]) && !empty(trim(strip_tags($_GET["page"]))) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) >= 1)){
                $page_num = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["page"])));
                $offset_statement = " OFFSET ".((10 * $page_num) - 10);
            }else{
                $offset_statement = "";
            }
            
            if(isset($_GET["searchq"]) && !empty(trim(strip_tags($_GET["searchq"])))){
                $search_statement = " && (api_website LIKE '%".trim(strip_tags($_GET["searchq"]))."%' OR api_type LIKE '%".trim(strip_tags($_GET["searchq"]))."%' OR description LIKE '%".trim(strip_tags($_GET["searchq"]))."%' OR price LIKE '%".trim(strip_tags($_GET["searchq"]))."%')";
                $search_parameter = "searchq=".trim(strip_tags($_GET["searchq"]))."&&";
            }else{
                $search_statement = "";
                $search_parameter = "";
            }
            $get_active_user_details = mysqli_query($connection_server, "SELECT * FROM sas_api_marketplace_listings WHERE $status_statement $search_statement ORDER BY date DESC LIMIT 20 $offset_statement");
            
        ?>
        <div class="card info-card px-5 py-5">
            <div class="row mb-3">
                <a href="Cart.php"  style="text-decoration: none;" class="">
                    <div class="col-12 d-flex justify-content-end">
                        <div class="position-relative d-inline-block">
                          <i class="bi bi-cart-fill text-success h2"></i>
                          <span id="count-cart-items" class="badge bg-primary badge-number position-absolute top-0 start-100 translate-middle">
                            0
                          </span>
                        </div>
                    </div>
                </a><br>
                
                <form method="get" action="MarketPlace.php" class="">
                    <input style="user-select: auto;" name="searchq" type="text" value="<?php echo trim(strip_tags($_GET["searchq"])); ?>" placeholder="Product Name, API Website e.t.c" class="form-control mt-3" />
                    <button style="user-select: auto;" type="submit" class="btn btn-success d-inline col-12 col-lg-auto my-2" >
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>
            </div>
                
          <span style="user-select: auto;" class="fw-bold h4">AVAILABLE API LIST (ONETIME PAYMENT)</span><br>
			
            <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
              <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
                  <thead class="thead-dark">
                    <tr>
                        <th>S/N</th><th>API Website</th><th>Product Type</th><th>Price (Naira)</th><th>Description</th><th>Status</th><th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if(mysqli_num_rows($get_active_user_details) >= 1){
                        while($user_details = mysqli_fetch_assoc($get_active_user_details)){
                            $transaction_type = ucwords($user_details["type_alternative"]);
                            $countTransaction += 1;
                            
                            $api_website = str_replace(["//www.","/","http:","https:"],"",$user_details["api_website"]);
                            $api_type = strtoupper(str_replace(["_","-"]," ",$user_details["api_type"]));
                            $product_description = str_replace("\n","<br/>",checkTextEmpty($user_details["description"]));
                            $product_api_price = toDecimal($user_details["price"], 2);
                            $api_status_array = array(1 => "Public", 2 => "Private");

                            $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_base_url='".$api_website."' && api_type='".$user_details["api_type"]."'");
                            if(mysqli_num_rows($select_api_lists) > 0){
                                if(mysqli_num_rows($select_api_lists) == 1){
                                    $api_status = "Installed";
                                    $purchase_action_button = '';
                                }else{
                                    if(mysqli_num_rows($select_api_lists) > 1){
                                        $api_status = "Installed (Duplicated API)";
                                        $purchase_action_button = '( )';
                                    }
                                }
                            }else{
                                $api_status = '<span style="color: red;">new</span>';
                                $purchase_action_button = '( <span onclick="addAPIToCart(this, `'.$user_details["id"].'`, `'.$get_logged_admin_details["id"].'`);" id="cart-'.$user_details["id"].'-'.$get_logged_admin_details["id"].'" style="text-decoration: underline; color: green;" class="h6 cart-spans">Add Cart</span> )';
                            }
                            $all_user_account_action = $api_status." ".$purchase_action_button;
                            if($api_website != $_SERVER["HTTP_HOST"]){
                                echo 
                                '<tr>
                                    <td>'.$countTransaction.'</td><td>'."https://".$api_website.'</td><td>'.$api_type.'</td><td>'.$product_api_price.'</td><td style="user-select: auto;">'.$product_description.'</td><td>'.$api_status_array[$user_details["status"]].'</td><td>'.$all_user_account_action.'</td>
                                </tr>';
                            }
                        }
                    }
                    ?>
                  </tbody>
                </table>
            </div><br/>
            
            <div class="mt-2 justify-content-between justify-items-center">
                <?php if(isset($_GET["page"]) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) > 1)){ ?>
                <a href="MarketPlace.php?<?php echo $search_parameter; ?>page=<?php echo (trim(strip_tags($_GET["page"])) - 1); ?>">
                    <button style="user-select: auto;" class="btn btn-success col-auto">Prev</button>
                </a>
                <?php } ?>
                <?php
                	if(isset($_GET["page"]) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) >= 1)){
                		$trans_next = (trim(strip_tags($_GET["page"])) +1);
                	}else{
                		$trans_next = 2;
                	}
                ?>
                <a href="MarketPlace.php?<?php echo $search_parameter; ?>page=<?php echo $trans_next; ?>">
                    <button style="user-select: auto;" class="btn btn-success col-auto">Next</button>
                </a>
            </div>
            
        </div>
      </div>
    </section>

        
    <?php include("../func/bc-admin-footer.php"); ?>
    
</body>
</html>