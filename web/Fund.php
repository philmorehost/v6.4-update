<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
include("../func/bc-config.php");

$payment_gateway_array = array("monnify", "flutterwave", "paystack", "beewave");
?>
<!DOCTYPE html>

<head>
    <title>Fund Wallet | <?php echo $get_all_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="<?php echo $get_all_site_details["primary_color"] ?? "#198754"; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">
    <script type="text/javascript" src="https://sdk.monnify.com/plugin/monnify.js" defer></script>
    <script src="https://checkout.flutterwave.com/v3.js" defer></script>
    <script src="https://js.paystack.co/v1/inline.js" defer></script>
    <script src="https://merchant.beewave.ng/checkout.min.js" defer></script>

    <script src="/jsfile/bc-custom-all.js" defer></script>
    <script>
        function monnifyPaymentGateway() {
            payWithMonnify();
        }

        function flutterwavePaymentGateway() {
            makePaymentFlutterwave();
        }

        function paystackPaymentGateway() {
            makePaymentPaystack();
        }

        function beewavePaymentGateway() {
            makePaymentBeewave();
        }

        //MONNIFY CHECKOUT GATEWAY
        function payWithMonnify() {
            setTimeout(() => {
                MonnifySDK.initialize({
                    amount: document.getElementById("amount-to-pay").value,
                    currency: "NGN",
                    reference: document.getElementById("num-ref").value,
                    customerFullName: document.getElementById("user-name").value,
                    customerEmail: document.getElementById("user-email").value,
                    apiKey: document.getElementById("gateway-public").value,
                    contractCode: document.getElementById("gateway-encrypt").value,
                    paymentDescription: "Wallet Funding",
                    metadata: {
                        "name": "",
                        "age": ""
                    },
                    incomeSplitConfig: [],
                    onLoadStart: () => {
                        console.log("loading has started");
                    },
                    onLoadComplete: () => {
                        console.log("SDK is UP");
                    },
                    onComplete: function (response) {
                        //Implement what happens when the transaction is completed.
                        window.location.href = "/web/Dashboard.php";
                    },
                    onClose: function (data) {
                        //Implement what should happen when the modal is closed here
                        //window.location.href = "/web/Dashboard.php";
                    }
                });
            }, 0);
        }

        //FLUTTERWAVE CHECKOUT GATEWAY
        function makePaymentFlutterwave() {
            setTimeout(() => {
                FlutterwaveCheckout({
                    public_key: document.getElementById("gateway-public").value,
                    tx_ref: document.getElementById("num-ref").value,
                    amount: document.getElementById("amount-to-pay").value,
                    currency: "NGN",
                    payment_options: "card, banktransfer, ussd",
                    redirect_url: "",
                    meta: {
                        consumer_id: "",
                        consumer_mac: "",
                    },
                    customer: {
                        email: document.getElementById("user-email").value,
                        phone_number: document.getElementById("user-phone").value,
                        name: document.getElementById("user-name").value,
                    },
                    customizations: {
                        title: "",
                        description: "",
                        logo: "",
                    },
                    callback: function (payment) {
                        window.location.href = "/web/Dashboard.php";
                    }
                });
            }, 0);
        }

        //PAYSTACK CHECKOUT GATEWAY
        function makePaymentPaystack() {
            setTimeout(() => {
                let handler = PaystackPop.setup({
                    key: document.getElementById("gateway-public").value, // Replace with your public key
                    email: document.getElementById("user-email").value,
                    amount: document.getElementById("amount-to-pay").value * 100,
                    currency: 'NGN', // Use GHS for Ghana Cedis or USD for US Dollars
                    ref: document.getElementById("num-ref").value, // Replace with a reference you generated

                    // label: "Optional string that replaces customer email"
                    onClose: function () {
                        //window.location.href = "/web/Dashboard.php";
                    },
                    callback: function (response) {
                        window.location.href = "/web/Dashboard.php";
                    }
                });
                handler.openIframe();
            }, 0);
        }

        //BEEWAVE CHECKOUT GATEWAY
        function makePaymentBeewave() {
            setTimeout(() => {
                BeefinanceCheckout.open({
                    accessKey: document.getElementById("gateway-public").value,
                    name: document.getElementById("user-name").value,
                    email: '<?php echo str_replace([".", "-"], "", $_SERVER["HTTP_HOST"])."-".$get_logged_user_details['username']."-".$get_logged_user_details['email']; ?>',
                    phone: document.getElementById("user-phone").value,
                    amount: document.getElementById("amount-to-pay").value
                });
            }, 0);
        }
    </script>
    
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
      <h1>ATM FUNDING</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">ATM Funding</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
		<div class="card info-card sales-card">
			<div class="card-body">
				<h5 class="card-title">Wallet Balance <span>| <?php echo "N".number_format($get_logged_user_details["balance"] ?? 0, 2); ?></span></h5>
			</div>
		</div>

    
    <div class="card info-card px-5 py-5">

        <form method="post" action="">
        <div style="text-align: center; user-select: auto;"
            class="container col-12 col-lg-8 justify-items-center justify-content-between">
                      
                <?php
                foreach ($payment_gateway_array as $gateway_name) {
                    $get_gateway_details = ($connection_server && isset($get_logged_user_details)) ? mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_payment_gateways WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && gateway_name='$gateway_name'") : false;
                    if (in_array($get_gateway_details["status"], array(1, 2))) {
                        if ($get_gateway_details["status"] == 1) {
                            $gateway_status = '<img alt="' . ucwords(trim($get_gateway_details["gateway_name"])) . '" id="' . strtolower(trim($get_gateway_details["gateway_name"])) . '-lg" product-status="enabled" gateway-public="' . trim($get_gateway_details["public_key"]) . '" gateway-encrypt="' . trim($get_gateway_details["encrypt_key"]) . '" gateway-int="' . trim($get_gateway_details["percentage"]) . '" product-name-array="' . implode(",", $payment_gateway_array) . '" src="/asset/' . strtolower(trim($get_gateway_details["gateway_name"])) . '.jpg" onclick="tickPaymentGateway(this, `' . strtolower(trim($get_gateway_details["gateway_name"])) . '`, `gatewayname`, `fundProceedBtn`, `jpg`);" class="col-2 rounded-5 border m-1"/>';
                        } else {
                            $gateway_status = '<img alt="' . ucwords(trim($get_gateway_details["gateway_name"])) . '" id="' . strtolower(trim($get_gateway_details["gateway_name"])) . '-lg" product-status="disabled" src="/asset/' . strtolower(trim($get_gateway_details["gateway_name"])) . '.jpg" class="col-2 rounded-5 border m-1"/>';
                        }
                    } else {
                        $gateway_status = '';
                    }

                    echo $gateway_status;
                }
                ?>
            </div><br />
            <input id="gatewayname" name="" type="text" placeholder="Gateway Name" hidden readonly required />
            <input id="amount-to-pay" name="" type="text" placeholder="" hidden readonly required />
            <input id="user-name" name="" type="text"
                value="<?php echo ($get_logged_user_details['firstname'] ?? '') . " " . ($get_logged_user_details['lastname'] ?? '') . " " . ($get_logged_user_details['othername'] ?? ''); ?>"
                placeholder="" hidden readonly required />
            <input id="user-email" name="" type="email" value="<?php echo $get_logged_user_details['email'] ?? ''; ?>"
                placeholder="" hidden readonly required />
            <input id="user-phone" name="" type="number" value="<?php echo $get_logged_user_details['phone_number'] ?? ''; ?>"
                placeholder="" hidden readonly required />
            <input id="num-ref" name="" type="number" value="" placeholder="" hidden readonly required />
            <input id="gateway-public" name="" type="text" placeholder="" hidden readonly required />
            <input id="gateway-encrypt" name="" type="text" placeholder="" hidden readonly required />
            <input style="text-align: center;" id="fund-amount" name="" type="text" inputmode="numeric" pattern="[0-9]*" value=""
                onkeyup="checkPaymentGatewayDetails('fundProceedBtn','2');" placeholder="Amount e.g 100"
                min="100" title="Charater must be atleast 3 digit"
                class="form-control mb-1"
                required /><br />
            <button id="fundProceedBtn" name="" type="button" onclick=""
                style="pointer-events: none; user-select: auto;"
                class="btn btn-success col-12">
                PROCEED
            </button><br>
            <div style="text-align: center;"
                class="col-12 mt-1">
                <span id="product-status-span" class="h5" style="user-select: auto;"></span>
            </div>
        </form>
    </div>
  </div>
</section>
    <?php include("../func/bc-footer.php"); ?>
</body>

</html>