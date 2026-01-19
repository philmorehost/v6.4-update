<?php
session_start();
if (!isset($_SESSION["user_session"])) {
    header("Location: /web/Login.php");
    exit();
}
include_once("../func/bc-config.php");

if (isset($_POST["upgrade-user"])) {
    $upgrade_type = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["upgrade-type"])));
    $username = $get_logged_user_details["username"];
    $vendor_id = $get_logged_user_details["vendor_id"];

    $level_map = array("smart" => 1, "agent" => 2, "api" => 3);
    $new_level = $level_map[$upgrade_type] ?? 0;

    if ($new_level > $get_logged_user_details["account_level"]) {
        // Fetch price
        $stmt = mysqli_prepare($connection_server, "SELECT * FROM sas_user_upgrade_price WHERE vendor_id=? AND account_type=? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "ii", $vendor_id, $new_level);
        mysqli_stmt_execute($stmt);
        $price_res = mysqli_stmt_get_result($stmt);
        if ($price_data = mysqli_fetch_assoc($price_res)) {
            $price = $price_data["price"];

            if ($get_logged_user_details["balance"] >= $price) {
                $reference = "UPG-" . substr(str_shuffle("1234567890"), 0, 10);
                $description = "Account Upgrade to " . accountLevel($new_level);

                $debit = chargeUser("debit", $username, "Account Upgrade", $reference, "", $price, $price, $description, "WEB", $_SERVER["HTTP_HOST"], 1);

                if ($debit === "success") {
                    mysqli_query($connection_server, "UPDATE sas_users SET account_level='$new_level' WHERE id='".$get_logged_user_details["id"]."'");

                    // Award Affiliate Reward
                    $referral_id = $get_logged_user_details["referral_id"];
                    if (!empty($referral_id)) {
                        // Referral ID might be username or ID. Check which one.
                        $ref_query = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE (username = '$referral_id' OR id = '$referral_id') AND vendor_id = '$vendor_id' LIMIT 1");
                        if ($ref_user = mysqli_fetch_assoc($ref_query)) {
                            $ref_user_id = $ref_user["id"];

                            // Get Reward Amount from settings
                            $reward_amount = 100;
                            $settings_query = mysqli_query($connection_server, "SELECT affiliate_reward FROM sas_coin_settings WHERE vendor_id = '$vendor_id'");
                            if ($settings = mysqli_fetch_assoc($settings_query)) {
                                $reward_amount = $settings["affiliate_reward"];
                            }

                            // Insert into points_log
                            $stmt_reward = mysqli_prepare($connection_server, "INSERT INTO points_log (user_id, point_amount, log_type) VALUES (?, ?, 'AFFILIATE_UPGRADE_BONUS')");
                            mysqli_stmt_bind_param($stmt_reward, "ii", $ref_user_id, $reward_amount);
                            mysqli_stmt_execute($stmt_reward);
                        }
                    }

                    $_SESSION["product_purchase_response"] = "Account upgraded successfully to " . accountLevel($new_level);
                } else {
                    $_SESSION["product_purchase_response"] = "Error: Upgrade failed. " . $debit;
                }
            } else {
                $_SESSION["product_purchase_response"] = "Error: Insufficient balance for upgrade.";
            }
        }
    }
    header("Location: /web/Dashboard.php");
    exit();
}
?>
