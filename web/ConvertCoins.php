<?php
session_start();
if (!isset($_SESSION["user_session"])) {
    header("Location: /web/Login.php");
    exit();
}
include_once("../func/bc-config.php");

$user_id = $get_logged_user_details["id"];
$username = $get_logged_user_details["username"];
$vendor_id = $get_logged_user_details["vendor_id"];

// Fetch current coin balance
$total_points = 0;
$points_query = "SELECT SUM(point_amount) as total_points FROM points_log WHERE user_id = ?";
$stmt = mysqli_prepare($connection_server, $points_query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $points_result = mysqli_stmt_get_result($stmt);
    $points_data = mysqli_fetch_assoc($points_result);
    $total_points = $points_data['total_points'] ?? 0;
}

// Fetch Conversion Rate from settings
$coins_per_naira = 20;
$settings_query = mysqli_query($connection_server, "SELECT coins_per_naira FROM sas_coin_settings WHERE vendor_id = '$vendor_id'");
if ($settings = mysqli_fetch_assoc($settings_query)) {
    $coins_per_naira = $settings['coins_per_naira'];
}

if (isset($_POST["convert-coins"])) {
    $coins_to_convert = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["coin-amount"])));

    if (is_numeric($coins_to_convert) && $coins_to_convert > 0) {
        if ($total_points >= $coins_to_convert) {
            if ($coins_per_naira > 0) {
                $naira_value = $coins_to_convert / $coins_per_naira;
                $reference = "CONV-" . substr(str_shuffle("1234567890"), 0, 10);

                // 1. Debit coins from points_log
                $stmt_points = mysqli_prepare($connection_server, "INSERT INTO points_log (user_id, point_amount, log_type) VALUES (?, ?, 'COIN_CONVERSION')");
                $negative_coins = -$coins_to_convert;
                mysqli_stmt_bind_param($stmt_points, "id", $user_id, $negative_coins);

                // 2. Credit user wallet
                // We use the chargeUser function from bc-func.php which should be included via bc-config.php
                $credit_description = "Coin conversion: $coins_to_convert coins to N" . number_format($naira_value, 2);
                $credit_status = chargeUser("credit", $username, "Coin Conversion", $reference, "", $naira_value, $naira_value, $credit_description, "WEB", $_SERVER["HTTP_HOST"], 1);

                if ($credit_status === "success" && mysqli_stmt_execute($stmt_points)) {
                    $_SESSION["product_purchase_response"] = "Successfully converted $coins_to_convert coins to ₦" . number_format($naira_value, 2);
                } else {
                    $_SESSION["product_purchase_response"] = "Error: Conversion failed. Please try again.";
                }
            } else {
                $_SESSION["product_purchase_response"] = "Error: Conversion rate not properly configured.";
            }
        } else {
            $_SESSION["product_purchase_response"] = "Error: Insufficient coins. You have $total_points coins.";
        }
    } else {
        $_SESSION["product_purchase_response"] = "Error: Invalid amount entered.";
    }
    header("Location: " . $_SERVER["REQUEST_URI"]);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Convert VTU Coins | <?php echo $get_all_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="<?php echo $get_all_site_details["primary_color"] ?? "#198754"; ?>" />
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets-2/css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include("../func/bc-header.php"); ?>

    <div class="pagetitle">
      <h1>CONVERT VTU COINS</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="Dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Convert Coins</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="card info-card sales-card">
                    <div class="card-body p-4">
                        <h5 class="card-title">Coin Conversion <span>| Rate: <?php echo $coins_per_naira; ?> Coins = ₦1.00</span></h5>

                        <div class="text-center mb-4">
                            <div class="display-4 fw-bold text-primary"><?php echo number_format($total_points); ?></div>
                            <div class="text-muted">Available VTU Coins</div>
                        </div>

                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="coin-amount" class="form-label">Coins to Convert</label>
                                <input type="number" name="coin-amount" id="coin-amount" class="form-control form-control-lg" placeholder="Enter amount of coins" required onkeyup="updatePreview(this.value)">
                            </div>
                            <div class="alert alert-info mb-3">
                                You will receive: <strong id="naira-preview">₦0.00</strong> in your wallet fund.
                            </div>
                            <button name="convert-coins" type="submit" class="btn btn-primary btn-lg w-100">CONVERT NOW</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body pt-3">
                        <h5 class="card-title">Conversion History</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Coins</th>
                                        <th>Value</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $history_query = "SELECT * FROM points_log WHERE user_id = ? AND log_type = 'COIN_CONVERSION' ORDER BY timestamp DESC LIMIT 20";
                                    $stmt_h = mysqli_prepare($connection_server, $history_query);
                                    if ($stmt_h) {
                                        mysqli_stmt_bind_param($stmt_h, "i", $user_id);
                                        mysqli_stmt_execute($stmt_h);
                                        $history_result = mysqli_stmt_get_result($stmt_h);
                                        if (mysqli_num_rows($history_result) > 0) {
                                            while ($row = mysqli_fetch_assoc($history_result)) {
                                                $coins_debited = abs($row["point_amount"]);
                                                $naira_value = $coins_debited / $coins_per_naira;
                                                echo "<tr>
                                                    <td>" . number_format($coins_debited) . "</td>
                                                    <td>₦" . number_format($naira_value, 2) . "</td>
                                                    <td>" . date('M d, Y h:i A', strtotime($row["timestamp"])) . "</td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='3' class='text-center'>No conversion history found.</td></tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function updatePreview(val) {
            const coinsPerNaira = <?php echo $coins_per_naira; ?>;
            if (coinsPerNaira > 0) {
                const naira = val / coinsPerNaira;
                document.getElementById('naira-preview').innerText = '₦' + naira.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                document.getElementById('naira-preview').innerText = 'N/A';
            }
        }
    </script>

    <?php include("../func/bc-footer.php"); ?>
</body>
</html>
