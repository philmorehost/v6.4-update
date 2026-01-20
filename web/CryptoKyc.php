<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
include("../func/bc-config.php");

if (isset($_POST["create-crypto-kyc"])) {
    $purchase_method = "web";
    $action_function = 0;
    include_once("func/crypto.php");
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

?>
<!DOCTYPE html>

<head>
    <title>Crypto KYC | <?php echo $get_all_site_details["site_title"]; ?></title>
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
        <h1>CRYPTO KYC</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Crypto KYC</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="col-12">


            <div class="card info-card px-5 py-5">
                <form method="post" enctype="multipart/form-data" action="">
                    <input style="text-align: center;" id="" name="firstname" onkeyup="" type="text"
                        value="<?php echo $get_logged_user_details['firstname']; ?>" placeholder="Firstame"
                        class="form-control mb-1" readonly /><br />
                    <input style="text-align: center;" id="" name="lastname" onkeyup="" type="text"
                        value="<?php echo $get_logged_user_details['lastname']; ?>" placeholder="Lastname"
                        class="form-control mb-1" readonly /><br />
                    <input style="text-align: center;" id="" name="email" onkeyup="" type="text"
                        value="<?php echo $get_logged_user_details['email']; ?>" placeholder="Email"
                        class="form-control mb-1" readonly /><br />
                    <input style="text-align: center;" id="" name="phone" onkeyup="" type="text"
                        value="<?php echo $get_logged_user_details['phone_number']; ?>" placeholder="Phone nuumber"
                        class="form-control mb-1" readonly /><br />
                    <input style="text-align: center;" id="" name="postal-code" onkeyup="" type="text" value=""
                        placeholder="Postal code" class="form-control mb-1" /><br />
                    <input style="text-align: center;" id="" name="address" onkeyup="" type="text"
                        value="<?php echo $get_logged_user_details['home_address']; ?>" placeholder="Address"
                        class="form-control mb-1" readonly /><br />
                    <input style="text-align: center;" id="" name="city" onkeyup="" type="text" value=""
                        placeholder="City" class="form-control mb-1" /><br />
                    <input style="text-align: center;" id="" name="state" onkeyup="" type="text" value=""
                        placeholder="State" class="form-control mb-1" /><br />
                    <select style="text-align: center;" id="" name="country" onchange="" class="form-control mb-4"
                        required />
                    <option value="" default hidden selected>Country</option>
                    <option value="nigeria">NIGERIA</option>
                    </select>
                    
                    <button id="proceedBtn" name="create-crypto-kyc" type="submit"
                        style="pointer-events: auto; user-select: auto;" class="btn btn-success mb-1 col-12">
                        COMPLETE KYC 
                    </button><br>
                    <div style="text-align: center;" class="col-8">
                        <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php include("../func/bc-footer.php"); ?>

</body>

</html>