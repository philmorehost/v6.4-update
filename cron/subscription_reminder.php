<?php
// This script should be run daily via a cron job.
include_once(__DIR__ . "/../func/bc-connect.php");
include_once(__DIR__ . "/../func/bc-func.php");
include_once(__DIR__ . "/../func/bc-tables.php");

// Set timezone to avoid date mismatches
date_default_timezone_set('Africa/Lagos');

// --- Main Logic ---
$today = date('Y-m-d');

// 1. Find vendors whose subscription expires in 15 days and who haven't been notified for 15 days
$reminder_date_15 = date('Y-m-d', strtotime('+15 days'));
$sql_15 = "
    SELECT id, email, firstname, lastname, expiry_date
    FROM sas_vendors
    WHERE expiry_date = ?
    AND expiry_notified < 1
    AND status = 1
";
$stmt_15 = mysqli_prepare($connection_server, $sql_15);
mysqli_stmt_bind_param($stmt_15, "s", $reminder_date_15);
mysqli_stmt_execute($stmt_15);
$result_15 = mysqli_stmt_get_result($stmt_15);

if ($result_15 && mysqli_num_rows($result_15) > 0) {
    echo "Found " . mysqli_num_rows($result_15) . " vendors for 15-day reminder.\n";
    while ($vendor = mysqli_fetch_assoc($result_15)) {
        send_expiry_reminder($vendor, 15);
        mysqli_query($connection_server, "UPDATE sas_vendors SET expiry_notified = 1 WHERE id = '" . $vendor['id'] . "'");
    }
}

// 2. Find vendors whose subscription expires in 7 days and who haven't been notified for 7 days
$reminder_date_7 = date('Y-m-d', strtotime('+7 days'));
$sql_7 = "
    SELECT id, email, firstname, lastname, expiry_date
    FROM sas_vendors
    WHERE expiry_date = ?
    AND expiry_notified < 2
    AND status = 1
";
$stmt_7 = mysqli_prepare($connection_server, $sql_7);
mysqli_stmt_bind_param($stmt_7, "s", $reminder_date_7);
mysqli_stmt_execute($stmt_7);
$result_7 = mysqli_stmt_get_result($stmt_7);

if ($result_7 && mysqli_num_rows($result_7) > 0) {
    echo "Found " . mysqli_num_rows($result_7) . " vendors for 7-day reminder.\n";
    while ($vendor = mysqli_fetch_assoc($result_7)) {
        send_expiry_reminder($vendor, 7);
        mysqli_query($connection_server, "UPDATE sas_vendors SET expiry_notified = 2 WHERE id = '" . $vendor['id'] . "'");
    }
}

function send_expiry_reminder($vendor, $days) {
    global $connection_server, $mail_sender_name, $mail_headers;
    $email = $vendor['email'];
    $firstname = $vendor['firstname'];
    $lastname = $vendor['lastname'];
    $expiry_date_formatted = date('F j, Y', strtotime($vendor['expiry_date']));

    $email_subject = "Your Subscription is Expiring Soon ($days days left)!";
    $email_body = "
        <p>Dear " . htmlspecialchars($firstname) . " " . htmlspecialchars($lastname) . ",</p>
        <p>This is a friendly reminder that your subscription is scheduled to expire in $days days, on <strong>" . $expiry_date_formatted . "</strong>.</p>
        <p>To ensure uninterrupted access to our services, please log in to your account and renew your subscription at your earliest convenience.</p>
        <p>Thank you for being a valued vendor!</p>
    ";

    $mail_html_body = mailDesignTemplate($email_subject, $email_body, []);
    customBCMailSender($mail_sender_name, $email, $email_subject, $mail_html_body, $mail_headers);
}

echo "Cron job finished.\n";
?>
