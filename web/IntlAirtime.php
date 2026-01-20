<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-config.php");

    if(isset($_POST["buy-airtime"])){
        $purchase_method = "web";
        // Since we don't have a dedicated intl-airtime.php yet,
        // we'll handle it here or create a new one.
        // For simplicity, I'll create web/func/intl-airtime.php
		include_once("func/intl-airtime.php");
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }

?>
<!DOCTYPE html>
<head>
<title>International Airtime | <?php echo $get_all_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
  <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets-2/css/style.css" rel="stylesheet">
</head>
<body>
	<?php include("../func/bc-header.php"); ?>

	<div class="pagetitle d-none d-md-block">
      <h1>INTERNATIONAL AIRTIME</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Buy International Airtime</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="col-12">
		<div class="card info-card sales-card">
			<div class="card-body">
				<h5 class="card-title">Wallet Balance <span>| <?php echo "N".number_format($get_logged_user_details["balance"], 2); ?></span></h5>
			</div>
		</div>


    <div class="card info-card px-5 py-5">
        <form method="post" action="">
                <div class="mb-3">
                    <label class="form-label">Country</label>
                    <select id="country-select" name="country_code" class="form-select" onchange="fetchOperators(this.value)" required>
                        <option value="">Select Country</option>
                        <?php
                        $countries = mysqli_query($connection_server, "SELECT DISTINCT country_code, country_name FROM sas_intl_airtime_operators WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' AND status='1' ORDER BY country_name ASC");
                        while($c = mysqli_fetch_assoc($countries)){
                            echo '<option value="'.$c["country_code"].'">'.$c["country_name"].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Product Type</label>
                    <select id="type-select" name="product_type_id" class="form-select" onchange="fetchOperators(this.value)" required>
                        <option value="">Select Product Type</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Operator</label>
                    <select id="operator-select" name="operator_id" class="form-select" onchange="fetchVariations(this.value)" required>
                        <option value="">Select Operator</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Package/Variation</label>
                    <select id="variation-select" name="variation_code" class="form-select" onchange="handleVariationChange(this)" required>
                        <option value="">Select Variation</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone Number (with country code)</label>
                    <input style="text-align: center;" name="phone-number" type="text" placeholder="e.g. 233241234567" class="form-control" required/>
                    <small class="text-muted">Ensure to include the country code (e.g. 233 for Ghana)</small>
                </div>
                <div class="mb-3" id="amount-container">
                    <label class="form-label" id="amount-label">Amount (NGN)</label>
                    <input style="text-align: center;" id="amount-input" name="amount" type="number" placeholder="Amount in NGN" class="form-control" required/>
                </div>

                <button name="buy-airtime" type="submit" class="btn btn-success w-100">
                    BUY INTERNATIONAL AIRTIME
                </button>
            </form>
        </div>
    </div>
</section>
	<?php include("../func/bc-footer.php"); ?>
    <script>
    function fetchProductTypes(countryCode) {
        const typeSelect = document.getElementById('type-select');
        const opSelect = document.getElementById('operator-select');
        const varSelect = document.getElementById('variation-select');

        typeSelect.innerHTML = '<option value="">Loading Types...</option>';
        opSelect.innerHTML = '<option value="">Select Operator</option>';
        varSelect.innerHTML = '<option value="">Select Variation</option>';

        if(!countryCode) return;

        fetch('api/intl-product-types.php?country_code=' + countryCode)
            .then(response => response.json())
            .then(data => {
                typeSelect.innerHTML = '<option value="">Select Product Type</option>';
                data.forEach(t => {
                    const option = document.createElement('option');
                    option.value = t.product_type_id;
                    option.textContent = t.product_type_name;
                    typeSelect.appendChild(option);
                });
            });
    }

    function fetchOperators(productTypeId) {
        const countryCode = document.getElementById('country-select').value;
        const opSelect = document.getElementById('operator-select');
        const varSelect = document.getElementById('variation-select');

        opSelect.innerHTML = '<option value="">Loading Operators...</option>';
        varSelect.innerHTML = '<option value="">Select Variation</option>';

        if(!productTypeId) return;

        fetch('api/intl-operators.php?country_code=' + countryCode + '&product_type_id=' + productTypeId)
            .then(response => response.json())
            .then(data => {
                opSelect.innerHTML = '<option value="">Select Operator</option>';
                data.forEach(op => {
                    const option = document.createElement('option');
                    option.value = op.operator_id;
                    option.textContent = op.operator_name;
                    opSelect.appendChild(option);
                });
            });
    }

    function fetchVariations(operatorId) {
        const productTypeId = document.getElementById('type-select').value;
        const varSelect = document.getElementById('variation-select');

        varSelect.innerHTML = '<option value="">Loading Variations...</option>';

        if(!operatorId) return;

        fetch('api/intl-variations.php?operator_id=' + operatorId + '&product_type_id=' + productTypeId)
            .then(response => response.json())
            .then(data => {
                varSelect.innerHTML = '<option value="">Select Variation</option>';
                if(data.content && data.content.variations) {
                    data.content.variations.forEach(v => {
                        const option = document.createElement('option');
                        option.value = v.variation_code;
                        option.textContent = v.name;
                        option.setAttribute('data-amount', v.variation_amount);
                        option.setAttribute('data-fixed', v.fixedPrice);
                        varSelect.appendChild(option);
                    });
                }
            });
    }

    function handleVariationChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const amountInput = document.getElementById('amount-input');
        const amountContainer = document.getElementById('amount-container');
        const amountLabel = document.getElementById('amount-label');

        if (selectedOption.value) {
            const amount = selectedOption.getAttribute('data-amount');
            const isFixed = selectedOption.getAttribute('data-fixed') === 'Yes';

            if (isFixed && parseFloat(amount) > 0) {
                amountInput.value = amount;
                amountInput.readOnly = true;
                amountLabel.textContent = "Price (NGN)";
            } else {
                amountInput.value = "";
                amountInput.readOnly = false;
                amountLabel.textContent = "Amount (NGN)";
            }
        }
    }

    document.getElementById('country-select').onchange = function() {
        fetchProductTypes(this.value);
    };
    </script>
</body>
</html>
