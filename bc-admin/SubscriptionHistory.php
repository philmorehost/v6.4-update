<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
include("../func/bc-admin-config.php");

$history = mysqli_query($connection_server, "SELECT s.*, p.name as package_name
    FROM sas_vendor_subscriptions s
    JOIN sas_billing_packages p ON s.package_id = p.id
    WHERE s.vendor_id = '".$get_logged_admin_details["id"]."'
    ORDER BY s.date DESC");

?>
<!DOCTYPE html>
<head>
    <title>Subscription History | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets-2/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include("../func/bc-admin-header.php"); ?>

    <div class="pagetitle">
        <h1>Subscription History</h1>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">All Subscriptions</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Package</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($history)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['package_name']); ?></td>
                                <td>₦<?php echo number_format($row['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['reference']); ?></td>
                                <td><?php echo $row['date']; ?></td>
                                <td><?php echo $row['expiry_date']; ?></td>
                                <td>
                                    <?php if(strtotime($row['expiry_date']) >= strtotime(date('Y-m-d'))): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Expired</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <?php include("../func/bc-admin-footer.php"); ?>
</body>
</html>
