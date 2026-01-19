<?php
function award_daily_bonus($user_id, $transaction_timestamp) {
    global $connection_server;
    if (!$connection_server) return ['awarded' => false];
    $user_id = mysqli_real_escape_string($connection_server, $user_id);
    $transaction_timestamp = mysqli_real_escape_string($connection_server, $transaction_timestamp);
    $last_bonus = get_last_bonus_details($user_id);
    if ($last_bonus) {
        $last_bonus_time = strtotime($last_bonus['timestamp']);
        if (strtotime($transaction_timestamp) - $last_bonus_time < 24 * 3600) {
            return ['awarded' => false];
        }
    }
    $streak_day = calculate_consecutive_purchase_days($user_id) + 1;

    // Fetch vendor_id for the user to get specific coin settings
    $user_res = mysqli_query($connection_server, "SELECT vendor_id FROM sas_users WHERE id = '$user_id' LIMIT 1");
    $vendor_id = 0;
    if ($user_row = mysqli_fetch_assoc($user_res)) {
        $vendor_id = $user_row['vendor_id'];
    }

    $coins_to_award = get_streak_reward($streak_day, $vendor_id);
    $stmt = mysqli_prepare($connection_server, "INSERT INTO points_log (user_id, point_amount, log_type, timestamp) VALUES (?, ?, 'DAILY_PURCHASE_BONUS', ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iis", $user_id, $coins_to_award, $transaction_timestamp);
        mysqli_stmt_execute($stmt);
        return ['awarded' => true, 'amount' => $coins_to_award, 'streak' => $streak_day];
    }
    return ['awarded' => false];
}

function get_last_bonus_details($user_id) {
    global $connection_server;
    if (!$connection_server) return null;
    $user_id = mysqli_real_escape_string($connection_server, $user_id);
    $stmt = mysqli_prepare($connection_server, "SELECT timestamp FROM points_log WHERE user_id = ? AND log_type = 'DAILY_PURCHASE_BONUS' ORDER BY timestamp DESC LIMIT 1");
    if (!$stmt) return null;
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $last_bonus_result = mysqli_stmt_get_result($stmt);
    if ($last_bonus_result && mysqli_num_rows($last_bonus_result) > 0) {
        return mysqli_fetch_assoc($last_bonus_result);
    }
    return null;
}

function calculate_consecutive_purchase_days($user_id) {
    global $connection_server;
    if (!$connection_server) return 0;
    $user_id = mysqli_real_escape_string($connection_server, $user_id);
    $stmt = mysqli_prepare($connection_server, "SELECT DISTINCT DATE(timestamp) as date FROM points_log WHERE user_id = ? AND log_type = 'DAILY_PURCHASE_BONUS' ORDER BY date DESC LIMIT 7");
    if (!$stmt) return 0;
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $dates = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $dates[] = $row;
        }
    }

    $streak = 0;
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    if (empty($dates) || ($dates[0]['date'] != $today && $dates[0]['date'] != $yesterday)) {
        return 0;
    }

    $current_check = $dates[0]['date'];
    foreach ($dates as $date) {
        if ($date['date'] == $current_check) {
            $streak++;
            $current_check = date('Y-m-d', strtotime($current_check . ' -1 day'));
        } else {
            break;
        }
    }
    return $streak;
}

function handle_bonus_award($user_id) {
    $bonus_result = award_daily_bonus($user_id, date('Y-m-d H:i:s'));
    if ($bonus_result['awarded']) {
        return "Bonus Earned! You received " . $bonus_result['amount'] . " VTU Coins for maintaining your " . $bonus_result['streak'] . "-day purchase streak!";
    }
    return "";
}

function get_streak_reward($streak_day, $vendor_id = 0) {
    global $connection_server;

    if ($vendor_id > 0) {
        $settings_query = mysqli_query($connection_server, "SELECT * FROM sas_coin_settings WHERE vendor_id = '$vendor_id'");
        if ($settings = mysqli_fetch_assoc($settings_query)) {
            $column = "streak_" . (min($streak_day, 7));
            return $settings[$column] ?? 100;
        }
    }

    // Fallback defaults
    switch ($streak_day) {
        case 1: return 20;
        case 2: return 20;
        case 3: return 20;
        case 4: return 20;
        case 5: return 20;
        case 6: return 20;
        case 7: return 20;
        default: return 20;
    }
}
?>
