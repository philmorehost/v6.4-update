<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-config.php");
    
    if(isset($_GET["ref"])){
        $reference = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["ref"])));
        
        if(!empty($reference)){
        	$get_purchased_cards = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && username='".$get_logged_user_details["username"]."' && reference='$reference'");
        	if(mysqli_num_rows($get_purchased_cards) == 1){
        		$all_purchased_cards = $get_purchased_cards;
        	}else{
        		if(mysqli_num_rows($get_purchased_cards) < 1){
        			//Invalid Card ID
        			$json_response_array = array("desc" => "Invalid Card ID");
        			$json_response_encode = json_encode($json_response_array,true);
        		}else{
        			if(mysqli_num_rows($get_purchased_cards) > 1){
        				//Duplicated Details, Contact Admin
        				$json_response_array = array("desc" => "Duplicated Details, Contact Admin");
        				$json_response_encode = json_encode($json_response_array,true);
        			}
        		}
        	}
    	}else{
    		//Reference ID Empty
    		$json_response_array = array("desc" => "Reference ID Empty");
    		$json_response_encode = json_encode($json_response_array,true);
    	}
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        //header("Location: ".$_SERVER["REQUEST_URI"]);
    }
?>
<!DOCTYPE html>
<head>
	<title>View Card | <?php echo $get_all_site_details["site_title"]; ?></title>
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

    <script src="https://merchant.beewave.ng/checkout.min.js"></script> 
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
	<?php include("../func/bc-header.php"); ?>	
<div class="pagetitle">
      <h1>VIEW VIRTUAL CARD</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Voew Virtual Card</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">

    
    <div class="card info-card px-5 py-5" id="printable-div-area">
  
			<!-- <div style="text-align: center;" id="printable-div-area" class="bg-2 m-block-dp s-block-dp m-position-rel s-position-rel m-width-100 s-width-100 m-height-auto s-height-auto"> -->
            <?php
            	if(mysqli_num_rows($all_purchased_cards) == 1){
            		while($card_details = mysqli_fetch_assoc($all_purchased_cards)){
            			$all_exploded_cards = array_filter(explode(",",trim($card_details["card_number"])));
            			$all_exploded_name = array_filter(explode("_",trim($card_details["card_name"])));
            			$isp_name = $all_exploded_name[0];
            			$card_qty = strtoupper($all_exploded_name[1]);
            			$card_type = $card_details["card_type"];
            			$date_puchased = formDate($card_details["date"]);
				$get_dial_code = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_cards WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && card_name='".$card_details["card_name"]."' LIMIT 1");
            			$explode_dialcode = array_filter(explode(",",trim($get_dial_code["dial_code"])));
            			$dial_code = $explode_dialcode[0];
            			$dial_code_2 = $explode_dialcode[1];
            			if(empty($card_details["business_name"])){
            				$business_name = strtoupper(explode(".",trim($_SERVER["HTTP_HOST"]))[0]);
            			}else{
            				$business_name = $card_details["business_name"];	
            			}
            			if(count($all_exploded_cards) >= 1){
            				foreach($all_exploded_cards as $each_card){
            				  
            				  echo '<div style="text-align: center;" class="container d-inline-flex flex-column flex-lg-row flex-md-row flex-xxl-row">
            				      
            				      <div class="d-inline-block py-2 col-12 col-lg-5 col-md-6 col-xxl-4">
            				        <img src="../asset/'.strtolower($isp_name).'-atm.png" class="col-12"/><br/>
            				      	<span class="h3">Card Balance: <span class="fw-bold h2">'.toDecimal($card_details["card_balance"], 2).' '.$card_details["card_currency"].'</span></span><br/>
            				      </div>
            				      
            				      <div style="text-align: left;" class="d-inline-block lh-lg px-5 py-2 col-12 col-lg-7 col-md-6 col-xxl-8 border-dark border-sm-top border-md-top border-lg-top">
            				        <span class="h6">Card Type: <span class="fw-bold h6">'.ucwords($card_type).' ( '.ucwords($isp_name).' )</span></span><br/>
            				        <span class="h6">Card Number: <span class="fw-bold h6">'.$each_card.'</span></span><br/>
            				        <span class="h6">Card Validity: <span class="fw-bold h6">'.$card_details["card_validity"].'</span></span><br/>
            				        <span class="h6">Card CVV: <span class="fw-bold h6">'.$card_details["card_cvv"].'</span></span><br/>
            				        <span class="h6">PIN: <span class="fw-bold h6">'.$card_details["card_pin"].'</span></span><br/>
            				        <span class="h6">Address: <span class="fw-bold h6">'.$card_details["card_address"].'</span></span><br/>
            				        <span class="h6">State: <span class="fw-bold h6">'.$card_details["card_state"].'</span></span><br/>
            				        <span class="h6">Country: <span class="fw-bold h6">'.$card_details["card_country"].'</span></span><br/>
            				        <span class="h6">Postal Code: <span class="fw-bold h6">'.$card_details["card_zipcode"].'</span></span><br/>
            				        <span class="h6">Status: <span class="fw-bold h6">'.strtoupper($card_details["card_status"]).'</span></span>
            				      </div>
            				  </div>';
            					
            				}
            				
            				echo "<br/>"."\n";
            				echo '<!-- <span onclick="printPage();" style="user-select: auto; text-decoration: underline;" class="a-cursor m-inline-block-dp s-inline-block-dp m-position-rel s-position-rel m-font-size-14 s-font-size-16 m-margin-tp-2 s-margin-tp-2">Print Card</span> -->';
            			}else{
            				//err no card
            			}
            		}
            	}else{
            		//err invalid card id
            	}
            ?>
            </div>
        </div>
	    </div>
	   </section>
	<script>
	function printPage(){
	
		var printableDivArea = document.getElementById("printable-div-area").innerHTML;
		const html = [];
		html.push('<html><head>');
		html.push('<link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">');
		html.push('</head><body onload="window.focus(); window.print()"><div>');
		html.push(printableDivArea);
		html.push('</div></body></html>');
	
		var mywindow = window.open('', '', 'width=640,height=480');
		mywindow.document.open("text/html");
		mywindow.document.write(html.join(""));
		mywindow.document.close();
	
		}
	</script>
	
	<?php include("../func/bc-footer.php"); ?>
	
</body>
</html>