<?php
include_once("daily-bonus.php");

function process_post_purchase_rewards($user_id, $amount, $tid) {
    global $connection_server, $get_logged_user_details;
    // 1. Handle Affiliate/Referral Bonus (Existing System)
    $referral_username = $get_logged_user_details["referral"];
    if (!empty($referral_username)) {
        $select_user_referral_setting = mysqli_query($connection_server, "SELECT * FROM user_referral_setting LIMIT 1");
        if (mysqli_num_rows($select_user_referral_setting) >= 1) {
            $get_user_referral_setting = mysqli_fetch_assoc($select_user_referral_setting);
            $upgrade_referral_commission = 0;
            if ($get_user_referral_setting["commission_mode"] == "flat") {
                $upgrade_referral_commission = $get_user_referral_setting[strtolower($get_logged_user_details["account_level"])."_commission"];
            } elseif ($get_user_referral_setting["commission_mode"] == "percent") {
                $upgrade_referral_commission = ($amount * ($get_user_referral_setting[strtolower($get_logged_user_details["account_level"])."_commission"] / 100));
            }
            if ($upgrade_referral_commission > 0) {
                $ref_tx_ref = uniqid("commission_", true);
                $ref_product_type_1 = "Referral Purchase Commission";
                $ref_product_qty = "1";
                $ref_product_id = $get_logged_user_details["username"];
                $ref_amount_1 = $upgrade_referral_commission;
                $ref_profit = "";
                $ref_description = "Referral Commision from " . ucwords($get_logged_user_details["username"]) . " Purchase";
                $ref_api_url = $_SERVER["HTTP_HOST"];
                $ref_api_ref = "";
                $ref_status = "success";
                central_billing($referral_username, "credit", $ref_tx_ref, $ref_product_type_1, $ref_product_qty, $ref_product_id, $ref_amount_1, $ref_profit, $ref_description, $ref_api_url, $ref_api_ref, $ref_status);
            }
        }
    }

    // 2. Handle Daily Purchase Loyalty Bonus (New System)
    return handle_bonus_award($user_id);
}
?>
