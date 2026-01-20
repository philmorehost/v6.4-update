<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
include("../func/bc-admin-config.php");

$vendor_id = $get_logged_admin_details["id"];

// Initialize settings if not exists
$check_settings = mysqli_query($connection_server, "SELECT * FROM sas_coin_settings WHERE vendor_id = '$vendor_id'");
if (mysqli_num_rows($check_settings) == 0) {
    mysqli_query($connection_server, "INSERT INTO sas_coin_settings (vendor_id) VALUES ('$vendor_id')");
}

if (isset($_POST["update-settings"])) {
    $streak_1 = mysqli_real_escape_string($connection_server, $_POST["streak_1"]);
    $streak_2 = mysqli_real_escape_string($connection_server, $_POST["streak_2"]);
    $streak_3 = mysqli_real_escape_string($connection_server, $_POST["streak_3"]);
    $streak_4 = mysqli_real_escape_string($connection_server, $_POST["streak_4"]);
    $streak_5 = mysqli_real_escape_string($connection_server, $_POST["streak_5"]);
    $streak_6 = mysqli_real_escape_string($connection_server, $_POST["streak_6"]);
    $streak_7 = mysqli_real_escape_string($connection_server, $_POST["streak_7"]);
    $coins_per_naira = mysqli_real_escape_string($connection_server, $_POST["coins_per_naira"]);
    $affiliate_reward = mysqli_real_escape_string($connection_server, $_POST["affiliate_reward"]);

    $update_query = "UPDATE sas_coin_settings SET
        streak_1 = '$streak_1',
        streak_2 = '$streak_2',
        streak_3 = '$streak_3',
        streak_4 = '$streak_4',
        streak_5 = '$streak_5',
        streak_6 = '$streak_6',
        streak_7 = '$streak_7',
        coins_per_naira = '$coins_per_naira',
        affiliate_reward = '$affiliate_reward'
        WHERE vendor_id = '$vendor_id'";

    if (mysqli_query($connection_server, $update_query)) {
        $_SESSION["product_purchase_response"] = "Coin settings updated successfully";
    } else {
        $_SESSION["product_purchase_response"] = "Error updating coin settings";
    }
    header("Location: " . $_SERVER["REQUEST_URI"]);
    exit();
}

$settings = mysqli_fetch_assoc(mysqli_query($connection_server, "SELECT * FROM sas_coin_settings WHERE vendor_id = '$vendor_id'"));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Coin Settings | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets-2/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include("../func/bc-admin-header.php"); ?>

    <div class="pagetitle">
      <h1>COIN SETTINGS</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="Dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Coin Settings</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card info-card p-4">
                    <div class="card-body">
                        <h5 class="card-title">VTU Loyalty Coin Settings</h5>
                        <form method="post" action="">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6>Streak Rewards (Daily Purchase)</h6>
                                    <p class="text-muted small">Coins awarded for each consecutive day of purchase (1 to 7 days).</p>
                                </div>
                                <?php for($i=1; $i<=7; $i++): ?>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Day <?php echo $i; ?> Reward</label>
                                    <input type="number" name="streak_<?php echo $i; ?>" class="form-control" value="<?php echo $settings['streak_'.$i]; ?>" required>
                                </div>
                                <?php endfor; ?>
                            </div>

                            <hr>

                            <div class="row mb-4 pt-2 border-top">
                                <div class="col-12 mt-3">
                                    <h6>Affiliate Bonus</h6>
                                    <p class="text-muted small">VTU Coins awarded to an affiliate when their referral performs a qualifying action (e.g., account upgrade).</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Affiliate Reward Amount</label>
                                    <div class="input-group">
                                        <input type="number" name="affiliate_reward" class="form-control" value="<?php echo $settings['affiliate_reward']; ?>" required>
                                        <span class="input-group-text">Coins</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4 pt-2 border-top">
                                <div class="col-12 mt-3">
                                    <h6>Conversion Rate</h6>
                                    <p class="text-muted small">How many VTU Coins are required to receive ₦1 during wallet conversion.</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">? Coins = ₦1</label>
                                    <div class="input-group">
                                        <input type="number" name="coins_per_naira" class="form-control" value="<?php echo $settings['coins_per_naira']; ?>" required>
                                        <span class="input-group-text">Coins</span>
                                    </div>
                                </div>
                            </div>

                            <button name="update-settings" type="submit" class="btn btn-primary w-100 py-2 fw-bold">SAVE SETTINGS</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include("../func/bc-admin-footer.php"); ?>
</body>
</html>
