<?php
include("../func/bc-connect.php");
include("../func/bc-func.php");

$reference = trim(basename($_SERVER['REQUEST_URI']), '/');

$checkmate_super_admin_table_exists = mysqli_query($connection_server, "SELECT * FROM sas_super_admin");
if (mysqli_num_rows($checkmate_super_admin_table_exists) >= 1) {
    unset($_SESSION["admin_to_user_redirect"]);

    $select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
    if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {
        $get_vendor_details = mysqli_fetch_assoc(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
    }
}

$stmt = $connection_server->prepare(
    "SELECT * FROM sas_payment_links WHERE vendor_id = ? AND reference = ? LIMIT 1"
);
$stmt->bind_param("is", $get_vendor_details["id"], $reference);
$stmt->execute();
$result = $stmt->get_result();

$payment_found = $result->num_rows === 1;
if ($payment_found) {
    $payment_link_details = $result->fetch_assoc();
}

$payment_gateway_array = array("monnify", "flutterwave", "paystack", "beewave");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Link</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            font-family: "Segoe UI", sans-serif;
        }

        body {
            background: #f8f9fb;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            display: flex;
            gap: 40px;
        }

        .payment-info,
        .payment-form {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
        }

        .payment-info {
            flex: 1;
        }

        .payment-form {
            width: 380px;
        }

        .info-row {
            margin-bottom: 20px;
            font-size: 15px;
        }

        .qty-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-box button {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid #ddd;
            background: #fff;
            cursor: pointer;
            font-size: 18px;
        }

        .amount-due {
            margin-top: 30px;
            font-size: 22px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .form-group input.error,
        .form-group select.error {
            border-color: #e63946;
        }

        .error-text {
            font-size: 12px;
            color: #e63946;
            margin-top: 4px;
            display: none;
        }

        .pay-btn {
            margin-top: 25px;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: none;
            background: #000;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
        }

        .payment-error {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            text-align: center;
            background: #fdecea;
            border-radius: 12px;
        }

        .payment-error h2 {
            color: #b00020;
        }

        @media(max-width: 900px) {
            .container {
                flex-direction: column;
            }

            .payment-form {
                width: 100%;
            }
        }
    </style>

    <script type="text/javascript" src="https://sdk.monnify.com/plugin/monnify.js"></script>
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://merchant.beewave.ng/checkout.min.js"></script>
</head>

<body>

    <?php if ($payment_found) { ?>
        <div class="container">
            <!-- LEFT -->
            <div class="payment-info">
                <h2>Payment Link</h2>
                <div class="info-row">
                    <strong>Description:</strong> <?= htmlspecialchars($payment_link_details["description"]) ?>
                </div>
                <div class="info-row">
                    <strong>Amount:</strong> ₦<span
                        id="unitPrice"><?= toDecimal($payment_link_details["amount"], 2) ?></span>
                </div>
                <div class="info-row qty-box">
                    <strong>Qty:</strong>
                    <button onclick="decreaseQty()">−</button>
                    <span id="qty"><?= (int) $payment_link_details["min_qty"] ?></span>
                    <button onclick="increaseQty()">+</button>
                </div>
                <div class="amount-due">
                    Amount Due: ₦<span id="totalAmount"><?= toDecimal($payment_link_details["amount"], 2) ?></span>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="payment-form">
                <h3>Make Payment</h3>
                <div class="form-group">
                    <input type="text" id="first_name" placeholder="First name *">
                    <div class="error-text">First name is required</div>
                </div>
                <div class="form-group">
                    <input type="text" id="last_name" placeholder="Last name *">
                    <div class="error-text">Last name is required</div>
                </div>
                <div class="form-group">
                    <input type="tel" id="phone" placeholder="Phone Number">
                </div>
                <div class="form-group">
                    <input type="email" id="email" placeholder="Email *">
                    <div class="error-text">Enter a valid email address</div>
                </div>
                <div class="form-group">
                    <select id="payment_method" onchange="updateGatewayKeys()">
                        <option value="">-- Select Payment Method --</option>
                        <?php
                        foreach ($payment_gateway_array as $gateway_name) {
                            $gateway_data = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_payment_link_gateways WHERE vendor_id='" . $get_vendor_details["id"] . "' AND gateway_name='$gateway_name'"));
                            if (!empty($gateway_data) && in_array($gateway_data["status"], [1, 2])) {
                                $disabled = $gateway_data["status"] == 2 ? "disabled" : "";
                                echo '<option value="' . $gateway_data["gateway_name"] . '" ' . $disabled
                                    . ' gateway-public="' . trim($gateway_data["public_key"]) . '"'
                                    . ' gateway-encrypt="' . trim($gateway_data["encrypt_key"]) . '"'
                                    . '>' . ucwords($gateway_data["gateway_name"]) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <div class="error-text">Please select a payment method</div>
                </div>

                <!-- Hidden inputs -->
                <input id="gateway-public" type="text" hidden readonly required />
                <input id="gateway-encrypt" type="text" hidden readonly required />
                <input id="num-ref" type="text" value="<? echo $reference; ?>" hidden readonly required />
                <input id="amount-to-pay" type="text" hidden readonly required
                    value="<?= toDecimal($payment_link_details["amount"], 2) ?>" />

                <div class="form-group">
                    <input type="text" id="formAmount" value="<?= toDecimal($payment_link_details["amount"], 2) ?>"
                        readonly>
                </div>

                <button class="pay-btn" onclick="proceedToPay()">Proceed to Pay</button>
            </div>
        </div>

        <script>
            let qty = <?= (int) $payment_link_details["min_qty"]; ?>;
            const unitPrice = <?= (float) $payment_link_details["amount"]; ?>;

            function updateAmount() {
                const total = qty * unitPrice;
                document.getElementById("qty").innerText = qty;
                document.getElementById("totalAmount").innerText = total.toFixed(2);
                document.getElementById("formAmount").value = total.toFixed(2);
                document.getElementById("amount-to-pay").value = total.toFixed(2);
            }

            function increaseQty() { qty++; updateAmount(); }
            function decreaseQty() { if (qty > 1) { qty--; updateAmount(); } }

            function updateGatewayKeys() {
                const select = document.getElementById("payment_method");
                const selectedOption = select.options[select.selectedIndex];
                document.getElementById("gateway-public").value = selectedOption.getAttribute("gateway-public") || "";
                document.getElementById("gateway-encrypt").value = selectedOption.getAttribute("gateway-encrypt") || "";
            }

            function showError(input, message) { input.classList.add("error"); const e = input.nextElementSibling; if (e) { e.innerText = message; e.style.display = "block"; } }
            function clearError(input) { input.classList.remove("error"); const e = input.nextElementSibling; if (e) { e.style.display = "none"; } }

            function proceedToPay() {
                let valid = true;
                const firstName = document.getElementById("first_name");
                const lastName = document.getElementById("last_name");
                const email = document.getElementById("email");
                const method = document.getElementById("payment_method");

                if (!firstName.value.trim()) { showError(firstName, "First name is required"); valid = false; } else clearError(firstName);
                if (!lastName.value.trim()) { showError(lastName, "Last name is required"); valid = false; } else clearError(lastName);
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email.value.trim())) { showError(email, "Enter a valid email address"); valid = false; } else clearError(email);
                if (!method.value) { showError(method, "Select payment method"); valid = false; } else clearError(method);

                if (!valid) return;

                // Proceed to gateway
                alert("Proceeding with " + method.value.toUpperCase() + " payment of ₦" + document.getElementById("formAmount").value);
                proceedPaymentGateway(method.value.toLowerCase());
                // TODO: Call gateway functions here
            }

            updateAmount(); // initialize amount


            function proceedPaymentGateway(gatewayName) {
                if (gatewayName == "monnify") payWithMonnify();
                else if (gatewayName == "flutterwave") makePaymentFlutterwave();
                else if (gatewayName == "paystack") makePaymentPaystack();
                else if (gatewayName == "beewave") makePaymentBeewave();
            }

            // Helper to get form values
            function getCustomerDetails() {
                return {
                    name: document.getElementById("first_name").value.trim() + " " + document.getElementById("last_name").value.trim(),
                    email: document.getElementById("email").value.trim(),
                    phone: document.getElementById("phone").value.trim(),
                    amount: document.getElementById("amount-to-pay").value.trim()
                };
            }

            // MONNIFY CHECKOUT
            function payWithMonnify() {
                const customer = getCustomerDetails();
                setTimeout(() => {
                    MonnifySDK.initialize({
                        amount: customer.amount,
                        currency: "NGN",
                        reference: document.getElementById("num-ref").value,
                        customerFullName: customer.name,
                        customerEmail: customer.email,
                        apiKey: document.getElementById("gateway-public").value,
                        contractCode: document.getElementById("gateway-encrypt").value,
                        paymentDescription: "Product Payment",
                        metadata: {},
                        incomeSplitConfig: [],
                        onLoadStart: () => console.log("Monnify loading started"),
                        onLoadComplete: () => console.log("Monnify SDK ready"),
                        onComplete: function (response) { window.location.href = "/web/Dashboard.php"; },
                        onClose: function (data) { }
                    });
                }, 0);
            }

            // FLUTTERWAVE CHECKOUT
            function makePaymentFlutterwave() {
                const customer = getCustomerDetails();
                setTimeout(() => {
                    FlutterwaveCheckout({
                        public_key: document.getElementById("gateway-public").value,
                        tx_ref: document.getElementById("num-ref").value,
                        amount: customer.amount,
                        currency: "NGN",
                        payment_options: "card, banktransfer, ussd",
                        redirect_url: "",
                        customer: {
                            email: customer.email,
                            phone_number: customer.phone,
                            name: customer.name
                        },
                        callback: function (payment) { window.location.href = "/web/Dashboard.php"; }
                    });
                }, 0);
            }

            // PAYSTACK CHECKOUT
            function makePaymentPaystack() {
                const customer = getCustomerDetails();
                setTimeout(() => {
                    let handler = PaystackPop.setup({
                        key: document.getElementById("gateway-public").value,
                        email: customer.email,
                        amount: customer.amount * 100,
                        currency: 'NGN',
                        ref: document.getElementById("num-ref").value,
                        callback: function (response) { window.location.href = "/web/Dashboard.php"; },
                        onClose: function () { }
                    });
                    handler.openIframe();
                }, 0);
            }

            // BEEWAVE CHECKOUT
            function makePaymentBeewave() {
                const customer = getCustomerDetails();
                setTimeout(() => {
                    BeefinanceCheckout.open({
                        accessKey: document.getElementById("gateway-public").value,
                        name: customer.name,
                        email: customer.email,
                        phone: customer.phone,
                        amount: customer.amount
                    });
                }, 0);
            }
        </script>


    <?php } else { ?>
        <div class="payment-error">
            <h2>Payment Link Not Found</h2>
            <p>The payment link you are trying to access is invalid, expired, or does not exist.</p>
        </div>
    <?php } ?>

</body>

</html>