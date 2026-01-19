<?php session_start();
    include("../func/bc-spadmin-config.php");

    // Handle actions
    if(isset($_GET['action']) && isset($_GET['id'])) {
        $action = $_GET['action'];
        $pending_id = mysqli_real_escape_string($connection_server, $_GET['id']);

        // Mark as Paid for manual deposit
        if($action == 'mark_paid') {
            mysqli_query($connection_server, "UPDATE sas_pending_vendors SET payment_status='paid' WHERE id='$pending_id'") or die("Error marking as paid: " . mysqli_error($connection_server));
            $_SESSION['page_alert'] = "Vendor marked as paid.";
            header("Location: VendorRegistrations.php");
            exit();
        }

        $result = mysqli_query($connection_server, "SELECT * FROM sas_pending_vendors WHERE id='$pending_id'") or die("Error fetching pending vendor: " . mysqli_error($connection_server));
        if(mysqli_num_rows($result) > 0) {
            $pending_vendor = mysqli_fetch_assoc($result);

            if($action == 'approve') {
                if(trim($pending_vendor['payment_status']) == 'paid') {
                    // 1. Get package details
                    $package_id = $pending_vendor['billing_package_id'];
                    $package_result = mysqli_query($connection_server, "SELECT * FROM sas_billing_packages WHERE id='$package_id'") or die("Error fetching package details: " . mysqli_error($connection_server));
                    $package = mysqli_fetch_assoc($package_result);
                    $duration_days = $package['duration_days'];

                    // 2. Insert into main vendors table
                    $email = $pending_vendor['email'];
                    $password = $pending_vendor['password'];
                    $firstname = $pending_vendor['firstname'];
                    $lastname = $pending_vendor['lastname'];
                    $phone_number = $pending_vendor['phone_number'];
                    $website_url = $pending_vendor['website_url'];
                    $home_address = $pending_vendor['home_address'];

                    $insert_sql = "INSERT INTO sas_vendors (email, password, firstname, lastname, phone_number, website_url, home_address, balance, status)
                                   VALUES ('$email', '$password', '$firstname', '$lastname', '$phone_number', '$website_url', '$home_address', '0.00', '1')";

                    if(mysqli_query($connection_server, $insert_sql)) {
                        $new_vendor_id = mysqli_insert_id($connection_server);

                        // 3. Set subscription dates
                        $start_date = date("Y-m-d");
                        $expiry_date = date('Y-m-d', strtotime("+$duration_days days"));

                        // 4. Update vendor with subscription info
                        $update_sql = "UPDATE sas_vendors SET start_date='$start_date', expiry_date='$expiry_date', current_billing_id='$package_id' WHERE id='$new_vendor_id'";
                        mysqli_query($connection_server, $update_sql) or die("Error updating vendor subscription: " . mysqli_error($connection_server));

                        // 5. Log this initial subscription to the history table
                        $price = $package['price'];
                        $purchase_date = date('Y-m-d H:i:s');
                        $log_sql = "INSERT INTO sas_vendor_subscriptions (vendor_id, package_id, purchase_date, expiry_date, amount_paid) VALUES ('$new_vendor_id', '$package_id', '$purchase_date', '$expiry_date', '$price')";
                        mysqli_query($connection_server, $log_sql) or die("Error logging subscription: " . mysqli_error($connection_server));

                        // 6. Delete from pending vendors
                        mysqli_query($connection_server, "DELETE FROM sas_pending_vendors WHERE id='$pending_id'") or die("Error deleting from pending vendors: " . mysqli_error($connection_server));


                        // Send welcome email
                        $email_placeholders = array(
                            "{firstname}" => $firstname,
                            "{lastname}" => $lastname,
                            "{expiry_date}" => date('F j, Y', strtotime($expiry_date))
                        );
                        $email_subject = getSuperAdminEmailTemplate('vendor-welcome-activated', 'subject');
                        $email_body = getSuperAdminEmailTemplate('vendor-welcome-activated', 'body');
                        foreach($email_placeholders as $key => $val) {
                            $email_subject = str_replace($key, $val, $email_subject);
                            $email_body = str_replace($key, $val, $email_body);
                        }
                        sendVendorEmail($email, $email_subject, $email_body);

                        $_SESSION['page_alert'] = "Vendor approved and account activated.";
                    } else {
                        $_SESSION['page_alert'] = "Error approving vendor: " . mysqli_error($connection_server);
                    }
                } else {
                    $_SESSION['page_alert'] = "Cannot approve vendor. Payment has not been confirmed.";
                }

            } elseif($action == 'decline') {
                $email = $pending_vendor['email'];
                $firstname = $pending_vendor['firstname'];
                $lastname = $pending_vendor['lastname'];

                mysqli_query($connection_server, "DELETE FROM sas_pending_vendors WHERE id='$pending_id'") or die("Error declining vendor: " . mysqli_error($connection_server));

                // Send rejection email
                $email_placeholders = array(
                    "{firstname}" => $firstname,
                    "{lastname}" => $lastname
                );
                $email_subject = getSuperAdminEmailTemplate('vendor-rejection', 'subject');
                $email_body = getSuperAdminEmailTemplate('vendor-rejection', 'body');
                foreach($email_placeholders as $key => $val) {
                    $email_subject = str_replace($key, $val, $email_subject);
                    $email_body = str_replace($key, $val, $email_body);
                }
                sendVendorEmail($email, $email_subject, $email_body);

                $_SESSION['page_alert'] = "Vendor registration declined.";
            }
        } else {
            $_SESSION['page_alert'] = "Invalid registration ID.";
        }
        header("Location: VendorRegistrations.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pending Vendor Registrations</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets-2/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include("../func/bc-spadmin-header.php"); ?>

    <div class="pagetitle">
        <h1>Pending Registrations</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="Dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Pending Registrations</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pending Vendor Approvals</h5>
                        <?php if(isset($_SESSION['page_alert'])): ?>
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['page_alert']; unset($_SESSION['page_alert']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Contact</th>
                                        <th scope="col">Package</th>
                                        <th scope="col">Payment Details</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT pv.*, bp.name as package_name
                                                FROM sas_pending_vendors pv
                                                JOIN sas_billing_packages bp ON pv.billing_package_id = bp.id
                                                ORDER BY pv.reg_date DESC";
                                        $result = mysqli_query($connection_server, $sql) or die("Error fetching registration list: " . mysqli_error($connection_server));
                                        $count = 1;
                                        if(mysqli_num_rows($result) > 0):
                                            while($row = mysqli_fetch_assoc($result)):
                                    ?>
                                    <tr>
                                        <th scope="row"><?php echo $count++; ?></th>
                                        <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?><br><small><?php echo htmlspecialchars($row['website_url']); ?></small></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?><br><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['package_name']); ?></td>
                                        <td>
                                            <strong>Method:</strong> <?php echo ucwords(str_replace('_', ' ', $row['payment_method'])); ?><br>
                                            <strong>Status:</strong>
                                            <?php if(trim($row['payment_status']) == 'paid'): ?>
                                                <span class="badge bg-success">Paid</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>

                                           
                                            <?php if(!empty($row['paystack_reference'])): ?>
                                                <br><small>Ref: <?php echo htmlspecialchars($row['paystack_reference']); ?></small>
                                            <?php endif; ?>
                                            <?php if(!empty($row['payment_proof_path'])): ?>
                                                <br><a href="/<?php echo htmlspecialchars($row['payment_proof_path']); ?>" target="_blank">View Proof</a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('Y-m-d', strtotime($row['reg_date'])); ?></td>
                                        <td>
                                            <?php if($row['payment_method'] == 'bank_deposit' && trim($row['payment_status']) != 'paid'): ?>
                                                <a href="?action=mark_paid&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info mb-1" onclick="return confirm('Are you sure you have confirmed this payment?');">Mark as Paid</a>
                                            <?php endif; ?>
                                            <a href="?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success <?php if(trim($row['payment_status']) != 'paid') echo 'disabled'; ?>" onclick="return confirm('Are you sure you want to approve this vendor?');">Approve</a>
                                            <a href="?action=decline&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to decline this registration?');">Decline</a>
                                        </td>
                                    </tr>
                                    <?php
                                            endwhile;
                                        else:
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No pending registrations found.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include("../func/bc-spadmin-footer.php"); ?>
</body>
</html>