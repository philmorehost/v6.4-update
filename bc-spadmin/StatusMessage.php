<?php session_start();
    include("../func/bc-spadmin-config.php");
    
    if(isset($_POST["update-status"])){
        $message = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["message"])));
        if(!empty($message)){
            $select_vendor_status_message = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_status_messages");
            if(mysqli_num_rows($select_vendor_status_message) == 1){
            	mysqli_query($connection_server, "UPDATE sas_super_admin_status_messages SET message='$message'");
            	//Hurray! Status Message Updated Successfully
            	$json_response_array = array("desc" => "Hurray! Status Message Updated Successfully");
            	$json_response_encode = json_encode($json_response_array,true);
            }else{
            	if(mysqli_num_rows($select_vendor_status_message) > 1){
            		mysqli_query($connection_server, "DELETE FROM sas_super_admin_status_messages");
            		mysqli_query($connection_server, "INSERT INTO sas_super_admin_status_messages (message) VALUES ('$message')");
            		//Hurray! Status Message Recreated Successfully
            		$json_response_array = array("desc" => "Hurray! Status Message Recreated Successfully");
            		$json_response_encode = json_encode($json_response_array,true);
            	}else{
            		mysqli_query($connection_server, "INSERT INTO sas_super_admin_status_messages (message) VALUES ('$message')");
            		//Hurray! Status Message Created Successfully
            		$json_response_array = array("desc" => "Hurray! Status Message Created Successfully");
            		$json_response_encode = json_encode($json_response_array,true);
            	}
            }
		}else{
			if(empty($message)){
                //Message Field Empty
				$json_response_array = array("desc" => "Message Field Empty");
				$json_response_encode = json_encode($json_response_array,true);
            }
		}
        
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
    
    $select_vendor_super_admin_status_message = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_status_messages");
    if(mysqli_num_rows($select_vendor_super_admin_status_message) == 1){
    	$get_vendor_super_admin_status_message = mysqli_fetch_array($select_vendor_super_admin_status_message);
    	$get_vendor_super_admin_status_message_text = 	$get_vendor_super_admin_status_message["message"];
    }else{
    	$get_vendor_super_admin_status_message_text = "";
    }
?>
<!DOCTYPE html>
<head>
    <title>Status Message</title>
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
      <h1>STATUS MESSAGE</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Status Message</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
    
        <div class="card info-card px-5 py-5">
            <form method="post" action="">
            	<div style="text-align: center;" class="container">
                    <span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="fw-bold" style="user-select: auto;">{firstname}</span></span>
  		        </div><br/>
                <textarea style="text-align: left; resize: none;" id="" name="message" onkeyup="" placeholder="Message" class="form-control mb-1" rows="10" required><?php echo $get_vendor_super_admin_status_message_text; ?></textarea><br>
                <button name="update-status" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-1">
                    UPDATE STATUS
                </button><br>
                <div style="text-align: center;" class="container">
                    <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                </div>
            </form>
        </div>
      </div>
    </section>

	<?php include("../func/bc-spadmin-footer.php"); ?>
	
</body>
</html>