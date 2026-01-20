<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-config.php");
	
    if(isset($_POST["submit-sender-id"])){
        $sender_id = mysqli_real_escape_string($connection_server, preg_replace("/[^a-zA-Z]+/","",trim(strip_tags(strtolower($_POST["sender-id"])))));
        $sample_message = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["sample-message"])));
        
        if(!empty($sender_id) && (strlen($sender_id) >= 3) && (strlen($sender_id) <= 11) && !empty($sample_message) && (strlen($sample_message) >= 1) && (strlen($sample_message) <= 160)) {
            $check_sender_id = mysqli_query($connection_server, "SELECT * FROM sas_bulk_sms_sender_id WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && sender_id='$sender_id'");
            $check_sender_id_by_user = mysqli_query($connection_server, "SELECT * FROM sas_bulk_sms_sender_id WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$get_logged_user_details["username"]."' && sender_id='$sender_id'");
            if(mysqli_num_rows($check_sender_id) == 0){
                mysqli_query($connection_server, "INSERT INTO sas_bulk_sms_sender_id (vendor_id, username, sender_id, sample_message, status) VALUES ('".$get_logged_user_details["vendor_id"]."', '".$get_logged_user_details["username"]."', '$sender_id', '$sample_message', '2')");
                //Sender ID Submitted Successfully For Review
                $json_response_array = array("desc" => "Sender ID Submitted Successfully For Review");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(mysqli_num_rows($check_sender_id) == 1){
                    if(mysqli_num_rows($check_sender_id_by_user) == 1){
                        //Sender ID Already Submitted by You
                        $json_response_array = array("desc" => "Sender ID Already Submitted by You");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                        //Sender ID Already Exists
                        $json_response_array = array("desc" => "Sender ID Already Exists");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }else{
                    if(mysqli_num_rows($check_sender_id) > 1){
                        //Duplicated Details, Contact Admin
                        $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }
            }
        }else{
            if(empty($sender_id)){
                //Sender ID Field Is Empty
                $json_response_array = array("desc" => "Sender ID Field Is Empty");
                $json_response_encode = json_encode($json_response_array,true); 
            }else{
                if(strlen($sender_id) < 3){
                    //Sender ID Must Not Be Less Than 3 Letter
                    $json_response_array = array("desc" => "Sender ID Must Not Be Less Than 3 Letter");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(strlen($sender_id) > 11){
                        //Sender ID Must Not Be Greater Than 11 Letter
                        $json_response_array = array("desc" => "Sender ID Must Not Be Greater Than 11 Letter");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                        if(empty($sample_message)){
                            //Sample Message Field Is Empty
                            $json_response_array = array("desc" => "Sample Message Field Is Empty");
                            $json_response_encode = json_encode($json_response_array,true); 
                        }else{
                            if(strlen($sample_message) < 1){
                                //Sample Message Must Not Be Less Than 1 Character
                                $json_response_array = array("desc" => "Sample Message Must Not Be Less Than 1 Character");
                                $json_response_encode = json_encode($json_response_array,true);
                            }else{
                                if(strlen($sample_message) > 160){
                                    //Sender ID Must Not Be Greater Than 160 Character
                                    $json_response_array = array("desc" => "Sender ID Must Not Be Greater Than 160 Character");
                                    $json_response_encode = json_encode($json_response_array,true);
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
    <title>Sender ID | <?php echo $get_all_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="<?php echo $get_all_site_details["primary_color"] ?? "#198754"; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">
    
      <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all'">
  <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" media="print" onload="this.media='all'">
  <link href="../assets-2/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets-2/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets-2/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets-2/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets-2/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="../assets-2/css/style.css" rel="stylesheet">

</head>
<body>
	<?php include("../func/bc-header.php"); ?>	

	<div class="pagetitle">
      <h1>SUBMIT SENDER ID</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Submit Sender ID</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">

    
    <div class="card info-card px-5 py-5">

            <form method="post" action="">
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">SENDER ID</span>
                </div><br/>
                <input style="text-align: center;" name="sender-id" type="text" value="" placeholder="Sender ID" pattern="[a-zA-Z]{3,11}" title="Sender ID must be atleast 3 and not greater than 11 letters long (No Space or Special Character)" class="form-control mb-1" autocomplete="off" required/><br/>
                <input style="text-align: center;" name="sample-message" type="text" value="" placeholder="Sample Message" pattern="[a-zA-Z0-9]{1, 160}" title="Sample Message must be atleast 1 and not greater than 160 letters long" class="form-control mb-1" autocomplete="off" required/><br/>
                <button id="" name="submit-sender-id" type="submit" style="user-select: auto;" class="btn btn-success mb-1 col-12" >
                    SUBMIT ID
                </button><br/>
                <div style="text-align: center;" class="col-8">
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
                $search_statement = " && (sender_id LIKE '%".trim(strip_tags($_GET["searchq"]))."%')";
                $search_parameter = "searchq=".trim(strip_tags($_GET["searchq"]))."&&";
            }else{
                $search_statement = "";
                $search_parameter = "";
            }
            $get_user_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_bulk_sms_sender_id WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$_SESSION["user_session"]."' $search_statement ORDER BY date DESC LIMIT 10 $offset_statement");
            
        ?>
<div class="card info-card px-5 py-5">
    <div class="col-12">
                <form method="get" action="SubmitSenderID.php" class="">
                    <input style="user-select: auto;" name="searchq" type="text" value="<?php echo trim(strip_tags($_GET["searchq"])); ?>" placeholder="Sender ID e.t.c" class="form-control" />
                    <button style="user-select: auto;" type="submit" class="btn btn-success d-inline col-12 col-lg-auto my-2" >
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>
            </div>
            <div style="user-select: auto; cursor: grab;" class="overflow-auto">
              <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
                  <thead class="thead-dark">
                    <tr>
                    	<th>S/N</th><th>Sender ID</th><th>Sample Message</th><th>Status</th><th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if(mysqli_num_rows($get_user_transaction_details) >= 1){
                    	while($user_transaction = mysqli_fetch_assoc($get_user_transaction_details)){
                    		$transaction_type = ucwords($user_transaction["type_alternative"]);
                    		$countTransaction += 1;
                    		echo 
                    		'<tr>
                    			<td>'.$countTransaction.'</td><td style="user-select: auto;">'.$user_transaction["sender_id"].'</td><td>'.$user_transaction["sample_message"].'</td><td>'.tranStatus($user_transaction["status"]).'</td><td>'.formDate($user_transaction["date"]).'</td>
                    		</tr>';
                    	}
                    }
                    ?>
                </table>
            </div>
            <div class="mt-2 justify-content-between justify-items-center">
                <?php if(isset($_GET["page"]) && is_numeric(trim(strip_tags($_GET["page"]))) && (trim(strip_tags($_GET["page"])) > 1)){ ?>
                <a href="SubmitSenderID.php?<?php echo $search_parameter; ?>page=<?php echo (trim(strip_tags($_GET["page"])) - 1); ?>">
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
                <a href="SubmitSenderID.php?<?php echo $search_parameter; ?>page=<?php echo $trans_next; ?>">
                    <button style="user-select: auto;" class="btn btn-success col-auto">Next</button>
                </a>
            </div>
        </div>
      </div>
    </section>
	<?php include("../func/bc-footer.php"); ?>
	
</body>
</html>