<?php session_start([
    'cookie_lifetime' => 286400,
    'gc_maxlifetime' => 286400,
]);
include("../func/bc-config.php");

if (isset($_POST["initiate-transfer"])) {
    $purchase_method = "web";
    $action_function = 1;
    include_once("func/bank-transfer.php");
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    unset($_SESSION["amount"]);
    unset($_SESSION["transfer_fee"]);
    unset($_SESSION["account_number"]);
    unset($_SESSION["transfer_enquiry_id"]);
    unset($_SESSION["bank_code"]);
    unset($_SESSION["bank_name"]);
    unset($_SESSION["account_name"]);
    unset($_SESSION["narration"]);
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["verify-bank"])) {
    $purchase_method = "web";
    $action_function = 3;
    include_once("func/bank-transfer.php");
    $json_response_decode = json_decode($json_response_encode, true);
    if ($json_response_decode["status"] == "success") {
        $_SESSION["amount"] = $amount;
        $_SESSION["transfer_fee"] = $transfer_fee;
        $_SESSION["account_number"] = $account_number;
        $_SESSION["transfer_enquiry_id"] = $json_response_decode["enquiry_id"];
        $_SESSION["bank_code"] = $bank_code;
        $_SESSION["bank_name"] = $json_response_decode["bank_name"];
        $_SESSION["account_name"] = $json_response_decode["customer_name"];
        $_SESSION["narration"] = $json_response_decode["narration"];
    }

    if ($json_response_decode["status"] == "failed") {
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    }
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["reset-bank"])) {
    unset($_SESSION["amount"]);
    unset($_SESSION["transfer_fee"]);
    unset($_SESSION["account_number"]);
    unset($_SESSION["transfer_enquiry_id"]);
    unset($_SESSION["bank_code"]);
    unset($_SESSION["bank_name"]);
    unset($_SESSION["account_name"]);
    unset($_SESSION["narration"]);
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

?>
<!DOCTYPE html>

<head>
    <title>Bank Transfer | <?php echo $get_all_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="<?php echo $get_all_site_details["primary_color"] ?? "#198754"; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">

    <script src="/jsfile/bc-custom-all.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

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
        <h1>BANK TRANSFER - NIP</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Bank Transfer</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="col-12">
            <div class="card info-card px-5 py-5">
                <span class="text text-dark h5 fw-bold">
                    Local Transfer (NGN)
                </span><br />

                <?php if (!isset($_SESSION["transfer_enquiry_id"])) { ?>
                    <form method="post" action="">
                        <select name="bank-code" class="form-select mb-3" style="text-align: center;">
                            <option> -- Choose Bank -- </option>
                            <?php
                            $retrieve_bank_list = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/func/banks.json");
                            $retrieve_bank_list = json_decode($retrieve_bank_list, true);
                            if (is_array($retrieve_bank_list)) {
                                foreach ($retrieve_bank_list as $each_bank) {
                                    $each_bank_json = $each_bank;
                                    echo '<option value="' . $each_bank_json["bankCode"] . '">' . $each_bank_json["bankName"] . '</option>';
                                }
                            }
                            ?>
                        </select>

                        <input style="text-align: center;" id="fund-amount" name="account-number" type="text" value=""
                            pattern="[0-9]{10}" placeholder="Account Number e.g 81XXXXXXXX"
                            title="Account number must be 10 digit" class="form-control mb-1" required /><br />

                        <input style="text-align: center;" id="fund-amount" name="amount" type="number" value=""
                            pattern="[0-9]{3}" placeholder="Amount e.g 100" title="Charater must be atleast 3 digit"
                            class="form-control mb-1" autocomplete="off" required /><br />
                        <input style="text-align: center;" id="narration" name="narration" type="text" value=""
                            placeholder="Narration" title="Narration must be added" class="form-control mb-1"
                            required /><br />
                        <button name="verify-bank" type="submit" style="user-select: auto;" class="btn btn-success col-12">
                            VERIFY
                        </button><br>
                        <div style="text-align: center;" class="col-12 mt-1">
                            <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                        </div>
                    </form>
                <?php } else { ?>
                    <form method="post" action="">
                        <div style="text-align: left;" class="col-12 mb-3 lh-lg">
                            <span class="h5" style="user-select: auto;">Bank Name:
                                <?php echo $_SESSION["bank_name"]; ?></span><br />
                            <span class="h5" style="user-select: auto;">Account Name:
                                <?php echo $_SESSION["account_name"]; ?></span><br />
                            <span class="h5" style="user-select: auto;">Account Number:
                                <?php echo $_SESSION["account_number"]; ?></span><br />
                            <span class="h5" style="user-select: auto;">Transfer Fee:
                                <?php echo $_SESSION["transfer_fee"]; ?></span><br />
                            <span class="h5" style="user-select: auto;">Amount to Transfer:
                                <?php echo $_SESSION["amount"]; ?></span>
                            <br />
                            <span class="h5" style="user-select: auto;">Amount to be Charged:
                                <?php echo (floatval($_SESSION["amount"]) + floatval($_SESSION["transfer_fee"])); ?></span><br />
                            <span class="h5" style="user-select: auto;">Narration:
                                <?php echo $_SESSION["narration"]; ?></span><br />
                        </div>
                        <button name="initiate-transfer" type="submit" style="user-select: auto;"
                            class="btn btn-success col-12 mb-3">
                            INITIATE TRANSFER
                        </button><br>
                    </form>

                    <form method="post" action="">
                        <button name="reset-bank" type="submit" style="user-select: auto;" class="btn btn-success col-12">
                            RESET DETAILS
                        </button><br>
                    </form>
                <?php } ?>
            </div>
        </div>
    </section>

    <?php include("../func/bc-footer.php"); ?>
</body>

</html>