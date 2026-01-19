<?php session_start();
include("../func/bc-config.php");

if (isset($_POST["buy-electric"])) {
    $purchase_method = "web";
    $action_function = 1;
    include_once("func/electric.php");
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    unset($_SESSION["meter_amount"]);
    unset($_SESSION["meter_number"]);
    unset($_SESSION["meter_provider"]);
    unset($_SESSION["meter_type"]);
    unset($_SESSION["meter_name"]);
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["verify-meter"])) {
    $purchase_method = "web";
    $action_function = 3;
    include_once("func/electric.php");
    $json_response_decode = json_decode($json_response_encode, true);
    if ($json_response_decode["status"] == "success") {
        $_SESSION["meter_amount"] = $amount;
        $_SESSION["meter_number"] = $meter_number;
        $_SESSION["meter_provider"] = $epp;
        $_SESSION["meter_type"] = $type;
        $_SESSION["meter_name"] = $json_response_decode["desc"];
    }

    if ($json_response_decode["status"] == "failed") {
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    }
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["reset-electric"])) {
    unset($_SESSION["meter_amount"]);
    unset($_SESSION["meter_number"]);
    unset($_SESSION["meter_provider"]);
    unset($_SESSION["meter_type"]);
    unset($_SESSION["meter_name"]);
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

?>
<!DOCTYPE html>

<head>
    <title>Utility Bills | <?php echo $get_all_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="<?php echo $get_all_site_details["primary_color"] ?? "#198754"; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
        <h1>BUY ELECTRIC</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Buy Electric</li>
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
                    <?php if (!isset($_SESSION["meter_name"])) { ?>
                        <div style="text-align: center; user-select: auto;" class="container">
                            <img alt="ekedc" id="ekedc-lg" product-status="enabled" src="/asset/ekedc.jpg"
                                onclick="tickElectricCarrier('ekedc'); resetElectricQuantity();"
                                class="col-2 rounded-5 border m-1 " />
                            <img alt="eedc" id="eedc-lg" product-status="enabled" src="/asset/eedc.jpg"
                                onclick="tickElectricCarrier('eedc'); resetElectricQuantity();"
                                class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1" />
                            <img alt="ikedc" id="ikedc-lg" product-status="enabled" src="/asset/ikedc.jpg"
                                onclick="tickElectricCarrier('ikedc'); resetElectricQuantity();"
                                class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1" />
                            <img alt="jedc" id="jedc-lg" product-status="enabled" src="/asset/jedc.jpg"
                                onclick="tickElectricCarrier('jedc'); resetElectricQuantity();"
                                class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1" />
                            <img alt="kedco" id="kedco-lg" product-status="enabled" src="/asset/kedco.jpg"
                                onclick="tickElectricCarrier('kedco'); resetElectricQuantity();"
                                class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1" />
                            <img alt="ibedc" id="ibedc-lg" product-status="enabled" src="/asset/ibedc.jpg"
                                onclick="tickElectricCarrier('ibedc'); resetElectricQuantity();"
                                class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1" />
                            <img alt="phed" id="phed-lg" product-status="enabled" src="/asset/phed.jpg"
                                onclick="tickElectricCarrier('phed'); resetElectricQuantity();"
                                class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1" />
                            <img alt="aedc" id="aedc-lg" product-status="enabled" src="/asset/aedc.jpg"
                                onclick="tickElectricCarrier('aedc'); resetElectricQuantity();"
                                class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1" />
                            <img alt="yedc" id="yedc-lg" product-status="enabled" src="/asset/yedc.jpg"
                                onclick="tickElectricCarrier('yedc'); resetElectricQuantity();"
                                class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1" />
                            <img alt="bedc" id="bedc-lg" product-status="enabled" src="/asset/bedc.jpg"
                                onclick="tickElectricCarrier('bedc'); resetElectricQuantity();"
                                class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1" />
                            <img alt="aba" id="aba-lg" product-status="enabled" src="/asset/aba.jpg"
                                onclick="tickElectricCarrier('aba'); resetElectricQuantity();"
                                class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1" />
                            <img alt="kaedco" id="kaedco-lg" product-status="enabled" src="/asset/kaedco.jpg"
                                onclick="tickElectricCarrier('kaedco'); resetElectricQuantity();"
                                class="col-2 rounded-5 border m-1 m-margin-lt-1 s-margin-lt-1" />
                        </div><br />
                        <input id="electricname" name="epp" type="text" placeholder="electric Name" hidden readonly
                            required />
                        <select style="text-align: center;" id="meter-type" name="type" onchange="pickElectricQty();"
                            class="form-control mb-1" required />
                        <option value="" default hidden selected>Meter Type</option>
                        <option value="prepaid">Prepaid</option>
                        <option value="postpaid">Postpaid</option>
                        </select><br />
                        <input style="text-align: center;" id="meter-number" name="meter-number"
                            onkeyup="pickElectricQty();" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="Meter Number"
                            title="Charater must be atleast 10 digit" class="form-control mb-1" required /><br />
                        <input style="text-align: center;" id="product-amount" name="amount" onkeyup="pickElectricQty();"
                            type="text" inputmode="numeric" pattern="[0-9]*" placeholder="Amount" title="Charater must be atleast 3 digit"
                            class="form-control mb-1" required /><br />
                    <?php } else { ?>
                        <div style="text-align: center; user-select: auto;" style="container">
                            <img alt="<?php echo $_SESSION['meter_provider']; ?>"
                                id="<?php echo $_SESSION['meter_provider']; ?>-lg"
                                src="/asset/<?php echo $_SESSION['meter_provider']; ?>.jpg" class="col-8 col-lg-5 " /><br />
                            <div style="text-align: left;" class="container mb-1">
                                <span class="h5" style="user-select: auto;">Full-Name: <span
                                        class="h4 fw-bold"><?php echo strtoupper($_SESSION['meter_name']); ?></span></span><br />
                                <span class="h5" style="user-select: none">Meter Number: <span
                                        class="h4 fw-bold"><?php echo $_SESSION['meter_number']; ?></span></span><br />
                                <span class="h5" style="user-select: auto;">Meter Type: <span
                                        class="h4 fw-bold"><?php echo strtoupper($_SESSION['meter_type']); ?></span></span><br />
                                <span class="h5" style="user-select: auto;">Amount To Pay: <span
                                        class="h4 fw-bold">N<?php echo $_SESSION['meter_amount']; ?></span></span>
                            </div>
                        </div><br />
                    <?php } ?>

                    <?php if (!isset($_SESSION["meter_name"])) { ?>
                        <button id="proceedBtn" name="verify-meter" type="button"
                            style="pointer-events: none; user-select: auto;" class="btn btn-success mb-1 col-12">
                            VERIFY METER
                        </button><br>
                    <?php } else { ?>
                        <button id="" name="buy-electric" type="submit" style="user-select: auto;"
                            class="btn btn-success mb-1 col-12">
                            BUY ELECTRIC
                        </button><br>
                        <button id="" name="reset-electric" type="submit" style="user-select: auto;"
                            class="btn btn-warning mb-1 col-12">
                            RESET METER DETAILS
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