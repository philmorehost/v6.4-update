<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
if (!isset($_SESSION["user_session"])) {
    header("Location: /web/Login.php");
}
include_once("../func/bc-config.php");

$crypto_arrays = array("ngn" => "Nigerian Naira", "usd" => "United State Dollar", "gbp" => "British Pounds", "cad" => "Canadian Dollar", "eur" => "Euro", "btc" => "Bitcoin", "eth" => "Ethereum", "doge" => "Dogecoin", "usdt" => "USDT", "usdc" => "USDC", "sol" => "Solana", "ada" => "Cardano", "trx" => "Tron");
$crypto_payment_link_arrays = array("ngn" => "Nigerian Naira");

foreach ($crypto_arrays as $each_crypto => $each_crypto_name) {
    $crypto_ledger_query = mysqli_query($connection_server, "SELECT * FROM sas_user_crypto_ledger_balance WHERE vendor_id = '" . $get_logged_user_details['vendor_id'] . "' AND username = '" . $get_logged_user_details['username'] . "' AND currency = '" . $each_crypto . "'");
    if (mysqli_num_rows($crypto_ledger_query) == 0) {
        $crypto_wallet_id = str_shuffle(substr("abcdefghijklmnopqrstuvwxyz1234567890", 0, 15));

        mysqli_query($connection_server, "INSERT INTO sas_user_crypto_ledger_balance (vendor_id, username, currency, wallet_balance, `status`) VALUES ('" . $get_logged_user_details['vendor_id'] . "', '" . $get_logged_user_details['username'] . "', '" . $each_crypto . "', '0', '3')");
    }
}

foreach (array("usdt", "usdc") as $each_currency) {
    $purchase_method = "web";
    $action_function = 1;
    $currency = $each_currency;
    $customer_ref = "";
    $select_customer_holder = mysqli_query($connection_server, "SELECT * FROM sas_crypto_customer_holders WHERE vendor_id = '" . $get_logged_user_details["vendor_id"] . "' && username = '" . $get_logged_user_details["username"] . "' LIMIT 1");
    if (mysqli_num_rows($select_customer_holder) == 1) {
        $get_customer_holder_detail = mysqli_fetch_array($select_customer_holder);
    }
    $json_response_encode = "";
    include("func/crypto.php");
    $json_response_decode = json_decode($json_response_encode ?? "", true);
    // $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
}

if (isset($_POST["buy-exam"])) {
    $purchase_method = "web";
    include_once("func/exam.php");
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>International Money Transfer | <?php echo $get_all_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="<?php echo $get_all_site_details["primary_color"] ?? "#198754"; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">

    <link rel="stylesheet" href="/cssfile/bc-style.css">

    <!-- Vendor CSS Files -->
    <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="../assets-2/css/style.css" rel="stylesheet">
    <link href="../cssfile/intl-money-transfer-2.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        @media (max-width: 767.98px) {
            .main {
                padding-top: 0;
            }

            .main-content {
                padding-top: 0;
            }

            .main-content-container {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                overflow-y: auto;
                background-color: #f7f7f7;
                z-index: 1000;
                padding-top: 0;
                height: 100vh;
            }

            .pagetitle,
            .header,
            .sidebar {
                display: none !important;
            }
        }

        .currency-carousel-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .crypto-currency-btn {
            display: inline-block !important;
            width: auto;
            padding: 8px 15px;
            border: 1px solid #eee;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .hidden-mobile {
            display: none;
        }

        @media (min-width: 768px) {
            .hidden-desktop {
                display: none;
            }

            .hidden-mobile {
                display: inline-block !important;
            }
        }

        @media (min-width: 768px) {
            .main-content-container {
                min-width: fit-content;
            }
        }
    </style>

</head>

<body>
    <?php include("../func/bc-header.php"); ?>

    <section class="section dashboard">
        <div class="main-content-container">

            <div class="main-content">

                <div class="intl-header">
                    <div class="d-flex align-items-center">
                        <a href="Dashboard.php" class="text-dark me-2">
                            <i class="bi bi-arrow-left fs-2"></i>
                        </a>
                        <div class="greeting">
                            <span class="text-dark fw-bold fs-4">Hello,
                                <?php echo ucwords($get_logged_user_details['username']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="referral-link">
                        <a href="#" id="copy-referral-link"
                            data-link="<?php echo $web_http_host; ?>/register.php?ref=<?php echo $get_logged_user_details['username']; ?>">Copy
                            referral link</a>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row bg-light px-0 py-0 mb-3">
                    <!-- Large Column -->
                    <div class="flex-grow-1 bg-white text-white p-2 mb-3 mb-md-0 me-md-3 rounded-4">
                        <div class="col bg-white">
                            <div class="currency-carousel-container">
                                <div class="currency-carousel">
                                    <?php
                                    $crypto_keys = array_keys($crypto_arrays);
                                    foreach ($crypto_keys as $index => $each_crypto_key) {
                                        $mobile_class = ($index >= 2) ? 'hidden-mobile' : '';
                                        $desktop_class = ($index >= 7) ? 'hidden-desktop' : '';
                                        echo
                                            '<div id="' . strtolower($each_crypto_key) . '-btn" class="crypto-currency-btn bg-white text-dark text-center fw-bolder d-inline-block px-3 py-2 rounded-5 mx-1 ' . $mobile_class . ' ' . $desktop_class . '" style="text-align: center; cursor: pointer; font-size: 10px;" onclick="selectCrypto(`' . strtolower($each_crypto_key) . '`);">
                                            <img src="../asset/' . strtolower($each_crypto_key) . '.jpg" class="rounded-circle mx-1" style="width: 15px; height: 15px;" />' . strtoupper($each_crypto_key) . '
                                            </div>';
                                    }
                                    if (count($crypto_keys) > 2) {
                                        echo '<button id="more-mobile-btn" class="btn btn-sm btn-outline-secondary d-md-none" onclick="toggleCurrencies(\'mobile\')">More</button>';
                                    }
                                    if (count($crypto_keys) > 7) {
                                        echo '<button id="more-desktop-btn" class="btn btn-sm btn-outline-secondary d-none d-md-inline-block" onclick="toggleCurrencies(\'desktop\')">More</button>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="d-flex flex-row bg-dark mt-3 px-3 py-3 rounded-4 text-white"
                                style="background-image: url('../asset/balance-card-bg.jpg'); background-size: cover; background-position: top; color: white; padding: 2rem; border-radius: 20px; margin-bottom: 1.5rem; position: relative;">
                                <div class="col-8">
                                    <span class="fs-6">Available balance</span><br />
                                    <div class="amount fs-3 my-2" id="available-balance">NGN 0.00</div>
                                    <div class="fw-normal fs-6">Ledger balance: <span class="ledger-balance"
                                            id="ledger-balance">NGN 0.00</span></div>
                                </div>
                                <div class="col-4" style="text-align: right;">
                                    <a href="#" class="btn btn-dark text-white mt-3">Account Details</a>
                                </div>
                            </div>

                            <div class="d-flex flex-row flex-wrap bg-white px-0 py-0 gap-3">

                                <div class="flex-fill bg-light px-4 py-3 rounded-4 text-center" style="cursor:pointer;"
                                    onclick="toggleActionPanel('deposit-div');">
                                    <div class="d-inline-block rounded-circle px-3 py-2 text-dark"
                                        style="background-color:#d0f2eeff;">↓</div>
                                    <div class="text-dark my-2 fw-bold fs-6">Deposit</div>
                                </div>

                                <div class="flex-fill bg-light px-4 py-3 rounded-4 text-center" style="cursor:pointer;"
                                    onclick="toggleActionPanel('transfer-div');">
                                    <div class="d-inline-block rounded-circle px-3 py-2 text-dark"
                                        style="background-color:#d0f2eeff;">↗</div>
                                    <div class="text-dark my-2 fw-bold fs-6">Transfer</div>
                                </div>

                                <div class="flex-fill bg-light px-4 py-3 rounded-4 text-center" style="cursor:pointer;"
                                    onclick="toggleActionPanel('swap-div');">
                                    <div class="d-inline-block rounded-circle px-3 py-2 text-dark"
                                        style="background-color:#d0f2eeff;">⇆</div>
                                    <div class="text-dark my-2 fw-bold fs-6">Swap</div>
                                </div>

                                <div class="flex-fill bg-light px-4 py-3 rounded-4 text-center" style="cursor:pointer;"
                                    onclick="toggleActionPanel('request-div');">
                                    <div class="d-inline-block rounded-circle px-3 py-2 text-dark"
                                        style="background-color: #d0f2eeff;">↓</div>
                                    <div class="text-dark my-2 fw-bold fs-6">Payments</div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- Small Column -->
                    <div class="flex-shrink-0 bg-white text-dark px-4 py-3 rounded-4" style="width: 350px;">

                        <div id="deposit-div" class="action-panel">
                            <span class="fw-bold fs-4">Deposit via Crypto</span>

                            <p class="mt-3">Deposit via USDT or USDC</p>
                            <div class="input-group border rounded-3 overflow-hidden my-1">
                                <select class="form-select border-0 shadow-none" id="crypto-deposit-currency" required>
                                    <?php
                                    foreach (array_keys($crypto_arrays) as $index => $each_crypto_key) {
                                        if (in_array($each_crypto_key, array("usdt", "usdc"))) {
                                            echo '<option value="' . strtolower($each_crypto_key) . '">' . strtoupper($each_crypto_key) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="input-group border rounded-3 overflow-hidden my-1">
                                <!-- Deposit amount input (left, borderless) -->
                                <input type="number" class="form-control border-0 shadow-none"
                                    id="crypto-deposit-amount" name="" placeholder="Amount to deposit" required>
                            </div>

                            <div id="crypto-deposit-address-div" class="bg-light rounded px-2 py-2"
                                style="display: none; margin: 10px 0 0 0;">
                                <p><span id="crypto-deposit-instruction-span"></span></p>
                                <p><strong>Address:</strong> <span id="crypto-deposit-address-span"></span></p>
                                <p><strong>Network/Chain:</strong> <span id="crypto-deposit-chain-span"></span>
                                </p>
                            </div>

                            <button onclick="fetchWWalletAddress();" class="btn btn-primary my-2 w-100">Fetch Deposit
                                Address</button>

                        </div>

                        <div id="transfer-div" class="action-panel" style="display: none;">
                            <div class="transfers-section">
                                <div class="tabs">
                                    <div class="tab active" onclick="switchTransferTab(event, 'transfer')">Transfer
                                    </div>
                                    <div class="tab" onclick="switchTransferTab(event, 'beneficiary')">Beneficiary</div>
                                </div>

                                <div id="transfer" class="transfer-tab-content" style="display:block;">
                                    <!-- Transfer content -->

                                    <p>Transfer crypto from USDT or USDC balance</p>

                                    <div class="input-group border rounded-3 overflow-hidden my-1">
                                        <select class="form-select border-0 shadow-none"
                                            id="crypto-transfer-beneficiary"
                                            onchange="setCryptoBeneficiaryInfo('crypto-transfer-beneficiary'); getTransferFee();"
                                            required>
                                            <option value="" default hidden>Choose Beneficiary</option>
                                        </select>
                                    </div>

                                    <div class="input-group border rounded-3 overflow-hidden my-1">
                                        <!-- Beneficiary input (left, borderless) -->
                                        <input type="text" class="form-control border-0 shadow-none"
                                            id="crypto-transfer-beneficiary-address" name=""
                                            placeholder="Crypto Address" required>
                                    </div>

                                    <div class="input-group border rounded-3 overflow-hidden my-1">
                                        <!-- Currency preview -->
                                        <span class="input-group-text bg-white border-0">
                                            <img id="crypto-transfer-beneficiary-currency-img" src="../asset/ngn.jpg"
                                                style="width:20px; height:20px;" class="rounded-circle">
                                        </span>

                                        <!-- Currency input (left, borderless) -->
                                        <input type="text" class="form-control border-0 shadow-none"
                                            id="crypto-transfer-beneficiary-currency" name="" placeholder="Currency"
                                            hidden required>

                                        <!-- Amount input (left, borderless) -->
                                        <input type="number" class="form-control border-0 shadow-none"
                                            id="crypto-transfer-beneficiary-amount" name="amount"
                                            placeholder="Amount to transfer" onkeyup="getTransferFee();" required>

                                        <!-- Chain input (left, borderless) -->
                                        <input type="text" class="form-control border-0 shadow-none"
                                            id="crypto-transfer-beneficiary-chain" name="" placeholder="Chain" required>

                                    </div>

                                    <span id="stamp-duty-span-details"></span>

                                    <button onclick="initiateCryptoTransfer();" id="initiate-crypto-transfer"
                                        class="btn btn-primary my-2 w-100">Iniitiate
                                        Transfer</button>

                                </div>

                                <div id="beneficiary" class="transfer-tab-content" style="display:none;">
                                    <!-- Beneficiary content -->

                                    <p>Create Crypto Beneficiary to send USDT or USDC</p>


                                    <label>Select Currency and Chain</label>
                                    <div class="input-group border rounded-3 overflow-hidden my-1">
                                        <!-- Currency preview -->
                                        <span class="input-group-text bg-white border-0">
                                            <img id="crypto-beneficiary-currency-img" src="../asset/ngn.jpg"
                                                style="width:20px; height:20px;" class="rounded-circle">
                                        </span>

                                        <!-- Currency input (left, borderless) -->
                                        <select class="form-select border-0 shadow-none"
                                            onchange="getCurrencyChain(this.value, 'crypto-beneficiary-chain');"
                                            id="crypto-beneficiary-currency" required>
                                            <option value="" default hidden>Choose Currency</option>
                                            <?php
                                            foreach (array_keys($crypto_arrays) as $index => $each_crypto_key) {
                                                if (in_array($each_crypto_key, array("usdt", "usdc"))) {
                                                    echo '<option value="' . strtolower($each_crypto_key) . '">' . strtoupper($each_crypto_key) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>


                                        <!-- Chain input (left, borderless) -->
                                        <select class="form-select border-0 shadow-none" id="crypto-beneficiary-chain"
                                            required>
                                            <option value="" default hidden>Choose Chain</option>
                                        </select>
                                    </div>

                                    <div class="input-group border rounded-3 overflow-hidden my-1">
                                        <!-- Beneficiary Label input (left, borderless) -->
                                        <input type="text" class="form-control border-0 shadow-none"
                                            id="crypto-beneficiary-label" name="" placeholder="Unique Label" required>
                                    </div>

                                    <div class="input-group border rounded-3 overflow-hidden my-1">
                                        <!-- Beneficiary Address input (left, borderless) -->
                                        <input type="text" class="form-control border-0 shadow-none"
                                            id="crypto-beneficiary-address" name=""
                                            placeholder="Wallet address eg Crypto address" required>
                                    </div>

                                    <button onclick="saveBeneficiary();" class="btn btn-primary my-2 w-100">Save
                                        Beneficiary</button>

                                </div>
                            </div>
                        </div>

                        <div id="swap-div" class="action-panel" style="display: none;">
                            <span class="fw-bold fs-4">Swap Currency</span>
                            <div class="form-group my-2">
                                <label for="convert-source-currency">Source currency</label>

                                <div class="input-group border rounded-3 overflow-hidden">
                                    <!-- Currency preview -->
                                    <span class="input-group-text bg-white border-0">
                                        <img id="source-currency-img" src="../asset/ngn.jpg"
                                            style="width:20px; height:20px;" class="rounded-circle">
                                    </span>

                                    <!-- Amount input (left, borderless) -->
                                    <input type="number" class="form-control border-0 shadow-none"
                                        id="amount-to-convert" name="amount" placeholder="0.00"
                                        onkeyup="javascript: document.getElementById('expected-converted-amount').value = '0'; currencyConvertRates('currency-change')"
                                        required>

                                    <!-- Currency select (right) -->
                                    <select class="form-select border-0 shadow-none" id="convert-source-currency"
                                        onchange="updateSourceCurrencyPreview(this); currencyConvertRates('currency-change')"
                                        required>
                                        <?php
                                        foreach (array_keys($crypto_arrays) as $each_crypto_key) {
                                            echo '<option value="' . strtolower($each_crypto_key) . '">' . strtoupper($each_crypto_key) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group my-2">
                                <label for="convert-target-currency">Target currency</label>

                                <div class="input-group w-100 border rounded-3 overflow-hidden">

                                    <!-- Currency image -->
                                    <span class="input-group-text bg-white border-0 flex-shrink-0">
                                        <img id="target-currency-img" src="../asset/ngn.jpg" class="rounded-circle"
                                            style="width:20px; height:20px;">
                                    </span>

                                    <!-- Amount input (left, borderless) -->
                                    <input type="number" class="form-control border-0 shadow-none"
                                        id="expected-converted-amount" name="amount" placeholder="Expected amount"
                                        readonly required>

                                    <!-- Select (fills remaining width) -->
                                    <select class="form-select border-0 shadow-none flex-grow-1"
                                        id="convert-target-currency"
                                        onchange="updateTargetCurrencyPreview(this); currencyConvertRates('currency-change')"
                                        required>
                                        <?php
                                        foreach (array_keys($crypto_arrays) as $each_crypto_key) {
                                            echo '<option value="' . strtolower($each_crypto_key) . '">' . strtoupper($each_crypto_key) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <span id="conversion-span-details"></span>

                            <button id="currency-convert-btn" onclick="currencyConvertRates('convert-currency')"
                                type="button" class="btn btn-primary my-2 w-100">
                                Convert
                            </button>

                            <input type="text" class="form-control" id="swap-id" name="" hidden>
                            <button id="currency-swap-btn" onclick="currencySwap()" type="button"
                                class="btn btn-primary my-2 w-100" style="display: none;">Initiate Swap</button>
                        </div>

                        <div id="request-div" class="action-panel" style="display: none;">

                            <span class="fw-bold fs-4">Swap Currency</span>
                            <p class="mt-3">Request payment from customers</p>
                            <div class="input-group border rounded-3 overflow-hidden my-1">
                                <select class="form-select border-0 shadow-none" id="payment-currency" required>
                                    <?php
                                    foreach (array_keys($crypto_payment_link_arrays) as $index => $each_crypto_key) {
                                        echo '<option value="' . strtolower($each_crypto_key) . '">' . strtoupper($each_crypto_key) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="input-group border rounded-3 overflow-hidden my-1">
                                <!-- Request amount input (left, borderless) -->
                                <input type="number" class="form-control border-0 shadow-none"
                                    id="payment-amount" name="" placeholder="Amount to request" required>
                            </div>

                            <div class="input-group border rounded-3 overflow-hidden my-1">
                                <!-- Request description input (left, borderless) -->
                                <input type="text" class="form-control border-0 shadow-none"
                                    id="payment-description" name="" placeholder="Description" required>
                            </div>

                            <button onclick="createPaymentLink();" type="button" class="btn btn-primary my-2 w-100">Create
                                Link</button>

                            <div id="payment-link-result" style="display:none;">
                                
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Section -->
                <div class="transactions-section">
                    <div class="tabs">
                        <div class="tab active" onclick="openTransactionTab(event, 'balances')">Balances</div>
                        <div class="tab" onclick="openTransactionTab(event, 'transactions')">Transactions</div>
                    </div>

                    <div id="balances" class="transaction-tab-content" style="display: block;">
                        <div class="transaction-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>SYMBOL</th>
                                        <th>CURRENCY</th>
                                        <th>AVL. BAL</th>
                                        <th>LDG. BAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($crypto_arrays as $each_crypto => $each_crypto_name) {
                                        $crypto_ledger_query = mysqli_query($connection_server, "SELECT * FROM sas_user_crypto_ledger_balance WHERE vendor_id = '" . $get_logged_user_details['vendor_id'] . "' AND username = '" . $get_logged_user_details['username'] . "' AND currency = '" . $each_crypto . "'");
                                        if (mysqli_num_rows($crypto_ledger_query) == 1) {
                                            $get_crypto_details = mysqli_fetch_array($crypto_ledger_query);
                                            $wallet_balance = toDecimal($get_crypto_details["wallet_balance"], 2);
                                            $ledger_balance = toDecimal($get_crypto_details["wallet_balance"], 2);
                                            echo
                                                '
                                                <tr>
                                                    <td>' . strtoupper($each_crypto) . '</td>
                                                    <td>' . $each_crypto_name . '</td>
                                                    <td>' . $wallet_balance . '</td>
                                                    <td>' . $ledger_balance . '</td>
                                                </tr>
                                                ';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="transactions" class="transaction-tab-content">
                        <div class="transaction-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>REFERENCE</th>
                                        <th>SELL</th>
                                        <th>BUY</th>
                                        <th>AMT</th>
                                        <th>AMT CHARGED</th>
                                        <th>DESCRIPTION</th>
                                        <th>STATUS</th>
                                        <th>DATE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $status_array = array(1 => "successful", 2 => "pending", 3 => "failed");
                                    // In a real application, you would also join with the users table to get the username
                                    $transactions_query = mysqli_query($connection_server, "SELECT * FROM sas_user_crypto_wallet_transactions WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' AND username='" . $get_logged_user_details["username"] . "' ORDER BY date DESC LIMIT 20");

                                    if (mysqli_num_rows($transactions_query) > 0) {
                                        while ($crypto_trans_details = mysqli_fetch_assoc($transactions_query)) {
                                            echo '<tr>
                                                <td>' . $crypto_trans_details["reference"] . '</td>
                                                <td>' . strtoupper($crypto_trans_details["from_currency"]) . '</td>
                                                <td>' . strtoupper($crypto_trans_details["to_currency"]) . '</td>
                                                <td>' . toDecimal($crypto_trans_details["amount"], 2) . '</td>
                                                <td>' . toDecimal($crypto_trans_details["discounted_amount"], 2) . '</td>
                                                <td>' . $crypto_trans_details["description"] . '</td>
                                                <td>' . $status_array[$crypto_trans_details["status"]] . '<span
                                                        class="status-badge status-success"></span>
                                                </td>
                                                <td>' . formDateWithoutTime($crypto_trans_details["date"]) . '</td>
                                            </tr>';
                                        }
                                    } else {
                                        echo '<tr>
                                            <td colspan="5" style="text-align: center;">No transactions found.</td>
                                        </tr>';
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Payout DIV Space -->
            <script src="../js/intl-money-transfer.js"></script>
    </section>
    <?php include("../func/bc-footer.php"); ?>
    <script src="../jsfile/blockchain.js"></script>
    <script>
        function toggleCurrencies(view) {
            const hiddenClass = view === 'mobile' ? '.hidden-mobile' : '.hidden-desktop';
            const buttonId = view === 'mobile' ? '#more-mobile-btn' : '#more-desktop-btn';
            const currencies = document.querySelectorAll(hiddenClass);
            const button = document.querySelector(buttonId);

            currencies.forEach(currency => {
                currency.style.display = currency.style.display === 'none' || currency.style.display === '' ? 'inline-block' : 'none';
            });

            button.textContent = button.textContent === 'More' ? 'Less' : 'More';
        }
    </script>
</body>

</html>