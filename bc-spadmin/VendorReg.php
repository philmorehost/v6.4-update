<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    // Use basic configs to avoid login enforcement
    include("../func/bc-connect.php");
	include("../func/bc-func.php");
	include("../func/bc-tables.php");

    // Ensure table exists
    $check_table = mysqli_query($connection_server, "SHOW TABLES LIKE 'sas_super_admin_options'");
    if(mysqli_num_rows($check_table) == 0) {
        $create_table = "CREATE TABLE sas_super_admin_options (
          option_name VARCHAR(255) PRIMARY KEY,
          option_value TEXT
        )";
        mysqli_query($connection_server, $create_table);
    }

    // Fetch domain settings
    $nameservers = '';
    $ip_address = '';
    $registrar_url = '';
    $sql_fetch = "SELECT * FROM sas_super_admin_options WHERE option_name IN ('domain_nameservers', 'domain_ip_address', 'domain_registrar_url')";
    $result = mysqli_query($connection_server, $sql_fetch);
    if ($result) {
        while($row = mysqli_fetch_assoc($result)) {
            if($row['option_name'] == 'domain_nameservers') {
                $nameservers = $row['option_value'];
            }
            if($row['option_name'] == 'domain_ip_address') {
                $ip_address = $row['option_value'];
            }
            if($row['option_name'] == 'domain_registrar_url') {
                $registrar_url = $row['option_value'];
            }
        }
    }

    if(isset($_POST["create-profile"])){
        $first = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["first"]))));
        $last = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["last"]))));
        $address = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["address"]))));
        $email = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["email"]))));
        $pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["pass"])));
        $phone = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["phone"]))));
        $billing_package_id = mysqli_real_escape_string($connection_server, $_POST['billing_package_id']);
        $payment_method = mysqli_real_escape_string($connection_server, $_POST['payment_method']);
        
        $unrefined_website_url = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["website-url"]))));
        $refined_website_url = trim(str_replace(["https","http",":/","/","www."," "],"",$unrefined_website_url));
        $website_url = $refined_website_url;
        
        if(!empty($first) && !empty($last) && !empty($address) && !empty($email) && !empty($pass) && !empty($phone) && !empty($website_url) && !empty($billing_package_id) && !empty($payment_method)){
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $check_vendor_details_with_email = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE email='$email'");
                $check_pending_vendor_details_with_email = mysqli_query($connection_server, "SELECT * FROM sas_pending_vendors WHERE email='$email'");
                if(mysqli_num_rows($check_vendor_details_with_email) == 0 && mysqli_num_rows($check_pending_vendor_details_with_email) == 0){
                    $check_vendor_details_with_url = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='$website_url'");
                    $check_pending_vendor_details_with_url = mysqli_query($connection_server, "SELECT * FROM sas_pending_vendors WHERE website_url='$website_url'");
                    if(mysqli_num_rows($check_vendor_details_with_url) == 0 && mysqli_num_rows($check_pending_vendor_details_with_url) == 0){
                        $md5_pass = md5($pass);
                        
                        $sql = "INSERT INTO sas_pending_vendors (website_url, email, password, firstname, lastname, phone_number, home_address, billing_package_id, payment_method, status) VALUES ('$website_url', '$email', '$md5_pass', '$first', '$last', '$phone', '$address', '$billing_package_id', '$payment_method', '0')";
                        if(mysqli_query($connection_server, $sql)) {
                            $pending_id = mysqli_insert_id($connection_server);

                            // Send admin notification email
                            $get_super_admin = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_super_admin LIMIT 1");
                            if($get_super_admin) {
                                $admin_email = $get_super_admin['email'];
                                $email_placeholders = array(
                                    "{firstname}" => $first,
                                    "{lastname}" => $last,
                                    "{email}" => $email,
                                    "{website}" => $website_url
                                );
                                $email_subject = getSuperAdminEmailTemplate('new-vendor-pending-admin-alert', 'subject');
                                $email_body = getSuperAdminEmailTemplate('new-vendor-pending-admin-alert', 'body');
                                foreach($email_placeholders as $key => $val) {
                                    $email_subject = str_replace($key, $val, $email_subject);
                                    $email_body = str_replace($key, $val, $email_body);
                                }
                                sendVendorEmail($admin_email, $email_subject, $email_body);
                            }

                            if ($payment_method == 'paystack') {
                                // Fetch package price
                                $package_res = mysqli_query($connection_server, "SELECT price FROM sas_billing_packages WHERE id='$billing_package_id'");
                                $package = mysqli_fetch_assoc($package_res);
                                $amount_in_kobo = $package['price'] * 100;

                                // Fetch Paystack secret key
                                $gateway_res = mysqli_query($connection_server, "SELECT secret_key FROM sas_super_admin_payment_gateways WHERE gateway_name='paystack'");
                                $gateway = mysqli_fetch_assoc($gateway_res);
                                $secret_key = $gateway['secret_key'];

                                if ($secret_key) {
                                    $callback_url = $web_http_host . '/web/paystack_callback.php';
                                    $reference = 'vendor_reg_' . $pending_id . '_' . time();

                                    $post_data = [
                                        'email' => $email,
                                        'amount' => $amount_in_kobo,
                                        'callback_url' => $callback_url,
                                        'reference' => $reference,
                                        'metadata' => [
                                            'pending_vendor_id' => $pending_id,
                                            'type' => 'vendor_subscription'
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
                                            mysqli_query($connection_server, "UPDATE sas_pending_vendors SET paystack_reference='$reference' WHERE id='$pending_id'");
                                            header('Location: ' . $result['data']['authorization_url']);
                                            exit();
                                        } else {
                                            $_SESSION["product_purchase_response"] = "Could not initialize payment: " . $result['message'];
                                        }
                                    }
                                } else {
                                     $_SESSION["product_purchase_response"] = "Paystack payment gateway is not configured. Please contact support.";
                                }
                            } else { // Manual bank deposit
                                header("Location: /web/manual_payment.php");
                                exit();
                            }
                        } else {
                            $_SESSION["product_purchase_response"] = "Could not save your registration. Please try again.";
                        }
                        header("Location: ".$_SERVER["REQUEST_URI"]);
                        exit();
                    } else {
                        $_SESSION["product_purchase_response"] = "A vendor with the same Website URL already exists.";
                    }
                } else {
                     $_SESSION["product_purchase_response"] = "A vendor with the same Email already exists.";
                }
            } else {
                $_SESSION["product_purchase_response"] = "Invalid Email format.";
            }
        } else {
            $_SESSION["product_purchase_response"] = "Please fill all required fields.";
        }
        header("Location: ".$_SERVER["REQUEST_URI"]);
        exit();
    }
?>
<!DOCTYPE html>
<head>
    <title>Vendor Registration</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets-2/css/style.css" rel="stylesheet">
</head>
<body>
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center py-4">
                                <a href="#" class="logo d-flex align-items-center w-auto">
                                    <span class="d-none d-lg-block">Vendor Registration</span>
                                </a>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Create Your Vendor Account</h5>
                                        <p class="text-center small">Enter your details to register</p>
                                    </div>

                                    <?php if(isset($_SESSION["product_purchase_response"])): ?>
                                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                                            <?php echo $_SESSION["product_purchase_response"]; unset($_SESSION["product_purchase_response"]); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <form class="row g-3" method="post" action="">
                                        <div class="col-12">
                                            <label class="form-label">First Name</label>
                                            <input type="text" name="first" class="form-control" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" name="last" class="form-control" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Home Address</label>
                                            <input type="text" name="address" class="form-control" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" name="phone" class="form-control" required pattern="[0-9]{11}">
                                        </div>
                                        <div class="col-12">
                                            <div class="alert alert-warning">
                                                <strong>Domain Setup Instructions:</strong><br>
                                                Please note that domain name registration is not free. You can register your domain through our suggested registrar: <a href="<?php echo htmlspecialchars($registrar_url); ?>" target="_blank"><?php echo htmlspecialchars($registrar_url); ?></a><br><br>
                                                To point your domain to our servers, please use the following nameservers:<br>
                                                <pre><?php echo htmlspecialchars($nameservers); ?></pre>
                                                If you plan to use a subdomain, please create an A record pointing to the following IP address:<br>
                                                <strong><?php echo htmlspecialchars($ip_address); ?></strong>
                                            </div>
                                            <label class="form-label">Website URL (e.g., yoursite.com)</label>
                                            <input type="text" name="website-url" class="form-control" required>
                                        </div>
                                        <hr>
                                        <div class="col-12">
                                            <label class="form-label">Billing Package</label>
                                            <select name="billing_package_id" class="form-select" required>
                                                <option value="" selected disabled>Choose a package...</option>
                                                <?php
                                                    $packages_result = mysqli_query($connection_server, "SELECT * FROM sas_billing_packages ORDER BY price ASC");
                                                    while($package = mysqli_fetch_assoc($packages_result)) {
                                                        echo "<option value='{$package['id']}'>".htmlspecialchars($package['name'])." - ₦".number_format($package['price'], 2)." for ".$package['duration_days']." days</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                         <div class="col-12">
                                            <label class="form-label">Payment Method</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="payment_method" id="paystack" value="paystack" required>
                                                    <label class="form-check-label" for="paystack">Paystack (Online)</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="payment_method" id="bank_deposit" value="bank_deposit">
                                                    <label class="form-check-label" for="bank_deposit">Manual Bank Deposit</label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-12">
                                            <label class="form-label">Password</label>
                                            <input type="password" name="pass" class="form-control" required>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit" name="create-profile">Create Account</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <script src="../assets-2/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>