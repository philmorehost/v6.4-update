<?php session_start();
    include("../func/bc-config.php");
        
    if(isset($_POST["buy-betting"])){
        $purchase_method = "web";
        $action_function = 1;
        include_once("func/betting.php");
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        unset($_SESSION["customer_amount"]);
        unset($_SESSION["customer_id"]);
        unset($_SESSION["customer_provider"]);
        unset($_SESSION["customer_type"]);
        unset($_SESSION["customer_name"]);
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }

    if(isset($_POST["verify-customer"])){
        $purchase_method = "web";
        $action_function = 3;
        include_once("func/betting.php");
        $json_response_decode = json_decode($json_response_encode,true);
        if($json_response_decode["status"] == "success"){
            $_SESSION["customer_amount"] = $amount;
            $_SESSION["customer_id"] = $customer_id;
            $_SESSION["customer_provider"] = $epp;
            $_SESSION["customer_name"] = $json_response_decode["desc"];
        }

        if($json_response_decode["status"] == "failed"){
            $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        }
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }

    if(isset($_POST["reset-betting"])){
        unset($_SESSION["customer_amount"]);
        unset($_SESSION["customer_id"]);
        unset($_SESSION["customer_provider"]);
        unset($_SESSION["customer_name"]);
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
    
?>
<!DOCTYPE html>
<head>
    <title>Fund Betting | <?php echo $get_all_site_details["site_title"]; ?></title>
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
    
	<div class="pagetitle d-none d-md-block">
      <h1>FUND BETTING</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Fund Betting</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
		<div class="card info-card sales-card">
			<div class="card-body">
				<h5 class="card-title">Wallet Balance <span>| <?php echo "N".number_format($get_logged_user_details["balance"], 2); ?></span></h5>
			</div>
		</div>

    
    <div class="card info-card px-5 py-5">

            <form method="post" action="">
                <?php if(!isset($_SESSION["customer_name"])){ ?>
                <div style="text-align: center; user-select: auto;" class="container">
                    <img alt="msport" id="msport-lg" product-status="enabled" src="/asset/msport.jpg" onclick="tickBettingCarrier('msport'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="naijabet" id="naijabet-lg" product-status="enabled" src="/asset/naijabet.jpg" onclick="tickBettingCarrier('naijabet'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="nairabet" id="nairabet-lg" product-status="enabled" src="/asset/nairabet.jpg" onclick="tickBettingCarrier('nairabet'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="bet9ja-agent" id="bet9ja-agent-lg" product-status="enabled" src="/asset/bet9ja-agent.jpg" onclick="tickBettingCarrier('bet9ja-agent'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="betland" id="betland-lg" product-status="enabled" src="/asset/betland.jpg" onclick="tickBettingCarrier('betland'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="betlion" id="betlion-lg" product-status="enabled" src="/asset/betlion.jpg" onclick="tickBettingCarrier('betlion'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="supabet" id="supabet-lg" product-status="enabled" src="/asset/supabet.jpg" onclick="tickBettingCarrier('supabet'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="bet9ja" id="bet9ja-lg" product-status="enabled" src="/asset/bet9ja.jpg" onclick="tickBettingCarrier('bet9ja'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="bangbet" id="bangbet-lg" product-status="enabled" src="/asset/bangbet.jpg" onclick="tickBettingCarrier('bangbet'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="betking" id="betking-lg" product-status="enabled" src="/asset/betking.jpg" onclick="tickBettingCarrier('betking'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="1xbet" id="1xbet-lg" product-status="enabled" src="/asset/1xbet.jpg" onclick="tickBettingCarrier('1xbet'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="betway" id="betway-lg" product-status="enabled" src="/asset/betway.jpg" onclick="tickBettingCarrier('betway'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="merrybet" id="merrybet-lg" product-status="enabled" src="/asset/merrybet.jpg" onclick="tickBettingCarrier('merrybet'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="mlotto" id="mlotto-lg" product-status="enabled" src="/asset/mlotto.jpg" onclick="tickBettingCarrier('mlotto'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="western-lotto" id="western-lotto-lg" product-status="enabled" src="/asset/western-lotto.jpg" onclick="tickBettingCarrier('western-lotto'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="hallabet" id="hallabet-lg" product-status="enabled" src="/asset/hallabet.jpg" onclick="tickBettingCarrier('hallabet'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="green-lotto" id="green-lotto-lg" product-status="enabled" src="/asset/green-lotto.jpg" onclick="tickBettingCarrier('green-lotto'); resetBettingQuantity();" class="col-2 rounded-5 border m-1 "/>

                </div><br/>
                <input id="bettingname" name="epp" type="text" placeholder="betting Name" hidden readonly required/>
                
                <input style="text-align: center;" id="customer-id" name="customer-id" onkeyup="pickBettingQty();" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="Customer ID" title="Charater must be atleast 10 digit" class="form-control mb-1" required/><br/>
                <input style="text-align: center;" id="product-amount" name="amount" onkeyup="pickBettingQty();" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="Amount" title="Charater must be atleast 3 digit" class="form-control mb-1" required/><br/>
                <?php }else{ ?>
                <div style="text-align: center; user-select: auto;" style="container">
                  <img alt="<?php echo $_SESSION['customer_provider']; ?>" id="<?php echo $_SESSION['customer_provider']; ?>-lg" src="/asset/<?php echo $_SESSION['customer_provider']; ?>.jpg" class="col-8 col-lg-5 "/><br/>
                  <div style="text-align: left;" class="container mb-1">
                      <span class="h5" style="user-select: auto;">Full-Name: <span class="h4 fw-bold"><?php echo strtoupper($_SESSION['customer_name']); ?></span></span><br/>
                      <span class="h5" style="user-select: none">Customer ID: <span class="h4 fw-bold"><?php echo $_SESSION['customer_id']; ?></span></span><br/>
                      <span class="h5" style="user-select: auto;">Amount To Pay: <span class="h4 fw-bold">N<?php echo $_SESSION['customer_amount']; ?></span></span>
                  </div>
                </div><br/>
                <?php } ?>

                <?php if(!isset($_SESSION["customer_name"])){ ?>
                <button id="proceedBtn" name="verify-customer" type="button" style="pointer-events: none; user-select: auto;" class="btn btn-success mb-1 col-12" >
                    VERIFY CUSTOMER
                </button><br>
                <?php }else{ ?>
                <button id="" name="buy-betting" type="submit" style="user-select: auto;" class="btn btn-success mb-1 col-12" >
                    FUND BETTING WALLET
                </button><br>
                <button id="" name="reset-betting" type="submit" style="user-select: auto;" class="btn btn-warning mb-1 col-12" >
                    RESET CUSTOMER DETAILS
                </button><br>
                <?php } ?>
                <div style="text-align: center;" class="col-8">
                    <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                </div>
            </form>
        </div>
      </div>
  </section>

		<div class="d-none d-md-block">
        <?php include("../func/short-trans.php"); ?>
		</div>
    <?php include("../func/bc-footer.php"); ?>
    
</body>
</html>