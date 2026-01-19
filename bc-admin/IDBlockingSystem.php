<?php session_start();
    include("../func/bc-admin-config.php");
        
    if(isset($_POST["take-action"])){
        $id = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["id"]))));
        $type = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["type"]))));
        $type_array = array(1 => "BLOCKED", 2 => "UNBLOCKED");
        $select_item_query = mysqli_query($connection_server, "SELECT * FROM sas_id_blocking_system WHERE vendor_id='".$get_logged_admin_details["id"]."' && product_id='$id'");
		if(!empty($id) && is_numeric($id)){
			if(in_array($type, array_keys($type_array))){
				if(mysqli_num_rows($select_item_query) == 1){
					
					if($type == 1){
						$json_response_array = array("desc" => "ID Has Already BLOCKED");
                		$json_response_encode = json_encode($json_response_array,true);
					}
    	                                                
        	   		if($type == 2){
        	   			mysqli_query($connection_server, "DELETE FROM sas_id_blocking_system WHERE vendor_id='".$get_logged_admin_details["id"]."' && product_id='$id'");
        	   			$json_response_array = array("desc" => "ID UNBLOCKED");
        	   			$json_response_encode = json_encode($json_response_array,true);
        	    	}
        	    			
				}else{
					if(mysqli_num_rows($select_item_query) == 0){
						if($type == 1){
							mysqli_query($connection_server, "INSERT INTO sas_id_blocking_system (vendor_id, product_id) VALUES ('".$get_logged_admin_details["id"]."', '$id')");
							$json_response_array = array("desc" => "ID BLOCKED");
							$json_response_encode = json_encode($json_response_array,true);
						}
						
						if($type == 2){
							$json_response_array = array("desc" => "ID Was Not On BLOCKED List");
							$json_response_encode = json_encode($json_response_array,true);
						}
						
					}
				}
			}else{
				//Invalid Action Type
				$json_response_array = array("desc" => "Invalid Action Type");
				$json_response_encode = json_encode($json_response_array,true);
			}
		}else{
			//Invalid ID (Empty/Non-numeric)
			$json_response_array = array("desc" => "Invalid ID (Empty/Non-numeric)");
			$json_response_encode = json_encode($json_response_array,true);
		}
        
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
?>
<!DOCTYPE html>
<head>
    <title>Share Fund | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
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
      <h1>ID BLOCKING SYSTEM</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">ID Blocking System</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">

    	<div style="text-align: center;" class="card info-card px-5 py-5">
            <form method="post" action="">
                <input style="text-align: center;" name="id" type="number" value="" placeholder="Phone number, Meter number, Cable IUC number" class="form-control mb-1" required/><br/>
                <select style="text-align: center;" id="" name="type" class="form-control mb-1" required/>
                	<option value="" selected hidden default>Choose Action Type</option>
                	<option value="1">Block</option>
                	<option value="2">Unblock</option>
                </select><br/>
                <button name="take-action" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
                    TAKE ACTION
                </button><br>
                <div style="text-align: center;" class="col-12 mt-1">
                    <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                </div>
            </form>
        </div>
        <?php            
            if(!isset($_GET["searchq"]) && isset($_GET["page"]) && !empty(trim(strip_tags($_GET["page"]))) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) >= 1)){
                $page_num = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["page"])));
                $offset_statement = " OFFSET ".((10 * $page_num) - 10);
            }else{
                $offset_statement = "";
            }
            
            if(isset($_GET["searchq"]) && !empty(trim(strip_tags($_GET["searchq"])))){
                $search_statement = " && (product_id LIKE '%".trim(strip_tags($_GET["searchq"]))."%')";
                $search_parameter = "searchq=".trim(strip_tags($_GET["searchq"]))."&&";
            }else{
                $search_statement = "";
                $search_parameter = "";
            }
            $get_active_blocked_details = mysqli_query($connection_server, "SELECT * FROM sas_id_blocking_system WHERE product_id != '' $search_statement ORDER BY date DESC LIMIT 10 $offset_statement");
            
        ?>
        <div class="card info-card px-5 py-5">
            <div class="row">
                <form method="get" action="IDBlockingSystem.php" class="m-margin-tp-1 s-margin-tp-1">
                    <input style="user-select: auto;" name="searchq" type="text" value="<?php echo trim(strip_tags($_GET["searchq"])); ?>" placeholder="Phone number, Meter number, Cable IUC number" class="form-control mt-3" />
                    <button style="user-select: auto;" type="submit" class="btn btn-success d-inline col-12 col-lg-auto my-2" >
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>
            </div>

            <div style="user-select: auto; cursor: grab;" class="overflow-auto">
              <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
                  <thead class="thead-dark">
                    <tr>
                        <th>S/N</th><th>Blocked IDs</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if(mysqli_num_rows($get_active_blocked_details) >= 1){
                        while($blocked_details = mysqli_fetch_assoc($get_active_blocked_details)){
                            $countTransaction += 1;
                            
                            echo 
                            '<tr>
                                <td>'.$countTransaction.'</td><td>'.$blocked_details["product_id"].'</td>
                            </tr>';
                        }
                    }
                    ?>
                  </tbody>
                </table>
            </div><br/>
            
            <div class="mt-2 justify-content-between justify-items-center">
                <?php if(isset($_GET["page"]) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) > 1)){ ?>
                <a href="IDBlockingSystem.php?<?php echo $search_parameter; ?>page=<?php echo (trim(strip_tags($_GET["page"])) - 1); ?>">
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
                <a href="IDBlockingSystem.php?<?php echo $search_parameter; ?>page=<?php echo $trans_next; ?>">
                    <button style="user-select: auto;" class="btn btn-success col-auto">Next</button>
                </a>
            </div>
        </div>
      </div>
    </section>

	<?php include("../func/bc-admin-footer.php"); ?>
	
</body>
</html>