<?php session_start();
    include("../func/bc-spadmin-config.php");

    // Handle Delete Request
    if(isset($_GET['delete_id'])) {
        $delete_id = mysqli_real_escape_string($connection_server, $_GET['delete_id']);
        mysqli_query($connection_server, "DELETE FROM sas_billing_packages WHERE id='$delete_id'");
        $_SESSION['page_alert'] = "Package deleted successfully!";
        header("Location: BillingPackages.php");
        exit();
    }

    // Handle Add/Edit Request
    if(isset($_POST['save_package'])) {
        $package_id = mysqli_real_escape_string($connection_server, $_POST['package_id']);
        $name = mysqli_real_escape_string($connection_server, $_POST['name']);
        $price = mysqli_real_escape_string($connection_server, $_POST['price']);
        $duration_days = mysqli_real_escape_string($connection_server, $_POST['duration_days']);

        if(empty($package_id)) {
            // Add New Package
            $sql = "INSERT INTO sas_billing_packages (name, price, duration_days) VALUES ('$name', '$price', '$duration_days')";
            $_SESSION['page_alert'] = "Package added successfully!";
        } else {
            // Update Existing Package
            $sql = "UPDATE sas_billing_packages SET name='$name', price='$price', duration_days='$duration_days' WHERE id='$package_id'";
            $_SESSION['page_alert'] = "Package updated successfully!";
        }

        if(!mysqli_query($connection_server, $sql)) {
            // If query fails, show the error instead of the generic success message
            $_SESSION['page_alert'] = "Error saving package: " . mysqli_error($connection_server);
        }

        header("Location: BillingPackages.php");
        exit();
    }

    // Fetch package for editing
    $edit_package = null;
    if(isset($_GET['edit_id'])) {
        $edit_id = mysqli_real_escape_string($connection_server, $_GET['edit_id']);
        $result = mysqli_query($connection_server, "SELECT * FROM sas_billing_packages WHERE id='$edit_id'");
        $edit_package = mysqli_fetch_assoc($result);
    }
?>
<!DOCTYPE html>
<head>
    <title>Billing Packages Management</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets-2/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include("../func/bc-spadmin-header.php"); ?>

    <div class="pagetitle">
        <h1>Billing Packages</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="Dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Billing Packages</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo isset($edit_package) ? 'Edit' : 'Add New'; ?> Package</h5>
                        <form method="POST" action="BillingPackages.php">
                            <input type="hidden" name="package_id" value="<?php echo $edit_package['id'] ?? ''; ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Package Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit_package['name'] ?? ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price (₦)</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo $edit_package['price'] ?? ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="duration_days" class="form-label">Duration (in days)</label>
                                <input type="number" class="form-control" id="duration_days" name="duration_days" value="<?php echo $edit_package['duration_days'] ?? ''; ?>" required>
                            </div>
                            <button type="submit" name="save_package" class="btn btn-primary">Save Package</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Existing Packages</h5>
                        <?php if(isset($_SESSION['page_alert'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['page_alert']; unset($_SESSION['page_alert']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Duration</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $result = mysqli_query($connection_server, "SELECT * FROM sas_billing_packages ORDER BY id DESC");
                                    $count = 1;
                                    while($row = mysqli_fetch_assoc($result)):
                                ?>
                                <tr>
                                    <th scope="row"><?php echo $count++; ?></th>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td>₦<?php echo number_format($row['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['duration_days']); ?> days</td>
                                    <td>
                                        <a href="BillingPackages.php?edit_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="BillingPackages.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this package?');">Delete</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include("../func/bc-spadmin-footer.php"); ?>
</body>
</html>