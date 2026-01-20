<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();
include("../func/bc-config.php");

// Fetch loyalty settings for the vendor
$vendor_id = $get_logged_user_details["vendor_id"];
$settings_query = mysqli_query($connection_server, "SELECT * FROM sas_settings WHERE vendor_id = '$vendor_id' AND setting_name IN ('points_conversion_rate', 'min_points_conversion')");
$settings = [];
while($row = mysqli_fetch_assoc($settings_query)){
    $settings[$row['setting_name']] = $row['setting_value'];
}
$points_conversion_rate = $settings['points_conversion_rate'] ?? 100;
$min_points_conversion = $settings['min_points_conversion'] ?? 100;

// Fetch user's current points balance
$username = $get_logged_user_details["username"];
$user_points_balance = get_user_vtu_details($username)['total_points'];

// Fetch user's conversion history
$stmt = mysqli_prepare($connection_server, "SELECT * FROM sas_conversions WHERE vendor_id = ? AND username = ? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt, "is", $vendor_id, $username);
mysqli_stmt_execute($stmt);
$history_query = mysqli_stmt_get_result($stmt);
$conversion_history = [];
while($row = mysqli_fetch_assoc($history_query)){
    $conversion_history[] = $row;
}

// Handle form submission
if (isset($_POST["convert-coins"])) {
    $points_to_convert = (int) mysqli_real_escape_string($connection_server, $_POST["points"]);

    // --- Validation ---
    if ($points_to_convert <= 0) {
        $_SESSION["product_purchase_response"] = "Invalid point amount entered.";
    } elseif ($points_to_convert < $min_points_conversion) {
        $_SESSION["product_purchase_response"] = "You must convert at least " . $min_points_conversion . " points.";
    } elseif ($points_to_convert > $user_points_balance) {
        $_SESSION["product_purchase_response"] = "Insufficient points balance.";
    } else {
        // --- Conversion Logic ---
        $amount_in_naira = $points_to_convert / $points_conversion_rate;
        $username = $get_logged_user_details["username"];

        // Use prepared statements to prevent SQL injection
        $query = "INSERT INTO sas_conversions (vendor_id, username, points, amount, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt = mysqli_prepare($connection_server, $query);
        mysqli_stmt_bind_param($stmt, "isid", $vendor_id, $username, $points_to_convert, $amount_in_naira);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION["product_purchase_response"] = "Conversion request for " . $points_to_convert . " points submitted successfully! It is pending admin approval.";
        } else {
            $_SESSION["product_purchase_response"] = "An error occurred. Please try again.";
        }
    }
    header("Location: " . $_SERVER["REQUEST_URI"]);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Convert Coins | <?php echo $get_all_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
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
        <h1>CONVERT COINS</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Convert Coins</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="col-12">
            <div class="card info-card px-5 py-5">
                <span class="text-dark h3">CONVERT YOUR VTU COINS TO CASH</span><br>

                <div class="alert alert-info">
                    <p><strong>Your current balance:</strong> <?php echo number_format($user_points_balance, 0); ?> VTU Coins</p>
                    <p><strong>Conversion Rate:</strong> <?php echo $points_conversion_rate; ?> Coins = ₦1.00</p>
                    <p><strong>Minimum to Convert:</strong> <?php echo number_format($min_points_conversion, 0); ?> Coins</p>
                </div>

                <form method="post" action="">
                    <div class="mb-3">
                        <label for="points" class="form-label">Points to Convert</label>
                        <input type="number" class="form-control" id="points" name="points" placeholder="Enter amount of points" required onkeyup="calculateNairaValue()">
                    </div>

                    <div class="text-dark h5">
                        <span id="naira-value" class="fw-bold">You will receive: ₦0.00</span>
                    </div><br/>

                    <button id="convert-btn" name="convert-coins" type="submit" class="btn btn-success col-12 mt-3" disabled>
                        SUBMIT CONVERSION REQUEST
                    </button>
                </form>
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="card info-card px-5 py-5">
                <span class="text-dark h3">CONVERSION HISTORY</span><br>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Points</th>
                                <th scope="col">Amount (₦)</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($conversion_history)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No conversion history found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($conversion_history as $conversion): ?>
                                    <tr>
                                        <td><?php echo date("Y-m-d H:i", strtotime($conversion['date'])); ?></td>
                                        <td><?php echo number_format($conversion['points'], 0); ?></td>
                                        <td><?php echo number_format($conversion['amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                switch ($conversion['status']) {
                                                    case 'pending':
                                                        echo 'warning';
                                                        break;
                                                    case 'approved':
                                                        echo 'success';
                                                        break;
                                                    case 'declined':
                                                        echo 'danger';
                                                        break;
                                                    default:
                                                        echo 'secondary';
                                                }
                                            ?>"><?php echo ucfirst($conversion['status']); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <?php include("../func/bc-footer.php"); ?>

    <script>
        const pointsInput = document.getElementById('points');
        const nairaValueSpan = document.getElementById('naira-value');
        const convertBtn = document.getElementById('convert-btn');

        const conversionRate = <?php echo $points_conversion_rate; ?>;
        const minConversion = <?php echo $min_points_conversion; ?>;
        const userBalance = <?php echo $user_points_balance; ?>;

        function calculateNairaValue() {
            const points = parseInt(pointsInput.value, 10);

            if (isNaN(points) || points <= 0) {
                nairaValueSpan.textContent = 'You will receive: ₦0.00';
                convertBtn.disabled = true;
                return;
            }

            const nairaValue = points / conversionRate;
            nairaValueSpan.textContent = `You will receive: ₦${nairaValue.toFixed(2)}`;

            if (points >= minConversion && points <= userBalance) {
                convertBtn.disabled = false;
            } else {
                convertBtn.disabled = true;
            }
        }
    </script>
</body>
</html>