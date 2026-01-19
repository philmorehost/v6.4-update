<?php
session_start();
include("../func/bc-spadmin-config.php");

$page_title = "All Vendor Subscriptions";
?>
<!DOCTYPE html>
<head>
    <title><?php echo $page_title; ?> | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets-2/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include("../func/bc-spadmin-header.php"); ?>

    <div class="pagetitle">
      <h1><?php echo $page_title; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="Dashboard.php">Home</a></li>
          <li class="breadcrumb-item active"><?php echo $page_title; ?></li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
        <div class="card info-card px-4 py-4">
            <div class="card-body">
                <h5 class="card-title">Filter Subscriptions</h5>

                <form method="get" action="AllSubscriptions.php" class="row g-3">
                    <div class="col-md-8">
                        <input name="searchq" type="text" value="<?php echo htmlspecialchars($_GET['searchq'] ?? ''); ?>" placeholder="Enter Vendor Email to search" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card info-card px-4 py-4">
            <div class="card-body">
                <h5 class="card-title">Subscription History</h5>

                <div class="overflow-auto">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                          <tr>
                              <th>S/N</th>
                              <th>Vendor Email</th>
                              <th>Package Name</th>
                              <th>Purchase Date</th>
                              <th>Expiry Date</th>
                              <th>Amount Paid</th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php
                            $limit = 20;
                            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                            $offset = ($page - 1) * $limit;

                            $search_query = "";
                            $params = [];
                            $types = "";

                            if (!empty($_GET['searchq'])) {
                                $search_term = '%' . $_GET['searchq'] . '%';
                                $search_query = " WHERE v.email LIKE ?";
                                $params[] = $search_term;
                                $types .= "s";
                            }

                            // Get total records for pagination
                            $count_stmt = $connection_server->prepare("SELECT COUNT(*) FROM sas_vendor_subscriptions s JOIN sas_vendors v ON s.vendor_id = v.id" . $search_query);
                            if ($search_query) {
                                $count_stmt->bind_param($types, ...$params);
                            }
                            $count_stmt->execute();
                            $total_records = $count_stmt->get_result()->fetch_row()[0];
                            $total_pages = ceil($total_records / $limit);
                            $count_stmt->close();


                            // Fetch records for the current page
                            $sql = "SELECT s.purchase_date, s.expiry_date, s.amount_paid, p.name as package_name, v.email as vendor_email
                                    FROM sas_vendor_subscriptions s
                                    JOIN sas_vendors v ON s.vendor_id = v.id
                                    JOIN sas_billing_packages p ON s.package_id = p.id
                                    $search_query
                                    ORDER BY s.purchase_date DESC
                                    LIMIT ? OFFSET ?";

                            $params[] = $limit;
                            $types .= "i";
                            $params[] = $offset;
                            $types .= "i";

                            $stmt = $connection_server->prepare($sql);
                            $stmt->bind_param($types, ...$params);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $count = $offset + 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>
                                            <td>' . $count++ . '</td>
                                            <td>' . htmlspecialchars($row['vendor_email']) . '</td>
                                            <td>' . htmlspecialchars($row['package_name']) . '</td>
                                            <td>' . htmlspecialchars(date("F j, Y, g:i a", strtotime($row['purchase_date']))) . '</td>
                                            <td>' . htmlspecialchars(date("F j, Y", strtotime($row['expiry_date']))) . '</td>
                                            <td>₦' . htmlspecialchars(number_format($row['amount_paid'], 2)) . '</td>
                                          </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center">No subscription records found.</td></tr>';
                            }
                            $stmt->close();
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mt-4">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&searchq=<?php echo htmlspecialchars($_GET['searchq'] ?? ''); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>

            </div>
        </div>
      </div>
    </section>

    <?php include("../func/bc-spadmin-footer.php"); ?>
</body>
</html>
