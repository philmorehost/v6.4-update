<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-spadmin-config.php");
    
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
            $get_active_vendor_details = mysqli_query($connection_server, "SELECT * FROM sas_api_marketplace_listings WHERE 1 $search_statement ORDER BY date DESC LIMIT 50 $offset_statement");
            
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

            <span style="user-select: auto;" class="fw-bold h4">API LIST</span><br>
			
            <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
              <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
                  <thead class="thead-dark">
                    <tr>
                        <th>S/N</th><th>API Website</th><th>Product Type</th><th>Price (Naira)</th><th>Description</th><th>Status</th><th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if(mysqli_num_rows($get_active_vendor_details) >= 1){
                        while($vendor_details = mysqli_fetch_assoc($get_active_vendor_details)){
                            $transaction_type = ucwords($vendor_details["type_alternative"]);
                            $countTransaction += 1;
                            
                            $api_website = str_replace(["//www.","/","http:","https:"],"",$vendor_details["api_website"]);
                            $api_type = strtoupper(str_replace(["_","-"]," ",$vendor_details["api_type"]));
                            $product_description = str_replace("\n","<br/>",checkTextEmpty($vendor_details["description"]));
                            $product_api_price = toDecimal($vendor_details["price"], 2);
                            $api_status_array = array(1 => "Public", 2 => "Private");

                            $purchase_action_button = '( <span onclick="addAPIToCart(this, `'.$vendor_details["id"].'`, `'.$get_logged_spadmin_details["id"].'`);" id="cart-'.$vendor_details["id"].'-'.$get_logged_spadmin_details["id"].'" style="text-decoration: underline; color: green;" class="a-cursor cart-spans">Add Cart</span> )';
                            $edit_action_button = '<span onclick="customJsRedirect(`/bc-spadmin/ApiEdit.php?apiID='.$vendor_details["id"].'`, `Are you sure you want to edit '.$api_type.' ( '.$api_website.' ) API`);" id="" style="text-decoration: underline; color: green;" class=""><i title="Edit API" style="" class="bi bi-pencil-square" ></i></span>';
                            $upload_action_button = '<span onclick="customJsRedirect(`/bc-spadmin/ApiUpload.php?apiID='.$vendor_details["id"].'`, `Are you sure you want to upload '.$api_type.' ( '.$api_website.' ) API`);" id="" style="text-decoration: underline; color: green;" class=""><i title="Upload Icon" style="" class="bi bi-upload" ></i></span>';
                            
                            $api_action_buttons = $edit_action_button." ".$upload_action_button;

                            $all_vendor_account_action = $purchase_action_button;

                            echo 
                            '<tr>
                                <td>'.$countTransaction.'</td><td>'."https://".$api_website.' '.$api_action_buttons.'</td><td>'.$api_type.'</td><td>'.$product_api_price.'</td><td style="user-select: auto;">'.$product_description.'</td><td>'.$api_status_array[$vendor_details["status"]].'</td><td>'.$all_vendor_account_action.'</td>
                            </tr>';
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

        
    <?php include("../func/bc-spadmin-footer.php"); ?>
    
</body>
</html>