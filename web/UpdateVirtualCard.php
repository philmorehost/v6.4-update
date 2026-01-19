<?php session_start();
include("../func/bc-config.php");

if (isset($_POST["update-card-pin"])) {
    $purchase_method = "web";
    $action_function = 2;
    include_once("func/virtualcard.php");
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-card-status"])) {
    $purchase_method = "web";
    $action_function = 3;
    include_once("func/virtualcard.php");
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

?>
<!DOCTYPE html>

<head>
    <title>Update Virtual Card | <?php echo $get_all_site_details["site_title"]; ?></title>
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
        <h1>UPDATE VIRTUAL CARD</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Update Virtual Card</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="col-12">


            <div class="card info-card px-5 py-5">
                <span style="user-select: auto;" class="h3">CHANGE CARD PIN</span><br>
                <form method="post" enctype="multipart/form-data" action="">
                    <label for="card-ref" class="mb-2">Select Card</label><br />
                    <select style="text-align: center;" id="card-ref" name="card-ref" class="form-select mb-3"
                        onchange="selectCardType(this);" required />
                    <option value="" default hidden selected>Choose Card</option>
                    <?php
                    $select_virtual_cards = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $_SESSION["user_session"] . "'");
                    if (mysqli_num_rows($select_virtual_cards) >= 1) {
                        while ($get_virtual_card = mysqli_fetch_array($select_virtual_cards)) {
                            echo '<option card-type="' . $get_virtual_card["card_type"] . '" value="' . $get_virtual_card["reference"] . '">' . $get_virtual_card["fullname"] . ' (' . $get_virtual_card["card_number"] . ') (' . strtoupper($get_virtual_card["card_brand"]) . ')</option>';
                        }
                    }
                    ?>
                    </select>

                    <input style="text-align: center;" id="card-type" name="type" type="text" value=""
                        placeholder="Card Type e.g Dollarcard" class="form-control mb-3" hidden readonly required />

                    <button id="proceedBtn" name="update-card-pin" type="submit"
                        style="pointer-events: auto; user-select: auto;" class="btn btn-success mb-1 col-12">
                        UPDATE CARD PIN
                    </button><br>
                    <div style="text-align: center;" class="col-8">
                        <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                    </div>
                </form>
            </div>

            <div class="card info-card px-5 py-5">
                <span style="user-select: auto;" class="h3">CHANGE CARD STATUS</span><br>
                <form method="post" enctype="multipart/form-data" action="">
                    <label for="card-ref" class="mb-2">Select Card</label><br />
                    <select style="text-align: center;" id="card-ref" name="card-ref" class="form-select mb-3"
                        onchange="selectCardStatus(this);" required />
                    <option value="" default hidden selected>Choose Card</option>
                    <?php
                    $select_virtual_cards = mysqli_query($connection_server, "SELECT * FROM sas_virtualcard_purchaseds WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $_SESSION["user_session"] . "'");
                    if (mysqli_num_rows($select_virtual_cards) >= 1) {
                        while ($get_virtual_card = mysqli_fetch_array($select_virtual_cards)) {
                            echo '<option card-type="' . $get_virtual_card["card_type"] . '" card-status="' . $get_virtual_card["card_status"] . '" value="' . $get_virtual_card["reference"] . '">' . $get_virtual_card["fullname"] . ' (' . $get_virtual_card["card_number"] . ') (' . strtoupper($get_virtual_card["card_brand"]) . ')</option>';
                        }
                    }
                    ?>
                    </select>

                    <label for="card-status" class="mb-2">New Status</label><br />
                    <select style="text-align: center;" id="card-status" name="card-status" class="form-select mb-3"
                        required />
                    <option value="" default hidden selected>Choose Status</option>
                    <option value="active">Active</option>
                    <option value="blocked">Blocked</option>
                    </select>

                    <input style="text-align: center;" id="card-status-type" name="type" type="text" value=""
                        placeholder="Card Type e.g Dollarcard" class="form-control mb-3" hidden readonly required />


                    <button id="proceedBtn" name="update-card-status" type="submit"
                        style="pointer-events: auto; user-select: auto;" class="btn btn-success mb-1 col-12">
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
                            document.getElementById("card-status").value = getSelectedCardStatus;
                            document.getElementById("card-status-type").value = getSelectedCardType;
                        }

                        function selectCardType(select) {
                            const selectElement = select;
                            const getSelectedCardType = selectElement.options[selectElement.selectedIndex].getAttribute("card-type");
                            document.getElementById("card-type").value = getSelectedCardType;
                        }
                    </script>
            </div>


        </div>
    </section>

    <?php include("../func/bc-footer.php"); ?>

</body>

</html>