<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
include("../func/bc-config.php");

// Function to format the log_type for display
function formatLogType($log_type) {
    return ucwords(str_replace('_', ' ', strtolower($log_type)));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Points History | <?php echo $get_all_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
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
    <?php include("../func/bc-header.php"); ?>

    <div class="pagetitle">
        <h1>POINTS HISTORY</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Points History</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="col-12">
            <div class="card info-card px-5 py-5">
                <div class="overflow-auto">
                    <table class="table table-responsive table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>S/N</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $username = $get_logged_user_details["username"];
                            $vendor_id = $get_logged_user_details["vendor_id"];

                            // Subquery to get the latest bonus log for each day
                            $query = "
                                SELECT id, username, vendor_id, point_amount, log_type, date
                                FROM sas_points_log
                                WHERE username = ? AND vendor_id = ? AND log_type <> 'DAILY_PURCHASE_BONUS'
                                UNION
                                SELECT l1.id, l1.username, l1.vendor_id, l1.point_amount, l1.log_type, l1.date
                                FROM sas_points_log l1
                                INNER JOIN (
                                    SELECT MAX(id) as max_id
                                    FROM sas_points_log
                                    WHERE username = ? AND vendor_id = ? AND log_type = 'DAILY_PURCHASE_BONUS'
                                    GROUP BY DATE(date)
                                ) l2 ON l1.id = l2.max_id
                                ORDER BY date DESC";

                            $stmt = mysqli_prepare($connection_server, $query);
                            mysqli_stmt_bind_param($stmt, "sisi", $username, $vendor_id, $username, $vendor_id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);

                            if (mysqli_num_rows($result) > 0) {
                                $sn = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $points_display = ($row['point_amount'] > 0) ? '+' . $row['point_amount'] : $row['point_amount'];
                                    $points_class = ($row['point_amount'] > 0) ? 'text-success' : 'text-danger';
                                    echo "<tr>
                                            <td>{$sn}</td>
                                            <td>" . date("M j, Y, g:i a", strtotime($row['date'])) . "</td>
                                            <td>" . formatLogType($row['log_type']) . "</td>
                                            <td class='{$points_class} fw-bold'>{$points_display}</td>
                                          </tr>";
                                    $sn++;
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No points history found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <?php include("../func/bc-footer.php"); ?>
</body>
</html>