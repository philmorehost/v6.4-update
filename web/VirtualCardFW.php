<?php session_start();
include("../func/bc-config.php");

if (isset($_POST["fund-virtual-card"])) {
    $purchase_method = "web";
    $action_function = 4;
    include_once("func/virtualcard.php");
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["withdraw-virtual-card"])) {
    $purchase_method = "web";
    $action_function = 5;
    include_once("func/virtualcard.php");
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

?>
<!DOCTYPE html>

<head>
    <title>Fund Virtual Card | <?php echo $get_all_site_details["site_title"]; ?></title>
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

    <div class="pagetitle">
        <h1>VIRTUAL CARD - FUNDING/WITHDRAWAL</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Virtual Card - Funding/Withdrawal</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="col-12">

            <div class="card info-card px-5 py-5">
                <span style="user-select: auto;" class="h3">CARD FUNDING/WITHDRAWAL</span><br>
                <form method="post" enctype="multipart/form-data" action="">
                    <label for="card-ref" class="mb-2">Select Card</label><br />
                    <select style="text-align: center;" id="card-ref" name="card-ref" class="form-select mb-3"
                        onchange="selectCardStatus(this);" required />
                    <option value="" default hidden selected>Choose Card</option>
                    <?php
                    $select_virtual_cards = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $_SESSION["user_session"] . "'");
                    if (mysqli_num_rows($select_virtual_cards) >= 1) {
                        while ($get_virtual_card = mysqli_fetch_array($select_virtual_cards)) {
                            echo '<option card-type="' . $get_virtual_card["card_type"] . '" card-brand="' . $get_virtual_card["card_brand"] . '" card-status="' . $get_virtual_card["card_status"] . '" card-balance="' . toDecimal($get_virtual_card["card_balance"], 2) . '" value="' . $get_virtual_card["reference"] . '">' . $get_virtual_card["fullname"] . ' (' . $get_virtual_card["card_number"] . ') (' . strtoupper($get_virtual_card["card_brand"]) . ')</option>';
                        }
                    }
                    ?>
                    </select>

                    <label for="card-status" class="mb-2">Card Status</label><br />
                    <input style="text-align: center;" id="card-status" name="card-status" type="text" value=""
                        placeholder="" class="form-control mb-3" readonly required />

                    <label for="card-status" class="mb-2">Current Balance</label><br />
                    <input style="text-align: center;" id="card-balance" name="" type="text" value="" placeholder=""
                        class="form-control mb-3" readonly required />

                    <label for="card-status" class="mb-2">Choose Transaction Type</label><br />
                    <select style="text-align: center;" onchange="changeTransType(this);" name=""
                        class="form-select mb-3" readonly required />
                    <option value="" default hidden selected>Choose Status</option>
                    <option value="withdraw" selected>Withdraw Fund</option>
                    <option value="fund">Funding Card</option>
                    </select>

                    <label for="card-status" id="trans-type-span" class="mb-2">Amount To Withdrawal</label><br />
                    <input style="text-align: center;" id="card-balance" name="amount" type="text" value=""
                        placeholder="" class="form-control mb-3" required />

                    <input style="text-align: center;" id="card-status-type" name="type" type="text" value=""
                        placeholder="Card Type e.g Dollarcard" class="form-control mb-3" hidden readonly required />

                    <input style="text-align: center;" id="card-brand" name="isp" type="text" value=""
                        placeholder="Card Type e.g Mastercard, Verve, Visa" class="form-control mb-3" hidden readonly
                        required />


                    <button id="proceedBtn" name="withdraw-virtual-card" type="submit" style="pointer-events: auto; user-select: auto;"
                        class="btn btn-success mb-1 col-12">
                        UPDATE CARD STATUS
                    </button><br>
                    <div style="text-align: center;" class="col-8">
                        <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                    </div>
                </form>

                <script>
                    function selectCardStatus(select) {
                        const selectElement = select;
                        const getSelectedCardStatus = selectElement.options[selectElement.selectedIndex].getAttribute("card-status");
                        const getSelectedCardType = selectElement.options[selectElement.selectedIndex].getAttribute("card-type");
                        const getSelectedCardBalance = selectElement.options[selectElement.selectedIndex].getAttribute("card-balance");
                        const getSelectedCardBrand = selectElement.options[selectElement.selectedIndex].getAttribute("card-brand");
                        document.getElementById("card-status").value = getSelectedCardStatus;
                        document.getElementById("card-status-type").value = getSelectedCardType;
                        document.getElementById("card-balance").value = getSelectedCardBalance;
                        document.getElementById("card-brand").value = getSelectedCardBrand;
                    }

                    function selectCardType(select) {
                        const selectElement = select;
                        const getSelectedCardType = selectElement.options[selectElement.selectedIndex].getAttribute("card-type");
                        document.getElementById("card-type").value = getSelectedCardType;
                    }

                    function changeTransType(select) {
                        const selectElement = select;
                        const getSelectedCardStatus = selectElement.value;
                        const getTransTypeSpan = document.getElementById("trans-type-span");
                        const getActionButton = document.getElementById("proceedBtn");
                        if (getSelectedCardStatus == "withdraw") {
                            getTransTypeSpan.innerText = "Amount To Withdrawal";
                            getActionButton.setAttribute("name", "withdraw-virtual-card");
                        }

                        if (getSelectedCardStatus == "fund") {
                            getTransTypeSpan.innerText = "Amount To Fund";
                            getActionButton.setAttribute("name", "fund-virtual-card");
                        }

                    }
                </script>
            </div>


        </div>
    </section>

    <?php include("../func/bc-footer.php"); ?>

</body>

</html>