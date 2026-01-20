<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
include("../func/bc-config.php");

if (isset($_POST["buy-card-holder"])) {
    $purchase_method = "web";
    $action_function = 0;
    include_once("func/virtualcard.php");
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

?>
<!DOCTYPE html>

<head>
    <title>Create CAD e-Transfer Beneficiary | <?php echo $get_all_site_details["site_title"]; ?></title>
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
        <h1>CREATE CAD BENEFICIARY</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Create CAD e-Transfer Beneficiary</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="col-12">


            <div class="card info-card px-5 py-5">
                <form method="post" enctype="multipart/form-data" action="">
                    <input style="text-align: center;" id="" name="firstname" onkeyup="" type="text"
                        value="" placeholder="Firstame"
                        class="form-control mb-1" readonly /><br />
                    <input style="text-align: center;" id="" name="lastname" onkeyup="" type="text"
                        value="" placeholder="Lastname"
                        class="form-control mb-1" readonly /><br />
                    <input style="text-align: center;" id="" name="email" onkeyup="" type="text"
                        value="" placeholder="Email"
                        class="form-control mb-1" readonly /><br />
                    <input style="text-align: center;" id="" name="postal-code" onkeyup="" type="text" value=""
                        placeholder="Postal code" class="form-control mb-1" /><br />
                    <input style="text-align: center;" id="" name="address-1" onkeyup="" type="text"
                        value="" placeholder="Line 1"
                        class="form-control mb-1" readonly /><br />
                    <input style="text-align: center;" id="" name="address-2" onkeyup="" type="text"
                        value="" placeholder="Line 2"
                        class="form-control mb-1" readonly /><br />
                    <input style="text-align: center;" id="" name="city" onkeyup="" type="text" value=""
                        placeholder="City" class="form-control mb-1" /><br />
                    <input style="text-align: center;" id="" name="state" onkeyup="" type="text" value=""
                        placeholder="State" class="form-control mb-1" /><br />
                    <select style="text-align: center;" id="" name="country" onchange="" class="form-control mb-4"
                        required />
                    <option value="" default hidden selected>Country</option>
                    <option value="ca">Canada</option>
                    </select>
                    
                    
                    <input style="text-align: center;" id="" name="kyc-id" onkeyup="" type="text" value=""
                        placeholder="KYC BVN" class="form-control mb-1" /><br />
                    <div style="text-align: center;" class="container">
                        <label class="h6 mb-1">Selfie / ID Card Image</label>
                        <input type="file" role="uploadcare-uploader" name="id-selfie-image" id="selfie-image"
                            data-crop="1:1" data-images-only data-source="camera" class="form-control mb-1" />
                        <img src="" id="preview" accept="image/*" width="150" height="0" style="object-position: cover;"
                            class="rounded-5 my-1" />
                        <input type="hidden" style="text-align: center;" id="selfie-dataurl" name="selfie-dataurl"
                            type="text" value="" placeholder="Selfie Image" class="form-control mb-1" name="selfie-url"
                            required /><br />
                    </div>
                    <script>
                        const fileInput = document.getElementById('selfie-image');
                        const preview = document.getElementById('preview');
                        const selfieDataurl = document.getElementById('selfie-dataurl');

                        fileInput.addEventListener('change', function () {
                            const file = this.files[0];
                            if (file) {
                                const reader = new FileReader();

                                reader.onload = function (e) {
                                    const dataURL = e.target.result;
                                    preview.style.height = "150px";
                                    preview.src = dataURL;
                                    selfieDataurl.value = dataURL;
                                    // If you want to store or submit it:
                                    console.log(dataURL); // data:image/jpeg;base64,...
                                };

                                reader.readAsDataURL(file);
                            }
                        });
                    </script>

                    <button id="proceedBtn" name="buy-card-holder" type="submit"
                        style="pointer-events: auto; user-select: auto;" class="btn btn-success mb-1 col-12">
                        GENERATE CARD
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