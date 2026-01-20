<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    include("../func/bc-admin-config.php");

    $vendor_id = $get_logged_admin_details["id"];

    // --- Handle Pay with Wallet ---
    if (isset($_POST['pay_with_wallet']) && isset($_POST['package_id'])) {
        $package_id = (int)$_POST['package_id'];

        $stmt = mysqli_prepare($connection_server, "SELECT * FROM sas_billing_packages WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $package_id);
        mysqli_stmt_execute($stmt);
        $pkg_result = mysqli_stmt_get_result($stmt);

        if ($pkg_result && mysqli_num_rows($pkg_result) > 0) {
            $package = mysqli_fetch_assoc($pkg_result);
            $price = $package['price'];
            $duration = $package['duration_days'];

            if ($get_logged_admin_details['balance'] >= $price) {
                $reference = 'SUB-WALLET-' . time() . '-' . $vendor_id;
                $description = "Subscription to " . htmlspecialchars($package['name']);
                $debit_result = chargeVendor("debit", "subscription", "Subscription", $reference, $price, $price, $description, $_SERVER['HTTP_HOST'], 1);

                if ($debit_result === "success") {
                    $current_expiry = $get_logged_admin_details['expiry_date'];
                    $today = date('Y-m-d');

                    $new_expiry_date = ($current_expiry && $current_expiry != '0000-00-00' && strtotime($current_expiry) > strtotime($today))
                        ? date('Y-m-d', strtotime($current_expiry . " + " . $duration . " days"))
                        : date('Y-m-d', strtotime($today . " + " . $duration . " days"));

                    $stmt_update = mysqli_prepare($connection_server, "UPDATE sas_vendors SET expiry_date=?, status=1 WHERE id=?");
                    mysqli_stmt_bind_param($stmt_update, "si", $new_expiry_date, $vendor_id);

                    if (mysqli_stmt_execute($stmt_update)) {
                        $purchase_date = date('Y-m-d H:i:s');
                        $stmt_log = mysqli_prepare($connection_server, "INSERT INTO sas_vendor_subscriptions (vendor_id, package_id, purchase_date, expiry_date, amount_paid) VALUES (?, ?, ?, ?, ?)");
                        mysqli_stmt_bind_param($stmt_log, "iissd", $vendor_id, $package_id, $purchase_date, $new_expiry_date, $price);
                        mysqli_stmt_execute($stmt_log);

                        $_SESSION["product_purchase_response"] = "Subscription successful! Your new expiry date is " . date('F j, Y', strtotime($new_expiry_date));
                    } else {
                        $db_error = mysqli_stmt_error($stmt_update);
                        $_SESSION["product_purchase_response"] = "Error: Could not update your subscription expiry date. Please contact support. (Debug info: " . $db_error . ")";
                    }
                } else {
                    $_SESSION["product_purchase_response"] = "An error occurred while charging your wallet. Please try again.";
                }
            } else {
                $_SESSION["product_purchase_response"] = "Insufficient funds. Please fund your wallet and try again.";
            }
        } else {
            $_SESSION["product_purchase_response"] = "Invalid subscription package selected.";
        }
        header("Location: RenewSubscription.php");
        exit();
    }

    // Handle form submission for Paystack
    if(isset($_POST['pay_with_paystack'])) {
        $billing_package_id = mysqli_real_escape_string($connection_server, $_POST['package_id']);

        // Fetch package price
        $package_res = mysqli_query($connection_server, "SELECT price FROM sas_billing_packages WHERE id='$billing_package_id'");
        if($package_res && mysqli_num_rows($package_res) > 0) {
            $package = mysqli_fetch_assoc($package_res);
            $amount_in_kobo = $package['price'] * 100;

            // Fetch Paystack secret key
            $gateway_res = mysqli_query($connection_server, "SELECT secret_key FROM sas_super_admin_payment_gateways WHERE gateway_name='paystack'");
            $gateway = mysqli_fetch_assoc($gateway_res);
            $secret_key = $gateway['secret_key'] ?? '';

            if ($secret_key) {
                $callback_url = $web_http_host . '/web/paystack_callback.php';
                $reference = 'vendor_renewal_' . $vendor_id . '_' . time();

                $post_data = [
                    'email' => $get_logged_admin_details['email'],
                    'amount' => $amount_in_kobo,
                    'callback_url' => $callback_url,
                    'reference' => $reference,
                    'metadata' => [
                        'vendor_id' => $vendor_id,
                        'billing_package_id' => $billing_package_id,
                        'type' => 'vendor_renewal'
                    ]
                ];

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($post_data),
                    CURLOPT_HTTPHEADER => [
                        "Authorization: Bearer " . $secret_key,
                        "Content-Type: application/json"
                    ],
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);

                if ($err) {
                    $_SESSION["product_purchase_response"] = "Payment gateway error. Please try again or contact support.";
                } else {
                    $result = json_decode($response, true);
                    if ($result['status'] == true) {
                        header('Location: ' . $result['data']['authorization_url']);
                        exit();
                    } else {
                        $_SESSION["product_purchase_response"] = "Could not initialize payment: " . $result['message'];
                    }
                }
            } else {
                 $_SESSION["product_purchase_response"] = "Paystack payment gateway is not configured. Please contact support.";
            }
        } else {
            $_SESSION["product_purchase_response"] = "Invalid billing package selected.";
        }
        header("Location: RenewSubscription.php");
        exit();
    }

    // Fetch bank details for manual payment instructions
    $bank_details_res = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_payments LIMIT 1");
    $bank_details = mysqli_fetch_assoc($bank_details_res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Renew Subscription | <?php echo $get_all_super_admin_site_details["site_title"] ?? ''; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets-2/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include("../func/bc-admin-header.php"); ?>

    <div class="pagetitle">
        <h1>Renew Subscription</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="Dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Renew Subscription</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Choose a Package</h5>
                        <?php if(isset($_SESSION["product_purchase_response"])): ?>
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION["product_purchase_response"]; unset($_SESSION["product_purchase_response"]); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                        <?php
                            $packages_result = mysqli_query($connection_server, "SELECT * FROM sas_billing_packages ORDER BY price ASC");
                            if (!$packages_result) {
                                echo '<div class="col-12"><div class="alert alert-danger">Query failed: ' . htmlspecialchars(mysqli_error($connection_server)) . '</div></div>';
                            }
                            if ($packages_result && mysqli_num_rows($packages_result) > 0) {
                                while($package = mysqli_fetch_assoc($packages_result)):
                        ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title text-center border-bottom pb-3"><?php echo htmlspecialchars($package['name']); ?></h5>
                                        <div class="text-center my-4">
                                            <span class="fs-2 fw-bold">₦<?php echo number_format($package['price'], 2); ?></span>
                                            <span class="text-muted">/ <?php echo htmlspecialchars($package['duration_days']); ?> days</span>
                                        </div>
                                        <div class="mt-auto">
                                            <form method="POST" action="RenewSubscription.php" class="d-grid gap-2">
                                                <input type="hidden" name="package_id" value="<?php echo $package['id']; ?>">
                                                <button type="submit" name="pay_with_wallet" class="btn btn-success">Pay with Wallet (Balance: ₦<?php echo number_format($get_logged_admin_details['balance'], 2); ?>)</button>
                                                <button type="submit" name="pay_with_paystack" class="btn btn-primary">Pay with Paystack</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                                endwhile;
                            } else {
                                echo '<div class="col-12"><div class="alert alert-warning">No subscription packages are available at the moment. Please check back later.</div></div>';
                            }
                        ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                 <div class="card">
                    <div class="card-body">
                         <h5 class="card-title">Manual Payment</h5>
                         <?php if($bank_details): ?>
                            <div class="alert alert-info">
                                <h4 class="alert-heading">Bank Transfer Instructions:</h4>
                                <p><strong>Bank Name:</strong> <?php echo htmlspecialchars($bank_details['bank_name']); ?></p>
                                <p><strong>Account Name:</strong> <?php echo htmlspecialchars($bank_details['account_name']); ?></p>
                                <p><strong>Account Number:</strong> <?php echo htmlspecialchars($bank_details['account_number']); ?></p>
                                <hr>
                                <p>After payment, please contact support with your email address and proof of payment to have your subscription renewed manually.</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <p>Manual payment details are not available at the moment. Please contact support.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include("../func/bc-admin-footer.php"); ?>
</body>
</html>
