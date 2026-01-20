<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include("bc-connect.php");
include("bc-func.php");
include("bc-tables.php");

// This script should be run via cron job daily

// 1. Send reminders (15 and 7 days before)
$reminders = array(15, 7);

foreach ($reminders as $days) {
    $target_date = date('Y-m-d', strtotime("+$days days"));
    $vendors = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE expiry_date = '$target_date' AND status = 1");

    while ($vendor = mysqli_fetch_assoc($vendors)) {
        $email = $vendor['email'];
        $firstname = $vendor['firstname'];
        $subject = "Subscription Renewal Reminder - $days Days Remaining";
        $message = "Hello $firstname,\n\nThis is a reminder that your subscription will expire in $days days on " . $vendor['expiry_date'] . ". Please renew your subscription to avoid service interruption.\n\nBest regards,\n" . $_SERVER['HTTP_HOST'];

        // Use existing mail function if available, or standard mail()
        if (function_exists('sendVendorEmail')) {
            sendVendorEmail($email, $subject, $message);
        } else {
            mail($email, $subject, $message);
        }
    }
}

// 2. Handle Expiry (Suspension)
$today = date('Y-m-d');
$expired_vendors = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE expiry_date < '$today' AND expiry_date IS NOT NULL AND status = 1");

while ($vendor = mysqli_fetch_assoc($expired_vendors)) {
    // We don't necessarily need to change status to 0 if we handle it in bc-admin-config.php redirect
    // But we can mark them in a way if needed.
    // The requirement says "Implement a suspension", which I've done via redirect in Step 2.

    // Optionally log suspension
    // error_log("Vendor " . $vendor['email'] . " subscription expired and suspended.");
}

echo "Subscription check completed for " . date('Y-m-d H:i:s');
?>
