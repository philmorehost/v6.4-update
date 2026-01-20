<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-config.php");
        
    if(isset($_POST["send-sms"])){
        $purchase_method = "web";
		include_once("func/sms.php");
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        //echo '<script>alert("'.$json_response_decode["status"].': '.$json_response_decode["desc"].'");</script>';
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
    
?>
<!DOCTYPE html>
<head>
<title>Bulk SMS | <?php echo $get_all_site_details["site_title"]; ?></title>
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
      <h1>BULK SMS</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Bulk SMS</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">

    
    <div class="card info-card px-5 py-5">
        
            <form method="post" action="">
                <div style="text-align: center; user-select: auto;" class="container">
                    <img alt="Airtel" id="airtel-lg" product-status="enabled" src="/asset/airtel.png" onclick="tickBulkSMSCarrier('airtel');" class="col-2 rounded-5 border m-1 "/>
                    <img alt="MTN" id="mtn-lg" product-status="enabled" src="/asset/mtn.png" onclick="tickBulkSMSCarrier('mtn');" class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1"/>
                    <img alt="Glo" id="glo-lg" product-status="enabled" src="/asset/glo.png" onclick="tickBulkSMSCarrier('glo');" class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1"/>
                    <img alt="9mobile" id="9mobile-lg" product-status="enabled" src="/asset/9mobile.png" onclick="tickBulkSMSCarrier('9mobile');" class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1"/>
                </div><br/>
                <input id="isprovider" name="isp" type="text" placeholder="Isp" hidden readonly required/>
                <input id="filtered-phone-numbers" name="filtered-phone-numbers" type="text" hidden readonly required/>
                <select style="text-align: center;" onchange="tickBulkSMSCarrier('');" id="" name="sender-id" onchange="" class="form-control mb-1" required/>
                	<option value="" default hidden selected>Sender ID</option>
                    <?php
                        $get_sms_sender_id_lists = mysqli_query($connection_server, "SELECT * FROM sas_bulk_sms_sender_id WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$get_logged_user_details["username"]."'");
                        if(mysqli_num_rows($get_sms_sender_id_lists) > 0){
                            while($sender_id_details = mysqli_fetch_assoc($get_sms_sender_id_lists)){
                                if($sender_id_details["status"] == 1){
                                    echo '<option value="'.$sender_id_details["sender_id"].'" >'.$sender_id_details["sender_id"].'</option>';
                                }else{
                                    if($sender_id_details["status"] == 2){
                                        echo '<option value="" disabled>'.$sender_id_details["sender_id"].' ( Disabled )</option>';
                                    }
                                }
                            }
                        }
                    ?>
                </select><br/>
                <textarea style="text-align: center; resize: none;" id="phone-numbers" name="" onkeyup="filterBulkSMSPhoneNumbers();" placeholder="Phone numbers seperated by commas" class="form-control mb-1" inputmode="numeric" pattern="[0-9,]*" required></textarea><br/>
                <div style="text-align: center;" class="col-12">
                    <span id="phone-numbers-span" class="h5" style="user-select: auto;">Phone Number Count: 0</span>
                </div><br/>
                <textarea style="text-align: center; resize: none;" id="text-message" name="text-message" onkeyup="filterBulkSMSMessage(); tickBulkSMSCarrier('');" placeholder="Message here" class="form-control mb-1" required></textarea><br/>
                <div style="text-align: center;" class="col-12">
                    <span id="text-message-span" class="h5" style="user-select: auto;">Word Count: 0/480</span>
                </div><br/>
                <select style="text-align: center;" id="sms-type" name="sms-type" onchange="tickBulkSMSCarrier('');" class="form-control mb-1" required/>
                    <option value="" default hidden selected>SMS Type</option>
                    <option value="standard_sms">Standard SMS</option>
                    <option value="flash_sms">Flash SMS</option>
                </select><br/>
                <div style="text-align: left;" class="container">
                    <div class="col-12">
                        <label onclick="restructureBulkSMSPhoneNumbers();" for="restructure-phone-numbers" class="h5" style="user-select: auto;">
                            Format Phone Numbers (Click Me)
                        </label>
                    </div>
                </div><br>
                <div style="text-align: left;" class="container mb-1">
                    <input id="phone-bypass" onclick="bypassBulkSMSPhoneNumbers();" checked type="checkbox" class="form-check-input mb-1" />
                    <div class="col-12">
                        <label for="phone-bypass" class="h5" style="user-select: auto;">
                            Bypass Phone Verification
                        </label>
                    </div>
                </div><br>
                <button id="proceedBtn" name="send-sms" type="button" style="pointer-events: unone; user-select: auto;" class="btn btn-success mb-1 col-12" >
                    SEND SMS
                </button><br>
                <div style="text-align: center;" class="col-8">
                    <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                </div>
            </form>
        </div>
      </div>
    </section>

		<?php include("../func/short-trans.php"); ?>
	<?php include("../func/bc-footer.php"); ?>
	
</body>
</html>