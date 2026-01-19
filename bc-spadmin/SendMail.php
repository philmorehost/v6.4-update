<?php session_start();
    include("../func/bc-spadmin-config.php");
    
    if(isset($_POST["send-mail"])){
        $subject = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["subject"])));
        $body = mysqli_real_escape_string($connection_server, str_replace(["\r\n"], "<br/>", trim(strip_tags($_POST["body"]))));
        $mailto = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["mailto"]))));
        $mailto_array = array("all","a","b","d","bd");
        if(!empty($subject) && !empty($body) && !empty($mailto) && in_array($mailto, $mailto_array)){
            $select_users = mysqli_query($connection_server, "SELECT * FROM sas_vendors");
                if(mysqli_num_rows($select_users) >= 1){
                    // Email Beginning
                    $send_mail_to_specified_users = sendSuperAdminEmailSpecific($mailto, $subject, $body);
                    if($send_mail_to_specified_users == "success"){
                        //Mail Sent Successfully
                        $json_response_array = array("desc" => "Mail Sent Successfully");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                        if($send_mail_to_specified_users == "failed"){
                            //Error: No Account For Mail-To Type
                            $json_response_array = array("desc" => "Error: No Account For Mail-To Type");
                            $json_response_encode = json_encode($json_response_array,true);
                        }else{
                            if($send_mail_to_specified_users == "error"){
                                //Error: Invalid Mail-To Function
                                $json_response_array = array("desc" => "Error: Invalid Mail-To Function");
                                $json_response_encode = json_encode($json_response_array,true);
                            }
                        }
                    }
                    // Email End
                }else{
                    //Error: No Account
                    $json_response_array = array("desc" => "Error: No Account");
                    $json_response_encode = json_encode($json_response_array,true);
                }
		}else{
			if(empty($subject)){
                //Email Subject Field Empty
				$json_response_array = array("desc" => "Email Subject Field Empty");
				$json_response_encode = json_encode($json_response_array,true);
            }else{
                if(empty($body)){
                    //Email Body Field Empty
                    $json_response_array = array("desc" => "Email Body Field Empty");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(empty($mailto)){
                        //Mail-To Field Empty
                        $json_response_array = array("desc" => "Mail-To Field Empty");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                        if(!in_array($mailto, $mailto_array)){
                            //Invalid Mail-To Function
                            $json_response_array = array("desc" => "Invalid Mail-To Function");
                            $json_response_encode = json_encode($json_response_array,true);
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
    <title>Mailing System</title>
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
      <h1>MAIL SENDER</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Send Mail</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
    
        <div class="card info-card px-5 py-5">
            <form method="post" action="">
                <div style="text-align: center;" class="container">
                    <span id="" class="h5"><span style="user-select: auto;">Firstname:</span> <span id="" class="fw-bold" style="user-select: auto;">{firstname}</span></span>, 
    		        <span id="" class="h5"><span style="user-select: auto;">Lastname:</span> <span id="" class="fw-bold" style="user-select: auto;">{lastname}</span></span><br/>
                    <span id="" class="h5"><span style="user-select: auto;">Email address:</span> <span id="" class="fw-bold" style="user-select: auto;">{email}</span></span>, 
    		        <span id="" class="h5"><span style="user-select: auto;">Phone number:</span> <span id="" class="fw-bold" style="user-select: auto;">{phone}</span></span><br/>
					<span id="" class="h5"><span style="user-select: auto;">Email:</span> <span id="" class="fw-bold" style="user-select: auto;">{email}</span></span>, 
    		        <span id="" class="h5"><span style="user-select: auto;">Home address:</span> <span id="" class="fw-bold" style="user-select: auto;">{address}</span></span><br/>
    			</div><br/>
                <input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-reg','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
                <select style="text-align: center;" id="" name="mailto" class="form-control mb-1" required/>
                	<option value="" selected hidden default>Choose Mail To</option>
                    <option value="all">All Accounts</option>
                	<option value="a">Active Accounts</option>
                	<option value="b">Blocked Accounts</option>
                    <option value="d">Deleted Accounts</option>
                    <option value="bd">Blocked and Deleted Accounts</option>
                </select><br/>
                <textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-reg','body'); ?></textarea><br>
                <button name="send-mail" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                    SEND MAIL
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