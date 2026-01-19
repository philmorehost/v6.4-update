<?php
session_start();
include("../func/bc-admin-config.php");

// Handle form submission to update loyalty settings
if (isset($_POST["update-loyalty-settings"])) {
    $vendor_id = $get_logged_admin_details["id"];

    // Update daily bonus amounts in the wide table format
    $bonus_amounts = [];
    for ($day = 1; $day <= 7; $day++) {
        $bonus_amounts[] = (int)$_POST["bonus_day_" . $day];
    }
    $first_purchase_bonus = (int)$_POST["first_purchase_bonus"];

    $query_bonus = "UPDATE sas_loyalty_bonus_settings SET day_1_bonus = ?, day_2_bonus = ?, day_3_bonus = ?, day_4_bonus = ?, day_5_bonus = ?, day_6_bonus = ?, day_7_bonus = ?, first_purchase_bonus = ? WHERE vendor_id = ?";
    $stmt_bonus = mysqli_prepare($connection_server, $query_bonus);
    mysqli_stmt_bind_param($stmt_bonus, "iiiiiiiii", $bonus_amounts[0], $bonus_amounts[1], $bonus_amounts[2], $bonus_amounts[3], $bonus_amounts[4], $bonus_amounts[5], $bonus_amounts[6], $first_purchase_bonus, $vendor_id);
    mysqli_stmt_execute($stmt_bonus);

    // Update conversion rate and minimum threshold in the key-value settings table
    $conversion_rate = $_POST["conversion_rate"];
    $min_conversion_threshold = $_POST["min_conversion_threshold"];

    $settings_to_update = [
        'points_conversion_rate' => $conversion_rate,
        'min_points_conversion' => $min_conversion_threshold,
    ];

    foreach ($settings_to_update as $name => $value) {
        $query_settings = "INSERT INTO sas_settings (vendor_id, setting_name, setting_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = ?";
        $stmt_settings = mysqli_prepare($connection_server, $query_settings);
        mysqli_stmt_bind_param($stmt_settings, "isss", $vendor_id, $name, $value, $value);
        mysqli_stmt_execute($stmt_settings);
    }

    $_SESSION["product_purchase_response"] = "Loyalty settings updated successfully!";
    header("Location: " . $_SERVER["REQUEST_URI"]);
    exit();
}

// Fetch current loyalty settings
$vendor_id = $get_logged_admin_details["id"];
$stmt = mysqli_prepare($connection_server, "SELECT * FROM sas_loyalty_bonus_settings WHERE vendor_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $vendor_id);
mysqli_stmt_execute($stmt);
$loyalty_settings_row = mysqli_stmt_get_result($stmt);
if(mysqli_num_rows($loyalty_settings_row) > 0){
    $loyalty_settings = mysqli_fetch_assoc($loyalty_settings_row);
} else {
    // If no settings exist, create a default row
    $stmt = mysqli_prepare($connection_server, "INSERT INTO sas_loyalty_bonus_settings (vendor_id) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "i", $vendor_id);
    mysqli_stmt_execute($stmt);

    $stmt = mysqli_prepare($connection_server, "SELECT * FROM sas_loyalty_bonus_settings WHERE vendor_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $vendor_id);
    mysqli_stmt_execute($stmt);
    $loyalty_settings_row = mysqli_stmt_get_result($stmt);
    $loyalty_settings = mysqli_fetch_assoc($loyalty_settings_row);
}

// Fetch conversion rate and minimum threshold from sas_settings
$stmt = mysqli_prepare($connection_server, "SELECT * FROM sas_settings WHERE vendor_id = ? AND setting_name IN ('points_conversion_rate', 'min_points_conversion')");
mysqli_stmt_bind_param($stmt, "i", $vendor_id);
mysqli_stmt_execute($stmt);
$settings_query = mysqli_stmt_get_result($stmt);
$settings = [];
while($row = mysqli_fetch_assoc($settings_query)){
    $settings[$row['setting_name']] = $row['setting_value'];
}
$points_conversion_rate = $settings['points_conversion_rate'] ?? 100;
$min_points_conversion = $settings['min_points_conversion'] ?? 100;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Loyalty Settings | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
    <?php include("../func/bc-admin-header.php"); ?>

    <div class="pagetitle">
        <h1>Loyalty Settings</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Loyalty Settings</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="col-12">
            <div class="card info-card px-5 py-5">
                <span class="text-dark h3">MANAGE LOYALTY REWARDS</span><br>
                <form method="post" action="">
                    <div class="text-dark h5">
                        <span class="h5">Daily Streak Bonuses (VTU Coins)</span>
                    </div><br />
                    <?php for ($day = 1; $day <= 7; $day++) : ?>
                        <div class="mb-3">
                            <label for="bonus_day_<?php echo $day; ?>" class="form-label">Day <?php echo $day; ?> Bonus</label>
                            <input type="number" class="form-control" id="bonus_day_<?php echo $day; ?>" name="bonus_day_<?php echo $day; ?>" value="<?php echo $loyalty_settings['day_' . $day . '_bonus'] ?? 0; ?>" required>
                        </div>
                    <?php endfor; ?>

                    <hr>

                    <div class="text-dark h5">
                        <span class="h5">Coin Conversion Settings</span>
                    </div><br />
                    <div class="mb-3">
                        <label for="conversion_rate" class="form-label">Conversion Rate (Points per Naira)</label>
                        <input type="number" step="0.01" class="form-control" id="conversion_rate" name="conversion_rate" value="<?php echo $points_conversion_rate; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="min_conversion_threshold" class="form-label">Minimum Conversion Threshold (Points)</label>
                        <input type="number" class="form-control" id="min_conversion_threshold" name="min_conversion_threshold" value="<?php echo $min_points_conversion; ?>" required>
                    </div>

                    <hr>

                    <div class="text-dark h5">
                        <span class="h5">Referral Settings</span>
                    </div><br />
                    <div class="mb-3">
                        <label for="first_purchase_bonus" class="form-label">First Purchase Referral Bonus (VTU Coins)</label>
                        <input type="number" class="form-control" id="first_purchase_bonus" name="first_purchase_bonus" value="<?php echo $loyalty_settings['first_purchase_bonus'] ?? 100; ?>" required>
                    </div>

                    <button name="update-loyalty-settings" type="submit" class="btn btn-success col-12 mt-3">
                        UPDATE SETTINGS
                    </button><br>
                </form>
            </div>
        </div>
    </section>

    <?php include("../func/bc-admin-footer.php"); ?>
</body>
</html>