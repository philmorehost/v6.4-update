<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
session_start();
include("../func/bc-config.php");
include("../func/daily-bonus.php");

$user_id = ($connection_server) ? mysqli_real_escape_string($connection_server, $get_logged_user_details["id"] ?? '') : '';
$last_bonus = ($connection_server) ? get_last_bonus_details($user_id) : null;
$bonus_message = "";

if ($last_bonus) {
    $last_bonus_time = strtotime($last_bonus['timestamp']);
    $next_bonus_time = $last_bonus_time + 24 * 3600;
    if (time() < $next_bonus_time) {
        $bonus_message = "Bonus Claimed! Next Eligible After " . date('g:i A', $next_bonus_time);
    } else {
        $streak_day = calculate_consecutive_purchase_days($user_id);
        $coins_to_award = get_streak_reward($streak_day + 1, $get_logged_user_details["vendor_id"]);
        $bonus_message = "Make a Purchase to Earn " . $coins_to_award . " VTU Coins!";
    }
} else {
    $coins_to_award = get_streak_reward(1, $get_logged_user_details["vendor_id"]);
    $bonus_message = "Start your Streak! Make a Purchase to Earn " . $coins_to_award . " VTU Coins.";
}

// Fetch the user's total loyalty points
$total_points = 0;
if ($connection_server) {
    $points_query = "SELECT SUM(point_amount) as total_points FROM points_log WHERE user_id = ?";
    $stmt = mysqli_prepare($connection_server, $points_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $points_result = mysqli_stmt_get_result($stmt);
        $points_data = mysqli_fetch_assoc($points_result);
        $total_points = $points_data['total_points'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Dashboard | <?php echo $get_all_site_details["site_title"] ?? 'My Site'; ?></title>
	<meta charset="UTF-8" />
	<meta name="description" content="<?php echo substr($get_all_site_details["site_desc"] ?? 'Site Description', 0, 160); ?>" />
	<meta http-equiv="Content-Type" content="text/html; " />
	<meta name="theme-color" content="<?php echo $get_all_site_details["primary_color"] ?? "#198754"; ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
	<link rel="stylesheet" href="/cssfile/bc-style.css">
	<meta name="author" content="BeeCodes Titan">
	<meta name="dc.creator" content="BeeCodes Titan">
	<link href="https://fonts.gstatic.com" rel="preconnect">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
	<link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
	<link href="../assets-2/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
	<link href="../assets-2/vendor/quill/quill.snow.css" rel="stylesheet">
	<link href="../assets-2/vendor/quill/quill.bubble.css" rel="stylesheet">
	<link href="../assets-2/vendor/remixicon/remixicon.css" rel="stylesheet">
	<link href="../assets-2/vendor/simple-datatables/style.css" rel="stylesheet">
	<link href="../assets-2/css/style.css" rel="stylesheet">
	<style>
		body { background-color: #f4f6f9; }
		.shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }
		@media (min-width: 768px) {
			.info-card { border-radius: 15px; }
			.info-card.card-blue { background-color: #eef7ff; }
			.info-card.card-red { background-color: #fceeed; }
			.info-card.card-green { background-color: #eefcef; }
			.info-card.card-yellow { background-color: #fff9e6; }
			.shadow { box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important; }
		}
		@media (max-width: 767px) {
			.pagetitle, .desktop-only { display: none; }
			.mobile-header {
				background: <?php echo $get_all_site_details['primary_color'] ?? '#198754'; ?>;
				padding: 1.5rem 1rem;
				border-radius: 0 0 25px 25px;
				color: #fff;
				margin-bottom: 1.5rem;
                width: 100%;
                position: absolute;
                top: 0;
                left: 0;
			}
			.mobile-balance-card { text-align: center; }
			.mobile-balance-card .balance-title { font-size: 1rem; opacity: 0.8; margin-bottom: 0.5rem; }
			.mobile-balance-card h2 { font-size: 2.8rem; font-weight: 700; margin-bottom: 1.5rem; }
			.mobile-balance-card .btn {
				background-color: rgba(255, 255, 255, 0.25);
				border: none;
				color: #fff;
				font-weight: 500;
				padding: 0.75rem 1.5rem;
				border-radius: 10px;
			}
			.services-grid {
				display: grid;
				grid-template-columns: repeat(3, 1fr);
				gap: 1.5rem;
				padding: 0 1rem;
				margin-top: 10rem;
			}
			.service-icon { text-align: center; font-size: 0.85rem; }
			.service-icon a { text-decoration: none; color: #333; font-weight: 500; }
			.service-icon .bi {
				font-size: 2rem;
				width: 60px;
				height: 60px;
				line-height: 60px;
				border-radius: 15px;
				color: #fff;
				display: inline-block;
				margin-bottom: 0.5rem;
			}
			.services-grid .service-icon:nth-of-type(1) .bi { background-color: #0d6efd; }
			.services-grid .service-icon:nth-of-type(2) .bi { background-color: #198754; }
			.services-grid .service-icon:nth-of-type(3) .bi { background-color: #6f42c1; }
			.services-grid .service-icon:nth-of-type(4) .bi { background-color: #ffc107; }
			.services-grid .service-icon:nth-of-type(5) .bi { background-color: #fd7e14; }
			.services-grid .service-icon:nth-of-type(6) .bi { background-color: #20c997; }
			.services-grid .service-icon:nth-of-type(7) .bi { background-color: #6c757d; }
			.services-grid .service-icon:nth-of-type(8) .bi { background-color: #dc3545; }
			.services-grid .service-icon:nth-of-type(9) .bi { background-color: #0dcaf0; }
            .mobile-vtu-card {
                margin-bottom: 1.5rem !important;
            }
            .mobile-vtu-card .card-body {
                padding: 0.5rem !important;
            }
            .loyalty-card {
                background-color: #f8f9fa;
            }
		}
	</style>
</head>
<body>
	<?php include("../func/bc-header.php"); ?>
	<div class="pagetitle">
		<h1>DASHBOARD</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#">Home</a></li>
				<li class="breadcrumb-item active">Dashboard</li>
			</ol>
		</nav>
	</div>
	<section class="section dashboard">
		<div class="mobile-header d-block d-md-none">
			<div class="mobile-balance-card">
				<h2>₦<?php echo toDecimal($get_logged_user_details["balance"], "2"); ?></h2>
				<div class="d-flex justify-content-around" style="gap: 15px;">
					<a href="/web/Fund.php" class="text-center text-white text-decoration-none">
						<i class="bi bi-plus-circle-fill fs-2"></i>
						<div>Fund</div>
					</a>
					<a href="/web/SubmitPayment.php" class="text-center text-white text-decoration-none">
						<i class="bi bi-receipt-cutoff fs-2"></i>
						<div>Submit</div>
					</a>
					<a href="/web/IntlMoneyTransfer.php" class="text-center text-white text-decoration-none">
						<i class="bi bi-wallet-fill fs-2"></i>
						<div>Wallet</div>
					</a>
					<a href="/web/ShareFund.php" class="text-center text-white text-decoration-none">
						<i class="bi bi-gift-fill fs-2"></i>
						<div>Share</div>
					</a>
				</div>
			</div>
		</div>

		<div class="row desktop-only">
			<div class="col-12 col-md-6 col-lg-3">
				<div class="card info-card card-blue shadow">
					<div class="card-body">
						<h5 class="card-title"><?php echo $get_logged_user_details["username"]; ?> <span>| Account Type</span></h5>
						<div class="d-flex align-items-center">
							<div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-person-badge"></i></div>
							<div class="ps-3"><h6><?php echo accountLevel($get_logged_user_details["account_level"]); ?></h6></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-6 col-lg-3">
				<div class="card info-card card-red shadow">
					<div class="card-body">
						<h5 class="card-title">Balance</h5>
						<div class="d-flex align-items-center">
							<div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-wallet2"></i></div>
							<div class="ps-3"><h6>₦<?php echo toDecimal($get_logged_user_details["balance"], "2"); ?></h6></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-6 col-lg-3">
				<div class="card info-card card-green shadow">
					<div class="card-body">
						<h5 class="card-title">Deposit <span>| Total</span></h5>
						<div class="d-flex align-items-center">
							<div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-cash-stack"></i></div>
							<div class="ps-3">
								<h6>₦<?php
								$all_user_credit_transaction = 0;
								$stmt = mysqli_prepare($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id=? AND username=? AND (type_alternative LIKE '%credit%' OR type_alternative LIKE '%received%' OR type_alternative LIKE '%commission%')");
								mysqli_stmt_bind_param($stmt, "is", $get_logged_user_details["vendor_id"], $get_logged_user_details["username"]);
								mysqli_stmt_execute($stmt);
								$get_all_user_credit_transaction_details = mysqli_stmt_get_result($stmt);
								if (mysqli_num_rows($get_all_user_credit_transaction_details) >= 1) {
									while ($transaction_record = mysqli_fetch_assoc($get_all_user_credit_transaction_details)) {
										$all_user_credit_transaction += $transaction_record["discounted_amount"];
									}
								}
								echo toDecimal($all_user_credit_transaction, 2);
								?></h6>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-6 col-lg-3">
				<div class="card info-card card-yellow shadow">
					<div class="card-body">
						<h5 class="card-title">Spent <span>| Total</span></h5>
						<div class="d-flex align-items-center">
							<div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-cart-x"></i></div>
							<div class="ps-3">
								<h6>₦<?php
								$all_user_debit_transaction = 0;
								$stmt = mysqli_prepare($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id=? AND username=? AND (type_alternative NOT LIKE '%credit%' AND type_alternative NOT LIKE '%refund%' AND type_alternative NOT LIKE '%received%' AND type_alternative NOT LIKE '%commission%' AND status NOT LIKE '%3%')");
								mysqli_stmt_bind_param($stmt, "is", $get_logged_user_details["vendor_id"], $get_logged_user_details["username"]);
								mysqli_stmt_execute($stmt);
								$get_all_user_debit_transaction_details = mysqli_stmt_get_result($stmt);
								if (mysqli_num_rows($get_all_user_debit_transaction_details) >= 1) {
									while ($transaction_record = mysqli_fetch_assoc($get_all_user_debit_transaction_details)) {
										$all_user_debit_transaction += $transaction_record["discounted_amount"];
									}
								}
								echo toDecimal($all_user_debit_transaction, 2);
								?></h6>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row desktop-only">
			<div class="col-12">
				<div class="card shadow">
					<div class="card-body">
						<div class="d-flex justify-content-around align-items-center h-100 py-3">
							<a href="Fund.php" class="text-center text-decoration-none"><i class="bi bi-plus-circle-fill fs-2 text-success"></i><div>Wallet Funding</div></a>
							<a href="ShareFund.php" class="text-center text-decoration-none"><i class="bi bi-send-fill fs-2 text-primary"></i><div>Fund Transfer</div></a>
							<a href="IntlMoneyTransfer.php" class="text-center text-decoration-none"><i class="bi bi-wallet-fill fs-2 text-primary"></i><div>Wallet</div></a>
							<a href="SubmitPayment.php" class="text-center text-decoration-none"><i class="bi bi-receipt-cutoff fs-2 text-info"></i><div>Submit Payment</div></a>
							<a href="Transactions.php" class="text-center text-decoration-none"><i class="bi bi-list-ul fs-2 text-warning"></i><div>Transactions</div></a>
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-lg-8">
				<div class="services-grid d-grid d-md-none">
					<div class="service-icon"><a href="Airtime.php"><i class="bi bi-telephone"></i><div>Airtime</div></a></div>
					<div class="service-icon"><a href="Data.php"><i class="bi bi-wifi"></i><div>Data</div></a></div>
					<div class="service-icon"><a href="Cable.php"><i class="bi bi-tv"></i><div>Cable</div></a></div>
					<div class="service-icon"><a href="Electric.php"><i class="bi bi-lightbulb"></i><div>Electric</div></a></div>
					<div class="service-icon"><a href="Exam.php"><i class="bi bi-card-list"></i><div>Exam</div></a></div>
					<div class="service-icon"><a href="Betting.php"><i class="bi bi-tag"></i><div>Betting</div></a></div>
					<div class="service-icon"><a href="IntlAirtime.php"><i class="bi bi-globe"></i><div>Int'l Airtime</div></a></div>
					<div class="service-icon"><a href="Transactions.php"><i class="bi bi-receipt"></i><div>History</div></a></div>
				</div>
            </div>
        </div>
<div class="row">
    <div class="col-lg-4">
        <div class="card info-card loyalty-card p-3">
            <div class="card-body">
                <h5 class="card-title">VTU Streak Bonus</h5>
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-award"></i>
                    </div>
                    <div class="ps-3">
                        <h6 class="fs-4 fw-bold"><?php echo number_format($total_points); ?> Coins</h6>
                        <span class="text-muted small pt-2"><?php echo $bonus_message; ?></span>
                        <div class="mt-2">
                            <a href="ConvertCoins.php" class="btn btn-sm btn-outline-primary">Convert to Wallet</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card info-card sales-card p-3 align-items-center">
            <?php
            $stmt = mysqli_prepare($connection_server, "SELECT COUNT(*) as referral_count FROM sas_users WHERE referral_id = ?");
            mysqli_stmt_bind_param($stmt, "i", $get_logged_user_details["id"]);
            mysqli_stmt_execute($stmt);
            $referral_query = mysqli_stmt_get_result($stmt);
            $referral_count = mysqli_fetch_assoc($referral_query)['referral_count'];
            ?>
            <h5 class="card-title">Refer and Earn Big</h5>
            <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                    <h6><?php echo $referral_count; ?> Referrals</h6>
                </div>
            </div>
            <button class="btn btn-primary mt-3" onclick="copyReferLink();" title="Click To Copy">Copy Referral Link</button>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card d-none d-md-block">
            <div class="card-body">
                <h5 class="card-title">Services</h5>
                <div class="row">
                    <div class="col-6 col-lg-3"><div class="card info-card sales-card p-3 align-items-center"><a href="Data.php" class=""><div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-wifi"></i></div><h5 class="card-title">Buy Data</h5></a></div></div>
                    <div class="col-6 col-lg-3"><div class="card info-card sales-card p-3 align-items-center"><a href="Airtime.php" class=""><div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-telephone"></i></div><h5 class="card-title">Buy Airtime</h5></a></div></div>
                    <div class="col-6 col-lg-3"><div class="card info-card sales-card p-3 align-items-center"><a href="Cable.php" class=""><div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-tv"></i></div><h5 class="card-title">Cable Tv</h5></a></div></div>
                    <div class="col-6 col-lg-3"><div class="card info-card sales-card p-3 align-items-center"><a href="Electric.php" class=""><div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-lightbulb"></i></div><h5 class="card-title">Electric</h5></a></div></div>
                    <div class="col-6 col-lg-3"><div class="card info-card sales-card p-3 align-items-center"><a href="IntlAirtime.php" class=""><div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-globe"></i></div><h5 class="card-title">Int'l Airtime</h5></a></div></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card info-card sales-card p-0 align-items-center">
                    <div class="card-body col-12 align-items-center">
                        <h5 class="card-title mb-0">Auto Funding</h5>
                        <div class="container-fluid">
                            <div class="d-flex flex-row flex-nowrap overflow-auto py-2" style="gap: 12px;">
                                <?php
                                foreach (getUserVirtualBank() as $bank_accounts_json) {
                                    $bank_accounts_json = json_decode($bank_accounts_json, true);
                                    if (in_array($bank_accounts_json["bank_code"], array("110072", "232", "035", "50515", "120001", "100039", "GLOBUS BANK", "WEMA BANK"))) {
                                        ?>
                                        <div class="card shadow-sm flex-shrink-0" style="min-width: 100%; max-width: 100%; border-radius: 12px;">
                                            <div class="card-header bg-success text-white text-center fw-semibold py-2" style="font-size: 14px;"><?php echo strtoupper($bank_accounts_json["account_name"]); ?></div>
                                            <div class="card-body bg-light text-center p-2">
                                                <h6 class="text-success mb-1" style="font-size: 13px;"><?php echo strtoupper($bank_accounts_json["bank_name"]); ?></h6>
                                                <h5 class="text-dark fw-bold mb-1" style="font-size: 16px;"><?php echo $bank_accounts_json["account_number"]; ?><span class="ms-1" style="cursor:pointer" onclick="copyAccount('<?php echo $bank_accounts_json['account_number']; ?>')"><i class="bi bi-copy text-success small" title="Copy Account Number"></i></span></h5>
                                                <div class="text-muted" style="font-size: 13px;">Charges <span class="fw-bold text-danger ms-1">₦50</span></div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($get_logged_user_details["account_level"] < 2) : ?>
            <div class="col-md-6 d-none d-md-block">
                <div class="card info-card sales-card p-3 align-items-center">
                    <div class="card-body col-12 align-items-center">
                        <h5 class="card-title">UPGRADE ACCOUNT</h5>
                        <form method="post" action="handle_upgrade.php">
                            <select id="" name="upgrade-type" onchange="" class="form-select col-12" required><option value="" default hidden selected>Choose Account Level</option><?php
                            if (!empty($get_logged_user_details["account_level"])) {
                                $account_level_upgrade_array = array(1 => "smart", 2 => "agent");
                                foreach ($account_level_upgrade_array as $index => $account_levels) {
                                    if ($index > $get_logged_user_details["account_level"]) {
                                        $stmt = mysqli_prepare($connection_server, "SELECT * FROM sas_user_upgrade_price WHERE vendor_id=? AND account_type=? LIMIT 1");
                                        mysqli_stmt_bind_param($stmt, "is", $get_logged_user_details["vendor_id"], $index);
                                        mysqli_stmt_execute($stmt);
                                        $get_upgrade_price_result = mysqli_stmt_get_result($stmt);
                                        $get_upgrade_price = mysqli_fetch_array($get_upgrade_price_result);
                                        echo '<option value="' . $account_levels . '">' . accountLevel($index) . ' @ N' . toDecimal($get_upgrade_price["price"], 2) . '</option>';
                                    }
                                }
                            }
                            ?></select><br />
                            <button id="" name="upgrade-user" type="submit" class="btn btn-success col-12">Proceed</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
	</section>
	<script>
		function copyAccount(accountNumber) {
			navigator.clipboard.writeText(accountNumber).then(() => {
				Swal.fire({ icon: 'success', title: 'Copied!', text: 'Account number copied: ' + accountNumber, timer: 2000, showConfirmButton: false });
			}).catch(err => { console.error('Failed to copy: ', err); });
		}
		let ReferLink = '<?php echo $web_http_host . "/web/Register.php?referral=" . $get_logged_user_details["username"]; ?>';
		const copyReferLink = async () => {
			try {
				await navigator.clipboard.writeText(ReferLink);
				Swal.fire({ icon: 'success', title: 'Copied!', text: 'Referral link copied: ' + ReferLink, timer: 2000, showConfirmButton: false });
			} catch (err) { console.error('Failed to copy: ' + err); }
		}
	</script>
	<div class="d-none d-md-block">
		<?php include("../func/short-trans.php"); ?>
	</div>
	<?php include("../func/bc-footer.php"); ?>
</body>
</html>
