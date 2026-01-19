<?php session_start();
    include("../func/bc-spadmin-config.php");

    // --- Fetch Report Data ---

    // 1. Estimated Total Revenue from active subscriptions
    $revenue_sql = "SELECT SUM(bp.price) as total_revenue
                    FROM sas_vendors v
                    JOIN sas_billing_packages bp ON v.current_billing_id = bp.id
                    WHERE v.status = 1 AND v.expiry_date >= CURDATE()";
    $revenue_res = mysqli_query($connection_server, $revenue_sql);
    $total_revenue = mysqli_fetch_assoc($revenue_res)['total_revenue'] ?? 0;

    // 2. Number of Active Subscriptions
    $active_sql = "SELECT COUNT(id) as active_count FROM sas_vendors WHERE status = 1 AND expiry_date >= CURDATE()";
    $active_res = mysqli_query($connection_server, $active_sql);
    $active_subscriptions = mysqli_fetch_assoc($active_res)['active_count'] ?? 0;

    // 3. New Vendors in the last 30 days
    $new_vendors_sql = "SELECT COUNT(id) as new_count FROM sas_vendors WHERE reg_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $new_vendors_res = mysqli_query($connection_server, $new_vendors_sql);
    $new_vendors_last_30_days = mysqli_fetch_assoc($new_vendors_res)['new_count'] ?? 0;

    // 4. Most Popular Subscription Package
    $popular_sql = "SELECT bp.name, COUNT(v.id) as count
                    FROM sas_vendors v
                    JOIN sas_billing_packages bp ON v.current_billing_id = bp.id
                    GROUP BY v.current_billing_id
                    ORDER BY count DESC
                    LIMIT 1";
    $popular_res = mysqli_query($connection_server, $popular_sql);
    $popular_package = mysqli_fetch_assoc($popular_res);
    $most_popular_package = $popular_package ? $popular_package['name'] . " (" . $popular_package['count'] . " vendors)" : "N/A";

?>
<!DOCTYPE html>
<head>
    <title>Subscription Reports</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets-2/css/style.css" rel="stylesheet">
    <style>
        .info-card { border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .card-blue { background-color: #eef7ff; }
        .card-red { background-color: #fceeed; }
        .card-green { background-color: #eefcef; }
        .card-yellow { background-color: #fff9e6; }
    </style>
</head>
<body>
    <?php include("../func/bc-spadmin-header.php"); ?>

    <div class="pagetitle">
        <h1>Subscription Reports</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="Dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Reports</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row">

            <!-- Total Revenue Card -->
            <div class="col-12 col-md-6">
                <div class="card info-card card-green shadow">
                    <div class="card-body">
                        <h5 class="card-title">Estimated Total Revenue <span>| Active</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="ps-3">
                                <h6>₦<?php echo number_format($total_revenue, 2); ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Total Revenue Card -->

            <!-- Active Subscriptions Card -->
            <div class="col-12 col-md-6">
                <div class="card info-card card-blue shadow">
                    <div class="card-body">
                        <h5 class="card-title">Active Subscriptions</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <div class="ps-3">
                                <h6><?php echo $active_subscriptions; ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Active Subscriptions Card -->

            <!-- New Vendors Card -->
            <div class="col-12 col-md-6">
                <div class="card info-card card-yellow shadow">
                    <div class="card-body">
                        <h5 class="card-title">New Vendors <span>| Last 30 Days</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                            <div class="ps-3">
                                <h6><?php echo $new_vendors_last_30_days; ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End New Vendors Card -->

            <!-- Most Popular Package Card -->
            <div class="col-12 col-md-6">
                <div class="card info-card card-red shadow">
                    <div class="card-body">
                        <h5 class="card-title">Most Popular Package</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <div class="ps-3">
                                <h6><?php echo htmlspecialchars($most_popular_package); ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Most Popular Package Card -->

        </div>
    </section>

    <?php include("../func/bc-spadmin-footer.php"); ?>
</body>
</html>